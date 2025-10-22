<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'description' => 'Administrador con acceso completo',
            ],
            [
                'name' => 'employee',
                'description' => 'Empleado con acceso limitado',
            ],
            [
                'name' => 'manager',
                'description' => 'Encargado de una sucursal',
            ]
        ]);
    }
}