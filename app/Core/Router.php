<?php

namespace App\Core;

/**
 * Router — Pencocokan rute HTTP sederhana.
 *
 * Mendukung GET dan POST dengan parameter path (contoh: /customers/{id}/edit).
 * Query string tidak mempengaruhi pencocokan rute.
 *
 * @package RestoReserve\Core
 */
class Router
{
    /** @var array<string, array<string, array{callback: callable, params: array}>> Daftar rute terdaftar */
    private array $routes = [];

    /**
     * Daftarkan rute GET.
     *
     * @param string   $path     Pola URL (contoh: /customers/{id}/edit)
     * @param callable $callback Fungsi handler [Controller::class, 'method']
     * @return self
     */
    public function get(string $path, callable|array $callback): self
    {
        $this->addRoute('GET', $path, $callback);
        return $this;
    }

    /**
     * Daftarkan rute POST.
     *
     * @param string   $path     Pola URL
     * @param callable $callback Fungsi handler
     * @return self
     */
    public function post(string $path, callable|array $callback): self
    {
        $this->addRoute('POST', $path, $callback);
        return $this;
    }

    /**
     * Tambahkan rute ke registry internal.
     *
     * @param string         $method   HTTP method
     * @param string         $path     Pola URL
     * @param callable|array $callback Handler
     */
    private function addRoute(string $method, string $path, callable|array $callback): void
    {
        // Ubah {param} menjadi regex named group
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[0-9]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][$pattern] = $callback;
    }

    /**
     * Jalankan pencocokan rute terhadap request saat ini.
     *
     * Mencocokkan method dan path, mengekstrak parameter,
     * lalu memanggil callback controller yang sesuai.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path   = '/' . trim($path, '/');

        // Cari kecocokan di rute untuk method ini
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $callback) {
                if (preg_match($pattern, $path, $matches)) {
                    // Ekstrak parameter bernama saja (bukan numerik)
                    $params = array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);

                    $this->executeCallback($callback, $params);
                    return;
                }
            }
        }

        // Cek apakah path cocok tapi method salah (405)
        foreach ($this->routes as $routeMethod => $patterns) {
            if ($routeMethod === $method) {
                continue;
            }
            foreach ($patterns as $pattern => $callback) {
                if (preg_match($pattern, $path)) {
                    $this->sendError(405, 'Method Not Allowed');
                    return;
                }
            }
        }

        // Tidak ada yang cocok (404)
        $this->sendError(404, 'Halaman tidak ditemukan');
    }

    /**
     * Eksekusi callback controller.
     *
     * @param callable|array $callback [ClassName, methodName] atau callable
     * @param array          $params   Parameter yang diekstrak dari URL
     */
    private function executeCallback(callable|array $callback, array $params): void
    {
        if (is_array($callback)) {
            [$controllerClass, $method] = $callback;
            $controller = new $controllerClass();
            $controller->$method(...array_values($params));
        } else {
            $callback(...array_values($params));
        }
    }

    /**
     * Kirim halaman error HTTP.
     *
     * @param int    $code    HTTP status code
     * @param string $message Pesan yang ditampilkan
     */
    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        $title = match ($code) {
            404 => '404 — Halaman Tidak Ditemukan',
            405 => '405 — Metode Tidak Diizinkan',
            default => "$code — Error",
        };

        // Coba render view error jika tersedia
        $errorView = dirname(__DIR__, 2) . "/views/errors/{$code}.php";
        if (file_exists($errorView)) {
            extract(['title' => $title, 'message' => $message]);
            include $errorView;
            return;
        }

        // Fallback sederhana
        echo "<!DOCTYPE html><html lang=\"id\"><head><meta charset=\"utf-8\"><title>{$title}</title></head>";
        echo "<body style=\"font-family:sans-serif;text-align:center;padding:4rem\">";
        echo "<h1>{$title}</h1><p>" . e($message) . "</p>";
        echo "<a href=\"/\">Kembali ke Dashboard</a></body></html>";
    }
}
