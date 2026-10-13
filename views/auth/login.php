<?php $errors = get_errors(); ?>

<div class="bg-white text-slate-800 rounded-xl border border-slate-200 p-8 shadow-xl animate-fade-up">
    <!-- Header Card -->
    <div class="text-center mb-8">
        <img src="/assets/images/logo.png" alt="RestoReserve Logo" class="w-16 h-16 rounded-xl object-cover mx-auto mb-3 shadow-md border border-slate-200">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">RestoReserve</h1>
        <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-semibold">Portal Administrasi Restoran</p>
    </div>

    <!-- Alert Kesalahan -->
    <?php if (!empty($errors['general'])): ?>
        <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center space-x-2">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span><?= e($errors['general']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form action="/login" method="POST" class="space-y-5" data-loading>
        <?= csrf_field() ?>

        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Username Admin <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="username"
                name="username"
                value="<?= e(old('username')) ?>"
                required
                autocomplete="username"
                autofocus
                placeholder="Masukkan username"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
            >
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Password <span class="text-red-500">*</span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#607D8B] focus:ring-2 focus:ring-[#607D8B]/20 transition-all"
            >
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-lg bg-[#263238] text-white text-sm font-semibold hover:bg-[#37474F] active:translate-y-px transition-all shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#263238]"
            >
                Masuk ke Dashboard
            </button>
        </div>
    </form>

    <!-- Footer Note -->
    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-400">
            Sistem Informasi Manajemen Restoran Terintegrasi
        </p>
    </div>
</div>
