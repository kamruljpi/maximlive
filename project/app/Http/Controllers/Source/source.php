<?php
namespace App\Http\Controllers\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Controller;
use App\Http\Controllers\taskController\BookingListController;
use App\Model\BookingFile;
use App\Model\MxpBooking;
use App\Model\MxpMultipleChallan;
use App\Model\MxpPi;
use Carbon\Carbon;
use App\Supplier;
use App\MxpIpo;
use App\Model\MxpMrf;
use Auth;
use DB;
/**
 * 
 */
class source extends Controller
{
	public function getUserDetails( $bookingId ){
        $getBookingUserDetails = DB::table('mxp_booking as mb')
            ->join('mxp_users as ms','mb.user_id','=','ms.user_id')
            ->select('ms.first_name','ms.middle_name','ms.last_name')
            ->where('mb.booking_order_id',$bookingId)
            ->first();
        return $getBookingUserDetails;
    }

    public function getCsExportData(){
        $booking_list_controller = new BookingListController();
        $bookingList = DB::table('mxp_bookingbuyer_details')->where([['is_complete', BookingFulgs::IS_COMPLETE],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('booking_order_id')->orderBy('id','DESC')->get();

        if(isset($bookingList) && !empty($bookingList)){
            foreach ($bookingList as &$booking) {
                $booking->itemLists = $booking_list_controller->getBookingItemLists($booking->booking_order_id);

                foreach ($booking->itemLists as &$itemListssvalue) {

                    $itemListssvalue->pi = MxpPi::select(DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where([['job_no',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_no')->first();   

                    $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                    $itemListssvalue->mrf = MxpMrf::select(DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                    $itemListssvalue->ipo = MxpIpo::select(DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();
                }
            }
        }

        return $bookingList;
    }

    public function csTrackingExportField(){
    	return [
            'job_id','buyer_name','company_name','attention_invoice','booking_order_id','po_cat_no','p_ids','challan_ids','ipo_ids','mrf_ids','order_date','requested_date','item_code','erp_code','item_size','item_description','sku','item_quantity','item_price','total_price','total'
        ];
    }
}