<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\ReservationRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\TableRepository;
use App\Services\ReservationService;

/**
 * ReservationController — Mengelola alur reservasi meja restoran.
 *
 * @package RestoReserve\Controllers
 */
class ReservationController extends BaseController
{
    private ReservationRepository $reservationRepo;
    private CustomerRepository $customerRepo;
    private TableRepository $tableRepo;
    private ReservationService $reservationService;

    public function __construct()
    {
        $this->reservationRepo    = new ReservationRepository();
        $this->customerRepo       = new CustomerRepository();
        $this->tableRepo          = new TableRepository();
        $this->reservationService = new ReservationService();
    }

    /**
     * Tampilkan daftar reservasi meja dengan opsi filter.
     */
    public function index(): void
    {
        $this->requireAuth();

        $filters = [
            'status'    => trim($_GET['status'] ?? ''),
            'from_date' => trim($_GET['from_date'] ?? ''),
            'to_date'   => trim($_GET['to_date'] ?? ''),
            'search'    => trim($_GET['search'] ?? ''),
        ];

        $reservations = $this->reservationRepo->getAll($filters);

        $this->view('reservations/index', [
            'title'        => 'Daftar Reservasi Meja',
            'reservations' => $reservations,
            'filters'      => $filters,
        ]);
    }

    /**
     * Tampilkan form pembuatan reservasi baru.
     */
    public function create(): void
    {
        $this->requireAuth();

        $customers    = $this->customerRepo->getAll();
        $activeTables = $this->tableRepo->getActiveTables();

        $this->view('reservations/create', [
            'title'        => 'Buat Reservasi Baru',
            'customers'    => $customers,
            'activeTables' => $activeTables,
        ]);
    }

    /**
     * Simpan reservasi baru ke database dengan pencegahan race condition.
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $tableId    = (int) ($_POST['table_id'] ?? 0);
        $reservedAt = trim($_POST['reserved_at'] ?? '');
        $guestCount = (int) ($_POST['guest_count'] ?? 0);
        $notes      = trim($_POST['notes'] ?? '');

        // Format datetime-local input jika ada 'T'
        if (str_contains($reservedAt, 'T')) {
            $reservedAt = str_replace('T', ' ', $reservedAt) . ':00';
        }

        $errors = $this->validateReservationInput($customerId, $tableId, $reservedAt, $guestCount);

        if (!empty($errors)) {
            $this->redirectWithErrors('/reservations/create', $errors, $_POST);
            return;
        }

        $result = $this->reservationService->createReservation([
            'customer_id' => $customerId,
            'table_id'    => $tableId,
            'reserved_at' => $reservedAt,
            'guest_count' => $guestCount,
            'notes'       => $notes,
        ]);

        if (!$result['success']) {
            $this->redirectWithErrors('/reservations/create', [
                'conflict' => $result['error']
            ], $_POST);
            return;
        }

        flash('success', 'Reservasi berhasil dibuat dengan status Pending.');
        $this->redirect('/reservations/' . $result['reservation_id']);
    }

    /**
     * Tampilkan detail lengkap reservasi.
     *
     * @param string|int $id
     */
    public function show(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $reservation = $this->reservationRepo->findById($id);

        if (!$reservation) {
            flash('error', 'Data reservasi tidak ditemukan.');
            $this->redirect('/reservations');
            return;
        }

        $this->view('reservations/show', [
            'title'       => "Detail Reservasi #{$id}",
            'reservation' => $reservation,
        ]);
    }

    /**
     * Tampilkan form edit reservasi (hanya diizinkan saat status pending).
     *
     * @param string|int $id
     */
    public function edit(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $reservation = $this->reservationRepo->findById($id);

        if (!$reservation) {
            flash('error', 'Data reservasi tidak ditemukan.');
            $this->redirect('/reservations');
            return;
        }

        if ($reservation['status'] !== 'pending') {
            flash('error', 'Hanya reservasi berstatus Pending yang dapat diubah.');
            $this->redirect("/reservations/{$id}");
            return;
        }

        $activeTables = $this->tableRepo->getActiveTables();

        $this->view('reservations/edit', [
            'title'        => "Edit Reservasi #{$id}",
            'reservation'  => $reservation,
            'activeTables' => $activeTables,
        ]);
    }

    /**
     * Simpan perubahan reservasi.
     *
     * @param string|int $id
     */
    public function update(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $tableId    = (int) ($_POST['table_id'] ?? 0);
        $reservedAt = trim($_POST['reserved_at'] ?? '');
        $guestCount = (int) ($_POST['guest_count'] ?? 0);
        $notes      = trim($_POST['notes'] ?? '');

        if (str_contains($reservedAt, 'T')) {
            $reservedAt = str_replace('T', ' ', $reservedAt) . ':00';
        }

        $errors = [];
        if ($tableId <= 0) $errors['table_id'] = 'Pilih meja yang valid.';
        if (empty($reservedAt)) $errors['reserved_at'] = 'Pilih jadwal tanggal dan jam reservasi.';
        if ($guestCount < 1) $errors['guest_count'] = 'Jumlah tamu minimal 1 orang.';

        if (!empty($errors)) {
            $this->redirectWithErrors("/reservations/{$id}/edit", $errors, $_POST);
            return;
        }

        $result = $this->reservationService->updateReservation($id, [
            'table_id'    => $tableId,
            'reserved_at' => $reservedAt,
            'guest_count' => $guestCount,
            'notes'       => $notes,
        ]);

        if (!$result['success']) {
            $this->redirectWithErrors("/reservations/{$id}/edit", [
                'conflict' => $result['error']
            ], $_POST);
            return;
        }

        flash('success', 'Jadwal reservasi berhasil diperbarui.');
        $this->redirect("/reservations/{$id}");
    }

    /**
     * Ubah status reservasi (confirmed, completed, cancelled).
     *
     * @param string|int $id
     */
    public function updateStatus(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $newStatus = trim($_POST['status'] ?? '');

        $result = $this->reservationService->changeStatus($id, $newStatus);

        if (!$result['success']) {
            flash('error', $result['error'] ?? 'Gagal mengubah status reservasi.');
        } else {
            flash('success', "Status reservasi berhasil diubah menjadi '" . ucfirst($newStatus) . "'.");
        }

        $this->redirect("/reservations/{$id}");
    }

    /**
     * Validasi format input awal reservasi.
     */
    private function validateReservationInput(int $customerId, int $tableId, string $reservedAt, int $guestCount): array
    {
        $errors = [];

        if ($customerId <= 0) {
            $errors['customer_id'] = 'Pilih pelanggan yang melakukan reservasi.';
        }

        if ($tableId <= 0) {
            $errors['table_id'] = 'Pilih meja restoran yang akan digunakan.';
        }

        if (empty($reservedAt)) {
            $errors['reserved_at'] = 'Tanggal dan jam mulai reservasi wajib diisi.';
        }

        if ($guestCount < 1 || $guestCount > 20) {
            $errors['guest_count'] = 'Jumlah tamu harus antara 1 sampai 20 orang.';
        }

        return $errors;
    }
}
