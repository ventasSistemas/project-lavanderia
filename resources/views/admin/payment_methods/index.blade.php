@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold text-dark mb-1">
                <i class="fa-solid fa-credit-card text-primary me-2"></i> Métodos de Pago
            </h4>
            <p class="text-muted small mb-0">Administra los métodos y submétodos de pago disponibles</p>
        </div>

        <!-- Botón Crear -->
        <button class="btn btn-primary shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createMethodModal">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Método
        </button>
    </div>

    <!-- Lista de Métodos -->
    <div class="accordion" id="accordionPaymentMethods">
        @forelse($paymentMethods as $method)
            <div class="accordion-item mb-3 shadow-sm border-0 rounded-3">
                <h2 class="accordion-header" id="heading{{ $method->id }}">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center fw-semibold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $method->id }}"
                            aria-expanded="false"
                            aria-controls="collapse{{ $method->id }}">
                        <span>
                            <i class="fa-solid fa-money-check-dollar" style="color: #3ec11a;"></i> {{ $method->name }}
                            <small class="text-muted ms-2">({{ $method->submethods->count() }} submétodos)</small>
                        </span>
                    </button>
                </h2>

                <div id="collapse{{ $method->id }}" class="accordion-collapse collapse"
                     aria-labelledby="heading{{ $method->id }}"
                     data-bs-parent="#accordionPaymentMethods">
                    <div class="accordion-body bg-light">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="mb-0 text-muted"><strong>Descripción:</strong> {{ $method->description ?? '—' }}</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-warning me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editMethodModal{{ $method->id }}">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createSubmethodModal{{ $method->id }}">
                                    <i class="fa-solid fa-plus"></i> Submétodo
                                </button>
                            </div>
                        </div>

                        @if($method->submethods->isEmpty())
                            <div class="alert alert-secondary py-2 px-3 small mb-0 rounded-pill text-center">
                                <i class="fa-solid fa-circle-info me-2"></i> No hay submétodos registrados.
                            </div>
                        @else
                            <!-- Tabla de Submétodos de Pago -->
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="table-responsive rounded">
                                        <table class="table table-hover table-nowrap align-middle mb-0 rounded">
                                            <thead class="table-primary text-white">
                                                <tr>
                                                    <th class="ps-3">#</th>
                                                    <th>Nombre</th>
                                                    <th>Destinatario</th>
                                                    <th>Cuenta / Nº</th>
                                                    <th>Información adicional</th>
                                                    <th class="pe-3 text-end">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($method->submethods as $sub)
                                                    <tr>
                                                        <td class="ps-3">{{ $loop->iteration }}</td>
                                                        <td>{{ $sub->name }}</td>
                                                        <td>{{ $sub->recipient_name ?? '—' }}</td>
                                                        <td>{{ $sub->account_number ?? '—' }}</td>
                                                        <td>{{ $sub->additional_info ?? '—' }}</td>
                                                        <td class="text-end pe-3">
                                                            <button class="btn btn-sm btn-outline-warning me-1"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editSubmethodModal{{ $sub->id }}">
                                                                <i class="fa-solid fa-pen"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteSubmethodModal{{ $sub->id }}">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                        
                                                    </tr>

                                                    <!-- Modal Editar Submétodo -->
                                                    <div class="modal fade" id="editSubmethodModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-md modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                                <div class="modal-header bg-warning text-white rounded-top-4">
                                                                    <h5 class="modal-title">
                                                                        <i class="fa-solid fa-pen me-2"></i> Editar Submétodo
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body px-4 py-3">
                                                                    <form method="POST" action="{{ route('admin.payment-submethods.update', $sub->id) }}">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Nombre</label>
                                                                            <input type="text" name="name" class="form-control" value="{{ $sub->name }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Nombre del destinatario</label>
                                                                            <input type="text" name="recipient_name" class="form-control" value="{{ $sub->recipient_name }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Número de cuenta / billetera</label>
                                                                            <input type="text" name="account_number" class="form-control" value="{{ $sub->account_number }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Información adicional</label>
                                                                            <input type="text" name="additional_info" class="form-control" value="{{ $sub->additional_info }}">
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
                                                    
                                                    <!-- Modal Eliminar Submétodo OPCIONAL POR EL MOMENTO-->
                                                    <div class="modal fade" id="deleteSubmethodModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                                <div class="modal-header bg-danger text-white rounded-top-4">
                                                                    <h5 class="modal-title">
                                                                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar eliminación
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <p class="text-muted mb-3">
                                                                        ¿Seguro que deseas eliminar el submétodo <br>
                                                                        <strong class="text-dark">"{{ $sub->name }}"</strong>?
                                                                    </p>
                                                                    <form method="POST" action="{{ route('admin.payment-submethods.destroy', $sub->id) }}">
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
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">No hay submétodos registrados.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Paginación (opcional si hay paginación implementada) -->
                                {{-- <div class="card-footer bg-white text-end">
                                    {{ $submethods->links('pagination::bootstrap-5') }}
                                </div> --}}
                            </div>

                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Editar Método -->
            <div class="modal fade" id="editMethodModal{{ $method->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-warning text-white rounded-top-4">
                            <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i> Editar Método</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.payment-methods.update', $method->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" value="{{ $method->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control">{{ $method->description }}</textarea>
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

            <!-- Modal Crear Submétodo -->
            <div class="modal fade" id="createSubmethodModal{{ $method->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-success text-white rounded-top-4">
                            <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nuevo Submétodo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.payment-submethods.store') }}">
                                @csrf
                                <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre del destinatario</label>
                                    <input type="text" name="recipient_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Número de cuenta / billetera</label>
                                    <input type="text" name="account_number" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Información adicional</label>
                                    <input type="text" name="additional_info" class="form-control">
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
                <i class="fa-solid fa-circle-info me-2"></i> No hay métodos de pago registrados.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Crear Método -->
<div class="modal fade" id="createMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i> Nuevo Método</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.payment-methods.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control"></textarea>
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
.accordion-button::after {
    transition: transform 0.3s ease;
}
.accordion-button:not(.collapsed)::after {
    transform: rotate(180deg);
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