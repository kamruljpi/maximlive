<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MxpStage extends Model
{
    protected $table = 'mxp_stage';
    protected $primaryKey = 'id_stage';

    protected $fillable = [
        'name',
        'user_id',
        'is_active',
        'is_deleted',
        'last_action_at'
    ];

    protected $hidden = ['created_at','updated_at'];
}
