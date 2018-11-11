<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsPoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_os_po', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_id');
            $table->integer('user_id');
            $table->integer('job_id');
            $table->string('mrf_id');
            $table->integer('supplier_id');
            $table->string('supplier_price');
            $table->string('material')->nullable();
            $table->integer('is_deleted')->default(0);
            $table->integer('deleted_user_id')->nullable();
            $table->date('order_date');
            $table->date('shipment_date');
            $table->string('last_action_at');
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
        Schema::dropIfExists('mxp_os_po');
    }
}
