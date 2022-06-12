<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id');
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->longText('url')->nullable();
            $table->boolean("status")->default(1);
           //$table->unique(['name', 'module_id']);
            $table->foreign('module_id')->references('id')->on("modules")->onupdate("cascade");
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
        Schema::dropIfExists('topics');
    }
}
