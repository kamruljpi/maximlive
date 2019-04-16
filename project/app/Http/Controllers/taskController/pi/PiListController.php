<?php
namespace App\Http\Controllers\taskController\pi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpPi;
use DB;
use App\Http\Controllers\taskController\pi\PiController;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use Auth;

class PiListController extends Controller
{
	use UserAccessBuyerList;

	public function getPiList(){

		/** buyer wiase booking value filter **/

        $buyerList = $this->getUserByerList(); // use trait class

        if(isset($buyerList) && !empty($buyerList)){

        	$piDetails = MxpPi::orderBy('id','DESC')
				->select('mxp_pi.*',DB::Raw('GROUP_CONCAT(DISTINCT mxp_pi.booking_order_id) as booking_order_id'),'mbd.buyer_name')
				->join('mxp_bookingbuyer_details as mbd', 'mbd.booking_order_id', 'mxp_pi.booking_order_id')
	            ->where('mxp_pi.is_deleted',BookingFulgs::IS_NOT_DELETED)
	            // use trait class
	            ->whereIn('mbd.buyer_name',$this->getUserByerNameList()) 
				->groupBy('p_id')
				->paginate(20);

        }else if(Auth::user()->type == 'super_admin'){

        	$piDetails = MxpPi::orderBy('id','DESC')
				->select('mxp_pi.*',DB::Raw('GROUP_CONCAT(DISTINCT mxp_pi.booking_order_id) as booking_order_id'),'mbd.buyer_name')
				->join('mxp_bookingbuyer_details as mbd', 'mbd.booking_order_id', 'mxp_pi.booking_order_id')
	            ->where('mxp_pi.is_deleted',BookingFulgs::IS_NOT_DELETED)
				->groupBy('p_id')
				->paginate(20);
				
        }else{
        	// when condition false
        	// return empty paginate object
        	$piDetails = MxpPi::orderBy('id','DESC')
	            ->where('mxp_pi.is_deleted',BookingFulgs::IS_NOT_DELETED)
	            ->where('mxp_pi.booking_order_id','')
				->groupBy('p_id')
				->paginate(20);
        }

        /** End**/ 

        // use trait class
        $this->addBuyerDetails($piDetails);

		return view('maxim.pi_format.list.pi_list',compact('piDetails'));
	}
	
	public function getPiReport(Request $request){

		$is_type = $request->is_type;

		$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where([
	    		['is_deleted',BookingFulgs::IS_NOT_DELETED],
	    		['booking_order_id',$request->bid]
	    	])
	    	->first();

	    if($buyerDetails->buyer_name == 'Gymboree') {
	    	$bookingDetails = MxpPi::where([
					['is_deleted',BookingFulgs::IS_NOT_DELETED],
					['p_id',$request->pid],
					['is_type',$is_type],
				])
				->select('*',DB::Raw('sum(item_quantity) as item_quantity'),
					DB::Raw('GROUP_CONCAT(DISTINCT style SEPARATOR ", ") as style'),
					DB::Raw('GROUP_CONCAT(DISTINCT item_description SEPARATOR ", ") as item_description'),
					DB::Raw('GROUP_CONCAT(DISTINCT oos_number SEPARATOR ", ") as oos_number'))
				->groupBy('item_code')
				->groupBy('poCatNo')
				->orderBy('poCatNo','ASC')
				->get();
		}else {
			$bookingDetails = MxpPi::where([
						['is_deleted',BookingFulgs::IS_NOT_DELETED],
						['p_id',$request->pid],
						['is_type',$is_type],
					])
					->select('*',DB::Raw('sum(item_quantity) as item_quantity'),
						DB::Raw('GROUP_CONCAT(DISTINCT style SEPARATOR ", ") as style'),
						DB::Raw('GROUP_CONCAT(DISTINCT item_description SEPARATOR ", ") as item_description'),
						DB::Raw('GROUP_CONCAT(DISTINCT oos_number SEPARATOR ", ") as oos_number'))
					->groupBy('job_no')
					->orderBy('job_no','ASC')
					->get();
		}
		
				
		$companyInfo = DB::table('mxp_header')->where('header_type', HeaderType::PI)->get();
		$footerData = DB::select("select * from mxp_reportfooter");
		$getUserDetails = PiController::getUserDetails($bookingDetails[0]->user_id);

		return view('maxim.pi_format.piReportPage', compact('companyInfo', 'bookingDetails', 'footerData','buyerDetails','is_type','getUserDetails'));
	}

	/**
	 *
	 *	@return PI list view page
	 */
	public function piSearch(Request $request) {

		$p_id = isset($request->p_id) ? $request->p_id : '' ;
		$piDetails = $this->piSearchById($p_id);

		// use trait class
		$this->addBuyerDetails($piDetails);

		return view('maxim.pi_format.list.pi_list',compact('piDetails'));
	}

	/**
	 * @param p_id get a id
	 *
	 * @return array()
	 */
	public function piSearchById($p_id) {

		$pi_value = [] ;

		if(!empty($p_id)) {
			$pi_value = MxpPi::orderBy('id','DESC')
				->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id) as booking_order_id'))
	            ->where([
	            	['is_deleted',BookingFulgs::IS_NOT_DELETED],
	            	['p_id','like','%'.$p_id.'%'],
	            ])
				->groupBy('p_id')
				->paginate(20);
		}

		return $pi_value;
	}

	/**
	 * PI reverse view 
	 * @return  array() mixed
	 */
	public function piReverseView($p_id) {

		$buyerDetails = [];
		$piDetails = MxpPi::orderBy('id','DESC')			
            ->where([
            	['mxp_pi.is_deleted',BookingFulgs::IS_NOT_DELETED],
            	['mxp_pi.p_id',$p_id]
            ])
            ->join('mxp_booking as mb','mb.id','mxp_pi.job_no')
            ->select('mxp_pi.*','mb.season_code')
			->get();

		if(!empty($piDetails[0]->booking_order_id)) {
			$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$piDetails[0]->booking_order_id)
	    	->first();
	    	$buyerDetails->prepared_by = DB::Table('mxp_users')->where('user_id',$piDetails[0]->user_id)->select('first_name','last_name')->first();
		}

		return view('maxim.pi_format.pi_reverse_page',compact('piDetails','buyerDetails'));
	}
}
