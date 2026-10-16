<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->decimal('formula', 10 , 3);
            $table->decimal('purchase_price', 10, 3)->default(0.000);
            $table->decimal('sell_price', 10, 3);
            $table->decimal('whosale_price', 10 , 3)->nullable();
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('base_unit_id');
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('base_unit_id')->references('id')->on('units');
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
        Schema::dropIfExists('product_units');
    }
}
