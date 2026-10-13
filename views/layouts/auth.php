<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> — RestoReserve</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body { font-family: Montserrat, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body class="bg-[#263238] text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-[#FF5722] selection:text-white">
    <main class="w-full max-w-md">
        <?php $flash = get_flash(); ?>
        <?php if ($flash): ?>
            <div data-flash-alert class="mb-4 p-4 rounded-lg text-sm border flex items-center justify-between <?= $flash['type'] === 'error' ? 'bg-red-950/80 border-red-800 text-red-200' : 'bg-emerald-950/80 border-emerald-800 text-emerald-200' ?>">
                <div class="flex items-center space-x-2">
                    <span><?= e($flash['message']) ?></span>
                </div>
                <button type="button" data-flash-close class="text-slate-400 hover:text-white ml-2">&times;</button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <script src="/assets/js/app.js"></script>
</body>
</html>
