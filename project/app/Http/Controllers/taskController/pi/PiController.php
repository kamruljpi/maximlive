<?php

namespace App\Http\Controllers\taskController\pi;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Message\StatusMessage;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use Validator;
use App\User;
use Session;
use Carbon;
use Auth;
use DB;


class PiController extends Controller
{
	public function piGenerate(Request $request){
		$data = $request->all();

		$job_ids = isset($data['job_id']) ? $data['job_id'] : '';

		$job_ids = array_unique($job_ids);

		$validMassage = [
		    'payment_days.required' => 'Payment days required.'
		];

		$validator = Validator::make($request->all(), [
		    'payment_days' => 'required'
		],$validMassage);

		if ($validator->fails()) {
		    return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		// This section set pi type 

		if($request->is_type === BookingFulgs::IS_PI_NON_FSC_TYPE){
			$is_type = BookingFulgs::IS_PI_NON_FSC_TYPE;
		}else if($request->is_type === BookingFulgs::IS_PI_FSC_TYPE){
			$is_type = BookingFulgs::IS_PI_FSC_TYPE;
		}

		// this section check empty job id
		if (empty($job_ids)) {
			StatusMessage::create('empty_booking_data', 'Have not checked any Item !');
			return \Redirect()->Route('task_dashboard_view');
		}

		// this section get avaiable job id value in booking table
		$pi_details = [];
		if(!empty($job_ids)) {
			foreach ($job_ids as $key => $job_id) {
				$pi_details[$job_id] = DB::table('mxp_booking')->where([['id',$job_id],['is_pi_type',BookingFulgs::IS_PI_UNSTAGE_TYPE]])->first();
			}
		}

		// this section check empty PI details
		if (empty($pi_details)) {
			StatusMessage::create('empty_booking_data', 'Are you sure, Someone not created This Order PI!');
			return \Redirect()->Route('task_dashboard_view');
		}

		// This section update booking table type
		if(!empty($job_ids)) {
			foreach ($job_ids as $key => $job_id) {
				$updateBookingtable = MxpBooking::find($job_id);
				$updateBookingtable->is_pi_type = $is_type;
				$updateBookingtable->save();
			}
		}

		// this section get buyer details
		$i = 0;
		$pibooking_order_id = '';
		foreach ($pi_details as $pi_detailskey => $pi_detailsvalue) {
			if($i == 0){
				$buyerDetails = DB::table('mxp_bookingbuyer_details')
					->where('booking_order_id',$pi_detailsvalue->booking_order_id)
					->first();
				$pibooking_order_id = $pi_detailsvalue->booking_order_id;
			}
			$i++;
		}
	    

	    $cc = MxpPI::select('p_id')->groupBy('p_id')->get();
      	$cc = count($cc);
		$count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
		$date = date('dmY');

		if($request->is_type === BookingFulgs::IS_PI_FSC_TYPE){
			$customid = "fsc-".$date.$count;
		}else{
			$customid = $date.$count;
		}

		if(isset($pi_details) && !empty($pi_details)) {
			foreach ($pi_details as $piValues) {
				if(!empty($piValues->item_code)){
					$piDetails = new MxpPi();
					$piDetails->p_id = $customid;
					$piDetails->job_no = $piValues->id;
					$piDetails->user_id = Auth::user()->user_id;
					$piDetails->booking_order_id = $piValues->booking_order_id;
					$piDetails->erp_code = $piValues->erp_code;
					$piDetails->item_code = $piValues->item_code;
					$piDetails->item_size = $piValues->item_size;
					$piDetails->item_description = $piValues->item_description;
					$piDetails->item_quantity = $piValues->item_quantity;
					$piDetails->item_price = $piValues->item_price;
					$piDetails->matarial = $piValues->matarial;
					$piDetails->gmts_color = $piValues->gmts_color;
					$piDetails->others_color = $piValues->others_color;
					$piDetails->orderDate = $piValues->orderDate;
					$piDetails->orderNo = $piValues->orderNo;
					$piDetails->shipmentDate = $piValues->shipmentDate;
					$piDetails->poCatNo = $piValues->poCatNo;
					$piDetails->oos_number = $piValues->oos_number;
					$piDetails->sku = $piValues->sku;
					$piDetails->style = $piValues->style;
					$piDetails->payment_days = $request->payment_days;
					$piDetails->is_type = $is_type;
					$piDetails->last_action_at = LastActionFlugs::CREATE_ACTION;
					$piDetails->save();
				}
			}
		}
		return \Redirect::route('refresh_pi_view',['is_type' => $is_type,'p_id' => $customid,'bid' => $pibooking_order_id]);
	}

	public function redirectPiReport(Request $request){
		$companyInfo = DB::table('mxp_header')->where('header_type',HeaderType::PI)->get();
		$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$request->bid)
	    	->first();

		if($buyerDetails->buyer_name == 'Gymboree') {

			$bookingDetails = MxpPi::where([
						['p_id',$request->p_id],
						['is_type',$request->is_type],
						['is_deleted',BookingFulgs::IS_NOT_DELETED]
					])
					->select('*',DB::Raw('sum(item_quantity) as item_quantity'),
						DB::Raw('GROUP_CONCAT(DISTINCT style SEPARATOR ", ") as style'),
						DB::Raw('GROUP_CONCAT(DISTINCT item_description SEPARATOR ", ") as item_description'),
						DB::Raw('GROUP_CONCAT(DISTINCT oos_number SEPARATOR ", ") as oos_number'))
					->groupBy('item_code')
					->groupBy('poCatNo')
					->orderBy('poCatNo')
					->get();
		}else{
			$bookingDetails = MxpPi::where([
						['p_id',$request->p_id],
						['is_type',$request->is_type],
						['is_deleted',BookingFulgs::IS_NOT_DELETED]
					])
					->select('*',DB::Raw('sum(item_quantity) as item_quantity'),
						DB::Raw('GROUP_CONCAT(DISTINCT style SEPARATOR ", ") as style'),
						DB::Raw('GROUP_CONCAT(DISTINCT item_description SEPARATOR ", ") as item_description'),
						DB::Raw('GROUP_CONCAT(DISTINCT oos_number SEPARATOR ", ") as oos_number'))
					->groupBy('job_no')
					->orderBy('job_no','ASC')
					->get();
		}

        $footerData = DB::table('mxp_reportfooter')->where('status', 1)->get();	
	    $is_type = $request->is_type;
		$getUserDetails = $this->getUserDetails($bookingDetails[0]->user_id);
		
		return view('maxim.pi_format.piReportPage', compact('companyInfo', 'bookingDetails','footerData','buyerDetails','is_type','getUserDetails'));
	}

	public static function getUserDetails($userId){
        $data = DB::table('mxp_users')
            ->where('user_id',$userId)
            ->get();
        return $data;
    }
    public function piEdit($p_id){

        $pi_value = MxpPi::where([
        		['p_id', $p_id],
        		['is_deleted',BookingFulgs::IS_NOT_DELETED]
        	])
        	->get();

        if(isset($pi_value) && !empty($pi_value)){
            foreach ($pi_value as $value) {
                $value->is_deleted = BookingFulgs::IS_DELETED;
                $value->deleted_user_id = Auth::User()->user_id;
                $value->deleted_date_at = Carbon\Carbon::now();
                $value->last_action_at = LastActionFlugs::DELETE_ACTION;
                $value->save();
                
                $booking = MxpBooking::find($value->job_no);
                $booking->is_pi_type = BookingFulgs::IS_PI_UNSTAGE_TYPE;
                $booking->save();
                
                $msg = "Pi ".$p_id." deleted successfully.";
            }

        }else{
            $error = "Something went wrong please try again later";
        }

        Session::flash('message', $msg);
        Session::flash('error-m', $error);

        return Redirect()->back();
    }
}