<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpProductionFinishedItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_production_finished_item', function (Blueprint $table) {
            $table->increments('id_production_finished_item');
            $table->string('production_id')->nullable();
            $table->string('item_code');
            $table->string('item_size')->nullable();
            $table->string('item_color')->nullable();
            $table->integer('quantity');
            $table->string('status')->nullable();

            $table->tinyInteger('is_deleted')->default('0');
            $table->integer('deleted_user_id')->default('0');
            $table->date('deleted_date_at')->nullable();
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
        Schema::dropIfExists('mxp_production_finished_item');
    }
}
