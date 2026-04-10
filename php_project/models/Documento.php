<?php
/**
 * Modelo Documentos de Embarque
 * LogiSystem — Fase 2
 */

class Documento
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByEmbarque(int $embarqueId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM documentos_embarque
             WHERE embarque_id = ?
             ORDER BY fecha_subida DESC'
        );
        $stmt->bind_param('i', $embarqueId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM documentos_embarque WHERE id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO documentos_embarque
             (embarque_id, nombre, tipo_documento, archivo_nombre, archivo_original,
              archivo_mime, tamano_bytes, descripcion, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'issssssii',
            $data['embarque_id'],      $data['nombre'],
            $data['tipo_documento'],   $data['archivo_nombre'],
            $data['archivo_original'], $data['archivo_mime'],
            $data['tamano_bytes'],     $data['descripcion'],
            $data['created_by']
        );
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM documentos_embarque WHERE id=?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /**
     * Retorna la ruta absoluta del archivo del documento
     */
    public static function filePath(array $doc): string
    {
        return __DIR__ . '/../uploads/documentos/' . $doc['embarque_id'] . '/' . $doc['archivo_nombre'];
    }
}
