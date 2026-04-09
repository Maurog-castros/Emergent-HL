<?php
/**
 * Modelo Cliente
 * LogiSystem
 */

class Client
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(string $search = '', string $status = ''): array
    {
        $sql    = 'SELECT * FROM clientes WHERE 1=1';
        $params = [];
        $types  = '';

        if ($search !== '') {
            $sql   .= ' AND (rut LIKE ? OR razon_social LIKE ? OR contacto LIKE ? OR correo LIKE ? OR ciudad LIKE ?)';
            $like   = '%' . $search . '%';
            $params = [$like, $like, $like, $like, $like];
            $types  = 'sssss';
        }
        if ($status !== '') {
            $sql     .= ' AND estado = ?';
            $params[] = $status;
            $types   .= 's';
        }
        $sql .= ' ORDER BY fecha_creacion DESC';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function countAll(): array
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(estado = 'activo')   AS activos,
                    SUM(estado = 'inactivo') AS inactivos
             FROM clientes"
        );
        return $res->fetch_assoc() ?? ['total' => 0, 'activos' => 0, 'inactivos' => 0];
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clientes (rut, razon_social, contacto, correo, telefono, direccion, ciudad, pais, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssssss',
            $data['rut'], $data['razon_social'], $data['contacto'],
            $data['correo'], $data['telefono'], $data['direccion'],
            $data['ciudad'], $data['pais'], $data['estado']
        );
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE clientes
             SET rut=?, razon_social=?, contacto=?, correo=?, telefono=?, direccion=?, ciudad=?, pais=?, estado=?
             WHERE id=?'
        );
        $stmt->bind_param(
            'sssssssssi',
            $data['rut'], $data['razon_social'], $data['contacto'],
            $data['correo'], $data['telefono'], $data['direccion'],
            $data['ciudad'], $data['pais'], $data['estado'], $id
        );
        return $stmt->execute();
    }

    public function toggleStatus(int $id): ?string
    {
        $client = $this->findById($id);
        if (!$client) return null;

        $newStatus = $client['estado'] === 'activo' ? 'inactivo' : 'activo';
        $stmt = $this->db->prepare('UPDATE clientes SET estado=? WHERE id=?');
        $stmt->bind_param('si', $newStatus, $id);
        $stmt->execute();
        return $newStatus;
    }

    public function rutExists(string $rut, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM clientes WHERE rut=? AND id!=? LIMIT 1');
        $stmt->bind_param('si', $rut, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
