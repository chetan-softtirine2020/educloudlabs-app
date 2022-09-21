<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVMUsedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v_m_useds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vm_id');
            // $table->foreignId('assign_by');    
            $table->string('assign_by');
            $table->foreignId('assign_user_id');
            $table->timestamp('vm_start')->nullable();
            $table->timestamp('vm_stop')->nullable();
            $table->string('used_min')->default(0);
            $table->string('cost')->default(0);
            $table->boolean('status')->default(1);
            $table->foreign('assign_user_id')->references('id')->on("users")->onupdate("cascade");
            // $table->foreign('assign_by')->references('id')->on("users")->onupdate("cascade");
            $table->foreign('vm_id')->references('id')->on("v_m_details")->onupdate("cascade");
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
        Schema::dropIfExists('v_m_useds');
    }
}
