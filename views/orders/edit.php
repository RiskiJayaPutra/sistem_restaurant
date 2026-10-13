<?php $errors = get_errors(); ?>

<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Item Pesanan Draft #<?= (int) $order['id'] ?></h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Reservasi #<?= (int) $order['reservation_id'] ?> &bull; Tamu: <strong><?= e($order['customer_name']) ?></strong> (Meja <?= e($order['table_number']) ?>)
            </p>
        </div>
        <a href="/orders/<?= (int) $order['id'] ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Batal &amp; Kembali
        </a>
    </div>

    <!-- Alert Kesalahan -->
    <?php if (!empty($errors['general'])): ?>
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors['items'])): ?>
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <?= e($errors['items']) ?>
        </div>
    <?php endif; ?>

    <!-- Form Update Pesanan -->
    <form action="/orders/<?= (int) $order['id'] ?>/update" method="POST" data-loading class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white p-6 sm:p-8 rounded-lg border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Ubah Pilihan Menu Makanan &amp; Minuman
                </h2>
                <button
                    type="button"
                    id="add-menu-item-btn"
                    class="inline-flex items-center px-3 py-1.5 rounded-md bg-[#607D8B] text-white text-xs font-semibold hover:bg-[#455A64] transition-colors shadow-sm"
                >
                    + Tambah Menu
                </button>
            </div>

            <!-- Header Kolom Baris -->
            <div class="hidden sm:grid sm:grid-cols-12 gap-3 text-xs font-semibold text-slate-500 uppercase tracking-wider px-2">
                <div class="col-span-6">Nama Menu</div>
                <div class="col-span-2 text-center">Jumlah (Qty)</div>
                <div class="col-span-3 text-right">Subtotal</div>
                <div class="col-span-1 text-center">Aksi</div>
            </div>

            <!-- Container Baris Menu -->
            <div id="order-items-container" class="space-y-3">
                <?php foreach ($order['items'] as $index => $item): ?>
                    <div class="order-item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3 rounded-lg border border-slate-200 bg-slate-50/50">
                        <div class="col-span-6">
                            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Menu</label>
                            <select name="items[<?= $index ?>][menu_item_id]" required class="menu-select w-full px-3 py-2 rounded-md border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
                                <option value="">-- Pilih Menu Makanan/Minuman --</option>
                                <?php foreach ($availableMenus as $m): ?>
                                    <option value="<?= (int) $m['id'] ?>" data-price="<?= (int) $m['price'] ?>" <?= ($m['id'] == $item['menu_item_id']) ? 'selected' : '' ?>>
                                        [<?= e($m['category']) ?>] <?= e($m['name']) ?> &mdash; <?= formatRupiah($m['price']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Qty</label>
                            <input type="number" name="items[<?= $index ?>][quantity]" value="<?= (int) $item['quantity'] ?>" min="1" max="99" required class="qty-input w-full px-3 py-2 text-center rounded-md border border-slate-300 text-sm font-mono focus:outline-none focus:border-[#607D8B]">
                        </div>
                        <div class="col-span-3 text-right">
                            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Subtotal</label>
                            <span class="subtotal-preview font-mono font-semibold text-slate-900 text-sm"><?= formatRupiah($item['subtotal']) ?></span>
                        </div>
                        <div class="col-span-1 text-center">
                            <button type="button" class="remove-row-btn p-1.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus baris">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Footer Total Preview -->
            <div class="border-t border-slate-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                    * Perubahan item akan menghitung ulang total pesanan secara otomatis di server.
                </div>
                <div class="flex items-center space-x-3 text-right">
                    <span class="text-sm font-medium text-slate-600 uppercase tracking-wider">Perkiraan Total:</span>
                    <span id="order-total-preview" class="text-2xl font-black font-mono text-slate-900 transition-colors">
                        <?= formatRupiah($order['total']) ?>
                    </span>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-3">
                <a href="/orders/<?= (int) $order['id'] ?>" class="px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-lg bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] shadow-sm transition-all active:translate-y-px"
                >
                    Simpan Perubahan Draft
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Template untuk Baris Baru -->
<template id="menu-item-row-template">
    <div class="order-item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3 rounded-lg border border-slate-200 bg-slate-50/50">
        <div class="col-span-6">
            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Menu</label>
            <select name="items[][menu_item_id]" required class="menu-select w-full px-3 py-2 rounded-md border border-slate-300 text-sm focus:outline-none focus:border-[#607D8B]">
                <option value="">-- Pilih Menu Makanan/Minuman --</option>
                <?php foreach ($availableMenus as $m): ?>
                    <option value="<?= (int) $m['id'] ?>" data-price="<?= (int) $m['price'] ?>">
                        [<?= e($m['category']) ?>] <?= e($m['name']) ?> &mdash; <?= formatRupiah($m['price']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-span-2">
            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Qty</label>
            <input type="number" name="items[][quantity]" value="1" min="1" max="99" required class="qty-input w-full px-3 py-2 text-center rounded-md border border-slate-300 text-sm font-mono focus:outline-none focus:border-[#607D8B]">
        </div>
        <div class="col-span-3 text-right">
            <label class="sm:hidden block text-xs font-semibold text-slate-600 mb-1">Subtotal</label>
            <span class="subtotal-preview font-mono font-semibold text-slate-900 text-sm">Rp0</span>
        </div>
        <div class="col-span-1 text-center">
            <button type="button" class="remove-row-btn p-1.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus baris">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>
</template>
