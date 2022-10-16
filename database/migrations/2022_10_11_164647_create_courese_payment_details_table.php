<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouresePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courese_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->foreignId('payment_id');
            $table->string('amount');
            $table->foreign('payment_id')->references('id')->on("payment_hirstories")->onupdate("cascade");
            $table->foreign('course_id')->references('id')->on("courses")->onupdate("cascade");
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
        Schema::dropIfExists('courese_payment_details');
    }
}
