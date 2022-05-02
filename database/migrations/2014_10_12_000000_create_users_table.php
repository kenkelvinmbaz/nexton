<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->string('last_name',50);
            $table->string('email',250)->unique();
            $table->string('phone_number',19)->unique();
            $table->string('cpf',14)->unique();
            $table->longText('password');
            $table->longText('pin');
            $table->integer('user_role_id')->unsigned();
            $table->foreign('user_role_id')->references('id')->on('user_role')->onUpdate('cascade')->onDelete('cascade');
            $table->char('status',10);
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
        Schema::dropIfExists('user');
    }
}
