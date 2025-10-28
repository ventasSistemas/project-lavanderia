<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Servicio por peso',
                'description' => 'Ropa de uso diario por kilogramo: (Lavado + Secado)',
                'image' => 'images/service_categories/1761176430_peso1.png',
            ],
            [
                'name' => 'Prenda especial',
                'description' => 'Prendas grandes o delicadas como cortinas, manteles, sábanas, edredones, etc: (Lavado + Secado)',
                'image' => 'images/service_categories/1761176572_prendanuea.jpg',
            ],
            [
                'name' => 'Prenda unitaria',
                'description' => 'Prendas sueltas o delicadas como polos, camisas, pantalones, casacas, etc: (Lavado + Secado)',
                'image' => 'images/service_categories/1761176613_unitaria.jpg',
            ],
            [
                'name' => 'Servicios adicionales',
                'description' => 'Servicios complementarios como recojo, entrega, doblado, plancha, etc.',
                'image' => 'images/service_categories/1761176732_servicio_adi.png',
            ],
        ];

        foreach ($categories as $cat) {
            ServiceCategory::create($cat);
        }
    }
}