<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;

class MxpPurchaseOrderItemWh extends Model
{
	protected $table = "mxp_purchase_order_item_wh";
	protected $primaryKey = "id_purchase_order_item_wh";

	protected $fillable = [
				'user_id',
				'purchase_order_wh_id',
				'raw_item_id',
				'item_qty',
				'status',
				'is_deleted',
				'last_action_at'
			];
}