@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-clipboard-list text-primary me-2"></i> Estados de Pedido
            </h4>
            <p class="text-muted small mb-0">Administra los estados que pueden tener los pedidos en el sistema</p>
        </div>

        <!-- Botón Crear -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createOrderStatusModal">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Estado
        </button>
    </div>

    <!-- Buscador -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.order-status.index') }}" class="d-flex align-items-center gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o descripción..."
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
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Color</th>
                            <th class="pe-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderStatuses as $status)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge px-3 py-2 text-uppercase"
                                          style="background-color: {{ $status->color_code ?? '#6c757d' }}">
                                          {{ $status->name }}
                                    </span>
                                </td>
                                <td>{{ $status->description ?? '-' }}</td>
                                <td>
                                    <span class="badge border" style="background-color: {{ $status->color_code ?? '#f8f9fa' }}; width: 40px; height: 30px; display: inline-block;">
                                        <!-- Sin texto, solo color -->
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editOrderStatusModal{{ $status->id }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.order-status.destroy', $status->id) }}" method="POST" class="d-inline-block"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este estado?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Editar Estado -->
                            <div class="modal fade" id="editOrderStatusModal{{ $status->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-md modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-warning text-white rounded-top-4">
                                            <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Estado</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4 py-3">
                                            <form method="POST" action="{{ route('admin.order-status.update', $status->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre</label>
                                                        <input type="text" name="name" class="form-control"
                                                               value="{{ $status->name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-dark">Código de color</label>
                                                        <div class="input-group align-items-center">
                                                            <div id="hiddenColorPickerEdit{{ $status->id }}" style="display:none;"></div>
                                                            <input type="text" name="color_code" id="colorInputEdit{{ $status->id }}"
                                                                class="form-control shadow-sm"
                                                                value="{{ $status->color_code }}" placeholder="#00FF00">
                                                            <button type="button"
                                                                    class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                                                                    id="openColorPickerEditBtn{{ $status->id }}"
                                                                    title="Seleccionar color" style="width: 45px;">
                                                                <i class="fa-solid fa-palette" id="colorIconEdit{{ $status->id }}"
                                                                style="font-size: 1.2rem; color: {{ $status->color_code ?? '#4D525A' }}"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-muted">Selecciona un color que identifique este estado.</small>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Descripción</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $status->description }}</textarea>
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
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No se encontraron estados de pedido.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación (si usas paginación en el controlador) -->
        <div class="card-footer bg-white text-end">
            {{ $orderStatuses->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>

<!-- Modal Crear Estado -->
<div class="modal fade" id="createOrderStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title">
          +<i class="fa-solid fa-clipboard-list me-2"></i> Nuevo Estado de Pedido
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 py-3">
        <form method="POST" action="{{ route('admin.order-status.store') }}">
          @csrf
          <div class="row g-3">
            
            <!-- Nombre -->
            <div class="col-md-6">
              <label class="form-label fw-semibold text-dark">Nombre</label>
              <input type="text" name="name" class="form-control shadow-sm" placeholder="Ej. En proceso" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold text-dark">Código de color</label>
              <div class="input-group align-items-center">
                <div id="hiddenColorPicker" style="display:none;"></div>
                <input type="text" name="color_code" id="colorInputNew"
                       class="form-control shadow-sm" placeholder="#00FF00">
                <button type="button"
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                        id="openColorPickerBtn" title="Seleccionar color" style="width: 45px;">
                    <i class="fa-solid fa-palette" id="colorIconNew" style="font-size: 1.2rem;"></i>
                </button>
              </div>

              <!-- Contenedor invisible para Pickr -->
              <small class="text-muted">Selecciona un color que identifique este estado.</small>
            </div>

            <!-- Descripción -->
            <div class="col-md-12">
              <label class="form-label fw-semibold text-dark">Descripción</label>
              <textarea name="description" class="form-control shadow-sm" rows="2"
                        placeholder="Descripción opcional del estado..."></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('colorInputNew');
  const icon = document.getElementById('colorIconNew');
  const openBtn = document.getElementById('openColorPickerBtn');
  const hiddenContainer = document.getElementById('hiddenColorPicker');

  if (typeof Pickr !== 'undefined' && hiddenContainer) {
    const pickr = Pickr.create({
      el: hiddenContainer, 
      theme: 'classic',
      default: input.value || '#4D525A',
      swatches: [
        '#0d6efd', '#198754', '#ffc107', '#dc3545',
        '#20c997', '#6610f2', '#6f42c1', '#fd7e14',
        '#6c757d', '#1985a1'
      ],
      components: {
        preview: true,
        opacity: false,
        hue: true,
        interaction: {
          hex: true,
          rgba: true,
          input: true,
          clear: true,
          save: true
        }
      }
    });

    openBtn.addEventListener('click', () => pickr.show());

    pickr.on('save', (color) => {
      const selectedColor = color.toHEXA().toString();
      input.value = selectedColor;
      icon.style.color = selectedColor; 
      pickr.hide();
    });
  } else {
    console.warn('Pickr no se inicializó. Revisa si el script está cargado.');
  }
});

document.addEventListener('DOMContentLoaded', () => {
  // Inicializar Pickr para cada modal de edición dinámicamente
  document.querySelectorAll('[id^="hiddenColorPickerEdit"]').forEach(container => {
    const idSuffix = container.id.replace('hiddenColorPickerEdit', '');
    const input = document.getElementById('colorInputEdit' + idSuffix);
    const icon = document.getElementById('colorIconEdit' + idSuffix);
    const openBtn = document.getElementById('openColorPickerEditBtn' + idSuffix);

    if (typeof Pickr !== 'undefined' && container && input && icon && openBtn) {
      const pickr = Pickr.create({
        el: container,
        theme: 'classic',
        default: input.value || '#4D525A',
        swatches: [
          '#0d6efd', '#198754', '#ffc107', '#dc3545',
          '#20c997', '#6610f2', '#6f42c1', '#fd7e14',
          '#6c757d', '#1985a1'
        ],
        components: {
          preview: true,
          opacity: false,
          hue: true,
          interaction: {
            hex: true,
            rgba: true,
            input: true,
            clear: true,
            save: true
          }
        }
      });

      openBtn.addEventListener('click', () => pickr.show());

      pickr.on('save', (color) => {
        const selectedColor = color.toHEXA().toString();
        input.value = selectedColor;
        icon.style.color = selectedColor;
        pickr.hide();
      });
    }
  });
});
</script>
@endpush