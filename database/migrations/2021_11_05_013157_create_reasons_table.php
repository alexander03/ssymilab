<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('document_type_id')->references('id')->on('document_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reasons');
    }
}
