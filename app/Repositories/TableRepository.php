<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * TableRepository — Akses data tabel restaurant_tables.
 *
 * @package RestoReserve\Repositories
 */
class TableRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil seluruh meja beserta riwayat jumlah reservasinya.
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = 'SELECT t.*, (SELECT COUNT(*) FROM reservations r WHERE r.table_id = t.id) AS reservation_count 
                FROM restaurant_tables t 
                ORDER BY t.table_number ASC';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Ambil hanya meja yang aktif untuk dropdown reservasi baru.
     *
     * @return array
     */
    public function getActiveTables(): array
    {
        $sql = 'SELECT * FROM restaurant_tables WHERE is_active = 1 ORDER BY capacity ASC, table_number ASC';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Cari meja berdasarkan ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM restaurant_tables WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $table = $stmt->fetch();
        return $table ?: null;
    }

    /**
     * Cek apakah nomor meja sudah digunakan oleh ID lain.
     *
     * @param string   $tableNumber
     * @param int|null $excludeId
     * @return bool
     */
    public function isTableNumberExists(string $tableNumber, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM restaurant_tables WHERE table_number = :num AND id <> :id');
            $stmt->execute([':num' => $tableNumber, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM restaurant_tables WHERE table_number = :num');
            $stmt->execute([':num' => $tableNumber]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Simpan meja baru.
     *
     * @param array $data ['table_number' => ..., 'capacity' => ..., 'is_active' => ...]
     * @return int
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO restaurant_tables (table_number, capacity, is_active) 
                VALUES (:table_number, :capacity, :is_active)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':table_number' => $data['table_number'],
            ':capacity'     => $data['capacity'],
            ':is_active'    => $data['is_active'] ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update data meja.
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE restaurant_tables 
                SET table_number = :table_number, capacity = :capacity, is_active = :is_active 
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'           => $id,
            ':table_number' => $data['table_number'],
            ':capacity'     => $data['capacity'],
            ':is_active'    => $data['is_active'] ? 1 : 0,
        ]);
    }

    /**
     * Cek apakah meja memiliki riwayat reservasi.
     *
     * @param int $id
     * @return bool
     */
    public function hasReservations(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE table_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Hapus meja secara fisik jika belum ada reservasi.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM restaurant_tables WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
