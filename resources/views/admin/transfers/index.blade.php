<!-- views/admin/transfers/index.blade.php -->
@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3 text-primary"><i class="fas fa-exchange-alt"></i> Transferencias de Productos</h4>

    @if(auth()->user()->hasRole('admin'))
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="text-secondary mb-3">Enviar Producto a Sucursal</h6>
                <form action="{{ route('admin.product-transfers.store') }}" method="POST">
                    @csrf
                    <div id="transfer-items">
                        <div class="row g-2 mb-2 transfer-item">
                            <div class="col-md-3">
                                <label>Categoría</label>
                                <select name="transfers[0][category_id]" class="form-select category-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\ComplementaryProductCategory::whereNull('branch_id')->get() as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Producto</label>
                                <select name="transfers[0][product_id]" class="form-select product-select" required>
                                    <option value="">Seleccione categoría primero...</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Sucursal destino</label>
                                <select name="transfers[0][branch_id]" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\Branch::all() as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Cantidad</label>
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

    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="text-secondary mb-3">Historial de Transferencias</h6>
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-info">
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Sucursal</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Enviado Por</th>
                        <th>Revisado Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->product->name ?? '-' }}</td>
                            <td>{{ $t->branch->name ?? '-' }}</td>
                            <td>{{ $t->quantity }}</td>
                            <td>
                                @if($t->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($t->status == 'accepted')
                                    <span class="badge bg-success">Aceptada</span>
                                @else
                                    <span class="badge bg-danger">Rechazada</span>
                                @endif
                            </td>
                            <td>{{ $t->sender->name ?? '-' }}</td>
                            <td>{{ $t->reviewer->name ?? '-' }}</td>
                            <td>
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
                            <td colspan="8" class="text-center text-muted">No hay transferencias registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;

    // Clonar fila
    document.getElementById('add-item').addEventListener('click', function() {
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


