<?php
/**
 * Funciones de autenticación y gestión de sesiones
 * LogiSystem
 */

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('/login');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (getCurrentUser()['role'] !== 'admin') {
        setFlash('error', t('access_denied'));
        redirect('/dashboard');
    }
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function getCurrentUser(): array
{
    return [
        'id'       => $_SESSION['user_id']    ?? null,
        'name'     => $_SESSION['user_name']   ?? '',
        'email'    => $_SESSION['user_email']  ?? '',
        'role'     => $_SESSION['user_role']   ?? '',
        'username' => $_SESSION['username']    ?? '',
    ];
}

function setUserSession(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['user_name']  = trim($user['nombre'] . ' ' . $user['apellido']);
    $_SESSION['user_email'] = $user['correo'];
    $_SESSION['user_role']  = $user['rol'];
    $_SESSION['username']   = $user['username'];
}

function destroySession(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 86400, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
