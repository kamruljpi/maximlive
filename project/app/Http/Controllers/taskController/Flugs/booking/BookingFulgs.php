<?php

namespace App\Http\Controllers\taskController\Flugs\booking;

class BookingFulgs
{	
	const IS_DELETED = 1;
	const IS_NOT_DELETED = 0;
	
	const IS_COMPLETE = 0;
	// const IS_NOT_COMPLETE = 0;

	const BOOKED_FLUG = 'Booked';
	const ON_HOLD_FLUG = 'Hold';
	const BOOKING_PROCESS_FLUG = 'Process';

	const IS_PI_FSC_TYPE = 'fsc';
	const IS_PI_UNSTAGE_TYPE = 'unstage';
	const IS_PI_NON_FSC_TYPE = 'non_fsc';
	
	const LAST_ACTION_CREATE = 'create';
	const LAST_ACTION_UPDATE = 'update';
	const LAST_ACTION_DELETE = 'delete';

	const ORDER_SAVE = 'save';
	const ORDER_SUBMIT = 'submit';
}