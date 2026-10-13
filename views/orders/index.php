<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Pesanan Makanan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau pesanan yang terhubung langsung dengan jadwal reservasi terkonfirmasi</p>
        </div>
        <div>
            <a href="/reservations?status=confirmed" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Pilih Reservasi Terkonfirmasi
            </a>
        </div>
    </div>

    <!-- Filter Status -->
    <div class="flex items-center space-x-2 border-b border-slate-200 pb-2">
        <a href="/orders" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= empty($status) ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Semua Pesanan
        </a>
        <a href="/orders?status=draft" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= ($status === 'draft') ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Draft
        </a>
        <a href="/orders?status=confirmed" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= ($status === 'confirmed') ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Confirmed
        </a>
        <a href="/orders?status=cancelled" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= ($status === 'cancelled') ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Cancelled
        </a>
    </div>

    <!-- Tabel Orders -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">ID Order</th>
                        <th scope="col" class="px-6 py-3.5">Reservasi / Tamu</th>
                        <th scope="col" class="px-6 py-3.5">Meja</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Jumlah Item</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Total Nilai</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-medium text-slate-700">Tidak ada pesanan makanan yang terdaftar</p>
                                <p class="text-xs text-slate-500 mt-1">Buat pesanan baru dari halaman detail reservasi yang telah dikonfirmasi.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                    #<?= (int) $order['id'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900"><?= e($order['customer_name']) ?></div>
                                    <div class="text-xs text-slate-500">
                                        Reservasi #<?= (int) $order['reservation_id'] ?> (<?= date('d M Y, H:i', strtotime($order['reserved_at'])) ?>)
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-800">
                                        Meja <?= e($order['table_number']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                        <?= (int) $order['item_count'] ?> menu
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-slate-900">
                                    <?= formatRupiah($order['total']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $orderBadges = [
                                        'draft'     => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'cancelled' => 'bg-red-50 text-red-800 border-red-200',
                                    ];
                                    $badge = $orderBadges[$order['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badge ?>">
                                        <?= ucfirst(e($order['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="/orders/<?= (int) $order['id'] ?>" class="text-xs font-semibold text-[#607D8B] hover:text-[#37474F] hover:underline">
                                        Rincian &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
