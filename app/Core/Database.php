<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database — Koneksi PDO MySQL singleton.
 *
 * Membuat satu koneksi PDO yang dipakai bersama oleh seluruh
 * Repository dan Service. Konfigurasi dibaca dari environment.
 *
 * @package RestoReserve\Core
 */
class Database
{
    /** @var PDO|null Instance PDO tunggal */
    private static ?PDO $instance = null;

    /**
     * Ambil instance koneksi PDO.
     *
     * @return PDO Koneksi database aktif
     * @throws PDOException Jika koneksi gagal
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $db   = $_ENV['DB_DATABASE'] ?? 'restoreserve_db';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        }

        return self::$instance;
    }

    /**
     * Reset koneksi (untuk testing).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /** Mencegah instansiasi langsung. */
    private function __construct() {}
}
