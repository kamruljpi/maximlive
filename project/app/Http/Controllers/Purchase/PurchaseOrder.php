<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\Purchase\PurchaseFlugs;
use App\Model\Purchase\MxpPurchaseOrderItemWh;
use App\Model\Purchase\MxpPurchaseOrderWh;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpRawItem;
use App\User;
use Session;
use Carbon;
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
        $details = MxpPurchaseOrderWh::where('is_deleted', BookingFulgs::IS_NOT_DELETED)
                ->where(function($query){
                    $query
                    ->orwhere('status', PurchaseFlugs::PURCHASE_ORDER)
                    ->orWhere('status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
                })
                ->orderBy('created_at','desc')
                ->paginate(20);

        if(!empty($details)) {
            foreach ($details as &$de_value) {
                $user_details = User::where('user_id',$de_value->from_user_id)->select('first_name', 'middle_name', 'last_name')->first();

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
        $store_order->from_user_id = Auth::user()->user_id;
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
                $store_item->from_user_id = Auth::user()->user_id ;
                $store_item->raw_item_id =  $items['raw_item_id'];
                $store_item->item_code =  $items['item_code'];
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
        $details = [];

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
        $details = MxpPurchaseOrderWh::where([
                    ['id_purchase_order_wh',$id],
                    ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                ])
                ->where(function($query){
                    $query
                    ->orwhere('status', PurchaseFlugs::PURCHASE_ORDER)
                    ->orWhere('status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
                })
                ->first();

        // if is rejected purshase order then show a alert
        if($details->is_rejected == BookingFulgs::IS_REJECTED) {
            Session::flash('delete','This '.$details->purchase_order_no.' Purchase Order is Rejected');

            return \Redirect()->Route('purchase_order_view');
        }

        //end

        if(! empty($details)) {

            $details->item_details = MxpPurchaseOrderItemWh::where([
                    ['purchase_order_wh_id',$details->id_purchase_order_wh],
                    ['is_deleted',BookingFulgs::IS_NOT_DELETED],
                    ['is_rejected',BookingFulgs::IS_NOT_DELETED]
                ])
                ->where(function($query){
                    $query
                    ->orwhere('status', PurchaseFlugs::PURCHASE_ORDER)
                    ->orWhere('status', PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER);
                })
                ->get();

            // store
            $total_price = 0;

            if(isset($details->item_details) && !empty($details->item_details)) {
                
                foreach ($details->item_details as &$item_value) {
                    $item_missing_details = MxpRawItem::where([
                            ['id_raw_item',$item_value->raw_item_id],
                            ['is_deleted',BookingFulgs::IS_NOT_DELETED],
                        ])
                        ->select('price')
                        ->first();

                    if(! empty($item_missing_details) ) {
                        // find missing item details
                        $item_value->price = $item_missing_details->price;
                        $item_value->total_price = $item_missing_details->price * $item_value->item_qty;

                        // calulate total price and store
                        $total_price += $item_value->total_price ;
                    }                    
                }
            }

            $details->in_all_total_price = $total_price ;
        }
        // $this->print_me($details);

        return view('purchase.purchase.edit',compact('details'));
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

        if($destroy->status == PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER) {
            Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order is Accpeted. Delete Failed');

            return \Redirect()->Route('purchase_order_view');
        }else{
            $destroy_b = MxpPurchaseOrderWh::find($id);
            $destroy_b->is_deleted = BookingFulgs::IS_DELETED;
            $destroy_b->deleted_user_id = Auth::user()->user_id;
            $destroy_b->last_action_at = LastActionFlugs::DELETE_ACTION;
            $destroy_b->save();   
        }        

        $destroy_a = MxpPurchaseOrderItemWh::where([['purchase_order_wh_id',$id],['status',PurchaseFlugs::PURCHASE_ORDER]])->get();

        if(! empty($destroy_a)) {
            foreach ($destroy_a as $a_value) {
                $destroy_c = MxpPurchaseOrderItemWh::find($a_value->id_purchase_order_item_wh);
                $destroy_c->is_deleted = BookingFulgs::IS_DELETED;
                $destroy_c->deleted_user_id = Auth::user()->user_id;
                $destroy_c->last_action_at = LastActionFlugs::DELETE_ACTION;
                $destroy_c->save();
            }
        }        

        Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order successfully deleted');

        return \Redirect()->Route('purchase_order_view');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        $reject = MxpPurchaseOrderWh::where('id_purchase_order_wh',$id)->first();

        if($reject->is_rejected == 1) {

            Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order is already Rejected.');
            return \Redirect()->Route('purchase_order_view');

        }elseif($reject->status == PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER) {

            Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order is Accpeted. Rejected Failed');
            return \Redirect()->Route('purchase_order_view');
            
        }else{
            $reject_b = MxpPurchaseOrderWh::where('id_purchase_order_wh',$id)->first();
            $reject_b->is_rejected = BookingFulgs::IS_REJECTED;
            $reject_b->rejected_user_id = Auth::user()->user_id;
            $reject_b->rejected_at = Carbon::now();
            $reject_b->last_action_at = LastActionFlugs::REJECTED_ACTION;
            $reject_b->save();
        }

        $reject_c = MxpPurchaseOrderItemWh::where('purchase_order_wh_id',$id)->get();

        if(! empty($reject_c)) {
            foreach ($reject_c as $c_value) {
                $reject_a = MxpPurchaseOrderItemWh::find($c_value->id_purchase_order_item_wh);
                $reject_a->is_rejected = BookingFulgs::IS_REJECTED;
                $reject_a->rejected_user_id = Auth::user()->user_id;
                $reject_a->rejected_at = Carbon::now();
                $reject_a->last_action_at = LastActionFlugs::REJECTED_ACTION;
                $reject_a->save();
            }
        }

        Session::flash('delete','This '.$destroy->purchase_order_no.' Purchase Order successfully Rejected');

        return \Redirect()->Route('purchase_order_view');
    }

    public function report($id) {
        return "report view" ;
    }
}
