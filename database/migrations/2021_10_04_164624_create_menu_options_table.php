<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order');
            $table->string('link');
            $table->unsignedBigInteger('menu_category_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('menu_category_id')->references('id')->on('menu_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_options');
    }
}
