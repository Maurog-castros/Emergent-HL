<?php
/**
 * Controlador de Embarques, Tracking y Documentos
 * LogiSystem — Fase 2
 */

require_once __DIR__ . '/../models/Embarque.php';
require_once __DIR__ . '/../models/Tracking.php';
require_once __DIR__ . '/../models/Documento.php';

class EmbarqueController
{
    private Embarque  $model;
    private Tracking  $tracking;
    private Documento $documento;

    public function __construct()
    {
        $this->model     = new Embarque();
        $this->tracking  = new Tracking();
        $this->documento = new Documento();
    }

    // ── Lista ─────────────────────────────────────────────────
    public function index(): void
    {
        $search    = sanitize($_GET['search'] ?? '');
        $estado    = sanitize($_GET['estado']  ?? '');
        $tipo      = sanitize($_GET['tipo']    ?? '');
        $embarques = $this->model->getAll($search, $estado, $tipo);

        $pageTitle    = t('embarque_list');
        $activeModule = 'embarques';
        require __DIR__ . '/../views/embarques/index.php';
    }

    // ── Crear ─────────────────────────────────────────────────
    public function create(): void
    {
        $formData     = [];
        $errors       = [];
        $embarqueEdit = null;
        $clients      = $this->model->getAllClients();
        $pageTitle    = t('new_embarque');
        $activeModule = 'embarques';
        require __DIR__ . '/../views/embarques/form.php';
    }

    public function store(): void
    {
        verifyCsrf();
        $data   = $this->collectFormData();
        $errors = $this->validate($data, false);

        if (empty($errors) && $this->model->numExists($data['numero_embarque'])) {
            $errors['numero_embarque'] = t('numero_embarque_exists');
        }

        if (!empty($errors)) {
            $formData     = $data;
            $embarqueEdit = null;
            $clients      = $this->model->getAllClients();
            $pageTitle    = t('new_embarque');
            $activeModule = 'embarques';
            require __DIR__ . '/../views/embarques/form.php';
            return;
        }

        $data['created_by'] = getCurrentUser()['id'];
        $this->model->create($data);

        setFlash('success', t('embarque_created'));
        redirect('/embarques');
    }

    // ── Detalle ───────────────────────────────────────────────
    public function show(int $id): void
    {
        $embarque = $this->model->findById($id);
        if (!$embarque) {
            setFlash('error', t('embarque_not_found'));
            redirect('/embarques');
        }

        require_once __DIR__ . '/../models/BL.php';
        require_once __DIR__ . '/../models/Contenedor.php';

        $blModel        = new BL();
        $contenedorModel = new Contenedor();

        $bls         = $blModel->getByEmbarque($id);
        $contenedores = $contenedorModel->getByEmbarque($id);
        $eventos     = $this->tracking->getByEmbarque($id);
        $documentos  = $this->documento->getByEmbarque($id);

        $pageTitle    = $embarque['numero_embarque'];
        $activeModule = 'embarques';
        require __DIR__ . '/../views/embarques/show.php';
    }

    // ── Editar ────────────────────────────────────────────────
    public function edit(int $id): void
    {
        $embarqueEdit = $this->model->findById($id);
        if (!$embarqueEdit) {
            setFlash('error', t('embarque_not_found'));
            redirect('/embarques');
        }

        $formData     = [];
        $errors       = [];
        $clients      = $this->model->getAllClients();
        $pageTitle    = t('edit_embarque');
        $activeModule = 'embarques';
        require __DIR__ . '/../views/embarques/form.php';
    }

    public function update(int $id): void
    {
        verifyCsrf();
        $embarqueEdit = $this->model->findById($id);
        if (!$embarqueEdit) {
            setFlash('error', t('embarque_not_found'));
            redirect('/embarques');
        }

        $data   = $this->collectFormData();
        $errors = $this->validate($data, true);

        if (empty($errors) && $this->model->numExists($data['numero_embarque'], $id)) {
            $errors['numero_embarque'] = t('numero_embarque_exists');
        }

        if (!empty($errors)) {
            $formData     = $data;
            $clients      = $this->model->getAllClients();
            $pageTitle    = t('edit_embarque');
            $activeModule = 'embarques';
            require __DIR__ . '/../views/embarques/form.php';
            return;
        }

        $this->model->update($id, $data);
        setFlash('success', t('embarque_updated'));
        redirect('/embarques/show/' . $id);
    }

    // ── Tracking ──────────────────────────────────────────────
    public function addTracking(int $embarqueId): void
    {
        verifyCsrf();

        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $fechaEvento = sanitize($_POST['fecha_evento'] ?? '');
        $tipoEvento  = sanitize($_POST['tipo_evento']  ?? 'transito');
        $ubicacion   = sanitize($_POST['ubicacion']    ?? '');

        if (empty($descripcion) || empty($fechaEvento)) {
            setFlash('error', t('field_required', ['field' => t('descripcion_evento')]));
        } else {
            $this->tracking->create([
                'embarque_id'  => $embarqueId,
                'fecha_evento' => $fechaEvento,
                'tipo_evento'  => $tipoEvento,
                'ubicacion'    => $ubicacion,
                'descripcion'  => $descripcion,
                'created_by'   => getCurrentUser()['id'],
            ]);
            setFlash('success', t('tracking_event_created'));
        }
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-tracking');
    }

