<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVMDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('v_m_details', function (Blueprint $table) {
            $table->string('name')->nullable()->after('vm_name');
            $table->timestamp('deleted')->nullable()->after('created');
            $table->float('used_min')->default(0)->after('status');
            $table->float('vm_cost')->default(0)->after('used_min');
            $table->float('storage_cost')->default(0)->after('vm_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('v_m_details', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('deleted');            
            $table->dropColumn('used_min');
            $table->dropColumn('vm_cost');
            $table->dropColumn('storage_cost');

        });
    }
}
