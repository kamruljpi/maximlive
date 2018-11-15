<?php

namespace App\Http\Controllers\taskController\History\Restore;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\History\Restore\Source\Restore;
use App\Http\Controllers\taskController\History\Restore\Source\Resource;
use App\Http\Controllers\taskController\History\Restore\Source\FindRestore;
use App\Http\Controllers\taskController\History\Restore\Source\RestoreList;
use App\Http\Controllers\Controller;
use App\Model\Os\MxpOsPo;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMultipleChallan;
use App\Model\MxpBookingChallan;
use App\Model\MxpBooking;
use App\Model\MxpMrf;
use App\Model\MxpPi;
use App\MxpIpo;
use Session;
use Auth;
use DB;

class RestoreData extends Controller
{
	const IPO_FILE_PATH = 'ipo.ipo_restore';
	const MRF_FILE_PATH = 'mrf.mrf_restore';
	const PI_FILE_PATH = 'pi.pi_restore_data';
	const CHALLAN_FILE_PATH = 'challan.challan_restore';
	const BOOKING_FILE_PATH = 'booking.booking_restore_data';

	protected function index(){
		return view('maxim.history.restore.restore');
	}

	protected function sentListRequest(Request $request){
		$type = $request->type;
		if(!empty($type) && !empty($type)){
			switch ($type) {
				case HeaderType::BOOKING : 
					return (new RestoreList($type,'booking_order_id',new MxpBookingBuyerDetails(),self::BOOKING_FILE_PATH))->getRestoreList();
				case HeaderType::PI :
					return (new RestoreList($type,'p_id',new MxpPi(),self::PI_FILE_PATH))->getRestoreList();
				case HeaderType::IPO :
					return (new RestoreList($type,'ipo_id',new MxpIpo(),self::IPO_FILE_PATH))->getRestoreList();
				case HeaderType::MRF :
					return (new RestoreList($type,'mrf_id',new MxpMrf(),self::MRF_FILE_PATH))->getRestoreList();
				case HeaderType::CHALLAN :
					return (new RestoreList($type,'challan_id',new MxpMultipleChallan(),self::CHALLAN_FILE_PATH))->getRestoreList();
				default:
				return "<span> You are enter invalid case value.</span><br>
				input name must be `filter_type` and valid values (booking,pi,challan,ipo,mrf,challan)";
			}
		}
		Session::flash('message','');
		return redirect()->back();
	}

	protected function piRestoreRequest(Request $request){
		$this->print_me($request->type);
		return Resource::piRestoreRequest($id);
	}

	protected function sentFindRequest(Request $request){
		$filter_type = $request['filter_type'];
		$filter_value = $request['filter_value'];

		if(!empty($filter_type) && !empty($filter_value)){
			switch ($filter_type) {
				case HeaderType::BOOKING :
					return (new FindRestore($filter_type,$filter_value,'booking_order_id',new MxpBookingBuyerDetails(),self::BOOKING_FILE_PATH))->search();
				case HeaderType::PI :
					return (new FindRestore($filter_type,$filter_value,'p_id',new MxpPi(),self::PI_FILE_PATH))->search();
				case HeaderType::IPO :
					return (new FindRestore($filter_type,$filter_value,'ipo_id',new MxpIpo(),self::IPO_FILE_PATH))->search();
				case HeaderType::MRF :
					return (new FindRestore($filter_type,$filter_value,'mrf_id',new MxpMrf(),self::MRF_FILE_PATH))->search();
				case HeaderType::CHALLAN :
					return (new FindRestore($filter_type,$filter_value,'challan_id',new MxpMultipleChallan(),self::CHALLAN_FILE_PATH))->search();
				default:
				return "<span> You are enter invalid case value.</span><br>
				input name must be `filter_type` and valid values (booking,pi,challan,ipo,mrf,challan)";
			}
		}
		Session::flash('message','Please enter a value');
		return redirect()->back();
	}
}