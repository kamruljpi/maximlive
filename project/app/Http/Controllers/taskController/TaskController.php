<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\RoleManagement;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpBooking;
use App\MxpIpo;
use App\Supplier;
use App\MxpProduct;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Model\MxpMrf;
use App\Http\Controllers\taskController\Os\Mrf\MrfListController;
use App\Http\Controllers\Source\User\UserAccessBuyerList;

class TaskController extends Controller 
{
	use UserAccessBuyerList;

	CONST CREATE_IPO = "create";
	CONST UPDATE_IPO = "update";

	public function getBuyerCompany(Request $request) {
		return json_encode(DB::table('mxp_party')->where('name_buyer', $request->buyerName)->get());
	}

	public function getItemCode() {
		$results = array();

		/** buyer wiase booking value filter **/

		$buyerList = $this->getUserByerList(); // use trait class

		if(isset($buyerList) && !empty($buyerList)){

			$productDetails = MxpProduct::whereIn('id_buyer',$buyerList)->select('product_code')->get();

		}else if(Auth::user()->type == 'super_admin'){

			$productDetails = MxpProduct::select(DB::Raw('DISTINCT product_code'))->get();	

		}else{
			$productDetails = MxpProduct::select(DB::Raw('DISTINCT product_code'))->get();			
		}

		/** End **/

		if (isset($productDetails) && !empty($productDetails)) {
			foreach ($productDetails as $itemKey => $itemValue) {

				$results[]['name'] = $itemValue->product_code;
			}
		}
		return json_encode($results);
	}
	public function gettaskActionOrsubmited(Request $request) {

		return \Redirect()->Route('dashboard_view');
	}
	public function taskActionOrsubmited(Request $request) {
		$roleManage = new RoleManagement();
		$datas = $request->all();

		$taskType = isset($request->taskType) ? $request->taskType : '';
		if ($taskType === 'booking' || $taskType === 'FSC Booking') {
			if($taskType == 'booking'){
				$taskType = 'Create Booking';
			}else{
				$taskType = 'Create FSC Booking';
			}

			$buyerDetails = DB::table('mxp_party')
				->where([
					['name_buyer', $request->buyerName],
					['name', $request->companyName],
				])
				->get();
			return view('maxim.orderInput.orderInputIndex', compact('buyerDetails'))->with(['taskType' => $taskType]);

		} elseif ($taskType === 'PI' || $taskType === 'FSC PI') {

			// there are was a multiple PI id
			$booking_id_1 = isset($request->bookingIdList) ? $request->bookingIdList : '' ;
			// there was a single PI id
			$booking_id_2 = isset($request->bookingId) ? $request->bookingId : '' ;

			// if $booking_id_1 this field is empty.
			$bookind_idss = !empty($booking_id_1) ? $booking_id_1 : $booking_id_2 ;

			$booking_id = rtrim($bookind_idss, ",");
			$booking_id = explode(' , ', $booking_id);
			$buyerName = '';
			$companyName = '';
			$iteration = 0;

			foreach ($booking_id as $bookingid) {
				$vendorDetails = MxpBookingBuyerDetails::where([['booking_order_id',$bookingid],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->first();
				if (! $vendorDetails->Company_name) {
					return redirect()->back()->withErrors($bookingid." Booking No. not found.");
				}
				if ($iteration > 0) {
					if ($buyerName != $vendorDetails->buyer_name || 
						$companyName != $vendorDetails->Company_name ) {
						return redirect()->back()->withErrors("Booking order ids are not in same Vendors");
					}
				}

				$buyerName = $vendorDetails->buyer_name;
				$companyName = $vendorDetails->Company_name;
				$iteration++;
			}

			if($taskType == 'PI'){
				$is_type = 'non_fsc';
				$bookingDetails = $this->getNonfscBookingValue($booking_id);
			}else if($taskType == 'FSC PI'){
				$is_type = 'fsc';
				$bookingDetails = $this->getFscBookingValue($booking_id);
			}
			return view('maxim.pi_format.pi_generate_page',compact('bookingDetails','is_type'));

		} elseif ($taskType === 'IPO') {

				$validMessages = [
					'bookingId.required' => 'Booking Id is required.',
				];
				$datas = $request->all();
				$validator = Validator::make($datas,
					[
						'bookingId' => 'required',
					], $validMessages);

				if ($validator->fails()) {
					return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
				}
				$validationError = $validator->messages();

				$vendorDetails = MxpBookingBuyerDetails::where([['booking_order_id',$request->bookingId],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->first();
				if (! $vendorDetails->Company_name) {
					return redirect()->back()->withErrors($request->bookingId." Booking No not found.");
				}
				$ipoValue = DB::table("mxp_booking_challan")->where('booking_order_id', $request->bookingId)->get();
				if (empty($ipoValue)) {
					return \Redirect()->Route('dashboard_view');
				}
				$buyerDetails = DB::table("mxp_bookingbuyer_details")
									->where('booking_order_id', $request->bookingId)
									->get();

				$headerValue = DB::table("mxp_header")
									->where('header_type', 11)
									->get();

				$ipoListValue = DB::table("mxp_ipo")
									->select('id','booking_order_id','ipo_id')
									->where('booking_order_id', $request->bookingId)
									->get();

				return view('maxim.ipo.ipo_price_manage', [
					'headerValue' => $headerValue,
					'buyerDetails' => $buyerDetails,
					'sentBillId' => $ipoValue,
					'ipoIds' => '0',
					'ipoIncrease' => $request->ipoIncrease,
					'ipoListValue' => $ipoListValue,
				]);

		} elseif ($taskType === 'MRF') {

			// there was a multiple mrf Id
			$mrf_id_1 = isset($request->mrfIdList) ? $request->mrfIdList : '' ;
			//there was a single mrf Id
			$mrf_id_2 = isset($request->mrfId) ? $request->mrfId : '' ;

			// if $mrf_id_1 one field is empty
			$mrf_idsss = !empty($mrf_id_1) ? $mrf_id_1 : $mrf_id_2 ;

			$mrf_ids = rtrim($mrf_idsss, ",");
			$mrf_ids = explode(' , ', $mrf_ids);

			foreach ($mrf_ids as $mrf_id) {

				$mrfDetails = MxpMrf::where([['mrf_id',$mrf_id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->first();
				if (! $mrfDetails->mrf_id) {
					return redirect()->back()->withErrors($mrf_id." Mrf No. not found.");
				}
			}

			if(is_array($mrf_ids) && !empty($mrf_ids)) {

				$mrflist_controller = new MrfListController();
				return $mrflist_controller->detailsViewForm($request);

			} else {

				return redirect()->back()->withErrors($mrf_id." MRF No. not found.");
			} 


		} elseif ($taskType === 'challan') { // it's not working now. 
		
			return "Coming soon ";
		
			$validMessages = [
				'bookingIdList.required' => 'Booking Id field is required.',
			];
			$validator = Validator::make($datas,
				[
					'bookingIdList' => 'required',
				],
				$validMessages
			);

			if ($validator->fails()) {
				return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
			}

			$validationError = $validator->messages();
			$bookedId = rtrim($request->bookingIdList, ", ");

			$bookingIds = explode(",", $bookedId);
			$bookingIdList = [];
			$companyName = '';
			$iteration = 0;

			foreach ($bookingIds as $bookingId) {
				$tempbookingId = $bookingId;
				$tempbookingId = str_replace(' ', '', $tempbookingId);
				$tempbookingId = str_replace(',', '', $tempbookingId);
				$companyDetails = MxpBookingBuyerDetails::where('booking_order_id', $tempbookingId)->first();
				if ($iteration > 0) {
					if ($companyDetails->Company_name != $companyName) {
						return redirect()->back()->withInput($request->input())->withErrors("Booking order ids are not in same company");
					}
				}
				$companyName = $companyDetails->Company_name;
				$iteration++;
				$bookingDetails = DB::select("SELECT * FROM mxp_items_details_by_booking_challan WHERE booking_order_id = '" . $tempbookingId . "'");
				foreach ($bookingDetails as $currentBooking) {
					$checkMatch = true;
					foreach ($bookingIdList as $bookingList) {
						if (($bookingList->item_code == $currentBooking->item_code) && ($bookingList->item_size == $currentBooking->item_size) && ($bookingList->gmts_color == $currentBooking->gmts_color)) {
//                            echo "item size is ".$currentBooking->item_size." has qnty ".$currentBooking->item_quantity." and pre total is ".$bookingList->item_quantity." and after add ";
							$bookingList->item_quantity += $currentBooking->item_quantity;
							$bookingList->booking_order_id = $bookingList->booking_order_id . ',' . $currentBooking->booking_order_id;
							$bookingList->booking_challan_id = $bookingList->booking_challan_id . ',' . $currentBooking->booking_challan_id;
							$checkMatch = false;
//                            echo $bookingList->item_quantity."<br>";
						}
					}
					if ($checkMatch) {
						array_push($bookingIdList, $currentBooking);
					}

				}
			}
//            $this->print_me($bookingIdList);

//            for ($i = 1; $i < count($bookingIdList); $i++){
			//         	    for( $j = 0; $j < $i; $j++){
			//                    if ($bookingIdList[$i]->item_code == $bookingIdList[$j]->item_code){
			//                        $bookingIdList[$j]->item_size = $bookingIdList[$j]->item_size.','.$bookingIdList[$i]->item_size;
			//                        $bookingIdList[$j]->item_quantity = $bookingIdList[$j]->item_quantity.','.$bookingIdList[$i]->item_quantity;
			//                        $bookingIdList[$j]->gmts_color = $bookingIdList[$j]->gmts_color.','.$bookingIdList[$i]->gmts_color;
			////                        $bookingIdList[$j]->booking_order_id = $bookingIdList[$j]->booking_order_id.','.$bookingIdList[$i]->booking_order_id;
			//                        $bookingIdList[$j]->booking_challan_id = $bookingIdList[$j]->booking_challan_id.'_'.$bookingIdList[$i]->booking_challan_id;
			//                        unset($bookingIdList[$i]);
			//                    }
			//                }
			//            }
			foreach ($bookingIdList as $booking) {
				if (!$booking->items_details_id) {
					unset($booking);
				}

			}
			$bookingDetails = $bookingIdList;
//            $this->print_me($bookingIdList);

			$buyerDetails = DB::select("SELECT * FROM mxp_bookingbuyer_details WHERE booking_order_id = '" . $bookedId . "'");

			if (empty($bookingDetails)) {
				StatusMessage::create('empty_booking_data', 'This booking Id does not show any result . Please check booking Id !');

				return \Redirect()->Route('dashboard_view');
			}

			$challanHistory = DB::table('mxp_multiplechallan')
				->where('checking_id',$bookedId)
				->get();

				// $this->print_me($challanHistory);

			return view('maxim.challan.challan', compact('bookingDetails','challanHistory'));
		} else if($taskType === 'bill'){

			$conversion_rate = $request->conversion_rate;
			$companyInfo = DB::table('mxp_header')->where('header_type', HeaderType::COMPANY)->get();
			$bookingDetails = DB::select('call getBookinAndBuyerDeatils("' . $request->bookingId . '")');
			if (empty($bookingDetails)) {
				StatusMessage::create('empty_booking_data', 'This booking Id doesnot show any result . Please check booking Id !');

				return \Redirect()->Route('dashboard_view');
			}

			$footerData = DB::select("select * from mxp_reportfooter");

			return view('maxim.bill_copy.bill_report', compact('companyInfo', 'bookingDetails', 'footerData', 'conversion_rate'));

		} else {
			$validMessages = [
				'taskType.required' => 'TaskType field is required.',
			];
			$validator = Validator::make($datas,
				[
					'taskType' => 'required',
				],
				$validMessages
			);

			if ($validator->fails()) {
				return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
			}

			$validationError = $validator->messages();
		}
	}
	public static function getOrderQuantity($booking_order_id, $item_code, $item_size = null, $color = null) {

		if ($item_size == null) {
			if ($color == null) {
				$bookingQuantityDetails = DB::select("SELECT item_quantity FROM mxp_booking WHERE booking_order_id = '" . $booking_order_id . "' AND item_code = '" . $item_code . "'");
			} else {
				$bookingQuantityDetails = DB::select("SELECT item_quantity FROM mxp_booking WHERE booking_order_id = '" . $booking_order_id . "' AND item_code = '" . $item_code . "' AND gmts_color = '" . $color . "'");
			}

		} else {
			if ($color == null) {
				$bookingQuantityDetails = DB::select("SELECT item_quantity FROM mxp_booking WHERE booking_order_id = '" . $booking_order_id . "' AND item_code = '" . $item_code . "' AND item_size = '" . $item_size . "'");
			} else {
				$bookingQuantityDetails = DB::select("SELECT item_quantity FROM mxp_booking WHERE booking_order_id = '" . $booking_order_id . "' AND item_code = '" . $item_code . "' AND item_size = '" . $item_size . "' AND gmts_color = '" . $color . "'");
			}

		}
		return isset($bookingQuantityDetails[0]->item_quantity) ? $bookingQuantityDetails[0]->item_quantity : 0;

	}


	public function getNonfscBookingValue($booking_order_id){
		if(isset($booking_order_id) && !empty($booking_order_id)){
			$bookingDetails = MxpBooking::where([['is_pi_type',BookingFulgs::IS_PI_UNSTAGE_TYPE],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->whereIn('booking_order_id',$booking_order_id)->orderBy('id','ASC')->get();
		}
		return $bookingDetails;
	}

	public function getFscBookingValue($booking_order_id){
		if(isset($booking_order_id) && !empty($booking_order_id)){
			$bookingDetails = MxpBooking::where([['is_pi_type',BookingFulgs::IS_PI_UNSTAGE_TYPE],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->whereIn('booking_order_id',$booking_order_id)->orderBy('id','ASC')->get();
		}
		return $bookingDetails;
	}
}
