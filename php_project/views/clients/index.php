<?php
/**
 * Vista: Lista de Clientes
 * Variables: $clients, $search, $status
 */
require __DIR__ . '/../layout/header.php';
?>

<!-- Toolbar -->
<div class="page-toolbar mb-4">
    <div>
        <h4 class="toolbar-title mb-0"><?= e(t('client_list')) ?></h4>
        <small class="text-muted"><?= count($clients) ?> <?= e(t('results_found')) ?></small>
    </div>
    <a href="/clients/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i><?= e(t('new_client')) ?>
    </a>
</div>

<!-- Filtros -->
<div class="logi-card mb-4">
    <div class="logi-card-body">
        <form method="GET" action="/clients" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1"><?= e(t('search')) ?></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        name="search"
                        placeholder="<?= e(t('rut')) ?>, <?= e(t('razon_social')) ?>, <?= e(t('city')) ?>..."
                        value="<?= e($search ?? '') ?>"
                    >
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1"><?= e(t('status')) ?></label>
                <select name="status" class="form-select form-select-sm">
                    <option value=""><?= e(t('all')) ?></option>
                    <option value="activo"   <?= ($status ?? '') === 'activo'   ? 'selected' : '' ?>><?= e(t('active')) ?></option>
                    <option value="inactivo" <?= ($status ?? '') === 'inactivo' ? 'selected' : '' ?>><?= e(t('inactive')) ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i><?= e(t('filter')) ?>
                </button>
            </div>
            <?php if (!empty($search) || !empty($status)): ?>
            <div class="col-md-2">
                <a href="/clients" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-x me-1"></i>Limpiar
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="logi-card">
    <div class="logi-card-body p-0">
        <div class="table-responsive">
            <table class="table logi-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th><?= e(t('rut')) ?></th>
                        <th><?= e(t('razon_social')) ?></th>
                        <th><?= e(t('contact')) ?></th>
                        <th><?= e(t('email')) ?></th>
                        <th><?= e(t('phone')) ?></th>
                        <th><?= e(t('city')) ?></th>
                        <th><?= e(t('country')) ?></th>
                        <th><?= e(t('status')) ?></th>
                        <th class="text-center"><?= e(t('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <?= e(t('no_records')) ?>
                        </td>
                    </tr>
                    <?php else: foreach ($clients as $c): ?>
                    <tr>
                        <td class="ps-4 text-muted fw-medium"><?= (int)$c['id'] ?></td>
                        <td><code class="text-muted"><?= e($c['rut']) ?></code></td>
                        <td class="fw-medium"><?= e($c['razon_social']) ?></td>
                        <td class="text-muted small"><?= e($c['contacto'] ?: '—') ?></td>
                        <td class="text-muted small"><?= e($c['correo'] ?: '—') ?></td>
                        <td class="text-muted small"><?= e($c['telefono'] ?: '—') ?></td>
                        <td class="text-muted small"><?= e($c['ciudad'] ?: '—') ?></td>
                        <td class="text-muted small"><?= e($c['pais'] ?: '—') ?></td>
                        <td><?= statusBadge($c['estado']) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/clients/edit/<?= (int)$c['id'] ?>"
                                   class="btn btn-outline-primary btn-icon"
                                   title="<?= e(t('edit')) ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/clients/toggle/<?= (int)$c['id'] ?>"
                                   class="btn btn-outline-<?= $c['estado'] === 'activo' ? 'warning' : 'success' ?> btn-icon"
                                   title="<?= $c['estado'] === 'activo' ? e(t('deactivate')) : e(t('activate')) ?>"
                                   onclick="return confirm('<?= e(t('confirm_toggle')) ?>')">
                                    <i class="bi bi-toggle-<?= $c['estado'] === 'activo' ? 'on' : 'off' ?>"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
