<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\Purchase\PurchaseFlugs;
use App\Model\Purchase\MxpPurchaseOrderItemWh;
use App\Model\Purchase\MxpPurchaseOrderWh;
use App\Http\Controllers\Controller;
use App\Model\Location\MxpLocation;
use Illuminate\Http\Request;
use App\MxpWarehouseType;
use App\MxpStore;
use App\MxpZone;
use App\User;
use Session;
use Auth;
use DB;

class Purchase extends Controller
{
    const LAST_ACTION_PURCHASE_STORE = 'store';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = MxpPurchaseOrderWh::join('mxp_purchase_order_item_wh as mporw','mporw.purchase_order_wh_id','mxp_purchase_order_wh.id_purchase_order_wh')
                ->where([
                    ['mporw.is_deleted', BookingFulgs::IS_NOT_DELETED],
                    ['mxp_purchase_order_wh.is_deleted', BookingFulgs::IS_NOT_DELETED]
                ])
                ->where(function($query){
                    $query
                    ->orwhere('mxp_purchase_order_wh.status', PurchaseFlugs::PURCHASE)
                    ->orWhere('mxp_purchase_order_wh.status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
                })
                ->select('mxp_purchase_order_wh.*',DB::Raw('SUM(mporw.item_qty) as item_total_qty'))
                ->groupBy('mporw.purchase_order_wh_id')
                ->orderBy('created_at','desc')
                ->paginate(20);

        if(!empty($details)) {
            foreach ($details as &$de_value) {

                $from_details = $this->getUserName($de_value->from_user_id);

                if(isset($from_details) && !empty($from_details)) {
                    $de_value->from_user_name = $from_details->first_name.' '. $from_details->last_name.' '. $from_details->middle_name ;
                }

                $to_details = $this->getUserName($de_value->to_user_id);

                if(isset($to_details) && !empty($to_details)) {
                    $de_value->to_user_name = $to_details->first_name.' '. $to_details->last_name.' '. $to_details->middle_name ;
                }
            }
        }

        return view('purchase.purchase.index',compact('details'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $details = [];

        return view('purchase.purchase.create',compact('details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $order_date = isset($request->order_date) ? $request->order_date : '';
        $purchase_voucher = isset($request->purchase_voucher) ? $request->purchase_voucher : '';
        $bilty_no = isset($request->bilty_no) ? $request->bilty_no : '';
        $description = isset($request->description) ? $request->description : '';
        $raw_item_id = isset($request->raw_item_id) ? $request->raw_item_id : '';
        $item_code = isset($request->item_code) ? $request->item_code : '';
        $item_qty = isset($request->item_qty) ? $request->item_qty : '';
        $price = isset($request->price) ? $request->price : '';
        $item_total_price = isset($request->item_total_price) ? $request->item_total_price : '';
        $in_all_total_price = isset($request->in_all_total_price) ? $request->in_all_total_price : '';
        $discount = isset($request->discount) ? $request->discount : '';
        $vat = isset($request->vat) ? $request->vat : '';
        $payment_status = isset($request->payment_status) ? $request->payment_status : '';
        $paying_by = isset($request->paying_by) ? $request->paying_by : '';
        $status = isset($request->status) ? $request->status : '';

        $item_details = [];

        if(! empty($raw_item_id)) {
            foreach ($raw_item_id as $keys => $item_id) {
                if(! empty($item_id)) {
                    $item_details[$keys]['raw_item_id'] = $item_id;
                    $item_details[$keys]['item_code'] = $item_code[$keys];
                    $item_details[$keys]['item_qty'] = $item_qty[$keys];
                    $item_details[$keys]['price'] = $price[$keys];
                    $item_details[$keys]['item_total_price'] = $item_total_price[$keys];
                }                
            }
        }

        // if not select any product then return redirect back
        if(empty($item_details)){
            return redirect()->back()->withInput($request->input())->withErrors(['errors' => 'Please input a Product.']);
        }

        // store mxp_purchase_order_wh table

        $store_order = new MxpPurchaseOrderWh();
        $store_order->from_user_id = Auth::user()->user_id;
        $store_order->order_date = $order_date;
        $store_order->purchase_voucher = $purchase_voucher;
        $store_order->bilty_no = $bilty_no;
        $store_order->description = $description;
        $store_order->in_all_total_price = $in_all_total_price;
        $store_order->discount = $discount;
        $store_order->vat = $vat;
        $store_order->payment_status = $payment_status;
        $store_order->paying_by = $paying_by;
        $store_order->status = PurchaseFlugs::PURCHASE;
        $store_order->last_action_at = LastActionFlugs::CREATE_ACTION;
        $store_order->save();

        $purchase_order_wh_id = $store_order->id_purchase_order_wh;

        // store mxp_purchase_order_item_wh table

        if(! empty($item_details)){
            foreach ($item_details as $key => $items) {
                $store_item = new MxpPurchaseOrderItemWh();
                $store_item->from_user_id = Auth::user()->user_id ;
                $store_item->raw_item_id =  $items['raw_item_id'];
                $store_item->item_code =  $items['item_code'];
                $store_item->item_qty =  $items['item_qty'];
                $store_item->price =  $items['price'];
                $store_item->total_price =  $items['item_total_price'];
                $store_item->purchase_order_wh_id =  $purchase_order_wh_id;
                $store_item->status = PurchaseFlugs::PURCHASE;
                $store_item->last_action_at = LastActionFlugs::CREATE_ACTION;
                $store_item->save();
                
            }
            Session::flash('create','New Purchase Order successfully created');
        }
        return \Redirect()->Route('purchase_list_view');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $details = MxpPurchaseOrderWh::where([
                    ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                    ['id_purchase_order_wh', $id]
                ])
                ->where(function($query){
                    $query
                    ->orwhere('status', PurchaseFlugs::PURCHASE)
                    ->orWhere('status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
                })
                ->first();
        if(! empty($details)) {
            $details->item_details = MxpPurchaseOrderItemWh::where([
                ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                ['purchase_order_wh_id', $details->id_purchase_order_wh]
            ])
            ->where(function($query){
                $query
                ->orwhere('status', PurchaseFlugs::PURCHASE)
                ->orWhere('status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
            })
            ->get();

            // if this data store in mxp_store table
            // then get the location_id, zone_id
            // and warehouse_type_id
            if(isset($details->item_details) && !empty($details->item_details)) {

                foreach ($details->item_details as &$item_values) {
                    $get_store_data = MxpStore::where([
                        ['is_deleted',0],
                        ['product_id',$item_values->raw_item_id],
                        ['purchase_order_wh_id',$item_values->purchase_order_wh_id],
                        ['item_code',$item_values->item_code],
                        ['is_type',PurchaseFlugs::PURCHASE_STORE],
                    ])
                    ->select('warehouse_type_id','location_id','zone_id')
                    ->first();

                    if(! empty($get_store_data)) {
                        $item_values->locations_id = $get_store_data->location_id;
                        $item_values->zone_id = $get_store_data->zone_id;
                        $item_values->warehouse_type_id = $get_store_data->warehouse_type_id;

                        if(isset($item_values->zone_id) && ! empty($item_values->zone_id)) {
                            $item_values->zones = MxpZone::where([
                                    ['is_deleted', 0],
                                    ['zone_id',$item_values->zone_id]
                                ])
                                ->get();
                        }
                    }
                }
            }

            //end
        }

        $locations = MxpLocation::where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['status',1]
                    ])
                    ->get();


        $warehouse_in_types = MxpWarehouseType::where([
                        ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                        ['warehouse_in_out_type', 'in'],
                    ])
                    ->get();

        return view('purchase.purchase.show',compact('details','locations','warehouse_in_types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showStore(Request $request)
    {
        $datas = isset($request->datas) ? $request->datas : [] ;

        $id_purchase_order_wh = isset($datas['id_purchase_order_wh']) ? $datas['id_purchase_order_wh'] : '';

        $raw_item_id = isset($datas['raw_item_id']) ? $datas['raw_item_id'] : '';
        $item_code = isset($datas['raw_item_code']) ? $datas['raw_item_code'] : '';
        $item_qty = isset($datas['item_qty']) ? $datas['item_qty'] : '';
        $price = isset($datas['price']) ? $datas['price'] : '';
        $item_total_price = isset($datas['total_price']) ? $datas['total_price'] : '';
        $location_id = isset($datas['location_id']) ? $datas['location_id'] : '';
        $zone_id = isset($datas['zone_id']) ? $datas['zone_id'] : '';
        $warehouse_type_id = isset($datas['warehouse_type_id']) ? $datas['warehouse_type_id'] : '';

        $show_store = new MxpStore();
        $show_store->user_id = Auth::user()->user_id;
        $show_store->product_id = $raw_item_id;
        $show_store->item_code = $item_code;
        $show_store->item_quantity = $item_qty;
        $show_store->location_id = $location_id;
        $show_store->zone_id = $zone_id;
        $show_store->warehouse_type_id = $warehouse_type_id;
        $show_store->purchase_order_wh_id = $id_purchase_order_wh;
        $show_store->is_type = PurchaseFlugs::PURCHASE_STORE;
        $show_store->last_action_at = self::LAST_ACTION_PURCHASE_STORE;
        $show_store->save();

        return json_encode("success");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function storePurchaseOrder(Request $request,$id) {

        $input_details = self::inputValuePurchaseBladePage($request);
        
        // store mxp_purchase_order_wh table

        $store_order = MxpPurchaseOrderWh::find($id);
        $store_order->to_user_id = Auth::user()->user_id;
        $store_order->order_date = $input_details['order_date'];
        $store_order->purchase_voucher = $input_details['purchase_voucher'];
        $store_order->bilty_no = $input_details['bilty_no'];
        $store_order->description = $input_details['description'];
        $store_order->in_all_total_price = $input_details['in_all_total_price'];
        $store_order->discount = $input_details['discount'];
        $store_order->vat = $input_details['vat'];
        $store_order->payment_status = $input_details['payment_status'];
        $store_order->paying_by = $input_details['paying_by'];;
        $store_order->grand_total = $input_details['grand_total'];
        $store_order->status = PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER;
        $store_order->last_action_at = LastActionFlugs::PURCHASE_ORDER_ACTION;
        $store_order->save();

        // store mxp_purchase_order_item_wh table

        if(! empty($input_details['item_details'])){
            foreach ($input_details['item_details'] as $key => $items) {
               
                $store_item = MxpPurchaseOrderItemWh::where([['purchase_order_wh_id',$id],['raw_item_id',$items['raw_item_id']]])->first();
                
                // when new data insert
                if(empty($store_item->raw_item_id) && ! empty($items['raw_item_id'])) {
                    $store_item_z = new MxpPurchaseOrderItemWh();
                    $store_item_z->to_user_id = Auth::user()->user_id ;
                    $store_item_z->raw_item_id =  $items['raw_item_id'];
                    $store_item_z->item_code =  $items['item_code'];
                    $store_item_z->item_qty =  $items['item_qty'];
                    $store_item_z->price =  $items['price'];
                    $store_item_z->total_price =  $items['item_total_price'];
                    $store_item_z->purchase_order_wh_id =  $id;
                    $store_item_z->status = PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER;
                    $store_item_z->last_action_at = LastActionFlugs::PURCHASE_ORDER_ACTION;
                    $store_item_z->save();
                }else{
                     // when update same id data
                    $store_item->to_user_id = Auth::user()->user_id ;
                    $store_item->raw_item_id =  $items['raw_item_id'];
                    $store_item->item_code =  $items['item_code'];
                    $store_item->item_qty =  $items['item_qty'];
                    $store_item->price =  $items['price'];
                    $store_item->total_price =  $items['item_total_price'];
                    $store_item->purchase_order_wh_id =  $id;
                    $store_item->status = PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER;
                    $store_item->last_action_at = LastActionFlugs::PURCHASE_ORDER_ACTION;
                    $store_item->update();
                }
                                
            }

            Session::flash('create','New Purchase Order successfully created');
        }

        return \Redirect()->Route('purchase_list_view');
    }

    public static function inputValuePurchaseBladePage($request = null) {

        if(is_null($request)) {
            return [];
        }

        $value = [];

        $value['order_date'] = isset($request->order_date) ? $request->order_date : '';
        $value['purchase_voucher'] = isset($request->purchase_voucher) ? $request->purchase_voucher : '';
        $value['bilty_no'] = isset($request->bilty_no) ? $request->bilty_no : '';
        $value['description'] = isset($request->description) ? $request->description : '';
        $raw_item_id = isset($request->raw_item_id) ? $request->raw_item_id : '';
        $item_code = isset($request->item_code) ? $request->item_code : '';
        $item_qty = isset($request->item_qty) ? $request->item_qty : '';
        $price = isset($request->price) ? $request->price : '';
        $item_total_price = isset($request->item_total_price) ? $request->item_total_price : '';
        $value['in_all_total_price'] = isset($request->in_all_total_price) ? $request->in_all_total_price : '';
        $value['discount'] = isset($request->discount) ? $request->discount : '';
        $value['vat'] = isset($request->vat) ? $request->vat : '';
        $value['payment_status'] = isset($request->payment_status) ? $request->payment_status : '';
        $value['paying_by'] = isset($request->paying_by) ? $request->paying_by : '';
        $value['status'] = isset($request->status) ? $request->status : '';

        $value['item_details'] = [];

        if(! empty($raw_item_id)) {
            foreach ($raw_item_id as $keys => $item_id) {
                if(! empty($item_id)) {
                    $value['item_details'][$keys]['raw_item_id'] = $item_id;
                    $value['item_details'][$keys]['item_code'] = $item_code[$keys];
                    $value['item_details'][$keys]['item_qty'] = $item_qty[$keys];
                    $value['item_details'][$keys]['price'] = $price[$keys];
                    $value['item_details'][$keys]['item_total_price'] = $item_total_price[$keys];
                }                
            }
        }

        return $value ;
    }

    public function getUserName($user_id = null) {

        if(is_null($user_id)) {
            return [];
        }

        return User::where('user_id',$user_id)->select('first_name', 'middle_name', 'last_name')->first() ;
    }
}
