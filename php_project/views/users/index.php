<?php
/**
 * Vista: Lista de Usuarios
 * Variables: $users, $search, $status
 */
require __DIR__ . '/../layout/header.php';
?>

<!-- Toolbar -->
<div class="page-toolbar mb-4">
    <div>
        <h4 class="toolbar-title mb-0"><?= e(t('user_list')) ?></h4>
        <small class="text-muted"><?= count($users) ?> <?= e(t('results_found')) ?></small>
    </div>
    <a href="/users/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i><?= e(t('new_user')) ?>
    </a>
</div>

<!-- Filtros -->
<div class="logi-card mb-4">
    <div class="logi-card-body">
        <form method="GET" action="/users" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1"><?= e(t('search')) ?></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        name="search"
                        placeholder="<?= e(t('first_name')) ?>, <?= e(t('email')) ?>, <?= e(t('username')) ?>..."
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
                <a href="/users" class="btn btn-outline-secondary btn-sm w-100">
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
                        <th><?= e(t('first_name')) ?></th>
                        <th><?= e(t('last_name')) ?></th>
                        <th><?= e(t('email')) ?></th>
                        <th><?= e(t('username')) ?></th>
                        <th><?= e(t('role')) ?></th>
                        <th><?= e(t('status')) ?></th>
                        <th><?= e(t('created_at')) ?></th>
                        <th class="text-center"><?= e(t('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <?= e(t('no_records')) ?>
                        </td>
                    </tr>
                    <?php else: foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-4 text-muted fw-medium"><?= (int)$u['id'] ?></td>
                        <td class="fw-medium"><?= e($u['nombre']) ?></td>
                        <td><?= e($u['apellido']) ?></td>
                        <td class="text-muted small"><?= e($u['correo']) ?></td>
                        <td><code class="text-primary"><?= e($u['username']) ?></code></td>
                        <td><?= roleBadge($u['rol']) ?></td>
                        <td><?= statusBadge($u['estado']) ?></td>
                        <td class="text-muted small"><?= e(formatDate($u['fecha_creacion'])) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/users/edit/<?= (int)$u['id'] ?>"
                                   class="btn btn-outline-primary btn-icon"
                                   title="<?= e(t('edit')) ?>">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ((int)$u['id'] !== (int)(getCurrentUser()['id'] ?? 0)): ?>
                                <a href="/users/toggle/<?= (int)$u['id'] ?>"
                                   class="btn btn-outline-<?= $u['estado'] === 'activo' ? 'warning' : 'success' ?> btn-icon"
                                   title="<?= $u['estado'] === 'activo' ? e(t('deactivate')) : e(t('activate')) ?>"
                                   onclick="return confirm('<?= e(t('confirm_toggle')) ?>')">
                                    <i class="bi bi-toggle-<?= $u['estado'] === 'activo' ? 'on' : 'off' ?>"></i>
                                </a>
                                <?php endif; ?>
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
