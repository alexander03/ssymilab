<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeclarationTypesToPeopleTable extends Migration
{
    /**
     * Run the migrations.
     * DECLARACIONES PERMITIDAS AL CLIENTE: "C" DECLARAR AL CONTADO, "D" DECLARAR CON CUOTAS, "C,D" AMBAS, NULL = AMBAS
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('declaration_types', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('declaration_types');
        });
    }
}
