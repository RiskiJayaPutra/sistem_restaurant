<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;

/**
 * AuthController — Mengelola login dan logout admin.
 *
 * @package RestoReserve\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): void
    {
        $this->requireGuest();
        $this->view('auth/login', [
            'title' => 'Masuk Admin',
        ], 'auth');
    }

    /**
     * Proses otentikasi login admin.
     */
    public function login(): void
    {
        $this->requireGuest();
        $this->validateCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->redirectWithErrors('/login', [
                'general' => 'Username dan password wajib diisi.'
            ], ['username' => $username]);
            return;
        }

        // Coba otentikasi
        if (Auth::attempt($username, $password)) {
            flash('success', 'Selamat datang kembali, ' . $username . '!');
            $this->redirect('/');
            return;
        }

        // Kredensial salah — tampilkan pesan umum tanpa membocorkan keberadaan username
        $this->redirectWithErrors('/login', [
            'general' => 'Kombinasi username atau password yang dimasukkan tidak sesuai.'
        ], ['username' => $username]);
    }

    /**
     * Proses logout admin.
     */
    public function logout(): void
    {
        $this->validateCsrf();
        Auth::logout();
        flash('info', 'Anda telah berhasil keluar dari sistem.');
        $this->redirect('/login');
    }
}
