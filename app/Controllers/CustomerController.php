<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\CustomerRepository;

/**
 * CustomerController — Mengelola data pelanggan.
 *
 * @package RestoReserve\Controllers
 */
class CustomerController extends BaseController
{
    private CustomerRepository $customerRepo;

    public function __construct()
    {
        $this->customerRepo = new CustomerRepository();
    }

    /**
     * Tampilkan daftar pelanggan.
     */
    public function index(): void
    {
        $this->requireAuth();
        $search = trim($_GET['search'] ?? '');
        $customers = $this->customerRepo->getAll($search);

        $this->view('customers/index', [
            'title'     => 'Daftar Pelanggan',
            'customers' => $customers,
            'search'    => $search,
        ]);
    }

    /**
     * Tampilkan form tambah pelanggan baru.
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->view('customers/create', [
            'title' => 'Tambah Pelanggan Baru',
        ]);
    }

    /**
     * Simpan data pelanggan baru.
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = $this->validateCustomerInput($name, $phone, $email);

        if (!empty($errors)) {
            $this->redirectWithErrors('/customers/create', $errors, $_POST);
            return;
        }

        $this->customerRepo->create([
            'name'  => $name,
            'phone' => $phone,
            'email' => $email,
        ]);

        flash('success', "Pelanggan '{$name}' berhasil ditambahkan ke sistem.");
        $this->redirect('/customers');
    }

    /**
     * Tampilkan form edit pelanggan.
     *
     * @param string|int $id
     */
    public function edit(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            flash('error', 'Data pelanggan tidak ditemukan.');
            $this->redirect('/customers');
            return;
        }

        $this->view('customers/edit', [
            'title'    => 'Edit Data Pelanggan',
            'customer' => $customer,
        ]);
    }

    /**
     * Simpan perubahan data pelanggan.
     *
     * @param string|int $id
     */
    public function update(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            flash('error', 'Data pelanggan tidak ditemukan.');
            $this->redirect('/customers');
            return;
        }

        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = $this->validateCustomerInput($name, $phone, $email);

        if (!empty($errors)) {
            $this->redirectWithErrors("/customers/{$id}/edit", $errors, $_POST);
            return;
        }

        $this->customerRepo->update($id, [
            'name'  => $name,
            'phone' => $phone,
            'email' => $email,
        ]);

        flash('success', "Data pelanggan '{$name}' berhasil diperbarui.");
        $this->redirect('/customers');
    }

    /**
     * Hapus data pelanggan.
     *
     * @param string|int $id
     */
    public function delete(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $customer = $this->customerRepo->findById($id);

        if (!$customer) {
            flash('error', 'Data pelanggan tidak ditemukan.');
            $this->redirect('/customers');
            return;
        }

        // Cek integritas referensial: dilarang menghapus jika ada riwayat reservasi
        if ($this->customerRepo->hasReservations($id)) {
            flash('error', "Pelanggan '{$customer['name']}' memiliki riwayat reservasi dan tidak boleh dihapus demi integritas data.");
            $this->redirect('/customers');
            return;
        }

        $this->customerRepo->delete($id);
        flash('success', "Pelanggan '{$customer['name']}' berhasil dihapus.");
        $this->redirect('/customers');
    }

    /**
     * Validasi input data pelanggan.
     *
     * @param string $name
     * @param string $phone
     * @param string $email
     * @return array
     */
    private function validateCustomerInput(string $name, string $phone, string $email): array
    {
        $errors = [];

        if (mb_strlen($name) < 3 || mb_strlen($name) > 100) {
            $errors['name'] = 'Nama pelanggan wajib diisi antara 3 hingga 100 karakter.';
        }

        if (empty($phone)) {
            $errors['phone'] = 'Nomor telepon wajib diisi.';
        } elseif (!preg_match('/^\+?[0-9\s\-]{8,20}$/', $phone)) {
            $errors['phone'] = 'Format nomor telepon tidak valid (8-20 digit angka, boleh diawali + atau tanda hubung).';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format alamat email tidak valid.';
        }

        return $errors;
    }
}
