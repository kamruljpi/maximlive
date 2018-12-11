<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notification;
use App\MxpProduct;
use App\MxpDraft;
use Validator;
use Carbon;
use Auth;
use DB;

class DraftBooking extends Controller
{
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
      // $this->print_me($order_date);

      $buyer_details = isset($request['buyerDetails']) ? json_decode($request['buyerDetails']) : '' ;

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

       // $this->print_me($datas);

      if(!empty($datas)) {
         foreach ($datas as $key => $data) {

            $item_details = MxpProduct::where('product_code',$data['item_code'])->first();

            $mxp_draft = new MxpDraft();
            $mxp_draft->user_id = Auth::User()->user_id;
            $mxp_draft->vendor_id = $buyer_details->id;
            $mxp_draft->booking_order_id = $id;
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
            $mxp_draft->is_pi_type        = BookingFulgs::IS_PI_UNSTAGE_TYPE;
            $mxp_draft->last_action_at    = BookingFulgs::LAST_ACTION_CREATE;
            $mxp_draft->save();
         }
      }

      return view('maxim.booking_list.draft_booking_page');
   }
}