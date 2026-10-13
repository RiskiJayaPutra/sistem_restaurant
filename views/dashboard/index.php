<div class="space-y-8 animate-fade-up">

    <!-- ============================================================
         HEADER COMMAND BAR
         ============================================================ -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-slate-200">
        <div>
            <div class="flex items-center space-x-2 text-[11px] font-mono uppercase tracking-widest text-[#607D8B] font-bold mb-1">
                <span>PUSAT KENDALI OPERASIONAL</span>
                <span>•</span>
                <span class="text-emerald-600 flex items-center">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                    SISTEM SIAP
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#263238] tracking-tight">
                Ringkasan Operasional RestoReserve
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Visualisasi denah okupansi meja, radar reservasi tamu, dan rekapitulasi valuasi pesanan terkonfirmasi.
            </p>
        </div>

        <!-- Quick Command Action Bar -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="/reservations/create" class="inline-flex items-center px-4 py-2.5 rounded-sm bg-[#FF5722] text-white text-xs font-bold hover:bg-[#E64A19] shadow-construct-orange active:translate-y-px transition-all uppercase tracking-wider font-mono">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Reservasi Baru
            </a>
            <a href="/orders" class="inline-flex items-center px-4 py-2.5 rounded-sm bg-[#263238] text-white text-xs font-bold hover:bg-[#37474F] shadow-construct active:translate-y-px transition-all uppercase tracking-wider font-mono">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Kelola Pesanan
            </a>
        </div>
    </div>

    <!-- ============================================================
         ZONA 1: ASYMMETRICAL METRIC MATRIX (Constructivist Bento)
         ============================================================ -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Hero Card: Omzet / Pesanan Terkonfirmasi (Spans 2 columns on lg) -->
        <div class="lg:col-span-2 bg-[#263238] text-white rounded-sm p-6 border-l-4 border-[#FF5722] shadow-construct relative overflow-hidden bg-grid-blueprint-dark flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-mono tracking-widest text-[#B0BEC5] uppercase font-bold">
                            VALUASI PESANAN BULAN INI
                        </span>
                        <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-[#FF5722]/30 text-[#FF5722] border border-[#FF5722]/50 font-bold">
                            AUDITED
                        </span>
                    </div>
                    <span class="text-xs font-mono text-slate-400">
                        <?= date('F Y') ?>
                    </span>
                </div>
                
                <div class="mt-4">
                    <div class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">
                        <?= formatRupiah($monthlyConfirmedRevenue) ?>
                    </div>
                    <p class="text-xs text-slate-300 mt-2 flex items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400 mr-1.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Khusus pesanan terkonfirmasi (snapshot integritas harga menu).
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-700/80 flex items-center justify-between">
                <span class="text-[11px] font-mono text-slate-400">
                    Kalkulasi mutlak server-side integer (IDR)
                </span>
                <a href="/reports" class="text-xs font-mono font-bold text-[#FF5722] hover:text-[#FF8A65] flex items-center uppercase">
                    Lihat Laporan Detail &rarr;
                </a>
            </div>
        </div>

        <!-- Metric 2: Reservasi Hari Ini -->
        <div class="bg-white rounded-sm p-5 border border-slate-200 border-l-4 border-l-[#FFC107] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold tracking-widest text-slate-500 uppercase">
                        RESERVASI HARI INI
                    </span>
                    <span class="w-7 h-7 rounded bg-amber-50 text-amber-700 flex items-center justify-center font-mono font-bold text-xs">
                        TODAY
                    </span>
                </div>
                <div class="mt-4 flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-slate-900 font-mono"><?= (int) $todayReservations ?></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase font-mono">Slot Jadwal</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Status pending &amp; confirmed yang terjadwal hari ini.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Durasi: 120 Menit/Slot</span>
                <a href="/reservations" class="text-slate-600 hover:text-slate-900 font-bold">&rarr; Filter</a>
            </div>
        </div>

        <!-- Metric 3: Meja Aktif & Kapasitas -->
        <div class="bg-white rounded-sm p-5 border border-slate-200 border-l-4 border-l-[#4CAF50] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold tracking-widest text-slate-500 uppercase">
                        KAPASITAS MEJA AKTIF
                    </span>
                    <span class="w-7 h-7 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center font-mono font-bold text-xs">
                        UNIT
                    </span>
                </div>
                <div class="mt-4 flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-slate-900 font-mono"><?= (int) $activeTables ?></span>
                    <span class="text-xs font-semibold text-slate-500 uppercase font-mono">Meja Operasional</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Siap dialokasikan untuk reservasi tamu &amp; walk-in.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Database: <?= (int) $totalCustomers ?> Tamu</span>
                <a href="/tables" class="text-slate-600 hover:text-slate-900 font-bold">&rarr; Kelola</a>
            </div>
        </div>
    </div>

    <!-- ============================================================
         ZONA 2: DENAH MEJA OPERASIONAL (INTERACTIVE FLOOR RADAR)
         Visualisasi denah tata letak restoran (Bukan AI Template Generik!)
         ============================================================ -->
    <div class="bg-white rounded-sm border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-[#263238]">
                        DENAH &amp; STATUS MEJA OPERASIONAL (FLOOR RADAR)
                    </span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#607D8B]/10 text-[#607D8B] font-bold border border-[#607D8B]/20">
                        REALTIME MATRIX
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Monitoring fisik ketersediaan unit meja dan jumlah antrean reservasi hari ini.
                </p>
            </div>
            <div class="flex items-center space-x-4 text-xs font-mono">
                <span class="flex items-center text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-xs bg-emerald-500 mr-1.5"></span> Tersedia
                </span>
                <span class="flex items-center text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-xs bg-[#FF5722] mr-1.5"></span> Ada Jadwal
                </span>
                <a href="/tables" class="text-xs font-bold text-[#607D8B] hover:underline font-mono">
                    + Kelola Meja
                </a>
            </div>
        </div>

        <div class="p-5 sm:p-6 bg-[#FAFAFA]">
            <?php if (empty($allTables)): ?>
                <div class="text-center py-8 text-slate-500 text-sm">
                    Belum ada meja yang terdaftar. <a href="/tables/create" class="text-[#FF5722] font-bold hover:underline">Tambah meja pertama</a>.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    <?php foreach ($allTables as $t): 
                        $hasTodayBooking = ((int) ($t['today_bookings_count'] ?? 0)) > 0;
                        $isActive = (bool) $t['is_active'];
                    ?>
                        <div class="bg-white rounded-sm border <?= $hasTodayBooking ? 'border-[#FF5722]/50 shadow-xs' : 'border-slate-200' ?> p-4 hover:border-[#263238] transition-all relative flex flex-col justify-between group">
                            
                            <!-- Table Head -->
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-mono font-bold text-slate-400">UNIT</span>
                                <?php if ($hasTodayBooking): ?>
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-[#FF5722]/10 text-[#FF5722] border border-[#FF5722]/30">
                                        <?= (int) $t['today_bookings_count'] ?> JADWAL
                                    </span>
                                <?php elseif ($isActive): ?>
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        READY
                                    </span>
                                <?php else: ?>
                                    <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">
                                        OFF
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Table Number Graphic -->
                            <div class="my-3 text-center">
                                <div class="w-12 h-12 mx-auto rounded-sm border-2 <?= $hasTodayBooking ? 'border-[#FF5722] bg-[#FF5722]/5 text-[#FF5722]' : 'border-slate-800 bg-slate-50 text-slate-900' ?> flex items-center justify-center font-mono font-black text-lg shadow-xs group-hover:scale-105 transition-transform">
                                    <?= e($t['table_number']) ?>
                                </div>
                                <div class="mt-2 text-xs font-bold text-slate-800 font-mono">
                                    Meja <?= e($t['table_number']) ?>
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono">
                                    <?= (int) $t['capacity'] ?> Kursi Tamu
                                </div>
                            </div>

                            <!-- Table Footer Action -->
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-center">
                                <a href="/reservations/create?table_id=<?= (int) $t['id'] ?>" class="text-[10px] font-mono font-bold text-[#607D8B] group-hover:text-[#FF5722] transition-colors uppercase">
                                    + Reservasi
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================
         ZONA 3: ASYMMETRIC SPLIT (Radar Reservasi + Konsol Protokol)
         ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kolom Kiri (8/12): Radar 5 Reservasi Terdekat -->
        <div class="lg:col-span-8 space-y-3">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h2 class="text-base font-bold text-[#263238] uppercase font-mono tracking-wide">
                        RADAR RESERVASI MENDATANG
                    </h2>
                    <p class="text-xs text-slate-500">5 jadwal aktif terdekat dari seluruh alokasi meja</p>
                </div>
                <a href="/reservations" class="text-xs font-mono font-bold text-[#607D8B] hover:text-[#263238] uppercase">
                    Buka Semua Jadwal &rarr;
                </a>
            </div>

            <div class="bg-white rounded-sm border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-mono font-semibold uppercase text-[10px] tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-3.5">Waktu Reservasi</th>
                                <th scope="col" class="px-5 py-3.5">Pelanggan &amp; Kontak</th>
                                <th scope="col" class="px-5 py-3.5">Meja</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Tamu</th>
                                <th scope="col" class="px-5 py-3.5 text-center">Status</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Pesanan</th>
                                <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <?php if (empty($upcomingReservations)): ?>
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                                        <div class="w-10 h-10 rounded-sm bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 font-mono font-bold text-xs">
                                            NIL
                                        </div>
                                        <p class="font-semibold text-slate-800 text-xs">Belum ada reservasi mendatang</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Seluruh slot meja saat ini bebas jadwal.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($upcomingReservations as $res): ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <!-- Waktu -->
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="font-mono font-bold text-slate-900 text-xs">
                                                <?= date('d M Y', strtotime($res['reserved_at'])) ?>
                                            </div>
                                            <div class="text-[11px] text-[#FF5722] font-mono font-semibold">
                                                <?= date('H:i', strtotime($res['reserved_at'])) ?> WIB
                                            </div>
                                        </td>

                                        <!-- Pelanggan -->
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-900"><?= e($res['customer_name']) ?></div>
                                            <div class="text-[11px] text-slate-500 font-mono"><?= e($res['customer_phone']) ?></div>
                                        </td>

                                        <!-- Meja -->
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-xs text-[11px] font-mono font-black bg-[#263238] text-white">
                                                Meja <?= e($res['table_number']) ?>
                                            </span>
                                        </td>

                                        <!-- Tamu -->
                                        <td class="px-5 py-3.5 text-center font-mono font-semibold text-slate-700">
                                            <?= (int) $res['guest_count'] ?> Pax
                                        </td>

                                        <!-- Status Reservasi -->
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            <?php
                                            $badges = [
                                                'pending'   => 'bg-amber-50 text-amber-800 border-amber-300',
                                                'confirmed' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                                'completed' => 'bg-blue-50 text-blue-800 border-blue-300',
                                                'cancelled' => 'bg-red-50 text-red-800 border-red-300',
                                            ];
                                            $cls = $badges[$res['status']] ?? 'bg-slate-100 text-slate-700 border-slate-300';
                                            ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-xs text-[10px] font-mono font-bold border <?= $cls ?> uppercase">
                                                <?= e($res['status']) ?>
                                            </span>
                                        </td>

                                        <!-- Pesanan -->
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap font-mono">
                                            <?php if (!empty($res['order_status'])): ?>
                                                <div class="font-bold <?= $res['order_status'] === 'confirmed' ? 'text-emerald-700' : 'text-slate-800' ?>">
                                                    <?= formatRupiah($res['order_total'] ?? 0) ?>
                                                </div>
                                                <div class="text-[10px] text-slate-400 uppercase">
                                                    [<?= e($res['order_status']) ?>]
                                                </div>
                                            <?php else: ?>
                                                <span class="text-[11px] text-slate-400 italic">Belum Pesan</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                            <a href="/reservations/<?= (int) $res['id'] ?>" class="text-[11px] font-mono font-bold text-[#607D8B] hover:text-[#263238] uppercase hover:underline">
                                                Detail &rarr;
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

        <!-- Kolom Kanan (4/12): Konsol Protokol Operasional & Aturan Mesin -->
        <div class="lg:col-span-4 space-y-4">
            <div>
                <h2 class="text-base font-bold text-[#263238] uppercase font-mono tracking-wide">
                    PROTOKOL MESIN OPERASIONAL
                </h2>
                <p class="text-xs text-slate-500">Integritas data &amp; standar operasional sistem</p>
            </div>

            <!-- Rule Card 1: Slot Waktu 2 Jam -->
            <div class="bg-white rounded-sm border border-slate-200 border-l-4 border-l-[#263238] p-4 shadow-xs">
                <div class="flex items-center space-x-2 text-xs font-mono font-bold text-slate-800 uppercase">
                    <span class="w-5 h-5 rounded-xs bg-[#263238] text-white flex items-center justify-center text-[10px]">01</span>
                    <span>SLOT WAKTU TERKUNCI (120 MENIT)</span>
                </div>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Setiap reservasi memiliki jendela okupansi tepat 2 jam. Sistem otomatis menolak reservasi yang bertabrakan waktu pada meja yang sama.
                </p>
                <div class="mt-2 text-[10px] font-mono text-emerald-700 font-semibold">
                    ✓ Validasi No-Overlap Terverifikasi
                </div>
            </div>

            <!-- Rule Card 2: Concurrency Locking -->
            <div class="bg-white rounded-sm border border-slate-200 border-l-4 border-l-[#FF5722] p-4 shadow-xs">
                <div class="flex items-center space-x-2 text-xs font-mono font-bold text-slate-800 uppercase">
                    <span class="w-5 h-5 rounded-xs bg-[#FF5722] text-white flex items-center justify-center text-[10px]">02</span>
                    <span>PENCEGAHAN DOUBLE-BOOKING</span>
                </div>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Menerapkan transaksi atomik dengan perintah <code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded text-[#263238]">SELECT ... FOR UPDATE</code> pada mesin InnoDB MySQL untuk proteksi race condition.
                </p>
                <div class="mt-2 text-[10px] font-mono text-emerald-700 font-semibold">
                    ✓ Row-Level Lock InnoDB Aktif
                </div>
            </div>

            <!-- Rule Card 3: Snapshot Harga Finansial -->
            <div class="bg-white rounded-sm border border-slate-200 border-l-4 border-l-[#607D8B] p-4 shadow-xs">
                <div class="flex items-center space-x-2 text-xs font-mono font-bold text-slate-800 uppercase">
                    <span class="w-5 h-5 rounded-xs bg-[#607D8B] text-white flex items-center justify-center text-[10px]">03</span>
                    <span>SNAPSHOT HARGA HISTORIS</span>
                </div>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Harga item menu disalin permanen ke <code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded text-[#263238]">order_items.unit_price</code>. Nilai pesanan masa lalu kebal terhadap fluktuasi harga menu master.
                </p>
                <div class="mt-2 text-[10px] font-mono text-emerald-700 font-semibold">
                    ✓ Integritas Audit Finansial
                </div>
            </div>

            <!-- Shortcut Aksi Tambahan -->
            <div class="bg-[#1e272c] text-white rounded-sm p-4 border border-[#37474F] text-xs font-mono">
                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-2">
                    PINTASAN MASTER DATA
                </div>
                <div class="space-y-2">
                    <a href="/customers/create" class="flex items-center justify-between text-slate-300 hover:text-white py-1 border-b border-slate-700">
                        <span>+ Tambah Tamu / Pelanggan</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="/tables/create" class="flex items-center justify-between text-slate-300 hover:text-white py-1 border-b border-slate-700">
                        <span>+ Tambah Nomor Meja</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="/menus/create" class="flex items-center justify-between text-slate-300 hover:text-white py-1">
                        <span>+ Tambah Menu Baru</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
