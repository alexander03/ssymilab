<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReportProductSaleMenu extends Migration
{
    /**
     * Run the migrations.
     * OPCION DE MENU REPORTES > R. VENTAS POR PRODUCTO Y SUS ACCIONES (PDF / EXCEL)
     *
     * @return void
     */
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $menu_option_id = DB::table('menu_options')->insertGetId([
            'name' => 'R. VENTAS POR PRODUCTO',
            'order' => 11,
            'link' => 'report-product-sale',
            'menu_category_id' => 8,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('actions')->insert([
            ['code' => 'pdf_report_product_sale', 'name' => 'PUEDE GENERAR PDF DE VENTAS POR PRODUCTO', 'description' => '', 'route' => 'report-pdf-product-sale', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'excel_report_product_sale', 'name' => 'PUEDE GENERAR EXCEL DE VENTAS POR PRODUCTO', 'description' => '', 'route' => 'report-excel-product-sale', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menu_option = DB::table('menu_options')->where('link', 'report-product-sale')->first();
        if($menu_option){
            $action_ids = DB::table('actions')->where('menu_option_id', $menu_option->id)->pluck('id');
            DB::table('operations')->whereIn('action_id', $action_ids)->delete();
            DB::table('actions')->whereIn('id', $action_ids)->delete();
            DB::table('permissions')->where('menu_option_id', $menu_option->id)->delete();
            DB::table('menu_options')->where('id', $menu_option->id)->delete();
        }
    }
}
