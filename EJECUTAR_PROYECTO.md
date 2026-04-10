# Cómo ejecutar LogiSystem (Emergent-HL)

## Requisitos previos

- **PHP 8.0+** instalado y accesible desde PowerShell
- **MySQL/MariaDB** en ejecución
- **Visual Studio Code** o tu navegador web preferido

## Instalación de PHP en Windows

Si no tienes PHP instalado:

1. Descarga PHP desde: https://www.php.net/downloads/
2. Descomprime en `C:\php` (o tu ruta preferida)
3. Agrega la ruta a la variable de entorno `PATH`:
   - Abre "Variables de entorno" (busca en Inicio)
   - Haz clic en "Nueva variable de entorno"
   - Nombre: `PATH`
   - Valor: `C:\php` (o tu ruta)
4. Reinicia PowerShell y verifica: `php -v`

## Opción 1: Script Rápido (recomendado)

### Uso

```powershell
cd c:\DEV\Emergent-HL
.\run.ps1
```

O con puerto personalizado:

```powershell
.\run.ps1 -Port 9090
```

Se abrirá automáticamente en:
- Aplicación: `http://localhost:8080`
- Setup: `http://localhost:8080/setup.php?token=logistics_setup_2024`

## Opción 2: Script Completo

```powershell
cd c:\DEV\Emergent-HL
.\run-project.ps1 -Port 8080
```

Incluye verificaciones adicionales y mensajes detallados.

## Pasos de configuración inicial

### 1. Configurar Base de Datos

Edita `php_project/config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Tu usuario MySQL
define('DB_PASSWORD', 'tu_clave');   // Tu contraseña MySQL
define('DB_NAME', 'logistics_db');   // Nombre de la BD
```

### 2. Crear Base de Datos MySQL

Opción A - Usando phpMyAdmin:
1. Abre `http://localhost/phpmyadmin`
2. Crea nueva base de datos: `logistics_db`
3. Importa el archivo: `php_project/database/schema.sql`

Opción B - Usando línea de comandos:

```bash
mysql -u root -p < php_project/database/schema.sql
```

### 3. Ejecutar Setup

1. Inicia el servidor: `.\run.ps1`
2. Abre en el navegador: `http://localhost:8080/setup.php?token=logistics_setup_2024`
3. Sigue las instrucciones en pantalla
4. **Importante:** Elimina el archivo `setup.php` después de completar

## Credenciales de demo (creadas en setup)

| Usuario | Contraseña |
|---------|-----------|
| admin | Admin@123 |
| juan.perez | Operador@123 |
| maria.gonzalez | Operador@123 |

## Solución de problemas

### Error: "PHP no encontrado"

Verifica que PHP esté en la variable `PATH`:

```powershell
php -v
```

### Puerto ya está en uso

Usa otro puerto:

```powershell
.\run.ps1 -Port 8081
```

### Error de conexión a base de datos

Verifica:
1. MySQL está corriendo
2. Las credenciales en `config/database.php` son correctas
3. La base de datos `logistics_db` existe

### Permiso denegado en PowerShell

Si obtienes error de ejecución:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

## Detener el servidor

Presiona **CTRL+C** en PowerShell

## Próximas lecturas

- Ver README.md en la raíz del proyecto
- Documentación de la API en `php_project/README.md`
- Esquema de base de datos: `php_project/database/schema.sql`

---

¡Listo! El servidor debe estar ejecutándose en `http://localhost:8080`
