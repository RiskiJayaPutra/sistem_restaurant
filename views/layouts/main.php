<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sistem Reservasi') ?> — RestoReserve Architecture</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body { font-family: Montserrat, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
    </style>
</head>
<body class="bg-[#F4F6F8] text-slate-800 min-h-[100dvh] flex flex-col md:flex-row antialiased selection:bg-[#FF5722] selection:text-white">

    <!-- ============================================================
         SIDEBAR NAVIGATION (Constructivismo Arquitetônico)
         Panel Samping Komando Operasional
         ============================================================ -->
    <aside class="hidden md:flex md:w-64 lg:w-72 md:shrink-0 bg-[#263238] text-white flex-col min-h-screen border-r border-[#1b2428] sticky top-0 h-screen select-none z-30">
        
        <!-- Brand & Blueprint Identifier -->
        <div class="p-5 border-b border-[#37474F]/60 bg-[#1e272c]">
            <div class="flex items-center space-x-3">
                <img src="/assets/images/logo.png" alt="RestoReserve" class="w-10 h-10 rounded-sm object-cover shrink-0 shadow-sm border border-[#37474F]">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center space-x-1.5">
                        <span class="font-extrabold text-sm tracking-wider uppercase text-white font-mono">RESTORESERVE</span>
                    </div>
                    <div class="text-[10px] text-[#B0BEC5] tracking-widest uppercase font-mono mt-0.5">
                        Sistem Reservasi &amp; POS
                    </div>
                </div>
            </div>

            <!-- System Telemetry Status -->
            <div class="mt-4 px-2.5 py-1.5 rounded bg-[#263238] border border-[#37474F] flex items-center justify-between text-[11px] font-mono">
                <span class="flex items-center text-emerald-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
                    INNODB ONLINE
                </span>
                <span class="text-slate-400 text-[10px]">v2.4 2026</span>
            </div>
        </div>

        <!-- Navigation Links Matrix -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto text-xs font-medium">
            <?php
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $navSections = [
                'OPERASIONAL' => [
                    ['path' => '/', 'label' => 'Pusat Kendali', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['path' => '/reservations', 'label' => 'Jadwal Reservasi', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['path' => '/orders', 'label' => 'Pesanan Makanan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                ],
                'MASTER DATA' => [
                    ['path' => '/tables', 'label' => 'Denah & Meja', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    ['path' => '/menus', 'label' => 'Daftar Menu', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['path' => '/customers', 'label' => 'Buku Tamu / Tamu', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ],
                'AUDIT & LAPORAN' => [
                    ['path' => '/reports', 'label' => 'Rekapitulasi Omzet', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ],
            ];

            foreach ($navSections as $sectionTitle => $items):
            ?>
                <div>
                    <div class="px-2.5 pb-2 text-[10px] font-mono font-bold tracking-widest text-[#90A4AE] uppercase">
                        <?= e($sectionTitle) ?>
                    </div>
                    <div class="space-y-1">
                        <?php foreach ($items as $item):
                            $isActive = ($item['path'] === '/' && $currentUri === '/')
                                || ($item['path'] !== '/' && str_starts_with($currentUri, $item['path']));
                        ?>
                            <a href="<?= $item['path'] ?>" class="flex items-center justify-between px-3 py-2.5 rounded-sm transition-all duration-150 group <?= $isActive ? 'bg-[#1b2327] text-white border-l-4 border-[#FF5722] font-semibold shadow-inner' : 'text-slate-300 hover:bg-[#37474F]/70 hover:text-white border-l-4 border-transparent' ?>">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <svg class="w-4 h-4 shrink-0 <?= $isActive ? 'text-[#FF5722]' : 'text-[#B0BEC5] group-hover:text-white' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $item['icon'] ?>"/>
                                    </svg>
                                    <span class="truncate"><?= e($item['label']) ?></span>
                                </div>
                                <?php if ($isActive): ?>
                                    <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-[#FF5722]/20 text-[#FF5722] border border-[#FF5722]/40 font-bold">ACT</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>

        <!-- Sidebar Operator Terminal Footer -->
        <div class="p-4 border-t border-[#37474F]/60 bg-[#1e272c] mt-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-sm bg-[#607D8B] text-white flex items-center justify-center font-mono font-bold text-xs uppercase shrink-0">
                        OP
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-white truncate font-mono">
                            <?= e(\App\Core\Auth::username() ?? 'admin') ?>
                        </div>
                        <div class="text-[10px] text-emerald-400 font-mono">Sesi Aktif Otentik</div>
                    </div>
                </div>
                <form action="/logout" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin keluar dari sistem?">
                    <?= csrf_field() ?>
                    <button type="submit" title="Keluar dari sistem" class="p-1.5 rounded text-slate-400 hover:text-red-400 hover:bg-[#263238] transition-colors border border-transparent hover:border-red-900/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ============================================================
         MOBILE TOP BAR (Small screens < md)
         ============================================================ -->
    <div class="md:hidden bg-[#263238] text-white sticky top-0 z-40 border-b border-[#37474F] px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <img src="/assets/images/logo.png" alt="RestoReserve" class="w-7 h-7 rounded-sm object-cover shrink-0">
            <span class="font-extrabold text-sm tracking-wider uppercase font-mono">RESTORESERVE</span>
        </div>
        <button type="button" id="mobile-menu-btn" aria-expanded="false" class="p-2 rounded text-slate-300 hover:text-white hover:bg-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer Overlay & Menu -->
    <div id="mobile-backdrop" class="hidden md:hidden fixed inset-0 bg-black/60 z-40 backdrop-blur-xs"></div>
    <div id="mobile-menu" class="hidden md:hidden fixed inset-y-0 left-0 w-72 bg-[#263238] text-white z-50 flex flex-col border-r border-[#37474F] shadow-2xl">
        <div class="p-4 border-b border-[#37474F] flex items-center justify-between bg-[#1e272c]">
            <div class="flex items-center space-x-2">
                <img src="/assets/images/logo.png" alt="RestoReserve" class="w-8 h-8 rounded-sm object-cover shrink-0">
                <div>
                    <div class="font-bold text-xs font-mono uppercase">RESTORESERVE</div>
                    <div class="text-[10px] text-slate-400 font-mono">PANEL OPERASIONAL</div>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('mobile-menu').classList.add('hidden'); document.getElementById('mobile-backdrop').classList.add('hidden');" class="text-slate-400 hover:text-white text-lg font-bold p-1">&times;</button>
        </div>
        <div class="flex-1 p-4 space-y-4 overflow-y-auto">
            <?php foreach ($navSections as $sectionTitle => $items): ?>
                <div>
                    <div class="text-[10px] font-mono text-slate-400 uppercase tracking-widest mb-1"><?= e($sectionTitle) ?></div>
                    <div class="space-y-1">
                        <?php foreach ($items as $item):
                            $isActive = ($item['path'] === '/' && $currentUri === '/')
                                || ($item['path'] !== '/' && str_starts_with($currentUri, $item['path']));
                        ?>
                            <a href="<?= $item['path'] ?>" class="flex items-center px-3 py-2 rounded text-sm <?= $isActive ? 'bg-[#1b2327] text-white border-l-4 border-[#FF5722] font-semibold' : 'text-slate-300 hover:bg-[#37474F]' ?>">
                                <?= e($item['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="p-4 border-t border-[#37474F] bg-[#1e272c]">
            <form action="/logout" method="POST" data-confirm="Apakah Anda yakin ingin keluar?">
                <?= csrf_field() ?>
                <button type="submit" class="w-full py-2 px-3 rounded bg-red-900/80 text-red-100 text-xs font-semibold">Logout Operator</button>
            </form>
        </div>
    </div>

    <!-- ============================================================
         MAIN WORKSPACE CANVAS
         ============================================================ -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#F4F6F8]">
        
        <!-- Architectural System Subheader (Desktop) -->
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <div class="flex items-center space-x-3">
                <div class="hidden sm:flex items-center text-xs font-mono text-slate-400">
                    <span class="text-slate-600 font-semibold">PORTAL</span>
                    <span class="mx-1.5 text-slate-300">/</span>
                    <span class="text-[#607D8B] font-semibold uppercase"><?= e($title ?? 'Dashboard') ?></span>
                </div>
            </div>

            <!-- Date, Telemetry & Quick Action -->
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2 text-xs font-mono text-slate-500 bg-slate-50 px-2.5 py-1 rounded border border-slate-200">
                    <svg class="w-3.5 h-3.5 text-[#607D8B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= date('l, d M Y') ?> (WIB)</span>
                </div>
                <a href="/reservations/create" class="inline-flex items-center px-3.5 py-1.5 rounded-sm bg-[#FF5722] text-white text-xs font-semibold hover:bg-[#E64A19] shadow-construct-orange active:translate-y-px transition-all uppercase tracking-wider font-mono">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Reservasi
                </a>
            </div>
        </header>

        <!-- Workspace Main Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-[1550px] w-full mx-auto space-y-6">
            <!-- Flash Message Banner (Constructivist Styling) -->
            <?php $flash = get_flash(); ?>
            <?php if ($flash): ?>
                <div data-flash-alert class="p-4 rounded-sm border-l-4 text-xs font-mono flex items-start justify-between shadow-xs <?= $flash['type'] === 'error' ? 'bg-red-50 border-red-600 text-red-900 border-t border-r border-b border-red-200' : ($flash['type'] === 'warning' ? 'bg-amber-50 border-amber-600 text-amber-900 border-t border-r border-b border-amber-200' : 'bg-emerald-50 border-emerald-600 text-emerald-900 border-t border-r border-b border-emerald-200') ?>">
                    <div class="flex items-center space-x-2.5">
                        <span class="font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-white border border-current text-[10px]">
                            <?= strtoupper(e($flash['type'])) ?>
                        </span>
                        <span class="font-medium text-sm font-sans"><?= e($flash['message']) ?></span>
                    </div>
                    <button type="button" data-flash-close class="text-slate-400 hover:text-slate-700 font-bold ml-4 text-base leading-none">&times;</button>
                </div>
            <?php endif; ?>

            <!-- View Specific Body -->
            <?= $content ?? '' ?>
        </main>

        <!-- Architectural Blueprint Footer -->
        <footer class="bg-white border-t border-slate-200 px-4 sm:px-6 lg:px-8 py-3.5 mt-auto text-xs font-mono text-slate-500">
            <div class="max-w-[1550px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-700">RESTORESERVE</span>
                    <span class="text-slate-300">•</span>
                    <span>SISTEM RESERVASI MEJA &amp; POS v2.4</span>
                </div>
                <div class="flex items-center space-x-3 text-[11px] text-slate-400">
                    <span>SISTEM INFORMASI RESTORAN</span>
                    <span>•</span>
                    <span>INNODB ROW-LOCKING ENGINE</span>
                </div>
            </div>
        </footer>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
