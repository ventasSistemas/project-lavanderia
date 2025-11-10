@extends('admin.layouts.app')

@section('title', 'Punto de Venta')

@section('content')
<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fas fa-cash-register text-danger"></i> Punto de Venta (P.O.S)
            </h4>
            <p class="text-muted small mb-0">Gestiona tus ventas en tiempo real: selecciona servicios, registra pagos y genera órdenes del negocio con facilidad.</p>
        </div>
    </div>

    {{-- Encabezado de página --}}
    <div class="row">
        {{-- PANEL IZQUIERDO - Categorías y Productos --}}
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    {{-- Buscador --}}
                    <input type="text" id="buscar_producto" class="form-control mb-4" placeholder="Buscar categoria...">

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
                    <!-- Buscar orden de servicio -->
                    <div class="mb-3 position-relative">
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-light">SRV-</span>
                            <input type="text" id="buscar_orden_servicio" class="form-control" placeholder="0001">
                        </div>

                        <ul id="resultados_ordenes" 
                            class="list-group position-absolute w-100 shadow-sm"
                            style="top: 40px; z-index: 1050; display:none; max-height:220px; overflow-y:auto;">
                        </ul>
                    </div>

                    <!-- Orden de producto -->
                    <div class="d-flex mb-3">
                        <input type="text" id="numero_orden" class="form-control me-2" readonly>
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

<!-- Modal Confirmar Venta (Diseño Moderno sin Tabla) -->
<div class="modal fade" id="modalConfirmarVenta" tabindex="-1" aria-labelledby="modalConfirmarVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-altura-custom">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Encabezado -->
      <div class="modal-header bg-gradient bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold" id="modalConfirmarVentaLabel">
          <i class="fas fa-receipt me-2"></i> Resumen de la Venta
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body p-4">
        <!-- Información de la Orden -->
        <div class="mb-4">
          <div class="d-flex justify-content-between flex-wrap">
            <div>
              <h6 class="text-muted mb-1">Número de Orden</h6>
              <p class="fw-semibold text-dark mb-0" id="numeroOrdenModal">ORD-0000</p>
            </div>
            <div>
              <h6 class="text-muted mb-1">Fecha de Registro</h6>
              <p class="fw-semibold text-dark mb-0" id="fechaOrdenModal">{{ date('Y-m-d') }}</p>
            </div>
          </div>
          <hr class="my-3">
        </div>

        <!-- Lista de Productos -->
        <div id="lista_detalles_modal" class="px-2">
          <!-- Aquí se agregan los productos dinámicamente -->
        </div>

        <!-- Total -->
        <!-- Sección de Totales Detallados -->
        <div class="mt-3">
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span id="subtotal_modal">S/ 0.00</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Descuento:</span>
                <span id="descuento_modal">S/ 0.00</span>
            </div>
            <!--
            <div class="d-flex justify-content-between">
                <span>Impuesto:</span>
                <span id="impuesto_modal">S/ 0.00</span>
            </div>-->
            <div class="d-flex justify-content-between fw-bold border-top mt-2 pt-2">
                <span>Total:</span>
                <span id="total_modal">S/ 0.00</span>
            </div>
            <hr>
            <!-- Selección de método de pago -->
            <div class="mt-3">
            <label class="fw-semibold">Método de pago:</label>
            <select id="metodoPagoSelect" class="form-select">
                <option value="">Seleccione un método</option>
            </select>
            </div>

            <!-- Submétodo -->
            <div class="mt-3">
            <label class="fw-semibold">Submétodo:</label>
            <select id="submetodoPagoSelect" class="form-select">
                <option value="">Seleccione un submétodo</option>
            </select>
            </div>

            <!-- Monto recibido -->
            <div class="mt-3">
            <label class="fw-semibold">Monto recibido:</label>
            <input type="number" id="montoRecibidoInput" class="form-control" min="0" step="0.01" placeholder="Ingrese monto recibido">
            </div>

            <!-- Vuelto dinámico -->
            <div class="d-flex justify-content-between mt-2">
            <span>Vuelto:</span>
            <span id="vuelto_modal" class="fw-bold text-success">S/ 0.00</span>
            </div>

            <!--
            <div class="d-flex justify-content-between">
                <span>Submétodo:</span>
                <span id="submetodo_pago_modal">---</span>
            </div>-->
        </div>
      </div>

      <!-- Pie -->
      <div class="modal-footer bg-light rounded-bottom-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-arrow-left"></i> Volver
        </button>
        <button type="button" class="btn btn-success" id="btnConfirmarVentaFinal">
          <i class="fas fa-check-circle"></i> Confirmar y Registrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detalle Orden de Servicio -->
