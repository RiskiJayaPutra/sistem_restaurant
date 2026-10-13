<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * OrderRepository — Akses data dan transaksi pesanan makanan (orders & order_items).
 *
 * Mengelola pembuatan pesanan draft, snapshot harga menu ke order_items,
 * dan transaksi pembaruan total pesanan.
 *
 * @package RestoReserve\Repositories
 */
class OrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil seluruh pesanan dengan relasi reservasi, pelanggan, dan meja.
     *
     * @param string $statusFilter
     * @return array
     */
    public function getAll(string $statusFilter = ''): array
    {
        $sql = "SELECT o.*, 
                       r.reserved_at, r.guest_count, r.status AS reservation_status,
                       c.name AS customer_name, c.phone AS customer_phone,
                       t.table_number,
                       (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
                FROM orders o
                JOIN reservations r ON o.reservation_id = r.id
                JOIN customers c ON r.customer_id = c.id
                JOIN restaurant_tables t ON r.table_id = t.id";

        if ($statusFilter !== '') {
            $sql .= " WHERE o.status = :status";
            $stmt = $this->db->prepare($sql . " ORDER BY o.id DESC");
            $stmt->execute([':status' => $statusFilter]);
            return $stmt->fetchAll();
        }

        $sql .= " ORDER BY o.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Cari order berdasarkan ID beserta rincian pesanan dan data reservasi.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT o.*, 
                       r.id AS reservation_id, r.reserved_at, r.guest_count, r.status AS reservation_status,
                       c.name AS customer_name, c.phone AS customer_phone,
                       t.table_number, t.capacity AS table_capacity
                FROM orders o
                JOIN reservations r ON o.reservation_id = r.id
                JOIN customers c ON r.customer_id = c.id
                JOIN restaurant_tables t ON r.table_id = t.id
                WHERE o.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Ambil item pesanan
        $order['items'] = $this->getOrderItems($id);

        return $order;
    }

    /**
     * Cari pesanan berdasarkan ID reservasi.
     *
     * @param int $reservationId
     * @return array|null
     */
    public function findByReservationId(int $reservationId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE reservation_id = :rid LIMIT 1');
        $stmt->execute([':rid' => $reservationId]);
        $order = $stmt->fetch();

        if ($order) {
            $order['items'] = $this->getOrderItems((int) $order['id']);
            return $order;
        }

        return null;
    }

    /**
     * Ambil rincian item pesanan untuk order tertentu.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT oi.*, m.name AS menu_name, m.category AS menu_category 
                FROM order_items oi
                JOIN menu_items m ON oi.menu_item_id = m.id
                WHERE oi.order_id = :order_id
                ORDER BY m.category ASC, m.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Simpan order baru beserta itemnya dalam satu transaksi database.
     * Harga diambil langsung dari tabel menu_items (snapshot).
     *
     * @param int   $reservationId
     * @param array $items Array [{menu_item_id, quantity}]
     * @return int ID order baru
     * @throws \Exception
     */
    public function createWithItems(int $reservationId, array $items): int
    {
        $this->db->beginTransaction();

        try {
            // 1. Buat record order (status draft)
            $orderSql = "INSERT INTO orders (reservation_id, status, total, created_at, updated_at) 
                         VALUES (:reservation_id, 'draft', 0, NOW(), NOW())";
            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([':reservation_id' => $reservationId]);
            $orderId = (int) $this->db->lastInsertId();

            // 2. Simpan setiap item dengan snapshot harga dari menu_items
            $grandTotal = 0;
            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) 
                 VALUES (:order_id, :menu_item_id, :quantity, :unit_price, :subtotal)"
            );

            $menuStmt = $this->db->prepare("SELECT price FROM menu_items WHERE id = :menu_id AND is_available = 1");

            foreach ($items as $item) {
                $menuId = (int) $item['menu_item_id'];
                $qty    = (int) $item['quantity'];

                $menuStmt->execute([':menu_id' => $menuId]);
                $price = $menuStmt->fetchColumn();

                if ($price === false) {
                    throw new \InvalidArgumentException("Menu dengan ID #{$menuId} tidak ditemukan atau sedang tidak tersedia.");
                }

                $unitPrice = (int) $price;
                $subtotal  = $qty * $unitPrice;
                $grandTotal += $subtotal;

                $itemStmt->execute([
                    ':order_id'     => $orderId,
                    ':menu_item_id' => $menuId,
                    ':quantity'     => $qty,
                    ':unit_price'   => $unitPrice,
                    ':subtotal'     => $subtotal,
                ]);
            }

            // 3. Update total akhir pada header order
            $updateTotalStmt = $this->db->prepare("UPDATE orders SET total = :total WHERE id = :order_id");
            $updateTotalStmt->execute([':total' => $grandTotal, ':order_id' => $orderId]);

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Perbarui item pesanan draft dalam transaksi.
     *
     * @param int   $orderId
     * @param array $items
     * @return void
     * @throws \Exception
     */
    public function updateDraftItems(int $orderId, array $items): void
    {
        $this->db->beginTransaction();

        try {
            // Hapus item pesanan draft lama
            $deleteStmt = $this->db->prepare('DELETE FROM order_items WHERE order_id = :order_id');
            $deleteStmt->execute([':order_id' => $orderId]);

            $grandTotal = 0;
            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) 
                 VALUES (:order_id, :menu_item_id, :quantity, :unit_price, :subtotal)"
            );

            $menuStmt = $this->db->prepare("SELECT price FROM menu_items WHERE id = :menu_id");

            foreach ($items as $item) {
                $menuId = (int) $item['menu_item_id'];
                $qty    = (int) $item['quantity'];

                $menuStmt->execute([':menu_id' => $menuId]);
                $price = $menuStmt->fetchColumn();

                if ($price === false) {
                    throw new \InvalidArgumentException("Menu ID #{$menuId} tidak ditemukan.");
                }

                $unitPrice = (int) $price;
                $subtotal  = $qty * $unitPrice;
                $grandTotal += $subtotal;

                $itemStmt->execute([
                    ':order_id'     => $orderId,
                    ':menu_item_id' => $menuId,
                    ':quantity'     => $qty,
                    ':unit_price'   => $unitPrice,
                    ':subtotal'     => $subtotal,
                ]);
            }

            $updateTotalStmt = $this->db->prepare("UPDATE orders SET total = :total, updated_at = NOW() WHERE id = :order_id");
            $updateTotalStmt->execute([':total' => $grandTotal, ':order_id' => $orderId]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Konfirmasi pesanan draft.
     *
     * @param int $id
     * @return bool
     */
    public function confirmOrder(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = 'confirmed', confirmed_at = NOW(), updated_at = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Batalkan pesanan.
     *
     * @param int $id
     * @return bool
     */
    public function cancelOrder(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
