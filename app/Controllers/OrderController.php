<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Repositories\OrderRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\MenuRepository;
use App\Services\OrderService;

/**
 * OrderController — Mengelola alur pesanan makanan dan detail item.
 *
 * @package RestoReserve\Controllers
 */
class OrderController extends BaseController
{
    private OrderRepository $orderRepo;
    private ReservationRepository $reservationRepo;
    private MenuRepository $menuRepo;
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderRepo       = new OrderRepository();
        $this->reservationRepo = new ReservationRepository();
        $this->menuRepo        = new MenuRepository();
        $this->orderService    = new OrderService();
    }

    /**
     * Tampilkan daftar pesanan makanan.
     */
    public function index(): void
    {
        $this->requireAuth();
        $status = trim($_GET['status'] ?? '');
        $orders = $this->orderRepo->getAll($status);

        $this->view('orders/index', [
            'title'  => 'Daftar Pesanan Makanan',
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    /**
     * Tampilkan form pembuatan pesanan baru untuk reservasi confirmed.
     */
    public function create(): void
    {
        $this->requireAuth();

        $reservationId = (int) ($_GET['reservation_id'] ?? 0);

        if ($reservationId <= 0) {
            flash('error', 'Pilih reservasi terlebih dahulu untuk membuat pesanan makanan.');
            $this->redirect('/reservations');
            return;
        }

        $reservation = $this->reservationRepo->findById($reservationId);

        if (!$reservation) {
            flash('error', 'Data reservasi tidak ditemukan.');
            $this->redirect('/reservations');
            return;
        }

        if ($reservation['status'] !== 'confirmed') {
            flash('error', "Pesanan hanya dapat dibuat untuk reservasi yang telah berstatus 'Confirmed'.");
            $this->redirect("/reservations/{$reservationId}");
            return;
        }

        if (!empty($reservation['order_id'])) {
            flash('info', 'Reservasi ini sudah memiliki pesanan makanan.');
            $this->redirect("/orders/{$reservation['order_id']}");
            return;
        }

        $availableMenus = $this->menuRepo->getAvailableMenus();

        $this->view('orders/create', [
            'title'          => "Buat Pesanan Makanan — Reservasi #{$reservationId}",
            'reservation'    => $reservation,
            'availableMenus' => $availableMenus,
        ]);
    }

    /**
     * Simpan pesanan draft baru beserta snapshot harga itemnya.
     */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        $rawItems      = $_POST['items'] ?? [];

        if (empty($rawItems) || !is_array($rawItems)) {
            $this->redirectWithErrors("/orders/create?reservation_id={$reservationId}", [
                'items' => 'Minimal pilih 1 item menu makanan atau minuman.'
            ]);
            return;
        }

        $result = $this->orderService->createOrder($reservationId, $rawItems);

        if (!$result['success']) {
            $this->redirectWithErrors("/orders/create?reservation_id={$reservationId}", [
                'general' => $result['error']
            ]);
            return;
        }

        flash('success', 'Draft pesanan makanan berhasil disimpan. Total dihitung otomatis di server.');
        $this->redirect('/orders/' . $result['order_id']);
    }

    /**
     * Tampilkan rincian pesanan makanan.
     *
     * @param string|int $id
     */
    public function show(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $order = $this->orderRepo->findById($id);

        if (!$order) {
            flash('error', 'Data pesanan tidak ditemukan.');
            $this->redirect('/orders');
            return;
        }

        $this->view('orders/show', [
            'title' => "Rincian Pesanan #{$id}",
            'order' => $order,
        ]);
    }

    /**
     * Tampilkan form edit item pesanan draft.
     *
     * @param string|int $id
     */
    public function edit(string|int $id): void
    {
        $this->requireAuth();
        $id = (int) $id;
        $order = $this->orderRepo->findById($id);

        if (!$order) {
            flash('error', 'Data pesanan tidak ditemukan.');
            $this->redirect('/orders');
            return;
        }

        if ($order['status'] !== 'draft') {
            flash('error', 'Hanya pesanan berstatus Draft yang dapat diubah rinciannya.');
            $this->redirect("/orders/{$id}");
            return;
        }

        $availableMenus = $this->menuRepo->getAvailableMenus();

        $this->view('orders/edit', [
            'title'          => "Edit Item Pesanan #{$id}",
            'order'          => $order,
            'availableMenus' => $availableMenus,
        ]);
    }

    /**
     * Simpan update item pesanan draft.
     *
     * @param string|int $id
     */
    public function update(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $rawItems = $_POST['items'] ?? [];

        if (empty($rawItems) || !is_array($rawItems)) {
            $this->redirectWithErrors("/orders/{$id}/edit", [
                'items' => 'Minimal harus memilih 1 menu makanan atau minuman.'
            ]);
            return;
        }

        $result = $this->orderService->updateDraftOrder($id, $rawItems);

        if (!$result['success']) {
            $this->redirectWithErrors("/orders/{$id}/edit", [
                'general' => $result['error']
            ]);
            return;
        }

        flash('success', 'Rincian pesanan draft berhasil diperbarui.');
        $this->redirect("/orders/{$id}");
    }

    /**
     * Konfirmasi pesanan draft menjadi confirmed.
     *
     * @param string|int $id
     */
    public function confirm(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $result = $this->orderService->confirmOrder($id);

        if (!$result['success']) {
            flash('error', $result['error'] ?? 'Gagal mengonfirmasi pesanan.');
        } else {
            flash('success', 'Pesanan makanan berhasil dikonfirmasi.');
        }

        $this->redirect("/orders/{$id}");
    }

    /**
     * Batalkan pesanan makanan.
     *
     * @param string|int $id
     */
    public function cancel(string|int $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int) $id;
        $result = $this->orderService->cancelOrder($id);

        if (!$result['success']) {
            flash('error', $result['error'] ?? 'Gagal membatalkan pesanan.');
        } else {
            flash('success', 'Pesanan makanan telah dibatalkan.');
        }

        $this->redirect("/orders/{$id}");
    }
}
