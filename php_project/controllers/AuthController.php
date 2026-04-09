<?php
/**
 * Controlador de Autenticación
 * LogiSystem
 */

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function showLogin(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        verifyCsrf();

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            setFlash('error', t('invalid_credentials'));
            redirect('/login');
        }

        $user = $this->model->findByUsername($username);

        if (!$user || !verifyPassword($password, $user['password'])) {
            setFlash('error', t('invalid_credentials'));
            redirect('/login');
        }

        if ($user['estado'] !== 'activo') {
            setFlash('error', t('user_inactive'));
            redirect('/login');
        }

        setUserSession($user);
        redirect('/dashboard');
    }

    public function logout(): void
    {
        destroySession();
        session_start();
        setFlash('success', t('session_closed'));
        redirect('/login');
    }
}
