<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function ventas(Request $request)
    {
        $user = Auth::user();

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

        // Filtros opcionales (solo visibles para admin)
        $filtroSucursalId = $request->input('sucursal_id');
        $filtroEmpleadoId = $request->input('empleado_id');

        /*
        Filtro rol/sucursal dinámico
        */
        $filtroSucursal = function ($query) use ($user, $filtroSucursalId, $filtroEmpleadoId) {

            if ($user->role->name === 'employee') {
                $query->where('branch_id', $user->branch_id)
                    ->where('employee_id', $user->id);
            } elseif (in_array($user->role->name, ['manager', 'subadmin'])) {
                $query->where('branch_id', $user->branch_id);
            } elseif ($user->role->name === 'admin') {
                if ($filtroSucursalId && $filtroSucursalId !== 'all') {
        $query->where('branch_id', $filtroSucursalId);
    }
    if ($filtroEmpleadoId && $filtroEmpleadoId !== 'all') {
        $query->where('employee_id', $filtroEmpleadoId);
    }
            }
        };

        /*
        Órdenes entregadas
        */
        $ventasOrdenes = Order::with(['customer', 'status', 'paymentMethod', 'branch'])
            ->whereHas('status', fn($q) => $q->where('name', 'Entregado'))
            ->whereBetween('delivery_date', [$fechaInicio, $fechaFin])
            ->when(true, fn($query) => $filtroSucursal($query))
            ->orderBy('delivery_date', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'tipo' => 'Orden de Servicio',
                    'fecha' => $order->delivery_date,
                    'numero' => $order->order_number,
                    'cliente' => $order->customer?->full_name ?? 'Sin cliente',
                    'estado' => $order->status?->name ?? '-',
                    'metodo_pago' => $order->paymentMethod?->name ?? 'No especificado',
                    'total' => $order->final_total,
                    'sucursal' => $order->branch?->name ?? '-',
                ];
            });

        /*
        Ventas de productos
        */
        $ventasProductos = Sale::with(['paymentMethod', 'branch'])
            ->whereBetween('sale_date', [$fechaInicio, $fechaFin])
            ->when(true, fn($query) => $filtroSucursal($query))
            ->orderBy('sale_date', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'tipo' => 'Venta de Producto',
                    'fecha' => $sale->sale_date,
                    'numero' => $sale->order_number,
                    'cliente' => '-',
                    'estado' => 'Completado',
                    'metodo_pago' => $sale->paymentMethod?->name ?? 'No especificado',
                    'total' => $sale->total,
                    'sucursal' => $sale->branch?->name ?? '-',
                ];
            });

        /*
        combinamos y sumamos
        */
        $ventas = (new Collection())
            ->concat($ventasOrdenes)
            ->concat($ventasProductos)
            ->sortByDesc('fecha')
            ->values();

        $total = $ventas->sum('total');

        // Cargar listas para el filtro admin
        $sucursales = [];
        $empleados = [];
        if ($user->role->name === 'admin') {
            $sucursales = Branch::all();
            $empleados = User::whereHas('role', fn($q) => $q->where('name', 'employee'))->get();
        }

        return view('admin.reports.ventas', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'total',
            'sucursales',
            'empleados',
            'filtroSucursalId',
            'filtroEmpleadoId'
        ));
    }

    public function buscarSucursales(Request $request)
    {
        $search = $request->input('q');
        $sucursales = Branch::query()
            ->when($search, fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($sucursales);
    }

    public function buscarEmpleados(Request $request)
    {
        $search = $request->input('q');
        $empleados = User::whereHas('role', fn($r) => $r->where('name', 'employee'))
            ->when($search, fn($q) => $q->where('full_name', 'LIKE', "%{$search}%"))
            ->limit(10)
            ->get(['id', 'full_name as name']);

        return response()->json($empleados);
    }
}
