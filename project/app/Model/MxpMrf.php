<?php

namespace App\Model;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use Illuminate\Database\Eloquent\Model;
use App\Model\MxpBooking;

class MxpMrf extends Model
{
    protected $table = "mxp_mrf_table";

    protected $fillable = [
    			'user_id',
    			'mrf_id',
    			'booking_order_id',
    			'erp_code',
    			'supplier_id',
    			'item_code',
    			'item_size',
                'item_quantity',
    			'mrf_quantity',
    			'item_price',
    			'matarial',
    			'gmts_color',
    			'orderDate',
    			'orderNo',
    			'shipmentDate',
    			'poCatNo',
    			'status',
    			'action'
    			];

//    protected function getSupplier($key)
//    {
//        return $this->hasOne('App\Supplier', 'supplier_id', 'supplier_id');
//    }

    public function mrf_ids(){
        return $this->hasMany(MxpBooking::class, 'id','job_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);
    }

    protected $hidden = ['id'];
}
