<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReportProductClientMenu extends Migration
{
    /**
     * Run the migrations.
     * OPCION DE MENU REPORTES > R. PRODUCTOS POR CLIENTE Y SUS ACCIONES (PDF / EXCEL)
     *
     * @return void
     */
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $menu_option_id = DB::table('menu_options')->insertGetId([
            'name' => 'R. PRODUCTOS POR CLIENTE',
            'order' => 10,
            'link' => 'report-product-client',
            'menu_category_id' => 8,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('actions')->insert([
            ['code' => 'pdf_report_product_client', 'name' => 'PUEDE GENERAR PDF DE PRODUCTOS POR CLIENTE', 'description' => '', 'route' => 'report-pdf-product-client', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'excel_report_product_client', 'name' => 'PUEDE GENERAR EXCEL DE PRODUCTOS POR CLIENTE', 'description' => '', 'route' => 'report-excel-product-client', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menu_option = DB::table('menu_options')->where('link', 'report-product-client')->first();
        if($menu_option){
            $action_ids = DB::table('actions')->where('menu_option_id', $menu_option->id)->pluck('id');
            DB::table('operations')->whereIn('action_id', $action_ids)->delete();
            DB::table('actions')->whereIn('id', $action_ids)->delete();
            DB::table('permissions')->where('menu_option_id', $menu_option->id)->delete();
            DB::table('menu_options')->where('id', $menu_option->id)->delete();
        }
    }
}
