<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleManagement;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\Http\Controllers\taskController\Flugs\HeaderType;

class ChallanListController extends Controller
{
    public function challanListView(){
        $challanDetails = DB::table('mxp_multiplechallan')
            ->groupBy('challan_id')
            ->orderBy('id','DESC')
            ->paginate(15);
        return view('maxim.challan.list.challanList',compact('challanDetails'));
    }


    public function showChallanReport(Request $request){
       // $this->print_me($request->bid);
        $headerValue = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();

        $multipleChallan = DB::table('mxp_multiplechallan as mmc')
                            ->join('mxp_booking as mb','mb.id','mmc.job_id')
                            ->where('mmc.challan_id',$request->cid)
                            ->get();

        $buyerDetails = DB::table("mxp_bookingbuyer_details")->where('booking_order_id',$request->bid)->get();
        
        $footerData = DB::select("select * from mxp_reportfooter");

        return view('maxim.challan.challanBoxingPage', compact('footerData','headerValue','buyerDetails','multipleChallan'));
    }

    public function getChallanListByChallanId(Request $request){

        $challanList = DB::table('mxp_multiplechallan')
            ->where('challan_id', 'like', '%'.$request->challan_id.'%')
            ->groupBy('challan_id')
            ->orderBy('id','DESC')
            ->get();

        return $challanList;
    }

    public function getChallanListBySearch(Request $request){

        $challanList = DB::table('mxp_multiplechallan');
        $checkValidation = false;

        if($request->booking_id_search != '')
        {
            $checkValidation = true;
            $challanList->where('checking_id','like','%'.$request->booking_id_search.'%');
        }
        if($request->from_create_date_search != '' && $request->to_create_date_search != '')
        {
            $checkValidation = true;
            if($request->from_create_date_search == $request->to_create_date_search)
                $challanList->whereDate('created_at', $request->from_create_date_search);
            else
                $challanList->whereDate('created_at','>=',$request->from_create_date_search)->whereDate('created_at','<=',$request->to_create_date_search);
        }

        if($checkValidation)
        {
            $challans = $challanList->groupBy('challan_id')->orderBy('id','DESC')->get();
            return $challans;
        }
        else
            return null;
    }
}
