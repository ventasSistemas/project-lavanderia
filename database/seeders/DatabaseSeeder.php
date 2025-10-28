<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            //ServiceItemSeeder::class,
            //ServiceComboSeeder::class,
            OrderStatusSeeder::class,
            PaymentMethodSeeder::class,
        ]);
    }
}
