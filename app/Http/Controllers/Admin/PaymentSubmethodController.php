<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSubmethod;
use App\Models\PaymentMethod;

use Illuminate\Http\Request;

class PaymentSubmethodController extends Controller
{
    /**
     * Lista todos los submétodos.
     */
    public function index()
    {
        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Submétodo de pago creado correctamente.');
    }

    /**
     * Formulario de creación de submétodo.
     */
    public function create()
    {
        $methods = PaymentMethod::all();
        return view('admin.payment_submethods.create', compact('methods'));
    }

    /**
     * Guarda un nuevo submétodo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:100',
            'recipient_name' => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:150',
            'identifier' => 'nullable|string|max:150',
            'additional_info' => 'nullable|string|max:255',
        ]);

        PaymentSubmethod::create($request->all());

        return redirect()->route('admin.payment-submethods.index')
            ->with('success', 'Submétodo de pago creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(PaymentSubmethod $paymentSubmethod)
    {
        $methods = PaymentMethod::all();
        return view('admin.payment_submethods.edit', compact('paymentSubmethod', 'methods'));
    }

    /**
     * Actualiza un submétodo existente.
     */
    public function update(Request $request, PaymentSubmethod $paymentSubmethod)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:100',
            'recipient_name' => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:150',
            'identifier' => 'nullable|string|max:150',
            'additional_info' => 'nullable|string|max:255',
        ]);

        $paymentSubmethod->update($request->all());

        return redirect()->route('admin.payment-method.index')
            ->with('success', 'Submétodo de pago actualizado correctamente.');
    }

    /**
     * Elimina un submétodo.
     */
    public function destroy(PaymentSubmethod $paymentSubmethod)
    {
        $paymentSubmethod->delete();

        return redirect()->route('admin.payment-method.index')
            ->with('success', 'Submétodo de pago eliminado correctamente.');
    }

    public function getByMethod($methodId)
    {
        $submethods = PaymentSubmethod::where('payment_method_id', $methodId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($submethods);
    }

}
