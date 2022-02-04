<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrgTrainingUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('org_training_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('training_id')->nullable();                    
            $table->unsignedBigInteger('org_id')->nullable();         
            $table->boolean('is_join')->default(0);
            $table->boolean('status')->default(1);
            $table->foreign('org_id')->references('id')->on("users")->onupdate("cascade");
            $table->foreign('user_id')->references('id')->on("users")->onupdate("cascade");
            $table->foreign('training_id')->references('id')->on("org_trainings")->onupdate("cascade");           
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
        Schema::dropIfExists('org_training_users');
    }
}
