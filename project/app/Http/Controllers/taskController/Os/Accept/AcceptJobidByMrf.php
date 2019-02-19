<?php

namespace App\Http\Controllers\taskController\Os\Accept;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\Controller;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Session;
use Auth;
use DB;

class AcceptJobidByMrf extends Controller
{
	public function __invoke(Request $request,$job_id){

		$mrf_ids = isset($request->mrf_ids) ? $request->mrf_ids : Session::get('mrf_ids');

		$mrf_idsss = implode(' , ', $mrf_ids);

		MxpMrf::where([
				['job_id',$job_id],
				['job_id_current_status','!=',MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT]
			])
			->update([
				'job_id_current_status' => MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT,
				'current_status_accepted_user_id' => Auth::user()->user_id,
				'current_status_accepted_date_at' => Carbon::today()
			]);
		
		return \Redirect()->Route('os_mrf_details_view',['mrfIdList' => $mrf_idsss])->with('mrfIdList', $mrf_idsss)->with('data', MrfFlugs::ACCEPTED_MAESSAGE);
	}
}