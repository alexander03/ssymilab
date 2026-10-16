<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->string('department_id', 2)->nullable();
            $table->string('province_id', 4)->nullable();
            $table->string('district_id', 6)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('branch_offices');
    }
}
