<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class ItemCostPrice extends Model
{
	
	protected $primaryKey = "cost_price_id";
	protected $table = "mxp_item_cost_price";

	protected $fillable = ['id_product','user_id','price_1','price_2','last_action'];
}