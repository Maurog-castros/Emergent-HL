<?php
/**
 * LogiSystem — Front Controller / Router Principal
 *
 * Todos los requests HTTP pasan por este archivo.
 * El archivo .htaccess redirige todo a index.php.
 */

session_start();

// Cargar núcleo del sistema
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/i18n.php';

// ── Cambio de idioma ──────────────────────────────────────────────────────────
if (isset($_GET['lang'])) {
    loadLanguage(sanitize($_GET['lang']));
    // Redirigir a la ruta actual sin parámetro lang
    $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    if (!preg_match('#^[/a-zA-Z0-9/_-]*$#', $currentPath)) {
        $currentPath = '/';
    }
    redirect($currentPath);
}

// Cargar idioma de la sesión
loadLanguage($_SESSION['lang'] ?? 'es');

// ── Parsear ruta actual ───────────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path   = rtrim($uri ?? '', '/') ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// ── Router ────────────────────────────────────────────────────────────────────
switch (true) {

    // Raíz → redirigir según estado de login
    case ($path === '/'):
        redirect(isLoggedIn() ? '/dashboard' : '/login');

    // ── AUTENTICACIÓN ──────────────────────────────────────────────────────────
    case ($path === '/login'):
        require_once __DIR__ . '/controllers/AuthController.php';
        $ctrl = new AuthController();
        if ($method === 'POST') {
            $ctrl->login();
        } else {
            $ctrl->showLogin();
        }
        break;

    case ($path === '/logout'):
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    // ── DASHBOARD ─────────────────────────────────────────────────────────────
    case ($path === '/dashboard'):
        requireLogin();
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    // ── USUARIOS ──────────────────────────────────────────────────────────────
    case ($path === '/users'):
        requireLogin();
        requireAdmin();
        require_once __DIR__ . '/controllers/UserController.php';
        (new UserController())->index();
        break;

    case ($path === '/users/create'):
        requireLogin();
        requireAdmin();
        require_once __DIR__ . '/controllers/UserController.php';
        $ctrl = new UserController();
        if ($method === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->create();
        }
        break;

    case (preg_match('#^/users/edit/(\d+)$#', $path, $m) === 1):
        requireLogin();
        requireAdmin();
        require_once __DIR__ . '/controllers/UserController.php';
        $ctrl = new UserController();
        if ($method === 'POST') {
            $ctrl->update((int) $m[1]);
        } else {
            $ctrl->edit((int) $m[1]);
        }
        break;

    case (preg_match('#^/users/toggle/(\d+)$#', $path, $m) === 1):
        requireLogin();
        requireAdmin();
        require_once __DIR__ . '/controllers/UserController.php';
        (new UserController())->toggle((int) $m[1]);
        break;

    // ── CLIENTES ──────────────────────────────────────────────────────────────
    case ($path === '/clients'):
        requireLogin();
        require_once __DIR__ . '/controllers/ClientController.php';
        (new ClientController())->index();
        break;

    case ($path === '/clients/create'):
        requireLogin();
        require_once __DIR__ . '/controllers/ClientController.php';
        $ctrl = new ClientController();
        if ($method === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->create();
        }
        break;

    case (preg_match('#^/clients/edit/(\d+)$#', $path, $m) === 1):
        requireLogin();
        require_once __DIR__ . '/controllers/ClientController.php';
        $ctrl = new ClientController();
        if ($method === 'POST') {
            $ctrl->update((int) $m[1]);
        } else {
            $ctrl->edit((int) $m[1]);
        }
        break;

    case (preg_match('#^/clients/toggle/(\d+)$#', $path, $m) === 1):
        requireLogin();
        require_once __DIR__ . '/controllers/ClientController.php';
        (new ClientController())->toggle((int) $m[1]);
        break;

    // ── 404 ───────────────────────────────────────────────────────────────────
    default:
        http_response_code(404);
        require __DIR__ . '/views/errors/404.php';
        break;
}
