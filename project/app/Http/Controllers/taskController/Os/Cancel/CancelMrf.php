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
	public function __invoke(Request $request){

		$mrf_ids = isset($request->mrf_ids) ? $request->mrf_ids : '';
		$mrf_idsss = implode(' , ', $mrf_ids);

		if(is_array($mrf_ids) && !empty($mrf_ids)) {
			foreach ($mrf_ids as $mrf_id) {
				MxpMrf::where('mrf_id',$mrf_id)->update([
					'mrf_status' => MrfFlugs::OPEN_MRF,
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
			}
		}

		return \Redirect()->Route('os_mrf_details_view',['mrfIdList' => $mrf_idsss])->with('mrfIdList', $mrf_idsss)->with('data', MrfFlugs::CANCEL_MAESSAGE);
	}
}