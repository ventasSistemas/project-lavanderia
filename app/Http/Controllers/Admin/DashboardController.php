<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CashMovement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'Empleado';
        $branchId = $user->branch_id ?? null;

        // Inicializamos variables para evitar errores
        $estadoPedidos = [
            'Pendiente' => 0,
            'En proceso' => 0,
            'Terminado' => 0,
            'Entregado' => 0,
        ];

        $gananciasDia = $gananciasSemana = $gananciasMes = $gananciasAnio = 0;
        $pedidosDelDia = collect();
        $ventasUltimos7Dias = [];
        $fechas = [];

        // Filtrado por rol
        $ordersQuery = Order::query()->with('status');
        $cashMovementsQuery = CashMovement::query();

        if ($role === 'manager' || $role === 'employee') {
            if ($branchId) {
                $ordersQuery->where('branch_id', $branchId);
                $cashMovementsQuery->whereHas('cashRegister', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }
        }

        // Fechas
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        try {
            // Totales de ganancias
            $gananciasDia = (clone $cashMovementsQuery)->whereDate('movement_date', $today)->sum('amount');
            $gananciasSemana = (clone $cashMovementsQuery)->whereBetween('movement_date', [$startOfWeek, now()])->sum('amount');
            $gananciasMes = (clone $cashMovementsQuery)->whereBetween('movement_date', [$startOfMonth, now()])->sum('amount');
            $gananciasAnio = (clone $cashMovementsQuery)->whereBetween('movement_date', [$startOfYear, now()])->sum('amount');

            // Conteo de pedidos por estado
            $estadoPedidos = [
                'Pendiente' => (clone $ordersQuery)->whereHas('status', fn($q) => $q->where('name', 'Pendiente'))->count(),
                'En proceso' => (clone $ordersQuery)->whereHas('status', fn($q) => $q->where('name', 'En proceso'))->count(),
                'Terminado' => (clone $ordersQuery)->whereHas('status', fn($q) => $q->where('name', 'Terminado'))->count(),
                'Entregado' => (clone $ordersQuery)->whereHas('status', fn($q) => $q->where('name', 'Entregado'))->count(),
            ];

            // Ventas de los últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $total = (clone $cashMovementsQuery)
                    ->whereDate('movement_date', $fecha)
                    ->where('type', 'sale')
                    ->sum('amount');
                $fechas[] = now()->subDays($i)->format('d/m');
                $ventasUltimos7Dias[] = $total;
            }

            // Pedidos del día
            $pedidosDelDia = (clone $ordersQuery)
                ->whereDate('created_at', $today)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        } catch (\Throwable $e) {
        }

        return view('admin.dashboard', compact(
            'role',
            'gananciasDia',
            'gananciasSemana',
            'gananciasMes',
            'gananciasAnio',
            'estadoPedidos',
            'pedidosDelDia',
            'ventasUltimos7Dias',
            'fechas'
        ));
    }
}