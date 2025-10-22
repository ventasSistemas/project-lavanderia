<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Lavandería CleanWash')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Fuentes modernas -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    body {
      scroll-behavior: smooth;
      font-family: 'Poppins', sans-serif;
    }

    /* Navbar */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
    }
    .navbar-brand {
      font-weight: 700;
      font-size: 1.4rem;
      color: #007bff !important;
      letter-spacing: 1px;
    }
    .nav-link {
      color: #333 !important;
      transition: color .3s;
    }
    .nav-link:hover {
      color: #007bff !important;
    }

    /* General sections */
    section {
      padding: 80px 0;
    }

    /* Hero slider text */
    .carousel-caption {
      background: rgba(0, 0, 0, 0.45);
      border-radius: 1rem;
      padding: 1.5rem;
    }

    /* Cards */
    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      transition: all .3s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    /* Titles */
    h2.section-title {
      font-weight: 700;
      color: #0d1b2a;
      margin-bottom: 2rem;
    }

    /* Footer */
    footer {
      background: #0d1b2a;
      color: #ddd;
      text-align: center;
      padding: 40px 0 20px;
    }
    footer p {
      margin: 0;
      font-size: 0.9rem;
    }

    /* Buttons */
    .btn-primary {
      background: #007bff;
      border: none;
      border-radius: 2rem;
      padding: 10px 30px;
      font-weight: 500;
      transition: background .3s;
    }
    .btn-primary:hover {
      background: #0069d9;
    }

    /* Animations */
    [data-aos] {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.6s ease-out;
    }
    [data-aos].aos-animate {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="#">CleanWash</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
          <li class="nav-item"><a class="nav-link" href="#equipos">Equipos</a></li>
          <li class="nav-item"><a class="nav-link" href="#productos">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="#comentarios">Opiniones</a></li>
          <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido dinámico -->
  <main class="mt-5 pt-4">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer>
    <p>&copy; {{ date('Y') }} Lavandería CleanWash — Limpieza y confianza a tu servicio.</p>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Animación simple al hacer scroll
    document.addEventListener("scroll", () => {
      document.querySelectorAll("[data-aos]").forEach(el => {
        if (el.getBoundingClientRect().top < window.innerHeight - 100) {
          el.classList.add("aos-animate");
        }
      });
    });
  </script>
</body>
</html>
