<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11);
            $table->string('business_name');
            $table->string('trade_name');
            $table->string('address')->nullable();
            $table->string('password')->nullable();
            $table->string('authentication_mode', 1)->nullable();
            $table->string('username_sunat')->nullable();
            $table->string('password_sunat')->nullable();
            $table->string('department_id', 2)->nullable();
            $table->string('province_id', 4)->nullable();
            $table->string('district_id', 6)->nullable();
            $table->string('status', 1)->default('Y');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('department_id')->references('code')->on('departments');
            $table->foreign('province_id')->references('code')->on('provinces');
            $table->foreign('district_id')->references('code')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
