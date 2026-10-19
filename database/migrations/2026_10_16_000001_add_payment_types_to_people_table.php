<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentTypesToPeopleTable extends Migration
{
    /**
     * Run the migrations.
     * FORMAS DE PAGO PERMITIDAS AL CLIENTE: "C" CONTADO, "D" CREDITO, "C,D" AMBAS, NULL = AMBAS
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('payment_types', 10)->nullable();
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
            $table->dropColumn('payment_types');
        });
    }
}
