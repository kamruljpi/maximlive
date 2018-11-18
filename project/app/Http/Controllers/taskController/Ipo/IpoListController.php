<?php

namespace App\Http\Controllers\taskController\Ipo;

use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MxpIpo;
use Validator;
use Auth;
use DB;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;

class IpoListController extends Controller
{

	public function getIpoValue(){
		$ipoDetails = MxpIpo::select('*',DB::Raw('sum(ipo_quantity) as ipo_quantity'))
            	->orderBy('ipo_id','DESC')
            	->groupBy('ipo_id')
            	->where('is_deleted',BookingFulgs::IS_NOT_DELETED)
				->paginate(20);

		return view('maxim.ipo.list.ipo_list',compact('ipoDetails'));
	}

	public function getIpoReport(Request $request){
		$footerData   = [];
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
			
		$buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->bid)->first();
		$ipoDetails = MxpIpo::join('mxp_booking as mp','mp.id','job_id')
	                ->select('mxp_ipo.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku')
	                ->where('ipo_id',$request->ipoid)
	                ->get();
		$ipoIncrease = $ipoDetails[0]->initial_increase;

		return view('maxim.ipo.ipoBillPage', [
		    'companyInfo'  => $companyInfo,
		    'initIncrease' => $ipoIncrease,
		    'buyerDetails' => $buyerDetails,
		    'ipoDetails'   => $ipoDetails,
		    'footerData'   => $footerData
		  ]
		);
	}
}