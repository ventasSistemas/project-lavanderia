<!-- views/admin/layouts/navbar.blade.php -->
<nav class="top-navbar navbar navbar-expand navbar-light border-bottom px-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Botón de colapsar sidebar -->
        <button id="toggleSidebar" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Menú de usuario -->
        <ul class="navbar-nav align-items-center mb-0">

            @php
                $notifications = \App\Models\OrderNotification::with('order.customer')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp
        
            <!-- Notificaciones -->
            <li class="nav-item dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bell fa-lg text-primary"></i>
                    @if($notifications->where('is_read', false)->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $notifications->where('is_read', false)->count() }}
                        </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="notifDropdown" style="width: 500px;">
                    <li class="dropdown-header fw-semibold text-center bg-light">Notificaciones de Ordenes</li>

                    @forelse($notifications as $notif)
                        <li>
                            <a href="{{ route('admin.orders.show', $notif->order_id) }}" class="dropdown-item small d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>#{{ $notif->order->order_number }}</strong> — {{ $notif->order->customer->full_name }}
                                    <div class="text-muted small">{{ $notif->message }}</div>
                                    @php
                                        $days = now()->diffInDays($notif->order->updated_at);
                                    @endphp
                                    @if($days > 0)
                                        <div class="text-danger small">🕒 {{ $days }} día{{ $days > 1 ? 's' : '' }} sin recoger</div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @empty
                        <li><div class="dropdown-item text-muted small text-center">Sin notificaciones recientes</div></li>
                    @endforelse
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-circle-user fa-lg me-2 text-primary"></i>
                    <div class="d-flex flex-column lh-1">
                        <span style="font-weight: bold;">{{ Auth::user()->full_name ?? 'Usuario' }}</span>
                        <small class="text-muted">{{ Auth::user()->email ?? 'correo@ejemplo.com' }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user-gear me-2"></i> Actualizar perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>