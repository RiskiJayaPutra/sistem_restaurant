<?php
$orderBadges = [
    'draft'     => 'bg-amber-50 text-amber-800 border-amber-200',
    'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'cancelled' => 'bg-red-50 text-red-800 border-red-200',
];
$badge = $orderBadges[$order['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
?>

<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rincian Pesanan #<?= (int) $order['id'] ?></h1>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold border <?= $badge ?>">
                    <?= ucfirst(e($order['status'])) ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Terkait dengan <a href="/reservations/<?= (int) $order['reservation_id'] ?>" class="text-[#607D8B] font-semibold hover:underline">Reservasi #<?= (int) $order['reservation_id'] ?></a> &bull; Tamu: <strong><?= e($order['customer_name']) ?></strong> (Meja <?= e($order['table_number']) ?>)
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/orders" class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                &larr; Daftar Pesanan
            </a>
            <?php if ($order['status'] === 'draft'): ?>
                <a href="/orders/<?= (int) $order['id'] ?>/edit" class="px-3.5 py-2 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 text-sm font-semibold hover:bg-amber-100 transition-colors">
                    Edit Menu Draft
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Rincian Menu Pesanan -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Daftar Menu yang Dipesan
            </h2>
            <span class="text-xs text-slate-500">
                Snapshot harga saat pesanan disimpan
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">No</th>
                        <th scope="col" class="px-6 py-3.5">Nama Menu</th>
                        <th scope="col" class="px-6 py-3.5">Kategori</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Jumlah</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Harga Satuan (Snapshot)</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($order['items'])): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Tidak ada item dalam pesanan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($order['items'] as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                    <?= $no++ ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= e($item['menu_name']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold <?= $item['menu_category'] === 'Makanan' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-cyan-50 text-cyan-800 border border-cyan-200' ?>">
                                        <?= e($item['menu_category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-semibold text-slate-900">
                                    <?= (int) $item['quantity'] ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-slate-600">
                                    <?= formatRupiah($item['unit_price']) ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-slate-900">
                                    <?= formatRupiah($item['subtotal']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right font-bold text-slate-700 uppercase tracking-wider text-xs">
                            Total Nilai Pesanan:
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-extrabold text-xl text-slate-900">
                            <?= formatRupiah($order['total']) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Manajemen Status Pesanan -->
    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
            Aksi Status Pesanan
        </h2>

        <?php if ($order['status'] === 'draft'): ?>
            <div class="flex flex-wrap items-center gap-3">
                <form action="/orders/<?= (int) $order['id'] ?>/confirm" method="POST" class="inline" data-confirm="Konfirmasi pesanan ini? Total nilai akan dimasukkan ke dalam agregat laporan.">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                        &check; Konfirmasi Pesanan
                    </button>
                </form>
                <form action="/orders/<?= (int) $order['id'] ?>/cancel" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin membatalkan pesanan draft ini?">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        &times; Batalkan Pesanan
                    </button>
                </form>
            </div>
        <?php elseif ($order['status'] === 'confirmed'): ?>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-xs text-slate-500">
                    Dikonfirmasi pada: <strong class="text-slate-700"><?= !empty($order['confirmed_at']) ? date('d M Y, H:i', strtotime($order['confirmed_at'])) : '-' ?> WIB</strong>
                </div>
                <form action="/orders/<?= (int) $order['id'] ?>/cancel" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin membatalkan pesanan yang telah terkonfirmasi ini?">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        &times; Batalkan Pesanan
                    </button>
                </form>
            </div>
        <?php else: ?>
            <p class="text-sm text-slate-500 italic">
                Pesanan ini telah dibatalkan (<strong>Cancelled</strong>) dan tidak dapat diubah lagi.
            </p>
        <?php endif; ?>
    </div>
</div>
