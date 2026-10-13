<?php

/**
 * Helper Functions — RestoReserve
 *
 * Fungsi utilitas global yang digunakan di seluruh aplikasi:
 * escaping HTML, format Rupiah, CSRF, flash message, dan redirect.
 *
 * @package RestoReserve
 */

declare(strict_types=1);

/**
 * Escape string untuk output HTML aman (mencegah XSS).
 *
 * @param string|null $value Nilai yang akan di-escape
 * @return string Nilai yang sudah di-escape
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka Rupiah bilangan bulat.
 *
 * @param int|string $amount Nilai nominal
 * @return string Format "Rp25.000"
 */
function formatRupiah(int|string $amount): string
{
    $amount = (int) $amount;
    return 'Rp' . number_format($amount, 0, ',', '.');
}

/**
 * Ambil atau buat CSRF token dari session.
 *
 * @return string Token CSRF
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Cetak hidden input CSRF untuk form.
 *
 * @return string HTML hidden input
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Set flash message ke session.
 *
 * @param string $type  Tipe pesan: 'success', 'error', 'info', 'warning'
 * @param string $message Isi pesan
 */
function flash(string $type, string $message): void
{
    $_SESSION['_flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Ambil dan hapus flash message dari session (sekali baca).
 *
 * @return array|null Array ['type' => ..., 'message' => ...] atau null
 */
function get_flash(): ?array
{
    $flash = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $flash;
}

/**
 * Simpan data input lama ke session untuk form repopulation.
 *
 * @param array $data Data POST yang akan disimpan
 */
function flash_old(array $data): void
{
    // Jangan simpan password atau CSRF token
    unset($data['password'], $data['_csrf_token']);
    $_SESSION['_old_input'] = $data;
}

/**
 * Ambil nilai input lama untuk repopulation form.
 *
 * @param string $key  Nama field
 * @param string $default Nilai default jika tidak ada
 * @return string
 */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['_old_input'][$key] ?? $default;
    unset($_SESSION['_old_input'][$key]);
    return $value;
}

/**
 * Simpan error validasi ke session.
 *
 * @param array $errors Array asosiatif ['field' => 'pesan error']
 */
function flash_errors(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

/**
 * Ambil error validasi dari session (sekali baca).
 *
 * @return array
 */
function get_errors(): array
{
    $errors = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors']);
    return $errors;
}

/**
 * Ambil error untuk field tertentu.
 *
 * @param string $field Nama field
 * @param array  $errors Array error yang sudah diambil
 * @return string|null
 */
function error_for(string $field, array $errors): ?string
{
    return $errors[$field] ?? null;
}

/**
 * Redirect ke URL tertentu.
 *
 * @param string $url  URL tujuan
 * @param int    $code HTTP status code (default 302)
 */
function redirect(string $url, int $code = 302): never
{
    header("Location: $url", true, $code);
    exit;
}

/**
 * Cek apakah admin sudah login.
 *
 * @return bool
 */
function is_authenticated(): bool
{
    return !empty($_SESSION['admin_id']);
}

/**
 * Paksa autentikasi — redirect ke login jika belum login.
 */
function require_auth(): void
{
    if (!is_authenticated()) {
        redirect('/login');
    }
}

/**
 * Ambil base URL aplikasi.
 *
 * @param string $path Path relatif (opsional)
 * @return string URL lengkap
 */
function url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    return $scheme . '://' . $host . '/' . ltrim($path, '/');
}

/**
 * Ambil path ke file asset (CSS/JS).
 *
 * @param string $path Path relatif dari public/assets/
 * @return string URL asset
 */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}
