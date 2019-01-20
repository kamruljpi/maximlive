<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleManagement;
use App\Model\BookingFile;
use App\Model\MxpPi;
use App\MxpProduct;
use Illuminate\Http\Request;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpBookingChallan;
use App\Model\MxpBooking;
use App\VendorPrice;
use App\MxpItemsQntyByBookingChallan;
use Validator;
use Auth;
use DB;
use App\User;
use App\Notification;
use App\Model\MxpMrf;
use App\MxpIpo;
use App\Http\Controllers\taskController\BookingListController;
use App\userbuyer;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Http\Controllers\Message\ActionMessage;
use Redirect;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Model\MxpItemDescription;
use Carbon;
use Session;
use App\Model\Os\MxpOsPo;
use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
use App\MxpDraft;

class BookingController extends Controller
{ 
    use UserAccessBuyerList;

    public function orderInputDetails(Request $request){
      // return json_encode(DB::select('Call getProductSizeQuantityWithConcat("'.$request->item.'")'));

      return json_encode($this->getItemDetails($request->item));
    }

    public function getVendorPrice(Request $request){
        $price  = VendorPrice::where('product_id',$request->productId)
            ->Where('party_table_id', $request->company_id)
            ->orderBy('price_id', 'DESC')
            ->first();

        if (count($price) > 0)
            return $price;
        else
            return new MxpBooking();
    }

    public function getordercode()
    {
      $results = array();
      $orderDetails = MxpBookingBuyerDetails::where('is_deleted',BookingFulgs::IS_NOT_DELETED)->select('booking_order_id')->groupBy('booking_order_id')->orderBy('id',DESC)->get();
      if(isset($orderDetails) && !empty($orderDetails)){
          foreach ($orderDetails as $orderKey => $orderValue) {
              
              $results[]['name'] = $orderValue->booking_order_id;
          }
      }
      print json_encode($results);
    }
    
    public function getUserDetails( $bookingId ){
        $getBookingUserDetails = DB::table('mxp_booking as mb')
            ->join('mxp_users as ms','mb.user_id','=','ms.user_id')
            ->select('ms.first_name','ms.middle_name','ms.last_name')
            ->where('mb.booking_order_id',$bookingId)
            ->first();
        return $getBookingUserDetails;
    }

