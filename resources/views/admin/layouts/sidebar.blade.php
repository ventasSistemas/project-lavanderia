<!--views/layouts/sidebar.blade.php-->
<div class="sidebar d-flex flex-column p-2">

    <!-- Logo -->
    <div class="text-center mb-3">
        <div class="icon-container">
            <i class="fa-solid fa-house-flood-water fa-3x me-0 text-light"></i>
        </div>
        <h4 class="mt-2" style="color: rgb(5, 130, 255); font-weight: bold;">CleanWash</h4>
        <p class="description">"Tu lavandería de confianza para un lavado rápido y de calidad."</p>
    </div>

    <!-- Enlaces -->
    <ul class="nav flex-column">

        <!-- Dashboard -->
        <li class="section-title">General</li>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line me-2"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('admin.pos.index') }}" class="nav-link {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register me-2"></i> POS
            </a>
        </li>

        <!-- Ordenes -->
        <li class="section-title mt-3">Órdenes</li>
        <li>
            <a href="{{ route('admin.order-status.index') }}" class="nav-link {{ request()->routeIs('admin.order-status.index') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-2"></i> Estados
            </a>
        </li>
        <li>
            <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.index') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card me-2"></i>  Metodos de Pago
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                <i class="fa-solid fa-box me-2"></i> Registrar Orden
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.changeStatus.view') }}" class="nav-link {{ request()->routeIs('admin.orders.changeStatus.view') ? 'active' : '' }}">
                <i class="fa-solid fa-display me-2"></i> Estado de Órdenes
            </a>
        </li>

        <!-- Clientes -->
        <li class="section-title mt-3">Clientes</li>
        <li>
            <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
                <i class="fa-solid fa-users me-2"></i> Clientes
            </a>
        </li>

        <!-- Servicios -->
        <li class="section-title mt-3">Servicios</li>
        <li>
            <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.index') ? 'active' : '' }}">
                <i class="fa-solid fa-tags me-2"></i> Lista de Servicios
            </a>
        </li>

        <!-- Gastos -->
        <li class="section-title mt-3">Gastos</li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.gastos') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill-wave me-2"></i> Lista de Gastos
            </a>
        </li>

        <!-- Informes -->
        <li class="section-title mt-3">Informes</li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.reporte.diario') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-day me-2"></i> Reporte Diario
            </a>
        </li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.informe.pedido') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines me-2"></i> Informe de Pedido
            </a>
        </li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.reporte.ventas') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-column me-2"></i> Reporte de Ventas
            </a>
        </li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.informe.gastos') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet me-2"></i> Informe de Gastos
            </a>
        </li>

        <!-- Configuración -->
        <li class="section-title mt-3">Configuración</li>
        <li>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="fa-solid fa-user-tie me-2"></i> Personales
            </a>
        </li>
        <li>
            <a href="{{ route('admin.branches.index') }}" class="nav-link {{ request()->routeIs('admin.branches.index') ? 'active' : '' }}">
                <i class="fa-solid fa-building me-2"></i> Sucursales
            </a>
        </li>
        <li>
            <a href="#" class="nav-link {{ request()->routeIs('admin.inventario') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Inventario
            </a>
        </li>

        <!-- Cerrar sesión -->
        <hr class="text-white mt-4">
        <div class="bg-light p-2 rounded">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="dropdown-item text-danger">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </ul>
</div>


<style>
.icon-container {
    display: inline-block;
    padding: 20px;
    border: 5px solid white;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.1);
}

.description {
    font-size: 0.9rem;
    color: #979696;
    margin-top: 10px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.4;
}

/* Título de secciones */
.section-title {
    color: #adb5bd;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
    margin-top: 1rem;
    margin-bottom: 0.4rem;
    letter-spacing: 0.5px;
}

/* Estilo general de links */

.nav-link.active {
    background-color: #0d6efd;
    color: #fff !important;
}

/* Separación y limpieza */
.sidebar hr {
    border-color: rgba(255, 255, 255, 0.3);
}

</style>