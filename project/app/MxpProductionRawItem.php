<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpProductionRawItem extends Model
{
    protected $table = 'mxp_production_raw_item';
    protected $primaryKey = 'id_production_raw_item';

    protected $fillable = ['production_id','item_code', 'quantity', 'status', 'is_deleted','deleted_user_id','deleted_date_at','last_action_at'];
}
