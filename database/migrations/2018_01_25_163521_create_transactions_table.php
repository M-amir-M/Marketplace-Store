<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank')->nullable();
            $table->integer('price');
            $table->integer('customer_id');
            $table->integer('orderlist_id');
            $table->string('track_code')->nullable();//کد پیگیری
            $table->string('return_code')->nullable();
            $table->string('result_code')->nullable();
            $table->string('status');//success or not
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
