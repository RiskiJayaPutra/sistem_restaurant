<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ReportService;

/**
 * ReportController — Mengelola rekapitulasi data dan ekspor file CSV.
 *
 * @package RestoReserve\Controllers
 */
class ReportController extends BaseController
{
    private ReportService $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    /**
     * Tampilkan halaman rekapitulasi laporan periode.
     */
    public function index(): void
    {
        $this->requireAuth();

        // Default rentang tanggal: awal bulan ini s/d 7 hari ke depan
        $startDate = trim($_GET['start_date'] ?? date('Y-m-01'));
        $endDate   = trim($_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days')));

        // Validasi urutan tanggal
        if ($startDate > $endDate) {
            flash('warning', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir. Rentang disesuaikan otomatis.');
            $startDate = $endDate;
        }

        $reportData = $this->reportService->getReportData($startDate, $endDate);

        $this->view('reports/index', [
            'title'      => 'Laporan & Rekapitulasi Operasional',
            'reportData' => $reportData,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }

    /**
     * Unduh file CSV sesuai filter periode tanggal.
     */
    public function exportCsv(): void
    {
        $this->requireAuth();

        $startDate = trim($_GET['start_date'] ?? date('Y-m-01'));
        $endDate   = trim($_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days')));

        if ($startDate > $endDate) {
            $startDate = $endDate;
        }

        $reportData = $this->reportService->getReportData($startDate, $endDate);
        $filename   = "laporan_restoreserve_{$startDate}_sd_{$endDate}.csv";

        $this->reportService->exportCsv($reportData, $filename);
    }
}
