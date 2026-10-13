<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\BookingRules;

/**
 * BookingRulesTest — Pengujian Otomatis Aturan Reservasi (UT-01 s/d UT-05, UT-09, UT-10).
 *
 * Menguji kalkulasi interval waktu, overlap bentrokan jadwal meja,
 * validasi kapasitas kursi tamu, dan aturan transisi status.
 *
 * @package Tests\Unit
 */
class BookingRulesTest extends TestCase
{
    /**
     * UT-01: Interval 18.00–20.00 vs 19.00–21.00 saling bentrok (overlap).
     */
    public function testUt01OverlappingIntervalsAreDetectedAsConflict(): void
    {
        $startA = '2026-10-15 18:00:00';
        $endA   = '2026-10-15 20:00:00';

        $startB = '2026-10-15 19:00:00';
        $endB   = '2026-10-15 21:00:00';

        $isConflict = BookingRules::isOverlapping($startA, $endA, $startB, $endB);

        $this->assertTrue($isConflict, 'Interval 18.00-20.00 dan 19.00-21.00 harus terdeteksi bentrok.');
    }

    /**
     * UT-02: Interval 18.00–20.00 vs 20.00–22.00 bersebelahan (tidak bentrok).
     */
    public function testUt02AdjacentFutureIntervalDoesNotConflict(): void
    {
        $startA = '2026-10-15 18:00:00';
        $endA   = '2026-10-15 20:00:00';

        $startB = '2026-10-15 20:00:00';
        $endB   = '2026-10-15 22:00:00';

        $isConflict = BookingRules::isOverlapping($startA, $endA, $startB, $endB);

        $this->assertFalse($isConflict, 'Interval 18.00-20.00 dan 20.00-22.00 yang bersebelahan tidak boleh bentrok.');
    }

    /**
     * UT-03: Interval 18.00–20.00 vs 16.00–18.00 bersebelahan sebelumnya (tidak bentrok).
     */
    public function testUt03AdjacentPastIntervalDoesNotConflict(): void
    {
        $startA = '2026-10-15 18:00:00';
        $endA   = '2026-10-15 20:00:00';

        $startB = '2026-10-15 16:00:00';
        $endB   = '2026-10-15 18:00:00';

        $isConflict = BookingRules::isOverlapping($startA, $endA, $startB, $endB);

        $this->assertFalse($isConflict, 'Interval 18.00-20.00 dan 16.00-18.00 yang bersebelahan tidak boleh bentrok.');
    }

    /**
     * UT-04: Jumlah tamu 5 pada meja berkapasitas 4 harus ditolak.
     */
    public function testUt04GuestCountExceedingTableCapacityIsRejected(): void
    {
        $guestCount = 5;
        $capacity   = 4;

        $isValid = BookingRules::validateCapacity($guestCount, $capacity);

        $this->assertFalse($isValid, 'Tamu 5 orang pada meja berkapasitas 4 orang harus ditolak.');
    }

    /**
     * UT-05: Jumlah tamu 4 pada meja berkapasitas 4 harus diterima.
     */
    public function testUt05GuestCountWithinTableCapacityIsAccepted(): void
    {
        $guestCount = 4;
        $capacity   = 4;

        $isValid = BookingRules::validateCapacity($guestCount, $capacity);

        $this->assertTrue($isValid, 'Tamu 4 orang pada meja berkapasitas 4 orang harus diterima.');
    }

    /**
     * UT-09: Transisi status dari 'pending' ke 'confirmed' diizinkan.
     */
    public function testUt09TransitionFromPendingToConfirmedIsAllowed(): void
    {
        $canTransition = BookingRules::canTransitionStatus('pending', 'confirmed');

        $this->assertTrue($canTransition, "Transisi dari 'pending' ke 'confirmed' harus diizinkan.");
    }

    /**
     * UT-10: Transisi status dari terminal 'cancelled' ke 'confirmed' ditolak.
     */
    public function testUt10TransitionFromCancelledToConfirmedIsRejected(): void
    {
        $canTransition = BookingRules::canTransitionStatus('cancelled', 'confirmed');

        $this->assertFalse($canTransition, "Transisi dari status terminal 'cancelled' ke 'confirmed' harus ditolak.");
    }

    /**
     * Test tambahan durasi 2 jam (120 menit).
     */
    public function testCalculateEndTimeAddsExactlyTwoHours(): void
    {
        $start = '2026-10-15 14:30:00';
        $expectedEnd = '2026-10-15 16:30:00';

        $end = BookingRules::calculateEndTime($start);

        $this->assertSame($expectedEnd, $end);
    }
}
