<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertMenuTableValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menu_exits = (DB::table('mxp_menu')->where([['name', 'Warehouse'],['description','Warehouse']])->select('menu_id')->first())->menu_id;
        
        $exits = DB::table('mxp_menu')->where('route_name', 'stored_product')->exists();

        if($exits != 1) {
            $id = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Stored Product Action',
                    'description' => 'Stored Product Action',
                    'route_name' => 'stored_product',
                    'parent_id' => $menu_exits,
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

        $exits_1 = DB::table('mxp_menu')->where('route_name', 'stored_item')->exists();

        if($exits_1 != 1) {
            $id_1 = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Stored Item Action',
                    'description' => 'Stored Item Action',
                    'route_name' => 'stored_item',
                    'parent_id' => $menu_exits,
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

        $exits_2 = DB::table('mxp_menu')->where('route_name', 'store_ipo_list')->exists();

        if($exits_2 != 1) {
            $id_2 = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Stored IPO List',
                    'description' => 'Stored IPO List',
                    'route_name' => 'store_ipo_list',
                    'parent_id' => $menu_exits,
                    'is_active' => 1,
                    'order_id' => 0
                )
            );

            DB::table('mxp_user_role_menu')->insert(
                array(
                    'menu_id' => $id_2,
                    'role_id' => 1,
                    'is_active' => 1
                )
            );
        }

        $exits_3 = DB::table('mxp_menu')->where('route_name', 'store_mrf_list')->exists();

        if($exits_3 != 1) {
            $id_3 = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Stored MRF List',
                    'description' => 'Stored MRF List',
                    'route_name' => 'store_mrf_list',
                    'parent_id' => $menu_exits,
                    'is_active' => 1,
                    'order_id' => 0
                )
            );

            DB::table('mxp_user_role_menu')->insert(
                array(
                    'menu_id' => $id_3,
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
