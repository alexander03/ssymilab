<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('document_detail_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->integer('bags')->nullable();
            $table->decimal('unit_value', 10, 3)->default(0);
            $table->decimal('igv', 10, 3)->default(0);
            $table->decimal('sell_price', 10, 3)->default(0);
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('icbper', 10, 3)->default(0);
            $table->decimal('subtotal', 10, 3)->default(0);
            $table->decimal('purchase_price', 10, 3)->default(0);
            $table->decimal('dispatched_quantity', 10, 3)->nullable();
            $table->string('status', 1)->default('Y'); // Y:OK N:PENDIENTE
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        Schema::table('document_details', function (Blueprint $table) {
            $table->foreign('document_detail_id')->references('id')->on('document_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_details');
    }
}
