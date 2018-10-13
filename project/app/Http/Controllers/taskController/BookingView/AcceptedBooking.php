<?php

namespace App\Http\Controllers\taskController\BookingView;

use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AcceptedBooking extends Controller
{
	const PROCESS_MESSAGE = 'Process';
		
	public function __invoke($request){
		try {
			MxpBookingBuyerDetails::where('booking_order_id',$request)->update([
				'booking_status' => self::PROCESS_MESSAGE,
				'accepted_user_id' => Auth::user()->user_id
			]);
			return redirect()->back()->with('data', self::PROCESS_MESSAGE);
		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}
}