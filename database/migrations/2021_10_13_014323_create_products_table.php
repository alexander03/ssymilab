<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('type', 1); // M : Maquinaria, R : Reactivos
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->string('igv', 2)->default('10');
            $table->decimal('security_stock', 10, 3)->default(0.000);
            $table->string('affects_stock', 1)->default('N');
            $table->text('specifications')->nullable();
            $table->string('status', 1)->default('Y');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('base_unit_id');
            $table->unsignedBigInteger('base_currency_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('supplier_id')->references('id')->on('people');
            $table->foreign('base_unit_id')->references('id')->on('units');
            $table->foreign('base_currency_id')->references('id')->on('currencies');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
