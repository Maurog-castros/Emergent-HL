<?php
/**
 * Modelo BL (Conocimiento de Embarque)
 * LogiSystem — Fase 2
 */

class BL
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByEmbarque(int $embarqueId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM bl_embarques WHERE embarque_id=? ORDER BY fecha_creacion'
        );
        $stmt->bind_param('i', $embarqueId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bl_embarques WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bl_embarques
             (embarque_id, numero_bl, tipo_bl, shipper, consignatario,
              notify_party, puerto_carga, puerto_descarga,
              fecha_emision, fecha_vencimiento, estado, observaciones)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'isssssssssss',
            $data['embarque_id'], $data['numero_bl'],   $data['tipo_bl'],
            $data['shipper'],     $data['consignatario'],$data['notify_party'],
            $data['puerto_carga'],$data['puerto_descarga'],
            $data['fecha_emision'],$data['fecha_vencimiento'],
            $data['estado'],      $data['observaciones']
        );
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE bl_embarques
             SET numero_bl=?, tipo_bl=?, shipper=?, consignatario=?,
                 notify_party=?, puerto_carga=?, puerto_descarga=?,
                 fecha_emision=?, fecha_vencimiento=?, estado=?, observaciones=?
             WHERE id=?'
        );
        $stmt->bind_param(
            'ssssssssssi',
            $data['numero_bl'],   $data['tipo_bl'],     $data['shipper'],
            $data['consignatario'],$data['notify_party'],
            $data['puerto_carga'],$data['puerto_descarga'],
            $data['fecha_emision'],$data['fecha_vencimiento'],
            $data['estado'],      $data['observaciones'], $id
        );
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM bl_embarques WHERE id=?');
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function numExists(string $num, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM bl_embarques WHERE numero_bl=? AND id!=? LIMIT 1'
        );
        $stmt->bind_param('si', $num, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
