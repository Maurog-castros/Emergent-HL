<?php
/**
 * Controlador de Clientes
 * LogiSystem
 */

require_once __DIR__ . '/../models/Client.php';

class ClientController
{
    private Client $model;

    public function __construct()
    {
        $this->model = new Client();
    }

    public function index(): void
    {
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $clients = $this->model->getAll($search, $status);

        $pageTitle    = t('client_list');
        $activeModule = 'clients';
        require __DIR__ . '/../views/clients/index.php';
    }

    public function create(): void
    {
        $formData     = [];
        $errors       = [];
        $clientEdit   = null;
        $pageTitle    = t('new_client');
        $activeModule = 'clients';
        require __DIR__ . '/../views/clients/form.php';
    }

    public function store(): void
    {
        verifyCsrf();

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->rutExists($data['rut'])) {
            $errors['rut'] = t('rut_exists');
        }

        if (!empty($errors)) {
            $formData     = $data;
            $clientEdit   = null;
            $pageTitle    = t('new_client');
            $activeModule = 'clients';
            require __DIR__ . '/../views/clients/form.php';
            return;
        }

        $this->model->create($data);
        setFlash('success', t('client_created'));
        redirect('/clients');
    }

    public function edit(int $id): void
    {
        $clientEdit = $this->model->findById($id);
        if (!$clientEdit) {
            setFlash('error', t('client_not_found'));
            redirect('/clients');
        }

        $formData     = [];
        $errors       = [];
        $pageTitle    = t('edit_client');
        $activeModule = 'clients';
        require __DIR__ . '/../views/clients/form.php';
    }

    public function update(int $id): void
    {
        verifyCsrf();

        $clientEdit = $this->model->findById($id);
        if (!$clientEdit) {
            setFlash('error', t('client_not_found'));
            redirect('/clients');
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->rutExists($data['rut'], $id)) {
            $errors['rut'] = t('rut_exists');
        }

        if (!empty($errors)) {
            $formData     = $data;
            $pageTitle    = t('edit_client');
            $activeModule = 'clients';
            require __DIR__ . '/../views/clients/form.php';
            return;
        }

        $this->model->update($id, $data);
        setFlash('success', t('client_updated'));
        redirect('/clients');
    }

    public function toggle(int $id): void
    {
        $newStatus = $this->model->toggleStatus($id);
        if ($newStatus === null) {
            setFlash('error', t('client_not_found'));
        } else {
            setFlash('success', t($newStatus === 'activo' ? 'client_activated' : 'client_deactivated'));
        }
        redirect('/clients');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function collectFormData(): array
    {
        return [
            'rut'          => sanitize($_POST['rut']          ?? ''),
            'razon_social' => sanitize($_POST['razon_social'] ?? ''),
            'contacto'     => sanitize($_POST['contacto']     ?? ''),
            'correo'       => sanitize($_POST['correo']       ?? ''),
            'telefono'     => sanitize($_POST['telefono']     ?? ''),
            'direccion'    => sanitize($_POST['direccion']    ?? ''),
            'ciudad'       => sanitize($_POST['ciudad']       ?? ''),
            'pais'         => sanitize($_POST['pais']         ?? ''),
            'estado'       => sanitize($_POST['estado']       ?? 'activo'),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['rut'])) {
            $errors['rut'] = t('field_required', ['field' => t('rut')]);
        }
        if (empty($data['razon_social'])) {
            $errors['razon_social'] = t('field_required', ['field' => t('razon_social')]);
        }
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = t('field_invalid_email');
        }

        return $errors;
    }
}
