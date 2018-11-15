<?php

namespace App\Http\Controllers\taskController\History\Restore\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\History\Restore\Source\Resource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use Session;
use Carbon;
use Auth;
use DB;

Class Restore extends Controller
{
	public function undo($request){
		$type = $request->type;
		if(!empty($type) && !empty($type)){
			switch ($type) {
				case HeaderType::BOOKING : 
					return  Resource::restorebooking($request->id);
				case HeaderType::PI :
					return  Resource::restorePi($request->id);
				case HeaderType::IPO :
					return  Resource::restoreIpo($request->id);
				case HeaderType::MRF :
					return Resource::restoreMrf($request->id);
				case HeaderType::CHALLAN :
					return  Resource::restoreChallan($request->id);
				default:
				return "<span> You are enter invalid case value.</span><br>
				input name must be `filter_type` and valid values (booking,pi,challan,ipo,mrf,challan)";
			}
		}
		Session::flash('message','Your request type empty.');
		return redirect()->back();
	}
}