<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ReservationRepository;

/**
 * OrderService — Aturan Bisnis & Orkestrasi Pesanan Makanan.
 *
 * Menjamin bahwa pesanan hanya dapat dibuat untuk reservasi berstatus 'confirmed',
 * mengonsolidasikan item duplikat, dan mengawasi siklus hidup order.
 *
 * @package RestoReserve\Services
 */
class OrderService
{
    private OrderRepository $orderRepo;
    private ReservationRepository $reservationRepo;

    public function __construct()
    {
        $this->orderRepo = new OrderRepository();
        $this->reservationRepo = new ReservationRepository();
    }

    /**
     * Buat draft pesanan baru untuk reservasi.
     *
     * @param int   $reservationId
     * @param array $rawItems Array [{menu_item_id, quantity}]
     * @return array ['success' => bool, 'order_id' => ?int, 'error' => ?string]
     */
    public function createOrder(int $reservationId, array $rawItems): array
    {
        // 1. Cek reservasi
        $reservation = $this->reservationRepo->findById($reservationId);

        if (!$reservation) {
            return ['success' => false, 'order_id' => null, 'error' => 'Data reservasi tidak ditemukan.'];
        }

        // 2. Wajib berstatus 'confirmed'
        if ($reservation['status'] !== 'confirmed') {
            return [
                'success'        => false,
                'order_id'       => null,
                'error'          => "Pesanan makanan hanya dapat dibuat untuk reservasi yang telah berstatus 'Confirmed'. Status saat ini: " . ucfirst($reservation['status'])
            ];
        }

        // 3. Pastikan belum ada pesanan sebelumnya untuk reservasi ini
        $existingOrder = $this->orderRepo->findByReservationId($reservationId);
        if ($existingOrder) {
            return [
                'success'        => false,
                'order_id'       => null,
                'error'          => 'Reservasi ini sudah memiliki pesanan makanan terdaftar (# ' . $existingOrder['id'] . ').'
            ];
        }

        // 4. Konsolidasi item duplikat
        $items = OrderCalculator::consolidateItems($rawItems);

        if (empty($items)) {
            return [
                'success'        => false,
                'order_id'       => null,
                'error'          => 'Pesanan harus memilih minimal 1 menu makanan atau minuman.'
            ];
        }

        try {
            $orderId = $this->orderRepo->createWithItems($reservationId, $items);
            return ['success' => true, 'order_id' => $orderId, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'order_id' => null, 'error' => 'Gagal membuat pesanan: ' . $e->getMessage()];
        }
    }

    /**
     * Perbarui item pesanan draft.
     *
     * @param int   $orderId
     * @param array $rawItems
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function updateDraftOrder(int $orderId, array $rawItems): array
    {
        $order = $this->orderRepo->findById($orderId);

        if (!$order) {
            return ['success' => false, 'error' => 'Data pesanan tidak ditemukan.'];
        }

        if ($order['status'] !== 'draft') {
            return ['success' => false, 'error' => 'Hanya pesanan berstatus Draft yang dapat diperbarui.'];
        }

        $items = OrderCalculator::consolidateItems($rawItems);

        if (empty($items)) {
            return ['success' => false, 'error' => 'Pesanan harus memiliki minimal 1 item menu.'];
        }

        try {
            $this->orderRepo->updateDraftItems($orderId, $items);
            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Gagal memperbarui pesanan: ' . $e->getMessage()];
        }
    }

    /**
     * Konfirmasi pesanan draft menjadi confirmed.
     *
     * @param int $orderId
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function confirmOrder(int $orderId): array
    {
        $order = $this->orderRepo->findById($orderId);

        if (!$order) {
            return ['success' => false, 'error' => 'Data pesanan tidak ditemukan.'];
        }

        if ($order['status'] !== 'draft') {
            return ['success' => false, 'error' => "Pesanan dengan status '{$order['status']}' tidak dapat dikonfirmasi ulang."];
        }

        if (empty($order['items'])) {
            return ['success' => false, 'error' => 'Pesanan tidak memiliki item menu untuk dikonfirmasi.'];
        }

        $this->orderRepo->confirmOrder($orderId);
        return ['success' => true, 'error' => null];
    }

    /**
     * Batalkan pesanan.
     *
     * @param int $orderId
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function cancelOrder(int $orderId): array
    {
        $order = $this->orderRepo->findById($orderId);

        if (!$order) {
            return ['success' => false, 'error' => 'Data pesanan tidak ditemukan.'];
        }

        if ($order['status'] === 'cancelled') {
            return ['success' => false, 'error' => 'Pesanan ini sudah dibatalkan sebelumnya.'];
        }

        $this->orderRepo->cancelOrder($orderId);
        return ['success' => true, 'error' => null];
    }
}
