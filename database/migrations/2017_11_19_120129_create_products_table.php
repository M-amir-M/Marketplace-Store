<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('quantity')->nullable();
            $table->string('amount');
            $table->string('price1');
            $table->string('price2');
            $table->string('price3');
            $table->string('min_order')->nullable();
            $table->string('image');
            $table->text('description')->nullable();
            $table->integer('brand_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
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
        Schema::dropIfExists('products');
    }
}
