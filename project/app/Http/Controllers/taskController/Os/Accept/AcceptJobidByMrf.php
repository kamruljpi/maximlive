<?php

namespace App\Http\Controllers\taskController\Os\Accept;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\Controller;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Auth;
use DB;

class AcceptJobidByMrf extends Controller
{
	public function __invoke($job_id){
		// $this->print_me($job_id);
		try {
			MxpMrf::where('job_id',$job_id)->update([
				'job_id_current_status' => MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT,
				'current_status_accepted_user_id' => Auth::user()->user_id,
				'current_status_accepted_date_at' => Carbon::today()
			]);
			return redirect()->back()->with('data', MrfFlugs::ACCEPTED_MAESSAGE);
		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}
}