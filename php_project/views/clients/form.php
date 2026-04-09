<?php
/**
 * Vista: Formulario de Cliente (Crear / Editar)
 * Variables: $clientEdit (null=crear, array=editar), $formData, $errors
 */
$isEdit = isset($clientEdit) && $clientEdit !== null;
require __DIR__ . '/../layout/header.php';
?>

<!-- Toolbar -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/clients" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i><?= e(t('back')) ?>
    </a>
    <h4 class="mb-0"><?= e($pageTitle) ?></h4>
</div>

<div class="logi-card">
    <div class="logi-card-body">
        <form method="POST"
              action="<?= $isEdit ? '/clients/edit/' . (int)$clientEdit['id'] : '/clients/create' ?>"
              novalidate
              id="clientForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="row g-3">

                <!-- RUT / Identificador -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <?= e(t('rut')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['rut']) ? 'is-invalid' : '' ?>"
                           name="rut"
                           value="<?= e($formData['rut'] ?? $clientEdit['rut'] ?? '') ?>"
                           placeholder="76.123.456-7"
                           required>
                    <?php if (isset($errors['rut'])): ?>
                    <div class="invalid-feedback"><?= e($errors['rut']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">RUT, NIF, EIN u otro identificador fiscal único.</div>
                </div>

                <!-- Razón Social -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold">
                        <?= e(t('razon_social')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['razon_social']) ? 'is-invalid' : '' ?>"
                           name="razon_social"
                           value="<?= e($formData['razon_social'] ?? $clientEdit['razon_social'] ?? '') ?>"
                           placeholder="Empresa S.A."
                           required>
                    <?php if (isset($errors['razon_social'])): ?>
                    <div class="invalid-feedback"><?= e($errors['razon_social']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Contacto -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('contact')) ?></label>
                    <input type="text"
                           class="form-control"
                           name="contacto"
                           value="<?= e($formData['contacto'] ?? $clientEdit['contacto'] ?? '') ?>"
                           placeholder="Nombre del contacto">
                </div>

                <!-- Correo -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold"><?= e(t('email')) ?></label>
                    <input type="email"
                           class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>"
                           name="correo"
                           value="<?= e($formData['correo'] ?? $clientEdit['correo'] ?? '') ?>"
                           placeholder="correo@empresa.com">
                    <?php if (isset($errors['correo'])): ?>
                    <div class="invalid-feedback"><?= e($errors['correo']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Teléfono -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><?= e(t('phone')) ?></label>
                    <input type="text"
                           class="form-control"
                           name="telefono"
                           value="<?= e($formData['telefono'] ?? $clientEdit['telefono'] ?? '') ?>"
                           placeholder="+56 9 1234 5678">
                </div>

                <!-- Dirección -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold"><?= e(t('address')) ?></label>
                    <input type="text"
                           class="form-control"
                           name="direccion"
                           value="<?= e($formData['direccion'] ?? $clientEdit['direccion'] ?? '') ?>"
                           placeholder="Av. Principal 1234, Of. 5">
                </div>

                <!-- Ciudad -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><?= e(t('city')) ?></label>
                    <input type="text"
                           class="form-control"
                           name="ciudad"
                           value="<?= e($formData['ciudad'] ?? $clientEdit['ciudad'] ?? '') ?>"
                           placeholder="Ciudad">
                </div>

                <!-- País -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><?= e(t('country')) ?></label>
                    <input type="text"
                           class="form-control"
                           name="pais"
                           value="<?= e($formData['pais'] ?? $clientEdit['pais'] ?? '') ?>"
                           placeholder="Chile"
                           list="countries-list">
                    <datalist id="countries-list">
                        <option value="Chile">
                        <option value="Argentina">
                        <option value="Perú">
                        <option value="Colombia">
                        <option value="Bolivia">
                        <option value="Brasil">
                        <option value="Uruguay">
                        <option value="Paraguay">
                        <option value="Ecuador">
                        <option value="Venezuela">
                        <option value="México">
                        <option value="España">
                        <option value="China">
                        <option value="Estados Unidos">
                        <option value="Alemania">
                        <option value="Japón">
                        <option value="Corea del Sur">
                        <option value="Países Bajos">
                        <option value="Bélgica">
                        <option value="Italia">
                        <option value="Francia">
                        <option value="Reino Unido">
                        <option value="Singapur">
                        <option value="Hong Kong">
                    </datalist>
                </div>

                <!-- Estado -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><?= e(t('status')) ?></label>
                    <select class="form-select" name="estado">
                        <option value="activo"
                            <?= ($formData['estado'] ?? $clientEdit['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>
                            <?= e(t('active')) ?>
                        </option>
                        <option value="inactivo"
                            <?= ($formData['estado'] ?? $clientEdit['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>
                            <?= e(t('inactive')) ?>
                        </option>
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i><?= e(t('save')) ?>
                </button>
                <a href="/clients" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i><?= e(t('cancel')) ?>
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
