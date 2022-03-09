<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrgDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('org_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');   
            $table->foreignId('org_id');   
            $table->foreignId('branch_id')->nullable();   
            $table->foreignId('department_id')->nullable();   
            $table->foreignId('section_id')->nullable(); 
            $table->boolean('status')->default(1);
            $table->foreign('user_id')->references('id')->on("users")->onupdate("cascade");  
            $table->foreign('org_id')->references('id')->on("organizations")->onupdate("cascade");
            $table->foreign('branch_id')->references('id')->on("branches")->onupdate("cascade");
            $table->foreign('department_id')->references('id')->on("departments")->onupdate("cascade");
            $table->foreign('section_id')->references('id')->on("sections")->onupdate("cascade");
          
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
        Schema::dropIfExists('org_details');
    }
}
