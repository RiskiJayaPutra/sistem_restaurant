<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Reservasi Meja</h1>
            <p class="text-sm text-slate-500 mt-0.5">Jadwal pemesanan meja dengan durasi tetap 2 jam (120 menit)</p>
        </div>
        <div>
            <a href="/reservations/create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#FF5722] text-white text-sm font-semibold hover:bg-[#E64A19] shadow-sm transition-all active:translate-y-px">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Reservasi Baru
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <form method="GET" action="/reservations" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-xs font-semibold text-slate-600 mb-1">Mulai Tanggal</label>
                <input type="date" id="from_date" name="from_date" value="<?= e($filters['from_date'] ?? '') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
            </div>
            <div>
                <label for="to_date" class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" id="to_date" name="to_date" value="<?= e($filters['to_date'] ?? '') ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
            </div>
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari Tamu / Meja</label>
                <input type="text" id="search" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Nama, telepon, atau meja..." class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-[#263238] text-white text-sm font-medium hover:bg-[#37474F] transition-colors">
                    Filter
                </button>
                <a href="/reservations" class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Reservasi -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">ID</th>
                        <th scope="col" class="px-6 py-3.5">Jadwal (Mulai &ndash; Selesai)</th>
                        <th scope="col" class="px-6 py-3.5">Nama Pelanggan</th>
                        <th scope="col" class="px-6 py-3.5">Meja</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Tamu</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status Reservasi</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Pesanan Makanan</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-medium text-slate-700">Tidak ada reservasi yang sesuai filter</p>
                                <p class="text-xs text-slate-500 mt-1">Gunakan tombol 'Buat Reservasi Baru' untuk menambahkan jadwal.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): 
                            $startTs = strtotime($res['reserved_at']);
                            $endTs   = $startTs + 7200; // 2 jam
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                                    #<?= (int) $res['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900">
                                        <?= date('d M Y', $startTs) ?>
                                    </div>
                                    <div class="text-xs text-slate-500 font-mono">
                                        <?= date('H:i', $startTs) ?> &ndash; <?= date('H:i', $endTs) ?> WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900"><?= e($res['customer_name']) ?></div>
                                    <div class="text-xs text-slate-500 font-mono"><?= e($res['customer_phone']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-800">
                                        Meja <?= e($res['table_number']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-slate-700">
                                    <?= (int) $res['guest_count'] ?> orang
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $statusBadges = [
                                        'pending'   => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'completed' => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'cancelled' => 'bg-red-50 text-red-800 border-red-200',
                                    ];
                                    $badgeClass = $statusBadges[$res['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                        <?= ucfirst(e($res['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if (!empty($res['order_id'])): ?>
                                        <a href="/orders/<?= (int) $res['order_id'] ?>" class="text-xs font-semibold text-[#607D8B] hover:underline">
                                            #<?= (int) $res['order_id'] ?> (<?= ucfirst(e($res['order_status'])) ?>: <?= formatRupiah($res['order_total'] ?? 0) ?>)
                                        </a>
                                    <?php else: ?>
                                        <?php if ($res['status'] === 'confirmed'): ?>
                                            <a href="/orders/create?reservation_id=<?= (int) $res['id'] ?>" class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800 hover:underline">
                                                + Buat Pesanan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Belum Ada</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                    <a href="/reservations/<?= (int) $res['id'] ?>" class="text-xs font-semibold text-[#607D8B] hover:text-[#37474F] hover:underline">
                                        Detail
                                    </a>
                                    <?php if ($res['status'] === 'pending'): ?>
                                        <span class="text-slate-300">|</span>
                                        <a href="/reservations/<?= (int) $res['id'] ?>/edit" class="text-xs font-semibold text-amber-600 hover:text-amber-800 hover:underline">
                                            Edit
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
