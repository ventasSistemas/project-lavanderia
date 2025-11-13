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
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
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
                    <div class="col-md-4 position-relative">
                        <label for="customerSearch" class="form-label fw-semibold">Cliente</label>
                        <input type="text" id="customerSearch" class="form-control mb-2" placeholder="Escribe para buscar cliente...">
                        <ul id="customerResults" class="list-group position-absolute w-100 shadow-sm" 
                            style="z-index: 1050; display: none; max-height: 180px; overflow-y: auto;">
                        </ul>
                        <input type="hidden" name="customer_id" id="customer_id" required>
                    </div>

                    <div class="col-md-4">
                        <label for="branch_id" class="form-label fw-semibold">Sucursal</label>

                        @if($user->role->name === 'admin')
                            <select name="branch_id" id="branch_id" class="form-select" required>
                                <option value="">-- Seleccionar Sucursal --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        @else
                            {{-- Mismo estilo visual que el select del estado --}}
                            <select class="form-select text-secondary" style="background-color:#f8f9fa; border-color:#ced4da;" disabled>
                                <option selected>{{ $user->branch->name }}</option>
                            </select>
                            <input type="hidden" name="branch_id" value="{{ $user->branch_id }}">
                        @endif
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
                    <div class="card-header text-white bg-warning fw-semibold">
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
                                <!--<tr><th>Pago:</th><td id="discountDisplay">S/0.00</td></tr>
                                <tr><th>Impuesto:</th><td id="taxDisplay">S/0.00</td></tr>-->
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-success bg-opacity-10 border-0 py-3">
                <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-25 text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <i class="fa-solid fa-cloud"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold text-success mb-0">Confirmar Orden</h5>
                    <small class="text-muted">Revisa los detalles antes de guardar</small>
                </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body bg-body-tertiary p-4">

                <!-- Información del Cliente -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-primary mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i> Información del Cliente
                    </h6>
                </div>
                <div class="card-body pt-0 small">
                    <div class="row mb-3">
                    <div class="col-md-6"><strong>Cliente:</strong> <span id="summaryCustomer">—</span></div>
                    <div class="col-md-6"><strong>Sucursal:</strong> <span id="summaryBranch">—</span></div>
                    </div>
                    <div class="mb-3"><strong>Estado del Pedido:</strong> <span id="summaryStatus">—</span></div>

                    <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1 fw-semibold">Fecha de Creación</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="{{ now()->format('d/m/Y H:i') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1 fw-semibold">Entrega Estimada</label>
                        <input type="datetime-local" id="delivery_date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    </div>
                </div>
                </div>

                <!-- Pago y Totales -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-success mb-0">
                    <i class="bi bi-cash-coin me-2"></i> Pago y Totales
                    </h6>
                </div>
                <div class="card-body pt-0 small">
                    <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                        <tr><th class="text-muted">Subtotal:</th><td id="subtotalConfirm" class="text-end">S/0.00</td></tr>
                        <tr><th class="text-muted">Descuento:</th><td id="discountConfirm" class="text-end">S/0.00</td></tr>
                        <tr><th class="text-muted">Impuesto:</th><td id="taxConfirm" class="text-end">S/0.00</td></tr>
                        <tr class="fw-bold border-top">
                            <th>Total:</th><td id="totalConfirm" class="text-end text-success fs-5">S/0.00</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>

                <!-- Detalles de Pago -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-secondary mb-0">
                    <i class="bi bi-credit-card me-2"></i> Detalles de Pago
                    </h6>
                </div>
                <div class="card-body pt-0 small">
                    <div class="mb-3">
                    <label class="form-label fw-semibold">Estado del Pago</label>
                    <select id="payment_status" name="payment_status" class="form-select form-select-sm">
                        <option value="pending" selected>Pendiente</option>
                        <option value="paid">Pagado Completo</option>
                        <option value="partial">Incompleto</option>
                    </select>
                    </div>

                    <div id="paymentDetails" style="display:none;">
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
                        <input type="number" step="0.01" id="payment_returned" name="payment_returned" class="form-control form-control-sm bg-light" value="0" readonly>
                        </div>
                        <div class="col-md-6">
                        <label class="form-label">Descuento (S/)</label>
                        <input type="number" step="0.01" id="discount" name="discount" class="form-control form-control-sm" value="0">
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- Notas -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold text-secondary mb-0">
                        <i class="bi bi-journal-text me-2"></i> Notas Adicionales
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <textarea id="notes" rows="3" class="form-control form-control-sm" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-white border-0 py-3">
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

        <!-- ESTILOS PERSONALIZADOS -->
        <style>
        #confirmOrderModal .modal-content {
            font-size: 0.95rem;
            background-color: #f9fafb;
        }
        #confirmOrderModal .card {
            transition: all 0.2s ease-in-out;
        }
        #confirmOrderModal .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
        }
        #confirmOrderModal .form-select,
        #confirmOrderModal .form-control {
            border-radius: 0.5rem;
        }
        #confirmOrderModal .card-header h6 {
            font-size: 0.95rem;
            letter-spacing: 0.3px;
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

