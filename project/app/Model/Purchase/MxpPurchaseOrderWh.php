<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;

class MxpPurchaseOrderWh extends Model
{
	protected $table = "mxp_purchase_order_wh";
	protected $primaryKey = "id_purchase_order_wh";

	protected $fillable = [
				'user_id',
				'order_date',
				'purchase_order_no',
				'description',
				'status',
				'is_deleted',
				'last_action_at'
			];
}