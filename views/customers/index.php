<div class="space-y-6 animate-fade-up">
    <!-- Header Page & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pelanggan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola data tamu restoran untuk keperluan reservasi meja</p>
        </div>
        <div>
            <a href="/customers/create" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pelanggan
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <form method="GET" action="/customers" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input
                    type="text"
                    name="search"
                    value="<?= e($search ?? '') ?>"
                    placeholder="Cari berdasarkan nama atau nomor telepon..."
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-[#263238] text-white text-sm font-medium hover:bg-[#37474F] transition-colors">
                    Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="/customers" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">ID</th>
                        <th scope="col" class="px-6 py-3.5">Nama Pelanggan</th>
                        <th scope="col" class="px-6 py-3.5">Nomor Telepon</th>
                        <th scope="col" class="px-6 py-3.5">Email</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Reservasi</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-slate-700">Belum ada data pelanggan</p>
                                <p class="text-xs text-slate-500 mt-1">Data pelanggan yang terdaftar akan ditampilkan di sini.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                                    #<?= (int) $customer['id'] ?>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <?= e($customer['name']) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                    <?= e($customer['phone']) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= !empty($customer['email']) ? e($customer['email']) : '<span class="text-slate-400 text-xs italic">-</span>' ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($customer['reservation_count'] > 0) ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= (int) $customer['reservation_count'] ?> kali
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="/customers/<?= (int) $customer['id'] ?>/edit" class="inline-flex items-center text-xs font-semibold text-[#607D8B] hover:text-[#37474F] hover:underline">
                                        Edit
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <form action="/customers/<?= (int) $customer['id'] ?>/delete" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus pelanggan '<?= e($customer['name']) ?>'?">
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
