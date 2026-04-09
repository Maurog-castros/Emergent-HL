<?php
/**
 * Configuración de Base de Datos
 * LogiSystem — Logistics Management Platform
 *
 * Modifique estas constantes según su entorno.
 */

define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_NAME',    'logistics_db');
define('DB_PORT',    3306);
define('DB_CHARSET', 'utf8mb4');

/**
 * Retorna conexión singleton a MySQL (mysqli)
 */
function getDB(): mysqli
{
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            error_log('[LogiSystem] DB Connection Error: ' . $conn->connect_error);
            http_response_code(500);
            die('<h2>Error de conexión a la base de datos.</h2><p>Verifique la configuración en <code>config/database.php</code></p>');
        }
        $conn->set_charset(DB_CHARSET);
    }
    return $conn;
}
