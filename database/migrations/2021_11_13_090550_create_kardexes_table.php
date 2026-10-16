<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();
            $table->string('type', 1); // I, E
            $table->decimal('quantity', 10, 3);
            $table->decimal('exchange_rate', 10, 3);
            $table->decimal('price', 10, 3)->default(0.000);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kardexes');
    }
}
