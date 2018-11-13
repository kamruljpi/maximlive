<?php

namespace App\Http\Controllers\taskController\History;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\History\Source\FindRestore;
use App\Http\Controllers\taskController\History\Source\RestorePi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpPi;
use Session;
use Auth;
use DB;

class RestoreData extends Controller
{
	const PI_FILE_PATH = 'pi.pi_restore_data'; 

	protected function index(){
		return view('maxim.history.restore');
	}

	protected function getPiDeletedValue(){
		$data = RestorePi::getDeletedPiValue();
		return view('maxim.history.pi.pi_restore_data',compact('data'));
	}

	protected function piRestoreRequest($id){

		return RestorePi::piRestoreRequest($id);
	}

	protected function restoreFindRequest(Request $request){
		$filter_type = $request['filter_type'];
		$filter_value = $request['filter_value'];

		if(!empty($filter_type) && !empty($filter_value)){
			switch ($filter_type) {
				case 'booking':
					return "Comming Soon";
				case 'pi':
					return (new FindRestore($filter_value,'p_id',new MxpPi(),self::PI_FILE_PATH))->search();
				case 'challan':
					return "Comming Soon";
				case 'ipo':
					return "Comming Soon";
				case 'mrf':
					return "Comming Soon";
				case 'challan':
					return "Comming Soon";
				default:
				return "<span> You are enter invalid case value.</span><br>
				input name must be `filter_type` and valid values (booking,pi,challan,ipo,mrf,challan)";
			}
		}

		return redirect()->back();
	}
}