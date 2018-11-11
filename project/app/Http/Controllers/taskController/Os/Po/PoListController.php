<?php
namespace App\Http\Controllers\taskController\Os\Po;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\taskController\Os\Po\PoController;
use App\Model\Os\MxpOsPo;
use App\Model\MxpMrf;
use Auth;
use DB;

class PoListController extends controller 
{
	public function opListView(){
		$poList = MxpOsPo::join('mxp_mrf_table as mmt', 'mmt.mrf_id','mxp_os_po.mrf_id')
			->select('mxp_os_po.*','mmt.booking_order_id')
			->where('mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED)
            ->orderBy('.mxp_os_po.po_id','DESC')
            ->groupBy('mxp_os_po.po_id')
			->paginate(20);

		return view('maxim.os.po.list.po_list',compact('poList'));
	}

	public function getPoReport(Request $request){
		$poDetails = PoController::getOsPoValues($request->poid);
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
		return view('maxim.os.po.po_report',compact('companyInfo','poDetails'));
	}
}