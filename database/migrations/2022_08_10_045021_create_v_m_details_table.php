<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVMDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v_m_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('vm_name');
            $table->string('image');
            $table->string('zone');
            $table->enum('protocol', ['ssh', 'rdp']);
            $table->string('storage')->default(10);
            $table->string('ram')->default(0.5);
            $table->string('softwares');
            $table->timestamp('created');
            $table->boolean('status')->default(1);
            $table->string('total_cost')->default(0);
            $table->boolean('is_assign')->default(0);
            $table->foreign('user_id')->references('id')->on("g_c_users")->onupdate("cascade");
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
        Schema::dropIfExists('v_m_details');
    }
}
