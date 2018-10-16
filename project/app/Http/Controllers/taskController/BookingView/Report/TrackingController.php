<?php

namespace App\Http\Controllers\taskController\BookingView\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class TrackingController extends Controller
{
    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    public function exportReport(Request $request){

        $export=$request->all();

        $this->excel->create('Export Data',function($excel) use ($export){
            $excel->sheet('Sheet 1',function($sheet) use ($export){
                $sheet->fromArray($export);
            });
        })->download('xls');


        return redirect()->back();
    }

}
