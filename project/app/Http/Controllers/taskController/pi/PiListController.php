<?php
namespace App\Http\Controllers\taskController\pi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpPi;
use DB;
use App\Http\Controllers\taskController\pi\PiController;

class PiListController extends Controller
{
	public function getPiList(){
		$piDetails = MxpPi::orderBy('id','DESC')
			->groupBy('p_id')
			->paginate(20);
		return view('maxim.pi_format.list.pi_list',compact('piDetails'));
	}
	public function getPiReport(Request $request){

		$is_type = $request->is_type;

		$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$request->bid)
	    	->first();

		$bookingDetails = MxpPi::where([
				['p_id',$request->pid],
				['is_type',$is_type],
			])
			->select('*',DB::Raw('sum(item_quantity) as item_quantity'))
			->groupBy('item_code')
			->get();
			
		$companyInfo = DB::table('mxp_header')->where('header_type', 11)->get();
		$footerData = DB::select("select * from mxp_reportfooter");
		$getUserDetails = PiController::getUserDetails($bookingDetails[0]->user_id);

		return view('maxim.pi_format.piReportPage', compact('companyInfo', 'bookingDetails', 'footerData','buyerDetails','is_type','getUserDetails'));
	}
}