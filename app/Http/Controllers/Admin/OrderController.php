<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Branch;
use App\Models\Service;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;   
use App\Models\CashMovement;   
use App\Models\CashRegister;   
use App\Models\PaymentSubmethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $query = Order::with(['customer', 'employee', 'branch', 'status']);

        // Si el usuario es manager, solo ve sus órdenes
        if ($user->role->name === 'manager') {
            $query->where('branch_id', $user->branch_id);
        }

        // Filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('full_name', 'LIKE', "%{$search}%")
                       ->orWhere('document_number', 'LIKE', "%{$search}%");
                })
                ->orWhere('order_number', 'LIKE', "%{$search}%")
                ->orWhereHas('employee', function ($q3) use ($search) {
                    $q3->where('full_name', 'LIKE', "%{$search}%");
                });
            });
        }

        $orders = $query->orderBy('id', 'desc')->paginate(10);

        $customers = Customer::orderBy('full_name')->get();
        $employees = User::whereHas('role', fn($q) => $q->where('name', 'Employee'))->get();
        $branches = Branch::orderBy('name')->get();
        $statuses = OrderStatus::orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'search', 'customers', 'employees', 'branches', 'statuses', 'user'));
    }

    public function create()
    {
        $customers = Customer::all();
        $branches = Branch::all();
        $statuses = OrderStatus::all();
        $categories = ServiceCategory::with('services')->get();
        $paymentMethods = PaymentMethod::with('submethods')->get();

        return view('admin.orders.create', compact('customers', 'branches', 'statuses', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'order_status_id' => 'required|exists:order_status,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_submethod_id' => 'nullable|exists:payment_submethods,id',
            'payment_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'order_items' => 'required|array|min:1',
            'order_items.*.service_id' => 'required|exists:services,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.unit_price' => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date|after_or_equal:receipt_date',
        ]);

        DB::beginTransaction();

        try {

            // Verificar si el usuario tiene caja abierta
                $cashRegister = CashRegister::where('user_id', Auth::id())
                    ->where('status', 'open')
                    ->first();

                if (!$cashRegister) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes registrar una orden porque no tienes una caja abierta. 
                                    Debes abrir tu caja antes de procesar ventas o pagos.',
                    ], 400);
            }

            // Calcular total de servicios
            $total = 0;
            foreach ($request->order_items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;
            }

            // Valores base
            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $paymentAmount = $request->payment_amount ?? 0;

            // Calcular total final
            $finalTotal = $total - $discount + $tax;
            $finalTotal = max(0, $finalTotal);

            // Calcular vuelto
            $paymentReturned = $paymentAmount > $finalTotal ? $paymentAmount - $finalTotal : 0;

            // Estado de pago
            $paymentStatus = $request->payment_status ?? 'pending';

            // Generar número de orden incremental (ORD-0001, ORD-0002, ...)
            $lastOrder = Order::latest('id')->first();
            $nextNumber = $lastOrder ? intval(substr($lastOrder->order_number, 4)) + 1 : 1;
            $orderNumber = 'SRV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Crear la orden
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'employee_id' => Auth::id(),
                'branch_id' => $request->branch_id,
                'order_status_id' => $request->order_status_id,
                'payment_method_id' => $request->payment_method_id,
                'payment_submethod_id' => $request->payment_submethod_id,
                'receipt_date' => now(),
                'delivery_date' => $request->delivery_date ?? now(),
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $total,
                'final_total' => $finalTotal,
                'payment_amount' => $paymentAmount,
                'payment_returned' => $paymentReturned,
                'payment_status' => $paymentStatus,
                'notes' => $request->notes,
            ]);

            // Guardar los ítems
            foreach ($request->order_items as $item) {
                $order->items()->create([
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // Registrar movimiento en caja si hay pago
            if ($paymentAmount > 0) {
                // Buscar la caja abierta del usuario
                $cashRegister = CashRegister::where('user_id', Auth::id())
                    ->where('status', 'open')
                    ->first();

                if ($cashRegister) {
                    // Registrar el movimiento de venta
                    CashMovement::create([
                        'cash_register_id' => $cashRegister->id,
                        'user_id' => Auth::id(),
                        'type' => 'sale',
                        'amount' => $paymentAmount,
                        'concept' => "Venta - Orden {$orderNumber}",
                        'movement_date' => now(),
                    ]);

                    // Actualizar totales de la caja
                    $cashRegister->increment('total_sales', $paymentAmount);

                    // Si hay vuelto, registrar también un egreso
                    if ($paymentReturned > 0) {
                        CashMovement::create([
                            'cash_register_id' => $cashRegister->id,
                            'user_id' => Auth::id(),
                            'type' => 'expense',
                            'amount' => $paymentReturned,
                            'concept' => "Vuelto - Orden {$orderNumber}",
                            'movement_date' => now(),
                        ]);

                        // Restar el vuelto al total de egresos
                        $cashRegister->increment('total_expense', $paymentReturned);
                    }

                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente.',
                'order_number' => $orderNumber,
                'ticket_url' => route('admin.orders.ticket', $order->id),
                'redirect_url' => route('admin.orders.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden: ' . $e->getMessage(),
            ]);
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.service', 'customer', 'employee', 'status', 'branch']);
        return view('admin.orders.show', compact('order'));
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'new_status_id' => 'required|exists:order_status,id',
        ]);

        $orderIds = $request->order_ids;
        $newStatusId = $request->new_status_id;

        $newStatus = OrderStatus::findOrFail($newStatusId);
        $newStatusName = strtolower($newStatus->name);

        $updatedCount = 0;
        $blocked = [];

        foreach ($orderIds as $orderId) {
            $order = Order::with('customer')->find($orderId);
            if (!$order || !$order->status) continue;

            $currentStatus = strtolower($order->status->name);

            $allowedTransitions = [
                'pendiente' => ['en proceso'],
                'en proceso' => ['terminado'],
            ];

            if (isset($allowedTransitions[$currentStatus]) && in_array($newStatusName, $allowedTransitions[$currentStatus])) {
                $order->update([
                    'order_status_id' => $newStatusId,
                    'updated_at' => now(),
                ]);
                $updatedCount++;

                // Si pasa a TERMINADO, crear notificación para admin y manager
                if ($newStatusName === 'terminado') {
                    $admins = \App\Models\User::whereHas('role', fn($q) => 
                        $q->whereIn('name', ['Admin', 'Manager'])
                    )->get();

                    foreach ($admins as $user) {
                        \App\Models\OrderNotification::create([
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'message' => "El pedido #{$order->order_number} de {$order->customer->full_name} está terminado.",
                        ]);
                    }
                }
            } else {
                $blocked[] = $order->order_number;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Se actualizaron {$updatedCount} órdenes al estado '{$newStatus->name}'.",
                'blocked' => $blocked
            ]);
        }

        return redirect()->back()->with('success', "Se actualizaron {$updatedCount} órdenes al estado '{$newStatus->name}'.");
    }

    public function changeStatusView()
    {
        // Cargar los tres estados principales
        $statuses = OrderStatus::whereIn('name', ['Pendiente', 'En Proceso', 'Terminado'])->get();

        // Traer las órdenes que estén en esos tres estados
        $orders = Order::with(['customer', 'branch', 'status'])
            ->whereHas('status', function ($q) {
                $q->whereIn('name', ['Pendiente', 'En Proceso', 'Terminado']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.change-status', compact('statuses', 'orders'));
    }

    public function edit(Order $order)
    {
        $order->load(['items.service', 'customer', 'branch', 'status']);

        $customers = Customer::all();
        $branches = Branch::all();
        $services = Service::all();
        $statuses = OrderStatus::all();
        $categories = ServiceCategory::with('services')->get();
        $paymentMethods = PaymentMethod::with('submethods')->get();

        // Calculamos los totales a mostrar
        $order->subtotal = $order->total_amount ?? 0;
        $order->total = $order->final_total ?? ($order->total_amount - $order->discount + $order->tax);

        return view('admin.orders.edit', compact(
            'order',
            'customers',
            'branches',
            'services',
            'statuses',
            'categories',
            'paymentMethods'
        ));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status_id' => 'required|exists:order_status,id', 
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_status' => 'required|in:pending,paid,partial',
            //'payment_method_id' => 'nullable|exists:payment_methods,id',
            //'payment_submethod_id' => 'nullable|exists:payment_submethods,id',
            'delivery_date' => 'nullable|date|after_or_equal:receipt_date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'order_items' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Primero actualizamos los datos generales de la orden
            $order->update([
                'order_status_id' => $request->order_status_id,
                'payment_status' => $request->payment_status,
                'payment_method_id' => $request->payment_method_id,
                'payment_submethod_id' => $request->payment_submethod_id,
                'payment_returned' => $request->payment_returned ?? 0,
                'delivery_date' => $request->delivery_date,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'notes' => $request->notes,
            ]);

            $total = 0;

            // Si vienen items actualizados desde el formulario
            if ($request->has('order_items')) {
                // Eliminamos los items anteriores
                $order->items()->delete();

                // Creamos los nuevos items
                foreach ($request->order_items as $item) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total += $subtotal;

                    $order->items()->create([
                        'service_id' => $item['service_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            } else {
                // Si no se enviaron items, mantenemos el total actual
                $total = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
            }

            // Recalculamos totales finales
            $finalTotal = $total - $order->discount + $order->tax;

            $order->update([
                'total_amount' => $total,
                'final_total' => $finalTotal,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Orden actualizada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order deleted successfully.');
    }
}