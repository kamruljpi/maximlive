<?php

namespace App\Http\Controllers\taskController\Os\Cancel;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\Controller;
use App\Model\Os\MxpOsPo;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Auth;
use DB;

class CancelMrf extends Controller 
{
	public function __invoke($mrf_id){
		try {
			MxpMrf::where('mrf_id',$mrf_id)->update([
				'mrf_status' => MrfFlugs::OPEN_MRF,
				'user_id' => Auth::user()->user_id,
				'accepted_user_id' => null,
				'accepted_date_at' => Carbon::today(),
				'job_id_current_status' => MrfFlugs::JOBID_CURRENT_STATUS_OPEN,
				'current_status_accepted_user_id' => Auth::user()->user_id,
				'current_status_accepted_date_at' => Carbon::today()
			]);

			MxpOsPo::where('mrf_id',$mrf_id)->update([
				'is_deleted' => BookingFulgs::IS_DELETED,
				'deleted_user_id' => Auth::user()->user_id
			]);
			return redirect()->back()->with('data', MrfFlugs::CANCEL_MAESSAGE);
		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}
}