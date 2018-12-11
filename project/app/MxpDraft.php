<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpDraft extends Model
{
    protected $table = 'mxp_draft';
    
    protected $fillable = [
    			'user_id',
    			'booking_order_id',
    			'erp_code',
    			'item_code',
    			'item_size',
    			'item_quantity',
    			'item_price',
    			'matarial',
    			'gmts_color',
    			'orderDate',
    			'orderNo',
                'shipmentDate',
    			'sku',
    			'poCatNo',
                'oos_number',
                'season_code'];
}
