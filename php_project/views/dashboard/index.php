<?php
/**
 * Vista: Dashboard Principal
 * Variables: $userStats, $clientStats
 */
require __DIR__ . '/../layout/header.php';
$user = getCurrentUser();
?>

<!-- Saludo -->
<div class="dash-welcome mb-4">
    <h2 class="dash-greeting"><?= e(t('welcome_back', ['name' => $user['name']])) ?></h2>
    <p class="dash-date"><?= date('l, d \d\e F \d\e Y') ?></p>
</div>

<!-- Estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-body">
                <span class="stat-num"><?= (int)($userStats['total'] ?? 0) ?></span>
                <span class="stat-lbl"><?= e(t('total_users')) ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-teal">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-body">
                <span class="stat-num"><?= (int)($userStats['activos'] ?? 0) ?></span>
                <span class="stat-lbl"><?= e(t('active_users')) ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-indigo">
            <div class="stat-icon"><i class="bi bi-building-fill"></i></div>
            <div class="stat-body">
                <span class="stat-num"><?= (int)($clientStats['total'] ?? 0) ?></span>
                <span class="stat-lbl"><?= e(t('total_clients')) ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="bi bi-building-check"></i></div>
            <div class="stat-body">
                <span class="stat-num"><?= (int)($clientStats['activos'] ?? 0) ?></span>
                <span class="stat-lbl"><?= e(t('active_clients')) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Módulos Activos -->
<div class="logi-card mb-4">
    <div class="logi-card-header">
        <i class="bi bi-grid-3x3-gap-fill me-2"></i><?= e(t('quick_access')) ?>
    </div>
    <div class="logi-card-body">
        <div class="row g-3">
            <?php if ($user['role'] === 'admin'): ?>
            <div class="col-lg-4 col-md-6">
                <a href="/users" class="module-tile">
                    <div class="module-tile-icon bg-blue-soft">
                        <i class="bi bi-people-fill text-blue"></i>
                    </div>
                    <div class="module-tile-info">
                        <strong><?= e(t('users')) ?></strong>
                        <small><?= (int)($userStats['total'] ?? 0) ?> <?= e(t('total_users')) ?></small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
            <?php endif; ?>

            <div class="col-lg-4 col-md-6">
                <a href="/clients" class="module-tile">
                    <div class="module-tile-icon bg-teal-soft">
                        <i class="bi bi-building-fill text-teal"></i>
                    </div>
                    <div class="module-tile-info">
                        <strong><?= e(t('clients')) ?></strong>
                        <small><?= (int)($clientStats['total'] ?? 0) ?> <?= e(t('total_clients')) ?></small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Módulos en Desarrollo -->
<div class="logi-card">
    <div class="logi-card-header">
        <i class="bi bi-tools me-2"></i><?= e(t('coming_soon_modules')) ?>
    </div>
    <div class="logi-card-body">
        <div class="row g-3">
            <?php
            $comingSoon = [
                ['module_shipments',  'bi-ship',              'bg-orange-soft', 'text-orange'],
                ['module_bl',         'bi-file-earmark-text', 'bg-purple-soft', 'text-purple'],
                ['module_containers', 'bi-box-seam',          'bg-red-soft',    'text-red'],
                ['module_tracking',   'bi-geo-alt',           'bg-yellow-soft', 'text-yellow'],
                ['module_documents',  'bi-folder2',           'bg-cyan-soft',   'text-cyan'],
            ];
            foreach ($comingSoon as $m): ?>
            <div class="col-lg-4 col-md-6">
                <div class="module-tile module-tile-disabled">
                    <div class="module-tile-icon <?= $m[2] ?>">
                        <i class="bi <?= $m[1] ?> <?= $m[3] ?>"></i>
                    </div>
                    <div class="module-tile-info">
                        <strong><?= e(t($m[0])) ?></strong>
                        <small class="text-muted"><?= e(t('coming_soon')) ?></small>
                    </div>
                    <span class="badge-soon"><?= e(t('coming_soon')) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
