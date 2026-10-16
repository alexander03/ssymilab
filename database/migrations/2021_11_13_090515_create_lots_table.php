<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot');
            $table->date('due_date');
            $table->string('status');
            $table->decimal('quantity', 10, 3);
            $table->decimal('remain', 10, 3);
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
        Schema::dropIfExists('lots');
    }
}
