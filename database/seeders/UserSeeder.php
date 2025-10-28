<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el ID del rol admin y user para asignarlo
        $adminRoleId = DB::table('roles')->where('name', 'admin')->first()->id;
        $userRoleId = DB::table('roles')->where('name', 'employee')->first()->id;
        $managerRoleId = DB::table('roles')->where('name', 'manager')->first()->id;

        DB::table('users')->insert([
            [
                'full_name' => 'Admin User',
                'email' => 'admin@cleanwash.com',
                'password' => Hash::make('123456'), 
                'role_id' => $adminRoleId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'John Doe',
                'email' => 'employee@employee.com',
                'password' => Hash::make('123456'),
                'role_id' => $userRoleId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Gerente A',
                'email' => 'gerentea@gmail.com',
                'password' => Hash::make('123456'),
                'role_id' => $managerRoleId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}