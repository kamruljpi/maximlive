<?php

namespace App\Http\Controllers\taskController\Flugs\Mrf;

class MrfFlugs
{
	const OPEN_MRF = 'open';
	const ACCEPT_MRF = 'proccess';

	const CANCEL_MAESSAGE = 'Cancel';
	const ACCEPTED_MAESSAGE = 'Accepted';

	const JOBID_CURRENT_STATUS_OPEN = 'open';
	const JOBID_CURRENT_STATUS_ACCEPT = 'proccess';
	const JOBID_CURRENT_STATUS_WAITING_FOR_GOODS = 'waiting_for_goods';
	
	const JOBID_CURRENT_STATUS_GOODS_RECEIVE = 'good_receives';
	const JOBID_CURRENT_STATUS_PARTIAL_GOODS_RECEIVE = 'partial_good_receives';
}