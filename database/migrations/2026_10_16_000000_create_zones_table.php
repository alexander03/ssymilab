<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->constrained('zones');
        });

        //OPCION DE MENU MANTENIMIENTO > ZONAS Y SUS ACCIONES
        $now = date('Y-m-d H:i:s');
        $menu_option_id = DB::table('menu_options')->insertGetId([
            'name' => 'ZONAS',
            'order' => 8,
            'link' => 'zone',
            'menu_category_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('actions')->insert([
            ['code' => 'create_zone', 'name' => 'PUEDE CREAR ZONAS', 'description' => '', 'route' => 'create-zone', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'edit_zone', 'name' => 'PUEDE ACTUALIZAR ZONAS', 'description' => '', 'route' => 'edit-zone', 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'delete_zone', 'name' => 'PUEDE ELIMINAR ZONAS', 'description' => '', 'route' => null, 'menu_option_id' => $menu_option_id, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menu_option = DB::table('menu_options')->where('link', 'zone')->first();
        if($menu_option){
            $action_ids = DB::table('actions')->where('menu_option_id', $menu_option->id)->pluck('id');
            DB::table('operations')->whereIn('action_id', $action_ids)->delete();
            DB::table('actions')->whereIn('id', $action_ids)->delete();
            DB::table('permissions')->where('menu_option_id', $menu_option->id)->delete();
            DB::table('menu_options')->where('id', $menu_option->id)->delete();
        }

        Schema::table('people', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zone_id');
        });

        Schema::dropIfExists('zones');
    }
}
