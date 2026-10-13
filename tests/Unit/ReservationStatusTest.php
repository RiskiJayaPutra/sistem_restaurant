<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\BookingRules;

/**
 * ReservationStatusTest — Pengujian Komprehensif Matriks Transisi Status Reservasi.
 *
 * Menguji seluruh kombinasi perpindahan status untuk menjamin integritas siklus reservasi.
 *
 * @package Tests\Unit
 */
class ReservationStatusTest extends TestCase
{
    /**
     * Pending status transitions:
     * - pending -> confirmed (valid)
     * - pending -> cancelled (valid)
     * - pending -> completed (invalid, must be confirmed first)
     */
    public function testPendingStatusTransitions(): void
    {
        $this->assertTrue(BookingRules::canTransitionStatus('pending', 'confirmed'));
        $this->assertTrue(BookingRules::canTransitionStatus('pending', 'cancelled'));
        $this->assertFalse(BookingRules::canTransitionStatus('pending', 'completed'));
    }

    /**
     * Confirmed status transitions:
     * - confirmed -> completed (valid)
     * - confirmed -> cancelled (valid)
     * - confirmed -> pending (invalid, cannot revert to pending)
     */
    public function testConfirmedStatusTransitions(): void
    {
        $this->assertTrue(BookingRules::canTransitionStatus('confirmed', 'completed'));
        $this->assertTrue(BookingRules::canTransitionStatus('confirmed', 'cancelled'));
        $this->assertFalse(BookingRules::canTransitionStatus('confirmed', 'pending'));
    }

    /**
     * Completed status is terminal:
     * - completed -> pending (invalid)
     * - completed -> confirmed (invalid)
     * - completed -> cancelled (invalid)
     */
    public function testCompletedStatusIsTerminal(): void
    {
        $this->assertFalse(BookingRules::canTransitionStatus('completed', 'pending'));
        $this->assertFalse(BookingRules::canTransitionStatus('completed', 'confirmed'));
        $this->assertFalse(BookingRules::canTransitionStatus('completed', 'cancelled'));
    }

    /**
     * Cancelled status is terminal:
     * - cancelled -> pending (invalid)
     * - cancelled -> confirmed (invalid)
     * - cancelled -> completed (invalid)
     */
    public function testCancelledStatusIsTerminal(): void
    {
        $this->assertFalse(BookingRules::canTransitionStatus('cancelled', 'pending'));
        $this->assertFalse(BookingRules::canTransitionStatus('cancelled', 'confirmed'));
        $this->assertFalse(BookingRules::canTransitionStatus('cancelled', 'completed'));
    }

    /**
     * Same status to same status is identity/idempotent.
     */
    public function testSameStatusIsAllowed(): void
    {
        $this->assertTrue(BookingRules::canTransitionStatus('pending', 'pending'));
        $this->assertTrue(BookingRules::canTransitionStatus('confirmed', 'confirmed'));
        $this->assertTrue(BookingRules::canTransitionStatus('completed', 'completed'));
        $this->assertTrue(BookingRules::canTransitionStatus('cancelled', 'cancelled'));
    }
}
