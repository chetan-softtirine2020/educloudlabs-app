<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('training_id');
            $table->integer('join_count')->default(0);
            $table->integer('total_join')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
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
        Schema::dropIfExists('training_infos');
    }
}
