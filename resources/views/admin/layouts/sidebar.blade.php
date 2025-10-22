<div class="sidebar d-flex flex-column p-2">
    <div class="text-center mb-4">
        <i class="fa-solid fa-circle-user fa-6x me-2 text-light"></i>
        <h3 style="margin-top: 20px;">CleanWash</h3>
    </div>

    <ul class="nav flex-column">
        <hr class="text-white">

        <!-- Dashboard -->
        <li>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>

        <!-- POS -->
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.pos') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register"></i> POS
            </a>
        </li>

        <!-- Pedidos -->
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.pedidos') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i> Pedidos
            </a>
        </li>

        <!-- Estado de pedidos -->
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.estado.pedidos') ? 'active' : '' }}">
                <i class="fa-solid fa-display"></i> Estado de pedidos
            </a>
        </li>

        <!-- Gastos -->
        <li class="mt-3 fw-bold text-uppercase small" data-bs-toggle="collapse" data-bs-target="#gastos" aria-expanded="false" aria-controls="gastos">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-money-bill-wave"></i> Gastos 
                <span class="toggle-icon-container float-end">
                    <i class="fa-solid fa-chevron-down toggle-icon"></i>
                </span>
            </a>
        </li>

        <ul class="collapse" id="gastos">
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.gastos') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-ul"></i> Lista de gastos
                </a>
            </li>
        </ul>

        <!-- Clientes -->
        <li class="mt-3 fw-bold text-uppercase small" data-bs-toggle="collapse" data-bs-target="#clientes" aria-expanded="false" aria-controls="clientes">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-users"></i> Clientes
                <i class="fa-solid fa-chevron-down float-end toggle-icon"></i>
            </a>
        </li>
        <ul class="collapse" id="clientes">
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.clientes') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Todos los clientes
                </a>
            </li>
        </ul>

        <!-- Servicios -->
        <li class="mt-3 fw-bold text-uppercase small" data-bs-toggle="collapse" data-bs-target="#servicios" aria-expanded="false" aria-controls="servicios">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-tags"></i> Servicios
                <i class="fa-solid fa-chevron-down float-end toggle-icon"></i>
            </a>
        </li>
        <ul class="collapse" id="servicios">
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.servicios') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Lista de servicios
                </a>
            </li>
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.tipo.servicio') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Tipo de servicio
                </a>
            </li>
        </ul>

        <!-- Informes -->
        <li class="mt-3 fw-bold text-uppercase small" data-bs-toggle="collapse" data-bs-target="#informes" aria-expanded="false" aria-controls="informes">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-file-alt"></i> Informes
                <i class="fa-solid fa-chevron-down float-end toggle-icon"></i>
            </a>
        </li>
        <ul class="collapse" id="informes">
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.reporte.diario') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-day"></i> Reporte diario
                </a>
            </li>
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.informe.pedido') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-lines"></i> Informe de pedido
                </a>
            </li>
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.reporte.ventas') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> Reporte de ventas
                </a>
            </li>
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.informe.gastos') ? 'active' : '' }}">
                    <i class="fa-solid fa-wallet"></i> Informe de gastos
                </a>
            </li>
        </ul>

        <!-- Configuración -->
        <li class="mt-3 fw-bold text-uppercase small" data-bs-toggle="collapse" data-bs-target="#configuracion" aria-expanded="false" aria-controls="configuracion">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-cogs"></i> Configuración
                <i class="fa-solid fa-chevron-down float-end toggle-icon"></i>
            </a>
        </li>
        <ul class="collapse" id="configuracion">
            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-tie"></i> Personales
                </a>
            </li>
            <li>
                <a href="{{ route('admin.branches.index') }}" class="nav-link {{ request()->routeIs('admin.branches.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i> Sucursales
                </a>
            </li>
            <li>
                <a href="#" class="nav-link {{ request()->routeIs('admin.inventario') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i> Inventario
                </a>
            </li>
        </ul>

        <hr class="text-white">
        <div class="bg-light p-2 rounded transition-colors">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión</button>
            </form>
        </div>
    </ul>
</div>