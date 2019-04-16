<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_warehouse_type', function (Blueprint $table) {
            $table->increments('id_warehouse_type');
            $table->string('warehouse_in_out_type')->nullable();
            $table->string('status')->nullable();
            $table->date('last_action_at')->nullable();
            $table->tinyInteger('is_deleted')->default('0');
            $table->integer('deleted_user_id')->default('0');
            $table->integer('user_id')->nullable();
            $table->string('warehouse_type')->nullable();
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
        Schema::dropIfExists('mxp_warehouse_type');
    }
}
