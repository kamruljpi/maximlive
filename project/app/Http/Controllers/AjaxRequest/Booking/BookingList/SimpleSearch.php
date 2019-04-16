<?php

namespace App\Http\Controllers\AjaxRequest\Booking\BookingList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\MxpProduct;
use Auth;

class SimpleSearch extends Controller
{
	public function __invoke(Request $request){
		if($request->ajax()){
			$booking = MxpBooking::where('booking_order_id',$request->booking_id)->get();
		}
	}
}