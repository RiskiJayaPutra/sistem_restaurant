<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Csrf — Proteksi Cross-Site Request Forgery.
 *
 * Menghasilkan token acak per sesi dan memverifikasinya
 * pada setiap request state-changing (POST).
 *
 * @package RestoReserve\Core
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Ambil token saat ini atau buat baru jika belum ada.
     *
     * @return string Token CSRF
     */
    public static function getToken(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Verifikasi token yang dikirim dari form POST.
     *
     * Menggunakan hash_equals() untuk mencegah timing attack.
     *
     * @param string|null $token Token dari input form ($_POST['_csrf_token'])
     * @return bool True jika valid
     */
    public static function validate(?string $token): bool
    {
        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';

        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Validasi request POST saat ini.
     * Jika tidak valid, hentikan eksekusi dengan HTTP 403.
     */
    public static function verifyOrAbort(): void
    {
        $token = $_POST['_csrf_token'] ?? null;

        if (!self::validate($token)) {
            http_response_code(403);
            flash('error', 'Token keamanan (CSRF) tidak valid atau telah kedaluwarsa. Silakan coba kembali.');
            
            // Tampilkan pesan error ramah jika bukan redirect
            echo "<!DOCTYPE html><html lang=\"id\"><head><meta charset=\"utf-8\"><title>403 — CSRF Error</title></head>";
            echo "<body style=\"font-family:sans-serif;text-align:center;padding:4rem\">";
            echo "<h1>403 — Token Keamanan Tidak Valid</h1>";
            echo "<p>Sesi Anda mungkin telah kedaluwarsa atau token form tidak cocok.</p>";
            echo "<p><a href=\"javascript:history.back()\">Kembali</a> | <a href=\"/\">Ke Beranda</a></p>";
            echo "</body></html>";
            exit;
        }
    }
}
