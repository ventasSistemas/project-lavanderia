{{-- resources/views/admin/pos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Punto de Venta')

@section('content_header')
    <h1 class="text-primary fw-bold">
        <i class="fas fa-cash-register"></i> Punto de Venta (P.O.S)
    </h1>
@stop

@section('content')
<div class="container mt-4">

    {{-- 🔹 Buscador de Orden --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label for="order_number" class="form-label fw-semibold">Número de Orden</label>
                    <input type="text" id="order_number" class="form-control form-control-lg" placeholder="Ejemplo: ORD-ABC123">
                </div>
                <div class="col-md-4 text-center mt-3 mt-md-0">
                    <button id="btnBuscar" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Sección de Resultados --}}
    <div id="resultado" class="d-none">
        {{-- Información del Cliente --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-user"></i> Información del Cliente
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <span id="cliente_nombre"></span></p>
                <p><strong>Teléfono:</strong> <span id="cliente_telefono"></span></p>
                <p><strong>Dirección:</strong> <span id="cliente_direccion"></span></p>
            </div>
        </div>

        {{-- Servicios --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white fw-bold">
                <i class="fas fa-tshirt"></i> Servicios Solicitados
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle" id="tabla_servicios">
                    <thead class="table-primary">
                        <tr>
                            <th>Servicio</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Subtotal</th>
                            <th id="subtotal"></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Descuento</th>
                            <th id="descuento"></th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Impuesto</th>
                            <th id="impuesto"></th>
                        </tr>
                        <tr class="table-success">
                            <th colspan="3" class="text-end">Total Final</th>
                            <th id="total_final"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Pago --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold">
                <i class="fas fa-credit-card"></i> Información de Pago
            </div>
            <div class="card-body">
                <p><strong>Método de Pago:</strong> <span id="metodo_pago"></span></p>
                <p><strong>Submétodo:</strong> <span id="submetodo_pago"></span></p>
                <p><strong>Monto Pagado:</strong> <span id="monto_pagado"></span></p>
                <p><strong>Vuelto:</strong> <span id="vuelto"></span></p>
                <p><strong>Estado:</strong>
                    <span id="estado_pago" class="badge fs-6"></span>
                </p>

                <button id="btnRegistrarPago" class="btn btn-success btn-lg mt-3">
                    <i class="fas fa-money-bill-wave"></i> Registrar Pago
                </button>
            </div>
        </div>
    </div>

    {{-- Alerta si no se encuentra --}}
    <div id="sin_resultado" class="alert alert-warning text-center d-none mt-4">
        <i class="fas fa-exclamation-triangle"></i> No se encontró la orden especificada.
    </div>

</div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnBuscar = document.getElementById('btnBuscar');
    const inputOrden = document.getElementById('order_number');
    const resultado = document.getElementById('resultado');
    const sinResultado = document.getElementById('sin_resultado');

    btnBuscar.addEventListener('click', buscarOrden);

    function buscarOrden() {
        const orderNumber = inputOrden.value.trim();
        if (!orderNumber) {
            alert('Por favor, ingresa un número de orden.');
            return;
        }

        fetch(`/admin/orders/${orderNumber}/details`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarDatos(data.order);
                } else {
                    resultado.classList.add('d-none');
                    sinResultado.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                resultado.classList.add('d-none');
                sinResultado.classList.remove('d-none');
            });
    }

    function mostrarDatos(order) {
        resultado.classList.remove('d-none');
        sinResultado.classList.add('d-none');

        // Cliente
        document.getElementById('cliente_nombre').textContent = order.customer.full_name;
        document.getElementById('cliente_telefono').textContent = order.customer.phone ?? '-';
        document.getElementById('cliente_direccion').textContent = order.customer.address ?? '-';

        // Servicios
        const tbody = document.querySelector('#tabla_servicios tbody');
        tbody.innerHTML = '';
        order.items.forEach(item => {
            const row = `
                <tr>
                    <td>${item.service.name}</td>
                    <td>${item.quantity}</td>
                    <td>S/ ${item.unit_price.toFixed(2)}</td>
                    <td>S/ ${(item.quantity * item.unit_price).toFixed(2)}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        document.getElementById('subtotal').textContent = `S/ ${order.total_amount.toFixed(2)}`;
        document.getElementById('descuento').textContent = `S/ ${order.discount.toFixed(2)}`;
        document.getElementById('impuesto').textContent = `S/ ${order.tax.toFixed(2)}`;
        document.getElementById('total_final').textContent = `S/ ${order.final_total.toFixed(2)}`;

        // Pago
        document.getElementById('metodo_pago').textContent = order.payment_method?.name ?? '-';
        document.getElementById('submetodo_pago').textContent = order.payment_submethod?.name ?? '-';
        document.getElementById('monto_pagado').textContent = `S/ ${(order.payment_amount ?? 0).toFixed(2)}`;
        document.getElementById('vuelto').textContent = `S/ ${(order.payment_returned ?? 0).toFixed(2)}`;

        const estado = document.getElementById('estado_pago');
        estado.textContent = order.payment_status.toUpperCase();

        estado.className = 'badge fs-6';
        if (order.payment_status === 'paid') {
            estado.classList.add('bg-success');
        } else if (order.payment_status === 'partial') {
            estado.classList.add('bg-warning', 'text-dark');
        } else {
            estado.classList.add('bg-danger');
        }
    }
});
</script>
@stop