    public function addBooking(Request $request,BookingListController $BookingListController){

      $order_submit = isset($request->order_submit) ? $request->order_submit : '';
      $booking_number = isset($request['booking_number']) ? $request['booking_number'] : '' ;

      $roleManage = new RoleManagement();

      $validMessages = [
            'item_code.required' => 'Brand Name field is required.',
            'poCatNo.required' => 'Po/Cat number field is required',
            'item_size.required' => 'Item Size field is required',
            'style.required' => 'Style field is required',
            'sku.required' => 'Sku field is required',
            'item_qty.required' => 'Item Quantity field is required',
            'item_price.required' => 'Item Price field is required'
            ];
      $datas = $request->all();

      $validator = Validator::make($datas, 
            [
          'item_code' => 'required',
          'poCatNo' => 'required',
          'item_size' => 'required',
          'style' => 'required',
          'sku' => 'required',
          'item_qty' => 'required',
          'item_price' => 'required'
        ],
            $validMessages
        );

      if ($validator->fails()) {
        return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
      }

      $validationError = $validator->messages();
      $companySortName = '';
      $buyerDetails = json_decode($request['buyerDetails']);
      
      foreach ($buyerDetails as $getSortCname) {
          $companySortName = $getSortCname->sort_name;
      }

      /**
       * Generate Boooking number
       */
      $cc_1 = MxpBookingBuyerDetails::count();
      $cc_2 = MxpDraft::select('booking_order_id')->groupBy('booking_order_id')->get();
      $cc_3 = count($cc_2);
      $cc = $cc_1 + $cc_3;
      $count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
      $id = "BK"."-";
      $date = date('dmY') ;
      $customid = $id.$date."-".$companySortName."-".$count;

      /** it's check draft booking id and remove draft value from draft table
       * and store booking in booking main table
       */
      if($booking_number) {

        $customid = $booking_number ? $booking_number : $customid ;
        $delete = MxpDraft::where('booking_order_id',$customid)->delete();

      }

      /** end  **/      

      /**
       * @return Draft store controller 
       */
      if ($order_submit == BookingFulgs::ORDER_SAVE) {
        return (new DraftBooking())->storeOrderDraft($request,$customid);
      }

      foreach ($buyerDetails as $buyers) {
        $InserBuyerDetails = new MxpBookingBuyerDetails();
        $InserBuyerDetails->user_id = Auth::user()->user_id;
        $InserBuyerDetails->booking_order_id      = $customid; //'booking-abc-002';
        $InserBuyerDetails->Company_name          = $buyers->name;
        $InserBuyerDetails->C_sort_name           = $buyers->sort_name;
        $InserBuyerDetails->buyer_name            = $buyers->name_buyer;
        $InserBuyerDetails->address_part1_invoice = $buyers->address_part1_invoice;
        $InserBuyerDetails->address_part2_invoice = $buyers->address_part2_invoice;
        $InserBuyerDetails->attention_invoice     = $buyers->attention_invoice;
        $InserBuyerDetails->mobile_invoice        = $buyers->mobile_invoice;
        $InserBuyerDetails->telephone_invoice     = $buyers->telephone_invoice;
        $InserBuyerDetails->fax_invoice           = $buyers->fax_invoice;
        $InserBuyerDetails->address_part1_delivery = $buyers->address_part1_delivery;
        $InserBuyerDetails->address_part2_delivery = $buyers->address_part2_delivery;
        $InserBuyerDetails->attention_delivery    = $buyers->attention_delivery;
        $InserBuyerDetails->mobile_delivery       = $buyers->mobile_delivery;
        $InserBuyerDetails->telephone_delivery    = $buyers->telephone_delivery;
        $InserBuyerDetails->fax_delivery          = $buyers->fax_delivery;
        $InserBuyerDetails->shipmentDate          = $request->shipmentDate;
        $InserBuyerDetails->booking_status        = BookingFulgs::BOOKED_FLUG;
        $InserBuyerDetails->last_action_at        = BookingFulgs::LAST_ACTION_CREATE;
        $InserBuyerDetails->is_complete           = BookingFulgs::IS_COMPLETE;
        $InserBuyerDetails->booking_category      = $request->booking_category;
        $InserBuyerDetails->save();
        $buyerId = $InserBuyerDetails->id;
      }

        $this->uploadBookingFiles($request, $buyerId);

        $data = $request->all();
        $item_description = (isset($data['item_description'])) ? $data['item_description'] : '';
        $item_gmts_color = (isset($data['item_gmts_color'])) ? $data['item_gmts_color'] : '';
        $others_color = (isset($data['others_color'])) ? $data['others_color'] : '';
        $oos_number = (isset($data['oos_number'])) ? $data['oos_number'] : '';
        $item_price = $data['item_price'];
        $item_size = (isset($data['item_size'])) ? $data['item_size'] : '';
        $item_code = $data['item_code'];
        $item_qty = $data['item_qty'];
        $poCatNo = (isset($data['poCatNo'])) ? $data['poCatNo'] : '';
        $style = (isset($data['style'])) ? $data['style'] : '';
        $erp = (isset($data['erp'])) ? $data['erp'] : '';
        $sku = $data['sku'];



      for ($i=0; $i < count($item_code); $i++) {

        $item_details = MxpProduct::where('product_code',$item_code[$i])->get();
        
        $str_qty = str_replace(',', '', $item_qty[$i]);
        $str_item_qty = trim($str_qty, ',');

        $str_price = str_replace('$', '', $item_price[$i]);
        $str_item_price = trim($str_price, '$');

        $insertBooking = new MxpBooking();
        $insertBooking->user_id           = Auth::user()->user_id;
        $insertBooking->booking_order_id  = $customid ;//'booking-abc-001';
        $insertBooking->erp_code          = $erp[$i];
        $insertBooking->item_code         = $item_code[$i];
        $insertBooking->sku               = $sku[$i];
        $insertBooking->gmts_color        = $item_gmts_color[$i];//(!empty($item_gmts_color[$i]) ? $item_gmts_color[$i] : '');
        $insertBooking->others_color      = (!empty($others_color[$i]) ? $others_color[$i] : '');
        $insertBooking->item_description  = (!empty($item_description[$i]) ? $item_description[$i] : '');
        $insertBooking->oos_number        = (!empty($oos_number[$i]) ? $oos_number[$i] : '');
        $insertBooking->poCatNo           = (!empty($poCatNo[$i]) ? $poCatNo[$i] : '');
        $insertBooking->style             = (!empty($style[$i]) ? $style[$i] : '');
        $insertBooking->item_size         = (!empty($item_size[$i]) ? $item_size[$i] : '');
        $insertBooking->item_quantity     = $str_item_qty;
        $insertBooking->item_price        = $str_item_price;
        $insertBooking->orderDate         = $request->orderDate;
        $insertBooking->orderNo           = $request->orderNo;
        $insertBooking->shipmentDate      = $request->shipmentDate;
        $insertBooking->season_code       = $request->season_code;
        $insertBooking->item_size_width_height = $item_details[0]->item_size_width_height;
        $insertBooking->is_type           = $request->is_type;
        $insertBooking->is_pi_type        = BookingFulgs::IS_PI_UNSTAGE_TYPE;
        $insertBooking->last_action_at    = BookingFulgs::LAST_ACTION_CREATE;
        $insertBooking->save();
        $booking_id = $insertBooking->id;

        $insertBookingChallan = new MxpBookingChallan();
        $insertBookingChallan->job_id           = $booking_id;
        $insertBookingChallan->user_id           = Auth::user()->user_id;
        $insertBookingChallan->booking_order_id  = $customid ;//'booking-abc-001';
        $insertBookingChallan->erp_code          = $erp[$i];
        $insertBookingChallan->item_code         = $item_code[$i];
        $insertBookingChallan->sku               = $sku[$i];
        $insertBookingChallan->gmts_color        = $item_gmts_color[$i];
        $insertBookingChallan->others_color      = (!empty($others_color[$i]) ? $others_color[$i] : '');
        $insertBookingChallan->item_description  = (!empty($item_description[$i]) ? $item_description[$i] : '');
        $insertBookingChallan->oos_number        = (!empty($oos_number[$i]) ? $oos_number[$i] : '');
        $insertBookingChallan->poCatNo           = (!empty($poCatNo[$i]) ? $poCatNo[$i] : '');
        $insertBookingChallan->style             = (!empty($style[$i]) ? $style[$i] : '');
        $insertBookingChallan->item_size         = (!empty($item_size[$i]) ? $item_size[$i] : '');
        $insertBookingChallan->item_quantity     = $str_item_qty;
        $insertBookingChallan->left_mrf_ipo_quantity     = $str_item_qty;
        $insertBookingChallan->item_price        = $str_item_price;
        $insertBookingChallan->orderDate         = $request->orderDate;
        $insertBookingChallan->orderNo           = $request->orderNo;
        $insertBookingChallan->shipmentDate      = $request->shipmentDate;
        $insertBookingChallan->season_code       = $request->season_code;
        $insertBookingChallan->last_action_at    = BookingFulgs::LAST_ACTION_CREATE;
        $insertBookingChallan->item_size_width_height       = $item_details[0]->item_size_width_height;
        $insertBookingChallan->save();
        $bookingChallanId = $insertBookingChallan->id;

        $itemQntyByChalan = new MxpItemsQntyByBookingChallan();
        $itemQntyByChalan->booking_challan_id = $bookingChallanId;
        $itemQntyByChalan->booking_order_id = $insertBookingChallan->booking_order_id;
        $itemQntyByChalan->item_code = $insertBookingChallan->item_code;
        $itemQntyByChalan->erp_code = $insertBookingChallan->erp_code;
        $itemQntyByChalan->item_size = (!empty($item_size[$i]) ? $item_size[$i] : '');
        $itemQntyByChalan->item_quantity = $str_item_qty;
        $itemQntyByChalan->gmts_color = $item_gmts_color[$i];
        $itemQntyByChalan->save();

      }

      $is_type = $request->is_type;

      $not_type = NotificationController::postNotification($type=Notification::CREATE_BOOKING, $customid);

      return \Redirect::route('refresh_booking_view', ['booking_id' => $customid,'is_type' => $request->is_type]);
    }

