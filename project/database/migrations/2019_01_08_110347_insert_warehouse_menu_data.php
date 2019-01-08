<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class InsertWarehouseMenuData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Start WareHouse Menu*/

        $menu_exits = DB::table('mxp_menu')->where([['name', 'Warehouse'],['description','Warehouse']])->exists();

        if($exits != 1) {
            $menu_id = DB::table('mxp_menu')->insertGetId(
                array(
                    'name' => 'Warehouse',
                    'description' => 'Warehouse',
                    'parent_id' => 0,
                    'is_active' => 1,
                    'order_id' => 0
                )
            );

            DB::table('mxp_user_role_menu')->insert(
                    array(
                        'menu_id' => $menu_id,
                        'role_id' => 1,
                        'is_active' => 1
                    )
                );
        }

        /* End WareHouse Menu*/

        if(!empty($menu_id)) {

            /* Start Opening Stock*/

            $exits = DB::table('mxp_menu')->where('route_name', 'opening_stock_view')->exists();

            if($exits != 1) {
                $id = DB::table('mxp_menu')->insertGetId(
                    array(
                        'name' => 'Opening Stock View',
                        'description' => 'Opening Stock View',
                        'route_name' => 'opening_stock_view',
                        'parent_id' => $menu_id,
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
                        'parent_id' => 0,
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

            /* End Opening Stock*/

            /* Start Location Menu*/

            $exits_2 = DB::table('mxp_menu')->where('route_name', 'location_list_view')->exists();

            if($exits_2 != 1) {
                $id_2 = DB::table('mxp_menu')->insertGetId(
                    array(
                        'name' => 'Location List View',
                        'description' => 'Location List View',
                        'route_name' => 'location_list_view',
                        'parent_id' => $menu_id,
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

            /* End Location Menu*/

            /* Start Warehouse in Type Menu*/
            $exits_3 = DB::table('mxp_menu')->where('route_name', 'warehouseintypelist')->exists();

            if($exits_3 != 1) {
                $id_3 = DB::table('mxp_menu')->insertGetId(
                    array(
                        'name' => 'Warehouse In Type List',
                        'description' => 'Warehouse In Type List',
                        'route_name' => 'warehouseintypelist',
                        'parent_id' => $menu_id,
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
            /* End Warehouse in Type Menu*/

            /* Start Warehouse out Type Menu*/
            $exits_4 = DB::table('mxp_menu')->where('route_name', 'warehouseouttypelist')->exists();

            if($exits_4 != 1) {
                $id_4 = DB::table('mxp_menu')->insertGetId(
                    array(
                        'name' => 'Warehouse Out Type List',
                        'description' => 'Warehouse Out Type List',
                        'route_name' => 'warehouseouttypelist',
                        'parent_id' => $menu_id,
                        'is_active' => 1,
                        'order_id' => 0
                    )
                );

                DB::table('mxp_user_role_menu')->insert(
                    array(
                        'menu_id' => $id_4,
                        'role_id' => 1,
                        'is_active' => 1
                    )
                );
            }
            /* End Warehouse out Type Menu*/
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
