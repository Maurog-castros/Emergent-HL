<?php
/**
 * ============================================================
 * LogiSystem — Script de Instalación Inicial
 * ============================================================
 *
 * INSTRUCCIONES:
 * 1. Configure las credenciales en config/database.php
 * 2. Acceda a este archivo en su navegador:
 *    http://localhost/logistics/setup.php?token=logistics_setup_2024
 * 3. Siga las instrucciones en pantalla
 * 4. ¡IMPORTANTE! Elimine este archivo después de la instalación
 *
 * CREDENCIALES GENERADAS:
 *   admin         / Admin@123
 *   juan.perez    / Operador@123
 *   maria.gonzalez/ Operador@123
 * ============================================================
 */

// Protección básica por token
$validToken = 'logistics_setup_2024';
if (($_GET['token'] ?? '') !== $validToken) {
    http_response_code(403);
    die('
    <html><head><style>
        body{font-family:Arial,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f1f5f9;}
        .box{background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:480px;text-align:center;}
        code{background:#f1f5f9;padding:2px 6px;border-radius:4px;}
    </style></head><body>
    <div class="box">
        <h2 style="color:#ef4444">403 — Acceso Denegado</h2>
        <p>Acceda con el token correcto:</p>
        <p><code>setup.php?token=logistics_setup_2024</code></p>
    </div></body></html>');
}

require_once __DIR__ . '/config/database.php';

$db       = getDB();
$messages = [];
$errors   = [];

// ── Crear tablas ──────────────────────────────────────────────────────────────
$tables = [
    'usuarios' => "
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id`                  INT UNSIGNED      NOT NULL AUTO_INCREMENT,
            `nombre`              VARCHAR(100)      NOT NULL,
            `apellido`            VARCHAR(100)      NOT NULL,
            `correo`              VARCHAR(191)      NOT NULL,
            `username`            VARCHAR(80)       NOT NULL,
            `password`            VARCHAR(255)      NOT NULL,
            `rol`                 ENUM('admin','operador') NOT NULL DEFAULT 'operador',
            `estado`              ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
            `fecha_creacion`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_correo`   (`correo`),
            UNIQUE KEY `uq_username` (`username`),
            KEY `idx_estado` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'clientes' => "
        CREATE TABLE IF NOT EXISTS `clientes` (
            `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `rut`                 VARCHAR(30)  NOT NULL,
            `razon_social`        VARCHAR(200) NOT NULL,
            `contacto`            VARCHAR(150) DEFAULT NULL,
            `correo`              VARCHAR(191) DEFAULT NULL,
            `telefono`            VARCHAR(50)  DEFAULT NULL,
            `direccion`           VARCHAR(300) DEFAULT NULL,
            `ciudad`              VARCHAR(100) DEFAULT NULL,
            `pais`                VARCHAR(100) DEFAULT NULL,
            `estado`              ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
            `fecha_creacion`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_rut` (`rut`),
            KEY `idx_estado` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    'audit_log' => "
        CREATE TABLE IF NOT EXISTS `audit_log` (
            `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`     INT UNSIGNED DEFAULT NULL,
            `accion`      VARCHAR(100) NOT NULL,
            `tabla`       VARCHAR(60)  DEFAULT NULL,
            `registro_id` INT UNSIGNED DEFAULT NULL,
            `datos`       JSON         DEFAULT NULL,
            `ip`          VARCHAR(45)  DEFAULT NULL,
            `creado_en`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
];

foreach ($tables as $name => $sql) {
    if ($db->query(trim($sql))) {
        $messages[] = ['ok', "Tabla <strong>$name</strong> verificada / creada correctamente."];
    } else {
        $errors[] = "Error creando tabla '$name': " . $db->error;
    }
}

// ── Datos semilla: Usuarios ───────────────────────────────────────────────────
$users = [
    ['System',  'Administrator', 'admin@logistics.com',        'admin',          'Admin@123',     'admin'],
    ['Juan',    'Pérez',         'juan.perez@logistics.com',    'juan.perez',     'Operador@123',  'operador'],
    ['María',   'González',      'maria.gonzalez@logistics.com','maria.gonzalez', 'Operador@123',  'operador'],
];

foreach ($users as $u) {
    $hash = password_hash($u[4], PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare(
        "INSERT IGNORE INTO usuarios (nombre, apellido, correo, username, password, rol, estado)
         VALUES (?, ?, ?, ?, ?, ?, 'activo')"
    );
    $stmt->bind_param('ssssss', $u[0], $u[1], $u[2], $u[3], $hash, $u[5]);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $messages[] = ['ok', "Usuario <strong>{$u[3]}</strong> creado (contraseña: <code>{$u[4]}</code>)."];
    } else {
        $messages[] = ['warn', "Usuario <strong>{$u[3]}</strong> ya existe — no se modificó."];
    }
}

// ── Datos semilla: Clientes ───────────────────────────────────────────────────
$clients = [
    ['76.123.456-7', 'Importaciones del Pacífico S.A.',   'Carlos Mendoza',    'carlos@impacpacifico.cl',    '+56 9 8765 4321', 'Av. Portales 1234',     'Valparaíso',    'Chile'],
    ['77.234.567-8', 'Trans-Asia Cargo Ltd.',              'Li Wei',            'li.wei@transasiacargo.com',  '+86 21 5555 6666','888 Zhongshan Rd',       'Shanghai',      'China'],
    ['78.345.678-9', 'Logística Andina SpA',               'Patricia Ríos',     'prios@logandina.cl',         '+56 2 2345 6789', 'Los Carrera 456',        'Santiago',      'Chile'],
    ['B-12345678',   'Mediterráneo Shipping SL',           'Pedro García',      'pedro@medshipping.es',       '+34 91 234 5678', 'Calle Alcalá 789',       'Madrid',        'España'],
    ['90-1234567',   'Pacific Rim Freight Inc.',           'Sarah Johnson',     'sjohnson@pacrimfreight.com', '+1 310 555 0123', '2500 Ocean Ave',          'Los Angeles',   'USA'],
    ['79.456.789-0', 'Navieras Austral Ltda.',             'Rodrigo Vargas',    'rvargas@naviaustral.cl',     '+56 65 234 5678', 'Av. Las Araucarias 321', 'Puerto Montt',  'Chile'],
    ['C-87654321',   'Euro Container Services GmbH',       'Klaus Mueller',     'k.mueller@eurocontainer.de', '+49 40 987 6543', 'Hafenstraße 45',          'Hamburg',       'Alemania'],
    ['80.567.890-1', 'Comercial Marítima del Norte S.A.',  'Ana Fuentes',       'afuentes@comarnorte.cl',     '+56 55 345 6789', 'Av. Balmaceda 1567',     'Antofagasta',   'Chile'],
    ['AB-9876543',   'Asia Pacific Trading Co.',           'Tanaka Hiroshi',    'tanaka@aptrading.jp',        '+81 6 6555 7777', '1-2-3 Namba',             'Osaka',         'Japón'],
    ['81.678.901-2', 'Freight Solutions Internacional',    'Diego Castillo',    'dcastillo@freightsi.com',    '+56 32 456 7890', 'Av. Argentina 890',       'Valparaíso',    'Chile'],
];

foreach ($clients as $c) {
    $stmt = $db->prepare(
        "INSERT IGNORE INTO clientes (rut, razon_social, contacto, correo, telefono, direccion, ciudad, pais, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')"
    );
    $stmt->bind_param('ssssssss', $c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $c[6], $c[7]);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $messages[] = ['ok', "Cliente <strong>" . htmlspecialchars($c[1]) . "</strong> creado."];
    } else {
        $messages[] = ['warn', "Cliente <strong>" . htmlspecialchars($c[1]) . "</strong> ya existe."];
    }
}

$hasErrors = !empty($errors);
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogiSystem — Instalación</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body   { font-family: 'Inter', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 24px; }
        h1     { color: #0d9488; font-size: 1.6rem; margin: 0 0 24px; }
        h2     { font-size: 1rem; margin: 0 0 12px; color: #1e293b; }
        .wrap  { max-width: 720px; margin: 0 auto; }
        .card  { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
        .msg   { font-size: .875rem; padding: 5px 0; border-bottom: 1px solid #f1f5f9; }
        .ok::before   { content: '✅ '; }
        .warn::before { content: '⚠️ '; }
        .err::before  { content: '❌ '; color: #ef4444; font-weight: bold; }
        table  { width: 100%; border-collapse: collapse; font-size: .875rem; }
        th     { background: #f8fafc; padding: 9px 12px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
        td     { padding: 9px 12px; border-bottom: 1px solid #e2e8f0; }
        code   { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .warn-box { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 14px 16px; margin-top: 16px; font-size: .875rem; }
        .success-box { background: #f0fdfa; border: 1px solid #0d9488; border-radius: 8px; padding: 14px 16px; margin-top: 16px; font-size: .875rem; }
        .btn   { display: inline-block; padding: 11px 24px; background: #0d9488; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 16px; }
        .btn:hover { background: #0f766e; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>LogiSystem — Instalación Inicial</h1>

    <!-- Resultado de instalación -->
    <div class="card">
        <h2>Resultado</h2>
        <?php foreach ($messages as $m): ?>
        <p class="msg <?= $m[0] ?>"><?= $m[1] ?></p>
        <?php endforeach; ?>
        <?php foreach ($errors as $e): ?>
        <p class="msg err"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>

    <!-- Credenciales -->
    <div class="card">
        <h2>Credenciales de Acceso</h2>
        <table>
            <tr><th>Usuario</th><th>Contraseña</th><th>Rol</th></tr>
            <tr><td><code>admin</code></td>         <td><code>Admin@123</code></td>     <td>Administrador</td></tr>
            <tr><td><code>juan.perez</code></td>    <td><code>Operador@123</code></td>  <td>Operador</td></tr>
            <tr><td><code>maria.gonzalez</code></td><td><code>Operador@123</code></td>  <td>Operador</td></tr>
        </table>
    </div>

    <?php if ($hasErrors): ?>
    <div class="warn-box">
        <strong>❌ Instalación incompleta.</strong> Revise los errores anteriores y verifique la configuración de base de datos en <code>config/database.php</code>.
    </div>
    <?php else: ?>
    <div class="success-box">
        <strong>✅ Instalación completada correctamente.</strong><br>
        <a href="/login" class="btn">Ir al Login →</a>
    </div>
    <div class="warn-box">
        <strong>⚠️ IMPORTANTE:</strong> Elimine el archivo <code>setup.php</code> inmediatamente del servidor. Mantenerlo accesible representa un riesgo de seguridad.
    </div>
    <?php endif; ?>
</div>
</body>
</html>
