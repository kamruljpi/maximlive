<?php

namespace App\Http\Controllers\taskController\Flugs;

class ChallanFlugs
{
	// when challan generate
	const CHALLAN_REQUEST_SENT = 'pending_to_approve';
	// when approved wharehouse
	const CHALLAN_REQUEST_ACCEPT = 'goods_out_to_delivery';
}