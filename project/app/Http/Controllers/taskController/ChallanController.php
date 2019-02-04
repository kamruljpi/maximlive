<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleManagement;
use Illuminate\Http\Request;
use App\Model\MxpBookingChallan;
use App\Model\MxpBookingMultipleChallan;
use App\Model\MxpMultipleChallan;
use App\MxpItemsQntyByBookingChallan;
use App\Model\MxpChallan;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\ChallanFlugs;

class ChallanController extends Controller
{
    const CREATE_MULTIPLE_CHALLAN = "create";
    const UPDATE_MULTIPLE_CHALLAN = "update";

    public function addChallan(Request $request){
        $data = $request->all();

        $challan_details = [];

        $job_ids = $data['job_id'];
        $booking_ids = $data['booking_id'];
        $challan_quantitys = $data['challan_quantity'];

        if (isset($job_ids) && !empty($job_ids)) {
           foreach ($job_ids as $key => $job_id) {
               $challan_details[$key]['job_id'] = $job_id;
               $challan_details[$key]['booking_id'] = $booking_ids[$key];
               $challan_details[$key]['challan_quantity'] = $challan_quantitys[$key];
           }
        }        

        /** this code only for Challan increment id genarate **/

        $companySortName = (DB::table("mxp_bookingbuyer_details")->where('booking_order_id', $booking_ids[0])->select('C_sort_name')->first())->C_sort_name;

        
        $cc = MxpMultipleChallan::distinct('challan_id')->count('challan_id');
        $count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
        $type = "M-CHA" . "-";
        $date = date('dmY');
        $MultipleChallanUniqueID = $type.$date . "-" . $companySortName . "-" . $count;

        /** End  **/

        if (isset($challan_details) && !empty($challan_details)) {
            foreach ($challan_details as $challan) {
                if($challan['challan_quantity'] >= 1) {
                    $store = new MxpMultipleChallan();
                    $store->user_id = Auth::user()->user_id;
                    $store->job_id = $challan['job_id'];
                    $store->challan_id = $MultipleChallanUniqueID;
                    $store->booking_order_id = $challan['booking_id'];
                    $store->quantity = $challan['challan_quantity'];
                    $store->status = ChallanFlugs::CHALLAN_REQUEST_SENT;
                    $store->last_action_at = self::CREATE_MULTIPLE_CHALLAN;
                    $store->save();
                }
            }
        }        

        return \Redirect::route('refresh_challan_view',['chllan_id' => $MultipleChallanUniqueID]);
    }

    public function redirectChallanReport(Request $request){

        $headerValue = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();

        $multipleChallan = DB::table('mxp_multiplechallan as mmc')
                            ->join('mxp_booking as mb','mb.id','mmc.job_id')
                            ->where('mmc.challan_id',$request->chllan_id)
                            ->get();

        $buyerDetails = DB::table("mxp_bookingbuyer_details")->where('booking_order_id',$multipleChallan[0]->booking_order_id)->get();

        $footerData = DB::table('mxp_reportfooter')->where('status', 1)->get();

        return view('maxim.challan.challanBoxingPage', compact('footerData','headerValue','buyerDetails','multipleChallan'));
    }
}