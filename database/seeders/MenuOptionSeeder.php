<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuOption;
use Illuminate\Database\Seeder;

class MenuOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu_mantenimiento = MenuCategory::where('name', 'MANTENIMIENTO')->first();
        $menu_sistema = MenuCategory::where('name', 'SISTEMA')->first();
        $menu_usuario = MenuCategory::where('name', 'USUARIOS')->first();
        $menu_seguridad = MenuCategory::where('name', 'SEGURIDAD')->first();
        $menu_compra = MenuCategory::where('name', 'COMPRAS')->first();
        $menu_almacen = MenuCategory::where('name', 'ALMACEN')->first();

        MenuOption::create([
            'name' => 'PRODUCTOS',
            'order' => 6,
            'link' => 'product',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'P. ACTIVOS',
            'order' => 5,
            'link' => 'active-ingredient',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'CATEGORIAS',
            'order' => 2,
            'link' => 'category',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'UNIDADES',
            'order' => 4,
            'link' => 'unit',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'MARCAS',
            'order' => 3,
            'link' => 'brand',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'PERSONAS',
            'order' => 1,
            'link' => 'person',
            'menu_category_id' => $menu_mantenimiento->id,
        ]);

        MenuOption::create([
            'name' => 'EMPRESA',
            'order' => 1,
            'link' => 'company',
            'menu_category_id' => $menu_sistema->id,
        ]);

        MenuOption::create([
            'name' => 'SUCURSALES',
            'order' => 2,
            'link' => 'branch-office',
            'menu_category_id' => $menu_sistema->id,
        ]);

        MenuOption::create([
            'name' => 'CAJAS',
            'order' => 3,
            'link' => 'checkout',
            'menu_category_id' => $menu_sistema->id,
        ]);

        MenuOption::create([
            'name' => 'TARJETAS',
            'order' => 4,
            'link' => 'card',
            'menu_category_id' => $menu_sistema->id,
        ]);

        MenuOption::create([
            'name' => 'M. TRANSFERENCIA',
            'order' => 5,
            'link' => 'transfer-method',
            'menu_category_id' => $menu_sistema->id,
        ]);

        MenuOption::create([
            'name' => 'USUARIOS',
            'order' => 1,
            'link' => 'user',
            'menu_category_id' => $menu_usuario->id,
        ]);

        MenuOption::create([
            'name' => 'TIPO DE USUARIO',
            'order' => 2,
            'link' => 'user-type',
            'menu_category_id' => $menu_usuario->id,
        ]);

        MenuOption::create([
            'name' => 'PERFIL',
            'order' => 3,
            'link' => 'profile',
            'menu_category_id' => $menu_usuario->id,
        ]);

        MenuOption::create([
            'name' => 'CATEGORIAS DE MENU',
            'order' => 1,
            'link' => 'menu-category',
            'menu_category_id' => $menu_seguridad->id,
        ]);

        MenuOption::create([
            'name' => 'OPCIONES DE MENU',
            'order' => 2,
            'link' => 'menu-option',
            'menu_category_id' => $menu_seguridad->id,
        ]);

        MenuOption::create([
            'name' => 'ACCIONES',
            'order' => 3,
            'link' => 'action',
            'menu_category_id' => $menu_seguridad->id,
        ]);

        MenuOption::create([
            'name' => 'REQUERIMIENTOS',
            'order' => 1,
            'link' => 'requirement',
            'menu_category_id' => $menu_compra->id,
        ]);

        MenuOption::create([
            'name' => 'COMPRAS',
            'order' => 2,
            'link' => 'purchase',
            'menu_category_id' => $menu_compra->id,
        ]);

        MenuOption::create([
            'name' => 'GUÍAS DE ENTRADA',
            'order' => 3,
            'link' => 'entry-guide',
            'menu_category_id' => $menu_compra->id,
        ]);

        MenuOption::create([
            'name' => 'DOC. ALMACEN',
            'order' => 1,
            'link' => 'store-document',
            'menu_category_id' => $menu_almacen->id,
        ]);

        MenuOption::create([
            'name' => 'LOTES',
            'order' => 2,
            'link' => 'lot',
            'menu_category_id' => $menu_almacen->id,
        ]);

        MenuOption::create([
            'name' => 'SERIES',
            'order' => 3,
            'link' => 'serie',
            'menu_category_id' => $menu_almacen->id,
        ]);

        MenuOption::create([
            'name' => 'STOCK',
            'order' => 4,
            'link' => 'stock',
            'menu_category_id' => $menu_almacen->id,
        ]);

        MenuOption::create([
            'name' => 'KARDEX',
            'order' => 5,
            'link' => 'kardex',
            'menu_category_id' => $menu_almacen->id,
        ]);
    }
}
