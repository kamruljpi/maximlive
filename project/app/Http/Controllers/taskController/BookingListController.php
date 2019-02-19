<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\BookingController;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\ChallanFlugs;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Source\User\RoleDefine;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\RoleManagement;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Source\source;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Model\MxpMultipleChallan;
use Illuminate\Http\Request;
use App\Model\BookingFile;
use App\Model\MxpBooking;
use App\Model\MxpMrf;
use App\Model\MxpPi;
use Carbon\Carbon;
use App\MxpStore;
use App\Supplier;
use ZipArchive;
use App\MxpIpo;
use Validator;
use App\User;
use Auth;
use DB;

class BookingListController extends Controller
{   
    use UserAccessBuyerList;

    CONST CREATE_IPO = "create";
    CONST UPDATE_IPO = "update";

    public function bookingListView(){

        /** buyer wiase booking value filter **/

        $buyerList = $this->getUserByerList(); // use trait class

        if(isset($buyerList) && !empty($buyerList)){

            $bookingList = MxpBookingBuyerDetails::groupBy('booking_order_id')
                ->where('is_deleted',BookingFulgs::IS_NOT_DELETED)
                ->whereIn('buyer_name',$this->getUserByerNameList()) // use trait class
                ->orderBy('booking_status')
                ->paginate(15);

        }else if(Auth::user()->type == 'super_admin'){

            $bookingList = MxpBookingBuyerDetails::groupBy('booking_order_id')
                ->where('is_deleted',BookingFulgs::IS_NOT_DELETED)
                ->orderBy('booking_status')
                ->paginate(15);

        }else{
            // when condition false
            // return empty paigante object
            $bookingList = MxpBookingBuyerDetails::where('buyer_name','')
                ->orderBy('id','DESC')
                ->paginate(15);

        }

        /** End**/

        if(isset($bookingList) && !empty($bookingList)) {
            foreach($bookingList as &$booking){
                $booking->booking = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->user_id)->first();
                $booking->accepted = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->accepted_user_id)->first();

                $booking->mrf = MxpMrf::where([
                                    ['booking_order_id',$booking->booking_order_id],
                                    ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                                ])
                                ->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')
                                ->select('mxp_mrf_table.user_id','mxp_mrf_table.created_at','mxp_mrf_table.mrf_status','mu.first_name','mu.middle_name','mu.last_name',DB::Raw('GROUP_CONCAT(DISTINCT mxp_mrf_table.mrf_id SEPARATOR ", ") as mrf_id'))
                                ->groupBy('mrf_id')
                                ->first();

                $booking->ipo = MxpIpo::where([
                                    ['booking_order_id',$booking->booking_order_id],
                                    ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                                ])
                                ->groupBy('ipo_id')
                                ->join('mxp_users as mu','mu.user_id','mxp_ipo.user_id')
                                ->select('mxp_ipo.user_id','mxp_ipo.created_at','mxp_ipo.ipo_status','mu.first_name','mu.middle_name','mu.last_name')
                                ->first();

                $booking->po = MxpIpo::where([
                                    ['booking_order_id', $booking->booking_order_id],
                                    ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                                ])
                                ->select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_id'))
                                ->groupBy('booking_order_id')
                                ->first();

                $booking->bookingDetails = MxpBooking::where([['booking_order_id', $booking->booking_order_id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->select(DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as po_cat'),'shipmentDate')->first();
            }
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
                    $booking->bookingDetails = MxpBooking::where('booking_order_id', $booking->booking_order_id)->select(DB::Raw('GROUP_CONCAT(DISTINCT poCatNo SEPARATOR ", ") as po_cat'))->first();
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

        /** buyer wiase booking value filter **/

        $buyerList = $this->getUserByerList(); // use trait class

        if(isset($buyerList) && !empty($buyerList)){

            $bookingList = DB::table('mxp_bookingbuyer_details')
                    ->where([
                        ['is_complete', BookingFulgs::IS_COMPLETE],
                        ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                    ])
                    // useing trait class
                    ->whereIn('buyer_name',$this->getUserByerNameList()) 
                    ->groupBy('booking_order_id')
                    ->orderBy('id','DESC')
                    ->paginate(20);

        }else if(Auth::user()->type == 'super_admin'){

            $bookingList = DB::table('mxp_bookingbuyer_details')
                        ->where([
                            ['is_complete', BookingFulgs::IS_COMPLETE],
                            ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                        ])
                        ->groupBy('booking_order_id')
                        ->orderBy('id','DESC')
                        ->paginate(20);
        }else{
            // when condition false
            // return empty paginate object
            $bookingList = DB::table('mxp_bookingbuyer_details')
                        ->where('buyer_name', '')
                        ->groupBy('booking_order_id')
                        ->orderBy('id','DESC')
                        ->paginate(20);
        }

        /** End**/

        if(isset($bookingList) && !empty($bookingList)){
            foreach ($bookingList as &$booking) {
                $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);

                foreach ($booking->itemLists as &$itemListssvalue) {

                    $itemListssvalue->pi = MxpPi::select(DB::Raw('GROUP_CONCAT(DISTINCT p_id SEPARATOR ", ") as p_ids'))->where([['job_no',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_no')->first();   

                    $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(DISTINCT challan_id SEPARATOR ", ") as challan_ids'))->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                    $itemListssvalue->mrf = MxpMrf::select(DB::Raw('GROUP_CONCAT(DISTINCT mrf_id SEPARATOR ", ") as mrf_ids'),'mrf_status','job_id_current_status')->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                    $itemListssvalue->ipo = MxpIpo::select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_ids'),'ipo_status')->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();
                }
            }
        }

        return view('maxim.booking_list.booking_list_report',compact('bookingList'));
    }

    /**
     * @return booking tracking advance search view page
     */

    public function getAdvanceSearchBookingList (Request $request) {

        $inputArray = [
            'booking_id' => isset($request->booking_id) ? $request->booking_id : '',
            'buyer_name' => isset($request->buyer_name_search) ? $request->buyer_name_search : '',
            'attention' => isset($request->attention_search) ? $request->attention_search : '',
            'company_name' => isset($request->company_name_search) ? $request->company_name_search : '',
            'from_oder_date' => isset($request->from_oder_date_search) ? $request->from_oder_date_search : '',
            'to_oder_date' => isset($request->to_oder_date_search) ? $request->to_oder_date_search : '',
            'from_shipment_date' => isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '',
            'to_shipment_date' => isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : '',
            'po_cat_no' => isset($request->po_cat_no) ? $request->po_cat_no : ''
        ];

        $bookingList = $this->filterBookingAdvanceSearch ($request);

        return view('maxim.booking_list.booking_list_report',compact('bookingList','inputArray'));
    }

    /**
     * @return planning tracking advance search view page
     */

    public function getAdvanceSearchPlanningList (Request $request) {

        $inputArray = [
            'booking_id' => isset($request->booking_id) ? $request->booking_id : '',
            'buyer_name' => isset($request->buyer_name_search) ? $request->buyer_name_search : '',
            'attention' => isset($request->attention_search) ? $request->attention_search : '',
            'company_name' => isset($request->company_name_search) ? $request->company_name_search : '',
            'from_oder_date' => isset($request->from_oder_date_search) ? $request->from_oder_date_search : '',
            'to_oder_date' => isset($request->to_oder_date_search) ? $request->to_oder_date_search : '',
            'from_shipment_date' => isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '',
            'to_shipment_date' => isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : '',
            'po_cat_no' => isset($request->po_cat_no) ? $request->po_cat_no : ''
        ];

        $bookingList = $this->filterBookingAdvanceSearch ($request);

        return view('maxim.booking_list.planning_tracking_report',compact('bookingList','inputArray'));
    }

    /** 
     * getbookingListAdvanceSearch_() copy this method 
     * and add some condition and changes
     * @return array
     * 
     */

    public function filterBookingAdvanceSearch (Request $request) {

        $booking_id = isset($request->booking_id) ? $request->booking_id : '';
        $buyer_name = isset($request->buyer_name_search) ? $request->buyer_name_search : '';
        $attention = isset($request->attention_search) ? $request->attention_search : '';
        $company_name = isset($request->company_name_search) ? $request->company_name_search : '';
        $from_oder_date = isset($request->from_oder_date_search) ? $request->from_oder_date_search : '';
        $to_oder_date = isset($request->to_oder_date_search) ? $request->to_oder_date_search : '';
        $from_shipment_date = isset($request->from_shipment_date_search) ? $request->from_shipment_date_search : '';
        $to_shipment_date = isset($request->to_shipment_date_search) ? $request->to_shipment_date_search : '';
        $po_cat_no = isset($request->po_cat_no) ? $request->po_cat_no : '';

        // if(empty($booking_id) && empty($buyer_name) && empty($attention)
        // && empty($company_name) && empty($from_oder_date) && empty($to_oder_date)
        // && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)){

        //     StatusMessage::create('messages', 'Please select a option');
        //     return \Redirect()->Route('booking_list_report');
        // }


        /** buyer wiase booking value filter **/

        $buyerList = $this->getUserByerList(); // use trait class

        if(isset($buyerList) && !empty($buyerList)){

            $bookingLists = DB::table('mxp_bookingbuyer_details')
                    ->where([
                        ['mxp_bookingbuyer_details.is_complete', BookingFulgs::IS_COMPLETE],
                        ['mxp_bookingbuyer_details.is_deleted',BookingFulgs::IS_NOT_DELETED]
                    ])
                    ->whereIn('buyer_name',$this->getUserByerNameList())
                    ->orderBy('mxp_bookingbuyer_details.id','DESC');

        }else if(Auth::user()->type == 'super_admin'){

            $bookingLists = DB::table('mxp_bookingbuyer_details')
                    ->where([
                        ['mxp_bookingbuyer_details.is_complete', BookingFulgs::IS_COMPLETE],
                        ['mxp_bookingbuyer_details.is_deleted',BookingFulgs::IS_NOT_DELETED]
                    ])
                    ->orderBy('mxp_bookingbuyer_details.id','DESC');

        }else{
            // when condition false
            // return empty paginate object
            $bookingLists = DB::table('mxp_bookingbuyer_details')
                    ->where('buyer_name', '')
                    ->orderBy('mxp_bookingbuyer_details.id','DESC');

        }

        /** end **/

        $checkValidation = false;

        /* only booking_id input value*/
        if (!empty($booking_id) && empty($buyer_name) && empty($company_name) && empty($attention) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->where('booking_order_id','like','%'.$booking_id.'%')
                ->paginate(20)
                ->setPath('list?booking_id='.$booking_id);

        /* only buyer_name_search input value*/
        } else if ($request->buyer_name_search != '' && empty($company_name) && empty($attention) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->where('buyer_name','like','%'.$request->buyer_name_search.'%')
                ->paginate(20)
                ->setPath('list?buyer_name_search='.$request->buyer_name_search);

        /* only company_name_search input value */
        } else if($request->company_name_search != '' && empty($buyer_name) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->where('Company_name','like','%'.$request->company_name_search.'%')
                ->paginate(20)
                ->setPath('list?company_name_search='.$request->company_name_search);

        /* only po_cat_no input value */
        } else if(!empty($po_cat_no) && empty($buyer_name) && empty($company_name) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($attention)) {

            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                ->where('mb.poCatNo',$po_cat_no)
                ->orderBy('mb.id','ASC')
                ->paginate(20)
                ->setPath('list?po_cat_no='.$po_cat_no);

        /* only attention_search input value */
        } else if($request->attention_search != '' && empty($buyer_name) && empty($company_name) && empty($from_oder_date) && empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->where('attention_invoice','like','%'.$request->attention_search.'%')
                ->paginate(20)
                ->setPath('list?attention_search='.$request->attention_search);

        /* only from_oder_date_search and to_oder_date_search input value */
        } else if ($request->from_oder_date_search != '' && $request->to_oder_date_search != '' && empty($buyer_name) && empty($company_name) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            if ($request->from_oder_date_search == $request->to_oder_date_search) {
                $bookingList = $bookingLists->whereDate('created_at', $request->from_oder_date_search)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$request->from_oder_date_search.'&to_oder_date_search='.$request->to_oder_date_search);
            } else {

                $bookingList = $bookingLists->whereDate('created_at','>=',$request->from_oder_date_search)->whereDate('created_at','<=',$request->to_oder_date_search)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$request->from_oder_date_search.'&to_oder_date_search='.$request->to_oder_date_search);
            }

        /* only buyer_name and company_name input value */
        } else if (!empty($buyer_name) && !empty($company_name) && empty($from_shipment_date) && empty($to_shipment_date) && empty($from_oder_date) && empty($to_oder_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->where('buyer_name','like','%'.$buyer_name.'%')
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&company_name_search='.$company_name);

        /* only from_shipment_date and from_shipment_date input value */
        } else if (!empty($from_shipment_date) && !empty($to_shipment_date) && empty($buyer_name) && empty($company_name) && empty($from_oder_date) && empty($to_oder_date) && empty($po_cat_no)) {

            $checkValidation = true;

            if ($from_shipment_date == $to_shipment_date) {

                $bookingList = $bookingLists->whereDate('shipmentDate', $from_shipment_date)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);

            } else {

                $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->paginate(20)
                    ->setPath('list?from_oder_date_search='.$from_shipment_date.'&to_oder_date_search='.$to_shipment_date);
            }

        /* only buyer_name, from_oder_date, and to_oder_date input value */
        } else if (!empty($buyer_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($company_name) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('created_at','>=',$from_oder_date)
                    ->whereDate('created_at','<=',$to_oder_date)
                    ->where('buyer_name','like','%'.$buyer_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only po_cat_no, and buyer_name input value */
        } else if (!empty($buyer_name) && empty($company_name) && !empty($po_cat_no) && empty($from_oder_date) && empty($to_oder_date) && empty($attention) && empty($from_shipment_date) && empty($to_shipment_date)) {
            // $this->print_me("sss");
            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.buyer_name','like','%'.$buyer_name.'%')
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'buyer_name_search='.$buyer_name);

        /* only company_name, and po_cat_no input value */
        } else if (empty($buyer_name) && !empty($company_name) && !empty($po_cat_no) && empty($from_oder_date) && empty($to_oder_date) && empty($attention) && empty($from_shipment_date) && empty($to_shipment_date)) {
            
            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'company_name_search='.$company_name);

        /* only buyer_name, company_name, and po_cat_no input value */
        } else if (!empty($buyer_name) && !empty($company_name) && !empty($po_cat_no) && empty($from_oder_date) && empty($to_oder_date) && empty($attention) && empty($from_shipment_date) && empty($to_shipment_date)) {
            
            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.buyer_name','like','%'.$buyer_name.'%')
                    ->where('mxp_bookingbuyer_details.Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'buyer_name_search='.$buyer_name
                            .'company_name_search='.$company_name);

        /* only po_cat_no, from_oder_date, and to_oder_date input value */
        } else if (empty($buyer_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($company_name) && !empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mxp_bookingbuyer_details.created_at','>=',$from_oder_date)
                    ->whereDate('mxp_bookingbuyer_details.created_at','<=',$to_oder_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only company_name, from_oder_date, and to_oder_date input value */
        } else if (empty($buyer_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && !empty($company_name) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('created_at','>=',$from_oder_date)
                    ->whereDate('created_at','<=',$to_oder_date)
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?company_name_search='.$company_name
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only buyer_name, company_name, from_oder_date, and to_oder_date input value */
        } else if (!empty($buyer_name) && !empty($company_name) && !empty($from_oder_date) && !empty($to_oder_date) && empty($from_shipment_date) && empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('created_at','>=',$from_oder_date)
                    ->whereDate('created_at','<=',$to_oder_date)
                    ->where('buyer_name','like','%'.$buyer_name.'%')
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&company_name_search='.$company_name
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only po_cat_no, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && empty($company_name) && !empty($po_cat_no) && empty($from_oder_date) && empty($to_oder_date) && empty($attention) && !empty($from_shipment_date) && !empty($to_shipment_date)) {
            
            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mb.shipmentDate','>=',$from_shipment_date)
                    ->whereDate('mb.shipmentDate','<=',$to_shipment_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date);
        /* only po_cat_no, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && empty($company_name) && !empty($po_cat_no) && !empty($from_oder_date) && !empty($to_oder_date) && empty($attention) && !empty($from_shipment_date) && !empty($to_shipment_date)) {
            // $this->print_me("sss");
            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mb.created_at','>=',$from_oder_date)
                    ->whereDate('mb.created_at','<=',$to_oder_date)
                    ->whereDate('mb.shipmentDate','>=',$from_shipment_date)
                    ->whereDate('mb.shipmentDate','<=',$to_shipment_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->paginate(20)
                    ->setPath('list?po_cat_no='.$po_cat_no
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only buyer_name, from_shipment_date, and to_shipment_date input value */
        } else if (!empty($buyer_name) && empty($company_name) && empty($from_oder_date) && empty($to_oder_date) && empty($attention) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->where('buyer_name','like','%'.$buyer_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&company_name_search='.$company_name
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date);

        /* only company_name, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && !empty($company_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($from_oder_date) && empty($to_oder_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date);

        /* only buyer_name, company_name, from_shipment_date, and to_shipment_date input value */
        } else if (!empty($buyer_name) && !empty($company_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->where('buyer_name','like','%'.$buyer_name.'%')
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&company_name_search='.$company_name
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date);

        /* only from_oder_date, to_oder_date, from_shipment_date, and to_shipment_date input value */
        } else if (empty($buyer_name) && empty($company_name) && !empty($from_oder_date) && !empty($to_oder_date) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->whereDate('created_at','>=',$from_oder_date)
                    ->whereDate('created_at','<=',$to_oder_date)
                    ->paginate(20)
                    ->setPath('list?from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* only buyer_name, company_name, from_shipment_date, to_shipment_date, from_oder_date, and to_oder_date input field value */
        } else if (!empty($buyer_name) && !empty($company_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date) && empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->whereDate('shipmentDate','>=',$from_shipment_date)
                    ->whereDate('shipmentDate','<=',$to_shipment_date)
                    ->whereDate('created_at','>=',$from_oder_date)
                    ->whereDate('created_at','<=',$to_oder_date)
                    ->where('buyer_name','like','%'.$buyer_name.'%')
                    ->where('Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&company_name_search='.$company_name
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* all input field value */
        } else if (!empty($buyer_name) && empty($company_name) && empty($from_shipment_date) && empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date) && !empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mb.created_at','>=',$from_oder_date)
                    ->whereDate('mb.created_at','<=',$to_oder_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.buyer_name','like','%'.$buyer_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&po_cat_no='.$po_cat_no
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);

        /* all input field value */
        } else if (!empty($buyer_name) && empty($company_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && empty($from_oder_date) && empty($to_oder_date) && !empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mb.shipmentDate','>=',$from_shipment_date)
                    ->whereDate('mb.shipmentDate','<=',$to_shipment_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.buyer_name','like','%'.$buyer_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&po_cat_no='.$po_cat_no
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date);

        /* all input field value */
        } else if (!empty($buyer_name) && !empty($company_name) && !empty($from_shipment_date) && !empty($to_shipment_date) && !empty($from_oder_date) && !empty($to_oder_date) && !empty($po_cat_no)) {

            $checkValidation = true;

            $bookingList = $bookingLists->join('mxp_booking as mb','mb.booking_order_id','mxp_bookingbuyer_details.booking_order_id')
                    ->whereDate('mb.shipmentDate','>=',$from_shipment_date)
                    ->whereDate('mb.shipmentDate','<=',$to_shipment_date)
                    ->whereDate('mb.created_at','>=',$from_oder_date)
                    ->whereDate('mb.created_at','<=',$to_oder_date)
                    ->where('mb.poCatNo',$po_cat_no)
                    ->where('mxp_bookingbuyer_details.buyer_name','like','%'.$buyer_name.'%')
                    ->where('mxp_bookingbuyer_details.Company_name','like','%'.$company_name.'%')
                    ->paginate(20)
                    ->setPath('list?buyer_name_search='.$buyer_name
                            .'&po_cat_no='.$po_cat_no
                            .'&company_name_search='.$company_name
                            .'&from_shipment_date_search='.$from_shipment_date
                            .'&to_shipment_date_search='.$to_shipment_date
                            .'&from_oder_date_search='.$from_oder_date
                            .'&to_oder_date_search='.$to_oder_date);
        }

        if ($checkValidation) {

            if (isset($bookingList) && !empty($bookingList)) {
                foreach ($bookingList as &$booking) {
                    $booking->itemLists = $this->getBookingItemLists($booking->booking_order_id);
                    foreach ($booking->itemLists as &$itemListssvalue) {

                        $itemListssvalue->pi = MxpPi::select(DB::Raw('GROUP_CONCAT(DISTINCT p_id SEPARATOR ", ") as p_ids'))->where([['job_no',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_no')->first();   

                        $itemListssvalue->challan = MxpMultipleChallan::select('mxp_multiplechallan.*',DB::Raw('GROUP_CONCAT(DISTINCT challan_id SEPARATOR ", ") as challan_ids'))->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                        $itemListssvalue->mrf = MxpMrf::select(DB::Raw('GROUP_CONCAT(DISTINCT mrf_id SEPARATOR ", ") as mrf_ids'),'mrf_status','job_id_current_status')->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();

                        $itemListssvalue->ipo = MxpIpo::select(DB::Raw('GROUP_CONCAT(DISTINCT ipo_id SEPARATOR ", ") as ipo_ids'),'ipo_status')->where([['job_id',$itemListssvalue->id],['is_deleted',BookingFulgs::IS_NOT_DELETED]])->groupBy('job_id')->first();
                    }
                }
            }

            return $bookingList ;
        } else {

            $bookingList = DB::table('mxp_bookingbuyer_details')->where('id','')->paginate(15);

            return $bookingList;
        }
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

    public function showBookingReport(Request $request) {
        
        $bookingReport = $this->getBookingDetailsValue($request->bid);
        $bookingBuyer = $this->getBookingBuyerDetails($request->bid);
        $companyInfo = DB::table('mxp_header')->where('header_type',HeaderType::COMPANY)->get();
        $user = new BookingController();
        $getBookingUserDetails = $user::getUserDetails( $request->bid );

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

        $role_define = new RoleDefine();

        // cs role define
        $bookingList['user_role_type'] = $role_define->getRole('Customer');

        if($bookingList['user_role_type'] == 'empty') {
            //super admin role define
            $bookingList['user_role_type'] = Auth::user()->type;
        }        

        $bookingList['details'] = DB::table('mxp_bookingbuyer_details')
            ->where([['booking_order_id', 'like', '%'.$request->booking_id.'%'],['is_deleted',BookingFulgs::IS_NOT_DELETED]])
            ->orderBy('id','DESC')
            ->get();

        foreach($bookingList['details'] as &$booking){
            $booking->booking = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->user_id)->first();
            $booking->accepted = User::select('user_id','first_name','middle_name','last_name')->where('user_id',$booking->accepted_user_id)->first();
            $booking->mrf = MxpMrf::where('booking_order_id',$booking->booking_order_id)->groupBy('mrf_id')->join('mxp_users as mu','mu.user_id','mxp_mrf_table.user_id')->select('mxp_mrf_table.user_id','mxp_mrf_table.created_at','mu.first_name','mu.middle_name','mu.last_name',DB::Raw('GROUP_CONCAT(DISTINCT mxp_mrf_table.mrf_id SEPARATOR ", ") as mrf_id'))->first();
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

        $count = 0;
        foreach ($bookingDetails->bookings_challan_table as $value){
            if ( !empty($value->ipo_quantity) || !empty($value->mrf_quantity)){
                $count++;
            }
        }
        $leftBook = count($bookingDetails->bookings_challan_table) - $count;
        $booking_objects = new source();
        $bookingDetails->prepared_by = $booking_objects->getUserDetails($request->booking_id);


        NotificationController::updateSeenStatus($type_id = $request->booking_id, Auth::user()->user_id);

        /** get stock quantity by booking **/
        $stock_booking = $this->getBookingStock($request->booking_id);
        /** End **/

        return view('maxim.booking_list.booking_View_Details',
                    [
                        'booking_id' => $request->booking_id, 
                        'bookingDetails' => $bookingDetails,
                        'leftBooking' => $leftBook,
                        'stock_booking' => $stock_booking
                    ]);
    }

    public static function getBookingDetailsValue($booking_order_id){
        $bookingDetails = MxpBooking::where([['is_deleted',BookingFulgs::IS_NOT_DELETED],['booking_order_id',$booking_order_id]])
                    ->orderBy('id','ASC')
                    ->get();
        return $bookingDetails;
    }

    public static function getBookingBuyerDetails($booking_order_id){
        $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$booking_order_id)
                    ->get();
        return $buyerDetails;
    }

    public function getBookingStock($booking_id){

        $booking_stock = [];

        if(isset($booking_id) && !empty($booking_id)) {
            $booking_stock = MxpStore::join('mxp_booking as mb','mb.id','mxp_store.job_id')
                            ->where('mxp_store.booking_order_id', $booking_id)
                            ->select('mxp_store.*',DB::Raw('SUM(mxp_store.item_quantity) as store_quantity'),'mb.item_quantity as booking_quantity')
                            ->groupBy('mxp_store.job_id')
                            ->get();
        }

        $this->addchallanQuantity($booking_stock);

        return $booking_stock;
    }

    public function makeChallan(Request $request){

        $bookingDetails = MxpStore::join('mxp_booking as mb','mb.id','mxp_store.job_id')
                        ->whereIn('mxp_store.job_id', $request->job_id)
                        ->select('mxp_store.*',DB::Raw('SUM(mxp_store.item_quantity) as store_quantity'),'mb.item_quantity as booking_quantity')
                        ->groupBy('mxp_store.job_id')
                        ->get();

        $this->addchallanQuantity($bookingDetails);
        
        return view('maxim.challan.challan', compact('bookingDetails'));
    }

    /**
     * return void()
     */
    public function addchallanQuantity($bookingDetails){
        if(isset($bookingDetails) && !empty($bookingDetails)) {
            foreach ($bookingDetails as &$challan) {
                $challan->delivery_challan_quantity = (DB::table('mxp_multiplechallan')
                ->select(DB::Raw('SUM(quantity) as delivery_challan_quantity'))
                ->groupBy('job_id')
                ->where('status',ChallanFlugs::CHALLAN_REQUEST_SENT)
                ->first())->delivery_challan_quantity;

                $challan->available_challan_quantity = $challan->store_quantity - $challan->delivery_challan_quantity ;

            }
        }
    }


    public function changeBookingStatus(Request $request) {

        $bid = isset($request->bid) ? $request->bid : '' ;
        $change_status = isset($request->change_status) ? $request->change_status : '' ;

        if(!empty($bid)) {
            if($change_status == BookingFulgs::BOOKED_FLUG) {
                MxpBookingBuyerDetails::where('booking_order_id',$bid)
                    ->update([
                        'booking_status' => BookingFulgs::BOOKED_FLUG,
                    ]);
            }else if($change_status == BookingFulgs::ON_HOLD_FLUG) {
                MxpBookingBuyerDetails::where('booking_order_id',$bid)
                    ->update([
                        'booking_status' => BookingFulgs::ON_HOLD_FLUG,
                    ]);
            }                    
        }        
        
        return Redirect()->Back();
        $this->print_me($request->all());
    }
}
