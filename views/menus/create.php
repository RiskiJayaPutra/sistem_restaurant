<?php $errors = get_errors(); ?>

<div class="max-w-2xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Menu Baru</h1>
            <p class="text-sm text-slate-500 mt-0.5">Daftarkan sajian makanan atau minuman baru ke daftar menu restoran</p>
        </div>
        <a href="/menus" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm">
        <form action="/menus" method="POST" class="space-y-5" data-loading>
            <?= csrf_field() ?>

            <!-- Nama Menu -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Menu <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= e(old('name')) ?>"
                    required
                    minlength="3"
                    maxlength="120"
                    placeholder="Contoh: Nasi Goreng Seafood"
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['name']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['name'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Kategori -->
            <div>
                <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select
                    id="category"
                    name="category"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['category']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Makanan" <?= old('category') === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                    <option value="Minuman" <?= old('category') === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                </select>
                <?php if (!empty($errors['category'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['category']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Harga (Rupiah Integer) -->
            <div>
                <label for="price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Harga Satuan (Rupiah) <span class="text-red-500">*</span>
                </label>
                <div class="relative rounded-lg">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm font-semibold">
                        Rp
                    </div>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="<?= e(old('price')) ?>"
                        required
                        min="1000"
                        step="500"
                        placeholder="25000"
                        class="w-full pl-11 pr-3.5 py-2.5 rounded-lg border <?= !empty($errors['price']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                    >
                </div>
                <?php if (!empty($errors['price'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['price']) ?></p>
                <?php else: ?>
                    <p class="text-xs text-slate-400 mt-1">Gunakan angka bulat tanpa titik atau koma.</p>
                <?php endif; ?>
            </div>

            <!-- Status Ketersediaan -->
            <div class="pt-2">
                <label class="relative flex items-start cursor-pointer">
                    <div class="flex items-center h-5">
                        <input
                            type="checkbox"
                            id="is_available"
                            name="is_available"
                            value="1"
                            <?= old('is_available', '1') === '1' ? 'checked' : '' ?>
                            class="w-4 h-4 rounded text-[#607D8B] border-slate-300 focus:ring-[#607D8B]"
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-slate-700">Tersedia untuk Dipesan</span>
                        <p class="text-xs text-slate-500">Jika tidak dicentang, menu berstatus habis dan tidak dapat dipilih pada pemesanan baru.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/menus" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px"
                >
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>
</div>
