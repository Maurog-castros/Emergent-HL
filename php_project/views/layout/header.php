<?php
/**
 * Layout Principal — Header y Sidebar
 * Variables esperadas: $pageTitle, $activeModule
 */
$user  = getCurrentUser();
$flash = getFlash();
$lang  = currentLang();
?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'LogiSystem') ?> — <?= e(t('app_name')) ?></title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- LogiSystem CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ═══════════════ SIDEBAR ═══════════════════════════════════════════════════ -->
<nav class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-diagram-3-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name"><?= e(t('app_name')) ?></span>
            <span class="brand-sub"><?= e(t('app_subtitle')) ?></span>
        </div>
    </div>

    <!-- Navegación -->
    <ul class="sidebar-nav">
        <li class="nav-section-label"><?= e(t('system_modules')) ?></li>

        <li>
            <a href="/dashboard" class="nav-link <?= ($activeModule ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span><?= e(t('nav_dashboard')) ?></span>
            </a>
        </li>

        <?php if ($user['role'] === 'admin'): ?>
        <li>
            <a href="/users" class="nav-link <?= ($activeModule ?? '') === 'users' ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span><?= e(t('nav_users')) ?></span>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="/clients" class="nav-link <?= ($activeModule ?? '') === 'clients' ? 'active' : '' ?>">
                <i class="bi bi-building"></i>
                <span><?= e(t('nav_clients')) ?></span>
            </a>
        </li>

        <!-- Módulos futuros -->
        <li class="nav-section-label mt-2"><?= e(t('coming_soon_modules')) ?></li>

        <li class="nav-item-disabled">
            <span class="nav-link">
                <i class="bi bi-ship"></i>
                <span><?= e(t('module_shipments')) ?></span>
                <span class="ms-auto badge-soon">Soon</span>
            </span>
        </li>
        <li class="nav-item-disabled">
            <span class="nav-link">
                <i class="bi bi-file-earmark-text"></i>
                <span><?= e(t('module_bl')) ?></span>
                <span class="ms-auto badge-soon">Soon</span>
            </span>
        </li>
        <li class="nav-item-disabled">
            <span class="nav-link">
                <i class="bi bi-box-seam"></i>
                <span><?= e(t('module_containers')) ?></span>
                <span class="ms-auto badge-soon">Soon</span>
            </span>
        </li>
        <li class="nav-item-disabled">
            <span class="nav-link">
                <i class="bi bi-geo-alt"></i>
                <span><?= e(t('module_tracking')) ?></span>
                <span class="ms-auto badge-soon">Soon</span>
            </span>
        </li>
        <li class="nav-item-disabled">
            <span class="nav-link">
                <i class="bi bi-folder2"></i>
                <span><?= e(t('module_documents')) ?></span>
                <span class="ms-auto badge-soon">Soon</span>
            </span>
        </li>
    </ul>

    <!-- Selector de Idioma -->
    <div class="sidebar-divider"></div>
    <div class="sidebar-lang">
        <i class="bi bi-translate lang-icon"></i>
        <div class="lang-btns">
            <a href="?lang=es" class="lang-btn <?= $lang === 'es' ? 'active' : '' ?>" title="Español">ES</a>
            <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" title="English">EN</a>
            <a href="?lang=zh" class="lang-btn <?= $lang === 'zh' ? 'active' : '' ?>" title="中文">中文</a>
        </div>
    </div>

    <!-- Usuario -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <?= e(strtoupper(substr($user['name'] ?: 'U', 0, 1))) ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= e($user['name']) ?></span>
            <span class="user-role"><?= e(t($user['role'] === 'admin' ? 'admin_role' : 'operator_role')) ?></span>
        </div>
        <a href="/logout" class="logout-btn" title="<?= e(t('logout')) ?>">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>

</nav><!-- /sidebar -->

<!-- ═══════════════ CONTENIDO PRINCIPAL ═══════════════════════════════════════ -->
<div class="main-wrapper">

    <!-- Top Navbar -->
    <header class="top-navbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="page-breadcrumb">
            <h1 class="page-title"><?= e($pageTitle ?? '') ?></h1>
        </div>
        <div class="navbar-end">
            <span class="current-user-badge">
                <i class="bi bi-person-circle"></i>
                <?= e($user['username']) ?>
            </span>
        </div>
    </header>

    <!-- Área de Contenido -->
    <main class="content-area">
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?> alert-dismissible fade show logi-alert" role="alert" id="flashMessage">
            <i class="bi bi-<?= $flash['type'] === 'error' ? 'exclamation-triangle-fill' : ($flash['type'] === 'success' ? 'check-circle-fill' : 'info-circle-fill') ?> me-2"></i>
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
