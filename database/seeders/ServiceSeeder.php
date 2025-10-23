<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $peso = ServiceCategory::where('name', 'Servicio por peso')->first();
        $especial = ServiceCategory::where('name', 'Prenda especial')->first();
        $unitario = ServiceCategory::where('name', 'Prenda unitaria')->first();
        $adicional = ServiceCategory::where('name', 'Servicios adicionales')->first();

        // Servicios principales
        $services = [
            // Por peso
            [
                'service_category_id' => $peso->id,
                'name' => 'Lavado + secado (por kg)',
                'description' => 'Servicio por peso para ropa diaria (mínimo 3 kg)',
                'base_price' => 4.00,
                'unit_type' => 'kg',
                'estimated_time' => '24-48h',
            ],
            [
                'service_category_id' => $peso->id,
                'name' => 'Planchado adicional (por kg)',
                'description' => 'Servicio adicional para prendas lavadas por peso',
                'base_price' => 2.00,
                'unit_type' => 'kg',
                'estimated_time' => '12h',
            ],
            [
                'service_category_id' => $peso->id,
                'name' => 'Solo planchado (por kg)',
                'description' => 'Solo planchado de ropa por kilogramo',
                'base_price' => 3.00,
                'unit_type' => 'kg',
                'estimated_time' => '12-24h',
            ],

            // Prenda especial
            [
                'service_category_id' => $especial->id,
                'name' => 'Cortina',
                'description' => 'Cortinas, gruesas, delgadas',
                'base_price' => 8.00,
                'unit_type' => 'pieza',
                'estimated_time' => '48h',
            ],

            // Prenda unitaria
            [
                'service_category_id' => $unitario->id,
                'name' => 'Polo',
                'description' => 'Polo, manga lagar, manga corta.',
                'base_price' => 2.00,
                'unit_type' => 'unidad',
                'estimated_time' => '24h',
            ],

            // Servicios adicionales
            [
                'service_category_id' => $adicional->id,
                'name' => 'Recojo a domicilio',
                'description' => 'Servicio de recojo de prendas a domicilio',
                'base_price' => 5.00,
                'unit_type' => 'servicio',
                'estimated_time' => '—',
            ],
            [
                'service_category_id' => $adicional->id,
                'name' => 'Entrega a domicilio',
                'description' => 'Servicio de entrega de prendas a domicilio',
                'base_price' => 5.00,
                'unit_type' => 'servicio',
                'estimated_time' => '—',
            ],
            [
                'service_category_id' => $adicional->id,
                'name' => 'Servicio exprés (24h)',
                'description' => 'Entrega en 24 horas con recargo del 20%',
                'base_price' => 0.00,
                'unit_type' => 'porcentaje',
                'estimated_time' => '24h',
            ],
            [
                'service_category_id' => $adicional->id,
                'name' => 'Quitar manchas difíciles',
                'description' => 'Eliminación de manchas complicadas en prendas específicas',
                'base_price' => 2.00,
                'unit_type' => 'prenda',
                'estimated_time' => '—',
            ],
            [
                'service_category_id' => $adicional->id,
                'name' => 'Doblado (por Unidad)',
                'description' => 'Doblado de prendas por unidad y especiales',
                'base_price' => 1.50,
                'unit_type' => 'prenda',
                'estimated_time' => '—',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}