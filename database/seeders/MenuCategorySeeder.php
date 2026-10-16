<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuCategory::create([
            'name' => 'MANTENIMIENTO',
            'order' => 1,
            'icon' => 'fas fa-clipboard',
        ]);

        MenuCategory::create([
            'name' => 'VENTA',
            'order' => 2,
            'icon' => 'fas fa-university',
        ]);

        MenuCategory::create([
            'name' => 'ALMACEN',
            'order' => 3,
            'icon' => 'fas fa-university',
        ]);

        MenuCategory::create([
            'name' => 'COMPRAS',
            'order' => 4,
            'icon' => 'fas fa-university',
        ]);

        MenuCategory::create([
            'name' => 'SISTEMA',
            'order' => 5,
            'icon' => 'fas fa-cogs',
        ]);

        MenuCategory::create([
            'name' => 'USUARIOS',
            'order' => 6,
            'icon' => 'fas fa-users',
        ]);

        MenuCategory::create([
            'name' => 'SEGURIDAD',
            'order' => 7,
            'icon' => 'fas fa-lock',
        ]);
    }
}
