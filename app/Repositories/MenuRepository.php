<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * MenuRepository — Akses data tabel menu_items.
 *
 * @package RestoReserve\Repositories
 */
class MenuRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil seluruh menu makanan & minuman beserta riwayat pemesanannya.
     *
     * @param string $category Filter kategori ('Makanan', 'Minuman', atau kosong untuk semua)
     * @return array
     */
    public function getAll(string $category = ''): array
    {
        if ($category !== '') {
            $sql = 'SELECT m.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.menu_item_id = m.id) AS ordered_count 
                    FROM menu_items m 
                    WHERE m.category = :category 
                    ORDER BY m.category ASC, m.name ASC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':category' => $category]);
            return $stmt->fetchAll();
        }

        $sql = 'SELECT m.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.menu_item_id = m.id) AS ordered_count 
                FROM menu_items m 
                ORDER BY m.category ASC, m.name ASC';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Ambil hanya menu yang tersedia (is_available = 1) untuk dropdown order baru.
     *
     * @return array
     */
    public function getAvailableMenus(): array
    {
        $sql = 'SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category ASC, name ASC';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Cari menu berdasarkan ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM menu_items WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $menu = $stmt->fetch();
        return $menu ?: null;
    }

    /**
     * Simpan menu baru.
     *
     * @param array $data ['name' => ..., 'category' => ..., 'price' => ..., 'is_available' => ...]
     * @return int
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO menu_items (name, category, price, is_available) 
                VALUES (:name, :category, :price, :is_available)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'         => $data['name'],
            ':category'     => $data['category'],
            ':price'        => (int) $data['price'],
            ':is_available' => $data['is_available'] ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update menu.
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE menu_items 
                SET name = :name, category = :category, price = :price, is_available = :is_available 
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'           => $id,
            ':name'         => $data['name'],
            ':category'     => $data['category'],
            ':price'        => (int) $data['price'],
            ':is_available' => $data['is_available'] ? 1 : 0,
        ]);
    }

    /**
     * Cek apakah menu sudah pernah dipesan pada tabel order_items.
     *
     * @param int $id
     * @return bool
     */
    public function hasOrderItems(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM order_items WHERE menu_item_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Hapus menu fisik jika belum pernah dipesan.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM menu_items WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
