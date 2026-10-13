<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Auth — Manajemen autentikasi Admin.
 *
 * Mengelola login admin tunggal, verifikasi password hash,
 * regenerasi ID sesi, proteksi route admin, dan logout.
 *
 * @package RestoReserve\Core
 */
class Auth
{
    private const SESSION_USER_ID = 'admin_user_id';
    private const SESSION_USERNAME = 'admin_username';

    /**
     * Coba melakukan login admin.
     *
     * @param string $username Username input
     * @param string $password Password input
     * @return bool True jika kredensial cocok
     */
    public static function attempt(string $username, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if (!$admin) {
            return false;
        }

        if (!password_verify($password, $admin['password_hash'])) {
            return false;
        }

        // Regenerasi session ID untuk mencegah session fixation
        session_regenerate_id(true);

        $_SESSION[self::SESSION_USER_ID] = (int) $admin['id'];
        $_SESSION[self::SESSION_USERNAME] = $admin['username'];

        return true;
    }

    /**
     * Cek apakah admin sedang login.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return !empty($_SESSION[self::SESSION_USER_ID]);
    }

    /**
     * Ambil ID admin yang sedang login.
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        return $_SESSION[self::SESSION_USER_ID] ?? null;
    }

    /**
     * Ambil username admin yang sedang login.
     *
     * @return string|null
     */
    public static function username(): ?string
    {
        return $_SESSION[self::SESSION_USERNAME] ?? null;
    }

    /**
     * Wajibkan autentikasi admin.
     * Jika belum login, redirect ke halaman /login.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            flash('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
            redirect('/login');
        }
    }

    /**
     * Wajibkan pengguna belum login (guest).
     * Jika sudah login, redirect ke dashboard (/).
     */
    public static function requireGuest(): void
    {
        if (self::check()) {
            redirect('/');
        }
    }

    /**
     * Lakukan logout admin: hapus data sesi dan cookie sesi.
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
