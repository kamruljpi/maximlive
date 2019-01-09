<?php

namespace App\Http\Controllers\taskController\Ipo;

use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MxpIpo;
use App\MxpStore;
use Validator;
use Auth;
use DB;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Source\source;

class IpoListController extends Controller
{
	/**
	 *	@return array
	 */	
	public function getIpoValue(){

		$ipoDetails = MxpIpo::select('mxp_ipo.*',DB::Raw('sum(mxp_ipo.ipo_quantity) as ipo'))	
            	->orderBy('mxp_ipo.ipo_id','DESC')
            	->groupBy('mxp_ipo.ipo_id')
            	->where('mxp_ipo.is_deleted',BookingFulgs::IS_NOT_DELETED)
				->paginate(20);

		/** calculate left quantity **/
		if(!empty($ipoDetails)) {
			foreach ($ipoDetails as &$details) {
				$ipo_quantitys = (DB::table('mxp_store')->select(DB::Raw('sum(item_quantity) as ipo_quantitys'))->where('product_id',$details->ipo_id)->first())->ipo_quantitys;
				$details->left_quantity = ($details->ipo) - $ipo_quantitys;
			}
		}
		/**End**/

		return view('maxim.ipo.list.ipo_list',compact('ipoDetails','ipo_store'));
	}

	/**
	 *	@return array
	 */
	public function getIpoReport(Request $request){
		$footerData   = [];
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
			
		$buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->bid)->first();

		$ipoDetails = MxpIpo::join('mxp_booking as mp','mp.id','job_id')
	                ->select('mxp_ipo.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku','mp.item_size_width_height')
	                ->where([['mxp_ipo.ipo_id',$request->ipoid],['mxp_ipo.is_deleted',BookingFulgs::IS_NOT_DELETED]])
	                ->get();
	    $object = new source();
	    $prepared_by = $object->getUserDetails($buyerDetails->booking_order_id);
		$ipoIncrease = $ipoDetails[0]->initial_increase;

		return view('maxim.ipo.ipoBillPage', [
		    'companyInfo'  => $companyInfo,
		    'initIncrease' => $ipoIncrease,
		    'buyerDetails' => $buyerDetails,
		    'ipoDetails'   => $ipoDetails,
		    'footerData'   => $footerData,
		    'prepared_by'   => $prepared_by
		  ]
		);
	}
}