<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing_charts', function (Blueprint $table) {
            $table->id();
            $table->integer('memory');
            $table->integer('virtual_cpu')->nullable();
            $table->float('linux');
            $table->float('windows');
            $table->float('ubuntu')->nullable();
            $table->float('debian');
            $table->float('storage_price')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('pricing_charts');
    }
}
