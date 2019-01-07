<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class InsertOpeningStockMenuTableData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exits = DB::table('mxp_menu')->where('route_name', 'opening_stock_view')->exists();

        if($exits != 1) {
            $id = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Opening Stock View',
                    'description' => 'Opening Stock View',
                    'route_name' => 'opening_stock_view',
                    'parent_id' => 75,
                    'is_active' => 1,
                    'order_id' => 0
                )
            );

            DB::table('mxp_user_role_menu')->insert(
                array(
                    'menu_id' => $id,
                    'role_id' => 1,
                    'is_active' => 1
                )
            );
        }


        $exits_1 = DB::table('mxp_menu')->where('route_name', 'store_opening_stock_action')->exists();

        if($exits_1 != 1) {
            $id_1 = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Store Opening Stock View',
                    'description' => 'Store Opening Stock View',
                    'route_name' => 'store_opening_stock_action',
                    'parent_id' => 75,
                    'is_active' => 1,
                    'order_id' => 0
                )
            );

            DB::table('mxp_user_role_menu')->insert(
                array(
                    'menu_id' => $id_1,
                    'role_id' => 1,
                    'is_active' => 1
                )
            );
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
