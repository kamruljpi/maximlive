<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_item_cost_price', function (Blueprint $table) {
            $table->increments('cost_price_id');
            $table->integer('id_product');
            $table->integer('user_id');
            $table->string('price_1')->nullable();
            $table->string('price_2')->nullable();
            $table->string('last_action');
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
        Schema::dropIfExists('mxp_item_cost_price');
    }
}
