<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Core\Database;

/**
 * ReservationService — Orkestrasi Aturan Bisnis & Alur Reservasi.
 *
 * Menggabungkan aturan domain BookingRules dengan repositori database
 * dan validasi integritas status keterkaitan pesanan makanan.
 *
 * @package RestoReserve\Services
 */
class ReservationService
{
    private ReservationRepository $reservationRepo;

    public function __construct()
    {
        $this->reservationRepo = new ReservationRepository();
    }

    /**
     * Buat reservasi baru dengan validasi bisnis lengkap.
     *
     * @param array $data ['customer_id', 'table_id', 'reserved_at', 'guest_count', 'notes']
     * @return array ['success' => bool, 'reservation_id' => ?int, 'error' => ?string]
     */
    public function createReservation(array $data): array
    {
        // 1. Validasi waktu masa depan
        if (!BookingRules::isFutureTime($data['reserved_at'])) {
            return [
                'success'        => false,
                'reservation_id' => null,
                'error'          => 'Jadwal reservasi harus di masa depan (tidak boleh di waktu lampau).'
            ];
        }

        // 2. Eksekusi repositori dengan transaksi dan lock
        return $this->reservationRepo->createWithLock($data);
    }

    /**
     * Perbarui data reservasi (hanya diizinkan saat status pending).
     *
     * @param int   $id
     * @param array $data
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function updateReservation(int $id, array $data): array
    {
        if (!BookingRules::isFutureTime($data['reserved_at'])) {
            return [
                'success' => false,
                'error'   => 'Jadwal reservasi baru harus di masa depan.'
            ];
        }

        return $this->reservationRepo->updateWithLock($id, $data);
    }

    /**
     * Ubah status reservasi dengan pemeriksaan aturan bisnis transisi.
     *
     * @param int    $id
     * @param string $newStatus 'confirmed', 'completed', 'cancelled'
     * @return array ['success' => bool, 'error' => ?string]
     */
    public function changeStatus(int $id, string $newStatus): array
    {
        $reservation = $this->reservationRepo->findById($id);

        if (!$reservation) {
            return ['success' => false, 'error' => 'Data reservasi tidak ditemukan.'];
        }

        $currentStatus = $reservation['status'];

        // Cek validitas transisi status
        if (!BookingRules::canTransitionStatus($currentStatus, $newStatus)) {
            return [
                'success' => false,
                'error'   => "Perubahan status dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan oleh sistem."
            ];
        }

        $orderStatus = $reservation['order_status'] ?? null;

        // Aturan saat membatalkan reservasi:
        // Jika ada order yang masih 'draft' atau 'confirmed', minta admin selesaikan/batalkan order terlebih dahulu
        if ($newStatus === 'cancelled' && $orderStatus !== null && in_array($orderStatus, ['draft', 'confirmed'], true)) {
            return [
                'success' => false,
                'error'   => "Reservasi ini memiliki pesanan makanan dengan status '{$orderStatus}'. Batalkan pesanan makanan terlebih dahulu sebelum membatalkan reservasi."
            ];
        }

        // Aturan saat menyelesaikan reservasi:
        // Jika order masih 'draft', admin harus mengonfirmasi atau membatalkan pesanan terlebih dahulu
        if ($newStatus === 'completed' && $orderStatus === 'draft') {
            return [
                'success' => false,
                'error'   => "Pesanan makanan pada reservasi ini masih berstatus 'Draft'. Mohon konfirmasi atau batalkan pesanan terlebih dahulu."
            ];
        }

        $this->reservationRepo->updateStatus($id, $newStatus);

        return ['success' => true, 'error' => null];
    }
}
