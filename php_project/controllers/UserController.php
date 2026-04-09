<?php
/**
 * Controlador de Usuarios
 * LogiSystem
 */

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function index(): void
    {
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $users  = $this->model->getAll($search, $status);

        $pageTitle    = t('user_list');
        $activeModule = 'users';
        require __DIR__ . '/../views/users/index.php';
    }

    public function create(): void
    {
        $formData     = [];
        $errors       = [];
        $userEdit     = null;
        $pageTitle    = t('new_user');
        $activeModule = 'users';
        require __DIR__ . '/../views/users/form.php';
    }

    public function store(): void
    {
        verifyCsrf();

        $data   = $this->collectFormData();
        $errors = $this->validate($data, false);

        if (empty($errors)) {
            if ($this->model->emailExists($data['correo'])) {
                $errors['correo'] = t('email_exists');
            }
            if ($this->model->usernameExists($data['username'])) {
                $errors['username'] = t('username_exists');
            }
        }

        if (!empty($errors)) {
            $formData     = $data;
            $userEdit     = null;
            $pageTitle    = t('new_user');
            $activeModule = 'users';
            require __DIR__ . '/../views/users/form.php';
            return;
        }

        $data['password'] = hashPassword($data['password']);
        $this->model->create($data);

        setFlash('success', t('user_created'));
        redirect('/users');
    }

    public function edit(int $id): void
    {
        $userEdit = $this->model->findById($id);
        if (!$userEdit) {
            setFlash('error', t('user_not_found'));
            redirect('/users');
        }

        $formData     = [];
        $errors       = [];
        $pageTitle    = t('edit_user');
        $activeModule = 'users';
        require __DIR__ . '/../views/users/form.php';
    }

    public function update(int $id): void
    {
        verifyCsrf();

        $userEdit = $this->model->findById($id);
        if (!$userEdit) {
            setFlash('error', t('user_not_found'));
            redirect('/users');
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data, true);

        if (empty($errors)) {
            if ($this->model->emailExists($data['correo'], $id)) {
                $errors['correo'] = t('email_exists');
            }
            if ($this->model->usernameExists($data['username'], $id)) {
                $errors['username'] = t('username_exists');
            }
        }

        if (!empty($errors)) {
            $formData     = $data;
            $pageTitle    = t('edit_user');
            $activeModule = 'users';
            require __DIR__ . '/../views/users/form.php';
            return;
        }

        if (!empty($data['password'])) {
            $data['password'] = hashPassword($data['password']);
        }

        $this->model->update($id, $data);
        setFlash('success', t('user_updated'));
        redirect('/users');
    }

    public function toggle(int $id): void
    {
        if ($id === (getCurrentUser()['id'] ?? 0)) {
            setFlash('error', t('cannot_self_deactivate'));
            redirect('/users');
        }

        $newStatus = $this->model->toggleStatus($id);
        if ($newStatus === null) {
            setFlash('error', t('user_not_found'));
        } else {
            setFlash('success', t($newStatus === 'activo' ? 'user_activated' : 'user_deactivated'));
        }
        redirect('/users');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function collectFormData(): array
    {
        return [
            'nombre'   => sanitize($_POST['nombre']   ?? ''),
            'apellido' => sanitize($_POST['apellido'] ?? ''),
            'correo'   => sanitize($_POST['correo']   ?? ''),
            'username' => sanitize($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'rol'      => sanitize($_POST['rol']      ?? 'operador'),
            'estado'   => sanitize($_POST['estado']   ?? 'activo'),
        ];
    }

    private function validate(array $data, bool $isEdit): array
    {
        $errors = [];

        if (empty($data['nombre'])) {
            $errors['nombre'] = t('field_required', ['field' => t('first_name')]);
        }
        if (empty($data['apellido'])) {
            $errors['apellido'] = t('field_required', ['field' => t('last_name')]);
        }
        if (empty($data['correo'])) {
            $errors['correo'] = t('field_required', ['field' => t('email')]);
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = t('field_invalid_email');
        }
        if (empty($data['username'])) {
            $errors['username'] = t('field_required', ['field' => t('username')]);
        }
        if (!$isEdit && empty($data['password'])) {
            $errors['password'] = t('field_required', ['field' => t('password')]);
        }
        if (!empty($data['password']) && strlen($data['password']) < 8) {
            $errors['password'] = t('password_min');
        }
        if (!in_array($data['rol'], ['admin', 'operador'], true)) {
            $errors['rol'] = t('field_required', ['field' => t('role')]);
        }

        return $errors;
    }
}
