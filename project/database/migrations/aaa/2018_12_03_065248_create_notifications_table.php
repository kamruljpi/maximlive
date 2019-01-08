<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('type_id')->nullable();
            $table->tinyInteger('seen')->default('0');
            $table->integer('seen_user_id')->nullable();
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
        Schema::dropIfExists('mxp_notifications');
    }
}
