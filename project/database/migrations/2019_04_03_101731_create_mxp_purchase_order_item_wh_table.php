<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpPurchaseOrderItemWhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_purchase_order_item_wh', function (Blueprint $table) {
            $table->increments('id_purchase_order_item_wh');
            $table->integer('user_id')->nullable();
            $table->string('status')->nullable();
            $table->integer('purchase_order_wh_id')->nullable();
            $table->integer('raw_item_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_qty')->nullable();
            $table->string('price')->nullable();
            $table->string('total_price')->nullable();
            $table->integer('is_deleted')->default('0');
            $table->string('last_action_at')->nullable();
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
        Schema::dropIfExists('mxp_purchase_order_item');
    }
}
