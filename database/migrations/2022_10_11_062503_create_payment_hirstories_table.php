<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHirstoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_hirstories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('payment_id');
            $table->text('email')->nullable();
            $table->text('mobile')->nullable();
            $table->text('amount');
            $table->boolean('status')->default(0);
            $table->boolean('payment_for')->default(1);
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
        Schema::dropIfExists('payment_hirstories');
    }
}
