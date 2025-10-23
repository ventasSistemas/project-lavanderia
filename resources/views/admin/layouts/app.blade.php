<!--views/admin/layouts/app.blade.php-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Lavandería</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Pickr (Color Picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>


    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            background: #000000;
            /*background: #0b3a6d;*/
            color: #fff;
            width: 260px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding-top: 1rem;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #0d6efd #004aa8;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: #0056b3;
            border-radius: 10px;
        }
        .sidebar .nav-link {
            color: #fff;
            font-size: 0.95rem;
            margin: 4px 0;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 8px;
        }
        .sidebar .nav-link:hover, .sidebar .active {
            background: #2970bc;
            border-radius: 8px;
        }
        .content {
            margin-left: 260px;
            padding: 70px 20px 20px;
        }
        .top-navbar {
            background: #fff;
            height: 60px;
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .dropdown-menu a {
            font-size: 0.9rem;
        }

        /* Contenedor para el icono */
        .toggle-icon-container {
            display: inline-block;  
            width: auto;  
            height: auto;  
        }

        /* Transición suave para el icono */
        .toggle-icon {
            transition: transform 0.2s ease; 
            transform-origin: center; 
        }

        /* Efecto hover más suave en las cards de categoría y servicio */
        .category-card,
        .service-card {
            transition: all 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
            cursor: pointer;
        }

        .category-card:hover,
        .service-card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.10);
        }

        /* Imagen con brillo suave */
        .category-card img,
        .service-card img {
            transition: filter 0.5s ease;
        }

        .category-card:hover img,
        .service-card:hover img {
            filter: brightness(1.03);
        }

    </style>
</head>
<body>

    @include('admin.layouts.sidebar')
    @include('admin.layouts.navbar')

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    @stack('scripts')

    <script>
        // Detectar cuando una sección se expande (colapsa) o se contrae (se despliega)
        document.querySelectorAll('.collapse').forEach(function(collapse) {
            collapse.addEventListener('shown.bs.collapse', function () {
                // Cambiar el icono cuando la sección se despliega
                let icon = this.previousElementSibling.querySelector('.toggle-icon');
                if (icon) {
                    icon.classList.add('rotate-up');  // Rota el icono hacia arriba
                }
            });

            collapse.addEventListener('hidden.bs.collapse', function () {
                // Cambiar el icono cuando la sección se colapsa
                let icon = this.previousElementSibling.querySelector('.toggle-icon');
                if (icon) {
                    icon.classList.remove('rotate-up');  // Rota el icono hacia abajo
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Verifica si hay algún estado guardado en localStorage
            const collapsedState = JSON.parse(localStorage.getItem('sidebarState')) || {};

            // Recorre todas las secciones colapsables
            document.querySelectorAll('.collapse').forEach(function(collapse) {
                const targetId = collapse.id;
                const collapseElement = collapse;
                
                // Si la sección estaba abierta anteriormente, mantenla abierta
                if (collapsedState[targetId]) {
                    const bootstrapCollapse = new bootstrap.Collapse(collapseElement, {
                        toggle: true
                    });
                }
                
                // Detecta cambios en el estado de cada sección (expansión/colapso)
                collapseElement.addEventListener('shown.bs.collapse', function () {
                    collapsedState[targetId] = true; // Guarda que esta sección está abierta
                    localStorage.setItem('sidebarState', JSON.stringify(collapsedState)); // Guarda el estado
                });

                collapseElement.addEventListener('hidden.bs.collapse', function () {
                    collapsedState[targetId] = false; // Guarda que esta sección está cerrada
                    localStorage.setItem('sidebarState', JSON.stringify(collapsedState)); // Guarda el estado
                });
            });
        });
    </script>
</body>
</html>