<?php
/**
 * Vista: Formulario Embarque (Crear / Editar)
 * Variables: $embarqueEdit (null=crear, array=editar), $formData, $errors, $clients
 */
$isEdit = isset($embarqueEdit) && $embarqueEdit !== null;
require __DIR__ . '/../layout/header.php';

$val = static fn(string $k) => e($formData[$k] ?? $embarqueEdit[$k] ?? '');
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= $isEdit ? '/embarques/show/' . (int)$embarqueEdit['id'] : '/embarques' ?>"
       class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i><?= e(t('back')) ?>
    </a>
    <h4 class="mb-0"><?= e($pageTitle) ?></h4>
</div>

<div class="logi-card">
    <div class="logi-card-body">
        <form method="POST"
              action="<?= $isEdit ? '/embarques/edit/' . (int)$embarqueEdit['id'] : '/embarques/create' ?>"
              novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <!-- Sección 1: Identificación -->
            <h6 class="form-section-title">Identificación</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <?= e(t('numero_embarque')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="numero_embarque"
                           class="form-control <?= isset($errors['numero_embarque']) ? 'is-invalid' : '' ?>"
                           value="<?= $val('numero_embarque') ?>"
                           placeholder="EMB-2024-001" required>
                    <?php if (isset($errors['numero_embarque'])): ?>
                    <div class="invalid-feedback"><?= e($errors['numero_embarque']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <?= e(t('clients')) ?> <span class="text-danger">*</span>
                    </label>
                    <select name="cliente_id"
                            class="form-select <?= isset($errors['cliente_id']) ? 'is-invalid' : '' ?>"
                            required>
                        <option value="">— Seleccionar cliente —</option>
                        <?php foreach ($clients as $cl): ?>
                        <option value="<?= (int)$cl['id'] ?>"
                            <?= ((int)($formData['cliente_id'] ?? $embarqueEdit['cliente_id'] ?? 0)) === (int)$cl['id'] ? 'selected' : '' ?>>
                            <?= e($cl['razon_social']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['cliente_id'])): ?>
                    <div class="invalid-feedback"><?= e($errors['cliente_id']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold"><?= e(t('tipo_embarque')) ?></label>
                    <select name="tipo" class="form-select">
                        <?php foreach (['maritimo','aereo','terrestre','multimodal'] as $tp): ?>
                        <option value="<?= $tp ?>"
                            <?= ($formData['tipo'] ?? $embarqueEdit['tipo'] ?? 'maritimo') === $tp ? 'selected' : '' ?>>
                            <?= e(t($tp)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold"><?= e(t('status')) ?></label>
                    <select name="estado" class="form-select">
                        <?php foreach (['borrador','confirmado','en_transito','en_destino','entregado','cancelado'] as $st): ?>
                        <option value="<?= $st ?>"
                            <?= ($formData['estado'] ?? $embarqueEdit['estado'] ?? 'borrador') === $st ? 'selected' : '' ?>>
                            <?= e(t($st)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Sección 2: Ruta -->
            <h6 class="form-section-title">Ruta</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('origen_ciudad')) ?></label>
                    <input type="text" name="origen_ciudad" class="form-control"
                           value="<?= $val('origen_ciudad') ?>" placeholder="Valparaíso">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('origen_pais')) ?></label>
                    <input type="text" name="origen_pais" class="form-control"
                           value="<?= $val('origen_pais') ?>" placeholder="Chile" list="countries-list">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('destino_ciudad')) ?></label>
                    <input type="text" name="destino_ciudad" class="form-control"
                           value="<?= $val('destino_ciudad') ?>" placeholder="Shanghai">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('destino_pais')) ?></label>
                    <input type="text" name="destino_pais" class="form-control"
                           value="<?= $val('destino_pais') ?>" placeholder="China" list="countries-list">
                </div>
            </div>

            <!-- Sección 3: Fechas e Incoterm -->
            <h6 class="form-section-title">Fechas y Condiciones</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('fecha_embarque')) ?></label>
                    <input type="date" name="fecha_embarque" class="form-control"
                           value="<?= $val('fecha_embarque') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('fecha_llegada_estimada')) ?></label>
                    <input type="date" name="fecha_llegada_estimada" class="form-control"
                           value="<?= $val('fecha_llegada_estimada') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('fecha_llegada_real')) ?></label>
                    <input type="date" name="fecha_llegada_real" class="form-control"
                           value="<?= $val('fecha_llegada_real') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('incoterm')) ?></label>
                    <select name="incoterm" class="form-select">
                        <option value="">— Sin incoterm —</option>
                        <?php foreach (['EXW','FCA','FAS','FOB','CFR','CIF','CPT','CIP','DPU','DAP','DDP'] as $inc): ?>
                        <option value="<?= $inc ?>"
                            <?= ($formData['incoterm'] ?? $embarqueEdit['incoterm'] ?? '') === $inc ? 'selected' : '' ?>>
                            <?= $inc ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Sección 4: Observaciones -->
            <h6 class="form-section-title">Observaciones</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('descripcion_carga')) ?></label>
                    <textarea name="descripcion_carga" class="form-control" rows="3"
                              placeholder="Descripción de la carga..."><?= $val('descripcion_carga') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Notas Internas</label>
                    <textarea name="notas" class="form-control" rows="3"
                              placeholder="Notas internas..."><?= $val('notas') ?></textarea>
                </div>
            </div>

            <datalist id="countries-list">
                <?php foreach (['Chile','Argentina','Perú','Colombia','Bolivia','Brasil','Uruguay','China','España','Estados Unidos','Alemania','Japón','Corea del Sur','Países Bajos','Singapur','Hong Kong','México','Francia','Reino Unido','Italia'] as $c): ?>
                <option value="<?= $c ?>">
                <?php endforeach; ?>
            </datalist>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= e(t('save')) ?>
                </button>
                <a href="<?= $isEdit ? '/embarques/show/' . (int)$embarqueEdit['id'] : '/embarques' ?>"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i><?= e(t('cancel')) ?>
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
