<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * BookingRules — Aturan Bisnis Reservasi Murni (Domain Logic).
 *
 * Dirancang independen dari database sehingga dapat diuji secara
 * menyeluruh melalui Unit Testing PHPUnit.
 *
 * @package RestoReserve\Services
 */
class BookingRules
{
    /** Durasi standar setiap reservasi dalam detik (120 menit = 2 jam). */
    public const DURATION_SECONDS = 7200;

    /**
     * Hitung waktu selesai reservasi (waktu mulai + 2 jam).
     *
     * @param string $start Datetime format 'Y-m-d H:i:s' atau 'Y-m-d\TH:i'
     * @return string
     * @throws InvalidArgumentException
     */
    public static function calculateEndTime(string $start): string
    {
        $dt = new DateTimeImmutable($start);
        $end = $dt->modify('+2 hours');
        return $end->format('Y-m-d H:i:s');
    }

    /**
     * Periksa apakah dua interval waktu reservasi saling bentrok (overlap).
     *
     * Rumus: startA < endB && endA > startB
     * Bersebelahan tepat (contoh: 18:00-20:00 vs 20:00-22:00) TIDAK bentrok.
     *
     * @param string $startA
     * @param string $endA
     * @param string $startB
     * @param string $endB
     * @return bool True jika bertabrakan
     */
    public static function isOverlapping(string $startA, string $endA, string $startB, string $endB): bool
    {
        $dtStartA = new DateTimeImmutable($startA);
        $dtEndA   = new DateTimeImmutable($endA);
        $dtStartB = new DateTimeImmutable($startB);
        $dtEndB   = new DateTimeImmutable($endB);

        return ($dtStartA < $dtEndB) && ($dtEndA > $dtStartB);
    }

    /**
     * Validasi jumlah tamu terhadap kapasitas meja.
     *
     * @param int $guestCount Jumlah tamu yang diajukan
     * @param int $capacity   Kapasitas maksimal meja
     * @return bool True jika memenuhi syarat (1 <= guests <= capacity)
     */
    public static function validateCapacity(int $guestCount, int $capacity): bool
    {
        return $guestCount >= 1 && $guestCount <= $capacity;
    }

    /**
     * Validasi apakah waktu reservasi berada di masa depan.
     *
     * @param string $reservedAt Datetime yang diajukan
     * @param string $now        Waktu acuan (default: waktu server saat ini)
     * @return bool True jika waktu reservasi > waktu acuan
     */
    public static function isFutureTime(string $reservedAt, ?string $now = null): bool
    {
        $dtReserved = new DateTimeImmutable($reservedAt);
        $dtNow = $now !== null ? new DateTimeImmutable($now) : new DateTimeImmutable('now');

        return $dtReserved > $dtNow;
    }

    /**
     * Validasi transisi status reservasi yang sah.
     *
     * Aturan transisi:
     * - pending   -> confirmed, cancelled
     * - confirmed -> completed, cancelled
     * - completed -> terminal (tidak bisa diubah)
     * - cancelled -> terminal (tidak bisa diubah)
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool True jika transisi diizinkan
     */
    public static function canTransitionStatus(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $validNext = $allowedTransitions[$currentStatus] ?? [];
        return in_array($newStatus, $validNext, true);
    }
}
