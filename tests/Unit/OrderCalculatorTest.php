<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\OrderCalculator;
use InvalidArgumentException;

/**
 * OrderCalculatorTest — Pengujian Otomatis Perhitungan Pesanan (UT-06, UT-07, UT-08, UT-11, UT-12).
 *
 * Menguji akurasi kalkulasi aritmatika integer Rupiah dan penanganan exception input invalid.
 *
 * @package Tests\Unit
 */
class OrderCalculatorTest extends TestCase
{
    /**
     * UT-06: 2 × 25.000 menghasilkan subtotal 50.000.
     */
    public function testUt06SubtotalCalculation(): void
    {
        $quantity  = 2;
        $unitPrice = 25000;

        $subtotal = OrderCalculator::calculateSubtotal($quantity, $unitPrice);

        $this->assertSame(50000, $subtotal, 'Subtotal 2 x 25.000 harus bernilai 50.000.');
    }

    /**
     * UT-07: Dua item: (2 × 25.000) + (2 × 5.000) menghasilkan total 60.000.
     */
    public function testUt07GrandTotalCalculation(): void
    {
        $items = [
            ['quantity' => 2, 'unit_price' => 25000], // 50.000 (Nasi Goreng)
            ['quantity' => 2, 'unit_price' => 5000],  // 10.000 (Es Teh)
        ];

        $total = OrderCalculator::calculateTotal($items);

        $this->assertSame(60000, $total, 'Total pesanan 2x25.000 + 2x5.000 harus bernilai 60.000.');
    }

    /**
     * UT-08: Kuantitas 0 ditolak oleh validator/kalkulator.
     */
    public function testUt08ZeroQuantityIsRejectedWithException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Kuantitas harus antara 1 sampai 99.');

        OrderCalculator::calculateSubtotal(0, 25000);
    }

    /**
     * UT-11: Menu/harga negatif atau nol ditolak.
     */
    public function testUt11NegativeOrZeroUnitPriceIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Harga satuan menu harus lebih besar dari 0.');

        OrderCalculator::calculateSubtotal(2, -5000);
    }

    /**
     * UT-12: Daftar item pesanan kosong ketika kalkulasi/konfirmasi ditolak.
     */
    public function testUt12EmptyItemsListIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Daftar item pesanan tidak boleh kosong.');

        OrderCalculator::calculateTotal([]);
    }

    /**
     * Pengujian konsolidasi menu duplikat: menu sama digabungkan kuantitasnya.
     */
    public function testConsolidateDuplicateMenuItems(): void
    {
        $rawItems = [
            ['menu_item_id' => 1, 'quantity' => 2],
            ['menu_item_id' => 2, 'quantity' => 1],
            ['menu_item_id' => 1, 'quantity' => 3], // duplikat menu ID 1
        ];

        $consolidated = OrderCalculator::consolidateItems($rawItems);

        $this->assertCount(2, $consolidated, 'Menu dengan ID sama harus digabungkan menjadi 1 baris.');
        $this->assertSame(1, $consolidated[0]['menu_item_id']);
        $this->assertSame(5, $consolidated[0]['quantity'], 'Kuantitas menu ID 1 harus menjadi 2 + 3 = 5.');
        $this->assertSame(2, $consolidated[1]['menu_item_id']);
        $this->assertSame(1, $consolidated[1]['quantity']);
    }
}
