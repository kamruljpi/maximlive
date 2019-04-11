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
use Session;
use Auth;
use DB;

class Purchase extends Controller
{
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
                    ['mxp_purchase_order_wh.is_deleted', BookingFulgs::IS_NOT_DELETED],
                    ['mxp_purchase_order_wh.status', PurchaseFlugs::PURCHASE]
                ])
                ->select('mxp_purchase_order_wh.*',DB::Raw('SUM(mporw.item_qty) as item_total_qty'))
                ->groupBy('mporw.purchase_order_wh_id')
                ->paginate(20);

        // $this->print_me($details);

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
        $store_order->user_id = Auth::user()->user_id;
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
                $store_item->user_id = Auth::user()->user_id ;
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
                    ['status', PurchaseFlugs::PURCHASE],
                    ['id_purchase_order_wh', $id]
                ])
                ->first();
        if(! empty($details)) {
            $details->item_details = MxpPurchaseOrderItemWh::where([
                ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                ['purchase_order_wh_id', $details->id_purchase_order_wh]
            ])
            ->get();
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
        // $this->print_me($details);

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

        return $request->datas ;
        // $order_date = isset($request->order_date) ? $request->order_date : '';
        // $purchase_voucher = isset($request->purchase_voucher) ? $request->purchase_voucher : '';
        // $bilty_no = isset($request->bilty_no) ? $request->bilty_no : '';
        // $description = isset($request->description) ? $request->description : '';

        $id_purchase_order_wh = isset($id) ? $id : '';
        
        $raw_item_id = isset($request->raw_item_id) ? $request->raw_item_id : [];
        $item_code = isset($request->item_code) ? $request->item_code : [];
        $item_qty = isset($request->item_qty) ? $request->item_qty : [];
        $price = isset($request->price) ? $request->price : [];
        $item_total_price = isset($request->item_total_price) ? $request->item_total_price : [];
        $location_id = isset($request->location_id) ? $request->location_id : [];
        $zone_id = isset($request->zone_id) ? $request->zone_id : [];
        $warehouse_type_id = isset($request->warehouse_type_id) ? $request->warehouse_type_id : [];

        // $in_all_total_price = isset($request->in_all_total_price) ? $request->in_all_total_price : '';
        // $discount = isset($request->discount) ? $request->discount : '';
        // $vat = isset($request->vat) ? $request->vat : '';
        // $payment_status = isset($request->payment_status) ? $request->payment_status : '';
        // $paying_by = isset($request->paying_by) ? $request->paying_by : '';

        // $item_details = [];

        // if(! empty($raw_item_id)) {
        //     foreach ($raw_item_id as $keys => $item_id) {
        //         if(! empty($item_id)) {
        //             $item_details[$keys]['raw_item_id'] = $item_id;
        //             $item_details[$keys]['item_code'] = $item_code[$keys];
        //             $item_details[$keys]['item_qty'] = $item_qty[$keys];
        //             $item_details[$keys]['price'] = $price[$keys];
        //             $item_details[$keys]['item_total_price'] = $item_total_price[$keys];
        //             $item_details[$keys]['location_id'] = $location_id[$keys];
        //             $item_details[$keys]['zone_id'] = $zone_id[$keys];
        //             $item_details[$keys]['warehouse_type_id'] = $warehouse_type_id[$keys];
        //         }                
        //     }
        // }
        $this->print_me($item_details);
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
}
