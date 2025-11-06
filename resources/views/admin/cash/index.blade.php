@extends('admin.layouts.app')

@section('content')
<div class="container py-4">

    {{-- Título principal --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-cash-stack me-2"></i> Módulo de Caja
        </h2>
        <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- Formulario para abrir caja --}}
    @if(!Auth::user()->cashRegisters()->where('status', 'open')->exists())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-3 text-secondary">
                    <i class="bi bi-box-arrow-in-right me-2 text-success"></i> Abrir nueva caja
                </h5>
                <form method="POST" action="{{ route('admin.cash.open') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Monto inicial (S/)</label>
                            <input type="number" step="0.01" name="opening_amount" class="form-control" placeholder="Ejemplo: 100.00" required>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success w-100 mt-2">
                                <i class="bi bi-check-circle me-1"></i> Abrir Caja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Listado de cajas --}}
    <div class="accordion" id="accordionCajas">
        @forelse($cashRegisters as $register)
            <div class="accordion-item shadow-sm border-0 mb-3 rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="heading{{ $register->id }}">
                    <button class="accordion-button {{ $register->status === 'closed' ? 'collapsed' : '' }}" 
                            type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapse{{ $register->id }}" 
                            aria-expanded="{{ $register->status === 'open' ? 'true' : 'false' }}">
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Caja #{{ $register->id }}</strong> — 
                                {{ $register->user->full_name ?? 'N/A' }} |
                                {{ $register->branch->name ?? '-' }}
                                <span class="badge ms-2 px-3 py-2 {{ $register->status === 'open' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($register->status) }}
                                </span>
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($register->opened_at)->format('d/m/Y H:i') }}
                                @if($register->closed_at)
                                    — {{ \Carbon\Carbon::parse($register->closed_at)->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </button>
                </h2>

                <div id="collapse{{ $register->id }}" class="accordion-collapse collapse {{ $register->status === 'open' ? 'show' : '' }}">
                    <div class="accordion-body bg-light">

                        {{-- Totales --}}
                        <div class="text-center mb-4">
                            @php
                                $totalActual = $register->opening_amount + $register->total_sales + $register->total_income - $register->total_expense;
                            @endphp
                            <div class="p-3 bg-gradient bg-warning text-dark rounded-3 border fw-bold fs-5 shadow-sm d-inline-block">
                                💰 Total en Caja: S/ {{ number_format($totalActual, 2) }}
                            </div>
                        </div>

                        {{-- Resumen --}}
                        <div class="row text-center mb-4 g-3">
                            <div class="col-md-3">
                                <div class="card mini-card border-0 shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <strong>Inicial</strong><br>
                                        <span class="fw-bold">S/ {{ number_format($register->opening_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-card border-0 shadow-sm bg-success text-white">
                                    <div class="card-body p-3">
                                        <strong>Ventas</strong><br>
                                        <span>S/ {{ number_format($register->total_sales, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-card border-0 shadow-sm bg-info text-white">
                                    <div class="card-body p-3">
                                        <strong>Ingresos</strong><br>
                                        <span>S/ {{ number_format($register->total_income, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card mini-card border-0 shadow-sm bg-danger text-white">
                                    <div class="card-body p-3">
                                        <strong>Egresos</strong><br>
                                        <span>S/ {{ number_format($register->total_expense, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.cash.movement') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="cash_register_id" value="{{ $register->id }}">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Tipo</label>
                                        <select name="type" class="form-select" required>
                                            <option value="sale">Venta</option>
                                            <option value="income">Ingreso</option>
                                            <option value="expense">Egreso</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Monto</label>
                                        <input type="number" name="amount" step="0.01" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Concepto</label>
                                        <input type="text" name="concept" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100">
                                            <i class="bi bi-plus-circle me-1"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </form>

                        {{-- Movimientos --}}
                        <h6 class="fw-semibold mb-3"><i class="bi bi-list-ul me-1"></i> Movimientos</h6>

                        @if($register->movements->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle text-center bg-white shadow-sm">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>#</th>
                                            <th>Tipo / Concepto</th>
                                            <th>Fecha y Hora</th>
                                            <th>Monto (S/)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($register->movements->sortByDesc('movement_date') as $move)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td class="text-start align-middle">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge tipo-badge
                                                            @if($move->type === 'sale') bg-success 
                                                            @elseif($move->type === 'income') bg-info 
                                                            @else bg-danger 
                                                            @endif">
                                                            {{ $move->type === 'sale' ? 'Venta' : ($move->type === 'income' ? 'Ingreso' : 'Egreso') }}
                                                        </span>
                                                        <span class="concept-text" title="{{ $move->concept ?? ucfirst($move->type) }}">
                                                            {{ $move->concept ?? ($move->type === 'sale' ? 'Venta' : ($move->type === 'income' ? 'Ingreso' : 'Egreso')) }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="text-muted small align-middle">
                                                    {{ $move->movement_date->format('d/m/Y H:i') }}
                                                </td>

                                                <td class="fw-bold text-end align-middle">
                                                    S/ {{ number_format($move->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light border text-center mt-2">
                                <i class="bi bi-calendar2-minus me-2"></i> Sin movimientos registrados.
                            </div>
                        @endif

                        {{-- Agregar movimiento / Cerrar caja --}}
                        @if($register->status === 'open')
                            <hr>
                            

                            <div class="text-end mt-4">
                                <form method="POST" action="{{ route('admin.cash.close') }}">
                                    @csrf
                                    <input type="hidden" name="cash_register_id" value="{{ $register->id }}">
                                    <button class="btn btn-danger px-4">
                                        <i class="bi bi-box-arrow-right me-1"></i> Cerrar Caja
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light border text-center py-4 shadow-sm">
                <i class="bi bi-exclamation-circle me-2"></i> No hay cajas registradas aún.
            </div>
        @endforelse
    </div>
</div>
@endsection

<style>
.mini-card {
    transition: transform 0.2s ease;
}
.mini-card:hover {
    transform: scale(1.03);
}
.accordion-button {
    background-color: #f8f9fa !important;
    font-weight: 600;
}
.accordion-button:not(.collapsed) {
    background-color: #e9ecef !important;
}
</style>
