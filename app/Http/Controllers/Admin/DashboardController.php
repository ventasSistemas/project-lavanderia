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

        // Inicializamos variables
        $estadoPedidos = [
            'Pendiente' => 0,
            'En proceso' => 0,
            'Terminado' => 0,
            'Entregado' => 0,
        ];

        $gananciasDia = $gananciasSemana = $gananciasMes = $gananciasAnio = 0;
        $pedidosHoy = $pedidosPasados = $pedidosManana = collect();
        $ventasUltimos7Dias = [];
        $fechas = [];

        // Queries base
        $ordersQuery = Order::query()->with('status', 'customer', 'employee');
        $cashMovementsQuery = CashMovement::query();

        if (in_array($role, ['manager', 'employee']) && $branchId) {
            $ordersQuery->where('branch_id', $branchId);
            $cashMovementsQuery->whereHas('cashRegister', fn($q) => $q->where('branch_id', $branchId));
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        try {
            // Ganancias
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

            // Ventas últimos 7 días
            for ($i = 6; $i >= 0; $i--) {
                $fecha = now()->subDays($i)->format('Y-m-d');
                $total = (clone $cashMovementsQuery)
                    ->whereDate('movement_date', $fecha)
                    ->where('type', 'sale')
                    ->sum('amount');
                $fechas[] = now()->subDays($i)->format('d/m');
                $ventasUltimos7Dias[] = $total;
            }

            // Configurar zona horaria a Perú
            $timezone = 'America/Lima';
            $today = Carbon::now($timezone)->startOfDay();
            $tomorrow = Carbon::now($timezone)->addDay()->startOfDay();

            $estadosPermitidos = ['Pendiente', 'En proceso', 'Terminado'];

            // Pedidos de hoy
            $pedidosHoy = (clone $ordersQuery)
                ->whereDate('delivery_date', $today)
                ->whereHas('status', fn($q) => $q->whereIn('name', $estadosPermitidos))
                ->orderBy('delivery_date', 'asc')
                ->paginate(10, ['*'], 'hoy_page');

            // Pedidos pasados
            $pedidosPasados = (clone $ordersQuery)
                ->whereDate('delivery_date', '<', $today)
                ->whereHas('status', fn($q) => $q->whereIn('name', $estadosPermitidos))
                ->orderBy('delivery_date', 'desc')
                ->paginate(10, ['*'], 'pasados_page');

            // Pedidos de mañana
            $pedidosManana = (clone $ordersQuery)
                ->whereDate('delivery_date', $tomorrow)
                ->whereHas('status', fn($q) => $q->whereIn('name', $estadosPermitidos))
                ->orderBy('delivery_date', 'asc')
                ->paginate(10, ['*'], 'manana_page');

        } catch (\Throwable $e) {
            // Manejar errores si es necesario
        }

        return view('admin.dashboard', compact(
            'pedidosHoy', 'pedidosPasados', 'pedidosManana', 'estadoPedidos',
            'gananciasDia', 'gananciasSemana', 'gananciasMes', 'gananciasAnio',
            'ventasUltimos7Dias', 'fechas'
        ));
    }
}
