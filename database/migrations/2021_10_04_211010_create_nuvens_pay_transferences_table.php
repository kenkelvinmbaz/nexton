<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNuvensPayTransferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount',19,2);
            $table->string('order_number')->unique();
            $table->string('ip_address',25);
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on('user')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('receiver_id')->unsigned();
            $table->foreign('receiver_id')->references('id')->on('user')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('transfer');
    }
}
