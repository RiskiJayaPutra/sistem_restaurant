<?php $errors = get_errors(); ?>

<div class="max-w-3xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Jadwal Reservasi #<?= (int) $reservation['id'] ?></h1>
            <p class="text-sm text-slate-500 mt-0.5">Tamu: <strong><?= e($reservation['customer_name']) ?></strong> (Hanya diizinkan saat status Pending)</p>
        </div>
        <a href="/reservations/<?= (int) $reservation['id'] ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Detail
        </a>
    </div>

    <!-- Alert Bentrokan / Konflik -->
    <?php if (!empty($errors['conflict'])): ?>
        <div class="p-4 rounded-lg bg-red-50 border border-red-300 text-red-800 text-sm flex items-start space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong class="font-bold block">Peringatan Bentrokan Jadwal / Kapasitas:</strong>
                <span><?= e($errors['conflict']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Edit -->
    <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm">
        <form action="/reservations/<?= (int) $reservation['id'] ?>/update" method="POST" class="space-y-5" data-loading>
            <?= csrf_field() ?>

            <!-- Info Tamu (Readonly) -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                    Nama Pelanggan (Tidak Dapat Diubah)
                </label>
                <input
                    type="text"
                    disabled
                    value="<?= e($reservation['customer_name']) ?> (<?= e($reservation['customer_phone']) ?>)"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 text-sm cursor-not-allowed"
                >
            </div>

            <!-- Meja -->
            <div>
                <label for="table_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pilih Meja Restoran <span class="text-red-500">*</span>
                </label>
                <select
                    id="table_id"
                    name="table_id"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['table_id']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                    <?php foreach ($activeTables as $tbl): ?>
                        <option value="<?= (int) $tbl['id'] ?>" <?= (old('table_id', (string) $reservation['table_id']) == (string) $tbl['id']) ? 'selected' : '' ?>>
                            Meja <?= e($tbl['table_number']) ?> &mdash; Kapasitas Maksimal <?= (int) $tbl['capacity'] ?> Orang
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['table_id'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['table_id']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Jadwal & Tamu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="reserved_at" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai Reservasi <span class="text-red-500">*</span>
                    </label>
                    <?php 
                    $currentFormatted = date('Y-m-d\TH:i', strtotime($reservation['reserved_at']));
                    ?>
                    <input
                        type="datetime-local"
                        id="reserved_at"
                        name="reserved_at"
                        value="<?= e(old('reserved_at', $currentFormatted)) ?>"
                        required
                        class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['reserved_at']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                    >
                    <?php if (!empty($errors['reserved_at'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e($errors['reserved_at']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="guest_count" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Tamu (Orang) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="guest_count"
                        name="guest_count"
                        value="<?= e(old('guest_count', (string) $reservation['guest_count'])) ?>"
                        required
                        min="1"
                        max="20"
                        class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['guest_count']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                    >
                    <?php if (!empty($errors['guest_count'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e($errors['guest_count']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Khusus <span class="text-slate-400 lowercase font-normal">(opsional)</span>
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    maxlength="255"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                ><?= e(old('notes', $reservation['notes'] ?? '')) ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/reservations/<?= (int) $reservation['id'] ?>" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