    public function redirectBookingReport(Request $request,BookingListController $BookingListController){
      $is_type = $request->is_type;
      $companyInfo = DB::table('mxp_header')->where('header_type',HeaderType::COMPANY)->get();
      $bookingReport = $BookingListController->getBookingDetailsValue($request->booking_id);
      $bookingBuyer = $BookingListController->getBookingBuyerDetails($request->booking_id);
      $footerData = DB::select("select * from mxp_reportfooter");
      $getBookingUserDetails =  $this->getUserDetails($request->booking_id);

      return view('maxim.orderInput.reportFile',compact('bookingReport','companyInfo','footerData','is_type','getBookingUserDetails','bookingBuyer'));
    }

    public function uploadBookingFiles(Request $request, $buyerId)
    {
      $i = 9999;
      foreach ($_FILES["booking_files"]["tmp_name"] as $key => $tmp_name){
        $file_name_server =  date("dYimsH").$i.$buyerId.rand(100,999);
        $file_name= $_FILES["booking_files"]["name"][$key];
        $file_tmp =$_FILES["booking_files"]["tmp_name"][$key];
        $ext = pathinfo($file_name,PATHINFO_EXTENSION);
        $file_name_original = str_replace('.'.$ext, '', $file_name);

        if(move_uploaded_file($file_tmp=$_FILES["booking_files"]["tmp_name"][$key],"booking_files/".$file_name_server.'.'.$ext)){

            $saveData = new BookingFile();
            $saveData->booking_buyer_id = $buyerId;
            $saveData->file_name_original = $file_name_original;
            $saveData->file_name_server = $file_name_server;
            $saveData->file_ext = $ext;
            $saveData->save();

        }else{

            return 'false';
        }
        $i++;
      }
      return ture;
    }



