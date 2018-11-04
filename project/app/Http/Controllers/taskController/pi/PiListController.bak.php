<?php
namespace App\Http\Controllers\taskController\pi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpPi;
use DB;
use App\Http\Controllers\taskController\pi\PiController;
use App\Http\Controllers\taskController\Flugs\HeaderType;

class PiListController extends Controller
{
	public function getPiList(){
		$piDetails = MxpPi::orderBy('id','DESC')
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id) as booking_order_id'))
            ->where('is_deleted','0')
			->groupBy('p_id')
			->paginate(20);
		return view('maxim.pi_format.list.pi_list',compact('piDetails'));
	}
	
	public function getPiReport(Request $request){

		$is_type = $request->is_type;

		$buyerDetails = DB::table('mxp_bookingbuyer_details')
	    	->where('booking_order_id',$request->bid)
	    	->first();

		// $bookingDetails = MxpPi::where([
		// 		['p_id',$request->pid],
		// 		['is_type',$is_type],
		// 	])
		// 	->select('*',DB::Raw('sum(item_quantity) as item_quantity'),DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as poCatNo'))
		// 	->groupBy('item_code')
		// 	->get();

		$tempbookingDetails = MxpPi::where([
				['p_id',$request->pid],
				['is_type',$is_type],
			])
			// ->select('*',DB::Raw('GROUP_CONCAT(DISTINCT item_quantity SEPARATOR ", ") as item_quantitya'),DB::Raw('GROUP_CONCAT(DISTINCT item_code SEPARATOR ", ") as item_codea'))
			// ->selectRaw(function($query){
			// 	$query->select(DB::Raw('sum(item_quantity) as item_quantity'))
			// 		->groupBy('item_code');
			// })
			// ->select('*',DB::Raw('sum(item_quantity) as item_quantity'),DB::Raw('GROUP_CONCAT(DISTINCT item_code SEPARATOR ", ") as item_code'))
			// ->groupBy('poCatNo')
			// ->groupBy('item_code')
			->get();

		$bookingDetails = MxpPi::where([
					['p_id',$request->pid],
					['is_type',$is_type],
				])
					->select('*',DB::Raw('sum(item_quantity) as item_quantity'))
				// ->select('*',DB::Raw('GROUP_CONCAT(DISTINCT item_quantity SEPARATOR ", ") as item_quantitya'),DB::Raw('GROUP_CONCAT(DISTINCT item_code SEPARATOR ", ") as item_codea'))
				// ->selectRaw(function($query){
				// 	$query->select(DB::Raw('sum(item_quantity) as item_quantity'))
				// 		->groupBy('item_code');
				// })
				// ->select('*',DB::Raw('sum(item_quantity) as item_quantity'),DB::Raw('GROUP_CONCAT(DISTINCT item_code SEPARATOR ", ") as item_code'))
				->groupBy('poCatNo')
				->groupBy('item_code')
				->get();
			$poCatNo_details = [];
		// if(isset($tempbookingDetails) && !empty($tempbookingDetails)){
		// 	foreach ($tempbookingDetails as $ngdetails) {
		// 		if(isset($poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_price'])){
		// 			$poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_price'] += $ngdetails->item_price;
		// 		}else{
		// 			$poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_price'] = $ngdetails->item_price;
		// 		}
		// 		if(isset($poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_quantity'])){
		// 			$poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_quantity'] += $ngdetails->item_quantity;
		// 		}else{
		// 			$poCatNo_details[$ngdetails->poCatNo][$ngdetails->item_code]['item_quantity'] = $ngdetails->item_quantity;
		// 		}
		// 	}
		// }

		// if(isset($bookingDetails) && !empty($bookingDetails)){
		// 	foreach ($bookingDetails as &$tngdetails) {
		// 		$tngdetails->item_price = $poCatNo_details[$tngdetails->poCatNo][$tngdetails->item_code]['item_price'];
		// 		$tngdetails->item_quantity = $poCatNo_details[$tngdetails->poCatNo][$tngdetails->item_code]['item_quantity'];
		// 	}
		// }

		$companyInfo = DB::table('mxp_header')->where('header_type', HeaderType::PI)->get();
		$footerData = DB::select("select * from mxp_reportfooter");
		$getUserDetails = PiController::getUserDetails($bookingDetails[0]->user_id);

		return view('maxim.pi_format.piReportPage', compact('companyInfo', 'bookingDetails', 'footerData','buyerDetails','is_type','getUserDetails'));
	}
}