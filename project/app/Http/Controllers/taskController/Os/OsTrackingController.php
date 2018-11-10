<?php

namespace App\Http\Controllers\taskController\Os;

use App\Http\Controllers\Controller;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Model\MxpMrf;
use App\Model\MxpMultipleChallan;
use App\Model\MxpPi;
use App\MxpIpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\taskController\BookingListController;
use Maatwebsite\Excel\Excel;
use Carbon;

class OsTrackingController extends Controller
{
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    public function trackingReportView(){
        $bookingList = DB::table('mxp_mrf_table')->where('is_deleted', '0')->orderBy('id','DESC')->paginate(150);

        return view('maxim.booking_list.os_tracking_report',compact('bookingList'));
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

}
