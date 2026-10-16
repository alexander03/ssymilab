<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::create([
            'name' => 'COMPRA'
        ]);

        Module::create([
            'name' => 'ALMACEN'
        ]);

        Module::create([
            'name' => 'VENTA'
        ]);

        Module::create([
            'name' => 'CAJA'
        ]);

        Module::create([
            'name' => 'PEDIDOS'
        ]);
    }
}
