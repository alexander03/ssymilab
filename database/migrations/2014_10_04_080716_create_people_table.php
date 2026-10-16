<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('type', 1); // Natural:1 , Juridica: 6
            $table->string('dni', 8)->nullable();
            $table->string('ruc', 25)->nullable();
            $table->string('name')->nullable();
            $table->string('surname_father')->nullable();
            $table->string('surname_mother')->nullable();
            $table->string('business_name')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