    //     public function getItemDetails($item_size){
    //       $buyerList = $this->getUserByerList();
    //       if(isset($buyerList) && !empty($buyerList)){
    //         $value = DB::table('mxp_product as mp')
    //           ->leftJoin('mxp_productsize as mps','mps.product_code', '=','mp.product_code')
    //           ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
    //           ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
    //           ->select('mp.erp_code','mp.product_id','mp.unit_price','mp.product_name','mp.others_color','mp.product_description',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size) as size'),DB::raw('GROUP_CONCAT(DISTINCT mgs.color_name) as color'))
    //           ->where([
    //               ['mp.product_code',$item_size],
    //               ['mp.status',ActionMessage::ACTIVE]
    //             ])
    //           ->whereIn('mp.id_buyer',$buyerList)
    //           ->get();
              
    //       }else if(Auth::user()->type == 'super_admin'){
    //         $value = DB::table('mxp_product as mp')
    //           ->leftJoin('mxp_productsize as mps','mps.product_code', '=','mp.product_code')
    //           ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
    //           ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
    //           ->select('mp.erp_code','mp.product_id','mp.unit_price','mp.product_name','mp.others_color','mp.product_description',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size) as size'),DB::raw('GROUP_CONCAT(DISTINCT mgs.color_name) as color'))
    //           ->where([
    //               ['mp.product_code',$item_size],
    //               ['mp.status',ActionMessage::ACTIVE]
    //             ])
    //           ->get();
    //       }else{
    //             $value = [];
    //       }
    //     return $value;
    //   }



    

    public function getItemDetails($item_size){
      $buyerList = $this->getUserByerList();
      if(isset($buyerList) && !empty($buyerList)){
        $value = DB::table('mxp_product as mp')
          // ->leftJoin('mxp_productsize as mps','mps.product_code', '=','mp.product_code')
          ->leftJoin('mxp_products_sizes as mpss','mpss.product_id', '=','mp.product_id')
          ->leftJoin('mxp_productsize as mps','mps.proSize_id', '=','mpss.size_id')
          ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
          ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
          ->select('mp.erp_code','mp.product_id','mp.unit_price','mp.product_name','mp.others_color','mp.product_description',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size) as size'),DB::raw('GROUP_CONCAT(DISTINCT mgs.color_name) as color'))
          ->where([
              ['mp.product_code',$item_size],
              ['mp.status',ActionMessage::ACTIVE]
            ])
          ->whereIn('mp.id_buyer',$buyerList)
          ->get();
          
      }else if(Auth::user()->type == 'super_admin'){
        $value = DB::table('mxp_product as mp')
          // ->leftJoin('mxp_productsize as mps','mps.product_code', '=','mp.product_code')
          ->leftJoin('mxp_products_sizes as mpss','mpss.product_id', '=','mp.product_id')
          ->leftJoin('mxp_productsize as mps','mps.proSize_id', '=','mpss.size_id')
          ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
          ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
          ->select('mp.erp_code','mp.product_id','mp.unit_price','mp.product_name','mp.others_color','mp.product_description',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size) as size'),DB::raw('GROUP_CONCAT(DISTINCT mgs.color_name) as color'))
          ->where([
              ['mp.product_code',$item_size],
              ['mp.status',ActionMessage::ACTIVE]
            ])
          ->get();
      }else{
            $value = [];
      }
    return $value;
  }

