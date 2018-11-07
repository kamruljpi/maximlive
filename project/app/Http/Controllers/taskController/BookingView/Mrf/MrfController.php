<?php

namespace App\Http\Controllers\taskController\BookingView\Mrf;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Model\MxpMrf;
use App\Model\MxpBookingChallan;
use Illuminate\Support\Facades\Auth;
use Carbon;
use Session;

class MrfController
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
	public function cancelMrf( $id ){

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
