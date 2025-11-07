<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function ventas(Request $request)
    {
        $user = Auth::user();

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

        /*
        ─────────────────────────────────────────────
        FILTRO POR ROL Y SUCURSAL
        ─────────────────────────────────────────────
        */
        $filtroSucursal = function ($query) use ($user) {
            // Empleado: solo sus propias órdenes y ventas de su sucursal
            if ($user->role->name === 'employee') {
                $query->where('branch_id', $user->branch_id)
                    ->where('employee_id', $user->id); // 👈 corregido aquí
            }
            // Manager/Subadmin: todas las ventas de su sucursal
            elseif (in_array($user->role->name, ['manager', 'subadmin'])) {
                $query->where('branch_id', $user->branch_id);
            }
            // Admin: sin restricciones
        };

        /*
        ─────────────────────────────────────────────
        ÓRDENES ENTREGADAS
        ─────────────────────────────────────────────
        */
        $ventasOrdenes = Order::with(['customer', 'status', 'paymentMethod', 'branch'])
            ->whereHas('status', fn($q) => $q->where('name', 'Entregado'))
            ->whereBetween('delivery_date', [$fechaInicio, $fechaFin])
            ->where($filtroSucursal)
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
        ─────────────────────────────────────────────
        VENTAS DE PRODUCTOS (POS)
        ─────────────────────────────────────────────
        */
        $ventasProductos = Sale::with(['paymentMethod', 'branch'])
            ->whereBetween('sale_date', [$fechaInicio, $fechaFin])
            ->where($filtroSucursal)
            ->orderBy('sale_date', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'tipo' => 'Venta de Producto',
                    'fecha' => $sale->sale_date,
                    'numero' => $sale->order_number,
                    'cliente' => '-', // sin cliente asociado
                    'estado' => 'Completado',
                    'metodo_pago' => $sale->paymentMethod?->name ?? 'No especificado',
                    'total' => $sale->total,
                    'sucursal' => $sale->branch?->name ?? '-',
                ];
            });

        /*
        ─────────────────────────────────────────────
        COMBINAMOS LOS RESULTADOS
        ─────────────────────────────────────────────
        */
        $ventas = $ventasOrdenes->merge($ventasProductos)->sortByDesc('fecha');
        $total = $ventas->sum('total');

        return view('admin.reports.ventas', compact('ventas', 'fechaInicio', 'fechaFin', 'total'));
    }
}
