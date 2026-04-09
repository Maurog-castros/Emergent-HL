<?php
/**
 * Funciones de utilidad general
 * LogiSystem
 */

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Escapa HTML para prevenir XSS
 */
function e(mixed $str): string
{
    return htmlspecialchars((string) $str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function sanitize(string $input): string
{
    return trim(strip_tags($input));
}

function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function formatDate(?string $date): string
{
    if (!$date) return '-';
    return date('d/m/Y H:i', strtotime($date));
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Error de seguridad: token CSRF inválido. Por favor recargue la página.');
    }
}

function statusBadge(string $status): string
{
    $class = $status === 'activo' ? 'badge-active' : 'badge-inactive';
    $label = t($status === 'activo' ? 'active' : 'inactive');
    return '<span class="status-badge ' . $class . '">' . e($label) . '</span>';
}

function roleBadge(string $role): string
{
    $class = $role === 'admin' ? 'badge-admin' : 'badge-operator';
    $label = t($role === 'admin' ? 'admin_role' : 'operator_role');
    return '<span class="role-badge ' . $class . '">' . e($label) . '</span>';
}
