<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MxpZone extends Model
{
    protected $table = 'mxp_zone';

    protected $primaryKey = 'zone_id';

    public $fillable = [
        'zone_name', 'status', 'last_action_at', 'is_deleted', 'deleted_user_id', 'user_id'
    ];
}
