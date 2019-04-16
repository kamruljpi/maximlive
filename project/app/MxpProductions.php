<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpProductions extends Model
{
    protected $table = 'mxp_productions';
    protected $primaryKey = 'id_mxp_productions';

    protected $fillable = ['id_mxp_productions','production_date', 'description', 'status', 'is_deleted','deleted_user_id','deleted_date_at','last_action_at'];

}
