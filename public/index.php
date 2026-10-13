<?php

/**
 * Entry Point Aplikasi RestoReserve
 *
 * Seluruh HTTP request dialihkan ke file ini.
 * Memuat bootstrap dan mendaftarkan rute sebelum dispatch.
 *
 * @package RestoReserve
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\CustomerController;
use App\Controllers\TableController;
use App\Controllers\MenuController;
use App\Controllers\ReservationController;
use App\Controllers\OrderController;
use App\Controllers\ReportController;

$router = new Router();

// ------------------------------------------------------------
// Autentikasi Admin
// ------------------------------------------------------------
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// ------------------------------------------------------------
// Dashboard
// ------------------------------------------------------------
$router->get('/', [DashboardController::class, 'index']);

// ------------------------------------------------------------
// CRUD Pelanggan (Customers)
// ------------------------------------------------------------
$router->get('/customers', [CustomerController::class, 'index']);
$router->get('/customers/create', [CustomerController::class, 'create']);
$router->post('/customers', [CustomerController::class, 'store']);
$router->get('/customers/{id}/edit', [CustomerController::class, 'edit']);
$router->post('/customers/{id}/update', [CustomerController::class, 'update']);
$router->post('/customers/{id}/delete', [CustomerController::class, 'delete']);

// ------------------------------------------------------------
// CRUD Meja Restoran (Tables)
// ------------------------------------------------------------
$router->get('/tables', [TableController::class, 'index']);
$router->get('/tables/create', [TableController::class, 'create']);
$router->post('/tables', [TableController::class, 'store']);
$router->get('/tables/{id}/edit', [TableController::class, 'edit']);
$router->post('/tables/{id}/update', [TableController::class, 'update']);
$router->post('/tables/{id}/delete', [TableController::class, 'delete']);

// ------------------------------------------------------------
// CRUD Menu Makanan & Minuman (Menus)
// ------------------------------------------------------------
$router->get('/menus', [MenuController::class, 'index']);
$router->get('/menus/create', [MenuController::class, 'create']);
$router->post('/menus', [MenuController::class, 'store']);
$router->get('/menus/{id}/edit', [MenuController::class, 'edit']);
$router->post('/menus/{id}/update', [MenuController::class, 'update']);
$router->post('/menus/{id}/delete', [MenuController::class, 'delete']);

// ------------------------------------------------------------
// Reservasi Meja (Reservations)
// ------------------------------------------------------------
$router->get('/reservations', [ReservationController::class, 'index']);
$router->get('/reservations/create', [ReservationController::class, 'create']);
$router->post('/reservations', [ReservationController::class, 'store']);
$router->get('/reservations/{id}', [ReservationController::class, 'show']);
$router->get('/reservations/{id}/edit', [ReservationController::class, 'edit']);
$router->post('/reservations/{id}/update', [ReservationController::class, 'update']);
$router->post('/reservations/{id}/status', [ReservationController::class, 'updateStatus']);

// ------------------------------------------------------------
// Pesanan Makanan (Orders)
// ------------------------------------------------------------
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/create', [OrderController::class, 'create']);
$router->post('/orders', [OrderController::class, 'store']);
$router->get('/orders/{id}', [OrderController::class, 'show']);
$router->get('/orders/{id}/edit', [OrderController::class, 'edit']);
$router->post('/orders/{id}/update', [OrderController::class, 'update']);
$router->post('/orders/{id}/confirm', [OrderController::class, 'confirm']);
$router->post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

// ------------------------------------------------------------
// Laporan & Rekapitulasi (Reports)
// ------------------------------------------------------------
$router->get('/reports', [ReportController::class, 'index']);
$router->get('/reports/export.csv', [ReportController::class, 'exportCsv']);

// Jalankan pencocokan dan eksekusi rute
$router->dispatch();
