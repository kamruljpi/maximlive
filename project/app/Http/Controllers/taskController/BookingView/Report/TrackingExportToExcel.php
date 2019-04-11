<?php

namespace App\Http\Controllers\taskController\BookingView\Report;

use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
use App\Http\Controllers\Source\source;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Carbon;

class TrackingExportToExcel extends Controller
{
    public function __construct(Excel $excel){ 
        $this->excel = $excel;
    }

    public function exportReport($request){
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
        $this->excel->create($today.'-Tracking Report' ,function($excel) use ($data){
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

    public function exportRequest(Request $request) {
        // if(isset($request['type']) && !empty($request['type'])) {
        //     if('cs' == $request['type']){

        //         $source = new source();
        //         $cs_data = $source->getCsExportData();
        //         $cs_export_data = array();

        //         // if(!empty($cs_data[0]->booking_order_id)){
        //             foreach ($cs_data as $cs_value) {
        //                 $key_i = 0;
        //                 foreach ($cs_value->itemLists as $key => $cs_data_value) {
        //                     $cs_export_data[$key_i] = $cs_data_value;                            
        //                     $key_i++;
        //                 }
        //             }
        //         // }
        //         $this->print_me($cs_export_data);
        //         return $request->type;

        //     }elseif('planning' == $request['type']) {

        //     }elseif ('os' == $request['type']) {
        //         # code...
        //     }
        //     return $request->type;
        // }die();
        return $this->exportReport($request);
    }
}
