<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMxpPurchaseItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mxp_purchase_order_item_wh', function (Blueprint $table) {
            $table->integer('from_user_id')->nullable();
            // $table->renameColumn('user_id', 'to_user_id');
            $table->integer('is_rejected')->nullable()->default('0');
            $table->integer('rejected_user_id')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('to_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mxp_purchase_order_item_wh', function (Blueprint $table) {
            //
        });
    }
}
