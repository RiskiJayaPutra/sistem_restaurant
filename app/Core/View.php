<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View — Template renderer sederhana untuk RestoReserve.
 *
 * Mendukung layout pembungkus (default: layouts/main.php).
 * Variabel diekstrak ke dalam scope view.
 *
 * @package RestoReserve\Core
 */
class View
{
    /**
     * Render view template dengan layout.
     *
     * @param string      $view   Path view relatif terhadap folder views (contoh: 'customers/index')
     * @param array       $data   Data yang diteruskan ke view
     * @param string|null $layout Nama layout (default: 'main', null untuk tanpa layout)
     * @return void
     */
    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $baseDir = dirname(__DIR__, 2) . '/views';
        $viewFile = "{$baseDir}/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View [{$view}] tidak ditemukan di {$viewFile}";
            return;
        }

        // Ekstrak data ke variabel lokal view
        extract($data, EXTR_SKIP);

        // Render konten view ke buffer
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Jika tanpa layout, langsung cetak
        if ($layout === null) {
            echo $content;
            return;
        }

        // Render layout pembungkus
        $layoutFile = "{$baseDir}/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            // Fallback jika layout tidak ada
            echo $content;
        }
    }
}
