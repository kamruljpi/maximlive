<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DB;

class InsertRolePermissionValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('mxp_menu')->insert([
            [
                'name' => 'Purchase Order Store Action',
                'route_name' => 'purchase_order_store_action',
                'description' => 'Purchase Order Store Action',
            ],
            [
                'name' => 'Purchase Order Report View',
                'route_name' => 'purchase_order_report_view',
                'description' => 'Purchase Order Report View',
            ],
            [
                'name' => 'Purchase Order Edit View',
                'route_name' => 'purchase_order_edit_view',
                'description' => 'Purchase Order Edit View',
            ],
            [
                'name' => 'Purchase Order Delete Action',
                'route_name' => 'purchase_order_delete_action',
                'description' => 'Purchase Order Delete Action',
            ],
            [
                'name' => 'Purchase Order Reject Action',
                'route_name' => 'purchase_order_reject_action',
                'description' => 'Purchase Order Reject Action',
            ],
            [
                'name' => 'Purchase Create View',
                'route_name' => 'purchase_create_view',
                'description' => 'Purchase Create View',
            ],
            [
                'name' => 'Purchase Store Action',
                'route_name' => 'purchase_store_action',
                'description' => 'Purchase Store Action',
            ],
            [
                'name' => 'Purchase Show View',
                'route_name' => 'purchase_show_view',
                'description' => 'Purchase Show View',
            ],
            [
                'name' => 'Purchase From Purchase Order Action',
                'route_name' => 'purchase_from_purchase_order_action',
                'description' => 'Purchase From Purchase Order Action',
            ],
        ]);
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
