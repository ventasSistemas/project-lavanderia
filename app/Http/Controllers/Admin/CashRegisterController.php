<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\CashMovement;
use App\Models\CashNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleName = $user->role->name ?? 'sin_rol';

        // Traer todas las cajas según el rol
        if ($roleName === 'admin') {
            $cashRegisters = CashRegister::with(['user.role', 'branch', 'movements' => function ($q) {
                $q->orderBy('movement_date', 'desc');
            }])
            ->orderByDesc('opened_at')
            ->get();
        } elseif ($roleName === 'manager') {
            $cashRegisters = CashRegister::with(['user.role', 'branch', 'movements' => function ($q) {
                $q->orderBy('movement_date', 'desc');
            }])
            ->where('branch_id', $user->branch_id)
            ->orderByDesc('opened_at')
            ->get();
        } else {
            $cashRegisters = CashRegister::with(['user.role', 'branch', 'movements' => function ($q) {
                $q->orderBy('movement_date', 'desc');
            }])
            ->where('user_id', $user->id)
            ->orderByDesc('opened_at')
            ->get();
        }

        return view('admin.cash.index', compact('cashRegisters', 'roleName'));
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        if (CashRegister::where('user_id', Auth::id())->where('status', 'open')->exists()) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        $cash = CashRegister::create([
            'user_id' => Auth::id(),
            'branch_id' => Auth::user()->branch_id,
            'opening_amount' => $request->opening_amount,
            'opened_at' => Carbon::now(),
            'status' => 'open',
        ]);

        return back()->with('success', 'Caja abierta correctamente.');
    }

    public function movement(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sale,income,expense',
            'amount' => 'required|numeric|min:0.1',
            'concept' => 'nullable|string|max:255',
        ]);

        $cashRegister = CashRegister::where('status', 'open')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $movement = CashMovement::create([
            'cash_register_id' => $cashRegister->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'concept' => $request->concept,
            'movement_date' => now(),
        ]);

        match ($request->type) {
            'sale' => $cashRegister->increment('total_sales', $request->amount),
            'income' => $cashRegister->increment('total_income', $request->amount),
            'expense' => $cashRegister->increment('total_expense', $request->amount),
        };

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    public function markNotificationRead($id)
    {
        $notif = CashNotification::findOrFail($id);
        $notif->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function close()
    {
        $user = Auth::user();
        $roleName = $user->role->name ?? 'sin_rol';

        $cashRegister = CashRegister::where('status', 'open')
            ->where('user_id', $user->id)
            ->firstOrFail();

        $closingAmount =
            $cashRegister->opening_amount +
            $cashRegister->total_sales +
            $cashRegister->total_income -
            $cashRegister->total_expense;

        $cashRegister->update([
            'closing_amount' => $closingAmount,
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        // Enviar notificación al superior
        if ($roleName === 'employee') {
            $manager = User::where('branch_id', $user->branch_id)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'manager');
                })
                ->first();

            if ($manager) {
                $notification = CashNotification::create([
                    'cash_register_id' => $cashRegister->id,
                    'user_id' => $manager->id,
                    'message' => "El empleado {$user->full_name} ha cerrado su caja con un total de S/ {$closingAmount}.",
                ]);
            } 

        } elseif ($roleName === 'manager') {
            $admin = User::whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->first();

            if ($admin) {
                $notification = CashNotification::create([
                    'cash_register_id' => $cashRegister->id,
                    'user_id' => $admin->id,
                    'message' => "El gerente {$user->full_name} ha cerrado la caja de la sucursal {$user->branch->name} con S/ {$closingAmount}.",
                ]);
            }
        }

        return back()->with('success', 'Caja cerrada correctamente y notificación enviada.');
    }
}
