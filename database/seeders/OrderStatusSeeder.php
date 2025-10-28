<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pendiente',
                'description' => 'El pedido ha sido creado pero aún no se ha iniciado el proceso.',
                'color_code' => '#FFC107', 
            ],
            [
                'name' => 'En Proceso',
                'description' => 'El pedido está siendo procesado actualmente.',
                'color_code' => '#17A2B8', 
            ],
            [
                'name' => 'Terminado',
                'description' => 'El pedido ha sido completado exitosamente.',
                'color_code' => '#28A745', 
            ],
            [
                'name' => 'Entregado',
                'description' => 'El pedido ha sido entregado al cliente.',
                'color_code' => '#007BFF', 
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}