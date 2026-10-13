<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * ReportService — Agregasi Data Laporan & Rekapitulasi Operasional.
 *
 * Menghitung ringkasan berdasarkan tanggal reservasi lokal (Asia/Jakarta)
 * dan menyediakan ekspor CSV yang aman terhadap spreadsheet formula injection.
 *
 * @package RestoReserve\Services
 */
class ReportService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Dapatkan ringkasan statistik dan rekap reservasi untuk periode tertentu.
     *
     * @param string $startDate Tanggal mulai (Y-m-d)
     * @param string $endDate   Tanggal akhir (Y-m-d)
     * @return array
     */
    public function getReportData(string $startDate, string $endDate): array
    {
        // 1. Rekap seluruh reservasi dalam rentang tanggal
        $sql = "SELECT r.id, r.reserved_at, r.guest_count, r.status AS reservation_status, r.notes,
                       c.name AS customer_name, c.phone AS customer_phone,
                       t.table_number,
                       o.id AS order_id, o.status AS order_status, 
                       CASE WHEN o.status = 'confirmed' THEN o.total ELSE 0 END AS confirmed_order_total
                FROM reservations r
                JOIN customers c ON r.customer_id = c.id
                JOIN restaurant_tables t ON r.table_id = t.id
                LEFT JOIN orders o ON o.reservation_id = r.id
                WHERE DATE(r.reserved_at) >= :start_date 
                  AND DATE(r.reserved_at) <= :end_date
                ORDER BY r.reserved_at ASC, r.id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ]);
        $rows = $stmt->fetchAll();

        // 2. Hitung statistik agregat
        $totalReservations = count($rows);
        $statusCounts = [
            'pending'   => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];
        $confirmedOrdersCount = 0;
        $totalConfirmedRevenue = 0;

        foreach ($rows as $row) {
            $status = $row['reservation_status'];
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }

            if (($row['order_status'] ?? '') === 'confirmed') {
                $confirmedOrdersCount++;
                $totalConfirmedRevenue += (int) $row['confirmed_order_total'];
            }
        }

        return [
            'startDate'             => $startDate,
            'endDate'               => $endDate,
            'totalReservations'     => $totalReservations,
            'statusCounts'          => $statusCounts,
            'confirmedOrdersCount'  => $confirmedOrdersCount,
            'totalConfirmedRevenue' => $totalConfirmedRevenue,
            'rows'                  => $rows,
        ];
    }

    /**
     * Sanitasi sel teks untuk mencegah Formula Injection (CSV Injection).
     * Jika diawali =, +, -, @, \t, atau \r, tambahkan prefix kutip tunggal '.
     *
     * @param mixed $value
     * @return string
     */
    public static function sanitizeCsvValue(mixed $value): string
    {
        $str = (string) ($value ?? '');
        if ($str === '') {
            return '';
        }

        $dangerousChars = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array($str[0], $dangerousChars, true)) {
            return "'" . $str;
        }

        return $str;
    }

    /**
     * Hasilkan file CSV dan stream ke browser.
     *
     * @param array  $reportData
     * @param string $filename
     * @return void
     */
    public function exportCsv(array $reportData, string $filename = 'laporan_reservasi.csv'): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM untuk kompatibilitas Microsoft Excel Windows
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header Metadata Laporan
        fputcsv($output, ['RestoReserve — Laporan Rekapitulasi Reservasi & Pesanan']);
        fputcsv($output, ['Periode Tanggal', $reportData['startDate'] . ' s/d ' . $reportData['endDate']]);
        fputcsv($output, ['Total Reservasi', $reportData['totalReservations']]);
        fputcsv($output, ['Jumlah Pesanan Terkonfirmasi', $reportData['confirmedOrdersCount']]);
        fputcsv($output, ['Total Nilai Pesanan Terkonfirmasi', formatRupiah($reportData['totalConfirmedRevenue'])]);
        fputcsv($output, ['Catatan', 'Nilai pesanan adalah rekap data confirmed, bukan bukti penerimaan kas']);
        fputcsv($output, []); // Baris kosong

        // Header Kolom Tabel
        fputcsv($output, [
            'ID Reservasi',
            'Tanggal Reservasi',
            'Jam Mulai',
            'Jam Selesai',
            'Nama Pelanggan',
            'No. Telepon',
            'Meja',
            'Jumlah Tamu',
            'Status Reservasi',
            'ID Order',
            'Status Order',
            'Nilai Order Terkonfirmasi (Rp)',
            'Catatan Khusus',
        ]);

        // Baris Data
        foreach ($reportData['rows'] as $row) {
            $startTs = strtotime($row['reserved_at']);
            $endTs   = $startTs + 7200;

            fputcsv($output, [
                self::sanitizeCsvValue($row['id']),
                self::sanitizeCsvValue(date('Y-m-d', $startTs)),
                self::sanitizeCsvValue(date('H:i', $startTs)),
                self::sanitizeCsvValue(date('H:i', $endTs)),
                self::sanitizeCsvValue($row['customer_name']),
                self::sanitizeCsvValue($row['customer_phone']),
                self::sanitizeCsvValue('Meja ' . $row['table_number']),
                self::sanitizeCsvValue($row['guest_count']),
                self::sanitizeCsvValue(ucfirst($row['reservation_status'])),
                self::sanitizeCsvValue($row['order_id'] ?? '-'),
                self::sanitizeCsvValue(!empty($row['order_status']) ? ucfirst($row['order_status']) : 'Belum Ada'),
                self::sanitizeCsvValue($row['confirmed_order_total']),
                self::sanitizeCsvValue($row['notes'] ?? ''),
            ]);
        }

        fclose($output);
        exit;
    }
}
