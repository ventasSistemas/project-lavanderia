@extends('admin.layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Nueva Orden</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
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
                        <select name="order_status_id" id="order_status_id" class="form-select">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="discount" class="form-label fw-semibold">Descuento</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0">
                    </div>

                    <div class="col-md-6">
                        <label for="tax" class="form-label fw-semibold">Impuesto</label>
                        <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="0">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold">Notas</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Optional notes about this order..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Selección de Categorías y Servicios --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light fw-semibold">Orden de Servicios</div>
            <div class="card-body">
                {{-- Categorías de Servicio --}}
                <h6 class="fw-bold mb-3">Selecciona una categoría:</h6>
                <div class="row g-3 mb-4">
                    @foreach($categories as $category)
                        <div class="col-md-3">
                            <div class="card category-card shadow-sm h-100" 
                                data-category-id="{{ $category->id }}" 
                                style="cursor:pointer;">
                                <img src="{{ asset($category->image ?? 'images/no-image.png') }}" 
                                    width="180" height="180" class="mx-auto d-block mt-3 mb-2">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-1">{{ $category->name }}</h6>
                                    <small class="text-muted">{{ Str::limit($category->description, 80) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Contenedor de servicios filtrados --}}
                <div id="servicesContainer" class="mt-4" style="display:none;">
                    <h6 class="fw-bold mb-3">Servicios disponibles:</h6>
                    <div class="row g-3" id="servicesCards"></div>
                </div>

                {{-- Tabla dinámica de orden --}}
                <div class="mt-4">
                    <h6 class="fw-bold mb-2">Servicios agregados:</h6>
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Servicio</th>
                                <th width="120">Cantidad</th>
                                <th width="150">Precio Unit. ($)</th>
                                <th width="150">SubTotal ($)</th>
                                <th width="60">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsBody"></tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <button type="button" id="clearSelection" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Limpiar selección
                    </button>
                </div>
            </div>
        </div>


        {{-- Totals --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table">
                            <tr>
                                <th>Subtotal:</th>
                                <td id="subtotalDisplay">$0.00</td>
                            </tr>
                            <tr>
                                <th>Descuento:</th>
                                <td id="discountDisplay">$0.00</td>
                            </tr>
                            <tr>
                                <th>Impuesto:</th>
                                <td id="taxDisplay">$0.00</td>
                            </tr>
                            <tr class="fw-bold">
                                <th>Total:</th>
                                <td id="totalDisplay">$0.00</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Registrar Orden
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Script for Dynamic Items --}}
@push('scripts')
<script>
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
            card.className = 'col-md-2';
            card.innerHTML = `
                <div class="card h-100 shadow-sm service-card" 
                    data-id="${service.id}" 
                    data-name="${service.name}" 
                    data-price="${service.base_price}" 
                    style="cursor:pointer;">
                    
                    <img src="${imagePath}" width="100" height="100" class="mx-auto d-block mt-3 mb-2">
                    
                    <div class="card-body text-center">
                        <h6 class="card-title mb-1">${service.name}</h6>
                        <p class="text-muted mb-2">$${parseFloat(service.base_price).toFixed(2)}</p>
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
                <td class="subtotal fw-semibold">$${price.toFixed(2)}</td>
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
            row.querySelector('.subtotal').innerText = '$' + sub.toFixed(2);
            subtotal += sub;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = subtotal - discount + tax;

        document.getElementById('subtotalDisplay').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('discountDisplay').innerText = '$' + discount.toFixed(2);
        document.getElementById('taxDisplay').innerText = '$' + tax.toFixed(2);
        document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
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
