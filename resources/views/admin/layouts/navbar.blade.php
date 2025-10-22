<!--views/admin/layouts/navbar.blade.php-->
<nav class="top-navbar navbar navbar-expand navbar-light border-bottom">
    <div class="container-fluid justify-content-end px-4">

        <ul class="navbar-nav align-items-center">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-circle-user fa-lg me-2 text-primary"></i>
                    <div class="d-flex flex-column lh-1">
                        <span class="fw-semibold">{{ Auth::user()->full_name ?? 'Usuario' }}</span>
                        <small class="text-muted">{{ Auth::user()->email ?? 'correo@ejemplo.com' }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user-gear me-2"></i> Actualizar perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
