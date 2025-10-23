@extends('admin.layouts.app')

@section('title', 'Combos de Servicios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Combos de Servicios</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createComboModal">
            <i class="bi bi-plus-circle"></i> Nuevo Combo
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Items Incluidos</th>
                        <th>Precio Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($combos as $combo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $combo->name }}</td>
                        <td>
                            <ul class="mb-0">
                                @foreach($combo->items as $item)
                                <li>{{ $item->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>S/ {{ number_format($combo->total_price, 2) }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editComboModal{{ $combo->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.service-combos.destroy', $combo->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este combo?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="editComboModal{{ $combo->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('admin.service-combos.update', $combo->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Editar Combo</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nombre</label>
                                            <input type="text" name="name" class="form-control" value="{{ $combo->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Items incluidos</label>
                                            <select name="items[]" class="form-select" multiple required>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $combo->items->contains($item->id) ? 'selected' : '' }}>
                                                        {{ $item->name }} - S/ {{ number_format($item->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Precio Total</label>
                                            <input type="number" step="0.01" name="total_price" class="form-control" value="{{ $combo->total_price }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button class="btn btn-primary">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createComboModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.service-combos.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Nuevo Combo</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Items incluidos</label>
                        <select name="items[]" class="form-select" multiple required>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} - S/ {{ number_format($item->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Precio Total</label>
                        <input type="number" step="0.01" name="total_price" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection