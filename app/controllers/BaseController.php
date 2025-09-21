<?php
// app/controllers/BaseController.php
class BaseController
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /* ===================== Helpers umum ===================== */

    // Cek method request
    protected function isPost(): bool
    {
        return (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
    }
    protected function isGet(): bool
    {
        return (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET');
    }

    // Ambil input (rename agar tidak bentrok dengan action post() di SalesController)
    protected function inputGet(string $key, $default = null)
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
    protected function inputPost(string $key, $default = null)
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    // Redirect helper
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    // Batasi akses sesuai level
    protected function requireLevel(array $levels)
    {
        $lv = $_SESSION['user']['level'] ?? null;
        if (!in_array($lv, $levels, true)) {
            http_response_code(403);
            exit('Forbidden');
        }
    }

    /* ===================== View renderers ===================== */

    /**
     * Render via layout.
     * Bisa menerima:
     *  - ID view relatif: 'customers/index'  → app/views/customers/index.php
     *  - ATAU path file .php yang sudah valid (akan dipakai apa adanya)
     */
    protected function view(string $view, array $data = []): void
    {
        if (is_file($view)) {
            $file = $view;
        } else {
            $v = ltrim($view, '/');
            if (substr($v, -4) !== '.php') $v .= '.php';
            $file = __DIR__ . '/../views/' . $v;
        }

        if (!is_file($file)) {
            http_response_code(500);
            echo "View file tidak ditemukan: " . htmlspecialchars($file);
            return;
        }

        extract($data, EXTR_SKIP);
        $GLOBALS['__view_content'] = $file; // layout akan include file ini
        include __DIR__ . '/../views/partials/layout.php';
    }

    // Render langsung tanpa layout
    protected function render(string $view, array $data = []): void
    {
        $file = is_file($view)
            ? $view
            : __DIR__ . '/../views/' . ltrim(substr($view, -4) === '.php' ? $view : $view . '.php', '/');

        if (!is_file($file)) {
            echo "View tidak ditemukan";
            return;
        }
        extract($data, EXTR_SKIP);
        include $file;
    }
}
