<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laporan &amp; Rekapitulasi Operasional</h1>
            <p class="text-sm text-slate-500 mt-0.5">Analisis reservasi meja dan rekapitulasi nilai pesanan terkonfirmasi</p>
        </div>
        <div>
            <a
                href="/reports/export.csv?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#263238] text-white text-sm font-semibold hover:bg-[#37474F] shadow-sm transition-all active:translate-y-px"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor ke Format CSV
            </a>
        </div>
    </div>

    <!-- Filter Tanggal -->
    <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <form method="GET" action="/reports" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex-1">
                <label for="start_date" class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai Reservasi</label>
                <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Akhir Reservasi</label>
                <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
            </div>
            <div>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] transition-colors">
                    Terapkan Periode
                </button>
            </div>
        </form>
    </div>

    <!-- 4 Kartu Metrik Periode -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Reservasi</span>
            <div class="mt-2 font-mono text-3xl font-extrabold text-slate-900"><?= (int) $reportData['totalReservations'] ?></div>
            <div class="mt-2 text-xs text-slate-400">Dalam periode terpilih</div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Reservasi</span>
            <div class="mt-2 grid grid-cols-2 gap-1 text-xs">
                <div><span class="text-amber-700 font-semibold">Pending:</span> <?= (int) $reportData['statusCounts']['pending'] ?></div>
                <div><span class="text-emerald-700 font-semibold">Confirmed:</span> <?= (int) $reportData['statusCounts']['confirmed'] ?></div>
                <div><span class="text-blue-700 font-semibold">Completed:</span> <?= (int) $reportData['statusCounts']['completed'] ?></div>
                <div><span class="text-red-700 font-semibold">Cancelled:</span> <?= (int) $reportData['statusCounts']['cancelled'] ?></div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pesanan Terkonfirmasi</span>
            <div class="mt-2 font-mono text-3xl font-extrabold text-slate-900"><?= (int) $reportData['confirmedOrdersCount'] ?></div>
            <div class="mt-2 text-xs text-slate-400">Order berstatus confirmed</div>
        </div>

        <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Pesanan</span>
            <div class="mt-2 font-mono text-xl font-extrabold text-emerald-700"><?= formatRupiah($reportData['totalConfirmedRevenue']) ?></div>
            <div class="mt-2 text-xs text-slate-400">Agregasi pesanan terkonfirmasi</div>
        </div>
    </div>

    <!-- Catatan Penjelasan Keuangan Operasional -->
    <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800">
        <strong class="font-bold">Keterangan Batasan Sistem:</strong>
        Aplikasi RestoReserve ini tidak memiliki modul kasir/pembayaran (payment gateway). Angka nominal di atas adalah <em>nilai total pesanan makanan yang telah dikonfirmasi</em> untuk reservasi pada periode tersebut, bukan bukti penerimaan uang atau kas masuk restoran.
    </div>

    <!-- Tabel Rekapitulasi Reservasi -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Rekapitulasi Reservasi &amp; Nilai Pesanan
            </h2>
            <span class="text-xs text-slate-500">
                <?= count($reportData['rows']) ?> baris data
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">ID</th>
                        <th scope="col" class="px-6 py-3.5">Tanggal &amp; Jam</th>
                        <th scope="col" class="px-6 py-3.5">Pelanggan</th>
                        <th scope="col" class="px-6 py-3.5">Meja</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Tamu</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status Reservasi</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status Order</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Nilai Terkonfirmasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($reportData['rows'])): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-medium text-slate-700">Tidak ada data reservasi pada periode tanggal ini</p>
                                <p class="text-xs text-slate-500 mt-1">Ubah rentang tanggal filter untuk melihat rekapitulasi data lain.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reportData['rows'] as $row): 
                            $startTs = strtotime($row['reserved_at']);
                            $endTs   = $startTs + 7200;
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                                    #<?= (int) $row['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900"><?= date('d M Y', $startTs) ?></div>
                                    <div class="text-xs text-slate-500 font-mono"><?= date('H:i', $startTs) ?> &ndash; <?= date('H:i', $endTs) ?></div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= e($row['customer_name']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-700">
                                    Meja <?= e($row['table_number']) ?>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-slate-700">
                                    <?= (int) $row['guest_count'] ?> orang
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        <?= ucfirst(e($row['reservation_status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if (!empty($row['order_status'])): ?>
                                        <span class="text-xs font-semibold <?= $row['order_status'] === 'confirmed' ? 'text-emerald-700' : 'text-slate-600' ?>">
                                            <?= ucfirst(e($row['order_status'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 italic">Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-semibold text-slate-900">
                                    <?= formatRupiah($row['confirmed_order_total']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
