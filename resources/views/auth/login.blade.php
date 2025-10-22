<!DOCTYPE html>  
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Anime.js para las burbujas -->
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <!-- LottieFiles para la animación del águila -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        section {
            min-height: 100vh;
            overflow: hidden;
        }

        /* ----- Columna izquierda ----- */
        .left-side {
            background: linear-gradient(to top, #a7d8f8 10%, #e0f7ff 100%);
            position: relative;
            overflow: hidden;
        }

        /* Fondo del agua */
        .left-side::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 180px;
            background: linear-gradient(to top, #007bff 30%, transparent 100%);
            opacity: 0.6;
        }
        

        /* Animación suave para flotar la imagen */
        @keyframes floatLogo {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-10px);
            }
        }

        /* Animación del águila */
        .eagle-animation {
            z-index: 2;
        }

        /* Burbujas */
        .bubble {
            position: absolute;
            bottom: -10px;
            width: 100px;
            height: 100px;
            background: rgb(255, 255, 255);
            border-radius: 50%;
            animation: floatUp 8s infinite ease-in;
            z-index: 0;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translateY(-800px) scale(1.5);
                opacity: 0;
            }
        }

        /* ----- Separador entre columnas ----- */
        .separator {
            width: 2px;
            background: linear-gradient(to bottom, #007bff, #00bfff);
            height: 80%;
            margin: auto;
            border-radius: 2px;
        }

        /* ----- Tarjeta del login ----- */
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card .form-label {
            font-weight: bold;
        }

        .card button[type="submit"] {
            font-weight: bold;
            background-color: #007bff;
            border-color: #007bff;
        }

        .card button[type="submit"]:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* ---- Estilo moderno del título ---- */
        .brand-title {
            position: absolute;
            top: 25%; 
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 3rem;
            background: linear-gradient(90deg, #007bff, #00c6ff, #007bff);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: 
                gradientMove 4s linear infinite,
                floatTitle 3s ease-in-out infinite;
            text-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
            letter-spacing: 1.5px;
        }

        /* Animación del gradiente brillante */
        @keyframes gradientMove {
            0% { background-position: 0% center; }
            50% { background-position: 100% center; }
            100% { background-position: 0% center; }
        }

        /* Efecto de flotación suave */
        @keyframes floatTitle {
            0%, 100% { transform: translate(-50%, -52%); }
            50% { transform: translate(-50%, -48%); }
        }

        /* Animación elegante de entrada */
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<section class="d-flex align-items-center justify-content-center">
    <div class="container-fluid">
        <div class="row align-items-center min-vh-100">
            
            <!-- Columna izquierda -->
            <div class="col-md-6 left-side d-none d-md-flex flex-column justify-content-center align-items-center position-relative text-center">
                
                <h1 class="brand-title">CleanWash</h1>

                <!-- Animación del águila -->
                <lottie-player
                    src="https://lottie.host/f7ba7904-b768-4b6d-964d-f1a68ead985c/tvqThjXWtn.json"
                    background="transparent"
                    speed="1"
                    class="eagle-animation"
                    style="width: 350px; height: 918px;"
                    loop
                    autoplay>
                </lottie-player>
            </div>

            <!-- Separador central -->
            <div class="col-md-1 d-none d-md-flex justify-content-center">
                <div class="separator"></div>
            </div>

            <!-- Columna derecha -->
            <div class="col-md-5 d-flex justify-content-center align-items-center">
                <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
                    <h3 class="text-center mb-3 text-primary fw-bold">Laundry System</h3>
                    <p class="text-center text-muted mb-4">Accede al panel administrativo</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Script para las burbujas -->
<script>
    const leftSide = document.querySelector('.left-side');

    function createBubble() {
        const bubble = document.createElement('div');
        bubble.classList.add('bubble');
        const size = Math.random() * 20 + 10;
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.left = `${Math.random() * 100}%`;
        leftSide.appendChild(bubble);

        setTimeout(() => bubble.remove(), 8000);
    }

    setInterval(createBubble, 500);
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
