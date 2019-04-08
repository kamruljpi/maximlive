<?php
namespace App\Http\Controllers\taskController\Os\Po;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Os\OsTrackingController;
use App\Http\Controllers\taskController\Os\Po\PoController;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Os\MxpOsPo;
use App\Model\MxpMrf;
use Auth;
use DB;

class PoListController extends controller 
{
	public function opListView(Request $request){
		$os_po_id = isset($request->os_po_id) ? $request->os_po_id : '' ;
		$inputArray = [
			'os_po_id' => $os_po_id
		];

		if(! empty($os_po_id)){
			$poList = MxpOsPo::join('mxp_mrf_table as mmt', 'mmt.mrf_id','mxp_os_po.mrf_id')
			->join('suppliers as sp', 'sp.supplier_id', 'mxp_os_po.supplier_id')
			->select('mxp_os_po.*','mmt.booking_order_id','sp.name')
			->where([
				['mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED],
				['mxp_os_po.po_id',$os_po_id]
			])
            ->orderBy('mxp_os_po.created_at','DESC')
            ->groupBy('mxp_os_po.po_id')
			->paginate(20);

		}else{
			$poList = MxpOsPo::join('mxp_mrf_table as mmt', 'mmt.mrf_id','mxp_os_po.mrf_id')
				->join('suppliers as sp', 'sp.supplier_id', 'mxp_os_po.supplier_id')
				->select('mxp_os_po.*','mmt.booking_order_id','sp.name')
				->where('mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED)
	            ->orderBy('mxp_os_po.created_at','DESC')
	            ->groupBy('mxp_os_po.po_id')
				->paginate(20);
		}

		return view('maxim.os.po.list.po_list',compact('poList','inputArray'));
	}

	public function getPoReport(Request $request){
		$poDetails = PoController::getOsPoValues($request->poid);
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();

		// notification seen action
		NotificationController::updateSeenStatus($request->poid, Auth::user()->user_id);

		return view('maxim.os.po.po_report',compact('companyInfo','poDetails'));
	}

	public function spoListAdvanceSearch(Request $request){

		$inputArray = [
		    'os_po_id' => isset($request->os_po_id) ? $request->os_po_id : '',
		    'supplier_name' => isset($request->supplier_name) ? $request->supplier_name : '',
		    'from_oder_date' => isset($request->from_oder_date_search) ? $request->from_oder_date_search : '',
		    'to_oder_date' => isset($request->to_oder_date_search) ? $request->to_oder_date_search : '',
		    'from_shipment_date' => isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '',
		    'to_shipment_date' => isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : ''
		];

		$poList = $this->filterOsAdvanceSearch($request);

		return view('maxim.os.po.list.po_list',compact('poList','inputArray'));
	}

	public function filterOsAdvanceSearch (Request $request) {

	    $os_po_id = isset($request->os_po_id) ? $request->os_po_id : '';
	    $buyer_name = isset($request->buyer_name_search) ? $request->buyer_name_search : '';
	    $attention = isset($request->attention_search) ? $request->attention_search : '';
	    $supplier_name = isset($request->supplier_name) ? $request->supplier_name : '';
	    $from_oder_date = isset($request->from_oder_date_search) ? $request->from_oder_date_search : '';
	    $to_oder_date = isset($request->to_oder_date_search) ? $request->to_oder_date_search : '';
	    $from_shipment_date = isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '';
	    $to_shipment_date = isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : '';

	    $bookingLists = MxpOsPo::join('mxp_mrf_table as mmt', 'mmt.mrf_id','mxp_os_po.mrf_id')
				->join('suppliers as sp', 'sp.supplier_id', 'mxp_os_po.supplier_id')
				->select('mxp_os_po.*','mmt.booking_order_id','sp.name')
				->where('mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED)
				->orderBy('mxp_os_po.created_at','DESC')
	            ->groupBy('mxp_os_po.po_id');

	    /* only os_po_id input value*/
	    if (!empty($os_po_id) && empty($supplier_name) && empty($attention) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date)) {

	        $bookingList = $bookingLists->where('mxp_os_po.po_id','like','%'.$os_po_id.'%')
	                    ->paginate(20)
	                    ->setPath('list?os_po_id='.$os_po_id);

	    /* only supplier_name input value */
	    } else if(!empty($supplier_name) && empty($buyer_name) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date)) {

	        $bookingList = $bookingLists
	            ->where('sp.name','like','%'.$supplier_name.'%')
	            ->paginate(20)
	            ->setPath('list?supplier_name='.$supplier_name);

	    } else if ($request->from_oder_date_search != '' && $request->to_oder_date_search != '' && empty($buyer_name) && empty($supplier_name) && empty($from_shipment_date) && empty($to_shipment_date)) {

	        if ($request->from_oder_date_search == $request->to_oder_date_search) {
	            $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.created_at', $request->from_oder_date_search)
	                ->paginate(20)
	                ->setPath('list?from_oder_date_search='.$request->from_oder_date_search);
	        } else {

	            $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.created_at','>=',$request->from_oder_date_search)
	                ->whereDate('mxp_os_po.created_at','<=',$request->to_oder_date_search)
	                ->paginate(20)
	                ->setPath('list?from_oder_date_search='.$request->from_oder_date_search.'&to_oder_date_search='.$request->to_oder_date_search);
	        }

	    /* only from_shipment_date and from_shipment_date input value */
	    } else if (!empty($from_shipment_date) && !empty($to_shipment_date) && empty($buyer_name) && empty($supplier_name) && empty($from_oder_date) && empty($to_oder_date)) {

	        if ($from_shipment_date == $to_shipment_date) {

	            $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.shipment_date', $from_shipment_date)
	                ->paginate(20)
	                ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);

	        } else {

	            $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.shipment_date','>=',$from_shipment_date)
	                ->whereDate('mxp_os_po.shipment_date','<=',$to_shipment_date)
	                ->paginate(20)
	                ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);
	        }

	    /* only supplier_name, from_oder_date, and to_oder_date input value */
	    } else if (empty($buyer_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && !empty($supplier_name)) {

	        $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.created_at','>=',$from_oder_date)
	                ->whereDate('mxp_os_po.created_at','<=',$to_oder_date)
	                ->where('sp.name','like','%'.$supplier_name.'%')
	                ->paginate(20)
	                ->setPath('list?supplier_name='.$supplier_name
	                        .'&from_oder_date_search='.$from_oder_date
	                        .'&to_oder_date_search='.$to_oder_date);

	    /* only supplier_name, from_shipment_date, and to_shipment_date input value */
	    } else if (empty($buyer_name) && !empty($supplier_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($from_oder_date) && empty($to_oder_date)) {

	        $bookingList = $bookingLists
	                ->where('sp.name','like','%'.$supplier_name.'%')
	                ->whereDate('mxp_os_po.shipment_date','>=',$from_shipment_date)
	                ->whereDate('mxp_os_po.shipment_date','<=',$to_shipment_date)
	                ->paginate(20)
	                ->setPath('list?from_oder_date_search='.$from_shipment_date
	                    .'&supplier_name='.$supplier_name
	                    .'&to_oder_date_search='.$to_shipment_date);

	    /* only from_oder_date, to_oder_date, from_shipment_date, and to_shipment_date input value */
	    } else if (empty($buyer_name) && empty($supplier_name) && !empty($from_oder_date) && !empty($to_oder_date) && !empty($from_shipment_date) && !empty($to_shipment_date)) {

	        $bookingList = $bookingLists
	                ->whereDate('mxp_os_po.shipment_date','>=',$from_shipment_date)
	                ->whereDate('mxp_os_po.shipment_date','<=',$to_shipment_date)
	                ->whereDate('mxp_os_po.created_at','>=',$from_oder_date)
	                ->whereDate('mxp_os_po.created_at','<=',$to_oder_date)
	                ->paginate(20)
	                ->setPath('list?from_shipment_date_search='.$from_shipment_date
	                        .'&to_shipment_date_search='.$to_shipment_date
	                        .'&from_oder_date_search='.$from_oder_date
	                        .'&to_oder_date_search='.$to_oder_date);

	    /* all input field value */
	    } else if (!empty($buyer_name) && !empty($supplier_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date)) {

	        $bookingList = $bookingLists
	                ->where('sp.name','like','%'.$supplier_name.'%')
	                ->whereDate('mxp_os_po.shipment_date','>=',$from_shipment_date)
	                ->whereDate('mxp_os_po.shipment_date','<=',$to_shipment_date)
	                ->whereDate('mxp_os_po.created_at','>=',$from_oder_date)
	                ->whereDate('mxp_os_po.created_at','<=',$to_oder_date)
	                ->paginate(20)
	                ->setPath('list?from_shipment_date_search='.$from_shipment_date
	                        .'&to_shipment_date_search='.$to_shipment_date
	                        .'&supplier_name='.$supplier_name
	                        .'&from_oder_date_search='.$from_oder_date
	                        .'&to_oder_date_search='.$to_oder_date);
	    }

	    if (!empty($bookingList[0]->po_id)) {
	        return $bookingList;

	    } else {

	        return MxpOsPo::where('po_id','')->paginate(20);

	    } 
	}
}
