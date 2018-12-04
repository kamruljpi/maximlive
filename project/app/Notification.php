<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	CONST CREATE_BOOKING = 'create_booking';
	CONST CREATE_MRF = 'create_mrf';
	CONST CREATE_SPO = 'create_spo';

    protected $table = "mxp_notifications";

	protected $fillable = [
		'type', 'type_id', 'seen','seen_user_id'
    ];
}
