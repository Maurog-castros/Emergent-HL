<?php
/**
 * Controlador de Contenedores
 * LogiSystem — Fase 2
 */

require_once __DIR__ . '/../models/Contenedor.php';

class ContenedorController
{
    private Contenedor $model;

    public function __construct()
    {
        $this->model = new Contenedor();
    }

    public function index(): void
    {
        $search      = sanitize($_GET['search'] ?? '');
        $estado      = sanitize($_GET['estado']  ?? '');
        $contenedores = $this->model->getAll($search, $estado);

        $pageTitle    = t('contenedor_list');
        $activeModule = 'contenedores';
        require __DIR__ . '/../views/contenedores/index.php';
    }

    public function create(): void
    {
        $contenedorEdit  = null;
        $formData        = [];
        $errors          = [];
        $embarques       = $this->model->getAllEmbarques();

        // Pre-asignar embarque desde URL si viene del show de embarque
        $defaultEmbarqueId = (int)($_GET['embarque_id'] ?? 0);

        $pageTitle    = t('new_contenedor');
        $activeModule = 'contenedores';
        require __DIR__ . '/../views/contenedores/form.php';
    }

    public function store(): void
    {
        verifyCsrf();
        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->numExists($data['numero_contenedor'])) {
            $errors['numero_contenedor'] = t('numero_contenedor_exists');
        }

        if (!empty($errors)) {
            $contenedorEdit     = null;
            $formData           = $data;
            $embarques          = $this->model->getAllEmbarques();
            $defaultEmbarqueId  = (int)($data['embarque_id'] ?? 0);
            $pageTitle          = t('new_contenedor');
            $activeModule       = 'contenedores';
            require __DIR__ . '/../views/contenedores/form.php';
            return;
        }

        $this->model->create($data);
        setFlash('success', t('contenedor_created'));

        // Redirigir al embarque si venía de ahí
        $embarqueId = (int)($data['embarque_id'] ?? 0);
        redirect($embarqueId ? '/embarques/show/' . $embarqueId . '?tab=tab-contenedores' : '/contenedores');
    }

    public function edit(int $id): void
    {
        $contenedorEdit = $this->model->findById($id);
        if (!$contenedorEdit) {
            setFlash('error', t('contenedor_not_found'));
            redirect('/contenedores');
        }

        $formData          = [];
        $errors            = [];
        $embarques         = $this->model->getAllEmbarques();
        $defaultEmbarqueId = (int)($contenedorEdit['embarque_id'] ?? 0);

        $pageTitle    = t('edit_contenedor');
        $activeModule = 'contenedores';
        require __DIR__ . '/../views/contenedores/form.php';
    }

    public function update(int $id): void
    {
        verifyCsrf();
        $contenedorEdit = $this->model->findById($id);
        if (!$contenedorEdit) {
            setFlash('error', t('contenedor_not_found'));
            redirect('/contenedores');
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->numExists($data['numero_contenedor'], $id)) {
            $errors['numero_contenedor'] = t('numero_contenedor_exists');
        }

        if (!empty($errors)) {
            $embarques         = $this->model->getAllEmbarques();
            $formData          = $data;
            $defaultEmbarqueId = (int)($data['embarque_id'] ?? 0);
            $pageTitle         = t('edit_contenedor');
            $activeModule      = 'contenedores';
            require __DIR__ . '/../views/contenedores/form.php';
            return;
        }

        $this->model->update($id, $data);
        setFlash('success', t('contenedor_updated'));

        $embarqueId = (int)($data['embarque_id'] ?? 0);
        redirect($embarqueId ? '/embarques/show/' . $embarqueId . '?tab=tab-contenedores' : '/contenedores');
    }

    public function toggle(int $id): void
    {
        $new = $this->model->toggleStatus($id);
        if ($new === null) {
            setFlash('error', t('contenedor_not_found'));
        } else {
            setFlash('success', t($new === 'disponible' ? 'activate' : 'deactivate'));
        }
        redirect('/contenedores');
    }

    private function collectFormData(): array
    {
        return [
            'numero_contenedor' => sanitize($_POST['numero_contenedor'] ?? ''),
            'embarque_id'       => sanitize($_POST['embarque_id']       ?? ''),
            'tipo'              => sanitize($_POST['tipo']              ?? '40DC'),
            'peso_bruto_kg'     => sanitize($_POST['peso_bruto_kg']    ?? ''),
            'sello'             => sanitize($_POST['sello']            ?? ''),
            'estado'            => sanitize($_POST['estado']           ?? 'disponible'),
            'notas'             => sanitize($_POST['notas']            ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['numero_contenedor'])) {
            $errors['numero_contenedor'] = t('field_required', ['field' => t('numero_contenedor')]);
        }
        return $errors;
    }
}
