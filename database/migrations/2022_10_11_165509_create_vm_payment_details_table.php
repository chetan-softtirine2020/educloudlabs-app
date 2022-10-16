<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vmused_id');
            $table->foreignId('payment_id');
            $table->string('amount');
            $table->foreign('payment_id')->references('id')->on("payment_hirstories")->onupdate("cascade");
            $table->foreign('vmused_id')->references('id')->on("v_m_useds")->onupdate("cascade");
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
        Schema::dropIfExists('vm_payment_details');
    }
}
