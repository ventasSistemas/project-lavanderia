@extends('admin.layouts.app')

@section('title', 'Items de Servicios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Items de Servicios</h4>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createItemModal">
            <i class="bi bi-plus-circle"></i> Nuevo Item
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Servicio</th>
                        <th>Nombre</th>
                        <th>Precio Unitario</th>
                        <th>Tiempo Estimado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->service->name }}</td>
                        <td>{{ $item->name }}</td>
                        <td>S/ {{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->estimated_time }} min</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editItemModal{{ $item->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.service-items.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este item?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.service-items.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Editar Item</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Servicio</label>
                                            <select name="service_id" class="form-select" required>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" {{ $service->id == $item->service_id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Nombre</label>
                                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Precio</label>
                                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $item->price }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Tiempo Estimado (min)</label>
                                            <input type="number" name="estimated_time" class="form-control" value="{{ $item->estimated_time }}">
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
<div class="modal fade" id="createItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.service-items.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Nuevo Item</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Servicio</label>
                        <select name="service_id" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Tiempo Estimado (min)</label>
                        <input type="number" name="estimated_time" class="form-control">
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