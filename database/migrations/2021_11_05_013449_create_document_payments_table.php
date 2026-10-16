<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('currency_id');
            $table->string('name');
            $table->dateTime('date_time');
            $table->date('schedule_date')->nullable();
            $table->decimal('total_amount', 10, 3)->default(0);
            $table->decimal('cash_amount', 10, 3)->default(0);
            $table->decimal('card_amount', 10, 3)->default(0);
            $table->decimal('transfer_amount', 10, 3)->default(0);
            $table->decimal('exchange_rate', 10, 3)->default(1);
            $table->string('status')->default('Y'); //Y:ACTIVO, P:PENDIENTE (PROGRAMDO), A:ANULADO
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
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
        Schema::dropIfExists('document_payments');
    }
}
