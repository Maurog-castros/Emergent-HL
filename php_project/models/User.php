<?php
/**
 * Modelo Usuario
 * LogiSystem
 */

class User
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM usuarios WHERE username = ? LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, apellido, correo, username, rol, estado, fecha_creacion
             FROM usuarios WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getAll(string $search = '', string $status = ''): array
    {
        $sql    = 'SELECT id, nombre, apellido, correo, username, rol, estado, fecha_creacion FROM usuarios WHERE 1=1';
        $params = [];
        $types  = '';

        if ($search !== '') {
            $sql   .= ' AND (nombre LIKE ? OR apellido LIKE ? OR correo LIKE ? OR username LIKE ?)';
            $like   = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
            $types  = 'ssss';
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

    public function countAll(): array
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(estado = 'activo')   AS activos,
                    SUM(estado = 'inactivo') AS inactivos
             FROM usuarios"
        );
        return $res->fetch_assoc() ?? ['total' => 0, 'activos' => 0, 'inactivos' => 0];
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre, apellido, correo, username, password, rol, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssss',
            $data['nombre'], $data['apellido'], $data['correo'],
            $data['username'], $data['password'], $data['rol'], $data['estado']
        );
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare(
                'UPDATE usuarios
                 SET nombre=?, apellido=?, correo=?, username=?, password=?, rol=?, estado=?
                 WHERE id=?'
            );
            $stmt->bind_param(
                'sssssssi',
                $data['nombre'], $data['apellido'], $data['correo'],
                $data['username'], $data['password'], $data['rol'], $data['estado'], $id
            );
        } else {
            $stmt = $this->db->prepare(
                'UPDATE usuarios
                 SET nombre=?, apellido=?, correo=?, username=?, rol=?, estado=?
                 WHERE id=?'
            );
            $stmt->bind_param(
                'ssssssi',
                $data['nombre'], $data['apellido'], $data['correo'],
                $data['username'], $data['rol'], $data['estado'], $id
            );
        }
        return $stmt->execute();
    }

    public function toggleStatus(int $id): ?string
    {
        $user = $this->findById($id);
        if (!$user) return null;

        $newStatus = $user['estado'] === 'activo' ? 'inactivo' : 'activo';
        $stmt = $this->db->prepare('UPDATE usuarios SET estado=? WHERE id=?');
        $stmt->bind_param('si', $newStatus, $id);
        $stmt->execute();
        return $newStatus;
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE correo=? AND id!=? LIMIT 1');
        $stmt->bind_param('si', $email, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function usernameExists(string $username, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE username=? AND id!=? LIMIT 1');
        $stmt->bind_param('si', $username, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
