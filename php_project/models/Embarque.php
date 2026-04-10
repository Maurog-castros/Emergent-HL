<?php
/**
 * Modelo Embarque
 * LogiSystem — Fase 2
 */

class Embarque
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(string $search = '', string $estado = '', string $tipo = ''): array
    {
        $sql    = 'SELECT e.*, c.razon_social AS cliente_nombre
                   FROM embarques e
                   LEFT JOIN clientes c ON c.id = e.cliente_id
                   WHERE 1=1';
        $params = [];
        $types  = '';

        if ($search !== '') {
            $sql   .= ' AND (e.numero_embarque LIKE ? OR c.razon_social LIKE ? OR e.origen_ciudad LIKE ? OR e.destino_ciudad LIKE ?)';
            $like   = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
            $types  = 'ssss';
        }
        if ($estado !== '') {
            $sql     .= ' AND e.estado = ?';
            $params[] = $estado;
            $types   .= 's';
        }
        if ($tipo !== '') {
            $sql     .= ' AND e.tipo = ?';
            $params[] = $tipo;
            $types   .= 's';
        }
        $sql .= ' ORDER BY e.fecha_creacion DESC';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*, c.razon_social AS cliente_nombre
             FROM embarques e
             LEFT JOIN clientes c ON c.id = e.cliente_id
             WHERE e.id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function countAll(): array
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(estado = 'en_transito')                    AS en_transito,
                    SUM(estado = 'entregado')                      AS entregados,
                    SUM(estado NOT IN ('entregado','cancelado'))   AS activos
             FROM embarques"
        );
        return $res->fetch_assoc() ?? ['total' => 0, 'en_transito' => 0, 'entregados' => 0, 'activos' => 0];
    }

    public function getAllClients(): array
    {
        $res = $this->db->query(
            "SELECT id, razon_social FROM clientes WHERE estado='activo' ORDER BY razon_social"
        );
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO embarques
             (numero_embarque, cliente_id, tipo, estado,
              origen_pais, origen_ciudad, destino_pais, destino_ciudad,
              fecha_embarque, fecha_llegada_estimada, fecha_llegada_real,
              incoterm, descripcion_carga, notas, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sissssssssssssi',
            $data['numero_embarque'], $data['cliente_id'],
            $data['tipo'],            $data['estado'],
            $data['origen_pais'],     $data['origen_ciudad'],
            $data['destino_pais'],    $data['destino_ciudad'],
            $data['fecha_embarque'],  $data['fecha_llegada_estimada'],
            $data['fecha_llegada_real'],
            $data['incoterm'],        $data['descripcion_carga'],
            $data['notas'],           $data['created_by']
        );
        $stmt->execute();
        return (int) $this->db->insert_id;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE embarques
             SET numero_embarque=?, cliente_id=?, tipo=?, estado=?,
                 origen_pais=?, origen_ciudad=?, destino_pais=?, destino_ciudad=?,
                 fecha_embarque=?, fecha_llegada_estimada=?, fecha_llegada_real=?,
                 incoterm=?, descripcion_carga=?, notas=?
             WHERE id=?'
        );
        $stmt->bind_param(
            'sissssssssssssi',
            $data['numero_embarque'], $data['cliente_id'],
            $data['tipo'],            $data['estado'],
            $data['origen_pais'],     $data['origen_ciudad'],
            $data['destino_pais'],    $data['destino_ciudad'],
            $data['fecha_embarque'],  $data['fecha_llegada_estimada'],
            $data['fecha_llegada_real'],
            $data['incoterm'],        $data['descripcion_carga'],
            $data['notas'],           $id
        );
        return $stmt->execute();
    }

    public function numExists(string $num, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM embarques WHERE numero_embarque=? AND id!=? LIMIT 1'
        );
        $stmt->bind_param('si', $num, $excludeId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
