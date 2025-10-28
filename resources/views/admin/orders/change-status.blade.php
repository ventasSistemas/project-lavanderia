@extends('admin.layouts.app')

@section('title', 'Pantalla de Estados de Órdenes')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-display text-primary me-2"></i> Pantalla de Estados de Órdenes
            </h4>
            <p class="text-muted small mb-0">Arrastra las órdenes entre estados (Pendiente → En Proceso → Terminado)</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Registrar Orden
        </a>
    </div>

    <!-- Secciones de estados -->
    <div class="row row-cols-1 row-cols-md-3 g-3 align-items-stretch">
        @foreach($statuses as $status)
            <div class="col d-flex">
                <div class="card shadow-sm border-0 flex-fill d-flex flex-column">
                    <div class="card-header text-white text-center py-2 text-uppercase fw-semibold"
                         style="background-color: {{ $status->color_code ?? '#6c757d' }}">
                        {{ $status->name }}
                    </div>

                    <div class="card-body p-2 overflow-auto droppable-area"
                         style="max-height: calc(100vh - 230px);"
                         data-status-id="{{ $status->id }}"
                         data-status-name="{{ strtolower($status->name) }}">

                        @php
                            $filteredOrders = $orders->where('order_status_id', $status->id);
                        @endphp

                        @forelse($filteredOrders as $order)
                            <div class="border rounded p-2 mb-2 bg-light position-relative draggable-card"
                                 draggable="true"
                                 data-order-id="{{ $order->id }}"
                                 data-current-status="{{ strtolower($order->status->name) }}">

                                <!-- Número de orden y estado de pago -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary">#{{ $order->order_number }}</strong>
                                    
                                    <span class="badge 
                                        @if($order->payment_status === 'paid') bg-success-subtle text-success
                                        @elseif($order->payment_status === 'partial') bg-warning-subtle text-dark
                                        @else bg-danger-subtle text-danger @endif">
                                        @if($order->payment_status === 'paid')
                                            Pagado
                                        @elseif($order->payment_status === 'partial')
                                            Incompleto
                                        @else
                                            Pendiente
                                        @endif
                                    </span>
                                </div>

                                <!-- Cliente -->
                                <div class="mt-1 small text-muted">
                                    <i class="fa-solid fa-user me-1"></i>
                                    {{ $order->customer->full_name ?? 'Sin cliente' }}
                                </div>

                                <!-- Sucursal -->
                                <div class="small">
                                    <i class="fa-solid fa-store me-1"></i>
                                    {{ $order->branch->name ?? 'Sucursal no asignada' }}
                                </div>

                                <!-- Fecha -->
                                <div class="small">
                                    <i class="fa-solid fa-calendar me-1"></i>
                                    {{ $order->created_at->format('d/m H:i') }}
                                </div>

                                <!-- Total, faltante y botones -->
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <span class="fw-semibold d-block">
                                            Total: S/{{ number_format($order->final_total, 2) }}
                                        </span>

                                        @if($order->payment_status === 'partial')
                                            @php
                                                $faltante = max(0, $order->final_total - ($order->payment_amount ?? 0));
                                            @endphp
                                            <span class="text-danger small">
                                                Falta: S/{{ number_format($faltante, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                            class="btn btn-sm btn-outline-info" title="Ver Detalle">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order->id) }}" 
                                        class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        {{-- Si la orden está en estado "Terminado", mostrar icono de contacto --}}
                                        @if(strtolower($order->status->name) === 'terminado' && $order->customer && $order->customer->phone)
                                            @php
                                                $telefono = preg_replace('/\D/', '', $order->customer->phone);
                                            @endphp
                                            <a href="javascript:void(0)" 
                                            class="btn btn-sm btn-outline-success contact-btn" 
                                            title="Contactar cliente"
                                            data-phone="{{ $telefono }}">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-4">
                                <i class="fa-regular fa-clipboard"></i> Sin órdenes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Script para arrastrar y soltar --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".draggable-card");
    const droppables = document.querySelectorAll(".droppable-area");

    cards.forEach(card => {
        card.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("orderId", card.dataset.orderId);
            e.dataTransfer.setData("currentStatus", card.dataset.currentStatus);
        });
    });

    droppables.forEach(area => {
        area.addEventListener("dragover", (e) => {
            e.preventDefault();
            area.classList.add("bg-light-subtle");
        });

        area.addEventListener("dragleave", () => {
            area.classList.remove("bg-light-subtle");
        });

        area.addEventListener("drop", (e) => {
            e.preventDefault();
            area.classList.remove("bg-light-subtle");

            const orderId = e.dataTransfer.getData("orderId");
            const currentStatus = e.dataTransfer.getData("currentStatus");
            const newStatusId = area.dataset.statusId;
            const newStatusName = area.dataset.statusName;

            // Validar transiciones permitidas
            const allowedTransitions = {
                'pendiente': ['en proceso'],
                'en proceso': ['terminado']
            };

            if (!allowedTransitions[currentStatus] || !allowedTransitions[currentStatus].includes(newStatusName)) {
                alert("No se puede mover esta orden directamente a ese estado.");
                return;
            }

            // Llamada AJAX para actualizar estado
            fetch("{{ route('admin.orders.changeStatus') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    order_ids: [orderId],
                    new_status_id: newStatusId
                })
            })
            .then(async res => {
                if (!res.ok) throw new Error(await res.text());
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || "Error al actualizar el estado.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error al conectar con el servidor.");
            });
        });
    });
});

// Detectar si el dispositivo es móvil
function isMobileDevice() {
    return /android|iphone|ipad|ipod/i.test(navigator.userAgent);
}

// Acción del botón de contacto
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".contact-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const phone = this.dataset.phone;

            if (!phone) {
                alert("El cliente no tiene un número registrado.");
                return;
            }

            if (isMobileDevice()) {
                // Si es un móvil → llamada directa
                window.location.href = `tel:${phone}`;
            } else {
                // Si es escritorio → abrir WhatsApp Web
                window.open(`https://wa.me/${phone}`, "_blank");
            }
        });
    });
});
</script>

<style>
.draggable-card {
    cursor: grab;
    transition: transform 0.15s ease;
}
.draggable-card:active {
    cursor: grabbing;
    transform: scale(1.03);
}
.droppable-area.bg-light-subtle {
    background-color: #f8f9fa;
    border: 2px dashed #0d6efd;
}
</style>
@endsection
