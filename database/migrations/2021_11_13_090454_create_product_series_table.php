<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_series', function (Blueprint $table) {
            $table->id();
            $table->string('serie');
            $table->string('status', 1); // A:ANULADO, P:PENDIENTE, V:VENDIDO, S:SESION DE USO, Y:HABILITADO
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('branch_office_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_series');
    }
}
