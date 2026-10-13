<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * ReservationRepository — Akses data dan transaksi reservasi meja.
 *
 * Menerapkan row locking (FOR UPDATE) pada tabel restaurant_tables
 * dan transaksi database untuk mencegah double booking (race condition).
 *
 * @package RestoReserve\Repositories
 */
class ReservationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil daftar reservasi dengan filter status, tanggal, atau nama pelanggan.
     *
     * @param array $filters ['status' => ..., 'from_date' => ..., 'to_date' => ..., 'search' => ...]
     * @return array
     */
    public function getAll(array $filters = []): array
    {
        $sql = "SELECT r.*, 
                       c.name AS customer_name, c.phone AS customer_phone,
                       t.table_number, t.capacity AS table_capacity,
                       o.id AS order_id, o.status AS order_status, o.total AS order_total
                FROM reservations r
                JOIN customers c ON r.customer_id = c.id
                JOIN restaurant_tables t ON r.table_id = t.id
                LEFT JOIN orders o ON o.reservation_id = r.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(r.reserved_at) >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(r.reserved_at) <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (c.name LIKE :search_name OR c.phone LIKE :search_phone OR t.table_number LIKE :search_table)";
            $searchWildcard = "%" . $filters['search'] . "%";
            $params[':search_name']  = $searchWildcard;
            $params[':search_phone'] = $searchWildcard;
            $params[':search_table'] = $searchWildcard;
        }

        $sql .= " ORDER BY r.reserved_at DESC, r.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil detail reservasi lengkap dengan data relasi.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT r.*, 
                       c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                       t.table_number, t.capacity AS table_capacity, t.is_active AS table_is_active,
                       o.id AS order_id, o.status AS order_status, o.total AS order_total
                FROM reservations r
                JOIN customers c ON r.customer_id = c.id
                JOIN restaurant_tables t ON r.table_id = t.id
                LEFT JOIN orders o ON o.reservation_id = r.id
                WHERE r.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Buat reservasi baru secara aman menggunakan database transaction dan row locking.
     *
     * @param array $data ['customer_id', 'table_id', 'reserved_at', 'guest_count', 'notes']
     * @return array ['success' => bool, 'reservation_id' => ?int, 'error' => ?string]
     */
    public function createWithLock(array $data): array
    {
        $this->db->beginTransaction();

        try {
            $tableId    = (int) $data['table_id'];
            $reservedAt = $data['reserved_at'];
            $newEnd     = date('Y-m-d H:i:s', strtotime($reservedAt . ' +2 hours'));

            // 1. Kunci baris meja yang dipesan untuk mencegah bentrokan konkuren
            $lockStmt = $this->db->prepare('SELECT id, capacity, is_active FROM restaurant_tables WHERE id = :table_id FOR UPDATE');
            $lockStmt->execute([':table_id' => $tableId]);
            $table = $lockStmt->fetch();

            if (!$table) {
                $this->db->rollBack();
                return ['success' => false, 'reservation_id' => null, 'error' => 'Meja yang dipilih tidak ditemukan.'];
            }

            if ((int) $table['is_active'] !== 1) {
                $this->db->rollBack();
                return ['success' => false, 'reservation_id' => null, 'error' => 'Meja yang dipilih saat ini sedang nonaktif.'];
            }

            if ((int) $data['guest_count'] > (int) $table['capacity']) {
                $this->db->rollBack();
                return ['success' => false, 'reservation_id' => null, 'error' => "Jumlah tamu ({$data['guest_count']}) melebihi kapasitas meja ({$table['capacity']} orang)."];
            }

            // 2. Cek apakah ada jadwal reservasi aktif yang bentrok
            $conflictStmt = $this->db->prepare(
                "SELECT id FROM reservations 
                 WHERE table_id = :table_id 
                   AND status IN ('pending', 'confirmed') 
                   AND reserved_at < :new_end 
                   AND DATE_ADD(reserved_at, INTERVAL 2 HOUR) > :new_start 
                 LIMIT 1"
            );
            $conflictStmt->execute([
                ':table_id'  => $tableId,
                ':new_end'   => $newEnd,
                ':new_start' => $reservedAt,
            ]);

            if ($conflictStmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'reservation_id' => null, 'error' => 'Meja ini sudah dipesan pada rentang jam tersebut. Silakan pilih meja lain atau jam berbeda.'];
            }

            // 3. Simpan data reservasi baru
            $insertSql = "INSERT INTO reservations (customer_id, table_id, reserved_at, guest_count, notes, status, created_at, updated_at) 
                          VALUES (:customer_id, :table_id, :reserved_at, :guest_count, :notes, 'pending', NOW(), NOW())";
            $insertStmt = $this->db->prepare($insertSql);
            $insertStmt->execute([
                ':customer_id' => (int) $data['customer_id'],
                ':table_id'    => $tableId,
                ':reserved_at' => $reservedAt,
                ':guest_count' => (int) $data['guest_count'],
                ':notes'       => !empty($data['notes']) ? $data['notes'] : null,
            ]);

            $newId = (int) $this->db->lastInsertId();
            $this->db->commit();

            return ['success' => true, 'reservation_id' => $newId, 'error' => null];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'reservation_id' => null, 'error' => 'Gagal memproses reservasi: ' . $e->getMessage()];
        }
    }

    /**
     * Perbarui jadwal atau rincian reservasi saat status masih 'pending'.
     *
     * @param int   $id
     * @param array $data ['table_id', 'reserved_at', 'guest_count', 'notes']
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function updateWithLock(int $id, array $data): array
    {
        $this->db->beginTransaction();

        try {
            // Ambil dan kunci baris reservasi
            $resStmt = $this->db->prepare('SELECT status FROM reservations WHERE id = :id FOR UPDATE');
            $resStmt->execute([':id' => $id]);
            $currentRes = $resStmt->fetch();

            if (!$currentRes) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Data reservasi tidak ditemukan.'];
            }

            if ($currentRes['status'] !== 'pending') {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Hanya reservasi berstatus Pending yang dapat diubah jadwal atau mejanya.'];
            }

            $tableId    = (int) $data['table_id'];
            $reservedAt = $data['reserved_at'];
            $newEnd     = date('Y-m-d H:i:s', strtotime($reservedAt . ' +2 hours'));

            // Kunci baris meja
            $tableStmt = $this->db->prepare('SELECT id, capacity, is_active FROM restaurant_tables WHERE id = :table_id FOR UPDATE');
            $tableStmt->execute([':table_id' => $tableId]);
            $table = $tableStmt->fetch();

            if (!$table || (int) $table['is_active'] !== 1) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Meja yang dipilih tidak aktif atau tidak ditemukan.'];
            }

            if ((int) $data['guest_count'] > (int) $table['capacity']) {
                $this->db->rollBack();
                return ['success' => false, 'error' => "Jumlah tamu melebihi kapasitas meja ({$table['capacity']} orang)."];
            }

            // Cek bentrokan dengan reservasi lain (kecualikan diri sendiri)
            $conflictStmt = $this->db->prepare(
                "SELECT id FROM reservations 
                 WHERE table_id = :table_id 
                   AND status IN ('pending', 'confirmed') 
                   AND reserved_at < :new_end 
                   AND DATE_ADD(reserved_at, INTERVAL 2 HOUR) > :new_start 
                   AND id <> :exclude_id 
                 LIMIT 1"
            );
            $conflictStmt->execute([
                ':table_id'    => $tableId,
                ':new_end'     => $newEnd,
                ':new_start'   => $reservedAt,
                ':exclude_id'  => $id,
            ]);

            if ($conflictStmt->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Meja ini sudah dipesan pada rentang jam tersebut oleh reservasi lain.'];
            }

            // Update reservasi
            $updateSql = "UPDATE reservations 
                          SET table_id = :table_id, reserved_at = :reserved_at, 
                              guest_count = :guest_count, notes = :notes, updated_at = NOW() 
                          WHERE id = :id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([
                ':id'          => $id,
                ':table_id'    => $tableId,
                ':reserved_at' => $reservedAt,
                ':guest_count' => (int) $data['guest_count'],
                ':notes'       => !empty($data['notes']) ? $data['notes'] : null,
            ]);

            $this->db->commit();
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'error' => 'Gagal memperbarui reservasi: ' . $e->getMessage()];
        }
    }

    /**
     * Ubah status reservasi.
     *
     * @param int    $id
     * @param string $newStatus 'confirmed', 'completed', 'cancelled'
     * @return bool
     */
    public function updateStatus(int $id, string $newStatus): bool
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([':id' => $id, ':status' => $newStatus]);
    }
}
