<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

/**
 * OrderCalculator — Kalkulator Perhitungan Nilai Pesanan Murni.
 *
 * Seluruh kalkulasi menggunakan bilangan bulat Rupiah tanpa floating point.
 * Dapat diuji secara unit testing tanpa ketergantungan database.
 *
 * @package RestoReserve\Services
 */
class OrderCalculator
{
    /** Batas maksimal kuantitas per item menu. */
    public const MAX_QUANTITY = 99;

    /**
     * Hitung subtotal untuk satu item pesanan.
     *
     * @param int $quantity  Jumlah pesanan (1-99)
     * @param int $unitPrice Harga satuan menu dalam Rupiah
     * @return int Subtotal dalam Rupiah
     * @throws InvalidArgumentException Jika kuantitas atau harga tidak valid
     */
    public static function calculateSubtotal(int $quantity, int $unitPrice): int
    {
        if ($quantity < 1 || $quantity > self::MAX_QUANTITY) {
            throw new InvalidArgumentException("Kuantitas harus antara 1 sampai " . self::MAX_QUANTITY . ".");
        }

        if ($unitPrice <= 0) {
            throw new InvalidArgumentException("Harga satuan menu harus lebih besar dari 0.");
        }

        return $quantity * $unitPrice;
    }

    /**
     * Hitung total nilai pesanan dari daftar item.
     *
     * @param array $items Array dari array item ['quantity' => int, 'unit_price' => int]
     * @return int Total dalam Rupiah
     * @throws InvalidArgumentException Jika daftar item kosong atau berisi data tidak valid
     */
    public static function calculateTotal(array $items): int
    {
        if (empty($items)) {
            throw new InvalidArgumentException("Daftar item pesanan tidak boleh kosong.");
        }

        $total = 0;
        foreach ($items as $item) {
            $qty   = (int) ($item['quantity'] ?? 0);
            $price = (int) ($item['unit_price'] ?? 0);

            $subtotal = self::calculateSubtotal($qty, $price);
            $total += $subtotal;
        }

        return $total;
    }

    /**
     * Gabungkan item pesanan duplikat (menu_item_id sama) dan jumlahkan kuantitasnya.
     *
     * @param array $rawItems Array item [{menu_item_id, quantity}]
     * @return array Array item unik [{menu_item_id, quantity}]
     */
    public static function consolidateItems(array $rawItems): array
    {
        $consolidated = [];

        foreach ($rawItems as $item) {
            $menuId = (int) ($item['menu_item_id'] ?? 0);
            $qty    = (int) ($item['quantity'] ?? 0);

            if ($menuId <= 0 || $qty <= 0) {
                continue;
            }

            if (isset($consolidated[$menuId])) {
                $consolidated[$menuId]['quantity'] += $qty;
                if ($consolidated[$menuId]['quantity'] > self::MAX_QUANTITY) {
                    $consolidated[$menuId]['quantity'] = self::MAX_QUANTITY;
                }
            } else {
                $consolidated[$menuId] = [
                    'menu_item_id' => $menuId,
                    'quantity'     => min($qty, self::MAX_QUANTITY),
                ];
            }
        }

        return array_values($consolidated);
    }
}
