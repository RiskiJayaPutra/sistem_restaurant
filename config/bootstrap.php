<?php

/**
 * Bootstrap Aplikasi RestoReserve
 *
 * File ini memuat konfigurasi awal, autoload Composer, environment,
 * timezone, dan konfigurasi session. Dipanggil oleh public/index.php.
 *
 * @package RestoReserve
 */

declare(strict_types=1);

// ---------- Autoload Composer ----------
$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    http_response_code(500);
    echo 'Jalankan "composer install" terlebih dahulu.';
    exit(1);
}

require_once $autoloadPath;

// ---------- Load Environment ----------
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// ---------- Timezone ----------
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Jakarta');

// ---------- Error Reporting ----------
$isDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

if ($isDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '0'); // Jangan tampilkan ke browser
    ini_set('log_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ---------- Session ----------
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecure,
        'httponly'  => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
