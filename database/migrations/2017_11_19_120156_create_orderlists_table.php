<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('required_date')->nullable();//درخواست
            $table->string('shipped_date')->nullable();//ارسال
            $table->string('price');//مبلغ
            $table->string('description')->nullable();
            $table->text('shipped_address');
            $table->string('status')->default('pending');//pending , canceled , confirmed , sent , delivered
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderlists');
    }
}
