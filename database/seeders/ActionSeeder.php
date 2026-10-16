<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\MenuOption;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $option_empresa = MenuOption::where('name', 'EMPRESA')->first();
        $option_sucursal = MenuOption::where('name', 'SUCURSALES')->first();
        $option_usuario = MenuOption::where('name', 'USUARIOS')->first();
        $option_tipo_usuario = MenuOption::where('name', 'TIPO DE USUARIO')->first();
        $option_categoria = MenuOption::where('name', 'CATEGORIAS DE MENU')->first();
        $option_opcion = MenuOption::where('name', 'OPCIONES DE MENU')->first();
        $option_accion = MenuOption::where('name', 'ACCIONES')->first();
        $option_persona = MenuOption::where('name', 'PERSONAS')->first();
        $option_marca = MenuOption::where('name', 'MARCAS')->first();
        $option_unidades = MenuOption::where('name', 'UNIDADES')->first();
        $option_categorias = MenuOption::where('name', 'CATEGORIAS')->first();
        $option_principio_activos = MenuOption::where('name', 'P. ACTIVOS')->first();
        $option_productos = MenuOption::where('name', 'PRODUCTOS')->first();
        $option_tarjetas = MenuOption::where('name', 'TARJETAS')->first();
        $option_transferencia = MenuOption::where('name', 'M. TRANSFERENCIA')->first();

        $option_requerimientos = MenuOption::where('name', 'REQUERIMIENTOS')->first();
        $option_compras = MenuOption::where('name', 'COMPRAS')->first();
        $option_guias_entrada = MenuOption::where('name', 'GUÍAS DE ENTRADA')->first();
        $option_doc_almacen = MenuOption::where('name', 'DOC. ALMACEN')->first();

        Action::create([
            'code' => 'create_transfer_method',
            'name' => 'PUEDE CREAR METODOS DE TRANSFERENCIA',
            'description' => '',
            'route' => 'create-transfer-method',
            'menu_option_id' => $option_transferencia->id,
        ]);

        Action::create([
            'code' => 'edit_transfer_method',
            'name' => 'PUEDE ACTUALIZAR METODOS DE TRANSFERENCIA',
            'description' => '',
            'route' => 'edit-transfer-method',
            'menu_option_id' => $option_transferencia->id,
        ]);

        Action::create([
            'code' => 'delete_transfer_method',
            'name' => 'PUEDE ELIMINAR METODOS DE TRANSFERENCIA',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_transferencia->id,
        ]);

        Action::create([
            'code' => 'create_card',
            'name' => 'PUEDE CREAR TARJETAS',
            'description' => '',
            'route' => 'create-card',
            'menu_option_id' => $option_tarjetas->id,
        ]);

        Action::create([
            'code' => 'edit_card',
            'name' => 'PUEDE ACTUALIZAR TARJETAS',
            'description' => '',
            'route' => 'edit-card',
            'menu_option_id' => $option_tarjetas->id,
        ]);

        Action::create([
            'code' => 'delete_card',
            'name' => 'PUEDE ELIMINAR TARJETAS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_tarjetas->id,
        ]);

        Action::create([
            'code' => 'create_product',
            'name' => 'PUEDE CREAR PRODUCTOS',
            'description' => '',
            'route' => 'create-product',
            'menu_option_id' => $option_productos->id,
        ]);

        Action::create([
            'code' => 'edit_product',
            'name' => 'PUEDE ACTUALIZAR PRODUCTOS',
            'description' => '',
            'route' => 'edit-product',
            'menu_option_id' => $option_productos->id,
        ]);

        Action::create([
            'code' => 'delete_product',
            'name' => 'PUEDE ELIMINAR PRODUCTOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_productos->id,
        ]);

        Action::create([
            'code' => 'change_status_product',
            'name' => 'PUEDE CAMBIAR ESTADOS DE PRODUCTOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_productos->id,
        ]);

        Action::create([
            'code' => 'create_active_ingredient',
            'name' => 'PUEDE CREAR PRINCIPIOS ACTIVOS',
            'description' => '',
            'route' => 'create-active-ingredient',
            'menu_option_id' => $option_principio_activos->id,
        ]);

        Action::create([
            'code' => 'edit_active_ingredient',
            'name' => 'PUEDE ACTUALIZAR PRINCIPIOS ACTIVOS',
            'description' => '',
            'route' => 'edit-active-ingredient',
            'menu_option_id' => $option_principio_activos->id,
        ]);

        Action::create([
            'code' => 'delete_active_ingredient',
            'name' => 'PUEDE ELIMINAR PRINCIPIOS ACTIVOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_principio_activos->id,
        ]);

        Action::create([
            'code' => 'create_category',
            'name' => 'PUEDE CREAR CATEGORIAS',
            'description' => '',
            'route' => 'create-category',
            'menu_option_id' => $option_categorias->id,
        ]);

        Action::create([
            'code' => 'edit_category',
            'name' => 'PUEDE ACTUALIZAR CATEGORIAS',
            'description' => '',
            'route' => 'edit-category',
            'menu_option_id' => $option_categorias->id,
        ]);

        Action::create([
            'code' => 'delete_category',
            'name' => 'PUEDE ELIMINAR CATEGORIAS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_categorias->id,
        ]);

        Action::create([
            'code' => 'create_unit',
            'name' => 'PUEDE CREAR UNIDADES',
            'description' => '',
            'route' => 'create-unit',
            'menu_option_id' => $option_unidades->id,
        ]);

        Action::create([
            'code' => 'edit_unit',
            'name' => 'PUEDE ACTUALIZAR UNIDADES',
            'description' => '',
            'route' => 'edit-unit',
            'menu_option_id' => $option_unidades->id,
        ]);

        Action::create([
            'code' => 'delete_unit',
            'name' => 'PUEDE ELIMINAR UNIDADES',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_unidades->id,
        ]);

        Action::create([
            'code' => 'create_brand',
            'name' => 'PUEDE CREAR MARCAS',
            'description' => '',
            'route' => 'create-brand',
            'menu_option_id' => $option_marca->id,
        ]);

        Action::create([
            'code' => 'edit_brand',
            'name' => 'PUEDE ACTUALIZAR MARCAS',
            'description' => '',
            'route' => 'edit-brand',
            'menu_option_id' => $option_marca->id,
        ]);

        Action::create([
            'code' => 'delete_brand',
            'name' => 'PUEDE ELIMINAR MARCAS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_marca->id,
        ]);

        Action::create([
            'code' => 'create_person',
            'name' => 'PUEDE CREAR PERSONAS',
            'description' => '',
            'route' => 'create-person',
            'menu_option_id' => $option_persona->id,
        ]);

        Action::create([
            'code' => 'edit_person',
            'name' => 'PUEDE ACTUALIZAR PERSONAS',
            'description' => '',
            'route' => 'edit-person',
            'menu_option_id' => $option_persona->id,
        ]);

        Action::create([
            'code' => 'delete_person',
            'name' => 'PUEDE ELIMINAR PERSONAS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_persona->id,
        ]);

        Action::create([
            'code' => 'edit_company',
            'name' => 'PUEDE ACTUALIZAR EMPRESA',
            'description' => '',
            'route' => 'edit-company',
            'menu_option_id' => $option_empresa->id,
        ]);



        Action::create([
            'code' => 'create_branch_office',
            'name' => 'PUEDE CREAR SUCURSALES',
            'description' => '',
            'route' => 'create-branch-office',
            'menu_option_id' => $option_sucursal->id,
        ]);

        Action::create([
            'code' => 'edit_branch_office',
            'name' => 'PUEDE ACTUALIZAR SUCURSALES',
            'description' => '',
            'route' => 'edit-branch-office',
            'menu_option_id' => $option_sucursal->id,
        ]);

        Action::create([
            'code' => 'delete_branch_office',
            'name' => 'PUEDE ELIMINAR SUCURSALES',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_sucursal->id,
        ]);

        Action::create([
            'code' => 'change_branch_office',
            'name' => 'PUEDE CAMBIAR DE SUCURSAL',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_sucursal->id,
        ]);

        Action::create([
            'code' => 'change_exchange_rate',
            'name' => 'PUEDE CAMBIAR TIPO CAMBIO',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_sucursal->id,
        ]);

        Action::create([
            'code' => 'create_user',
            'name' => 'PUEDE CREAR USUARIOS',
            'description' => '',
            'route' => 'create-user',
            'menu_option_id' => $option_usuario->id,
        ]);

        Action::create([
            'code' => 'edit_user',
            'name' => 'PUEDE ACTUALIZAR USUARIOS',
            'description' => '',
            'route' => 'edit-user',
            'menu_option_id' => $option_usuario->id,
        ]);

        Action::create([
            'code' => 'delete_user',
            'name' => 'PUEDE ELIMINAR USUARIOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_usuario->id,
        ]);

        Action::create([
            'code' => 'create_user_type',
            'name' => 'PUEDE CREAR TIPOS DE USUARIOS',
            'description' => '',
            'route' => 'create-user-type',
            'menu_option_id' => $option_tipo_usuario->id,
        ]);

        Action::create([
            'code' => 'edit_user_type',
            'name' => 'PUEDE ACTUALIZAR TIPOS DE USUARIOS',
            'description' => '',
            'route' => 'edit-user-type',
            'menu_option_id' => $option_tipo_usuario->id,
        ]);

        Action::create([
            'code' => 'delete_user_type',
            'name' => 'PUEDE ELIMINAR TIPOS DE USUARIOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_tipo_usuario->id,
        ]);

        Action::create([
            'code' => 'assign_permission',
            'name' => 'PUEDE ASIGNAR PERMISOS',
            'description' => '',
            'route' => 'assign-permission',
            'menu_option_id' => $option_tipo_usuario->id,
        ]);

        Action::create([
            'code' => 'create_menu_category',
            'name' => 'PUEDE CREAR CATEGORIAS DE MENU',
            'description' => '',
            'route' => 'create-menu-category',
            'menu_option_id' => $option_categoria->id,
        ]);

        Action::create([
            'code' => 'edit_menu_category',
            'name' => 'PUEDE ACTUALIZAR CATEGORIAS DE MENU',
            'description' => '',
            'route' => 'edit-menu-category',
            'menu_option_id' => $option_categoria->id,
        ]);

        Action::create([
            'code' => 'delete_menu_category',
            'name' => 'PUEDE ELIMINAR CATEGORIAS DE MENU',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_categoria->id,
        ]);

        Action::create([
            'code' => 'create_menu_option',
            'name' => 'PUEDE CREAR OPCIONES DE MENU',
            'description' => '',
            'route' => 'create-menu-option',
            'menu_option_id' => $option_opcion->id,
        ]);

        Action::create([
            'code' => 'edit_menu_option',
            'name' => 'PUEDE ACTUALIZAR OPCIONES DE MENU',
            'description' => '',
            'route' => 'edit-menu-option',
            'menu_option_id' => $option_opcion->id,
        ]);

        Action::create([
            'code' => 'delete_menu_option',
            'name' => 'PUEDE ELIMINAR OPCIONES DE MENU',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_opcion->id,
        ]);

        Action::create([
            'code' => 'create_action',
            'name' => 'PUEDE CREAR ACCIONES',
            'description' => '',
            'route' => 'create-action',
            'menu_option_id' => $option_accion->id,
        ]);

        Action::create([
            'code' => 'edit_action',
            'name' => 'PUEDE ACTUALIZAR ACCIONES',
            'description' => '',
            'route' => 'edit-action',
            'menu_option_id' => $option_accion->id,
        ]);

        Action::create([
            'code' => 'delete_action',
            'name' => 'PUEDE ELIMINAR ACCIONES',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_accion->id,
        ]);

        Action::create([
            'code' => 'create_requirement',
            'name' => 'PUEDE CREAR REQUERIMIENTOS',
            'description' => '',
            'route' => 'create-requirement',
            'menu_option_id' => $option_requerimientos->id,
        ]);

        Action::create([
            'code' => 'edit_requirement',
            'name' => 'PUEDE ACTUALIZAR REQUERIMIENTOS',
            'description' => '',
            'route' => 'edit-requirement',
            'menu_option_id' => $option_requerimientos->id,
        ]);

        Action::create([
            'code' => 'show_requirement',
            'name' => 'PUEDE VER DETALLES DE REQUERIMIENTOS DE COMPRA',
            'description' => '',
            'route' => 'show-requirement',
            'menu_option_id' => $option_requerimientos->id,
        ]);

        Action::create([
            'code' => 'delete_requirement',
            'name' => 'PUEDE ELIMINAR REQUERIMIENTOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_requerimientos->id,
        ]);

        Action::create([
            'code' => 'create_purchase',
            'name' => 'PUEDE CREAR COMPRAS',
            'description' => '',
            'route' => 'create-purchase',
            'menu_option_id' => $option_compras->id,
        ]);

        Action::create([
            'code' => 'show_purchase',
            'name' => 'PUEDE VER DETALLES DE COMPRA',
            'description' => '',
            'route' => 'show-purchase',
            'menu_option_id' => $option_compras->id,
        ]);

        Action::create([
            'code' => 'delete_purchase',
            'name' => 'PUEDE ELIMINAR COMPRAS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_compras->id,
        ]);

        Action::create([
            'code' => 'create_entry_guide',
            'name' => 'PUEDE CREAR GUIAS DE ENTRADA',
            'description' => '',
            'route' => 'create-entry-guide',
            'menu_option_id' => $option_guias_entrada->id,
        ]);

        Action::create([
            'code' => 'show_entry_guide',
            'name' => 'PUEDE VER DETALLES DE GUIAS DE ENTRADA',
            'description' => '',
            'route' => 'show-entry-guide',
            'menu_option_id' => $option_guias_entrada->id,
        ]);

        Action::create([
            'code' => 'delete_entry_guide',
            'name' => 'PUEDE ELIMINAR GUIAS DE ENTRADA',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_guias_entrada->id,
        ]);

        Action::create([
            'code' => 'dispatched_entry_guide',
            'name' => 'PUEDE CONFIRMAR INGRESO DE PRODUCTOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_guias_entrada->id,
        ]);

        Action::create([
            'code' => 'create_store_document',
            'name' => 'PUEDE CREAR DOCUMENTO DE ALMACEN',
            'description' => '',
            'route' => 'create-store-document',
            'menu_option_id' => $option_doc_almacen->id,
        ]);

        Action::create([
            'code' => 'show_store_document',
            'name' => 'PUEDE VER DETALLES DE DOCUMENTO DE ALMACEN',
            'description' => '',
            'route' => 'show-store-document',
            'menu_option_id' => $option_doc_almacen->id,
        ]);

        Action::create([
            'code' => 'delete_store_document',
            'name' => 'PUEDE ELIMINAR DOCUMENTO DE ALMACEN',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_doc_almacen->id,
        ]);

        Action::create([
            'code' => 'dispatched_store_document',
            'name' => 'PUEDE CONFIRMAR INGRESO DE PRODUCTOS',
            'description' => '',
            'route' => null,
            'menu_option_id' => $option_doc_almacen->id,
        ]);
    }
}
