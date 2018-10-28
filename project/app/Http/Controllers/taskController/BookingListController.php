<?php

namespace App\Http\Controllers\taskController;

use  App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\taskController\BookingController;
use App\Http\Controllers\RoleManagement;
use App\Model\BookingFile;
use App\Model\MxpBooking;
use App\Model\MxpMultipleChallan;
use App\Model\MxpPi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Supplier;
use App\MxpIpo;
use App\Model\MxpMrf;
use Validator;
use Auth;
use DB;
use Illuminate\Support\Facades\Response;
use ZipArchive;
use App\Model\MxpBookingBuyerDetails;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\User;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;


class BookingListController extends Controller
{   
    CONST CREATE_IPO = "create";
    CONST UPDATE_IPO = "update";

    public function bookingListView(){

        $bookingList = MxpBookingBuyerDetails::groupBy('booking_order_id')
            ->where('is_deleted',BookingFulgs::IS_NOT_DELETED)
            ->orderBy('id','DESC')
            ->paginate(15);
        foreach($bookingList as &$booking){
            $booking->booking = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->user_id)->first();
            $booking->accepted = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->accepted_user_id)->first();
            $booking->mrf = MxpMrf::where('booking_order_id',$booking->booking_order_id)->groupBy('mrf_id')->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')->select('mxp_mrf_table.user_id','mxp_mrf_table.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
            $booking->ipo = MxpIpo::where('booking_order_id',$booking->booking_order_id)->groupBy('ipo_id')->join('mxp_users as mu','mu.user_id','mxp_ipo.user_id')->select('mxp_ipo.user_id','mxp_ipo.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
            $booking->po = MxpIpo::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_id'))->groupBy('booking_order_id')->first();
            $booking->bookingDetails = MxpBooking::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as po_cat'))->groupBy('item_code')->first();
        }

