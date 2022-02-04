<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLPTUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('l_p_t_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('training_id')->nullable();                    
            $table->unsignedBigInteger('provider_id')->nullable();         
            $table->boolean('is_join')->default(0);
            $table->boolean('status')->default(1);
            $table->foreign('provider_id')->references('id')->on("users")->onupdate("cascade");
            $table->foreign('user_id')->references('id')->on("users")->onupdate("cascade");
            $table->foreign('training_id')->references('id')->on("l_p_trainings")->onupdate("cascade");
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
        Schema::dropIfExists('l_p_t_users');
    }
}
