<?php

namespace App\Http\Controllers\taskController\Booking;

use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AcceptedBooking extends Controller
{
	const ALERT_MESSAGE = 'Process';
		
	public function __invoke($id){
		try {
			MxpBookingBuyerDetails::where('booking_order_id',$id)->update([
				'booking_status' => self::ALERT_MESSAGE,
				'status_changes_user_id' => Auth::user()->user_id
			]);
			return redirect()->back()->with('data', self::ALERT_MESSAGE);
		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}
}