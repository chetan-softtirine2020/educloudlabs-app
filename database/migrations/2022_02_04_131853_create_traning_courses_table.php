<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraningCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traning_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_id')->nullable();   
            $table->string('name');
            $table->boolean('is_complete')->default(0);
            $table->dateTime('completed_date')->nullable();
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
        Schema::dropIfExists('traning_courses');
    }
}
