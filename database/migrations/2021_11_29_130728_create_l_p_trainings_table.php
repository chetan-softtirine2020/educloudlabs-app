<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLPTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('l_p_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('link');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_paid')->default(0);
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
        Schema::dropIfExists('l_p_trainings');
    }
}
