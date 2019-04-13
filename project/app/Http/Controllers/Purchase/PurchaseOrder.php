<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\Purchase\PurchaseFlugs;
use App\Model\Purchase\MxpPurchaseOrderItemWh;
use App\Model\Purchase\MxpPurchaseOrderWh;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Session;
use Auth;
use DB;

class PurchaseOrder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = MxpPurchaseOrderWh::where([
                    ['is_deleted', BookingFulgs::IS_NOT_DELETED],
                    ['status', PurchaseFlugs::PURCHASE_ORDER]
                ])
                ->paginate(20);

        if(!empty($details)) {

            foreach ($details as &$de_value) {
                $user_details = User::where('user_id',$de_value->user_id)->select('first_name', 'middle_name', 'last_name')->first();

                if(isset($user_details) && !empty($user_details)) {
                    $de_value->created_user_name = $user_details->first_name.' '. $user_details->last_name.' '. $user_details->middle_name ;
                }
            }
        }

        return view('purchase.purchase_order.index',compact('details'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $details = [];

        return view('purchase.purchase_order.create',compact('details'));
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
        $purchase_order_no = isset($request->purchase_order_no) ? $request->purchase_order_no : '';
        $description = isset($request->description) ? $request->description : '';
        $raw_item_id = isset($request->raw_item_id) ? $request->raw_item_id : '';
        $item_code = isset($request->item_code) ? $request->item_code : '';
        $item_qty = isset($request->item_qty) ? $request->item_qty : '';

        $item_details = [];

        if(! empty($raw_item_id)) {
            foreach ($raw_item_id as $keys => $item_id) {
                if(! empty($item_id)) {
                    $item_details[$keys]['raw_item_id'] = $item_id;
                    $item_details[$keys]['item_code'] = $item_code[$keys];
                    $item_details[$keys]['item_qty'] = $item_qty[$keys];
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
        $store_order->purchase_order_no = $purchase_order_no;
        $store_order->description = $description;
        $store_order->status = PurchaseFlugs::PURCHASE_ORDER;
        $store_order->last_action_at = LastActionFlugs::CREATE_ACTION;
        $store_order->save();

        $purchase_order_wh_id = $store_order->id_purchase_order_wh;

        // store mxp_purchase_order_item_wh table

        if(! empty($item_details)){
            foreach ($item_details as $key => $items) {
                $store_item = new MxpPurchaseOrderItemWh();
                $store_item->user_id = Auth::user()->user_id ;
                $store_item->raw_item_id =  $items['raw_item_id'];
                $store_item->item_qty =  $items['item_qty'];
                $store_item->purchase_order_wh_id =  $purchase_order_wh_id;
                $store_item->status = PurchaseFlugs::PURCHASE_ORDER;
                $store_item->last_action_at = LastActionFlugs::CREATE_ACTION;
                $store_item->save();
                
            }
            Session::flash('create','New Purchase Order successfully created.');
        }
        return \Redirect()->Route('purchase_order_view');
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
                    ['id_purchase_order_wh',$id],
                    ['is_deleted',BookingFulgs::IS_DELETED],
                ])
                ->first();

        if(! empty($details)) {
            foreach ($details as &$de_value) {
                $de_value->item_details = MxpPurchaseOrderItemWh::where([
                        ['purchase_order_wh_id',$de_value->id_purchase_order_wh],
                        ['is_deleted',BookingFulgs::IS_DELETED],
                    ])
                    ->get();
            }
        }

        return view('purchase.purchase_order.show',compact('details'));
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destroy = MxpPurchaseOrderWh::find($id);
        $destroy->is_deleted = BookingFulgs::IS_DELETED;
        $destroy->deleted_user_id = Auth::user()->user_id;
        $destroy->last_action_at = LastActionFlugs::DELETE_ACTION;
        $destroy->save();

        Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order successfully deleted');

        return \Redirect()->Route('purchase_order_view');
    }

    public function report($id) {
        return "report view" ;
    }
}
