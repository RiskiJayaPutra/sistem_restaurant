<?php
$startTs = strtotime($reservation['reserved_at']);
$endTs   = $startTs + 7200; // 2 jam

$statusBadges = [
    'pending'   => 'bg-amber-50 text-amber-800 border-amber-200',
    'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'completed' => 'bg-blue-50 text-blue-800 border-blue-200',
    'cancelled' => 'bg-red-50 text-red-800 border-red-200',
];
$badgeClass = $statusBadges[$reservation['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
?>

<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Reservasi #<?= (int) $reservation['id'] ?></h1>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                    <?= ucfirst(e($reservation['status'])) ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Dibuat pada <?= date('d M Y, H:i', strtotime($reservation['created_at'])) ?> WIB</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="/reservations" class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                &larr; Daftar Reservasi
            </a>
            <?php if ($reservation['status'] === 'pending'): ?>
                <a href="/reservations/<?= (int) $reservation['id'] ?>/edit" class="px-3.5 py-2 rounded-lg bg-amber-50 border border-amber-300 text-amber-800 text-sm font-semibold hover:bg-amber-100 transition-colors">
                    Edit Jadwal
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informasi Kartu 2 Kolom -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Tamu & Meja -->
        <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Informasi Pelanggan &amp; Meja
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Pelanggan:</span>
                    <span class="font-bold text-slate-900"><?= e($reservation['customer_name']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nomor Telepon:</span>
                    <span class="font-mono font-semibold text-slate-900"><?= e($reservation['customer_phone']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Email:</span>
                    <span class="text-slate-800"><?= !empty($reservation['customer_email']) ? e($reservation['customer_email']) : '<em class="text-slate-400">Tidak ada</em>' ?></span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-3">
                    <span class="text-slate-500">Nomor Meja:</span>
                    <span class="font-bold text-slate-900 font-mono">Meja <?= e($reservation['table_number']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kapasitas Maksimal Meja:</span>
                    <span class="font-semibold text-slate-800"><?= (int) $reservation['table_capacity'] ?> orang</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jumlah Tamu yang Hadir:</span>
                    <span class="font-bold text-[#FF5722]"><?= (int) $reservation['guest_count'] ?> orang</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Jadwal Waktu & Catatan -->
        <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                Jadwal Waktu &amp; Durasi
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal Reservasi:</span>
                    <span class="font-semibold text-slate-900"><?= date('l, d F Y', $startTs) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Mulai:</span>
                    <span class="font-mono font-bold text-slate-900"><?= date('H:i', $startTs) ?> WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Selesai (Otomatis):</span>
                    <span class="font-mono font-bold text-slate-900"><?= date('H:i', $endTs) ?> WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi Layanan:</span>
                    <span class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">2 Jam (120 Menit)</span>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <span class="text-slate-500 block text-xs mb-1">Catatan Khusus Tamu:</span>
                    <div class="p-3 bg-slate-50 rounded text-slate-700 text-xs italic">
                        <?= !empty($reservation['notes']) ? nl2br(e($reservation['notes'])) : 'Tidak ada catatan khusus.' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aksi Perubahan Status Reservasi -->
    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
            Manajemen Status Reservasi
        </h2>

        <?php if ($reservation['status'] === 'pending'): ?>
            <div class="flex flex-wrap items-center gap-3">
                <form action="/reservations/<?= (int) $reservation['id'] ?>/status" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
                        &check; Konfirmasi Reservasi
                    </button>
                </form>
                <form action="/reservations/<?= (int) $reservation['id'] ?>/status" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin membatalkan reservasi ini?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        &times; Batalkan Reservasi
                    </button>
                </form>
            </div>
        <?php elseif ($reservation['status'] === 'confirmed'): ?>
            <div class="flex flex-wrap items-center gap-3">
                <form action="/reservations/<?= (int) $reservation['id'] ?>/status" method="POST" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                        &check; Tandai Reservasi Selesai (Completed)
                    </button>
                </form>
                <form action="/reservations/<?= (int) $reservation['id'] ?>/status" method="POST" class="inline" data-confirm="Membatalkan reservasi terkonfirmasi? Pastikan pesanan makanan dibatalkan terlebih dahulu jika ada.">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        &times; Batalkan Reservasi
                    </button>
                </form>
            </div>
        <?php else: ?>
            <p class="text-sm text-slate-500 italic">
                Reservasi ini telah berstatus terminal (<strong><?= ucfirst(e($reservation['status'])) ?></strong>) dan tidak dapat diubah lagi statusnya.
            </p>
        <?php endif; ?>
    </div>

    <!-- Bagian Pesanan Makanan (Orders) Terkait -->
    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Pesanan Makanan Terkait (Maks. 1 Order)
            </h2>
            <?php if (empty($reservation['order_id']) && $reservation['status'] === 'confirmed'): ?>
                <a href="/orders/create?reservation_id=<?= (int) $reservation['id'] ?>" class="text-xs font-semibold px-3 py-1.5 rounded bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                    + Buat Pesanan Makanan
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($reservation['order_id'])): ?>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 rounded-lg bg-slate-50 border border-slate-200 gap-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-slate-900">Pesanan #<?= (int) $reservation['order_id'] ?></span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded <?= $reservation['order_status'] === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : ($reservation['order_status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                            <?= ucfirst(e($reservation['order_status'])) ?>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 font-mono">
                        Total Pesanan: <strong class="text-slate-900 text-sm"><?= formatRupiah($reservation['order_total'] ?? 0) ?></strong>
                    </p>
                </div>
                <div>
                    <a href="/orders/<?= (int) $reservation['order_id'] ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#607D8B] text-white text-xs font-semibold hover:bg-[#455A64] transition-colors">
                        Lihat Rincian Pesanan &rarr;
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-lg">
                <p class="text-sm font-medium">Belum ada pesanan makanan yang dicatat untuk reservasi ini.</p>
                <?php if ($reservation['status'] === 'pending'): ?>
                    <p class="text-xs text-slate-400 mt-1">Konfirmasi reservasi terlebih dahulu sebelum membuat pesanan makanan.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
