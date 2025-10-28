<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Muestra la lista de métodos de pago.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::with('submethods')->get();
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('admin.payment_methods.create');
    }

    /**
     * Guarda un nuevo método de pago.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_methods,name',
            'description' => 'nullable|string|max:255',
        ]);

        PaymentMethod::create($request->all());

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Método de pago creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Actualiza un método existente.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:payment_methods,name,' . $paymentMethod->id,
            'description' => 'nullable|string|max:255',
        ]);

        $paymentMethod->update($request->all());

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Método de pago actualizado correctamente.');
    }

    /**
     * Elimina un método de pago.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Método de pago eliminado correctamente.');
    }
}