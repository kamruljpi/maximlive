<?php

namespace App\Http\Controllers\taskController\Os\Accept;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\Controller;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Auth;
use DB;

class AcceptMrf extends Controller
{
	public function __invoke($request){
		try {
			MxpMrf::where('mrf_id',$request)->update([
				'mrf_status' => MrfFlugs::ACCEPT_MRF,
				'accepted_user_id' => Auth::user()->user_id,
				'accepted_date_at' => Carbon::today()
			]);
			return redirect()->back()->with('data', MrfFlugs::ACCEPTED_MAESSAGE);
		} catch (Exception $e) {
			report($e);
        	return false;
		}
	}
}