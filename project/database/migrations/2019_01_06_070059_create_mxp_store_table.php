<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_store', function (Blueprint $table) {
            $table->increments('id_store');
            $table->integer('user_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('booking_order_id')->nullable();
            $table->string('erp_code')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_quantity')->nullable();
            $table->string('item_size')->nullable();
            $table->string('item_color')->nullable();
            $table->string('is_type')->nullable();
            $table->string('status')->nullable();

            $table->tinyInteger('is_deleted')->default('0');
            $table->integer('deleted_user_id')->default('0');
            $table->date('deleted_date_at')->nullable();
            $table->date('last_action_at')->nullable();

            $table->date('receive_date')->nullable();
            $table->date('shipment_date')->nullable();
            
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
        Schema::dropIfExists('mxp_store');
    }
}
