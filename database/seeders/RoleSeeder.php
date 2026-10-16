<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'CLIENTE',
            'code' => 'C',
        ]);

        Role::create([
            'name' => 'PROVEEDOR',
            'code' => 'P',
        ]);

        Role::create([
            'name' => 'PERSONAL',
            'code' => 'U',
        ]);

        Role::create([
            'name' => 'OTROS',
            'code' => 'O',
        ]);
    }
}
