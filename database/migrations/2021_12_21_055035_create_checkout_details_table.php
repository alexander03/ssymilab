<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checkout_id');
            $table->unsignedBigInteger('open_document_id');
            $table->unsignedBigInteger('close_document_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('open_date_time');
            $table->dateTime('close_date_time')->nullable();
            $table->timestamps();

            $table->foreign('checkout_id')->references('id')->on('checkouts');
            $table->foreign('open_document_id')->references('id')->on('documents');
            $table->foreign('close_document_id')->references('id')->on('documents');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkout_details');
    }
}
