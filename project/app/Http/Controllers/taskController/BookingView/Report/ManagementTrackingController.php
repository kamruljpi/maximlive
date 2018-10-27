<?php

namespace App\Http\Controllers\taskController\BookingView\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Model\MxpMrf;
use App\Model\MxpMultipleChallan;
use App\Model\MxpPi;
use App\MxpIpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\taskController\BookingListController;

class ManagementTrackingController extends Controller
{
    public function managementTrackingReport(){
        $bookingList = DB::table('mxp_bookingbuyer_details')->where([['is_complete', BookingFulgs::IS_COMPLETE],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('booking_order_id')->orderBy('id','DESC')->paginate(150);

        if(isset($bookingList) && !empty($bookingList)){
            foreach ($bookingList as &$booking) {
                $booking->itemLists = BookingListController::getBookingItemLists($booking->booking_order_id);

                foreach ($booking->itemLists as &$itemListssvalue) {

                    $itemListssvalue->pi = MxpPi::select(DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where('job_no',$itemListssvalue->id)->groupBy('job_no')->first();

                    $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();

                    $itemListssvalue->mrf = MxpMrf::select(DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();

                    $itemListssvalue->ipo = MxpIpo::select(DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();
                }
            }
        }

        return view('maxim.booking_list.management_booking_list',compact('bookingList'));
    }

}
