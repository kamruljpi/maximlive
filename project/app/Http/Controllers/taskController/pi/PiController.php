<?php

namespace App\Http\Controllers\taskController\pi;

use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use App\User;
use Carbon;
use Session;
use App\Http\Controllers\taskController\Flugs\HeaderType;

class PiController extends Controller
{
	public function piGenerate(Request $request){
		$data = $request->all();

		if($request->is_type === 'non_fsc'){
			$is_type = 'non_fsc';
		}else if($request->is_type === 'fsc'){
			$is_type = 'fsc';
		}

		if (empty($data)) {
			StatusMessage::create('empty_booking_data', 'Have not checked any Item !');
			return \Redirect()->Route('dashboard_view');
		}

		$getDbValue = [];
		foreach ($data as $key => $dataValue) {
			foreach ($dataValue as $values) {
				$getDbValue[] = DB::table('mxp_booking')->where('id',$values)->get();
			}
		}
		$pi_details = [];
		foreach ($getDbValue as $key => $aaavalue) {
			foreach ($aaavalue as $key => $bbbvalue) {
				$pi_details[] =  $bbbvalue;
			}
		}

		foreach ($data as $key => $dataValue) {
			foreach ($dataValue as $values) {
				$updateBookingtable = MxpBooking::find($values);
				$updateBookingtable->is_pi_type = $is_type;
				$updateBookingtable->save();
			}
		}

	    $buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$pi_details[0]->booking_order_id)
	    	->first();

	    $cc = MxpPI::select('p_id')->groupBy('p_id')->get();
      	$cc = count($cc);
		$count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
		$date = date('dmY') ;

		if($request->is_type === 'fsc'){
			$customid = "fsc-".$date.$count;
		}else{
			$customid = $date.$count;
		}

		if(isset($pi_details) && !empty($pi_details)) {
			foreach ($pi_details as $piValues) {

				$piDetails = new MxpPi();
				$piDetails->p_id = $customid;
				$piDetails->job_no = $piValues->id;
				$piDetails->user_id = Auth::user()->user_id;
				$piDetails->booking_order_id = $piValues->booking_order_id;
				$piDetails->erp_code = $piValues->erp_code;
				$piDetails->item_code = $piValues->item_code;
				$piDetails->item_size = $piValues->item_size;
				$piDetails->item_description = $piValues->item_description;
				$piDetails->item_quantity = $piValues->item_quantity;
				$piDetails->item_price = $piValues->item_price;
				$piDetails->matarial = $piValues->matarial;
				$piDetails->gmts_color = $piValues->gmts_color;
				$piDetails->others_color = $piValues->others_color;
				$piDetails->orderDate = $piValues->orderDate;
				$piDetails->orderNo = $piValues->orderNo;
				$piDetails->shipmentDate = $piValues->shipmentDate;
				$piDetails->poCatNo = $piValues->poCatNo;
				$piDetails->oos_number = $piValues->oos_number;
				$piDetails->sku = $piValues->sku;
				$piDetails->style = $piValues->style;
				$piDetails->is_type = $is_type;
				$piDetails->save();
			}
		}

		return \Redirect::route('refresh_pi_view',['is_type' => $is_type,'p_id' => $customid]);
	}

	public function redirectPiReport(Request $request){
		$companyInfo = DB::table('mxp_header')->where('header_type',HeaderType::PI)->get();
		$bookingDetails = MxpPi::where([
					['p_id',$request->p_id],
					['is_type',$request->is_type],
				])
				->select('*',DB::Raw('sum(item_quantity) as item_quantity'),
					DB::Raw('GROUP_CONCAT(DISTINCT style SEPARATOR ", ") as style'),
					DB::Raw('GROUP_CONCAT(DISTINCT item_description SEPARATOR ", ") as item_description'),
					DB::Raw('GROUP_CONCAT(DISTINCT oos_number SEPARATOR ", ") as oos_number'))
				->groupBy('item_code')
				->groupBy('poCatNo')
				->orderBy('poCatNo')
				->get();
			
        $footerData = DB::table('mxp_reportfooter')->where('status', 1)->get();
		$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$bookingDetails[0]->booking_order_id)
	    	->first();
	    $is_type = $request->is_type;
		$getUserDetails = $this->getUserDetails($bookingDetails[0]->user_id);
		return view('maxim.pi_format.piReportPage', compact('companyInfo', 'bookingDetails','footerData','buyerDetails','is_type','getUserDetails'));
	}

	public static function getUserDetails($userId){
        $data = DB::table('mxp_users')
            ->where('user_id',$userId)
            ->get();
        return $data;
    }
    public function piEdit($p_id){

        $pi_value = MxpPi::where('p_id', $p_id)->get();

        if(isset($pi_value) && !empty($pi_value)){
            foreach ($pi_value as $value) {
                $value->is_deleted = 1;
                $value->deleted_user_id = Auth::User()->user_id;
                $value->deleted_date_at = Carbon\Carbon::now();
                $value->save();
                $msg = "Pi ".$p_id." deleted successfully.";
            }

        }else{
            $error = "Something went wrong please try again later";
        }

        Session::flash('message', $msg);
        Session::flash('error-m', $error);

        return Redirect()->back();
    }
}