<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ComplementarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // --- Categorías ---
        $categories = [
            [
                'name' => 'Bebidas',
                'description' => 'Refrescos, jugos y bebidas varias',
                'image' => 'images/complementary_categories/1761696325_the-refrescos.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Detergentes',
                'description' => 'Distintas marcas de detergentes',
                'image' => 'images/complementary_categories/1761696334_Aseo-Tema-4-ok.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insertar categorías
        DB::table('complementary_product_categories')->insert($categories);

        // Obtener las categorías insertadas (por nombre)
        $bebidas = DB::table('complementary_product_categories')->where('name', 'Bebidas')->first();
        $detergentes = DB::table('complementary_product_categories')->where('name', 'Detergentes')->first();

        // --- Productos ---
        $products = [
            [
                'complementary_product_category_id' => $bebidas->id,
                'name' => 'Coca Cola 600ml',
                'price' => 1.50,
                'image' => 'images/complementary_products/1761696344_coca-cola.jpg',
                'state' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'complementary_product_category_id' => $detergentes->id,
                'name' => 'Ace de 500 gramos',
                'price' => 2.50,
                'image' => 'images/complementary_products/1761696352_images.jpg',
                'state' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('complementary_products')->insert($products);
    }
}