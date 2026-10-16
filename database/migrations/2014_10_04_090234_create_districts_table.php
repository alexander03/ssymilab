<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->string('code', 6)->primary();
            $table->string('name');
            $table->string('department_id', 2);
            $table->string('province_id', 4);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('department_id')->references('code')->on('departments');
            $table->foreign('province_id')->references('code')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
}
