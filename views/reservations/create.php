<?php $errors = get_errors(); ?>

<div class="max-w-3xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Reservasi Meja Baru</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tentukan pelanggan, meja, jadwal mulai, dan jumlah tamu</p>
        </div>
        <a href="/reservations" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- Alert Bentrokan / Konflik Jadwal -->
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

    <!-- Info Banner Aturan Bisnis -->
    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-xs text-blue-800 flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 1118 0z"/>
        </svg>
        <div>
            <span class="font-bold block mb-0.5">Ketentuan Durasi &amp; Kapasitas Meja:</span>
            <span>Durasi setiap reservasi tepat <strong>2 jam (120 menit)</strong>. Sistem secara otomatis memblokir bentrokan jam pada meja yang sama dan menolak pemesanan bila jumlah tamu melebihi kapasitas kursi meja.</span>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm">
        <form action="/reservations" method="POST" class="space-y-5" data-loading>
            <?= csrf_field() ?>

            <!-- Pelanggan -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Pilih Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <a href="/customers/create" class="text-xs text-[#607D8B] font-semibold hover:underline">
                        + Tambah Pelanggan Baru
                    </a>
                </div>
                <select
                    id="customer_id"
                    name="customer_id"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['customer_id']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                >
                    <option value="">-- Pilih Tamu Pelanggan --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (old('customer_id') == (string) $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['name']) ?> (<?= e($c['phone']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['customer_id'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['customer_id']) ?></p>
                <?php endif; ?>
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
                    <option value="">-- Pilih Meja Aktif --</option>
                    <?php foreach ($activeTables as $tbl): ?>
                        <option value="<?= (int) $tbl['id'] ?>" <?= (old('table_id') == (string) $tbl['id']) ? 'selected' : '' ?>>
                            Meja <?= e($tbl['table_number']) ?> &mdash; Kapasitas Maksimal <?= (int) $tbl['capacity'] ?> Orang
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['table_id'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e($errors['table_id']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Jadwal Waktu Mulai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="reserved_at" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai Reservasi <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        id="reserved_at"
                        name="reserved_at"
                        value="<?= e(old('reserved_at')) ?>"
                        required
                        class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['reserved_at']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                    >
                    <?php if (!empty($errors['reserved_at'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e($errors['reserved_at']) ?></p>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 mt-1">Harus di masa depan (Asia/Jakarta).</p>
                    <?php endif; ?>
                </div>

                <!-- Jumlah Tamu -->
                <div>
                    <label for="guest_count" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Tamu (Orang) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="guest_count"
                        name="guest_count"
                        value="<?= e(old('guest_count', '2')) ?>"
                        required
                        min="1"
                        max="20"
                        class="w-full px-3.5 py-2.5 rounded-lg border <?= !empty($errors['guest_count']) ? 'border-red-400 bg-red-50/30' : 'border-slate-300' ?> text-sm font-mono focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                    >
                    <?php if (!empty($errors['guest_count'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e($errors['guest_count']) ?></p>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 mt-1">Tidak boleh melebihi kapasitas meja yang dipilih.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan Khusus -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Khusus <span class="text-slate-400 lowercase font-normal">(opsional)</span>
                </label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    maxlength="255"
                    placeholder="Contoh: Permintaan meja dekat jendela, kursi bayi, atau perayaan ulang tahun..."
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
                ><?= e(old('notes')) ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/reservations" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-lg bg-[#FF5722] text-white text-sm font-semibold hover:bg-[#E64A19] shadow-sm transition-all active:translate-y-px"
                >
                    Simpan Reservasi
                </button>
            </div>
        </form>
    </div>
</div>