/*Buscador de cliente y asignar*/
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('customerSearch');
    const resultsList = document.getElementById('customerResults');
    const hiddenInput = document.getElementById('customer_id');
    let debounceTimer;

    searchInput.addEventListener('input', function () {
        const term = this.value.trim();

        clearTimeout(debounceTimer);
        if (term.length < 2) {
            resultsList.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/admin/customers/search?term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    resultsList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(customer => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item list-group-item-action';
                            li.textContent = customer.full_name;
                            li.dataset.id = customer.id;

                            li.addEventListener('click', () => {
                                searchInput.value = customer.full_name;
                                hiddenInput.value = customer.id;
                                resultsList.style.display = 'none';
                            });

                            resultsList.appendChild(li);
                        });
                        resultsList.style.display = 'block';
                    } else {
                        resultsList.innerHTML = '<li class="list-group-item text-muted small text-center">No se encontraron clientes</li>';
                        resultsList.style.display = 'block';
                    }
                })
                .catch(err => console.error(err));
        }, 300); // retardo para evitar muchas peticiones
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const paymentStatus = document.getElementById('payment_status');
    const paymentDetails = document.getElementById('paymentDetails');
    const paymentAmount = document.getElementById('payment_amount');
    const discountInput = document.getElementById('discount');
    //const taxInput = document.getElementById('tax');
    const paymentReturnedInput = document.getElementById('payment_returned');

    const subtotalDisplay = document.getElementById('subtotalDisplay');
    //const discountDisplay = document.getElementById('discountDisplay');
    //const taxDisplay = document.getElementById('taxDisplay');
    const totalDisplay = document.getElementById('totalDisplay');

    const subtotalConfirm = document.getElementById('subtotalConfirm');
    const discountConfirm = document.getElementById('discountConfirm');
    //const taxConfirm = document.getElementById('taxConfirm');
    const totalConfirm = document.getElementById('totalConfirm');

    // Mostrar u ocultar la sección de pago según estado
    paymentStatus.addEventListener('change', function () {
        paymentDetails.style.display = (this.value === 'pending') ? 'none' : 'flex';
    });

    // Función para actualizar los totales dinámicamente
    function updateTotals() {
        const subtotal = parseFloat(subtotalDisplay?.innerText.replace('S/', '')) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        //const tax = parseFloat(taxInput.value) || 0;
        const payment = parseFloat(paymentAmount.value) || 0;

        // Calcular el total final de la orden
        let finalTotal = subtotal - discount;
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

        //discountDisplay.innerText = `S/${discount.toFixed(2)}`;
        //taxDisplay.innerText = `S/${tax.toFixed(2)}`;
        totalDisplay.innerText = `S/${finalTotal.toFixed(2)}`;

        subtotalConfirm.innerText = `S/${subtotal.toFixed(2)}`;
        discountConfirm.innerText = `S/${discount.toFixed(2)}`;
        //taxConfirm.innerText = `S/${tax.toFixed(2)}`;

        // Mostrar total final con estado de pago dinámico
        totalConfirm.innerHTML = `S/${finalTotal.toFixed(2)} <br>${mensajePago}`;
    }

    // Actualizar cada vez que cambien los valores
    [paymentAmount, discountInput].forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    // Abrir modal con resumen actualizado
    document.getElementById('openConfirmModal').addEventListener('click', function () {
        updateTotals(); // recalcula antes de mostrar
        const modal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
        modal.show();

        // Obtener los valores visibles
        const customerName =
            document.querySelector('#customerSearch')?.value?.trim() ||
            document.querySelector('#customerResults .active')?.textContent ||
            '—';

        let branchName = '—';
        const branchSelect = document.getElementById('branch_id');
        const branchHidden = document.querySelector('input[name="branch_id"]');

        // Si es admin (select activo)
        if (branchSelect) {
            branchName = branchSelect.selectedOptions[0]?.text || '—';
        } 
        // Si es empleado o manager (select deshabilitado)
        else if (branchHidden) {
            branchName = document.querySelector('select[disabled] option')?.textContent || '—';
        }

        const orderStatusName =
            document.getElementById('order_status_id').selectedOptions[0]?.text || '—';

        // Actualizar en el modal
        document.getElementById('summaryCustomer').textContent = customerName;
        document.getElementById('summaryBranch').textContent = branchName;
        document.getElementById('summaryStatus').textContent = orderStatusName;
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

/*Opciones de Metodos de Pago y Submetodos*/
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
        select.style.borderColor = "#ced4da"; // borde estándar Bootstrap
        select.style.backgroundColor = "#f8f9fa"; // gris claro Bootstrap
    }

    updateSelectColor();
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
    document.addEventListener('click', function (e) {
        if (e.target.closest('.add-service')) {
            const card = e.target.closest('.service-card');
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);

            // Buscar si ya existe una fila con ese service_id
            const existingRow = Array.from(orderItemsBody.querySelectorAll('tr')).find(row => {
                const serviceInput = row.querySelector('input[name*="[service_id]"]');
                return serviceInput && serviceInput.value === id;
            });

            if (existingRow) {
                // Si ya existe, aumentar cantidad
                const qtyInput = existingRow.querySelector('.quantity');
                qtyInput.value = parseInt(qtyInput.value) + 1;
            } else {
                // Si no existe, crear nueva fila
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
            }

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
        //const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = subtotal - discount;

        document.getElementById('subtotalDisplay').innerText = 'S/' + subtotal.toFixed(2);
        //document.getElementById('discountDisplay').innerText = 'S/' + discount.toFixed(2);
        //document.getElementById('taxDisplay').innerText = 'S/' + tax.toFixed(2);
        document.getElementById('totalDisplay').innerText = 'S/' + total.toFixed(2);
    }

    document.addEventListener('input', e => {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price') ||
            e.target.id === 'discount' || e.target.id) {
            updateTotals();
        }
    });
});
</script>
@endpush
@endsection
