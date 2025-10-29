@extends('admin.layouts.app')

@section('title', 'Punto de Venta')

@section('content_header')
<h1 class="text-primary fw-bold mb-0">
    <i class="fas fa-cash-register"></i> Punto de Venta (P.O.S)
</h1>
@stop

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        {{-- PANEL IZQUIERDO - Categorías y Productos --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    {{-- Buscador --}}
                    <input type="text" id="buscar_producto" class="form-control mb-4" placeholder="Busca aquí">

                    <div id="contenedor_pos">
                        {{-- Vista de Categorías --}}
                        <div id="vista_categorias">
                            <div class="row g-3" id="lista_categorias">
                                @foreach($categorias as $categoria)
                                <div class="col-md-3 col-sm-6">
                                    <div class="card text-center categoria-card h-100" data-id="{{ $categoria->id }}">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                            @if($categoria->image && file_exists(public_path($categoria->image)))
                                                <img src="{{ asset($categoria->image) }}" 
                                                    alt="{{ $categoria->name }}" 
                                                    class="img-fluid rounded mb-2" 
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('images/default_category.png') }}" 
                                                    alt="Sin imagen" 
                                                    class="img-fluid rounded mb-2" 
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            @endif
                                            <h6 class="fw-semibold mt-2">{{ $categoria->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Vista de Productos --}}
                        <div id="vista_productos" class="d-none">
                            <button class="btn btn-outline-secondary mb-3" id="btnVolverCategorias">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>

                            <div class="row g-3" id="productos_categoria">
                                @foreach($categoria->products as $producto)
                                <div class="col-md-3 col-sm-6">
                                    <div class="card text-center service-card h-100" data-id="{{ $producto->id }}">
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                            @if($producto->image && file_exists(public_path($producto->image)))
                                                <img src="{{ asset($producto->image) }}" 
                                                    alt="{{ $producto->name }}" 
                                                    class="img-fluid rounded mb-2" 
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('images/default_product.png') }}" 
                                                    alt="Sin imagen" 
                                                    class="img-fluid rounded mb-2" 
                                                    style="width: 100px; height: 100px; object-fit: cover;">
                                            @endif
                                            <h6 class="fw-semibold mt-2">{{ $producto->name }}</h6>
                                            <small class="text-muted">S/ {{ number_format($producto->price, 2) }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PANEL DERECHO - Carrito --}}
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <select class="form-select me-2" id="cliente_id">
                            <option value="">Seleccione un cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" id="btnAgregarCliente">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>

                    <div class="d-flex mb-3">
                        <input type="text" id="numero_orden" class="form-control me-2" value="ORD-0001" readonly>
                        <input type="date" id="fecha_orden" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Tabla carrito --}}
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>PRODUCTO</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>SUBTOTAL</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tabla_carrito"></tbody>
                        </table>
                    </div>

                    {{-- Total --}}
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="fw-bold text-secondary">Bruto total</span>
                        <span class="fw-bold text-success fs-5" id="bruto_total">S/ 0.00</span>
                    </div>

                    {{-- Botones --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-danger me-2" id="btnLimpiar">
                            <i class="fas fa-trash-alt"></i> Limpiar todo
                        </button>
                        <button class="btn btn-primary" id="btnGuardar">
                            <i class="fas fa-save"></i> Guardar Continuar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Estilos personalizados --}}
