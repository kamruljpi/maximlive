<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpProductionFinishedItem extends Model
{
    protected $table = 'mxp_production_finished_item';
    protected $primaryKey = 'id_production_finished_item';

    protected $fillable = ['production_id','item_code', 'item_size','item_color','quantity', 'status', 'is_deleted','deleted_user_id','deleted_date_at','last_action_at'];
}
