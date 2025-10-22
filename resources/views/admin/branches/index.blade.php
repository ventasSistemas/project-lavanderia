@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-building text-primary me-2"></i> Gestión de Sucursales
            </h4>
            <p class="text-muted small mb-0">Administra las sucursales del sistema</p>
        </div>

        <!-- Botón Crear -->
            <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createBranchModal">
                <i class="fa-solid fa-plus me-1"></i> Nueva Sucursal
            </button>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.branches.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o dirección..." 
                    value="{{ request('search') }}" style="max-width: 300px;">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive rounded">
                @php
                    $days = [
                        'monday' => 'Lunes',
                        'tuesday' => 'Martes',
                        'wednesday' => 'Miércoles',
                        'thursday' => 'Jueves',
                        'friday' => 'Viernes',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo'
                    ];
                @endphp
                <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                    <thead class="table-primary text-white">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Encargado</th>
                            <th>Estado</th>
                            <th>Apertura</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone ?? '-' }}</td>
                                <td>{{ $branch->email ?? '-' }}</td>
                                <td><span class="badge bg-info-subtle text-dark">{{ optional($branch->manager)->full_name ?? 'Sin asignar' }}</span></td>
                                <td>
                                    @if($branch->status === 'active')
                                        <span class="badge bg-success-subtle text-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-dark">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    @if($branch->is_open)
                                        <span class="badge bg-info-subtle text-info">Abierta</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Cerrada</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBranchModal{{ $branch->id }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Editar Sucursal -->
                            <div class="modal fade" id="editBranchModal{{ $branch->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-warning text-white rounded-top-4">
                                            <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2"></i> Editar Sucursal</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body px-4 py-3">
                                            <form method="POST" action="{{ route('admin.branches.update', ['branch' => $branch->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-3">

                                                    <!-- Datos principales -->
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Nombre</label>
                                                        <input type="text" name="name" class="form-control shadow-sm" value="{{ $branch->name }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Email</label>
                                                        <input type="email" name="email" class="form-control shadow-sm" value="{{ $branch->email }}">
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold">Dirección</label>
                                                        <textarea name="address" class="form-control shadow-sm" rows="2" required>{{ $branch->address }}</textarea>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Teléfono</label>
                                                        <input type="text" name="phone" class="form-control shadow-sm" value="{{ $branch->phone }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Encargado</label>
                                                        <select name="manager_id" class="form-select shadow-sm">
                                                            <option value="">Sin asignar</option>
                                                            @foreach($assignedUser as $user)
                                                                <option value="{{ $user->id }}" {{ $branch->manager_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Estado</label>
                                                        <select name="status" class="form-select shadow-sm" required>
                                                            <option value="active" {{ $branch->status == 'active' ? 'selected' : '' }}>Activa</option>
                                                            <option value="inactive" {{ $branch->status == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">¿Abierta?</label>
                                                        <select name="is_open" class="form-select shadow-sm">
                                                            <option value="1" {{ $branch->is_open ? 'selected' : '' }}>Sí</option>
                                                            <option value="0" {{ !$branch->is_open ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>

                                                    <!-- Horario editable -->
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-semibold">Horario de Atención</label>
                                                        <div class="border rounded-4 p-3 bg-light shadow-sm">
                                                            @foreach($days as $key => $label)
                                                                @php
                                                                    $daySchedule = $branch->schedule[$key] ?? ['active' => false];
                                                                    $daySchedule['open'] = $daySchedule['open'] ?? '09:00';
                                                                    $daySchedule['close'] = $daySchedule['close'] ?? '19:00';
                                                                @endphp

                                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="day_{{ $key }}" id="day_{{ $key }}_{{ $branch->id }}" {{ $daySchedule['active'] ? 'checked' : '' }}>
                                                                        <label class="form-check-label fw-semibold" for="day_{{ $key }}_{{ $branch->id }}">{{ $label }}</label>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <input type="time" name="open_{{ $key }}" value="{{ $daySchedule['open'] }}" class="form-control form-control-sm shadow-sm" style="width: 130px;">
                                                                        <span class="fw-semibold">a</span>
                                                                        <input type="time" name="close_{{ $key }}" value="{{ $daySchedule['close'] }}" class="form-control form-control-sm shadow-sm" style="width: 130px;">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="text-end mt-4">
                                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                        <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-warning text-white px-4">
                                                        <i class="fa-solid fa-save me-1"></i> Actualizar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron sucursales.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white text-end">
            {{ $branches->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Crear Sucursal -->
<div class="modal fade" id="createBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-building me-2"></i> Nueva Sucursal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <form method="POST" action="{{ route('admin.branches.store') }}">
                    @csrf
                    <div class="row g-3">

                        <!-- Información básica -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input type="text" name="name" class="form-control shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control shadow-sm">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección</label>
                            <textarea name="address" class="form-control shadow-sm" rows="2" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="phone" class="form-control shadow-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Encargado</label>
                            <select name="manager_id" class="form-select shadow-sm">
                                <option value="">Sin asignar</option>
                                @foreach($assignedUser as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="status" class="form-select shadow-sm" required>
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">¿Abierta?</label>
                            <select name="is_open" class="form-select shadow-sm">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Horario de atención -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Horario de Atención</label>
                            <div class="border rounded-4 p-3 bg-light shadow-sm">
                                @php
                                    $days = [
                                        'monday' => 'Lunes',
                                        'tuesday' => 'Martes',
                                        'wednesday' => 'Miércoles',
                                        'thursday' => 'Jueves',
                                        'friday' => 'Viernes',
                                        'saturday' => 'Sábado',
                                        'sunday' => 'Domingo'
                                    ];
                                @endphp

                                @foreach($days as $key => $label)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="day_{{ $key }}" id="day_{{ $key }}">
                                            <label class="form-check-label fw-semibold" for="day_{{ $key }}">{{ $label }}</label>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="time" name="open_{{ $key }}" value="09:00" class="form-control form-control-sm shadow-sm" style="width: 130px;">
                                            <span class="fw-semibold">a</span>
                                            <input type="time" name="close_{{ $key }}" value="19:00" class="form-control form-control-sm shadow-sm" style="width: 130px;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

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