  public function updateBookingView(Request $request){
    $description = MxpItemDescription::where('is_active',ActionMessage::ACTIVE)->get();
    if(isset($request->job_id) && !empty($request->job_id)){
      $mxpBooking = MxpBooking::where([
                  ['is_deleted',BookingFulgs::IS_NOT_DELETED],
                  ['id',$request->job_id]
                ])
                ->first();

      $pi_value = MxpPi::where([
              ['job_no',$request->job_id],
              ['is_deleted',BookingFulgs::IS_NOT_DELETED]
            ])
            ->select('p_id','booking_order_id','item_code','item_quantity','item_size','item_price')
            ->first();
    }
    
    $party_id = $request->party_id;

    // $this->print_me($pi_value);
    return view('maxim.booking_list.booking_update',compact('description','mxpBooking','party_id','pi_value'));
  }

  public function updateBooking(Request $request){


    $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($request->booking_id));
    $job_id_id = str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount).$request->booking_id;

    $insertBooking = MxpBooking::where('id', $request->booking_id)->first();
    $mxp_pi = MxpPi::where('job_no', $request->booking_id)->first();
    // self::print_me($mxp_pi);
      
      if(!empty($mxp_pi)) {
        $mxp_pi->item_description = $request->item_description;
        $mxp_pi->oos_number = $request->oos_number;
        $mxp_pi->style = $request->style;
        $mxp_pi->poCatNo = $request->poCatNo;
        $mxp_pi->item_code = $request->item_code;
        $mxp_pi->gmts_color = $request->gmts_color;
        $mxp_pi->item_size = $request->item_size;
        $mxp_pi->sku = $request->sku;
        $mxp_pi->item_quantity = $request->item_qty;
        $mxp_pi->item_price = $request->item_price;
        $mxp_pi->last_action_at = BookingFulgs::LAST_ACTION_UPDATE;
        $mxp_pi->save();

        $msg = $job_id_id." Job id Successfully updated.";

      }else{
        $msg = "Something went wrong please try again later"; 
      }

      if(isset($insertBooking) && !empty($insertBooking)){

        $insertBooking->item_description = $request->item_description;
        $insertBooking->oos_number = $request->oos_number;
        $insertBooking->style = $request->style;
        $insertBooking->poCatNo = $request->poCatNo;
        $insertBooking->item_code = $request->item_code;
        $insertBooking->gmts_color = $request->gmts_color;
        $insertBooking->item_size = $request->item_size;
        $insertBooking->sku = $request->sku;
        $insertBooking->item_quantity = $request->item_qty;
        $insertBooking->item_price = $request->item_price;
        $insertBooking->last_action_at = BookingFulgs::LAST_ACTION_UPDATE;
        $insertBooking->save();


        $msg = $job_id_id." Job id Successfully updated.";


      }else{

        $msg = "Something went wrong please try again later"; 
      }

      $insertBookingChallan = MxpBookingChallan::where('id', $request->booking_id)->first();

      if(isset($insertBookingChallan) && !empty($insertBookingChallan)){
        $insertBookingChallan->item_description = $request->item_description;
        $insertBookingChallan->oos_number = $request->oos_number;
        $insertBookingChallan->style = $request->style;
        $insertBookingChallan->poCatNo = $request->poCatNo;
        $insertBookingChallan->item_code = $request->item_code;
        $insertBookingChallan->gmts_color = $request->gmts_color;
        $insertBookingChallan->item_size = $request->item_size;
        $insertBookingChallan->sku = $request->sku;
        $insertBookingChallan->item_quantity = $request->item_qty;
        $insertBookingChallan->left_mrf_ipo_quantity = $request->item_qty;
        $insertBookingChallan->item_price = $request->item_price;
        $insertBookingChallan->last_action_at = BookingFulgs::LAST_ACTION_UPDATE;
        $insertBookingChallan->save();

        $msg = $job_id_id." Job id Successfully updated.";

      }else{
        $msg = "Something went wrong please try again later.";
      }

      Session::flash('message', $msg);

      return redirect()->route('booking_list_details_view', $insertBooking->booking_order_id);
    }

    public function cancelBooking($id){
      
      $booking = MxpBooking::where('booking_order_id', $id)->get();

      if(isset($booking) && !empty($booking)){
        foreach ($booking as $value) {
          $value->is_deleted = BookingFulgs::IS_DELETED;
          $value->deleted_user_id = Auth::User()->user_id;
          $value->deleted_date_at = Carbon\Carbon::now();
          $value->last_action_at = BookingFulgs::LAST_ACTION_DELETE;
          $value->save();
          $msg = "Booking ".$id." canceled"; 
        }

        $booking_challan = MxpBookingChallan::where('booking_order_id', $id)->get();

        if(isset($booking_challan) && !empty($booking_challan[0]->booking_order_id)){
            foreach ($booking_challan as $booking_challan_value) {
                $booking_challan_value->is_deleted = BookingFulgs::IS_DELETED;
                $booking_challan_value->deleted_user_id = Auth::User()->user_id;
                $booking_challan_value->deleted_date_at = Carbon\Carbon::now();
                $booking_challan_value->last_action_at = BookingFulgs::LAST_ACTION_DELETE;
                $booking_challan_value->save();
                $msg = "Booking ".$id." canceled";
            }

        }else{
            $error = "Something went wrong on Booking Challan Table please try again later";
        }

        $InserBuyerDetails = MxpBookingBuyerDetails::where('booking_order_id', $id)->get();

        if(isset($InserBuyerDetails) && !empty($InserBuyerDetails)){
            foreach ($InserBuyerDetails as $value) {
                $value->is_deleted = BookingFulgs::IS_DELETED;
                $value->deleted_user_id = Auth::User()->user_id;
                $value->deleted_date_at = Carbon\Carbon::now();
                $value->last_action_at = BookingFulgs::LAST_ACTION_DELETE;
                $value->save();
                $msg = "Booking ".$id." canceled";
            }

        }else{
            $error = "Something went wrong on Buyer Details table please try again later";
        }

        $pi_value = MxpPi::where('booking_order_id', $id)->get();

        if(isset($pi_value) && !empty($pi_value)) {
          foreach ($pi_value as $value) {
            $value->is_deleted = BookingFulgs::IS_DELETED;
            $value->deleted_user_id = Auth::User()->user_id;
            $value->deleted_date_at = Carbon\Carbon::now();
            $value->save();
          }
        }

        // $ipo = MxpIpo::where('booking_order_id', $id)->get();

        // if(isset($ipo) && !empty($ipo[0]->ipo_id)) {
        //   foreach ($ipo as $ipovalue) {
        //     $ipovalue->is_deleted = BookingFulgs::IS_DELETED;
        //     $ipovalue->deleted_user_id = Auth::User()->user_id;
        //     $ipovalue->deleted_date_at = Carbon\Carbon::now();
        //     $ipovalue->save();
        //   }
        // }

        // $mrf_value = MxpMrf::where('booking_order_id', $id)->get();

        //  if(isset($mrf_value) && !empty($mrf_value[0]->mrf_id)) {
        //   foreach ($mrf_value as $mrfvalue) {
        //     $mrfvalue->is_deleted = BookingFulgs::IS_DELETED;
        //     $mrfvalue->deleted_user_id = Auth::User()->user_id;
        //     $mrfvalue->deleted_date_at = Carbon\Carbon::now();
        //     $mrfvalue->save();
        //   }
        // $os_po_value = MxpOsPo::where('mrf_id', $mrf_value[0]->mrf_id)->get();
        // }

        // if(isset($os_po_value) && !empty($os_po_value[0]->mrf_id)) {
        //   foreach ($os_po_value as $osPoValue) {
        //     $osPoValue->is_deleted = BookingFulgs::IS_DELETED;
        //     $osPoValue->deleted_user_id = Auth::User()->user_id;
        //     $osPoValue->save();
        //   }
        // }
        
      }else{
        $error = "Something went wrong please on booking table try again later ";
      }

        Session::flash('message', $msg);
        Session::flash('error-m', $error);
      
      return Redirect()->back();
    }

    public function bookingJobIdDelete(Request $request){
      $idstrcount = (8 - strlen($request->id));
      $job_id_id = str_repeat('0',$idstrcount).$request->id;

      MxpBooking::where('id', $request->id)->update([
        'is_deleted' => BookingFulgs::IS_DELETED,
        'deleted_user_id' => Auth::User()->user_id,
        'deleted_date_at' =>  Carbon\Carbon::now(),
        'last_action_at' =>  BookingFulgs::LAST_ACTION_DELETE,
      ]);

      MxpBookingChallan::where('job_id', $request->id)->update([
        'is_deleted' => BookingFulgs::IS_DELETED,
        'deleted_user_id' => Auth::User()->user_id,
        'deleted_date_at' =>  Carbon\Carbon::now(),
        'last_action_at' =>  BookingFulgs::LAST_ACTION_DELETE,
      ]);

      Session::flash('message', $job_id_id." Job id Successfully Deleted.");
      return Redirect()->back();
    }
}
