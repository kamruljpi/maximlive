<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpWarehouseType extends Model
{
    protected $table = 'mxp_warehouse_type';

    protected $primaryKey = 'id_warehouse_type';

    public $fillable = [
    	'warehouse_in_out_type', 'status', 'last_action_at', 'is_deleted', 'deleted_user_id', 'user_id', 'warehouse_type'
    ];

}
