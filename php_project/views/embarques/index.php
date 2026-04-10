<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- Toolbar -->
<div class="page-toolbar mb-4">
    <div>
        <h4 class="toolbar-title mb-0"><?= e(t('embarque_list')) ?></h4>
        <small class="text-muted"><?= count($embarques) ?> <?= e(t('results_found')) ?></small>
    </div>
    <a href="/embarques/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i><?= e(t('new_embarque')) ?>
    </a>
</div>

<!-- Filtros -->
<div class="logi-card mb-4">
    <div class="logi-card-body">
        <form method="GET" action="/embarques" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1"><?= e(t('search')) ?></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search"
                           placeholder="N° embarque, cliente, ciudad..."
                           value="<?= e($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1"><?= e(t('status')) ?></label>
                <select name="estado" class="form-select form-select-sm">
                    <option value=""><?= e(t('all')) ?></option>
                    <?php foreach (['borrador','confirmado','en_transito','en_destino','entregado','cancelado'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($estado ?? '') === $s ? 'selected' : '' ?>>
                        <?= e(t($s)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1"><?= e(t('tipo_embarque')) ?></label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value=""><?= e(t('all')) ?></option>
                    <?php foreach (['maritimo','aereo','terrestre','multimodal'] as $tp): ?>
                    <option value="<?= $tp ?>" <?= ($tipo ?? '') === $tp ? 'selected' : '' ?>>
                        <?= e(t($tp)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i><?= e(t('filter')) ?>
                </button>
            </div>
            <?php if (!empty($search) || !empty($estado) || !empty($tipo)): ?>
            <div class="col-md-2">
                <a href="/embarques" class="btn btn-outline-secondary btn-sm w-100">
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
                        <th class="ps-4">N° Embarque</th>
                        <th><?= e(t('clients')) ?></th>
                        <th><?= e(t('tipo_embarque')) ?></th>
                        <th><?= e(t('origen')) ?></th>
                        <th><?= e(t('destino')) ?></th>
                        <th><?= e(t('fecha_embarque')) ?></th>
                        <th><?= e(t('fecha_llegada_estimada')) ?></th>
                        <th><?= e(t('status')) ?></th>
                        <th class="text-center"><?= e(t('actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($embarques)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            <?= e(t('no_records')) ?>
                        </td>
                    </tr>
                    <?php else: foreach ($embarques as $emb): ?>
                    <tr>
                        <td class="ps-4 fw-semibold">
                            <a href="/embarques/show/<?= (int)$emb['id'] ?>" class="text-decoration-none">
                                <i class="bi <?= e(tipoEmbarqueIcon($emb['tipo'])) ?> me-1 text-muted"></i>
                                <?= e($emb['numero_embarque']) ?>
                            </a>
                        </td>
                        <td class="text-muted small"><?= e($emb['cliente_nombre'] ?? '—') ?></td>
                        <td><span class="tipo-badge tipo-<?= e($emb['tipo']) ?>"><?= e(t($emb['tipo'])) ?></span></td>
                        <td class="small text-muted">
                            <?= e($emb['origen_ciudad'] ?: '—') ?>
                            <?= $emb['origen_pais'] ? ', ' . e($emb['origen_pais']) : '' ?>
                        </td>
                        <td class="small text-muted">
                            <?= e($emb['destino_ciudad'] ?: '—') ?>
                            <?= $emb['destino_pais'] ? ', ' . e($emb['destino_pais']) : '' ?>
                        </td>
                        <td class="small text-muted"><?= e(formatDateOnly($emb['fecha_embarque'])) ?></td>
                        <td class="small text-muted"><?= e(formatDateOnly($emb['fecha_llegada_estimada'])) ?></td>
                        <td><?= embarqueStatusBadge($emb['estado']) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="/embarques/show/<?= (int)$emb['id'] ?>"
                                   class="btn btn-teal btn-icon" title="<?= e(t('view_detail')) ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/embarques/edit/<?= (int)$emb['id'] ?>"
                                   class="btn btn-outline-primary btn-icon" title="<?= e(t('edit')) ?>">
                                    <i class="bi bi-pencil"></i>
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
