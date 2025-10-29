@extends('admin.layouts.app')

@section('title', 'Crear Orden')

@section('content')
<div class="container-fluid py-3">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-primary">
            <i class="bi bi-receipt-cutoff"></i> Nueva Orden
        </h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Alertas --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf

        {{-- INFORMACIÓN DEL CLIENTE --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="bi bi-person-circle"></i> Información del Cliente
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customer_id" class="form-label fw-semibold">Cliente</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="branch_id" class="form-label fw-semibold">Sucursal</label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="">-- Seleccionar Sucursal --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="order_status_id" class="form-label fw-semibold">Estado de Orden</label>
                        <select name="order_status_id" id="order_status_id" class="form-select" disabled>
                            @foreach($statuses as $status)
                                @if(strtolower($status->name) === 'pendiente')
                                    <option value="{{ $status->id }}" selected style="color: {{ $status->color_code }};">
                                        {{ ucfirst($status->name) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        {{-- Campo oculto para enviar el valor al backend --}}
                        @foreach($statuses as $status)
                            @if(strtolower($status->name) === 'pendiente')
                                <input type="hidden" name="order_status_id" value="{{ $status->id }}">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN PRINCIPAL: SERVICIOS --}}
        <div class="row g-4 mb-4">
            {{-- IZQUIERDA --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white fw-semibold">
                        <i class="bi bi-basket"></i> Seleccionar Servicios
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Categorías disponibles:</h6>
                        <div class="row g-3 mb-4">
                            @foreach($categories as $category)
                                <div class="col-md-4">
                                    <div class="card category-card text-center p-2 shadow-sm"
                                         data-category-id="{{ $category->id }}"
                                         style="cursor:pointer; transition:0.3s;">
                                        <img src="{{ asset($category->image ?? 'images/no-image.png') }}" 
                                             class="img-fluid rounded mb-2" style="max-height:110px; object-fit:cover;">
                                        <h6 class="fw-semibold">{{ $category->name }}</h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="servicesContainer" style="display:none;">
                            <h6 class="fw-bold mb-3">Servicios disponibles:</h6>
                            <div class="row g-3" id="servicesCards"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DERECHA --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-list-check"></i> Servicios Agregados
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-sm table-bordered align-middle text-center mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Cant.</th>
                                        <th>Precio Unit.</th>
                                        <th>SubTotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsBody"></tbody>
                            </table>
                        </div>

                        {{-- Totales --}}
                        <div class="mt-3">
                            <table class="table table-sm mb-0">
                                <tr><th>Subtotal:</th><td id="subtotalDisplay">S/0.00</td></tr>
                                <tr><th>Pago:</th><td id="discountDisplay">S/0.00</td></tr>
                                <tr><th>Impuesto:</th><td id="taxDisplay">S/0.00</td></tr>
                                <tr class="fw-bold text-success"><th>Total:</th><td id="totalDisplay">S/0.00</td></tr>
                            </table>
                        </div>

                        <div class="text-end mt-2">
                            <button type="button" id="clearSelection" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x-circle"></i> Limpiar selección
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTÓN FINAL --}}
        <div class="text-end">
            <button type="button" class="btn btn-success px-4" id="openConfirmModal">
                <i class="bi bi-save"></i> Guardar Continuar
            </button>
        </div>

        <!-- MODAL: CONFIRMAR ORDEN -->
        <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 600px;">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- HEADER -->
                <div class="modal-header bg-success bg-gradient text-white py-3">
                    <h5 class="modal-title fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i> Confirmar Orden
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body px-4 py-3">

                    <!-- INFORMACIÓN DEL CLIENTE -->
                    <div class="mb-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="bi bi-person-lines-fill me-1"></i> Información del Cliente
                    </h5>

                    <div id="orderSummary" class="bg-light p-3 rounded-3 mb-3 border small shadow-sm">
                        <p class="mb-2"><strong>Cliente:</strong> <span id="summaryCustomer">—</span></p>
                        <p class="mb-2"><strong>Sucursal:</strong> <span id="summaryBranch">—</span></p>
                        <p class="mb-0"><strong>Estado del Pedido:</strong> <span id="summaryStatus">—</span></p>
                    </div>

                    <div class="row g-2 small">
                        <div class="col-6">
                        <label class="form-label mb-1">Fecha de Creación</label>
                        <input type="text" class="form-control form-control-sm bg-light" 
                                value="{{ now()->format('d/m/Y H:i') }}" readonly>
                        </div>
                        <div class="col-6">
                        <label class="form-label mb-1">Entrega Estimada</label>
                        <input type="datetime-local" id="delivery_date"
                                class="form-control form-control-sm" 
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    </div>

                    <!-- PAGO Y TOTALES -->
                    <div class="mb-4">
                    <h5 class="fw-bold text-success mb-3">
                        <i class="bi bi-cash-coin me-1"></i> Pago y Totales
                    </h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-borderless mb-0 align-middle fs-6">
                        <tbody>
                            <tr><th class="text-muted">Subtotal:</th><td id="subtotalConfirm" class="text-end">S/0.00</td></tr>
                            <tr><th class="text-muted">Descuento:</th><td id="discountConfirm" class="text-end">S/0.00</td></tr>
                            <tr><th class="text-muted">Impuesto:</th><td id="taxConfirm" class="text-end">S/0.00</td></tr>
                            <tr class="fw-bold border-top border-success">
                            <th>Total:</th><td id="totalConfirm" class="text-end text-success fs-5">S/0.00</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    </div>

                    <!-- ESTADO DE PAGO -->
                    <div class="mb-4">
                    <label class="form-label fw-semibold">Estado del Pago</label>
                    <select id="payment_status" name="payment_status" class="form-select form-select-sm">
                        <option value="pending" selected>Pendiente</option>
                        <option value="paid">Pagado</option>
                        <option value="partial">Incompleto</option>
                    </select>
                    </div>

                    <!-- DETALLES DE PAGO -->
                    <div id="paymentDetails" class="mb-4" style="display:none;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Método</label>
                                <select id="payment_method_id" name="payment_method_id" class="form-select form-select-sm">
                                    <option value="">Seleccione</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Submétodo</label>
                                <select id="payment_submethod_id" name="payment_submethod_id" class="form-select form-select-sm" disabled>
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monto Pagado (S/)</label>
                                <input type="number" step="0.01" id="payment_amount" name="payment_amount" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vuelto (S/)</label>
                                <input type="number" step="0.01" id="payment_returned" name="payment_returned" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Descuento (S/)</label>
                                <input type="number" step="0.01" id="discount" name="discount" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Impuesto (S/)</label>
                                <input type="number" step="0.01" id="tax" name="tax" class="form-control form-control-sm" value="0">
                            </div>
                        </div>
                    </div>


                    <!-- NOTAS -->
                    <div>
                        <label class="form-label fw-semibold">Notas</label>
                        <textarea id="notes" rows="3" class="form-control form-control-sm" placeholder="Observaciones adicionales..."></textarea>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Confirmar y Guardar
                    </button>
                </div>

                </div>
            </div>
        </div>

        <!-- ESTILOS MODAL CONFIRMAR -->
        <style>
        .modal-dialog {
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .modal-content {
            font-size: 0.95rem;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-title {
            font-size: 1.1rem;
            letter-spacing: .3px;
        }

        .form-label {
            font-size: 0.85rem;
            color: #555;
        }

        #orderSummary {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .form-control-sm, .form-select-sm {
            border-radius: 0.4rem;
        }

        .modal-footer .btn {
            border-radius: 0.4rem;
        }

        .modal-body h5 {
            font-size: 1rem;
        }
        </style>


    </form>
</div>

{{-- ESTILOS --}}
<style>
    .category-card:hover {
        transform: scale(1.03);
        background-color: #e7f3ff;
    }
</style>

{{-- Script for Dynamic Items --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentStatus = document.getElementById('payment_status');
    const paymentDetails = document.getElementById('paymentDetails');
    const paymentAmount = document.getElementById('payment_amount');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const paymentReturnedInput = document.getElementById('payment_returned');

    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const discountDisplay = document.getElementById('discountDisplay');
    const taxDisplay = document.getElementById('taxDisplay');
    const totalDisplay = document.getElementById('totalDisplay');

    const subtotalConfirm = document.getElementById('subtotalConfirm');
    const discountConfirm = document.getElementById('discountConfirm');
    const taxConfirm = document.getElementById('taxConfirm');
    const totalConfirm = document.getElementById('totalConfirm');

    // Mostrar u ocultar la sección de pago según estado
    paymentStatus.addEventListener('change', function () {
        paymentDetails.style.display = (this.value === 'pending') ? 'none' : 'flex';
    });

    // Función para actualizar los totales dinámicamente
    function updateTotals() {
        const subtotal = parseFloat(subtotalDisplay?.innerText.replace('S/', '')) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        const payment = parseFloat(paymentAmount.value) || 0;

        // Calcular el total final de la orden
        let finalTotal = subtotal - discount + tax;
        if (finalTotal < 0) finalTotal = 0;

        // Variables auxiliares
        let vuelto = 0;
        let saldoPendiente = 0;
        let mensajePago = "";

        // Calcular saldo o vuelto
        if (payment > finalTotal) {
            vuelto = payment - finalTotal;
            mensajePago = `<span class="text-success">Pagado completo. Vuelto: S/${vuelto.toFixed(2)}</span>`;
        } else if (payment < finalTotal) {
            saldoPendiente = finalTotal - payment;
            mensajePago = `<span class="text-danger">Pago parcial. Falta: S/${saldoPendiente.toFixed(2)}</span>`;
        } else {
            mensajePago = `<span class="text-primary">Pagado exacto</span>`;
        }

        // Actualizar los valores visibles
        paymentReturnedInput.value = vuelto.toFixed(2);

        discountDisplay.innerText = `S/${discount.toFixed(2)}`;
        taxDisplay.innerText = `S/${tax.toFixed(2)}`;
        totalDisplay.innerText = `S/${finalTotal.toFixed(2)}`;

        subtotalConfirm.innerText = `S/${subtotal.toFixed(2)}`;
        discountConfirm.innerText = `S/${discount.toFixed(2)}`;
        taxConfirm.innerText = `S/${tax.toFixed(2)}`;

        // Mostrar total final con estado de pago dinámico
        totalConfirm.innerHTML = `S/${finalTotal.toFixed(2)} <br>${mensajePago}`;
    }

    // Actualizar cada vez que cambien los valores
    [paymentAmount, discountInput, taxInput].forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    // Abrir modal con resumen actualizado
    document.getElementById('openConfirmModal').addEventListener('click', function () {
        updateTotals(); // recalcula antes de mostrar
        const modal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
        modal.show();

        const resumenHTML = `
            <ul class="list-group small">
                <li class="list-group-item"><strong>Cliente:</strong> ${document.getElementById('customer_id').selectedOptions[0]?.text || '-'}</li>
                <li class="list-group-item"><strong>Sucursal:</strong> ${document.getElementById('branch_id').selectedOptions[0]?.text || '-'}</li>
                <li class="list-group-item"><strong>Estado de Orden:</strong> ${document.getElementById('order_status_id').selectedOptions[0]?.text || '-'}</li>
            </ul>
        `;
        document.getElementById('orderSummary').innerHTML = resumenHTML;
    });
});

//Envio y Generacion de Ticket
document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');

    if (orderForm) {
        orderForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(orderForm);

            try {
                const response = await fetch(orderForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Manejar si el backend no responde con JSON válido
                let data;
                try {
                    data = await response.json();
                } catch (err) {
                    throw new Error('Respuesta no válida del servidor');
                }

                if (data.success) {
                    // Abrir el ticket en nueva pestaña
                    const ticketWindow = window.open(data.ticket_url, '_blank');

                    // Cerrar modal si existe
                    const modalElement = document.getElementById('confirmOrderModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }

                    // Mostrar notificación visual
                    Swal.fire({
                        icon: 'success',
                        title: '¡Orden creada!',
                        text: data.message,
                        timer: 1000,
                        showConfirmButton: false
                    });

                    // Redirigir al index después del mensaje
                    setTimeout(() => {
                        if (ticketWindow) {
                            ticketWindow.focus();
                        }
                        console.log('Redirigiendo a:', data.redirect_url);
                        window.location.replace(data.redirect_url);
                    }, 1200);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo crear la orden.'
                    });
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error inesperado al crear la orden.'
                });
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const methodSelect = document.getElementById('payment_method_id');
    const submethodSelect = document.getElementById('payment_submethod_id');

    methodSelect.addEventListener('change', async function () {
        const methodId = this.value;
        submethodSelect.innerHTML = '<option value="">Cargando...</option>';
        submethodSelect.disabled = true;

        if (methodId) {
            try {
                const response = await fetch(`/admin/payment-submethods/by-method/${methodId}`);

                if (!response.ok) throw new Error('Error al cargar submétodos');

                const submethods = await response.json();

                submethodSelect.innerHTML = '<option value="">Seleccione un submétodo</option>';
                submethods.forEach(sub => {
                    submethodSelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                });

                submethodSelect.disabled = submethods.length === 0;
            } catch (error) {
                console.error(error);
                submethodSelect.innerHTML = '<option value="">Error al cargar</option>';
            }
        } else {
            submethodSelect.innerHTML = '<option value="">Seleccione un submétodo</option>';
        }
    });
});

/*Color de Estado de la Orden*/
document.addEventListener("DOMContentLoaded", () => {
    const select = document.getElementById("order_status_id");

    function updateSelectColor() {
        const selectedOption = select.options[select.selectedIndex];
        const color = selectedOption.dataset.color || '#6c757d';
        select.style.color = color;
        select.style.borderColor = color;
    }

    // Al cargar
    updateSelectColor();

    // Al cambiar
    select.addEventListener("change", updateSelectColor);
});

document.addEventListener('DOMContentLoaded', function() {
    const categories = @json($categories);
    let selectedCategory = null;
    let itemIndex = 0;

    const categoryCards = document.querySelectorAll('.category-card');
    const servicesContainer = document.getElementById('servicesContainer');
    const servicesCards = document.getElementById('servicesCards');
    const orderItemsBody = document.getElementById('orderItemsBody');
    const clearSelectionBtn = document.getElementById('clearSelection');

    // Mostrar servicios de la categoría seleccionada
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            categoryCards.forEach(c => c.classList.remove('border-primary', 'shadow-lg'));
            this.classList.add('border-primary', 'shadow-lg');

            selectedCategory = this.dataset.categoryId;
            const category = categories.find(c => c.id == selectedCategory);
            renderServices(category.services);
        });
    });

    // Renderizar servicios
    function renderServices(services) {
        servicesContainer.style.display = 'block';
        servicesCards.innerHTML = '';

        if (services.length === 0) {
            servicesCards.innerHTML = '<p class="text-muted">No hay servicios en esta categoría.</p>';
            return;
        }

        services.forEach(service => {
            const imagePath = service.image 
                ? `/${service.image}` 
                : '/images/no-image.jpg';

            const card = document.createElement('div');
            card.className = 'col-md-4';
            card.innerHTML = `
                <div class="card h-100 shadow-sm service-card" 
                    data-id="${service.id}" 
                    data-name="${service.name}" 
                    data-price="${service.base_price}" 
                    style="cursor:pointer;">
                    
                    <img src="${imagePath}" width="100" height="100" class="mx-auto d-block mt-3 mb-2">
                    
                    <div class="card-body text-center">
                        <h6 class="card-title mb-1">${service.name}</h6>
                        <p class="text-muted mb-2">S/${parseFloat(service.base_price).toFixed(2)}</p>
                        <button type="button" class="btn btn-sm btn-outline-primary add-service">
                            <i class="bi bi-plus-circle"></i> Agregar
                        </button>
                    </div>
                </div>`;
            servicesCards.appendChild(card);
        });
    }

    // Agregar servicio a la tabla
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-service')) {
            const card = e.target.closest('.service-card');
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="hidden" name="order_items[${itemIndex}][service_id]" value="${id}">
                    ${name}
                </td>
                <td><input type="number" name="order_items[${itemIndex}][quantity]" class="form-control quantity" value="1" min="1" required></td>
                <td><input type="number" name="order_items[${itemIndex}][unit_price]" class="form-control unit-price" step="0.01" value="${price}" required></td>
                <td class="subtotal fw-semibold">S/${price.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;
            orderItemsBody.appendChild(row);
            itemIndex++;
            updateTotals();
        }
    });

    // Quitar filas
    document.addEventListener('click', e => {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            updateTotals();
        }
    });

    // Limpiar selección
    clearSelectionBtn.addEventListener('click', function() {
        categoryCards.forEach(c => c.classList.remove('border-primary', 'shadow-lg'));
        servicesContainer.style.display = 'none';
        servicesCards.innerHTML = '';
        selectedCategory = null;
    });

    // Actualizar totales
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('#orderItemsBody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const sub = qty * price;
            row.querySelector('.subtotal').innerText = 'S/' + sub.toFixed(2);
            subtotal += sub;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = subtotal - discount + tax;

        document.getElementById('subtotalDisplay').innerText = 'S/' + subtotal.toFixed(2);
        document.getElementById('discountDisplay').innerText = 'S/' + discount.toFixed(2);
        document.getElementById('taxDisplay').innerText = 'S/' + tax.toFixed(2);
        document.getElementById('totalDisplay').innerText = 'S/' + total.toFixed(2);
    }

    document.addEventListener('input', e => {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price') ||
            e.target.id === 'discount' || e.target.id === 'tax') {
            updateTotals();
        }
    });
});
</script>
@endpush
@endsection
