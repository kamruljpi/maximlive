<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationEntryFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_location', function (Blueprint $table) {
            $table->increments('id_location');
            $table->integer('user_id');
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->integer('is_deleted')->default(0);
            $table->integer('deleted_user_id')->nullable();
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
        Schema::dropIfExists('mxp_locations');
    }
}
