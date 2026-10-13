<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? '405 — Metode Tidak Diizinkan') ?> | RestoReserve</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body { font-family: Montserrat, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-lg border border-slate-200 p-8 shadow-sm text-center">
        <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-2xl border border-amber-200">
            405
        </div>
        <h1 class="text-xl font-bold text-slate-900 mb-2">Metode HTTP Tidak Diizinkan</h1>
        <p class="text-sm text-slate-600 mb-6"><?= e($message ?? 'Akses request method tidak diizinkan pada endpoint ini.') ?></p>
        <a href="/" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-[#607D8B] text-white text-sm font-semibold hover:bg-[#455A64] transition-colors shadow-sm">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
