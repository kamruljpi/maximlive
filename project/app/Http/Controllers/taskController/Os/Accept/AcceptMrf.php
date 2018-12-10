<?php

namespace App\Http\Controllers\taskController\Os\Accept;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\Controller;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Auth;
use DB;
use Session;

class AcceptMrf extends Controller
{
	public function __invoke(Request $request){

		$mrf_ids = isset($request->mrf_ids) ? $request->mrf_ids : '';
		$mrf_idsss = implode(' , ', $mrf_ids);

		if(is_array($mrf_ids) && !empty($mrf_ids)) {
			foreach ($mrf_ids as $mrf_id) {
				MxpMrf::where('mrf_id',$mrf_id)->update([
					'mrf_status' => MrfFlugs::ACCEPT_MRF,
					'accepted_user_id' => Auth::user()->user_id,
					'accepted_date_at' => Carbon::today()
				]);
			}
		}	

		return \Redirect()->Route('os_mrf_details_view',['mrfIdList' => $mrf_idsss])->with('mrfIdList', $mrf_idsss)->with('data', MrfFlugs::ACCEPTED_MAESSAGE);
	}
}