<?php
namespace App\Http\Controllers\taskController\Os\Mrf;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Model\MxpBookingBuyerDetails;
use App\Model\Os\MxpOsPo;
use App\Model\MxpMrf;
use App\Supplier;
use App\User;
use Auth;
use DB;
use Session;

class MrfListController extends Controller
{
    /**
     * @author shohid
     * @return array() 0bject
     */

    public function detailsViewForm(Request $request) {

        $mrf_ids = [];
        $category = [];
        $mrfDetails = [];

        /** 
         * this request value ->
         * get App\Http\Controllers\taskController\TaskController (MRF) 
         * this Session::get('mrfIdList') value ->
         * get App\Http\Controllers\taskController\Os\Po\PoController
         */

        $mrfIdList = isset($request->mrfIdList) ? $request->mrfIdList : Session::get('mrfIdList');

        if(empty($mrfIdList)) {

            /** 
             *  this is work when $request->mid request get
             *  this is work when multiple mrf reload $request->mid request get 
             */

            $mrfIdList = isset($request->mid) ? $request->mid : Session::get('mrfIdList');
        }

        if(!empty($mrfIdList)) {
            $mrf_ids = rtrim($mrfIdList, ",");
            $mrf_ids = explode(' , ', $mrf_ids);   
        }

        if(isset($mrf_ids) && is_array($mrf_ids) && !empty($mrf_ids)) {

            $mrfDetails = MxpMrf::join('mxp_bookingbuyer_details as mbd','mbd.booking_order_id','mxp_mrf_table.booking_order_id')
                ->join('mxp_booking as mb','mb.id','mxp_mrf_table.job_id')
                ->join('mxp_product as mp','mp.product_code','mb.item_code')
                ->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')
                ->select('mxp_mrf_table.*','mbd.buyer_name','mbd.Company_name','mbd.booking_category','mb.item_size_width_height','mb.oos_number','mb.season_code','mb.sku','mb.style','mu.first_name','mu.last_name','mp.other_colors','mp.material')
                ->whereIn('mxp_mrf_table.mrf_id',$mrf_ids)
                ->get();

        } else { /** now this condition is not work **/

            $mrf_ids = isset($request->mid) ? $request->mid : '';

            $mrfDetails = MxpMrf::join('mxp_bookingbuyer_details as mbd','mbd.booking_order_id','mxp_mrf_table.booking_order_id')
                    ->join('mxp_booking as mb','mb.id','mxp_mrf_table.job_id')
                    ->join('mxp_product as mp','mp.product_code','mb.item_code')
                    ->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')
                    ->select('mxp_mrf_table.*','mbd.buyer_name','mbd.Company_name','mbd.booking_category','mb.item_size_width_height','mb.oos_number','mb.season_code','mb.sku','mb.style','mu.first_name','mu.last_name','mp.other_colors','mp.material')
                    ->where('mxp_mrf_table.mrf_id',$request->mid)
                    ->get();
        }

        if(isset($mrfDetails) && !empty($mrfDetails[0]->booking_order_id)){

            // Check in available job id
            $request_mid = $request->mid;
            $mrfDetails->available_jobs = MxpMrf::where([
                        ['job_id_current_status','!=',MrfFlugs::JOBID_CURRENT_STATUS_WAITING_FOR_GOODS],
                        ['job_id_current_status','!=',MrfFlugs::JOBID_CURRENT_STATUS_GOODS_RECEIVE],
                        ['job_id_current_status','!=',MrfFlugs::JOBID_CURRENT_STATUS_PARTIAL_GOODS_RECEIVE],
                    ])
                    ->where(function($query) use ($mrf_ids,$request_mid){
                        $query->where('mrf_id',$request_mid)->orWhereIn('mrf_id',$mrf_ids);
                    })                    
                    ->count();
            // end

            foreach ($mrfDetails as &$value) {

                $value->mrf_accpeted = User::where('user_id',$value->accepted_user_id)
                                    ->select('first_name','last_name')
                                    ->first();
                $value->jobid_accpeted = User::where('user_id',$value->current_status_accepted_user_id)
                                    ->select('first_name','last_name')
                                    ->first();
                $value->po_details = DB::table('mxp_os_po')
                                    ->join('mxp_mrf_table as mrf','mrf.job_id','mxp_os_po.job_id')
                                    ->join('mxp_booking as mp','mp.id','mxp_os_po.job_id')
                                    ->Leftjoin('suppliers as s','s.supplier_id','mxp_os_po.supplier_id')
                                    ->select('mxp_os_po.po_id','mxp_os_po.job_id','mxp_os_po.user_id','mrf.mrf_id','mrf.booking_order_id','mrf.erp_code',
                                        'mrf.item_code','mrf.item_size','mrf.item_description','mrf.gmts_color','mrf.poCatNo','mrf.mrf_quantity','mp.sku','mp.season_code','mp.oos_number','mp.style','mp.item_size_width_height','mxp_os_po.supplier_price','mxp_os_po.material','mxp_os_po.order_date','mxp_os_po.shipment_date','s.name','s.person_name','mrf.job_id_current_status'
                                    )
                                    ->where([
                                        ['mxp_os_po.job_id',$value->job_id],
                                        ['mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED]
                                    ])
                                    ->first();

                    /** calculate left quantity **/
                    if(!empty($value->po_details)) {                        
                        $mrf_quantitys = (DB::table('mxp_store')->select(DB::Raw('sum(item_quantity) as quantitys'))->where('job_id',$value->po_details->job_id)->first())->quantitys;
                        $value->po_details->left_quantity = $mrf_quantitys;

                    }
                    /**End**/

            }

            foreach ($mrfDetails as $valuessss) {
                $category[] = ucfirst(str_replace('_',' ',$valuessss->booking_category));
            }

            /** this make unique Category **/

            $categorys = is_array($category) ? implode(', ', array_unique($category)) : '';
        }

        $suppliers = Supplier::where('status', 1)->where('is_delete', 0)->get();

        NotificationController::updateSeenStatus($request->mid, Auth::user()->user_id);

        // $this->print_me($mrfDetails->available_jobs);
        
        return view('maxim.os.mrf.mrf_Details_View', compact('mrfDetails','suppliers','mrf_ids','categorys'));
    }
}