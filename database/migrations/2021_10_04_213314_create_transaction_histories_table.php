<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount',19,2);
            $table->string('order_number');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('user')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('user_origin_id')->unsigned();
            $table->foreign('user_origin_id')->references('id')->on('user')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('user_destiny_id')->unsigned();
            $table->foreign('user_destiny_id')->references('id')->on('user')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('transaction_history');
    }
}
