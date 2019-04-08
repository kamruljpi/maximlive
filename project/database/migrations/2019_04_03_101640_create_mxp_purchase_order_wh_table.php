<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpPurchaseOrderWhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_purchase_order_wh', function (Blueprint $table) {
            $table->increments('id_purchase_order_wh');
            $table->integer('user_id')->nullable();
            $table->string('status')->nullable();
            $table->date('order_date')->nullable();
            $table->string('purchase_order_no')->nullable();
            $table->string('purchase_voucher')->nullable();
            $table->string('bilty_no')->nullable();
            $table->string('discount')->nullable();
            $table->string('vat')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('paying_by')->nullable();
            $table->string('in_all_total_price')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('mxp_purchase_order');
    }
}
