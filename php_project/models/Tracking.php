<?php
/**
 * Modelo Tracking Eventos
 * LogiSystem — Fase 2
 */

class Tracking
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByEmbarque(int $embarqueId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM tracking_eventos
             WHERE embarque_id = ?
             ORDER BY fecha_evento ASC'
        );
        $stmt->bind_param('i', $embarqueId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tracking_eventos WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tracking_eventos
             (embarque_id, fecha_evento, tipo_evento, ubicacion, descripcion, created_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'issssi',
            $data['embarque_id'],  $data['fecha_evento'],
            $data['tipo_evento'],  $data['ubicacion'],
            $data['descripcion'],  $data['created_by']
        );
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tracking_eventos WHERE id=?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
