<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGCUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_c_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('password2');
            $table->longText('token')->nullable();
            $table->longText('refreshToken')->nullable();
            $table->boolean('status')->default(1);
            $table->string('text1')->nullable();
            $table->foreign('user_id')->references('id')->on("users")->onupdate("cascade");
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
        Schema::dropIfExists('g_c_users');
    }
}
