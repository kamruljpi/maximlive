<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMrfTableField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mxp_mrf_table', function (Blueprint $table) {
            $table->string('current_status_accepted_user_id')->nullable();
            $table->date('current_status_accepted_date_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mxp_mrf_table', function (Blueprint $table) {
            //
        });
    }
}
