<?php

declare(strict_types=1);

namespace App\Core;

/**
 * BaseController — Induk seluruh Controller aplikasi.
 *
 * Menyediakan metode utilitas umum seperti render view,
 * verifikasi CSRF, pemeriksaan autentikasi, dan redirect.
 *
 * @package RestoReserve\Core
 */
abstract class BaseController
{
    /**
     * Render view template dengan data.
     *
     * @param string      $view   Nama view (contoh: 'customers/index')
     * @param array       $data   Data untuk view
     * @param string|null $layout Nama layout (default: 'main')
     */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Pastikan request memiliki CSRF token yang valid.
     * Menghentikan eksekusi jika tidak valid.
     */
    protected function validateCsrf(): void
    {
        Csrf::verifyOrAbort();
    }

    /**
     * Wajibkan admin login untuk mengakses endpoint ini.
     */
    protected function requireAuth(): void
    {
        Auth::requireAuth();
    }

    /**
     * Wajibkan pengguna belum login (untuk halaman login).
     */
    protected function requireGuest(): void
    {
        Auth::requireGuest();
    }

    /**
     * Redirect ke URL tujuan.
     *
     * @param string $url URL tujuan
     */
    protected function redirect(string $url): void
    {
        redirect($url);
    }

    /**
     * Redirect kembali ke form dengan data error dan input lama.
     *
     * @param string $url    URL form
     * @param array  $errors Array pesan error ['field' => 'pesan']
     * @param array  $input  Data input yang diisi pengguna
     */
    protected function redirectWithErrors(string $url, array $errors, array $input = []): void
    {
        flash_errors($errors);
        if (!empty($input)) {
            flash_old($input);
        }
        flash('error', 'Terdapat kesalahan pada isian formulir. Mohon periksa kembali.');
        redirect($url);
    }

    /**
     * Kirim respons JSON.
     *
     * @param mixed $data Data respons
     * @param int   $code HTTP status code
     */
    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }
}
