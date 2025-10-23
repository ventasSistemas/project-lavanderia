@extends('admin.layouts.app')
<!--Hacer como desplegable-->

@section('title', 'Categorías y Servicios')

@section('content')
<div class="container-fluid">
    
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-layer-group text-primary me-2"></i> Gestión de Categorías y Servicios
            </h4>
            <p class="text-muted small mb-0">Administra las categorías y servicios disponibles en el sistema</p>
        </div>

        <!-- Botón Crear -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fa-solid fa-plus me-1"></i> Nueva Categoría de Servicio
        </button>
    </div>

    @if($categories->isEmpty())
        <div class="alert alert-warning text-center">No hay categorías registradas aún.</div>
    @else
        <div class="row g-4">
            @foreach($categories as $category)
                <!-- Cada card ocupa toda la fila -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
                            <h6 class="mb-0">{{ $category->name }}</h6>
                            <div>
                                <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal"
                                    data-bs-target="#editCategoryModal{{ $category->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.service-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta categoría y sus servicios?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            @if($category->image)
                                <div class="text-center mb-3">
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover;">
                                </div>
                            @endif
                            <p class="text-muted small mb-3">{{ $category->description ?? 'Sin descripción disponible' }}</p>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-secondary mb-0"><i class="fa-solid fa-shirt me-1 text-info"></i> Servicios</h6>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#createServiceModal{{ $category->id }}">
                                    <i class="fa-solid fa-plus"></i> Agregar
                                </button>
                            </div>

                            @if($category->services->isEmpty())
                                <p class="text-muted small text-center mt-2">No hay servicios registrados.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Nombre Servicio</th>
                                                <th>Descripción</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-end">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($category->services as $service)
                                                <tr>
                                                    <td>
                                                        @if($service->image)
                                                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        @endif
                                                        {{ $service->name }}
                                                    </td>
                                                    <td>{{ $service->description }}</td>
                                                    <td class="text-end">S/ {{ number_format($service->base_price, 2) }}</td>
                                                    <td class="text-end">{{ $service->status }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                                            data-bs-target="#editServiceModal{{ $service->id }}">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este servicio?')">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <!-- Modal Editar Servicio -->
                                                <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf @method('PUT')
                                                                <div class="modal-header bg-warning text-dark">
                                                                    <h5 class="modal-title">Editar Servicio</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label>Nombre</label>
                                                                        <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label>Precio</label>
                                                                        <input type="number" step="0.01" name="base_price" class="form-control" value="{{ $service->base_price }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label>Imagen</label>
                                                                        <input type="file" name="image" class="form-control" accept="image/*">
                                                                        @if($service->image)
                                                                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="mt-2 rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                                                        @endif
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
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Editar Categoría -->
                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.service-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-header bg-warning text-white rounded-top-4">
                                    <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Categoría</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nombre</label>
                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción</label>
                                        <textarea name="description" class="form-control">{{ $category->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Imagen</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        @if($category->image)
                                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="mt-2 rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                        @endif
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

                <!-- Modal Crear Servicio -->
                <div class="modal fade" id="createServiceModal{{ $category->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="service_category_id" value="{{ $category->id }}">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Nuevo Servicio para {{ $category->name }}</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nombre del Servicio</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Precio Base</label>
                                        <input type="number" step="0.01" name="base_price" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción</label>
                                        <textarea name="description" class="form-control" placeholder="Opcional"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Imagen</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
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

            @endforeach
        </div>
    @endif
</div>

<!-- Modal Crear Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.service-categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">+<i class="fa-solid fa-layer-group me-2"></i> Nueva Categoría</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Imagen</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
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
@endsection