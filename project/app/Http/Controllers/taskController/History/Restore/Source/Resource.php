<?php
namespace App\Http\Controllers\taskController\History\Restore\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Os\MxpOsPo;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMultipleChallan;
use App\Model\MxpBookingChallan;
use App\Model\MxpBooking;
use App\Model\MxpMrf;
use App\Model\MxpPi;
use App\MxpIpo;
use Session;
use Carbon;
use Auth;
use DB;

Class Resource extends Controller
{
	public static function getIdByDetails($model,$field,$value){
		$data = $model->orderBy($field,'DESC')
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id) as booking_order_id'))
            ->where([[$field,$value],['is_deleted',BookingFulgs::IS_DELETED]])
			->groupBy($field)
			->paginate(20);
		return $data;
	}
	public static function getDeletedPiValue($model,$field){
		$data = $model->orderBy('id','DESC')
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id SEPARATOR ", ") as booking_order_id'))
            ->where('is_deleted',BookingFulgs::IS_DELETED)
			->groupBy($field)
			->paginate(20);
		return $data;
	}

	public function restorebooking($id) {
		try {
			$booking = MxpBooking::where([
						['booking_order_id', $id],
						['is_deleted',BookingFulgs::IS_DELETED]
					])
					->get();

			if(isset($booking) && !empty($booking[0]->booking_order_id)){
	            foreach ($booking as $bookvalue) {
	                $bookvalue->is_deleted = BookingFulgs::IS_NOT_DELETED;
	                $bookvalue->deleted_user_id = Auth::User()->user_id;
	                $bookvalue->deleted_date_at = Carbon\Carbon::now();
	                $bookvalue->last_action_at = LastActionFlugs::RESTORED_ACTION;
	                $bookvalue->save();
	                
	                // self::print_me($value->id);
	                MxpBookingChallan::where('job_id',$value->id)->update([
	                	'is_deleted' => BookingFulgs::IS_NOT_DELETED,
	                	'deleted_user_id' => Auth::User()->user_id,
	                	'deleted_date_at' => Carbon\Carbon::now(),
	                	'last_action_at' => LastActionFlugs::RESTORED_ACTION
	                ]);               
	                
	                $msg = "Booking  ".$id." Restore successfully.";
	            }

	            MxpBookingBuyerDetails::where('booking_order_id',$booking[0]->booking_order_id)->update([
                	'is_deleted' => BookingFulgs::IS_NOT_DELETED,
                	'deleted_user_id' => Auth::User()->user_id,
                	'deleted_date_at' => Carbon\Carbon::now(),
                	'last_action_at' => LastActionFlugs::RESTORED_ACTION
                ]);
	        }else{
	            $error = "Something went wrong please try again later";
	        }
	        Session::flash('message', $msg);
	        Session::flash('error-m', $error);
			return redirect()->back();

		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}

	public function restorePi($id) {
		try {
			$pi_value = MxpPi::where([
						['p_id', $id],
						['is_deleted',BookingFulgs::IS_DELETED]
					])
					->get();

			if(isset($pi_value) && !empty($pi_value[0]->p_id)){
	            foreach ($pi_value as $pivalue) {
	                $pivalue->is_deleted = BookingFulgs::IS_NOT_DELETED;
	                $pivalue->deleted_user_id = Auth::User()->user_id;
	                $pivalue->deleted_date_at = Carbon\Carbon::now();
	                $pivalue->last_action_at = LastActionFlugs::RESTORED_ACTION;
	                $pivalue->save();
	                
	                $bookingss = MxpBooking::find($pivalue->job_no);
	                $bookingss->is_pi_type = $pivalue->is_type;
	                $bookingss->save();
	                
	                $msg = "Pi ".$id." Restore successfully.";
	            }

	        }else{

	            $error = "Something went wrong please try again later";

	        }

	        Session::flash('message', $msg);
	        Session::flash('error-m', $error);

			return redirect()->back();

		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}

	public function restoreIpo($id){
		$ipo = MxpIpo::where([
					['ipo_id', $id],
					['is_deleted',BookingFulgs::IS_DELETED]
				])
				->get();

		if(isset($ipo) && !empty($ipo[0]->ipo_id)){
            foreach ($ipo as $ipovalue) {
                $ipovalue->is_deleted = BookingFulgs::IS_NOT_DELETED;
                $ipovalue->deleted_user_id = Auth::User()->user_id;
                $ipovalue->deleted_date_at = Carbon\Carbon::now();
                $ipovalue->last_action_at = LastActionFlugs::RESTORED_ACTION;
                $ipovalue->save();                
                $msg = "IPO ".$id." Restore successfully.";
            }
        }else{
            $error = "Something went wrong please try again later";
        }
        Session::flash('message', $msg);
        Session::flash('error-m', $error);
		return redirect()->back();
	}
	public function restoreMrf($id){
		$mrf = MxpMrf::where([
					['mrf_id', $id],
					['is_deleted',BookingFulgs::IS_DELETED]
				])
				->get();

		if(isset($mrf) && !empty($mrf[0]->mrf_id)){
            foreach ($mrf as $mrfvalue) {
                $mrfvalue->is_deleted = BookingFulgs::IS_NOT_DELETED;
                $mrfvalue->deleted_user_id = Auth::User()->user_id;
                $mrfvalue->deleted_date_at = Carbon\Carbon::now();
                $mrfvalue->last_action_at = LastActionFlugs::RESTORED_ACTION;
                $mrfvalue->save();                
                $msg = "MRF ".$id." Restore successfully.";
            }
        }else{
            $error = "Something went wrong please try again later";
        }
        Session::flash('message', $msg);
        Session::flash('error-m', $error);
		return redirect()->back();
	}
	public function restoreChallan($id){
		$challan = MxpMultipleChallan::where([
					['challan_id', $id],
					['is_deleted',BookingFulgs::IS_DELETED]
				])
				->get();

		if(isset($challan) && !empty($challan[0]->mrf_id)){
            foreach ($challan as $challanvalue) {
                $challanvalue->is_deleted = BookingFulgs::IS_NOT_DELETED;
                $challanvalue->deleted_user_id = Auth::User()->user_id;
                $challanvalue->deleted_date_at = Carbon\Carbon::now();
                $challanvalue->last_action_at = LastActionFlugs::RESTORED_ACTION;
                $challanvalue->save();                
                $msg = "Challan ".$id." Restore successfully.";
            }
        }else{
            $error = "Something went wrong please try again later";
        }
        Session::flash('message', $msg);
        Session::flash('error-m', $error);
		return redirect()->back();
	}
}