@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fas fa-exchange-alt text-primary me-2"></i> Transferencias de Productos
            </h4>
            <p class="text-muted small mb-0">Envía productos a sucursales y revisa el historial de transferencias</p>
        </div>
    </div>

    @if(auth()->user()->hasRole('admin'))
        <!-- Formulario de Envío -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="text-secondary mb-3"><i class="fa-solid fa-paper-plane text-primary me-1"></i> Enviar Producto a Sucursal</h6>
                <form action="{{ route('admin.product-transfers.store') }}" method="POST">
                    @csrf
                    <div id="transfer-items">
                        <div class="row g-2 mb-2 transfer-item">
                            <div class="col-md-3">
                                <label class="form-label">Categoría</label>
                                <select name="transfers[0][category_id]" class="form-select category-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\ComplementaryProductCategory::whereNull('branch_id')->get() as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Producto</label>
                                <select name="transfers[0][product_id]" class="form-select product-select" required>
                                    <option value="">Seleccione categoría primero...</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Sucursal destino</label>
                                <select name="transfers[0][branch_id]" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\Branch::all() as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="transfers[0][quantity]" class="form-control" min="1" required>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <button type="button" id="add-item" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Agregar producto
                        </button>

                        <button class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Enviar Todo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.product-transfers.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control"
                    placeholder="Buscar por producto o sucursal..."
                    value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive rounded">
                <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                    <thead class="table-primary text-white">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Producto</th>
                            <th>Sucursal</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Enviado Por</th>
                            <th>Revisado Por</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $t)
                            <tr>
                                <td class="ps-3">{{ $t->id }}</td>
                                <td>{{ $t->product->name ?? '-' }}</td>
                                <td>{{ $t->branch->name ?? '-' }}</td>
                                <td>{{ $t->quantity }}</td>
                                <td>
                                    @if($t->status == 'pending')
                                        <span class="badge bg-warning-subtle text-dark">Pendiente</span>
                                    @elseif($t->status == 'accepted')
                                        <span class="badge bg-success-subtle text-success">Aceptada</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td>{{ $t->sender->full_name ?? '-' }}</td>
                                <td>{{ $t->reviewer->full_name ?? '-' }}</td>
                                <td class="text-end pe-3">
                                    @if(auth()->user()->hasRole('manager') && $t->status == 'pending')
                                        <form action="{{ route('admin.product-transfers.approve', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('admin.product-transfers.reject', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay transferencias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $transfers->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;

    // Clonar fila
    document.getElementById('add-item')?.addEventListener('click', function() {
        const container = document.getElementById('transfer-items');
        const firstItem = container.querySelector('.transfer-item');
        const clone = firstItem.cloneNode(true);

        clone.querySelectorAll('input, select').forEach(input => {
            input.value = '';
            const name = input.getAttribute('name').replace(/\[\d+\]/, `[${itemIndex}]`);
            input.setAttribute('name', name);
        });

        container.appendChild(clone);
        itemIndex++;
    });

    // Eliminar fila
    document.addEventListener('click', e => {
        if (e.target.closest('.remove-item')) {
            const item = e.target.closest('.transfer-item');
            if (document.querySelectorAll('.transfer-item').length > 1) {
                item.remove();
            }
        }
    });

    // Filtrar productos por categoría (Ajax)
    document.addEventListener('change', e => {
        if (e.target.classList.contains('category-select')) {
            const categoryId = e.target.value;
            const productSelect = e.target.closest('.transfer-item').querySelector('.product-select');
            productSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch(`/admin/product-transfers/get-products/${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">Seleccione...</option>';
                    data.forEach(prod => {
                        options += `<option value="${prod.id}">${prod.name} (Stock: ${prod.stock})</option>`;
                    });
                    productSelect.innerHTML = options;
                });
        }
    });
</script>
@endpush
@endsection