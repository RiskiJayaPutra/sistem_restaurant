<?php

/**
 * Script CLI — Pembuatan Akun Admin RestoReserve
 *
 * Mengizinkan pembuatan akun admin melalui terminal secara interaktif
 * atau melalui argumen: php scripts/create_admin.php [username] [password]
 * Password disimpan menggunakan password_hash() (bcrypt/argon2).
 *
 * @package RestoReserve\Scripts
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use App\Core\Database;

echo "====================================================\n";
echo "  RestoReserve — Pembuatan Akun Admin Sistem\n";
echo "====================================================\n\n";

// Ambil username dari argumen atau prompt
$username = $argv[1] ?? null;
if (empty($username)) {
    echo "Masukkan username admin [admin]: ";
    $handle = fopen('php://stdin', 'r');
    $input = trim(fgets($handle));
    $username = $input !== '' ? $input : 'admin';
}

// Ambil password dari argumen atau prompt
$password = $argv[2] ?? null;
if (empty($password)) {
    echo "Masukkan password untuk {$username}: ";
    $handle = fopen('php://stdin', 'r');
    $password = trim(fgets($handle));
}

if (strlen($password) < 6) {
    echo "\n[ERROR] Password minimal harus 6 karakter.\n";
    exit(1);
}

try {
    $db = Database::getInstance();

    // Cek apakah username sudah ada
    $checkStmt = $db->prepare('SELECT id FROM admins WHERE username = :username LIMIT 1');
    $checkStmt->execute([':username' => $username]);
    $existing = $checkStmt->fetch();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');

    if ($existing) {
        $updateStmt = $db->prepare('UPDATE admins SET password_hash = :hash, created_at = :now WHERE id = :id');
        $updateStmt->execute([
            ':hash' => $hash,
            ':now'  => $now,
            ':id'   => $existing['id'],
        ]);
        echo "\n[SUKSES] Password untuk admin '{$username}' berhasil diperbarui!\n";
    } else {
        $insertStmt = $db->prepare('INSERT INTO admins (username, password_hash, created_at) VALUES (:username, :hash, :now)');
        $insertStmt->execute([
            ':username' => $username,
            ':hash'     => $hash,
            ':now'      => $now,
        ]);
        echo "\n[SUKSES] Akun admin '{$username}' berhasil dibuat!\n";
    }

    echo "Kredensial tersimpan dengan aman menggunakan password_hash().\n\n";
    exit(0);
} catch (PDOException $e) {
    echo "\n[ERROR DATABASE] Gagal menyimpan admin: " . $e->getMessage() . "\n";
    exit(1);
}
