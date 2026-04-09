<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — LogiSystem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; margin: 0;
        }
        .error-box {
            text-align: center;
            padding: 48px 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 420px;
        }
        .error-icon { font-size: 3rem; color: #0d9488; margin-bottom: 16px; }
        h1 { font-size: 4rem; font-weight: 700; color: #1e293b; margin: 0 0 8px; }
        p { color: #64748b; margin: 0 0 24px; }
        a {
            display: inline-block;
            padding: 10px 24px;
            background: #0d9488; color: #fff;
            border-radius: 8px; text-decoration: none;
            font-weight: 600; transition: background 0.2s;
        }
        a:hover { background: #0f766e; }
    </style>
</head>
<body>
<div class="error-box">
    <div class="error-icon"><i class="bi bi-compass"></i></div>
    <h1>404</h1>
    <p>La página que buscas no existe o fue movida.</p>
    <a href="/dashboard"><i class="bi bi-house me-2"></i>Ir al Dashboard</a>
</div>
</body>
</html>
