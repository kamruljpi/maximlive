<?php

namespace App\Http\Controllers\taskController\Os;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\BookingListController;
use App\Http\Controllers\Controller;
use App\Model\MxpMultipleChallan;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use App\Model\Os\MxpOsPo;
use App\Model\MxpMrf;
use App\Model\MxpPi;
use App\MxpIpo;
use Carbon;
use Auth;
use DB;

class OsTrackingController extends Controller
{
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    public function trackingReportView(){
        $bookingList = MxpMrf::where('mxp_mrf_table.is_deleted', BookingFulgs::IS_NOT_DELETED)
                ->select($this->mrf())
                ->orderBy('mxp_mrf_table.job_id_current_status','ASC')
                ->orderBy('mxp_mrf_table.job_id','DESC')
                ->paginate(100);

        if (!empty($bookingList[0]->job_id)) {
            foreach ($bookingList as &$bookingList_value) {
                $bookingList_value->booking_values = DB::table('mxp_booking')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['id',$bookingList_value->job_id]
                    ])
                    ->select('sku','item_size_width_height')
                    ->first();

                $bookingList_value->os_po = DB::table('mxp_os_po')
                    ->join('suppliers', 'suppliers.supplier_id', 'mxp_os_po.supplier_id')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['job_id',$bookingList_value->job_id]
                    ])
                    ->select('mxp_os_po.po_id','mxp_os_po.supplier_id','mxp_os_po.supplier_price','mxp_os_po.order_date','mxp_os_po.shipment_date','mxp_os_po.material','suppliers.name','suppliers.person_name')
                    ->first();
                $bookingList_value->booking_details = DB::table('mxp_bookingbuyer_details')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['booking_order_id', $bookingList_value->booking_order_id]
                    ])
                    ->select('booking_category')
                    ->first();
            }
        }
            // $this->print_me($bookingList);
        return view('maxim.os.os_tracking_report',compact('bookingList'));
    }

    /**
     * @return Os tracking advance search view page
     */

    public function getAdvanceSearchOsList (Request $request) {
        $inputArray = [
            'os_po_id' => isset($request->os_po_id) ? $request->os_po_id : '',
            'supplier_name' => isset($request->supplier_name) ? $request->supplier_name : '',
            'from_oder_date' => isset($request->from_oder_date_search) ? $request->from_oder_date_search : '',
            'to_oder_date' => isset($request->to_oder_date_search) ? $request->to_oder_date_search : '',
            'from_shipment_date' => isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '',
            'to_shipment_date' => isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : ''
        ];

        $bookingList = $this->filterOsAdvanceSearch($request);
        
        return view('maxim.os.os_tracking_report',compact('bookingList','inputArray'));
    }

    /**
     * copy trackingReportView() method and 
     * taskController\BookingListController\filterBookingAdvanceSearch
     * and add some condition and changes
     *
     * @return array()
     */

    public function filterOsAdvanceSearch (Request $request) {

        $os_po_id = isset($request->os_po_id) ? $request->os_po_id : '';
        $buyer_name = isset($request->buyer_name_search) ? $request->buyer_name_search : '';
        $attention = isset($request->attention_search) ? $request->attention_search : '';
        $supplier_name = isset($request->supplier_name) ? $request->supplier_name : '';
        $from_oder_date = isset($request->from_oder_date_search) ? $request->from_oder_date_search : '';
        $to_oder_date = isset($request->to_oder_date_search) ? $request->to_oder_date_search : '';
        $from_shipment_date = isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '';
        $to_shipment_date = isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : '';

        $bookingLists = MxpMrf::where('mxp_mrf_table.is_deleted', BookingFulgs::IS_NOT_DELETED)
                ->select($this->mrf())
                ->groupBy('mxp_mrf_table.job_id')
                ->orderBy('mxp_mrf_table.job_id','DESC');

        /* only os_po_id input value*/
        if (!empty($os_po_id) && empty($supplier_name) && empty($attention) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                        ->where('mop.po_id','like','%'.$os_po_id.'%')
                        ->paginate(20)
                        ->setPath('list?os_po_id='.$os_po_id);

        /* only supplier_name input value */
        } else if(!empty($supplier_name) && empty($buyer_name) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                ->join('suppliers as ms','ms.supplier_id','mop.supplier_id')
                ->where('ms.name','like','%'.$supplier_name.'%')
                ->paginate(20)
                ->setPath('list?supplier_name='.$supplier_name);

        } else if ($request->from_oder_date_search != '' && $request->to_oder_date_search != '' && empty($buyer_name) && empty($supplier_name) && empty($from_shipment_date) && empty($to_shipment_date)) {

            if ($request->from_oder_date_search == $request->to_oder_date_search) {
                $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->whereDate('mop.created_at', $request->from_oder_date_search)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$request->from_oder_date_search.'&to_oder_date_search='.$request->to_oder_date_search);
            } else {

                $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->whereDate('mop.created_at','>=',$request->from_oder_date_search)
                    ->whereDate('mop.created_at','<=',$request->to_oder_date_search)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$request->from_oder_date_search.'&to_oder_date_search='.$request->to_oder_date_search);
            }

        /* only from_shipment_date and from_shipment_date input value */
        } else if (!empty($from_shipment_date) && !empty($to_shipment_date) && empty($buyer_name) && empty($supplier_name) && empty($from_oder_date) && empty($to_oder_date)) {

            if ($from_shipment_date == $to_shipment_date) {

                $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->whereDate('mop.shipment_date', $from_shipment_date)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);

            } else {

                $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->whereDate('mop.shipment_date','>=',$from_shipment_date)
                    ->whereDate('mop.shipment_date','<=',$to_shipment_date)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);
            }

        /* only supplier_name, from_oder_date, and to_oder_date input value */
        } else if (empty($buyer_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && !empty($supplier_name)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->join('suppliers as ms','ms.supplier_id','mop.supplier_id')
                    ->whereDate('mop.created_at','>=',$from_oder_date)
                    ->whereDate('mop.created_at','<=',$to_oder_date)
                    ->where('ms.name','like','%'.$supplier_name.'%')
                    ->paginate(20)
                    ->setPath('list?supplier_name='.$supplier_name
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only supplier_name, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && !empty($supplier_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($from_oder_date) && empty($to_oder_date)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->join('suppliers as ms','ms.supplier_id','mop.supplier_id')
                    ->where('ms.name','like','%'.$supplier_name.'%')
                    ->whereDate('mop.shipment_date','>=',$from_shipment_date)
                    ->whereDate('mop.shipment_date','<=',$to_shipment_date)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$from_shipment_date
                        .'&supplier_name='.$supplier_name
                        .'&to_oder_date_search='.$to_shipment_date);

        /* only from_oder_date, to_oder_date, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && empty($supplier_name) && !empty($from_oder_date) && !empty($to_oder_date) && !empty($from_shipment_date) && !empty($to_shipment_date)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->whereDate('mop.shipment_date','>=',$from_shipment_date)
                    ->whereDate('mop.shipment_date','<=',$to_shipment_date)
                    ->whereDate('mop.created_at','>=',$from_oder_date)
                    ->whereDate('mop.created_at','<=',$to_oder_date)
                    ->paginate(20)
                    ->setPath('list?from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* all input field value */
        } else if (!empty($buyer_name) && !empty($supplier_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date)) {

            $bookingList = $bookingLists->join('mxp_os_po as mop','mop.mrf_id','mxp_mrf_table.mrf_id')
                    ->join('suppliers as ms','ms.supplier_id','mop.supplier_id')
                    ->where('ms.name','like','%'.$supplier_name.'%')
                    ->whereDate('mop.shipment_date','>=',$from_shipment_date)
                    ->whereDate('mop.shipment_date','<=',$to_shipment_date)
                    ->whereDate('mop.created_at','>=',$from_oder_date)
                    ->whereDate('mop.created_at','<=',$to_oder_date)
                    ->paginate(20)
                    ->setPath('list?from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&supplier_name='.$supplier_name
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);
        }

        if (!empty($bookingList[0]->job_id)) {
            foreach ($bookingList as &$bookingList_value) {
                $bookingList_value->booking_values = DB::table('mxp_booking')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['id',$bookingList_value->job_id]
                    ])
                    ->select('sku','item_size_width_height')
                    ->first();

                $bookingList_value->os_po = DB::table('mxp_os_po')
                    ->join('suppliers', 'suppliers.supplier_id', 'mxp_os_po.supplier_id')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['job_id',$bookingList_value->job_id]
                    ])
                    ->select('mxp_os_po.po_id','mxp_os_po.supplier_id','mxp_os_po.supplier_price','mxp_os_po.order_date','mxp_os_po.shipment_date','mxp_os_po.material','suppliers.name','suppliers.person_name')
                    ->first();
                $bookingList_value->booking_details = DB::table('mxp_bookingbuyer_details')
                    ->where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['booking_order_id', $bookingList_value->booking_order_id]
                    ])
                    ->select('booking_category')
                    ->first();
            }

            return $bookingList;

        } else {

            return MxpMrf::where('mrf_id','')->paginate(20);

        } 
    }

    public function exportReport(Request $request){
        $export = $request->all();
        $data = array();
        if(isset($export) && !empty($export)){
            foreach ($export as $key => $xprt) {
                $key_i = 0;
                foreach ($xprt as $xprtk => $xprtv) {
                    $exptf = str_replace('_',' ', $key);
                    $data[$key_i][ucwords($exptf)] = $xprtv;
                    $key_i++;
                }
            }
        }
        $today = Carbon\Carbon::today()->format('d-m-y');
        $this->excel->create('OS Tracking Report- '.$today ,function($excel) use ($data){
            $excel->sheet('Sheet 1',function($sheet) use ($data){
                $sheet->setColumnFormat(array(
                    'B' =>  \PHPExcel_Style_NumberFormat::FORMAT_TEXT,
                    'D' => '0.00',
                    'E' => 'dd-mm-yyyy',
                ));

                $sheet->fromArray($data);
            });
        })->download('xlsx');
        return redirect()->back();
    }

    public function mrf(){
        return [
            'mxp_mrf_table.job_id','mxp_mrf_table.mrf_id','mxp_mrf_table.booking_order_id','mxp_mrf_table.erp_code','mxp_mrf_table.item_code','mxp_mrf_table.item_size','mxp_mrf_table.item_description','mxp_mrf_table.mrf_quantity','mxp_mrf_table.mrf_quantity','mxp_mrf_table.gmts_color','mxp_mrf_table.poCatNo','mxp_mrf_table.orderDate','mxp_mrf_table.shipmentDate','mxp_mrf_table.mrf_status','mxp_mrf_table.job_id_current_status'
        ];
    }

    public function os_po(){
        return [
            'mxp_os_po.po_id','mxp_os_po.supplier_id','mxp_os_po.supplier_price','mxp_os_po.order_date','mxp_os_po.shipment_date','mxp_os_po.material'
        ];
    }

    public function suppliers(){
        return [
            'suppliers.name','suppliers.person_name'
        ];
    }

}
