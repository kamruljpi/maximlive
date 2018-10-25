<?php

namespace App\Http\Controllers\taskController\BookingView\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Carbon;

class TrackingController extends Controller
{
    public function __construct(Excel $excel){
        $this->excel = $excel;
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
        $this->excel->create('Tracking Report- '.$today ,function($excel) use ($data){
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
