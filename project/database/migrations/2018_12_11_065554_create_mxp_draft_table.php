<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpDraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_draft', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('booking_order_id')->nullable();
            $table->string('erp_code')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_size')->nullable();
            $table->string('item_description')->nullable();
            $table->string('item_quantity')->nullable();
            $table->string('item_price')->nullable();
            $table->string('material')->nullable();
            $table->string('gmts_color')->nullable();
            $table->string('others_color')->nullable();
            $table->string('season_code')->nullable();
            $table->string('oos_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('style')->nullable();
            $table->string('is_type')->nullable();
            $table->string('is_pi_type')->nullable();
            $table->string('item_size_width_height')->nullable();
            $table->integer('orderNo')->nullable();
            $table->integer('poCatNo')->nullable();

            $table->tinyInteger('is_deleted')->default('0');
            $table->integer('deleted_user_id')->default('0');
            $table->date('deleted_date_at')->nullable();
            $table->date('last_action_at')->nullable();

            $table->date('order_date')->nullable();
            $table->date('orderDate')->nullable();
            $table->date('shipmentDate')->nullable();
            
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
        Schema::dropIfExists('mxp_draft');
    }
}
