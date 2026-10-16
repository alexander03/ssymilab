<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('document_payment_id');
            $table->unsignedBigInteger('currency_id');
            $table->dateTime('date_time');
            $table->string('type'); // E:EFECTIVO, C:TARJETA, T:TRANSFERENCIA
            $table->string('method_name'); //EFECTIVO, MASTERCARD ... EL NOMBRE DE LATABLA CARDS O TRANSFER METHOD
            $table->decimal('amount', 10 , 3);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('document_payment_id')->references('id')->on('document_payments');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
