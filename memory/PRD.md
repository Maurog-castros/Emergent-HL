# LogiSystem — PRD (Product Requirements Document)

## Problema Original
Plataforma web de logística en PHP + MySQL para servidor tradicional Apache/Nginx.

## Decisiones del Usuario
- **Stack**: PHP puro + MySQL (sin framework)
- **Distribución**: Archivos descargables para instalar en propio servidor
- **Diseño**: Moderno y limpio (colores neutros con acento teal)
- **Idiomas**: Español, English, 中文 (Chinese)
- **Datos de prueba**: 1 admin + 2 operadores + 10 clientes

## Arquitectura
- **Patrón**: MVC + Front Controller (index.php único punto de entrada)
- **Router**: switch(true) en index.php con URL rewriting via .htaccess
- **Base de datos**: MySQL via mysqli con Prepared Statements (anti SQL injection)
- **Sesiones**: PHP sessions con regeneración de ID al login
- **Seguridad**: bcrypt (cost=12), CSRF tokens, XSS escape, headers de seguridad

## Estructura de Archivos
```
php_project/
├── assets/css/style.css       # Bootstrap 5.3 + custom teal theme
├── assets/js/main.js          # Sidebar toggle, flash dismiss, password toggle
├── config/database.php        # DB connection singleton
├── controllers/               # AuthController, DashboardController, UserController, ClientController
├── database/schema.sql        # Esquema MySQL (3 tablas)
├── includes/                  # auth.php, functions.php, i18n.php
├── lang/                      # es.php, en.php, zh.php
├── models/                    # User.php, Client.php
├── views/                     # layout/header+footer, auth/login, dashboard, users, clients, errors
├── .htaccess                  # Apache rewrite + security headers
├── index.php                  # Front Controller / Router
├── setup.php                  # Instalador inicial (eliminar tras uso)
└── README.md                  # Guía de instalación completa
```

## Base de Datos
- **usuarios**: id, nombre, apellido, correo, username, password(bcrypt), rol(admin/operador), estado(activo/inactivo), fecha_creacion
- **clientes**: id, rut, razon_social, contacto, correo, telefono, direccion, ciudad, pais, estado, fecha_creacion
- **audit_log**: preparada para uso futuro (trazabilidad de acciones)

## Módulos Implementados (Fase 1) — Completado 2026-02
- [x] Login / Logout con sesiones seguras
- [x] Dashboard con estadísticas (usuarios/clientes activos y totales)
- [x] CRUD Usuarios (admin/operador, activo/inactivo, búsqueda)
- [x] CRUD Clientes (búsqueda, activo/inactivo, datalist de países)
- [x] Sistema i18n (ES/EN/ZH en runtime via selector en sidebar)
- [x] Diseño responsive Bootstrap 5.3 (sidebar + topbar)
- [x] Protección anti-XSS, CSRF, SQL Injection
- [x] Formularios con validación PHP + feedback visual

## Módulos Futuros (Fase 2+)
- [ ] Embarques (shipments)
- [ ] Conocimientos de Embarque (BL)
- [ ] Contenedores
- [ ] Tracking de carga
- [ ] Gestión de Documentos

## Entregables
- `php_logistics_logisystem.zip` (en /app/) — proyecto completo descargable
- `setup.php` — instalador web con seed data automático
- `README.md` — guía de instalación Apache/Nginx completa
