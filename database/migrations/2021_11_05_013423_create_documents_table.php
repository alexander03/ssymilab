<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('document_type_id');
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('shipper_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_office_id');
            $table->unsignedBigInteger('checkout_id')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('ubigeo_start_code', 6)->nullable();
            $table->string('ubige_end_code', 6)->nullable();
            $table->string('type', 1); // C: Contado, D:Credito
            $table->string('number', 20);
            $table->dateTime('date_time');
            $table->date('schedule_time')->nullable();
            $table->decimal('total_taxed', 10 , 3)->nullable();
            $table->decimal('total_exonerated', 10 , 3)->nullable();
            $table->decimal('total_unaffected', 10 , 3)->nullable();
            $table->decimal('igv', 10 , 3)->nullable();
            $table->decimal('icbper', 10 , 3)->nullable();
            $table->decimal('global_discount', 10 , 3)->nullable();
            $table->decimal('discount', 10 , 3)->nullable();
            $table->decimal('total', 10 , 3)->nullable();
            $table->decimal('exchange_rate', 10 , 3)->nullable();
            $table->decimal('cash_amount', 10 , 3)->nullable();
            $table->decimal('card_amount', 10 , 3)->nullable();
            $table->decimal('transfer_amount', 10 , 3)->nullable();
            $table->decimal('pending_amount', 10 , 3)->nullable();
            $table->decimal('paid', 10 , 3)->nullable();
            $table->decimal('money_back', 10 , 3)->nullable();
            $table->decimal('weight', 10 , 3)->nullable();
            $table->text('detail')->nullable();
            $table->string('dispatched')->default('Y'); // Y:SI N:NO.
            $table->string('status')->default('Y'); // Y:CREADO A:ANULADO C:COBRADO/PAGADO P:PENDIENTE.
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->foreign('client_id')->references('id')->on('people');
            $table->foreign('shipper_id')->references('id')->on('people');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('branch_office_id')->references('id')->on('branch_offices');
            $table->foreign('checkout_id')->references('id')->on('checkouts');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('ubigeo_start_code')->references('code')->on('districts');
            $table->foreign('ubige_end_code')->references('code')->on('districts');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
