<?php
namespace App\Http\Controllers\taskController\History\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use Session;
use Carbon;
use Auth;
use DB;

Class RestorePi extends Controller
{
	public static function getPidByDetails($p_id){
		$data = MxpPi::orderBy('id','DESC')
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id) as booking_order_id'))
            ->where([['p_id',$p_id],['is_deleted',BookingFulgs::IS_DELETED]])
			->groupBy('p_id')
			->paginate(20);
		return $data;
	}
	public static function getDeletedPiValue(){
		$data = MxpPi::orderBy('id','DESC')
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id SEPARATOR ", ") as booking_order_id'))
            ->where('is_deleted',BookingFulgs::IS_DELETED)
			->groupBy('p_id')
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