<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('status', 1)->default('Y');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('user_type_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('user_type_id')->references('id')->on('user_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
