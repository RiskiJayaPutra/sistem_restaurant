<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\TableRepository;

/**
 * TableController — Mengelola data meja restoran.
 *
 * @package RestoReserve\Controllers
 */
class TableController extends BaseController
{
    private TableRepository $tableRepo;

    public function __construct()
    {
        $this->tableRepo = new TableRepository();
    }

    /**
     * Tampilkan daftar meja restoran.
     */
    public function index(): void
    {
        $this->requireAuth();
        $tables = $this->tableRepo->getAll();

        $this->view('tables/index', [
            'title'  => 'Manajemen Meja Restoran',
            'tables' => $tables,
        ]);
    }

    /**
     * Tampilkan form tambah meja baru.
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->view('tables/create', [
            'title' => 'Tambah Meja Baru',
        ]);
    }

    /**
     * Simpan meja baru ke database.
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $tableNumber = strtoupper(trim($_POST['table_number'] ?? ''));
        $capacity    = (int) ($_POST['capacity'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        $errors = $this->validateTableInput($tableNumber, $capacity);

        if ($this->tableRepo->isTableNumberExists($tableNumber)) {
            $errors['table_number'] = "Nomor meja '{$tableNumber}' sudah digunakan. Gunakan nomor meja lain.";
        }

        if (!empty($errors)) {
            $this->redirectWithErrors('/tables/create', $errors, $_POST);
            return;
        }

        $this->tableRepo->create([
            'table_number' => $tableNumber,
            'capacity'     => $capacity,
            'is_active'    => $isActive,
        ]);

        flash('success', "Meja {$tableNumber} (Kapasitas {$capacity} orang) berhasil ditambahkan.");
        $this->redirect('/tables');
    }

    /**
     * Tampilkan form edit meja.
     *
     * @param string|int $id
     */
    public function edit(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $table = $this->tableRepo->findById($id);

        if (!$table) {
            flash('error', 'Data meja tidak ditemukan.');
            $this->redirect('/tables');
            return;
        }

        $this->view('tables/edit', [
            'title' => 'Edit Data Meja',
            'table' => $table,
        ]);
    }

    /**
     * Simpan update meja.
     *
     * @param string|int $id
     */
    public function update(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $table = $this->tableRepo->findById($id);

        if (!$table) {
            flash('error', 'Data meja tidak ditemukan.');
            $this->redirect('/tables');
            return;
        }

        $tableNumber = strtoupper(trim($_POST['table_number'] ?? ''));
        $capacity    = (int) ($_POST['capacity'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        $errors = $this->validateTableInput($tableNumber, $capacity);

        if ($this->tableRepo->isTableNumberExists($tableNumber, $id)) {
            $errors['table_number'] = "Nomor meja '{$tableNumber}' sudah digunakan oleh meja lain.";
        }

        if (!empty($errors)) {
            $this->redirectWithErrors("/tables/{$id}/edit", $errors, $_POST);
            return;
        }

        $this->tableRepo->update($id, [
            'table_number' => $tableNumber,
            'capacity'     => $capacity,
            'is_active'    => $isActive,
        ]);

        flash('success', "Data meja {$tableNumber} berhasil diperbarui.");
        $this->redirect('/tables');
    }

    /**
     * Hapus meja restoran.
     *
     * @param string|int $id
     */
    public function delete(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $table = $this->tableRepo->findById($id);

        if (!$table) {
            flash('error', 'Data meja tidak ditemukan.');
            $this->redirect('/tables');
            return;
        }

        if ($this->tableRepo->hasReservations($id)) {
            flash('error', "Meja {$table['table_number']} memiliki riwayat reservasi dan tidak boleh dihapus. Silakan ubah status menjadi Nonaktif.");
            $this->redirect('/tables');
            return;
        }

        $this->tableRepo->delete($id);
        flash('success', "Meja {$table['table_number']} berhasil dihapus.");
        $this->redirect('/tables');
    }

    /**
     * Validasi field input meja.
     *
     * @param string $tableNumber
     * @param int    $capacity
     * @return array
     */
    private function validateTableInput(string $tableNumber, int $capacity): array
    {
        $errors = [];

        if (empty($tableNumber) || mb_strlen($tableNumber) > 20) {
            $errors['table_number'] = 'Nomor meja wajib diisi maksimal 20 karakter (contoh: M01).';
        }

        if ($capacity < 1 || $capacity > 20) {
            $errors['capacity'] = 'Kapasitas meja harus antara 1 sampai 20 orang tamu.';
        }

        return $errors;
    }
}
