<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Servicio por peso', 'description' => 'Ropa de uso diario por kilogramo: (Lavado + Secado)'],
            ['name' => 'Prenda especial', 'description' => 'Prendas grandes o delicadas como cortinas, manteles, sábanas, edredones, etc: (Lavado + Secado)'],
            ['name' => 'Prenda unitaria', 'description' => 'Prendas sueltas o delicadas como polos, camisas, pantalones, casacas, etc: (Lavado + Secado)'],
            ['name' => 'Servicios adicionales', 'description' => 'Servicios complementarios como recojo, entrega, doblado, plancha, etc.'],
        ];

        foreach ($categories as $cat) {
            ServiceCategory::create($cat);
        }
    }
}