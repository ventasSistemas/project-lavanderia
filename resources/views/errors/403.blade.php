<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | Acceso denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }
        .error-box {
            background: rgba(255, 255, 255, 0.08);
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            max-width: 500px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #60a5fa;
        }
        .btn-home {
            margin-top: 1rem;
            background-color: #3b82f6;
            border: none;
        }
        .btn-home:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">403</div>
        <h2>Acceso Denegado</h2>
        <p>No tienes permiso para acceder a esta sección del sistema.</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" class="btn btn-home btn-lg text-white">Volver al Panel</a>
    </div>
</body>
</html>