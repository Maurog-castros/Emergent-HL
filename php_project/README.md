# LogiSystem — Plataforma de Logística

Sistema web de gestión logística construido con **PHP + MySQL** para ejecutarse en servidores con Apache/Nginx.

---

## Requisitos del Servidor

| Componente | Versión mínima |
|------------|---------------|
| PHP        | 8.0+           |
| MySQL      | 5.7+ / MariaDB 10.4+ |
| Apache     | 2.4+ (con `mod_rewrite`) |
| Nginx      | 1.18+          |

---

## Instalación Rápida

### Paso 1 — Descargar y Colocar el Proyecto

Copie la carpeta `logistics/` a la raíz de su servidor web:

```
/var/www/html/logistics/       (Linux/Apache)
C:/xampp/htdocs/logistics/     (XAMPP Windows)
C:/laragon/www/logistics/      (Laragon)
```

### Paso 2 — Crear la Base de Datos

Desde phpMyAdmin o la consola MySQL:

```sql
CREATE DATABASE logistics_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

O importe el schema directamente:

```bash
mysql -u root -p logistics_db < database/schema.sql
```

### Paso 3 — Configurar Credenciales de Base de Datos

Edite el archivo `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Su usuario MySQL
define('DB_PASS', '');            // Su contraseña MySQL
define('DB_NAME', 'logistics_db');
define('DB_PORT', 3306);
```

### Paso 4 — Instalar y Sembrar Datos

Abra en su navegador:

```
http://localhost/logistics/setup.php?token=logistics_setup_2024
```

Esto creará las tablas y los datos de prueba automáticamente.

> **⚠️ IMPORTANTE:** Elimine `setup.php` del servidor después de la instalación.

### Paso 5 — Acceder al Sistema

```
http://localhost/logistics/
```

---

## Credenciales de Prueba

| Usuario          | Contraseña      | Rol           |
|-----------------|-----------------|---------------|
| `admin`          | `Admin@123`     | Administrador |
| `juan.perez`     | `Operador@123`  | Operador      |
| `maria.gonzalez` | `Operador@123`  | Operador      |

---

## Configuración Apache

El proyecto incluye `.htaccess` con las reglas necesarias. Asegúrese de tener habilitado `mod_rewrite`:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Si el proyecto está en un **subdirectorio**, descomente y ajuste la línea en `index.php`:

```php
// $basePath = '/logistics'; // Ajustar si está en subdirectorio
```

## Configuración Nginx

```nginx
server {
    listen 80;
    server_name logistics.local;
    root /var/www/html/logistics;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    # Bloquear acceso a directorios sensibles
    location ~ ^/(config|includes|lang|models|controllers|database) {
        deny all;
        return 403;
    }
}
```

---

## Estructura del Proyecto

```
logistics/
├── assets/
│   ├── css/style.css           # Estilos personalizados
│   └── js/main.js              # JavaScript del frontend
├── config/
│   └── database.php            # Configuración de base de datos
├── controllers/
│   ├── AuthController.php      # Login / Logout
│   ├── DashboardController.php # Panel principal
│   ├── UserController.php      # CRUD de usuarios
│   └── ClientController.php    # CRUD de clientes
├── database/
│   └── schema.sql              # Esquema de la base de datos
├── includes/
│   ├── auth.php                # Funciones de autenticación/sesión
│   ├── functions.php           # Utilidades generales
│   └── i18n.php                # Sistema de internacionalización
├── lang/
│   ├── es.php                  # Traducciones Español
│   ├── en.php                  # Traducciones English
│   └── zh.php                  # 翻译 中文
├── models/
│   ├── User.php                # Modelo de Usuario
│   └── Client.php              # Modelo de Cliente
├── views/
│   ├── layout/
│   │   ├── header.php          # Header + Sidebar (layout base)
│   │   └── footer.php          # Footer + JS
│   ├── auth/
│   │   └── login.php           # Pantalla de login
│   ├── dashboard/
│   │   └── index.php           # Panel principal
│   ├── users/
│   │   ├── index.php           # Lista de usuarios
│   │   └── form.php            # Formulario crear/editar usuario
│   ├── clients/
│   │   ├── index.php           # Lista de clientes
│   │   └── form.php            # Formulario crear/editar cliente
│   └── errors/
│       └── 404.php             # Página de error 404
├── .htaccess                   # Reglas Apache (URL rewriting + seguridad)
├── index.php                   # Front Controller (router principal)
├── setup.php                   # Instalador inicial (ELIMINAR después de usar)
└── README.md                   # Este archivo
```

---

## Módulos Actuales

| Módulo        | Estado       | Descripción                        |
|---------------|--------------|------------------------------------|
| Login         | ✅ Activo    | Autenticación con sesiones PHP     |
| Dashboard     | ✅ Activo    | Panel con estadísticas             |
| Usuarios      | ✅ Activo    | CRUD completo, roles, estado       |
| Clientes      | ✅ Activo    | CRUD completo, búsqueda, estado    |

## Módulos Futuros (Logística Marítima)

| Módulo        | Estado       |
|---------------|--------------|
| Embarques     | 🔜 Próximamente |
| BL / Conocimientos | 🔜 Próximamente |
| Contenedores  | 🔜 Próximamente |
| Tracking      | 🔜 Próximamente |
| Documentos    | 🔜 Próximamente |

---

## Seguridad Implementada

- **Contraseñas**: Hash bcrypt (cost=12) — nunca se almacenan en texto plano
- **SQL Injection**: 100% Prepared Statements con `mysqli`
- **XSS**: Escape con `htmlspecialchars()` en todas las salidas
- **CSRF**: Token en todos los formularios POST
- **Sesiones**: Regeneración de ID al hacer login, destrucción completa al salir
- **Directorios sensibles**: Bloqueados vía `.htaccess`
- **Estado de usuarios**: Verificación en cada login

---

## Idiomas Soportados

El sistema incluye soporte completo para 3 idiomas:

- 🇪🇸 **Español** (predeterminado)
- 🇺🇸 **English**
- 🇨🇳 **中文** (Chino simplificado)

El selector de idioma está disponible en el sidebar y en la pantalla de login.

---

## Notas de Desarrollo

- Arquitectura **MVC** sin framework, fácil de mantener y extender
- Patrón **Front Controller** — un único `index.php` como punto de entrada
- Estilos con **Bootstrap 5.3** + CSS personalizado
- Iconos con **Bootstrap Icons**
- Fuente: **Inter** (Google Fonts)
- Sin dependencias de Composer — instalación directa en servidor
