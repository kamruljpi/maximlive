<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_stage', function (Blueprint $table) {
            $table->increments('id_stage');
            $table->integer('user_id')->nullable();
            $table->string('name');
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
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
        Schema::dropIfExists('mxp_stage');
    }
}
