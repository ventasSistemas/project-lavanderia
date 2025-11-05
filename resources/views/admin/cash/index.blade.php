@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4 text-primary">
        <i class="bi bi-cash-stack me-2"></i> Módulo de Caja
    </h2>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- 🔹 Botón para abrir nueva caja si no hay una abierta --}}
    @if(!Auth::user()->cashRegisters()->where('status', 'open')->exists())
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-semibold mb-3"><i class="bi bi-box-arrow-in-right me-2"></i>Abrir nueva caja</h5>
                <form method="POST" action="{{ route('admin.cash.open') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="number" step="0.01" name="opening_amount" class="form-control" placeholder="Monto inicial (S/)" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i> Abrir
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- 🔹 Listado de todas las cajas --}}
    <div class="accordion" id="accordionCajas">
        @forelse($cashRegisters as $register)
            <div class="accordion-item shadow-sm mb-3 border-0">
                <h2 class="accordion-header" id="heading{{ $register->id }}">
                    <button class="accordion-button {{ $register->status === 'closed' ? 'collapsed' : '' }}" 
                            type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapse{{ $register->id }}" 
                            aria-expanded="{{ $register->status === 'open' ? 'true' : 'false' }}" 
                            aria-controls="collapse{{ $register->id }}">
                        <strong>Caja #{{ $register->id }}</strong> — 
                        {{ $register->user->full_name ?? 'N/A' }} |
                        {{ $register->branch->name ?? '-' }}
                        <span class="badge ms-2 {{ $register->status === 'open' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($register->status) }}
                        </span>
                        <span class="ms-3 text-muted small">
                            {{ \Carbon\Carbon::parse($register->opened_at)->format('d/m/Y H:i') }}
                            @if($register->closed_at)
                                — {{ \Carbon\Carbon::parse($register->closed_at)->format('d/m/Y H:i') }}
                            @endif
                        </span>
                    </button>
                </h2>
                <div id="collapse{{ $register->id }}" class="accordion-collapse collapse {{ $register->status === 'open' ? 'show' : '' }}" aria-labelledby="heading{{ $register->id }}" data-bs-parent="#accordionCajas">
                    <div class="accordion-body bg-light">
                        <div class="row text-center mb-3">
                            <div class="col-md-12">
                                @php
                                    $totalActual = $register->opening_amount + $register->total_sales + $register->total_income - $register->total_expense;
                                @endphp
                                <div class="p-3 bg-warning text-dark rounded border fw-bold fs-5 shadow-sm">
                                    💰 Total Actual en Caja: S/ {{ number_format($totalActual, 2) }}
                                </div>
                            </div>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-md-3"><div class="p-2 bg-light rounded border"><strong>Inicial:</strong><br>S/ {{ $register->opening_amount }}</div></div>
                            <div class="col-md-3"><div class="p-2 bg-success text-white rounded border"><strong>Ventas:</strong><br>S/ {{ $register->total_sales }}</div></div>
                            <div class="col-md-3"><div class="p-2 bg-info text-white rounded border"><strong>Ingresos:</strong><br>S/ {{ $register->total_income }}</div></div>
                            <div class="col-md-3"><div class="p-2 bg-danger text-white rounded border"><strong>Egresos:</strong><br>S/ {{ $register->total_expense }}</div></div>
                        </div>

                        <h6 class="fw-semibold mb-2"><i class="bi bi-list-ul me-1"></i> Movimientos</h6>
                        @if($register->movements->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach($register->movements as $move)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($move->type === 'sale')
                                                <span class="badge bg-success me-2">Venta</span>
                                            @elseif($move->type === 'income')
                                                <span class="badge bg-info me-2">Ingreso</span>
                                            @else
                                                <span class="badge bg-danger me-2">Egreso</span>
                                            @endif
                                            {{ $move->concept ?? ucfirst($move->type) }}
                                        </div>
                                        <small class="text-muted">{{ $move->movement_date->format('d/m/Y H:i') }}</small>
                                        <strong>S/ {{ number_format($move->amount, 2) }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-light border text-center mt-2">
                                <i class="bi bi-calendar2-minus me-2"></i> Sin movimientos registrados.
                            </div>
                        @endif

                        {{-- Si está abierta, permitir agregar movimiento o cerrar --}}
                        @if($register->status === 'open')
                            <form method="POST" action="{{ route('admin.cash.movement') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="cash_register_id" value="{{ $register->id }}">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <select name="type" class="form-select" required>
                                            <option value="sale">Venta</option>
                                            <option value="income">Ingreso</option>
                                            <option value="expense">Egreso</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3"><input type="number" name="amount" step="0.01" placeholder="Monto" class="form-control" required></div>
                                    <div class="col-md-4"><input type="text" name="concept" placeholder="Concepto" class="form-control"></div>
                                    <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i> Agregar</button></div>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('admin.cash.close') }}" class="mt-3 text-end">
                                @csrf
                                <input type="hidden" name="cash_register_id" value="{{ $register->id }}">
                                <button class="btn btn-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar Caja
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light border text-center">
                <i class="bi bi-exclamation-circle me-2"></i> No hay cajas registradas aún.
            </div>
        @endforelse
    </div>
</div>
@endsection
