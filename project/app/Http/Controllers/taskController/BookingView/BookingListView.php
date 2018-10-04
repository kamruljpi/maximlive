<?php

namespace App\Http\Controllers\taskController\BookingView;

use App\Http\Controllers\taskController\BookingView\Mrf\MrfController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class BookingListView extends Controller
{
	CONST JOB_EMPTY_MESSAGE = 'Please select a job id';
	CONST MRF_IPO_EMPTY_MESSAGE = 'Please select Ipo or Mrf';

	public $mrfController ;

	public function __construct(MrfController $MrfController){
		$this->mrfController = $MrfController;
	}

	public function IpoOrMrfDefine(Request $request){
		if(!isset($request->job_id)){
			return redirect()->back()->with('empty_message', self::JOB_EMPTY_MESSAGE);
		}
		if(!isset($request->ipo_or_mrf)){
			return redirect()->back()->with('empty_message', self::MRF_IPO_EMPTY_MESSAGE);
		}

		if($request->ipo_or_mrf === 'mrf'){
			$bookingDetails = $this->mrfController->getBookingValue($request->job_id);

			return view('maxim.mrf.mrf',compact('bookingDetails'));
		}elseif ($request->ipo_or_mrf === 'ipo') {
			
			$bookingDetails = $this->mrfController->getBookingValue($request->job_id);
			return view('maxim.ipo.ipo_price_manage');
		}		
	}
}