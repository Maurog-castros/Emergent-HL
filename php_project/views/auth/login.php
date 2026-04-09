<?php
/**
 * Vista: Página de Login
 * LogiSystem
 */
$lang  = currentLang();
$flash = getFlash();
?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(t('login')) ?> — <?= e(t('app_name')) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-container">

    <!-- Selector de idioma -->
    <div class="login-lang-bar">
        <a href="?lang=es" class="lang-btn <?= $lang === 'es' ? 'active' : '' ?>" title="Español">ES</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" title="English">EN</a>
        <a href="?lang=zh" class="lang-btn <?= $lang === 'zh' ? 'active' : '' ?>" title="中文">中文</a>
    </div>

    <!-- Card de Login -->
    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">
            <div class="login-logo-icon">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <h1 class="login-app-name"><?= e(t('app_name')) ?></h1>
            <p class="login-app-sub"><?= e(t('app_subtitle')) ?></p>
        </div>

        <!-- Flash Message -->
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?> alert-sm mb-4" role="alert">
            <i class="bi bi-<?= $flash['type'] === 'error' ? 'exclamation-triangle-fill' : 'check-circle-fill' ?> me-2"></i>
            <?= e($flash['message']) ?>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" action="/login" novalidate id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person me-1"></i><?= e(t('username')) ?>
                </label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                    autofocus
                    placeholder="<?= e(t('username')) ?>"
                    value="<?= e($_POST['username'] ?? '') ?>"
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i><?= e(t('password')) ?>
                </label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control form-control-lg"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    <button type="button" class="btn btn-outline-secondary toggle-pwd" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 login-btn" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                <?= e(t('login')) ?>
            </button>
        </form>
    </div>

    <p class="login-footer-text">
        &copy; <?= date('Y') ?> <?= e(t('app_name')) ?> &nbsp;&middot;&nbsp; <?= e(t('app_subtitle')) ?>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
