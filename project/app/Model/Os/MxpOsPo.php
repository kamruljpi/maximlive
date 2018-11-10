<?php

namespace App\Model\Os;

use Illuminate\Database\Eloquent\Model;

class MxpOsPo extends Model
{
	protected $primaryKey = 'po_id';
    protected $table = "mxp_os_po";

    protected $fillable = ['user_id',
						'mrf_id',
						'mrf_job_id',
						'supplier_id',
						'supplier_price',
						'material',
						'is_deleted',
						'deleted_user_id',
						'order_date',
						'shipment_date',
						'last_action_at'
						];
}
