<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCombo;
use App\Models\Service;

class ServiceComboSeeder extends Seeder
{
    public function run(): void
    {
        $lavado = Service::where('name', 'Lavado + secado (por kg)')->first();
        $planchado = Service::where('name', 'Planchado adicional (por kg)')->first();

        $combo1 = ServiceCombo::create([
            'name' => 'Todo incluido (lavado + secado + planchado)',
            'description' => 'Combo completo de lavado, secado y planchado por kg',
            'price' => 6.00,
        ]);

        $combo2 = ServiceCombo::create([
            'name' => 'Solo lavado + secado',
            'description' => 'Combo económico sin planchado',
            'price' => 4.00,
        ]);

        $combo1->services()->attach([$lavado->id, $planchado->id]);
        $combo2->services()->attach([$lavado->id]);
    }
}