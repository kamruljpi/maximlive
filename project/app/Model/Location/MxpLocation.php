<?php

namespace App\Model\Location;

use Illuminate\Database\Eloquent\Model;

class MxpLocation extends Model
{

    protected $table = "mxp_location";

    protected $primaryKey = 'id_location';

	/**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['user_id','location','status','is_deleted',
                           'deleted_user_id','last_action'];
}