<style>
    .categoria-card, .producto-card {
        border: 1px dashed #d0d0d0;
        border-radius: 12px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .categoria-card:hover, .producto-card:hover {
        background-color: #f0f8ff;
        border-color: #0d6efd;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    #lista_categorias, #productos_categoria {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 8px;
    }
</style>
@stop

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabla = document.getElementById('tabla_carrito');
    const totalBruto = document.getElementById('bruto_total');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const btnGuardar = document.getElementById('btnGuardar');

    const vistaCategorias = document.getElementById('vista_categorias');
    const vistaProductos = document.getElementById('vista_productos');
    const productosContainer = document.getElementById('productos_categoria');
    const btnVolverCategorias = document.getElementById('btnVolverCategorias');

    const categorias = @json($categorias);
    let carrito = [];

    // --- Mostrar productos al hacer clic en una categoría ---
    document.querySelectorAll('.categoria-card').forEach(card => {
        card.addEventListener('click', () => {
            const categoriaId = card.dataset.id;
            const categoria = categorias.find(c => c.id == categoriaId);

            productosContainer.innerHTML = '';

            if (categoria && categoria.products.length > 0) {
                categoria.products.forEach(prod => {
                    productosContainer.insertAdjacentHTML('beforeend', `
                        <div class="col-md-3 col-sm-6">
                            <div class="card text-center producto-card h-100" 
                                data-id="${prod.id}" 
                                data-nombre="${prod.name}" 
                                data-precio="${prod.price}">
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <img src="${prod.image ? `/${prod.image}` : '/images/default_product.png'}" 
                                        alt="${prod.name}" 
                                        class="img-fluid rounded mb-2" 
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                    <h6 class="fw-semibold mt-2">${prod.name}</h6>
                                    <small class="text-muted">S/ ${parseFloat(prod.price).toFixed(2)}</small>
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else {
                productosContainer.innerHTML = `<div class="text-center text-muted">No hay productos en esta categoría.</div>`;
            }

            vistaCategorias.classList.add('d-none');
            vistaProductos.classList.remove('d-none');
            agregarEventosProductos();
        });
    });

    // --- Volver a categorías ---
    btnVolverCategorias.addEventListener('click', () => {
        vistaProductos.classList.add('d-none');
        vistaCategorias.classList.remove('d-none');
    });

    // --- Agregar productos al carrito ---
    function agregarEventosProductos() {
        document.querySelectorAll('.producto-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                const nombre = card.dataset.nombre;
                const precio = parseFloat(card.dataset.precio);

                const existente = carrito.find(item => item.id === id);
                if (existente) {
                    existente.cantidad++;
                } else {
                    carrito.push({ id, nombre, cantidad: 1, precio });
                }
                renderCarrito();
            });
        });
    }

    // --- Render del carrito ---
    function renderCarrito() {
        tabla.innerHTML = '';
        let total = 0;
        carrito.forEach(item => {
            const subtotal = item.cantidad * item.precio;
            total += subtotal;

            tabla.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${item.nombre}</td>
                    <td><input type="number" class="form-control form-control-sm cantidad" data-id="${item.id}" value="${item.cantidad}" min="1" style="width: 70px;"></td>
                    <td>S/ ${item.precio.toFixed(2)}</td>
                    <td>S/ ${subtotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger eliminar" data-id="${item.id}"><i class="fas fa-times"></i></button></td>
                </tr>
            `);
        });
        totalBruto.textContent = `S/ ${total.toFixed(2)}`;
        agregarEventosCarrito();
    }

    // --- Eventos del carrito ---
    function agregarEventosCarrito() {
        document.querySelectorAll('.eliminar').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.target.closest('button').dataset.id;
                carrito = carrito.filter(item => item.id !== id);
                renderCarrito();
            });
        });

        document.querySelectorAll('.cantidad').forEach(input => {
            input.addEventListener('change', e => {
                const id = e.target.dataset.id;
                const nuevoValor = parseInt(e.target.value);
                carrito = carrito.map(item => item.id === id ? { ...item, cantidad: nuevoValor } : item);
                renderCarrito();
            });
        });
    }

    // --- Limpiar carrito ---
    btnLimpiar.addEventListener('click', () => {
        Swal.fire({
            title: '¿Vaciar carrito?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then(res => {
            if (res.isConfirmed) {
                carrito = [];
                renderCarrito();
            }
        });
    });

    // --- Guardar pedido ---
    btnGuardar.addEventListener('click', () => {
        if (carrito.length === 0) {
            Swal.fire('Atención', 'Debe agregar al menos un producto.', 'info');
            return;
        }
        Swal.fire('Guardado', 'El pedido fue registrado con éxito.', 'success');
    });
});
</script>
@endpush