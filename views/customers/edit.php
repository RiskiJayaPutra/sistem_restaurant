<?php $errors = get_errors(); ?>

<div class="max-w-2xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Data Pelanggan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Perbarui data informasi kontak pelanggan</p>
        </div>
        <a href="/customers" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm">
        <form action="/customers/<?= (int) $customer['id'] ?>/update" method="POST" class="space-y-5" data-loading>
            <?= csrf_field() ?>

            <!-- Nama -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= e(old('name', $customer['name'])) ?>"
                    required
                    minlength="3"
                    maxlength="100"
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['name']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['name'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Telepon -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor Telepon / WhatsApp <span class="text-red-500">*</span>
                </label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?= e(old('phone', $customer['phone'])) ?>"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['phone']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['phone'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['phone']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alamat Email <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e(old('email', $customer['email'] ?? '')) ?>"
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['email']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                <?php if (!empty($errors['email'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/customers" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
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
