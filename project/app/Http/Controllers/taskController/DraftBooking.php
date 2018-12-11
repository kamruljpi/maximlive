<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notification;
use Validator;
use Carbon;
use Auth;
use DB;

class DraftBooking extends Controller
{
   protected $is_type = '';

   protected $order_date = '';

   protected $shipment_date = '';

   protected $season_code = '';

   protected $booking_category = '';

   // order buyer details
   protected $buyer_details = [];

   // order filed value
   protected $datas = [];

   /**
   * @param $request get all input field value
   */
   public function __construct(Request $request) {

      $this->is_type = isset($request->is_type) ? $request->is_type : '';
      $this->order_date = isset($request->orderDate) ? $request->orderDate : Carbon::now()->format('d-m-Y');
      $this->shipment_date = isset($request->shipmentDate) ? $request->shipmentDate :'';
      $this->season_code = isset($request->season_code) ? $request->season_code :'';
      $this->booking_category = isset($request->booking_category) ? $request->booking_category : 'sss';
      $this->buyer_details = isset($request['buyerDetails']) ? json_decode($request['buyerDetails']) : '' ;

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
         	$this->datas[$keys]['item_description'] = $item_descriptions[$keys];
         	$this->datas[$keys]['item_gmts_color'] = $item_gmts_colors[$keys];
         	$this->datas[$keys]['others_color'] = $others_colors[$keys];
         	$this->datas[$keys]['oos_number'] = $oos_numbers[$keys];
         	$this->datas[$keys]['item_price'] = $item_prices[$keys];
         	$this->datas[$keys]['item_size'] = $item_sizes[$keys];
         	$this->datas[$keys]['item_code'] = $item_code;
         	$this->datas[$keys]['item_qty'] = $item_qtys[$keys];
         	$this->datas[$keys]['poCatNo'] = $poCatNos[$keys];
         	$this->datas[$keys]['style'] = $styles[$keys];
         	$this->datas[$keys]['erp'] = $erps[$keys];
         	$this->datas[$keys]['sku'] = $skus[$keys];
         }
      }
   }

   public function storeOrderDraft() {

      return view('maxim.booking_list.draft_booking_page');
   }
}