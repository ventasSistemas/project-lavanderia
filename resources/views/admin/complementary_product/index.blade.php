@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-cubes text-primary me-2"></i> Productos Complementarios
            </h4>
            <p class="text-muted small mb-0">Administra las categorías y productos complementarios disponibles</p>
        </div>

        <!-- Botón Crear Categoría -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fa-solid fa-plus me-1"></i> Nueva Categoría
        </button>
    </div>

    <!-- Lista de Categorías -->
    <div class="accordion" id="accordionComplementaryCategories">
        @forelse($categories as $category)
            <div class="accordion-item mb-3 shadow-sm border-0 rounded-3">
                <h2 class="accordion-header" id="heading{{ $category->id }}">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center fw-semibold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $category->id }}"
                            aria-expanded="false"
                            aria-controls="collapse{{ $category->id }}">
                        <span>
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="Imagen" class="me-2 rounded-circle" width="35" height="35">
                            @else
                                <i class="fa-solid fa-layer-group text-primary me-2"></i>
                            @endif
                            {{ $category->name }}
                            <small class="text-muted ms-2">({{ $category->products->count() }} productos)</small>
                        </span>
                    </button>
                </h2>

                <div id="collapse{{ $category->id }}" class="accordion-collapse collapse"
                     aria-labelledby="heading{{ $category->id }}"
                     data-bs-parent="#accordionComplementaryCategories">
                    <div class="accordion-body bg-light">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="mb-0 text-muted"><strong>Descripción:</strong> {{ $category->description ?? '—' }}</p>
                            <div>
                                <button class="btn btn-sm btn-outline-warning me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->id }}">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createProductModal{{ $category->id }}">
                                    <i class="fa-solid fa-plus"></i> Producto
                                </button>
                            </div>
                        </div>

                        @if($category->products->isEmpty())
                            <div class="alert alert-secondary py-2 px-3 small mb-0 rounded-pill text-center">
                                <i class="fa-solid fa-circle-info me-2"></i> No hay productos registrados.
                            </div>
                        @else
                            <!-- Tabla de Productos -->
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="table-responsive rounded">
                                        <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                                            <thead class="table-primary text-white">
                                                <tr>
                                                    <th class="ps-3">#</th>
                                                    <th>Imagen</th>
                                                    <th>Nombre</th>
                                                    <th>Precio</th>
                                                    <th>Estado</th>
                                                    <th class="pe-3 text-end">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($category->products as $product)
                                                    <tr>
                                                        <td class="ps-3">{{ $loop->iteration }}</td>
                                                        <td>
                                                            @if($product->image)
                                                                <img src="{{ asset($product->image) }}" alt="Imagen" width="45" height="45" class="rounded-circle">
                                                            @else
                                                                <i class="fa-regular fa-image text-muted"></i>
                                                            @endif
                                                        </td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>S/ {{ number_format($product->price, 2) }}</td>
                                                        <td>
                                                            @if($product->state === 'active')
                                                                <span class="badge bg-success">Activo</span>
                                                            @else
                                                                <span class="badge bg-secondary">Inactivo</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <button class="btn btn-sm btn-outline-warning me-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editProductModal{{ $product->id }}">
                                                                <i class="fa-solid fa-pen"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteProductModal{{ $product->id }}">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Editar Producto -->
                                                    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-md modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                                <div class="modal-header bg-warning text-white rounded-top-4">
                                                                    <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Producto</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body px-4 py-3">
                                                                    <form method="POST" action="{{ route('admin.complementary-products.update', $product->id) }}" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Nombre</label>
                                                                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Precio</label>
                                                                            <input type="number" name="price" step="0.01" class="form-control" value="{{ $product->price }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Imagen</label>
                                                                            <input type="file" name="image" class="form-control" onchange="previewImage(this, 'previewProduct{{ $product->id }}')">
                                                                            @if($product->image)
                                                                                <div class="mt-2 text-center">
                                                                                    <img id="previewProduct{{ $product->id }}" src="{{ asset($product->image) }}" alt="Imagen actual" class="img-fluid rounded" style="max-height: 120px;">
                                                                                </div>
                                                                            @else
                                                                                <div class="mt-2 text-center">
                                                                                    <img id="previewProduct{{ $product->id }}" class="img-fluid rounded" style="max-height: 120px; display:none;">
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Estado</label>
                                                                            <select name="state" class="form-select">
                                                                                <option value="active" {{ $product->state === 'active' ? 'selected' : '' }}>Activo</option>
                                                                                <option value="inactive" {{ $product->state === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <button type="submit" class="btn btn-warning text-white">
                                                                                <i class="fa-solid fa-save me-1"></i> Actualizar
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Eliminar Producto -->
                                                    <div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                                <div class="modal-header bg-danger text-white rounded-top-4">
                                                                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar eliminación</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <p class="text-muted mb-3">
                                                                        ¿Seguro que deseas eliminar el producto <br>
                                                                        <strong class="text-dark">"{{ $product->name }}"</strong>?
                                                                    </p>
                                                                    <form method="POST" action="{{ route('admin.complementary-products.destroy', $product->id) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <div class="d-flex justify-content-center gap-2">
                                                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                                                                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                                                                            </button>
                                                                            <button type="submit" class="btn btn-danger px-3">
                                                                                <i class="fa-solid fa-trash me-1"></i> Eliminar
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Editar Categoría -->
            <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-warning text-white rounded-top-4">
                            <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Categoría</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.complementary-product-categories.update', $category->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control">{{ $category->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imagen</label>
                                    <input type="file" name="image" class="form-control" onchange="previewImage(this, 'previewCategory{{ $category->id }}')">
                                    @if($category->image)
                                        <div class="mt-2 text-center">
                                            <img id="previewCategory{{ $category->id }}" src="{{ asset($category->image) }}" alt="Imagen actual" class="img-fluid rounded" style="max-height: 120px;">
                                        </div>
                                    @else
                                        <div class="mt-2 text-center">
                                            <img id="previewCategory{{ $category->id }}" class="img-fluid rounded" style="max-height: 120px; display:none;">
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-warning text-white">
                                        <i class="fa-solid fa-save me-1"></i> Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Crear Producto -->
            <div class="modal fade" id="createProductModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-success text-white rounded-top-4">
                            <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nuevo Producto</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.complementary-products.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="complementary_product_category_id" value="{{ $category->id }}">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imagen</label>
                                    <input type="file" name="image" class="form-control" onchange="previewImage(this, 'previewProductNew{{ $category->id }}')">
                                    <div class="mt-2 text-center">
                                        <img id="previewProductNew{{ $category->id }}" class="img-fluid rounded" style="max-height: 120px; display:none;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="state" class="form-select">
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-save me-1"></i> Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="alert alert-light border text-center py-4 text-muted">
                <i class="fa-solid fa-circle-info me-2"></i> No hay categorías registradas.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Crear Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nueva Categoría</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.complementary-product-categories.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="image" class="form-control" onchange="previewImage(this, 'previewNewCategory')">
                        <div class="mt-2 text-center">
                            <img id="previewNewCategory" class="img-fluid rounded" style="max-height: 120px; display:none;">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
</script>

<style>
.accordion-button {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}
.accordion-button:not(.collapsed) {
    background-color: #e9f5ff;
    color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
}
.accordion-body {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-5px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
@endsection