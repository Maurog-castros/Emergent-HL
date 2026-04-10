<?php
/**
 * Modelo Contenedor
 * LogiSystem — Fase 2
 */

class Contenedor
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(string $search = '', string $estado = ''): array
    {
        $sql    = 'SELECT c.*, e.numero_embarque AS embarque_numero
                   FROM contenedores c
                   LEFT JOIN embarques e ON e.id = c.embarque_id
                   WHERE 1=1';
        $params = [];
        $types  = '';

        if ($search !== '') {
            $sql   .= ' AND (c.numero_contenedor LIKE ? OR e.numero_embarque LIKE ? OR c.sello LIKE ?)';
            $like   = '%' . $search . '%';
            $params = [$like, $like, $like];
            $types  = 'sss';
        }
        if ($estado !== '') {
            $sql     .= ' AND c.estado = ?';
            $params[] = $estado;
            $types   .= 's';
        }
        $sql .= ' ORDER BY c.fecha_creacion DESC';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByEmbarque(int $embarqueId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM contenedores WHERE embarque_id=? ORDER BY fecha_creacion'
        );
        $stmt->bind_param('i', $embarqueId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, e.numero_embarque AS embarque_numero
             FROM contenedores c
             LEFT JOIN embarques e ON e.id = c.embarque_id
             WHERE c.id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function countAll(): array
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(estado = 'disponible')  AS disponibles,
                    SUM(estado = 'en_transito') AS en_transito,
                    SUM(estado = 'en_uso')      AS en_uso
             FROM contenedores"
        );
        return $res->fetch_assoc() ?? ['total' => 0, 'disponibles' => 0, 'en_transito' => 0, 'en_uso' => 0];
    }

    public function getAllEmbarques(): array
    {
        $res = $this->db->query(
            "SELECT id, numero_embarque, estado FROM embarques
             WHERE estado NOT IN ('cancelado','entregado') ORDER BY numero_embarque"
        );
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): bool
    {
        // embarque_id and peso_bruto_kg can be NULL — use 's' type for nullable
        $embarqueId = $data['embarque_id'] !== '' ? $data['embarque_id'] : null;
        $peso       = $data['peso_bruto_kg'] !== '' ? $data['peso_bruto_kg'] : null;

        $stmt = $this->db->prepare(
            'INSERT INTO contenedores
             (numero_contenedor, embarque_id, tipo, peso_bruto_kg, sello, estado, notas)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssss',
            $data['numero_contenedor'], $embarqueId,
            $data['tipo'],             $peso,
            $data['sello'],            $data['estado'],
            $data['notas']
        );
        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $embarqueId = $data['embarque_id'] !== '' ? $data['embarque_id'] : null;
        $peso       = $data['peso_bruto_kg'] !== '' ? $data['peso_bruto_kg'] : null;

        $stmt = $this->db->prepare(
            'UPDATE contenedores
             SET numero_contenedor=?, embarque_id=?, tipo=?, peso_bruto_kg=?,
                 sello=?, estado=?, notas=?
             WHERE id=?'
        );
        $stmt->bind_param(
            'sssssss i',
            $data['numero_contenedor'], $embarqueId,
            $data['tipo'],             $peso,
            $data['sello'],            $data['estado'],
            $data['notas'],            $id
        );
        // Fix type string (no space allowed)
        $stmt = $this->db->prepare(
            'UPDATE contenedores
             SET numero_contenedor=?, embarque_id=?, tipo=?, peso_bruto_kg=?,
                 sello=?, estado=?, notas=?
             WHERE id=?'
        );
        $stmt->bind_param(
            'sssssssi',
            $data['numero_contenedor'], $embarqueId,
            $data['tipo'],             $peso,
            $data['sello'],            $data['estado'],
            $data['notas'],            $id
        );
        return $stmt->execute();
    }

    public function toggleStatus(int $id): ?string
    {
        $c = $this->findById($id);
        if (!$c) return null;
        $new = $c['estado'] === 'disponible' ? 'retirado' : 'disponible';
        $stmt = $this->db->prepare('UPDATE contenedores SET estado=? WHERE id=?');
        $stmt->bind_param('si', $new, $id);
        $stmt->execute();
        return $new;
    }

    public function numExists(string $num, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM contenedores WHERE numero_contenedor=? AND id!=? LIMIT 1'
        );
        $stmt->bind_param('si', $num, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
