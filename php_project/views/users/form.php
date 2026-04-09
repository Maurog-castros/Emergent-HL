<?php
/**
 * Vista: Formulario de Usuario (Crear / Editar)
 * Variables: $userEdit (null=crear, array=editar), $formData, $errors
 */
$isEdit = isset($userEdit) && $userEdit !== null;
require __DIR__ . '/../layout/header.php';
?>

<!-- Toolbar de vuelta -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/users" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i><?= e(t('back')) ?>
    </a>
    <h4 class="mb-0"><?= e($pageTitle) ?></h4>
</div>

<div class="logi-card">
    <div class="logi-card-body">
        <form method="POST"
              action="<?= $isEdit ? '/users/edit/' . (int)$userEdit['id'] : '/users/create' ?>"
              novalidate
              id="userForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="row g-3">
                <!-- Nombre -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <?= e(t('first_name')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>"
                           name="nombre"
                           value="<?= e($formData['nombre'] ?? $userEdit['nombre'] ?? '') ?>"
                           required>
                    <?php if (isset($errors['nombre'])): ?>
                    <div class="invalid-feedback"><?= e($errors['nombre']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Apellido -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <?= e(t('last_name')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['apellido']) ? 'is-invalid' : '' ?>"
                           name="apellido"
                           value="<?= e($formData['apellido'] ?? $userEdit['apellido'] ?? '') ?>"
                           required>
                    <?php if (isset($errors['apellido'])): ?>
                    <div class="invalid-feedback"><?= e($errors['apellido']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Correo -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <?= e(t('email')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="email"
                           class="form-control <?= isset($errors['correo']) ? 'is-invalid' : '' ?>"
                           name="correo"
                           value="<?= e($formData['correo'] ?? $userEdit['correo'] ?? '') ?>"
                           required>
                    <?php if (isset($errors['correo'])): ?>
                    <div class="invalid-feedback"><?= e($errors['correo']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Username -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <?= e(t('username')) ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                           name="username"
                           value="<?= e($formData['username'] ?? $userEdit['username'] ?? '') ?>"
                           required
                           autocomplete="off">
                    <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?= e($errors['username']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Contraseña -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <?= e(t('password')) ?>
                        <?php if ($isEdit): ?>
                        <span class="text-muted fw-normal small"> — <?= e(t('leave_blank_pwd')) ?></span>
                        <?php else: ?>
                        <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               name="password"
                               <?= !$isEdit ? 'required' : '' ?>
                               autocomplete="new-password"
                               minlength="8">
                        <button type="button" class="btn btn-outline-secondary toggle-pwd" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-text">Mínimo 8 caracteres.</div>
                </div>

                <!-- Rol -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <?= e(t('role')) ?> <span class="text-danger">*</span>
                    </label>
                    <select class="form-select <?= isset($errors['rol']) ? 'is-invalid' : '' ?>"
                            name="rol" required>
                        <option value="admin"
                            <?= ($formData['rol'] ?? $userEdit['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            <?= e(t('admin_role')) ?>
                        </option>
                        <option value="operador"
                            <?= ($formData['rol'] ?? $userEdit['rol'] ?? 'operador') === 'operador' ? 'selected' : '' ?>>
                            <?= e(t('operator_role')) ?>
                        </option>
                    </select>
                    <?php if (isset($errors['rol'])): ?>
                    <div class="invalid-feedback"><?= e($errors['rol']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Estado -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><?= e(t('status')) ?></label>
                    <select class="form-select" name="estado">
                        <option value="activo"
                            <?= ($formData['estado'] ?? $userEdit['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>
                            <?= e(t('active')) ?>
                        </option>
                        <option value="inactivo"
                            <?= ($formData['estado'] ?? $userEdit['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>
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
                <a href="/users" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i><?= e(t('cancel')) ?>
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
