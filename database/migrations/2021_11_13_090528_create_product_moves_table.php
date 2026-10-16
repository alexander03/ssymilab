<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_moves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('document_detail_id')->nullable();
            $table->unsignedBigInteger('guide_detail_id')->nullable();
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->unsignedBigInteger('product_serie_id')->nullable();
            $table->decimal('quantity', 10, 3);
            $table->decimal('dispatched_quantity', 10, 3)->default(0.00);
            $table->string('type',1); // I, E
            $table->string('status',1); // A:ANULADO, P:PENDIENTE, N:CON GUIA, Y:COMPLETO
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('document_detail_id')->references('id')->on('document_details');
            $table->foreign('guide_detail_id')->references('id')->on('document_details');
            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('lot_id')->references('id')->on('lots');
            $table->foreign('product_serie_id')->references('id')->on('product_series');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_moves');
    }
}
