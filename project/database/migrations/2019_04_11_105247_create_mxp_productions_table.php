<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMxpProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mxp_productions', function (Blueprint $table) {
            $table->increments('id_mxp_productions');
            $table->date('production_date')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('mxp_productions');
    }
}
