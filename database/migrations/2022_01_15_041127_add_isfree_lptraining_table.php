<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsfreeLptrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('l_p_trainings', function (Blueprint $table) {
            $table->boolean('is_public')->default(0);
            $table->boolean('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('l_p_trainings', function (Blueprint $table) {
            $table->dropColumn('is_public');
            $table->dropColumn('created_by');
        });
    }
}
