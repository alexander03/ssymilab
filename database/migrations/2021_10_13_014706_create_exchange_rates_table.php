<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('value', 10, 3);
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('base_currency_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('base_currency_id')->references('id')->on('currencies');
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
        Schema::dropIfExists('exchange_rates');
    }
}
