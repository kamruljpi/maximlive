<?php
namespace App\Http\Controllers\taskController\Os;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMrf;
use Auth;
use DB;

class PoController extends Controller
{
	public function poGenarateView(){
		return view('maxim.os.po.po_genarate',compact('bookingList'));
	}

	public function storeOsPo(Request $request){
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
		return view('maxim.os.po.po_report',compact('companyInfo'));
	}
}