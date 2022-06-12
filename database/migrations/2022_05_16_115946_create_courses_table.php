<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string("name");
            $table->string("slug")->unique();
            $table->longText('description')->nullable();
            $table->integer("amount")->nullable();
            $table->boolean("is_public")->default(0);
            $table->boolean("status")->default(1);
            $table->boolean("is_paid")->default(1);           
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
        Schema::dropIfExists('courses');
    }
}
