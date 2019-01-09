<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreTableFiled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mxp_store', function (Blueprint $table) {
            // $table->integer('location_id')->nullable();
            // $table->integer('warehouse_type_id')->nullable();
            // $table->integer('warehouse_entry_date')->nullable();
            // $table->date('warehouse_user_id')->nullable();
            $table->smallInteger('stock_type')->defualt(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mxp_store', function (Blueprint $table) {
            //
        });
    }
}