<div class="modal fade" id="modalDetalleOrdenServicio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-info text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="fas fa-soap me-2"></i> Detalle de la Orden de Servicio
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="detalleOrdenServicioBody"></div>

        <!-- Entregado por -->
        <div class="mt-3 p-2 bg-light rounded border">
          <p class="mb-0 fw-semibold text-secondary">
            <i class="fas fa-user-check text-success me-1"></i>
            <span id="entregadoPorTexto">Aún no entregado</span>
          </p>
        </div>
      </div>

      <div class="modal-footer bg-light rounded-bottom-4">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button class="btn btn-success" id="btnActualizarOrdenServicio">
          <i class="fas fa-save"></i> Guardar cambios
        </button>
      </div>
    </div>
  </div>
</div>


<style>
.modal-altura-custom {
    max-width: 600px; 
    height: 90vh;   
}

.modal-altura-custom .modal-content {
  height: 100%;
}

</style>


@endsection


@push('scripts')
<script>

let carrito = [];
let ordenSeleccionada = null;

/*Buscar Categoria*/
document.addEventListener("DOMContentLoaded", () => {
    const inputBuscar = document.getElementById("buscar_producto");
    const listaCategorias = document.getElementById("lista_categorias");
    const categorias = @json($categorias); // 👈 tenemos productos dentro
    const cards = listaCategorias.querySelectorAll(".categoria-card");

    // Mensaje cuando no hay coincidencias
    const mensajeNoEncontrado = document.createElement("div");
    mensajeNoEncontrado.id = "mensaje_no_encontrado";
    mensajeNoEncontrado.className = "text-center text-muted mt-3 fw-semibold";
    mensajeNoEncontrado.style.display = "none";
    mensajeNoEncontrado.textContent = "😕 No se encontraron coincidencias.";
    listaCategorias.parentElement.appendChild(mensajeNoEncontrado);

    inputBuscar.addEventListener("input", () => {
        const texto = inputBuscar.value.trim().toLowerCase();
        let coincidencias = 0;

        if (texto === "") {
            // Mostrar todas si no hay texto
            cards.forEach(cat => cat.closest(".col-md-3").classList.remove("d-none"));
            mensajeNoEncontrado.style.display = "none";
            return;
        }

        cards.forEach(cat => {
            const categoriaId = cat.dataset.id;
            const categoria = categorias.find(c => c.id == categoriaId);

            // Buscar coincidencia por nombre de categoría o producto
            const nombreCategoria = categoria.name.toLowerCase();
            const coincideCategoria = nombreCategoria.includes(texto);

            const coincideProducto = categoria.products.some(prod =>
                prod.name.toLowerCase().includes(texto)
            );

            const mostrar = coincideCategoria || coincideProducto;
            cat.closest(".col-md-3").classList.toggle("d-none", !mostrar);
            if (mostrar) coincidencias++;
        });

        mensajeNoEncontrado.style.display = coincidencias === 0 ? "block" : "none";
    });
});


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
            if (res.message) {
                modal.hide();
                Swal.fire('Éxito', `${res.message}\nN° de Orden: ${res.order_number || ''}`, 'success');

                // --- Reiniciar carrito ---
                carrito = [];
                renderCarrito();

                // --- Obtener siguiente número de orden ---
                fetch('/admin/pos/next-order-number')
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('numero_orden').value = data.next_order_number;
                        document.getElementById('numeroOrdenModal').textContent = data.next_order_number;
                    });
            } else {
                Swal.fire('Error', res.error || 'No se pudo guardar la venta', 'error');
            }
        });
    });



    // --- Buscar órdenes de servicio ---
    const inputBuscarOrden = document.getElementById('buscar_orden_servicio');
    const resultadosOrdenes = document.getElementById('resultados_ordenes');
    const prefixSpan = document.querySelector('.input-group-text'); 

    inputBuscarOrden.addEventListener('input', async () => {
        let numero = inputBuscarOrden.value.replace(/\D/g, '');

        if (numero.length < 2) {
            resultadosOrdenes.style.display = 'none';
            return;
        }

        // Tomamos el prefijo actual directamente del span
        const prefix = prefixSpan.textContent.trim();
        const query = `${prefix}${numero}`; 

        try {
            const res = await fetch(`/admin/pos/buscar-orden?q=${query}`);
            const data = await res.json();

            resultadosOrdenes.innerHTML = '';
            if (data.length > 0) {
                data.forEach(o => {
                    resultadosOrdenes.insertAdjacentHTML('beforeend', `
                        <li class="list-group-item list-group-item-action" data-id="${o.id}">
                            <strong>${o.order_number}</strong> - ${o.customer_name}
                        </li>
                    `);
                });
                resultadosOrdenes.style.display = 'block';
            } else {
                resultadosOrdenes.innerHTML = '<li class="list-group-item text-muted">No se encontraron resultados</li>';
                resultadosOrdenes.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al buscar orden:', error);
            resultadosOrdenes.innerHTML = '<li class="list-group-item text-danger">Error al buscar</li>';
            resultadosOrdenes.style.display = 'block';
        }
    });

    // --- Seleccionar una orden ---
    resultadosOrdenes.addEventListener('click', async (e) => {
        const li = e.target.closest('li[data-id]');
        if (!li) return;
        const id = li.dataset.id;

        const res = await fetch(`/admin/pos/orden/${id}`);
        const data = await res.json();

        if (data.error) {
            Swal.fire('Error', data.error, 'error');
            return;
        }

        // Mostrar número y cliente
        document.getElementById('numero_orden').value = data.order_number;

        // Abrir modal detalle
        mostrarModalDetalleOrden(data);
    });

async function mostrarModalDetalleOrden(data) {
    const contenedor = document.getElementById('detalleOrdenServicioBody');

    // --- Obtener estados de orden ---
    const estadosResponse = await fetch('/admin/pos/order-statuses');
    const estados = await estadosResponse.json();

    // --- Obtener métodos de pago y submétodos ---
    const metodosResponse = await fetch('/admin/pos/payment-methods');
    const metodos = await metodosResponse.json();

    // --- Estado actual ---
    const estadoActual = data.order_status?.toLowerCase() || 'pendiente';

    // --- Opciones de estado de orden ---
    let opcionesEstados = '';
    if (estadoActual === 'terminado') {
        const estadosFiltrados = estados.filter(est =>
            ['terminado', 'entregado'].includes(est.name.toLowerCase())
        );
        opcionesEstados = estadosFiltrados.map(est => `
            <option value="${est.id}" ${est.name.toLowerCase() === estadoActual ? 'selected' : ''}>
                ${est.name}
            </option>
        `).join('');
    } else {
        const estadoSolo = estados.find(est => est.name.toLowerCase() === estadoActual);
        opcionesEstados = estadoSolo
            ? `<option value="${estadoSolo.id}" selected>${estadoSolo.name}</option>`
            : '<option selected>Sin estado</option>';
    }

    // --- Opciones de estado de pago ---
    const estadosPago = [
        { value: 'pending', label: 'Pendiente' },
        { value: 'partial', label: 'Pago incompleto' },
        { value: 'paid', label: 'Pagado completo' }
    ];

    const opcionesPago = estadosPago.map(ep => `
        <option value="${ep.value}" ${ep.value === data.payment_status ? 'selected' : ''}>
            ${ep.label}
        </option>
    `).join('');

    // --- Opciones de métodos de pago ---
    let opcionesMetodos = '<option value="">No especificado</option>';
    if (metodos.length > 0) {
        opcionesMetodos += metodos.map(m => `
            <option value="${m.id}" ${m.id === data.payment_method_id ? 'selected' : ''}>
                ${m.name}
            </option>
        `).join('');
    }
    if (!data.payment_method_id) {
        opcionesMetodos = opcionesMetodos.replace(
            '<option value="">No especificado</option>',
            '<option value="" selected>No especificado</option>'
        );
    }

    // --- Opciones de submétodo ---
    let opcionesSubmetodos = '<option value="">No especificado</option>';
    const metodoActual = metodos.find(m => m.id === data.payment_method_id);
    if (metodoActual && metodoActual.submethods?.length) {
        opcionesSubmetodos += metodoActual.submethods.map(s => `
            <option value="${s.id}" ${s.id === data.payment_submethod_id ? 'selected' : ''}>
                ${s.name}
            </option>
        `).join('');
    }
    if (!data.payment_submethod_id) {
        opcionesSubmetodos = opcionesSubmetodos.replace(
            '<option value="">No especificado</option>',
            '<option value="" selected>No especificado</option>'
        );
    }

    // --- Cálculos ---
    const subtotal = Number(data.final_total) + Number(data.discount || 0) - Number(data.tax || 0);
    const restante = Math.max(0, Number(data.final_total) - Number(data.payment_amount || 0));
    const vuelto = Math.max(0, Number(data.payment_amount || 0) - Number(data.final_total || 0));

    

    // --- Render modal ---
    contenedor.innerHTML = `
        <div class="mb-3">
            <p><strong>Número de Orden:</strong> ${data.order_number}</p>
            <p><strong>Cliente:</strong> ${data.customer_name}</p>
            <p><strong>Fecha Actualización:</strong> ${data.updated_at}</p>
        </div>

        <div class="mt-2">
            <label class="fw-semibold"><i class="fas fa-clipboard-check me-1"></i> Estado de Orden:</label>
            <select id="estadoOrdenSelect" class="form-select" ${estadoActual !== 'terminado' ? 'disabled' : ''}>
                ${opcionesEstados}
            </select>
        </div>

        <div class="mt-3">
            <label class="fw-semibold"><i class="fas fa-money-bill-wave me-1"></i> Estado de Pago:</label>
            <select id="estadoPagoSelect" class="form-select">
                ${opcionesPago}
            </select>
        </div>

        <div class="mt-3">
            <label class="fw-semibold"><i class="fas fa-credit-card me-1"></i> Método de Pago:</label>
            <select id="metodoPagoServicioSelect" class="form-select">
                ${opcionesMetodos}
            </select>
        </div>

        <div class="mt-3">
            <label class="fw-semibold"><i class="fas fa-hand-holding-usd me-1"></i> Submétodo de Pago:</label>
            <select id="submetodoPagoServicioSelect" class="form-select">
                ${opcionesSubmetodos}
            </select>
        </div>

        <hr>
        <h6 class="fw-semibold text-primary mt-3">Servicios:</h6>
        ${data.items.map(i => `
            <div class="border-bottom py-2 d-flex justify-content-between">
                <span>${i.service_name}</span>
                <span>${i.quantity} × S/ ${Number(i.unit_price).toFixed(2)}</span>
            </div>
        `).join('')}

        <div class="mt-3 p-3 bg-light rounded">
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span>S/ ${subtotal.toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Descuento:</span>
                <span>- S/ ${Number(data.discount || 0).toFixed(2)}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-semibold text-dark">
                <span>Total:</span>
                <span>S/ ${Number(data.final_total || 0).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between text-danger fw-semibold mt-2">
                <span>Faltante:</span>
                <span>S/ ${restante.toFixed(2)}</span>
            </div>
        </div>

        <div class="mt-4">
            <label class="fw-semibold"><i class="fa-solid fa-money-check-dollar me-1"></i> Monto Pagado:</label>
            <div class="input-group">
                <input type="number" id="montoPagadoInput" class="form-control" 
                    min="0" value="${Number(data.payment_amount || 0).toFixed(2)}" step="0.01">
                <button class="btn btn-outline-success" type="button" id="btnPagoCompleto">
                    <i class="fas fa-coins"></i> Pago Completo
                </button>
            </div>
        </div>
    `;

    // --- Campo dinámico para pago restante si está incompleto ---
    if (data.payment_status === 'partial') {
        contenedor.innerHTML += `
            <div class="mt-3" id="pagoRestanteContainer">
                <label class="fw-semibold text-danger">
                    <i class="fas fa-hand-holding-dollar me-1"></i> Ingresar pago restante:
                </label>
                <input type="number" id="montoRestanteInput" class="form-control" 
                    min="0" max="${restante}" step="0.01" placeholder="Ejemplo: ${restante.toFixed(2)}">
                <div class="form-text text-muted">
                    El monto restante será registrado en caja y cambiará el estado a 'Pagado completo'.
                </div>
            </div>
        `;

            // --- Detectar si el pago restante completa el total ---
            const montoPagadoInput = document.getElementById('montoPagadoInput');
            const montoRestanteInput = document.getElementById('montoRestanteInput');
            const estadoPagoSelect = document.getElementById('estadoPagoSelect');
            const estadoOrdenSelect = document.getElementById('estadoOrdenSelect');
            const btnPagoCompleto = document.getElementById('btnPagoCompleto');

            // Si el usuario ingresa un monto restante
            montoRestanteInput?.addEventListener('input', () => {
                const total = parseFloat(data.final_total) || 0;
                const pagoActual = parseFloat(montoPagadoInput.value) || 0;
                const restanteNuevo = parseFloat(montoRestanteInput.value) || 0;
                const suma = pagoActual + restanteNuevo;

                if (suma >= total) {
                    estadoPagoSelect.value = 'paid';
                    // Si el estado actual es terminado, lo pasamos a entregado automáticamente
                    if (estadoOrdenSelect.options.length > 1) {
                        const entregado = Array.from(estadoOrdenSelect.options).find(
                            opt => opt.textContent.toLowerCase() === 'entregado'
                        );
                        if (entregado) estadoOrdenSelect.value = entregado.value;
                    }

                    Swal.fire({
                        icon: 'info',
                        title: 'Pago completo detectado',
                        text: 'El monto total ha sido cubierto. La orden se marcará como entregada.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    estadoPagoSelect.value = 'partial';
                }
            });

            // Si el usuario presiona el botón “Pago Completo”
            btnPagoCompleto.addEventListener('click', () => {
                montoPagadoInput.value = parseFloat(data.final_total).toFixed(2);
                if (montoRestanteInput) montoRestanteInput.value = '';
                estadoPagoSelect.value = 'paid';

                if (estadoOrdenSelect.options.length > 1) {
                    const entregado = Array.from(estadoOrdenSelect.options).find(
                        opt => opt.textContent.toLowerCase() === 'entregado'
                    );
                    if (entregado) estadoOrdenSelect.value = entregado.value;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Pago completado',
                    text: 'El monto total ha sido cubierto y la orden será marcada como entregada.',
                    timer: 1800,
                    showConfirmButton: false
                });
            });
    }

    // --- Función auxiliar ---
    function calcularVuelto() {
        const monto = parseFloat(document.getElementById('montoPagadoInput').value) || 0;
        const total = parseFloat(data.final_total) || 0;
        return Math.max(0, monto - total);
    }

    // --- Guardar cambios ---
    const btnGuardar = document.getElementById('btnActualizarOrdenServicio');
    btnGuardar.disabled = estadoActual !== 'terminado';
    btnGuardar.onclick = async () => {
        const montoPagadoActual = parseFloat(document.getElementById('montoPagadoInput').value) || 0;
        const montoRestante = parseFloat(document.getElementById('montoRestanteInput')?.value) || 0;
        const nuevoPagoTotal = montoPagadoActual + montoRestante;

        const payload = {
            order_status_id: document.getElementById('estadoOrdenSelect').value || null,
            payment_status: document.getElementById('estadoPagoSelect').value,
            payment_amount: nuevoPagoTotal,
            payment_method_id: document.getElementById('metodoPagoServicioSelect').value || null,
            payment_submethod_id: document.getElementById('submetodoPagoServicioSelect').value || null,
            payment_returned: calcularVuelto(),
            remaining_payment: parseFloat(document.getElementById('montoRestanteInput')?.value) || 0
        };

        const res = await fetch(`/admin/pos/orden/${data.id}/actualizar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        if (result.success) {
            Swal.fire({
                title: 'Éxito',
                text: result.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                modal.hide();
                location.reload();
            });
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    };

    // --- Control de estados ---
    const estadoOrdenSelect = document.getElementById('estadoOrdenSelect');
    if (estadoActual === 'terminado') {
        estadoOrdenSelect.addEventListener('change', e => {
            const texto = e.target.options[e.target.selectedIndex].text.trim().toLowerCase();
            btnGuardar.disabled = texto !== 'entregado';
        });
    }

    // --- Submétodos dinámicos ---
    document.getElementById('metodoPagoServicioSelect').addEventListener('change', e => {
        const metodoSeleccionado = metodos.find(m => m.id == e.target.value);
        const subSelect = document.getElementById('submetodoPagoServicioSelect');
        subSelect.innerHTML = metodoSeleccionado
            ? metodoSeleccionado.submethods.map(s => `<option value="${s.id}">${s.name}</option>`).join('')
            : '<option value="">-- Sin submétodos --</option>';
    });

    // --- Actualización dinámica de montos ---
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleOrdenServicio'));
    const inputMontoPagado = document.getElementById('montoPagadoInput');
    const estadoPagoSelect = document.getElementById('estadoPagoSelect');
    const btnPagoCompleto = document.getElementById('btnPagoCompleto');

    if (btnPagoCompleto) {
        btnPagoCompleto.addEventListener('click', () => {
            inputMontoPagado.value = Number(data.final_total || 0).toFixed(2);
            inputMontoPagado.dispatchEvent(new Event('input'));
        });
    }

    inputMontoPagado.addEventListener('input', () => {
        const monto = parseFloat(inputMontoPagado.value) || 0;
        const total = parseFloat(data.final_total) || 0;

        if (monto === 0) estadoPagoSelect.value = 'pending';
        else if (monto < total) estadoPagoSelect.value = 'partial';
        else estadoPagoSelect.value = 'paid';
    });

    modal.show();
}

    // Guardar y mostrar modal
    btnGuardar.addEventListener('click', () => {
        if (carrito.length === 0) {
            Swal.fire('Carrito vacío', 'Agrega productos antes de continuar', 'warning');
            return;
        }

        // Calcular totales
        let subtotal = 0;
        carrito.forEach(item => {
            subtotal += item.cantidad * item.precio;
        });

        let descuento = 0; 
        //let impuesto = 0; 
        //let total = subtotal - descuento + impuesto;
        let total = subtotal - descuento;
        let montoRecibido = total; 
        let vuelto = montoRecibido - total;
        let metodoPagoSeleccionadoNombre = "Efectivo"; 
        let submetodoPagoSeleccionadoNombre = "N/A"; 

        // Mostrar datos básicos en el modal
        document.getElementById('numeroOrdenModal').textContent = document.getElementById('numero_orden').value;
        document.getElementById('fechaOrdenModal').textContent = document.getElementById('fecha_orden').value;
        document.getElementById('subtotal_modal').textContent = `S/ ${subtotal.toFixed(2)}`;
        document.getElementById('descuento_modal').textContent = `S/ ${descuento.toFixed(2)}`;
        //document.getElementById('impuesto_modal').textContent = `S/ ${impuesto.toFixed(2)}`;
        document.getElementById('total_modal').textContent = `S/ ${total.toFixed(2)}`;
        document.getElementById('vuelto_modal').textContent = `S/ ${vuelto.toFixed(2)}`;
        //document.getElementById('submetodo_pago_modal').textContent = submetodoPagoSeleccionadoNombre;

        // Rellenar productos en el modal
        const listaModal = document.getElementById('lista_detalles_modal');
        listaModal.innerHTML = '';

        carrito.forEach(item => {
            const subtotalItem = item.cantidad * item.precio;

            listaModal.insertAdjacentHTML('beforeend', `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <h6 class="fw-semibold mb-1 text-dark">${item.nombre}</h6>
                        <small class="text-muted">Cantidad: ${item.cantidad} × S/ ${item.precio.toFixed(2)}</small>
                    </div>
                    <span class="fw-bold text-success">S/ ${subtotalItem.toFixed(2)}</span>
                </div>
            `);
        });

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmarVenta'));
        modal.show();

        // Confirmar venta
        const btnConfirmar = document.getElementById('btnConfirmarVentaFinal');
        btnConfirmar.onclick = () => {
            const data = {
                order_number: document.getElementById('numero_orden').value,
                sale_date: document.getElementById('fecha_orden').value,
                subtotal,
                descuento,
                //impuesto,
                total,
                amount_received: parseFloat(document.getElementById('montoRecibidoInput').value) || 0,
                change_given: parseFloat(document.getElementById('vuelto_modal').textContent.replace('S/', '')) || 0,
                payment_method_id: document.getElementById('metodoPagoSelect').value || null,
                payment_submethod_id: document.getElementById('submetodoPagoSelect').value || null,
                items: carrito.map(item => ({
                    id: item.id,
                    cantidad: item.cantidad,
                    precio: item.precio
                }))
            };


            fetch('/admin/pos/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {

                if (res.success || res.message === 'Venta registrada correctamente') {
                    Swal.fire({
                        title: 'Éxito',
                        text: res.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        modal.hide();
                        location.reload();
                    });

                    carrito = [];
                    renderCarrito();
                } else {
                    Swal.fire('Error', res.error || res.message || 'No se pudo guardar la venta', 'error');
                }
            })


            .catch(err => {
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                console.error(err);
            });
        };
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const metodoPagoSelect = document.getElementById('metodoPagoSelect');
    const submetodoPagoSelect = document.getElementById('submetodoPagoSelect');
    const montoRecibidoInput = document.getElementById('montoRecibidoInput');
    const vueltoModal = document.getElementById('vuelto_modal');
    const numeroOrdenInput = document.getElementById('numero_orden');
    const numeroOrdenModal = document.getElementById('numeroOrdenModal');
    const buscarOrdenServicio = document.getElementById('buscar_orden_servicio');
    const resultadosOrdenes = document.getElementById('resultados_ordenes');

    let metodosPago = [];
    let totalActual = 0;

    // Cargar número de orden de PRODUCTOS
    fetch('/admin/pos/next-order-number')
        .then(res => res.json())
        .then(data => {
            numeroOrdenInput.value = data.next_order_number;
            if (numeroOrdenModal) numeroOrdenModal.textContent = data.next_order_number;
        })
        .catch(err => console.error('Error cargando número de orden PRODUCTO:', err));

    // Cargar número de orden de SERVICIO (mostrar como placeholder)
    fetch('/admin/orders/next-order-number')
        .then(res => res.json())
        .then(data => {
            // Ejemplo: SRV-A-0005
            const partes = data.next_order_number.split('-'); 

            const prefixSpan = document.querySelector('.input-group-text');
            const inputServicio = document.getElementById('buscar_orden_servicio');

            if (partes.length === 3) {
                // Mostrar prefijo SRV-A-
                prefixSpan.textContent = `${partes[0]}-${partes[1]}-`;
                // Mostrar el número sugerido como placeholder
                inputServicio.placeholder = partes[2];
            } else {
                // Si el formato no es el esperado, mostrar todo como placeholder
                prefixSpan.textContent = 'SRV-';
                inputServicio.placeholder = data.next_order_number;
            }
        })
        .catch(err => console.error('Error cargando número de orden SERVICIO:', err));

        // Cargar métodos de pago
        fetch('/admin/pos/payment-methods')
            .then(res => res.json())
            .then(data => {
                metodosPago = data;
                metodoPagoSelect.innerHTML = `<option value="">Seleccione un método</option>`;
                data.forEach(m => {
                    metodoPagoSelect.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.name}</option>`);
                });
            });

        // Cargar submétodos según método seleccionado 
        metodoPagoSelect.addEventListener('change', e => {
            const metodoSeleccionado = metodosPago.find(m => m.id == e.target.value);
            submetodoPagoSelect.innerHTML = `<option value="">Seleccione un submétodo</option>`;
            if (metodoSeleccionado && metodoSeleccionado.submethods.length > 0) {
                metodoSeleccionado.submethods.forEach(s => {
                    submetodoPagoSelect.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.name}</option>`);
                });
            }
        });

        // Calcular vuelto
        montoRecibidoInput.addEventListener('input', () => {
            const recibido = parseFloat(montoRecibidoInput.value) || 0;
            const vuelto = recibido - totalActual;
            vueltoModal.textContent = `S/ ${vuelto.toFixed(2)}`;
        });

        // Observar cambios en el total del modal
        const totalModal = document.getElementById('total_modal');
        if (totalModal) {
            const observer = new MutationObserver(() => {
                const totalText = totalModal.textContent.replace('S/', '').trim();
                totalActual = parseFloat(totalText) || 0;
            });
            observer.observe(totalModal, { childList: true });
        }

        // Seleccionar orden de servicio del listado
        resultadosOrdenes.addEventListener('click', e => {
            if (e.target.matches('li[data-id]')) {
                buscarOrdenServicio.value = e.target.textContent.trim();
                resultadosOrdenes.style.display = 'none';
            }
        });
});
</script>
@endpush