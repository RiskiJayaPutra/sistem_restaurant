<?php $errors = get_errors(); ?>

<div class="max-w-2xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Data Meja</h1>
            <p class="text-sm text-slate-500 mt-0.5">Ubah informasi nomor meja atau status operasional</p>
        </div>
        <a href="/tables" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm">
        <form action="/tables/<?= (int) $table['id'] ?>/update" method="POST" class="space-y-5" data-loading>
            <?= csrf_field() ?>

            <!-- Nomor Meja -->
            <div>
                <label for="table_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor / Kode Meja <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="table_number"
                    name="table_number"
                    value="<?= e(old('table_number', $table['table_number'])) ?>"
                    required
                    maxlength="20"
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['table_number']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm uppercase font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['table_number'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['table_number']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Kapasitas -->
            <div>
                <label for="capacity" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kapasitas Kursi (Orang) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    value="<?= e(old('capacity', (string) $table['capacity'])) ?>"
                    required
                    min="1"
                    max="20"
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['capacity']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['capacity'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['capacity']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Status Aktif -->
            <div class="pt-2">
                <label class="relative flex items-start cursor-pointer">
                    <div class="flex items-center h-5">
                        <input
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            <?= (old('is_active', (string) $table['is_active']) === '1') ? 'checked' : '' ?>
                            class="w-4 h-4 rounded text-[#607D8B] border-slate-300 focus:ring-[#607D8B]"
                        >
                    </div>
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-slate-700">Meja Aktif & Tersedia untuk Reservasi</span>
                        <p class="text-xs text-slate-500">Nonaktifkan meja jika sedang dalam perbaikan atau tidak dapat dipakai.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/tables" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