        return view('maxim.booking_list.booking_list_page',compact('bookingList'));
    }

    public function getbookingListAdvanceSearch_(Request $request){
        $bookingList = DB::table('mxp_bookingbuyer_details');
        $checkValidation = false;

        if($request->buyer_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('buyer_name','like','%'.$request->buyer_name_search.'%');
        }
        if($request->company_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('Company_name','like','%'.$request->company_name_search.'%');
        }
        if($request->attention_search != '')
        {
            $checkValidation = true;
            $bookingList->where('attention_invoice','like','%'.$request->attention_search.'%');
        }
        if($request->from_oder_date_search != '' && $request->to_oder_date_search != '')
        {
            $checkValidation = true;
            if($request->from_oder_date_search == $request->to_oder_date_search)
                $bookingList->whereDate('created_at', $request->from_oder_date_search);
            else
                $bookingList->whereDate('created_at','>=',$request->from_oder_date_search)->whereDate('created_at','<=',$request->to_oder_date_search);
        }

        if($checkValidation)
        {
            $bookings = $bookingList->groupBy('booking_order_id')->orderBy('id','DESC')->get();
            if(isset($bookings) && !empty($bookings)){
                foreach ($bookings as &$booking) {
                    $booking->booking = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->user_id)->first();
                    $booking->accepted = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->accepted_user_id)->first();
                    $booking->mrf = MxpMrf::where('booking_order_id',$booking->booking_order_id)->groupBy('mrf_id')->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')->select('mxp_mrf_table.user_id','mxp_mrf_table.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
                    $booking->ipo = MxpIpo::where('booking_order_id',$booking->booking_order_id)->groupBy('ipo_id')->join('mxp_users as mu','mu.user_id','mxp_ipo.user_id')->select('mxp_ipo.user_id','mxp_ipo.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
                    $booking->po = MxpIpo::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_id'))->groupBy('booking_order_id')->first();
                    $booking->bookingDetails = MxpBooking::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as po_cat'))->groupBy('item_code')->first();
                }
            }
            return $bookings;
        }else{
            return null;
        }
    }

    public function getBookingItemLists($booking_id = null){

        if($booking_id == null)
            return false;
        $bookingList = DB::table('mxp_booking')
            ->where('booking_order_id', $booking_id)
            ->orderBy('id','DESC')
            ->get();
        if(isset($bookingList) && !empty($bookingList)){
            if(isset($bookingList) && !empty($bookingList)){
                foreach ($bookingList as &$booking) {
                    $booking->job_number = $booking->id;
                }
            }
            return $bookingList;
        }else{
            return false;
        }
    }

    public function bookingListReport(){

        $bookingList = DB::table('mxp_bookingbuyer_details')->where([['is_complete', BookingFulgs::IS_COMPLETE],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('booking_order_id')->orderBy('id','DESC')->paginate(150);

        if(isset($bookingList) && !empty($bookingList)){
            foreach ($bookingList as &$booking) {
                $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);

                foreach ($booking->itemLists as &$itemListssvalue) {

                    $itemListssvalue->pi = MxpPi::select(DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where('job_no',$itemListssvalue->id)->groupBy('job_no')->first();   

                    $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();

                    $itemListssvalue->mrf = MxpMrf::select(DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();

                    $itemListssvalue->ipo = MxpIpo::select(DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->first();
                }
            }
        }

        return view('maxim.booking_list.booking_list_report',compact('bookingList'));
    }

    public function bookingFilesDownload(Request $request){

        $fileinfo = BookingFile::get()->where('booking_buyer_id', $request->booking_buyer_id);


        $files = [];
        $oriFiles = [];

        foreach ($fileinfo as $info){
            array_push($files, 'booking_files/'.$info->file_name_server.'.'.$info->file_ext);
            array_push($oriFiles, 'booking_files/'.$info->file_name_original.'.'.$info->file_ext);
        }

        $bbInfo = MxpBookingBuyerDetails::where('id', $request->booking_buyer_id)->first();

        $zipname = $bbInfo->booking_order_id.'.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);

        $i=0;
        foreach ($files as $file) {
            $zip->addFile($file);
            $zip->renameName($file, $oriFiles[$i]);
            $i++;
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));

        readfile($zipname);

        File::delete($zipname);

        return redirect()->back();
    }

    public function showBookingReport(Request $request){
        // $bookingReport = DB::select("call getBookinAndBuyerDeatils('".$request->bid."')");

        $bookingReport = $this->getBookingDetailsValue($request->bid);
        $bookingBuyer = $this->getBookingBuyerDetails($request->bid);
        $companyInfo = DB::table('mxp_header')->where('header_type',11)->get();
        $user = new BookingController();
        $getBookingUserDetails = $user::getUserDetails( $request->bid );
        // $this->print_me($bookingBuyer);

        return view('maxim.orderInput.reportFile',compact('bookingReport','companyInfo','gmtsOrSizeGroup','getBookingUserDetails','bookingBuyer'));
    }

    public function getBookingReportListByBookingId(Request $request){

        $bookingList = DB::table('mxp_bookingbuyer_details')
            ->where('booking_order_id', 'like', '%'.$request->booking_id.'%')
            ->orderBy('id','DESC')
            ->get();
        if(isset($bookingList) && !empty($bookingList)){
            foreach ($bookingList as &$booking) {
                $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);
                foreach ($booking->itemLists as &$itemListssvalue) {

                    $itemListssvalue->pi = MxpPi::select('mxp_pi.*',DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where('job_no',$itemListssvalue->id)->groupBy('job_no')->get();   

                    $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                    $itemListssvalue->ipo = MxpIpo::select('mxp_ipo.*',DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                    $itemListssvalue->mrf = MxpMrf::select('mxp_mrf_table.*',DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();
                }
            }
        }
        return $bookingList;
    }

    public function getBookingListByBookingId(Request $request){

        $bookingList = DB::table('mxp_bookingbuyer_details')
            ->where([['booking_order_id', 'like', '%'.$request->booking_id.'%'],['is_deleted',BookingFulgs::IS_NOT_DELETED]])
            ->orderBy('id','DESC')
            ->get();

        foreach($bookingList as &$booking){
            $booking->booking = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->user_id)->first();
            $booking->accepted = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->accepted_user_id)->first();
            $booking->mrf = MxpMrf::where('booking_order_id',$booking->booking_order_id)->groupBy('mrf_id')->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')->select('mxp_mrf_table.user_id','mxp_mrf_table.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
            $booking->ipo = MxpIpo::where('booking_order_id',$booking->booking_order_id)->groupBy('ipo_id')->join('mxp_users as mu','mu.user_id','mxp_ipo.user_id')->select('mxp_ipo.user_id','mxp_ipo.created_at','mu.first_name','mu.middle_name','mu.last_name')->first();
            $booking->po = MxpIpo::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_id'))->groupBy('booking_order_id')->first();
            $booking->bookingDetails = MxpBooking::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as po_cat'))->groupBy('item_code')->first();
        }
        return $bookingList;
    }

    public function getBookingListBySearch(Request $request){

        $bookingList = DB::table('mxp_bookingbuyer_details');
        $checkValidation = false;

        if($request->buyer_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('buyer_name','like','%'.$request->buyer_name_search.'%');
        }
        if($request->company_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('Company_name','like','%'.$request->company_name_search.'%');
        }
        if($request->attention_search != '')
        {
            $checkValidation = true;
            $bookingList->where('attention_invoice','like','%'.$request->attention_search.'%');
        }
        if($request->from_oder_date_search != '' && $request->to_oder_date_search != '')
        {
            $checkValidation = true;
            if($request->from_oder_date_search == $request->to_oder_date_search)
                $bookingList->whereDate('created_at', $request->from_oder_date_search);
            else
                $bookingList->whereDate('created_at','>=',$request->from_oder_date_search)->whereDate('created_at','<=',$request->to_oder_date_search);
        }

        if($checkValidation)
        {
            $bookings = $bookingList->groupBy('booking_order_id')->orderBy('id','DESC')->get();
            if(isset($bookings) && !empty($bookings)){
                foreach ($bookings as &$booking) {
                    $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);
                    foreach ($booking->itemLists as &$itemListssvalue) {

                        $itemListssvalue->pi = MxpPi::select('mxp_pi.*',DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where('job_no',$itemListssvalue->id)->groupBy('job_no')->get();   

                        $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                        $itemListssvalue->ipo = MxpIpo::select('mxp_ipo.*',DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                        $itemListssvalue->mrf = MxpMrf::select('mxp_mrf_table.*',DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();
                    }
                }
            }
            return $bookings;
        }
        else
            return null;
    }

    public function getBookingListbookSearch(Request $request){
        // return "AAAA";
        $bookingList = DB::table('mxp_bookingbuyer_details');
        $checkValidation = false;

        if($request->buyer_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('buyer_name','like','%'.$request->buyer_name_search.'%');
        }
        if($request->company_name_search != '')
        {
            $checkValidation = true;
            $bookingList->where('Company_name','like','%'.$request->company_name_search.'%');
        }
        if($request->attention_search != '')
        {
            $checkValidation = true;
            $bookingList->where('attention_invoice','like','%'.$request->attention_search.'%');
        }
        if($request->from_oder_date_search != '' && $request->to_oder_date_search != '')
        {
            $checkValidation = true;
            if($request->from_oder_date_search == $request->to_oder_date_search)
                $bookingList->whereDate('created_at', $request->from_oder_date_search);
            else
                $bookingList->whereDate('created_at','>=',$request->from_oder_date_search)->whereDate('created_at','<=',$request->to_oder_date_search);
        }

        if($checkValidation)
        {
            $bookings = $bookingList->groupBy('booking_order_id')->orderBy('id','DESC')->get();
            if(isset($bookings) && !empty($bookings)){
                foreach ($bookings as &$booking) {
                    $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);
                    foreach ($booking->itemLists as &$itemListssvalue) {

                        $itemListssvalue->pi = MxpPi::select('mxp_pi.*',DB::Raw('GROUP_CONCAT(p_id) as p_ids'))->where('job_no',$itemListssvalue->id)->groupBy('job_no')->get();   

                        $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(challan_id) as challan_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                        $itemListssvalue->ipo = MxpIpo::select('mxp_ipo.*',DB::Raw('GROUP_CONCAT(ipo_id) as ipo_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();

                        $itemListssvalue->mrf = MxpMrf::select('mxp_mrf_table.*',DB::Raw('GROUP_CONCAT(mrf_id) as mrf_ids'))->where('job_id',$itemListssvalue->id)->groupBy('job_id')->get();
                    }
                }
            }
            return $bookings;
        }
        else
            return null;
    }

    public function createIpoView(Request $request){

        $ipoValue = DB::table("mxp_booking_challan")->where('booking_order_id', $request->booking_id)->get();

        if (empty($ipoValue)) {
            return \Redirect()->Route('dashboard_view');
        }
        $buyerDetails = DB::table("mxp_bookingbuyer_details")
            ->where('booking_order_id', $request->booking_id)
            ->get();

        $headerValue = DB::table("mxp_header")
            ->where('header_type', 11)
            ->get();

        $ipoListValue = DB::table("mxp_ipo")
            ->select('id','booking_order_id','ipo_id')
            ->where('booking_order_id', $request->booking_id)
            ->get();

        return view('maxim.ipo.ipo_price_manage', [
            'headerValue' => $headerValue,
            'buyerDetails' => $buyerDetails,
            'sentBillId' => $ipoValue,
            'ipoIds' => '0',
            'ipoIncrease' => $request->ipoIncrease,
            'ipoListValue' => $ipoListValue,
        ]);
    }

    public function createMrfView(Request $request){
        // return 'createMrfView and booking id is '.$request->booking_id;

         // $data = $request->all();

            $suppliers = Supplier::where('status', 1)
                                 ->where('is_delete', 0)
                                 ->get();

            $booking_order_id = $request->booking_id;

            $bookingDetails = DB::select("SELECT * FROM mxp_booking_challan WHERE booking_order_id = '".$request->booking_id."' GROUP BY item_code");

            $buyerDetails = DB::select("SELECT * FROM mxp_bookingbuyer_details WHERE booking_order_id = '".$request->booking_id."'");

            if(empty($bookingDetails)){
                StatusMessage::create('empty_booking_data', 'This booking id empty value !');

                return \Redirect()->Route('booking_list_view');
            }

            $MrfDetails = DB::select("select * from mxp_mrf_table where booking_order_id = '".$request->booking_id."' GROUP BY mrf_id");

            return view('maxim.mrf.mrf',compact('bookingDetails','MrfDetails','booking_order_id', 'suppliers'));
    }

    public function detailsViewForm(Request $request)
    {

        $bookingDetails = MxpBookingBuyerDetails::with('bookings', 'ipo', 'mrf')
                            ->leftjoin('mxp_users as mu','mu.user_id','accepted_user_id')
                            ->select('mxp_bookingbuyer_details.*','mu.first_name','mu.last_name')
                            ->where('booking_order_id', $request->booking_id)
                            ->first();

        $bookingDetails->party_id_ = DB::table('mxp_party')->select('id as party_id_')->where('name',$bookingDetails->Company_name)->first();
        return view('maxim.booking_list.booking_View_Details',
                    [
                        'booking_id' => $request->booking_id, 
                        'bookingDetails' => $bookingDetails
                    ]);
    }

    public static function getBookingDetailsValue($booking_order_id){
        $bookingDetails = MxpBooking::where('booking_order_id',$booking_order_id)
                    ->orderBy('id','ASC')
                    ->get();
        return $bookingDetails;
    }

    public static function getBookingBuyerDetails($booking_order_id){
        $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$booking_order_id)
                    ->get();
        return $buyerDetails;
    }

}
