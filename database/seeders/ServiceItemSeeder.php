<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceItem;

class ServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        // Servicio por peso
        $porPeso = Service::where('name', 'Lavado + secado (por kg)')->first();

        ServiceItem::create([
            'service_id' => $porPeso->id,
            'item_name' => 'Lavado + secado (mínimo 3 kg)',
            'price' => 4.00,
            'includes' => 'Lavado, secado',
            'additional_price' => 2.00,
            'notes' => 'Planchado adicional +S/2.00 por kg'
        ]);

        // Prendas especiales
        $especial = Service::where('name', 'Lavado + secado de prenda especial')->first();

        $especialItems = [
            ['item_name' => 'Mantel chico', 'price' => 6.00, 'includes' => 'Lavado + secado'],
            ['item_name' => 'Mantel mediano', 'price' => 8.00, 'includes' => 'Lavado + secado'],
            ['item_name' => 'Mantel grande', 'price' => 10.00, 'includes' => 'Lavado + secado'],
            ['item_name' => 'Cortina por paño', 'price' => 11.80, 'includes' => 'Lavado + secado', 'additional_price' => 3.00],
            ['item_name' => 'Sábana extra grande', 'price' => 8.00, 'includes' => 'Lavado + secado'],
        ];

        foreach ($especialItems as $item) {
            $item['service_id'] = $especial->id;
            ServiceItem::create($item);
        }

        // Prendas unitarias
        $unitario = Service::where('name', 'Lavado + secado de prenda unitaria')->first();

        $unitItems = [
            ['item_name' => 'Polo', 'price' => 2.00, 'includes' => 'Lavado + secado', 'additional_price' => 1.00],
            ['item_name' => 'Camisa', 'price' => 3.00, 'includes' => 'Lavado + secado', 'additional_price' => 1.50],
            ['item_name' => 'Pantalón', 'price' => 2.50, 'includes' => 'Lavado + secado', 'additional_price' => 1.50],
            ['item_name' => 'Casaca', 'price' => 4.00, 'includes' => 'Lavado + secado', 'additional_price' => 2.00],
            ['item_name' => 'Jeans', 'price' => 3.00, 'includes' => 'Lavado + secado', 'additional_price' => 1.50],
            ['item_name' => 'Ropa interior (x unidad)', 'price' => 1.00, 'includes' => 'Lavado + secado'],
        ];

        foreach ($unitItems as $item) {
            $item['service_id'] = $unitario->id;
            ServiceItem::create($item);
        }
    }
}