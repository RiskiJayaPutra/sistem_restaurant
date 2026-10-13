<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

/**
 * CustomerRepository — Akses data tabel customers.
 *
 * Mengelola query SQL terparameterisasi dengan PDO
 * untuk operasi CRUD data pelanggan.
 *
 * @package RestoReserve\Repositories
 */
class CustomerRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil seluruh data pelanggan dengan opsi pencarian nama/telepon.
     *
     * @param string $search Kata kunci pencarian
     * @return array
     */
    public function getAll(string $search = ''): array
    {
        if ($search !== '') {
            $sql = 'SELECT c.*, (SELECT COUNT(*) FROM reservations r WHERE r.customer_id = c.id) AS reservation_count 
                    FROM customers c 
                    WHERE c.name LIKE :search_name OR c.phone LIKE :search_phone 
                    ORDER BY c.id DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':search_name'  => "%{$search}%",
                ':search_phone' => "%{$search}%",
            ]);
            return $stmt->fetchAll();
        }

        $sql = 'SELECT c.*, (SELECT COUNT(*) FROM reservations r WHERE r.customer_id = c.id) AS reservation_count 
                FROM customers c 
                ORDER BY c.id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Cari pelanggan berdasarkan ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $customer = $stmt->fetch();
        return $customer ?: null;
    }

    /**
     * Simpan data pelanggan baru.
     *
     * @param array $data ['name' => ..., 'phone' => ..., 'email' => ...]
     * @return int ID pelanggan yang baru dibuat
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO customers (name, phone, email, created_at, updated_at) 
                VALUES (:name, :phone, :email, NOW(), NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'  => $data['name'],
            ':phone' => $data['phone'],
            ':email' => !empty($data['email']) ? $data['email'] : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Perbarui data pelanggan.
     *
     * @param int   $id
     * @param array $data ['name' => ..., 'phone' => ..., 'email' => ...]
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE customers 
                SET name = :name, phone = :phone, email = :email, updated_at = NOW() 
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'    => $id,
            ':name'  => $data['name'],
            ':phone' => $data['phone'],
            ':email' => !empty($data['email']) ? $data['email'] : null,
        ]);
    }

    /**
     * Cek apakah pelanggan memiliki riwayat reservasi.
     *
     * @param int $id
     * @return bool
     */
    public function hasReservations(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE customer_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Hapus data pelanggan fisik (hanya jika belum memiliki reservasi).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM customers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
