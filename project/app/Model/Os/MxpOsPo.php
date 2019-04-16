<?php

namespace App\Model\Os;

use Illuminate\Database\Eloquent\Model;

class MxpOsPo extends Model
{
    protected $table = "mxp_os_po";

    protected $fillable = ['po_id',
    					'user_id',
						'mrf_id',
						'job_id',
						'supplier_id',
						'supplier_price',
						'material',
						'is_deleted',
						'deleted_user_id',
						'order_date',
						'shipment_date',
						'last_action_at'
						];
	protected $hidden = ['id'];
}
