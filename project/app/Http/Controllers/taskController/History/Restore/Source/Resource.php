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

	public function piRestoreRequest($id){
		try {
			$pi_value = MxpPi::where('p_id', $id)->get();

			if(isset($pi_value) && !empty($pi_value)){
	            foreach ($pi_value as $value) {
	                $value->is_deleted = BookingFulgs::IS_NOT_DELETED;
	                $value->deleted_user_id = Auth::User()->user_id;
	                $value->deleted_date_at = Carbon\Carbon::now();
	                $value->last_action_at = LastActionFlugs::RESTORED_ACTION;
	                $value->save();
	                
	                $booking = MxpBooking::find($value->job_no);
	                $booking->is_pi_type = $value->is_type;
	                $booking->save();
	                
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
}