<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Meja Restoran</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola tata letak nomor meja dan batas kapasitas kursi tamu</p>
        </div>
        <div>
            <a href="/tables/create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Meja
            </a>
        </div>
    </div>

    <!-- Grid / Tabel Meja -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Nomor Meja</th>
                        <th scope="col" class="px-6 py-3.5">Kapasitas Tamu</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Riwayat Reservasi</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($tables)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-medium text-slate-700">Belum ada meja yang didaftarkan</p>
                                <p class="text-xs text-slate-500 mt-1">Tambahkan meja baru untuk mulai menerima reservasi tamu.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tables as $table): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-300 flex items-center justify-center font-bold text-slate-700 text-sm font-mono">
                                            <?= e($table['table_number']) ?>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900">Meja <?= e($table['table_number']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    <span class="font-semibold text-slate-900"><?= (int) $table['capacity'] ?></span> orang
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($table['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        <?= (int) $table['reservation_count'] ?> reservasi
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="/tables/<?= (int) $table['id'] ?>/edit" class="inline-flex items-center text-xs font-semibold text-[#607D8B] hover:text-[#37474F] hover:underline">
                                        Edit
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <form action="/tables/<?= (int) $table['id'] ?>/delete" method="POST" class="inline" data-confirm="Hapus meja <?= e($table['table_number']) ?>?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