    public function deleteTracking(int $embarqueId, int $eventId): void
    {
        $this->tracking->delete($eventId);
        setFlash('success', t('tracking_event_deleted'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-tracking');
    }

    // ── Documentos ────────────────────────────────────────────
    public function uploadDoc(int $embarqueId): void
    {
        verifyCsrf();

        $nombre       = sanitize($_POST['nombre']        ?? '');
        $tipoDoc      = sanitize($_POST['tipo_documento'] ?? 'otro');
        $descripcion  = sanitize($_POST['descripcion']   ?? '');

        if (empty($nombre)) {
            setFlash('error', t('field_required', ['field' => t('doc_name')]));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        if (empty($_FILES['archivo']['name'])) {
            setFlash('error', t('no_file_selected'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        $file      = $_FILES['archivo'];
        $maxSize   = 10 * 1024 * 1024; // 10 MB
        $allowed   = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','zip','txt','csv'];
        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > $maxSize) {
            setFlash('error', t('file_too_large'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }
        if (!in_array($ext, $allowed, true)) {
            setFlash('error', t('file_invalid_type'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        // Crear directorio
        $uploadDir = __DIR__ . '/../uploads/documentos/' . $embarqueId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uniqueName = bin2hex(random_bytes(10)) . '.' . $ext;
        $destPath   = $uploadDir . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', 'Error al subir el archivo.');
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        $this->documento->create([
            'embarque_id'      => $embarqueId,
            'nombre'           => $nombre,
            'tipo_documento'   => $tipoDoc,
            'archivo_nombre'   => $uniqueName,
            'archivo_original' => $file['name'],
            'archivo_mime'     => $file['type'],
            'tamano_bytes'     => $file['size'],
            'descripcion'      => $descripcion,
            'created_by'       => getCurrentUser()['id'],
        ]);

        setFlash('success', t('doc_uploaded'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
    }

    public function deleteDoc(int $embarqueId, int $docId): void
    {
        $doc = $this->documento->findById($docId);
        if ($doc) {
            $filePath = Documento::filePath($doc);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->documento->delete($docId);
        }
        setFlash('success', t('doc_deleted'));
        redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
    }

    public function downloadDoc(int $embarqueId, int $docId): void
    {
        $doc = $this->documento->findById($docId);
        if (!$doc || (int)$doc['embarque_id'] !== $embarqueId) {
            setFlash('error', t('doc_not_found'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        $filePath = Documento::filePath($doc);
        if (!file_exists($filePath)) {
            setFlash('error', t('doc_not_found'));
            redirect('/embarques/show/' . $embarqueId . '?tab=tab-docs');
        }

        header('Content-Type: ' . ($doc['archivo_mime'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $doc['archivo_original'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($filePath);
        exit;
    }

    // ── Helpers ───────────────────────────────────────────────
    private function collectFormData(): array
    {
        $nullIfEmpty = static fn(string $v) => $v !== '' ? $v : null;

        return [
            'numero_embarque'       => sanitize($_POST['numero_embarque']       ?? ''),
            'cliente_id'            => (int) ($_POST['cliente_id']              ?? 0),
            'tipo'                  => sanitize($_POST['tipo']                  ?? 'maritimo'),
            'estado'                => sanitize($_POST['estado']                ?? 'borrador'),
            'origen_pais'           => sanitize($_POST['origen_pais']           ?? ''),
            'origen_ciudad'         => sanitize($_POST['origen_ciudad']         ?? ''),
            'destino_pais'          => sanitize($_POST['destino_pais']          ?? ''),
            'destino_ciudad'        => sanitize($_POST['destino_ciudad']        ?? ''),
            'fecha_embarque'        => $nullIfEmpty(sanitize($_POST['fecha_embarque']        ?? '')),
            'fecha_llegada_estimada'=> $nullIfEmpty(sanitize($_POST['fecha_llegada_estimada']?? '')),
            'fecha_llegada_real'    => $nullIfEmpty(sanitize($_POST['fecha_llegada_real']    ?? '')),
            'incoterm'              => sanitize($_POST['incoterm']              ?? ''),
            'descripcion_carga'     => sanitize($_POST['descripcion_carga']    ?? ''),
            'notas'                 => sanitize($_POST['notas']                ?? ''),
            'created_by'            => getCurrentUser()['id'],
        ];
    }

    private function validate(array $data, bool $isEdit): array
    {
        $errors = [];
        if (empty($data['numero_embarque'])) {
            $errors['numero_embarque'] = t('field_required', ['field' => t('numero_embarque')]);
        }
        if (empty($data['cliente_id'])) {
            $errors['cliente_id'] = t('field_required', ['field' => t('clients')]);
        }
        return $errors;
    }
}
