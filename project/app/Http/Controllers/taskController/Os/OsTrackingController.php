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
