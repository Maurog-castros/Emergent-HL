<?php
/**
 * Controlador de BL (Conocimientos de Embarque)
 * LogiSystem — Fase 2
 */

require_once __DIR__ . '/../models/BL.php';
require_once __DIR__ . '/../models/Embarque.php';

class BLController
{
    private BL       $model;
    private Embarque $embarqueModel;

    public function __construct()
    {
        $this->model         = new BL();
        $this->embarqueModel = new Embarque();
    }

    public function create(int $embarqueId): void
    {
        $embarque = $this->embarqueModel->findById($embarqueId);
        if (!$embarque) {
            setFlash('error', t('embarque_not_found'));
            redirect('/embarques');
        }
        $blEdit   = null;
        $formData = [];
        $errors   = [];

        $pageTitle    = t('new_bl') . ' — ' . e($embarque['numero_embarque']);
        $activeModule = 'embarques';
        require __DIR__ . '/../views/bl/form.php';
    }

    public function store(int $embarqueId): void
    {
        verifyCsrf();

        $data   = $this->collectFormData($embarqueId);
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->numExists($data['numero_bl'])) {
            $errors['numero_bl'] = t('bl_numero_exists');
        }

        if (!empty($errors)) {
            $embarque = $this->embarqueModel->findById($embarqueId);
            $blEdit   = null;
            $formData = $data;
            $pageTitle    = t('new_bl');
            $activeModule = 'embarques';
            require __DIR__ . '/../views/bl/form.php';
            return;
        }

        $this->model->create($data);
        setFlash('success', t('bl_created'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-bl');
    }

    public function edit(int $embarqueId, int $blId): void
    {
        $blEdit   = $this->model->findById($blId);
        $embarque = $this->embarqueModel->findById($embarqueId);

        if (!$blEdit || !$embarque) {
            setFlash('error', t('bl_not_found'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-bl');
        }

        $formData = [];
        $errors   = [];

        $pageTitle    = t('edit_bl') . ' — ' . e($embarque['numero_embarque']);
        $activeModule = 'embarques';
        require __DIR__ . '/../views/bl/form.php';
    }

    public function update(int $embarqueId, int $blId): void
    {
        verifyCsrf();

        $blEdit = $this->model->findById($blId);
        if (!$blEdit) {
            setFlash('error', t('bl_not_found'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-bl');
        }

        $data   = $this->collectFormData($embarqueId);
        $errors = $this->validate($data);

        if (empty($errors) && $this->model->numExists($data['numero_bl'], $blId)) {
            $errors['numero_bl'] = t('bl_numero_exists');
        }

        if (!empty($errors)) {
            $embarque = $this->embarqueModel->findById($embarqueId);
            $formData = $data;
            $pageTitle    = t('edit_bl');
            $activeModule = 'embarques';
            require __DIR__ . '/../views/bl/form.php';
            return;
        }

        $this->model->update($blId, $data);
        setFlash('success', t('bl_updated'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-bl');
    }

    public function delete(int $embarqueId, int $blId): void
    {
        $this->model->delete($blId);
        setFlash('success', t('bl_deleted'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-bl');
    }

    private function collectFormData(int $embarqueId): array
    {
        $nullIfEmpty = static fn(string $v) => $v !== '' ? $v : null;
        return [
            'embarque_id'      => $embarqueId,
            'numero_bl'        => sanitize($_POST['numero_bl']        ?? ''),
            'tipo_bl'          => sanitize($_POST['tipo_bl']          ?? 'house'),
            'shipper'          => sanitize($_POST['shipper']          ?? ''),
            'consignatario'    => sanitize($_POST['consignatario']    ?? ''),
            'notify_party'     => sanitize($_POST['notify_party']     ?? ''),
            'puerto_carga'     => sanitize($_POST['puerto_carga']     ?? ''),
            'puerto_descarga'  => sanitize($_POST['puerto_descarga']  ?? ''),
            'fecha_emision'    => $nullIfEmpty(sanitize($_POST['fecha_emision']   ?? '')),
            'fecha_vencimiento'=> $nullIfEmpty(sanitize($_POST['fecha_vencimiento']?? '')),
            'estado'           => sanitize($_POST['estado']           ?? 'borrador'),
            'observaciones'    => sanitize($_POST['observaciones']    ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['numero_bl'])) {
            $errors['numero_bl'] = t('field_required', ['field' => t('numero_bl')]);
        }
        return $errors;
    }
}
