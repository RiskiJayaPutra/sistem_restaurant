<?php

/**
 * Router untuk PHP Built-in Web Server (CLI)
 *
 * Contoh penggunaan:
 * php -S 127.0.0.1:8000 -t public public/router.php
 *
 * Jika file statis (CSS, JS, gambar) ada di public/, langsung sajikan.
 * Jika tidak, teruskan request ke public/index.php.
 */

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filePath = __DIR__ . $uri;

// Jika file statis fisik ditemukan, biarkan PHP built-in server menyajikannya
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// Teruskan ke router aplikasi
require_once __DIR__ . '/index.php';
