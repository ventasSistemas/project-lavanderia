<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\PaymentSubmethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Métodos de pago principales
        $methods = [
            [
                'name' => 'Pago en Efectivo',
                'description' => 'Pago realizado directamente en el local o contra entrega.',
            ],
            [
                'name' => 'Billetera Digital',
                'description' => 'Pagos mediante aplicaciones móviles como Yape o Plin.',
            ],
            [
                'name' => 'Transferencia Bancaria',
                'description' => 'Pagos mediante transferencia directa desde una cuenta bancaria.',
            ],
        ];

        foreach ($methods as $methodData) {
            $method = PaymentMethod::create($methodData);

            // Submétodos según el tipo
            switch ($method->name) {
                case 'Pago en Efectivo':
                    PaymentSubmethod::create([
                        'payment_method_id' => $method->id,
                        'name' => 'En tienda',
                        'recipient_name' => null,
                        'account_number' => null,
                        'identifier' => null,
                        'additional_info' => 'Disponible al momento de la entrega del pedido.',
                    ]);
                    break;

                case 'Billetera Digital':
                    $submethods = [
                        [
                            'name' => 'Yape',
                            'recipient_name' => 'Lavandería CleanExpress',
                            'account_number' => '987654321',
                            'identifier' => 'YAPE9876',
                            'additional_info' => 'Número asociado a BCP.',
                        ],
                        [
                            'name' => 'Plin',
                            'recipient_name' => 'Lavandería CleanExpress',
                            'account_number' => '912345678',
                            'identifier' => 'PLIN9123',
                            'additional_info' => 'Número asociado a Interbank.',
                        ],
                    ];
                    foreach ($submethods as $sub) {
                        PaymentSubmethod::create(array_merge(['payment_method_id' => $method->id], $sub));
                    }
                    break;

                case 'Transferencia Bancaria':
                    $submethods = [
                        [
                            'name' => 'BCP',
                            'recipient_name' => 'Lavandería CleanExpress S.A.C.',
                            'account_number' => '123-4567890-0-12',
                            'identifier' => 'Cuenta Corriente BCP',
                            'additional_info' => 'Transferencia interbancaria disponible.',
                        ],
                        [
                            'name' => 'Interbank',
                            'recipient_name' => 'Lavandería CleanExpress S.A.C.',
                            'account_number' => '200-1234567890',
                            'identifier' => 'Cuenta Ahorros Interbank',
                            'additional_info' => 'Tiempo de confirmación: 15 min aprox.',
                        ],
                    ];
                    foreach ($submethods as $sub) {
                        PaymentSubmethod::create(array_merge(['payment_method_id' => $method->id], $sub));
                    }
                    break;
            }
        }
    }
}