<?php

/**
 * Script CLI — Seed Data Reservasi & Pesanan Dinamis Relatif ke Waktu Sekarang
 *
 * Mengisi reservasi masa depan fiktif agar demonstrasi di depan asesor
 * memiliki data aktif yang relevan tanpa tanggal kedaluwarsa.
 *
 * @package RestoReserve\Scripts
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use App\Core\Database;

echo "====================================================\n";
echo "  RestoReserve — Seed Data Demo Dinamis\n";
echo "====================================================\n\n";

try {
    $db = Database::getInstance();

    // Pastikan pelanggan dan meja sudah ada
    $customers = $db->query('SELECT id, name FROM customers ORDER BY id ASC')->fetchAll();
    $tables = $db->query('SELECT id, table_number, capacity FROM restaurant_tables ORDER BY id ASC')->fetchAll();
    $menus = $db->query('SELECT id, name, price FROM menu_items ORDER BY id ASC')->fetchAll();

    if (empty($customers) || empty($tables) || empty($menus)) {
        echo "[PERINGATAN] Tabel master (pelanggan, meja, menu) masih kosong.\n";
        echo "Silakan import database/seed.sql terlebih dahulu.\n";
        exit(1);
    }

    $db->beginTransaction();

    // Hapus data order dan reservasi dummy sebelumnya jika ada
    $db->exec('DELETE FROM order_items');
    $db->exec('DELETE FROM orders');
    $db->exec('DELETE FROM reservations');

    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $dayAfter = date('Y-m-d', strtotime('+2 days'));
    $now = date('Y-m-d H:i:s');

    // 1. Reservasi Besok: Budi Santoso (M01, 13:00 - 15:00, 2 tamu, confirmed)
    $stmt = $db->prepare('INSERT INTO reservations (customer_id, table_id, reserved_at, guest_count, notes, status, created_at, updated_at) VALUES (:cid, :tid, :res_at, :guests, :notes, :status, :created, :updated)');
    $stmt->execute([
        ':cid'     => $customers[0]['id'],
        ':tid'     => $tables[0]['id'], // M01
        ':res_at'  => "{$tomorrow} 13:00:00",
        ':guests'  => 2,
        ':notes'   => 'Dekat jendela jika memungkinkan',
        ':status'  => 'confirmed',
        ':created' => $now,
        ':updated' => $now,
    ]);
    $resId1 = $db->lastInsertId();

    // Tambah order untuk Reservasi Budi Santoso
    $orderStmt = $db->prepare('INSERT INTO orders (reservation_id, status, total, confirmed_at, created_at, updated_at) VALUES (:rid, :status, :total, :conf_at, :created, :updated)');
    $total1 = (2 * 25000) + (2 * 5000); // 2 Nasi Goreng + 2 Es Teh = 60000
    $orderStmt->execute([
        ':rid'     => $resId1,
        ':status'  => 'confirmed',
        ':total'   => $total1,
        ':conf_at' => $now,
        ':created' => $now,
        ':updated' => $now,
    ]);
    $orderId1 = $db->lastInsertId();

    // Items order 1
    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) VALUES (:oid, :mid, :qty, :price, :subtotal)');
    // 2 Nasi Goreng
    $itemStmt->execute([
        ':oid'      => $orderId1,
        ':mid'      => $menus[0]['id'],
        ':qty'      => 2,
        ':price'    => 25000,
        ':subtotal' => 50000,
    ]);
    // 2 Es Teh Manis
    $itemStmt->execute([
        ':oid'      => $orderId1,
        ':mid'      => $menus[3]['id'],
        ':qty'      => 2,
        ':price'    => 5000,
        ':subtotal' => 10000,
    ]);

    // 2. Reservasi Besok: Siti Rahayu (M04, 19:00 - 21:00, 4 tamu, pending)
    $stmt->execute([
        ':cid'     => $customers[1]['id'],
        ':tid'     => $tables[3]['id'], // M04
        ':res_at'  => "{$tomorrow} 19:00:00",
        ':guests'  => 4,
        ':notes'   => 'Ulang tahun keluarga',
        ':status'  => 'pending',
        ':created' => $now,
        ':updated' => $now,
    ]);

    // 3. Reservasi Lusa: Rina Wulandari (M05, 18:30 - 20:30, 5 tamu, pending)
    $stmt->execute([
        ':cid'     => $customers[2]['id'],
        ':tid'     => $tables[4]['id'], // M05
        ':res_at'  => "{$dayAfter} 18:30:00",
        ':guests'  => 5,
        ':notes'   => 'Pertemuan kantor',
        ':status'  => 'pending',
        ':created' => $now,
        ':updated' => $now,
    ]);

    $db->commit();
    echo "[SUKSES] Data reservasi dan order dinamis untuk tanggal {$tomorrow} dan {$dayAfter} berhasil disiapkan!\n";
    echo "Total 3 reservasi uji coba dan 1 pesanan terkonfirmasi siap didemonstrasikan.\n\n";
    exit(0);
} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "[ERROR] Gagal memasukkan data demo: " . $e->getMessage() . "\n";
    exit(1);
}
