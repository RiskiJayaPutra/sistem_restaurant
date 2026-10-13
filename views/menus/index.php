<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Menu Restoran</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola sajian makanan, minuman, dan status ketersediaannya</p>
        </div>
        <div>
            <a href="/menus/create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Menu
            </a>
        </div>
    </div>

    <!-- Filter Kategori Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 pb-2">
        <a href="/menus" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= empty($category) ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Semua Menu
        </a>
        <a href="/menus?category=Makanan" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= ($category === 'Makanan') ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Makanan
        </a>
        <a href="/menus?category=Minuman" class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-colors <?= ($category === 'Minuman') ? 'bg-[#263238] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
            Minuman
        </a>
    </div>

    <!-- Tabel Menu -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">ID</th>
                        <th scope="col" class="px-6 py-3.5">Nama Menu</th>
                        <th scope="col" class="px-6 py-3.5">Kategori</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Harga Satuan</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Pernah Dipesan</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($menus)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-medium text-slate-700">Tidak ada menu yang sesuai kriteria</p>
                                <p class="text-xs text-slate-500 mt-1">Daftarkan menu makanan atau minuman baru.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($menus as $menu): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                                    #<?= (int) $menu['id'] ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <?= e($menu['name']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold <?= $menu['category'] === 'Makanan' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-cyan-50 text-cyan-800 border border-cyan-200' ?>">
                                        <?= e($menu['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-semibold text-slate-900">
                                    <?= formatRupiah($menu['price']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($menu['is_available']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Tersedia
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5"></span>
                                            Habis
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                        <?= (int) $menu['ordered_count'] ?> kali
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="/menus/<?= (int) $menu['id'] ?>/edit" class="inline-flex items-center text-xs font-semibold text-[#607D8B] hover:text-[#37474F] hover:underline">
                                        Edit
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <form action="/menus/<?= (int) $menu['id'] ?>/delete" method="POST" class="inline" data-confirm="Hapus menu '<?= e($menu['name']) ?>'?">
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
