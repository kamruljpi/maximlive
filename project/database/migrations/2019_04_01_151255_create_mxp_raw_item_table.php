<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpRawItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_raw_item', function (Blueprint $table) {
            $table->increments('id_raw_item');
            $table->string('user_id')->nullable();
            $table->string('item_code');
            $table->string('item_name')->nullable();
            $table->string('price')->nullable();
            $table->string('sort_description')->nullable();
            $table->string('opening_quantity')->nullable();
            $table->string('is_active')->default('1');
            $table->string('is_deleted')->default('0');
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
        Schema::dropIfExists('mxp_raw_item');
    }
}
