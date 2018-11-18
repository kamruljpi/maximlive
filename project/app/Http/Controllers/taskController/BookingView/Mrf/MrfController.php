<?php

namespace App\Http\Controllers\taskController\BookingView\Mrf;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Model\MxpBookingBuyerDetails;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use App\Model\MxpMrf;
use App\MxpIpo;
use Session;
use Carbon;

class MrfController extends Controller
{
	public function getBookingValue($id){
		$getDbValue = [];
		if(is_array($id) && !empty($id)){
			foreach ($id as $idValue) {
				$getDbValue[] = MxpBookingChallan::where('job_id',$idValue)->get();
			}
		}

		$pi_details = [];
		if(!empty($getDbValue)){
			foreach ($getDbValue as $key => $aaavalue) {
				foreach ($aaavalue as $key => $bbbvalue) {
					$pi_details[] =  $bbbvalue;
				}
			}
		}

		return (!empty($pi_details)) ? $pi_details : '';
	}

	public function cancelBookingByPlanning($b_id){
		$mrf_details = MxpMrf::where([
				['booking_order_id',$b_id],
				['is_deleted', BookingFulgs::IS_NOT_DELETED]
			])->get();

		$ipo_details = MxpIpo::where([
				['booking_order_id',$b_id],
				['is_deleted', BookingFulgs::IS_NOT_DELETED]
			])->get();

		$check_1 = [];
		if(!empty($mrf_details[0]->job_id)){
			foreach ($mrf_details as $mrf_values) {
				if($mrf_values->mrf_status == MrfFlugs::ACCEPT_MRF || $mrf_values->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT){

					$check_1[]['job_id'] = $mrf_values->job_id;
				}
			}
		}

		$check_2 = [];
		if(!empty($ipo_details[0]->job_id)){
			foreach ($ipo_details as $ipo_values) {
				if($ipo_values->ipo_status == MrfFlugs::ACCEPT_MRF || $ipo_values->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT){

					$check_2[]['job_id'] = $ipo_values->job_id;
				}
			}
		}

		if(is_array($check_1) && empty($check_1) && empty($check_2)){
			MxpBookingBuyerDetails::where([
					['booking_order_id',$b_id],
					['is_deleted', BookingFulgs::IS_NOT_DELETED]
				])
				->update([
					'booking_status' => BookingFulgs::BOOKED_FLUG,
					'accepted_user_id' => Null,//Auth::User()->user_id,
					'accepted_date_at' => Null //Carbon::today()
				]);

			$booking_challan = MxpBookingChallan::where([
					['booking_order_id',$b_id],
					['is_deleted', BookingFulgs::IS_NOT_DELETED]
				])->get();

			if(!empty($booking_challan[0]->job_id)){
				foreach ($booking_challan as $challan_value) {
					$challan_value->left_mrf_ipo_quantity = $challan_value->item_quantity;
					$challan_value->mrf_quantity = NULL;
					$challan_value->ipo_quantity = NULL;
					$challan_value->save();
				}
			}

			if(!empty($mrf_details[0]->job_id)){
				foreach ($mrf_details as $mrf_valuess) {
					$mrf_valuess->is_deleted = BookingFulgs::IS_DELETED;
					$mrf_valuess->deleted_user_id = Auth::User()->user_id;
					$mrf_valuess->deleted_date_at =  Carbon\Carbon::now();
					$mrf_valuess->last_action_at =  BookingFulgs::LAST_ACTION_DELETE;
					$mrf_valuess->save();
				}
			}

			if(!empty($ipo_details[0]->job_id)){
				foreach ($ipo_details as $ipo_valuess) {
					$ipo_valuess->is_deleted = BookingFulgs::IS_DELETED;
					$ipo_valuess->deleted_user_id = Auth::User()->user_id;
					$ipo_valuess->deleted_date_at =  Carbon\Carbon::now();
					$ipo_valuess->last_action_at =  BookingFulgs::LAST_ACTION_DELETE;
					$ipo_valuess->save();
				}
			}

		}else{
			Session::flash('message','You can\'t cancel the booking. Because some job id is running to processing. ');			
		}

		return redirect()->back()->with('message', MrfFlugs::CANCEL_MAESSAGE);
	}

	public function cancelMrfById( $id ){

	    $mrf = MxpMrf::where('job_id', $id)->first();
        $bc= MxpBookingChallan::where('job_id', $id)->first();

        $bc->mrf_quantity = ($bc->mrf_quantity - $mrf->mrf_quantity == 0)? '': ($bc->mrf_quantity - $mrf->mrf_quantity) ;
        $bc->left_mrf_ipo_quantity = $bc->item_quantity;

        $bc->save();

        MxpMrf::where('job_id', $id)->update([
            'is_deleted' => BookingFulgs::IS_DELETED,
            'deleted_user_id' => Auth::User()->user_id,
            'deleted_date_at' =>  Carbon\Carbon::now(),
            'last_action_at' =>  BookingFulgs::LAST_ACTION_DELETE,
        ]);

	    return redirect()->back();
    }
}
