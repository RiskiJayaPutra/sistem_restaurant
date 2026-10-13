<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;

/**
 * DashboardController — Mengelola halaman ringkasan operasional restoran.
 *
 * @package RestoReserve\Controllers
 */
class DashboardController extends BaseController
{
    /**
     * Tampilkan statistik dashboard dan 5 reservasi mendatang.
     */
    public function index(): void
    {
        $this->requireAuth();
        $db = Database::getInstance();

        // 1. Total Pelanggan
        $totalCustomers = (int) $db->query('SELECT COUNT(*) FROM customers')->fetchColumn();

        // 2. Total Meja Aktif
        $activeTables = (int) $db->query('SELECT COUNT(*) FROM restaurant_tables WHERE is_active = 1')->fetchColumn();

        // 3. Jumlah Reservasi Hari Ini (selain cancelled)
        $todayReservations = (int) $db->query(
            "SELECT COUNT(*) FROM reservations WHERE DATE(reserved_at) = CURDATE() AND status <> 'cancelled'"
        )->fetchColumn();

        // 4. Total Nilai Pesanan Terkonfirmasi Bulan Ini
        $stmtMonthlyOrders = $db->query(
            "SELECT COALESCE(SUM(o.total), 0) 
             FROM orders o 
             JOIN reservations r ON o.reservation_id = r.id 
             WHERE o.status = 'confirmed' 
               AND MONTH(r.reserved_at) = MONTH(CURDATE()) 
               AND YEAR(r.reserved_at) = YEAR(CURDATE())"
        );
        $monthlyConfirmedRevenue = (int) $stmtMonthlyOrders->fetchColumn();

        // 5. Maksimal 5 Reservasi Mendatang
        $stmtUpcoming = $db->query(
            "SELECT r.*, c.name AS customer_name, c.phone AS customer_phone, 
                    t.table_number, t.capacity, 
                    o.status AS order_status, o.total AS order_total
             FROM reservations r
             JOIN customers c ON r.customer_id = c.id
             JOIN restaurant_tables t ON r.table_id = t.id
             LEFT JOIN orders o ON o.reservation_id = r.id
             WHERE r.reserved_at >= NOW() AND r.status <> 'cancelled'
             ORDER BY r.reserved_at ASC
             LIMIT 5"
        );
        $upcomingReservations = $stmtUpcoming->fetchAll();

        // 6. Denah Seluruh Meja & Okupansi Realtime
        $stmtTables = $db->query(
            "SELECT t.*,
                    (SELECT COUNT(*) FROM reservations r 
                     WHERE r.table_id = t.id 
                       AND DATE(r.reserved_at) = CURDATE() 
                       AND r.status IN ('pending', 'confirmed')) AS today_bookings_count
             FROM restaurant_tables t
             ORDER BY t.table_number ASC"
        );
        $allTables = $stmtTables->fetchAll();

        $this->view('dashboard/index', [
            'title'                   => 'Dashboard Operasional',
            'totalCustomers'          => $totalCustomers,
            'activeTables'            => $activeTables,
            'todayReservations'       => $todayReservations,
            'monthlyConfirmedRevenue' => $monthlyConfirmedRevenue,
            'upcomingReservations'    => $upcomingReservations,
            'allTables'               => $allTables,
        ]);
    }
}
