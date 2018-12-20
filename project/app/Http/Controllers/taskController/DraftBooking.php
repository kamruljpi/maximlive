<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\NotificationController;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notification;
use App\MxpProduct;
use App\MaxParty;
use App\MxpDraft;
use Validator;
use Session;
use Carbon;
use Auth;
use DB;

class DraftBooking extends Controller
{

    public function index(){
        $draft_list = MxpDraft::where('is_deleted',BookingFulgs::IS_NOT_DELETED)
            ->groupBy('booking_order_id')
            ->orderBy('id',DESC)
            ->paginate(15);
            
        return view('maxim.draft.draft_list',compact('draft_list'));
    }

    public function draftIndex($id){
        $draft_list = MxpDraft::where(
            [
                ['booking_order_id' , $id ],
                ['is_deleted', BookingFulgs::IS_NOT_DELETED ],
            ]
            )->get();
        return view('maxim.booking_list.draft_booking_page',compact('draft_list'));
    }

   /**
    *
    * @param $request get all input field value
    */
    public function storeOrderDraft(Request $request, $id) {
      $datas = [];

      $is_type = isset($request->is_type) ? $request->is_type : '';
      $order_date = isset($request->orderDate) ? $request->orderDate : Carbon::now()->format('Y-m-d');
      $shipment_date = isset($request->shipmentDate) ? $request->shipmentDate :'';
      $season_code = isset($request->season_code) ? $request->season_code :'';
      $booking_category = isset($request->booking_category) ? $request->booking_category : 'sss';
      $buyer_details = isset($request['buyerDetails']) ? json_decode($request['buyerDetails']) : '' ;
      $booking_number = isset($request['booking_number']) ? $request['booking_number'] : '' ;

      foreach ($buyer_details as $key => $buyers) {
         $buyer_details = $buyers;
      }

      $item_descriptions = isset($request['item_description']) ? $request['item_description'] : [];
      $item_gmts_colors = isset($request['item_gmts_color']) ? $request['item_gmts_color'] : [];
      $others_colors = isset($request['others_color']) ? $request['others_color'] : [];
      $oos_numbers  = isset($request['oos_number']) ? $request['oos_number'] : [];
      $item_prices = isset($request['item_price']) ? $request['item_price'] : [];
      $item_sizes = isset($request['item_size']) ? $request['item_size'] : [];
      $item_codes = isset($request['item_code']) ? $request['item_code'] : [];
      $item_qtys = isset($request['item_qty']) ? $request['item_qty'] : [];
      $poCatNos = isset($request['poCatNo']) ? $request['poCatNo'] : [];
      $styles = isset($request['style']) ? $request['style'] : [];
      $erps  = isset($request['erp']) ? $request['erp'] : [];
      $skus = isset($request['sku']) ? $request['sku'] : [];

      if (is_array($item_codes)) {
         foreach ($item_codes as $keys => $item_code) {
         	$datas[$keys]['item_description'] = $item_descriptions[$keys];
         	$datas[$keys]['item_gmts_color'] = $item_gmts_colors[$keys];
         	$datas[$keys]['others_color'] = $others_colors[$keys];
         	$datas[$keys]['oos_number'] = $oos_numbers[$keys];
         	$datas[$keys]['item_price'] = $item_prices[$keys];
         	$datas[$keys]['item_size'] = $item_sizes[$keys];
         	$datas[$keys]['item_code'] = $item_code;
         	$datas[$keys]['item_qty'] = $item_qtys[$keys];
         	$datas[$keys]['poCatNo'] = $poCatNos[$keys];
         	$datas[$keys]['style'] = $styles[$keys];
         	$datas[$keys]['erp'] = $erps[$keys];
         	$datas[$keys]['sku'] = $skus[$keys];
         }
      }

      $ids = $booking_number ? $booking_number : $id ;


      if(!empty($datas)) {

         $delete = MxpDraft::where('booking_order_id',$ids)->delete();

         foreach ($datas as $key => $data) {

            $item_details = MxpProduct::where('product_code',$data['item_code'])->first();

            $mxp_draft = new MxpDraft();
            $mxp_draft->user_id = Auth::User()->user_id;
            $mxp_draft->vendor_id = $buyer_details->id;
            $mxp_draft->booking_order_id = $ids;
            $mxp_draft->erp_code = $data['erp'];
            $mxp_draft->item_code = $data['item_code'];
            $mxp_draft->item_size = $data['item_size'];
            $mxp_draft->item_quantity = $data['item_qty'];
            $mxp_draft->item_price = $data['item_price'];
            $mxp_draft->item_description  = $data['item_description'];
            $mxp_draft->gmts_color = $data['item_gmts_color'];
            $mxp_draft->style = $data['style'];
            $mxp_draft->orderDate = $order_date;
            $mxp_draft->shipmentDate = $shipment_date;
            $mxp_draft->sku = $data['sku'];
            $mxp_draft->poCatNo = $data['poCatNo'];
            $mxp_draft->oos_number = $data['oos_number'];
            $mxp_draft->season_code = $season_code;
            $mxp_draft->item_size_width_height = $item_details->item_size_width_height;
            $mxp_draft->is_type           = $is_type;
            $mxp_draft->booking_category  = $booking_category;
            $mxp_draft->is_pi_type        = BookingFulgs::IS_PI_UNSTAGE_TYPE;
            $mxp_draft->last_action_at    = BookingFulgs::LAST_ACTION_CREATE;
            $mxp_draft->save();
         }
      }

      return \Redirect::route('refresh_booking_draft',$ids);
   }

   public function redirectDraftBooking($id) {
      $buyer_details = [];
      $buyerDetails = [];
      $bookings = MxpDraft::where('booking_order_id',$id)->get();

      if(!empty($bookings)) {
         $buyer_details = MaxParty::where('id',$bookings[0]->vendor_id)
            ->select('id','party_id','name','sort_name','name_buyer','address_part1_invoice','address_part2_invoice','attention_invoice','mobile_invoice','telephone_invoice','fax_invoice','address_part1_delivery','address_part2_delivery','attention_delivery','mobile_delivery')
            ->first();

         $buyerDetails = json_encode(MaxParty::where('id',$bookings[0]->vendor_id)
            ->get());
      }

      return view('maxim.draft.draft_booking_page',compact('bookings','buyer_details','buyerDetails'));
   }

  /**
   * this method soft delete draft booking
   */
  public function draftDeleteAction($booking_id) {
    $draft_bookings = MxpDraft::where('booking_order_id',$booking_id)->get();

    if(!empty($draft_bookings[0]->booking_order_id)) {
      foreach ($draft_bookings as $boking_value) {
        $boking_value->is_deleted = BookingFulgs::IS_DELETED;
        $boking_value->deleted_user_id = Auth::User()->user_id;
        $boking_value->deleted_date_at = Carbon\Carbon::now();
        $boking_value->save();
      }      
      Session::flash('message', $draft_bookings[0]->booking_order_id.' successfuly deleted.');
    } else {
      Session::flash('error-m', $booking_id. ' Something is wrong');
    }     

    return Redirect()->back();
  }

}
