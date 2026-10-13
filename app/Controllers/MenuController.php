<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\MenuRepository;

/**
 * MenuController — Mengelola data menu makanan & minuman.
 *
 * @package RestoReserve\Controllers
 */
class MenuController extends BaseController
{
    private MenuRepository $menuRepo;

    public function __construct()
    {
        $this->menuRepo = new MenuRepository();
    }

    /**
     * Tampilkan daftar menu makanan dan minuman.
     */
    public function index(): void
    {
        $this->requireAuth();
        $category = trim($_GET['category'] ?? '');
        $menus = $this->menuRepo->getAll($category);

        $this->view('menus/index', [
            'title'    => 'Daftar Menu Makanan & Minuman',
            'menus'    => $menus,
            'category' => $category,
        ]);
    }

    /**
     * Tampilkan form tambah menu baru.
     */
    public function create(): void
    {
        $this->requireAuth();
        $this->view('menus/create', [
            'title' => 'Tambah Menu Baru',
        ]);
    }

    /**
     * Simpan menu baru ke database.
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $name        = trim($_POST['name'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $price       = (int) ($_POST['price'] ?? 0);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        $errors = $this->validateMenuInput($name, $category, $price);

        if (!empty($errors)) {
            $this->redirectWithErrors('/menus/create', $errors, $_POST);
            return;
        }

        $this->menuRepo->create([
            'name'         => $name,
            'category'     => $category,
            'price'        => $price,
            'is_available' => $isAvailable,
        ]);

        flash('success', "Menu '{$name}' (" . formatRupiah($price) . ") berhasil ditambahkan.");
        $this->redirect('/menus');
    }

    /**
     * Tampilkan form edit menu.
     *
     * @param string|int $id
     */
    public function edit(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $menu = $this->menuRepo->findById($id);

        if (!$menu) {
            flash('error', 'Data menu tidak ditemukan.');
            $this->redirect('/menus');
            return;
        }

        $this->view('menus/edit', [
            'title' => 'Edit Data Menu',
            'menu'  => $menu,
        ]);
    }

    /**
     * Simpan update menu.
     *
     * @param string|int $id
     */
    public function update(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $menu = $this->menuRepo->findById($id);

        if (!$menu) {
            flash('error', 'Data menu tidak ditemukan.');
            $this->redirect('/menus');
            return;
        }

        $name        = trim($_POST['name'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $price       = (int) ($_POST['price'] ?? 0);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        $errors = $this->validateMenuInput($name, $category, $price);

        if (!empty($errors)) {
            $this->redirectWithErrors("/menus/{$id}/edit", $errors, $_POST);
            return;
        }

        $this->menuRepo->update($id, [
            'name'         => $name,
            'category'     => $category,
            'price'        => $price,
            'is_available' => $isAvailable,
        ]);

        flash('success', "Data menu '{$name}' berhasil diperbarui.");
        $this->redirect('/menus');
    }

    /**
     * Hapus menu makanan/minuman.
     *
     * @param string|int $id
     */
    public function delete(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $menu = $this->menuRepo->findById($id);

        if (!$menu) {
            flash('error', 'Data menu tidak ditemukan.');
            $this->redirect('/menus');
            return;
        }

        if ($this->menuRepo->hasOrderItems($id)) {
            flash('error', "Menu '{$menu['name']}' telah memiliki riwayat pesanan dan tidak boleh dihapus. Silakan ubah ketersediaan menjadi 'Habis' sebagai gantinya.");
            $this->redirect('/menus');
            return;
        }

        $this->menuRepo->delete($id);
        flash('success', "Menu '{$menu['name']}' berhasil dihapus.");
        $this->redirect('/menus');
    }

    /**
     * Validasi input menu.
     *
     * @param string $name
     * @param string $category
     * @param int    $price
     * @return array
     */
    private function validateMenuInput(string $name, string $category, int $price): array
    {
        $errors = [];

        if (mb_strlen($name) < 3 || mb_strlen($name) > 120) {
            $errors['name'] = 'Nama menu wajib diisi antara 3 sampai 120 karakter.';
        }

        if (!in_array($category, ['Makanan', 'Minuman'], true)) {
            $errors['category'] = 'Kategori menu harus berupa Makanan atau Minuman.';
        }

        if ($price <= 0) {
            $errors['price'] = 'Harga menu harus berupa bilangan bulat positif lebih besar dari 0.';
        }

        return $errors;
    }
}
