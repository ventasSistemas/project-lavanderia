<!-- resources/views/admin/customers/index.blade.php -->
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-users text-primary me-2"></i> Gestión de Clientes
            </h4>
            <p class="text-muted small mb-0">Administra los clientes registrados en el sistema</p>
        </div>

        <!-- Botón Crear -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Cliente
        </button>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o DNI..."
                       value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-search"></i></button>
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
                            <th>Nombre completo</th>
                            <th>Teléfono</th>
                            <th>DNI</th>
                            <th>Dirección</th>
                            <th>Sucursal</th>
                            <th>Registrado por</th>
                            <th>Fecha registro</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                                <td>{{ $customer->full_name }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>{{ $customer->document_number ?? '-' }}</td>
                                <td>{{ Str::limit($customer->address, 30) ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $customer->branch->name ?? 'Sin sucursal' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark">
                                        {{ $customer->user->full_name ?? 'No asignado' }}
                                    </span>
                                </td>
                                <td>{{ $customer->registration_date->format('d/m/Y H:i') }}</td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCustomerModal{{ $customer->id }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Editar Cliente -->
                            <div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-warning text-white rounded-top-4">
                                            <h5 class="modal-title"><i class="fa-solid fa-user-pen me-2"></i> Editar Cliente</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4 py-3">
                                            <form method="POST" action="{{ route('admin.customers.update', ['customer' => $customer->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre completo</label>
                                                        <input type="text" name="full_name" class="form-control" value="{{ $customer->full_name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Teléfono</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">DNI</label>
                                                        <input type="text" name="document_number" class="form-control" value="{{ $customer->document_number }}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Dirección</label>
                                                        <textarea name="address" class="form-control" rows="2">{{ $customer->address }}</textarea>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">Sucursal</label>
                                                        <input type="text" class="form-control bg-light" value="{{ $customer->branch->name ?? 'Sin sucursal' }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="text-end mt-4">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning text-white">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No se encontraron clientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i> Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <form method="POST" action="{{ route('admin.customers.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DNI</label>
                            <input type="text" name="document_number" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>

                        @if($user->role->name === 'admin')
                            <div class="col-md-12">
                                <label class="form-label">Sucursal</label>
                                <select name="branch_id" class="form-select" required>
                                    <option value="" selected disabled>Seleccionar sucursal</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="col-md-12">
                                <label class="form-label">Sucursal</label>
                                <input type="text" class="form-control bg-light" value="{{ $branchName }}" readonly>
                            </div>
                        @endif
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection