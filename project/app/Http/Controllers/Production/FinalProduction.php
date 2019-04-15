<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Model\MxpRawItem;
use App\MxpProductionFinishedItem;
use App\MxpProductionRawItem;
use App\MxpProductionRawItemWaste;
use App\MxpProductions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Validator;

class FinalProduction extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = MxpProductions::where([
            ['status', 1],
            ['is_deleted', 0],
        ])->paginate(10);
        return view('production.final_production.index',compact('details'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $details = [];

        return view('production.final_production.create',compact('details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datas = $request->all();
        $validMessages = [
            'raw_item_code.*.required' => 'Raw Item Code field is required.',
            'p_item_code.*.required' => 'Production Item Code field is required.',
            'raw_item_qty.*.required' => 'Raw Quantity field is required.',
            'p_item_qty.*.required' => 'Production Quantity field is required.',
            'order_date.required' => 'Order Date field is required.',
            'description.required' => 'Description field is required.',
        ];
        $validator = Validator::make($datas,
            [
                'raw_item_code.*' => 'required',
                'p_item_code.*' => 'required',
                'raw_item_qty.*' => 'required',
                'p_item_qty.*' => 'required',
                'order_date' => 'required',
                'description' => 'required',
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        /*Final Product Add in mxp_productions, mxp_production_finished_item, mxp_production_raw_item tables. */

        if(isset($datas)){
            if (isset($datas['order_date'])){
                $production = new MxpProductions();
                $production->production_date = $request->order_date;
                $production->description = $request->description;
                $production->status = 1;
                $production->last_action_at = 'Create';
                $production->save();
                $last_id = $production->id_mxp_productions;
            }
            if (isset($datas['raw_item_code'])){
                for($i = 0; $i < count($request['raw_item_code']) ; $i++){
                    $raw_production = new MxpProductionRawItem();
                    $raw_production->production_id = $last_id;
                    $raw_production->item_code = $request['raw_item_code'][$i];
                    $raw_production->quantity = $request['raw_item_qty'][$i];
                    $raw_production->status = 1;
                    $raw_production->last_action_at = 'Create';
                    $raw_production->save();
                }
            }
            if (isset($datas['p_item_code'])){
                for ($i=0; $i< count($request['p_item_code']); $i++){
                    $p_production = new MxpProductionFinishedItem();
                    $p_production->production_id = $last_id;
                    $p_production->item_code = $request['p_item_code'][$i];
                    $p_production->item_size = $request['p_size_range'][$i];
                    $p_production->item_color = $request['p_gmt_color'][$i];
                    $p_production->quantity = $request['p_item_qty'][$i];
                    $p_production->status = 1;
                    $p_production->last_action_at = 'Create';
                    $p_production->save();
                }
            }
            if (isset($datas['w_raw_item_code'])){
                for ($i=0; $i< count($request['w_raw_item_code']); $i++){
                    $raw_production = new MxpProductionRawItemWaste();
                    $raw_production->production_id = $last_id;
                    $raw_production->item_code = $request['w_raw_item_code'][$i];
                    $raw_production->quantity = $request['w_raw_item_qty'][$i];
                    $raw_production->status = 1;
                    $raw_production->last_action_at = 'Create';
                    $raw_production->save();
                }
            }
        }
        StatusMessage::create('store', 'Finished Product Created Successfully');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $product_id)
    {
        $details = MxpProductions::where('id_mxp_productions', $product_id)->get();

        foreach ($details as $value){
            $details->raw = MxpProductionRawItem::where('production_id', $value->id_mxp_productions)->get();
            $details->raw_waste = MxpProductionRawItemWaste::where('production_id', $value->id_mxp_productions)->get();
            $details->finished = MxpProductionFinishedItem::where('production_id', $value->id_mxp_productions)->get();

            if(isset($details->finished) && ! empty($details->finished)) {
                foreach ($details->finished as &$item_value){
                    $item_value->get_item_size = $this->getItemSizeByItemCode($item_value->item_code);
                    $item_value->get_item_color = $this->getItemColorByItemCode($item_value->item_code);
                }
            }

        }
//        $this->print_me($details);
        return view('production.final_production.edit',compact('details'));
    }

    public function getItemSizeByItemCode($item_code) {
        $item_size = DB::table('mxp_product as mp')
            ->leftJoin('mxp_products_sizes as mpss','mpss.product_id', '=','mp.product_id')
            ->leftJoin('mxp_productsize as mps','mps.proSize_id', '=','mpss.size_id')
            ->select('mps.product_size')
            ->where([
                ['mp.product_code',$item_code],
                ['mp.status',1]
            ])
            ->get();

        return $item_size ;
    }

    public function getItemColorByItemCode($item_code) {
        $item_color = DB::table('mxp_product as mp')
            ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
            ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
            ->select('mgs.color_name')
            ->where([
                ['mp.product_code',$item_code],
                ['mp.status',1]
            ])
            ->get();

        return $item_color ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $datas = $request->all();
        dd($datas);
        $validMessages = [
            'raw_item_code.*.required' => 'Raw Item Code field is required.',
            'p_item_code.*.required' => 'Production Item Code field is required.',
            'raw_item_qty.*.required' => 'Raw Quantity field is required.',
            'p_item_qty.*.required' => 'Production Quantity field is required.',
            'order_date.required' => 'Order Date field is required.',
            'description.required' => 'Description field is required.',
        ];
        $validator = Validator::make($datas,
            [
                'raw_item_code.*' => 'required',
                'p_item_code.*' => 'required',
                'raw_item_qty.*' => 'required',
                'p_item_qty.*' => 'required',
                'order_date' => 'required',
                'description' => 'required',
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        /*Final Product Update in mxp_productions, mxp_production_finished_item, mxp_production_raw_item tables. */

        if(isset($datas)) {
            if (isset($datas['order_date'])) {
                $production = MxpProductions::find($request->product_id);
                $production->production_date = $request->order_date;
                $production->description = $request->description;
                $production->status = 1;
                $production->last_action_at = 'updated';
                $production->save();
            }
            if (isset($datas['raw_item_code'])) {
                for ($i = 0; $i < count($request['raw_item_code']); $i++) {
                    if ($request['addinput'] == 'raw_new') {
                        $raw_production = new MxpProductionRawItem();
                        $raw_production->production_id = $request->product_id;
                        $raw_production->item_code = $request['raw_item_code'][$i];
                        $raw_production->quantity = $request['raw_item_qty'][$i];
                        $raw_production->status = 1;
                        $raw_production->last_action_at = 'Create';
                        $raw_production->save();

                    } else {
                        $raw_production = MxpProductionRawItem::where('production_id', $request->product_id)->first();
                        $raw_production->item_code = $request['raw_item_code'];
                        $raw_production->quantity = $request['raw_item_qty'];
                        $raw_production->status = 1;
                        $raw_production->last_action_at = 'Create';
                        $raw_production->save();
                    }

                }
            }
            if (isset($datas['p_item_code'])) {
                for ($i = 0; $i < count($request['p_item_code']); $i++) {
                    if ($request['addinput'] == 'p_new') {
                        $p_production = new MxpProductionFinishedItem();
                        $p_production->production_id = $request->product_id;
                        $p_production->item_code = $request['p_item_code'][$i];
                        $p_production->item_size = $request['p_size_range'][$i];
                        $p_production->item_color = $request['p_gmt_color'][$i];
                        $p_production->quantity = $request['p_item_qty'][$i];
                        $p_production->status = 1;
                        $p_production->last_action_at = 'Create';
                        $p_production->save();
                    }else{
                        $p_production = MxpProductionFinishedItem::where('production_id', $request->product_id)->first();
                        $p_production->item_code = $request['p_item_code'];
                        $p_production->item_size = $request['p_size_range'];
                        $p_production->item_color = $request['p_gmt_color'];
                        $p_production->quantity = $request['p_item_qty'];
                        $p_production->status = 1;
                        $p_production->last_action_at = 'Create';
                        $p_production->save();
                    }
                }
            }

            if (isset($datas['w_raw_item_code'])) {
                for ($i = 0; $i < count($request['raw_item_code']); $i++) {
                    if ($request['addinput'] == 'w_new') {
                        $raw_production = new MxpProductionRawItemWaste();
                        $raw_production->production_id = $request->product_id;
                        $raw_production->item_code = $request['raw_item_code'][$i];
                        $raw_production->quantity = $request['raw_item_qty'][$i];
                        $raw_production->status = 1;
                        $raw_production->last_action_at = 'Create';
                        $raw_production->save();

                    } else {
                        $raw_production = MxpProductionRawItemWaste::where('production_id', $request->product_id)->first();
                        $raw_production->item_code = $request['raw_item_code'];
                        $raw_production->quantity = $request['raw_item_qty'];
                        $raw_production->status = 1;
                        $raw_production->last_action_at = 'Create';
                        $raw_production->save();
                    }

                }
            }
        }
        StatusMessage::create('store', 'Finished Product updated Successfully');
        return redirect()->back();
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id)
    {
       if(is($product_id)){
           MxpProductions::where('id_mxp_productions', $product_id)
               ->update([
                   'is_deleted' => 1,
                   'status' => 0,
                   'deleted_user_id' => Auth::user()->user_id,
                   'deleted_date_at' => Carbon::today(),
                   'last_action_at' => 'Deleted',
               ]);
           MxpProductionRawItem::where('production_id', $product_id)
               ->update([
                   'is_deleted' => 1,
                   'status' => 0,
                   'deleted_user_id' => Auth::user()->user_id,
                   'deleted_date_at' => Carbon::today(),
                   'last_action_at' => 'Deleted',
               ]);
           MxpProductionFinishedItem::where('production_id', $product_id)
               ->update([
                   'is_deleted' => 1,
                   'status' => 0,
                   'deleted_user_id' => Auth::user()->user_id,
                   'deleted_date_at' => Carbon::today(),
                   'last_action_at' => 'Deleted',
               ]);
       }
       return redirect()->back();
    }

    public function getProductItems(Request $request) {
        $item_code = isset($request->item_code) ? $request->item_code : '';

        /** increase group_concat_max_len **/
        DB::statement('SET SESSION group_concat_max_len=10000000;');


        $value = DB::table('mxp_product as mp')
          ->leftJoin('mxp_products_sizes as mpss','mpss.product_id', '=','mp.product_id')
          ->leftJoin('mxp_productsize as mps','mps.proSize_id', '=','mpss.size_id')
          ->leftJoin('mxp_products_colors as mpc','mpc.product_id', '=', 'mp.product_id')
          ->leftJoin('mxp_gmts_color as mgs','mgs.id', '=', 'mpc.color_id')
          ->select('mp.erp_code','mp.product_id','mp.product_code','mp.product_description',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size) as size'),DB::raw('GROUP_CONCAT(DISTINCT mgs.color_name) as color'))
          ->where([
              ['mp.product_code',$item_code],
              ['mp.status',1]
            ])
          ->get();

        return $value;
    }
    public function getProductDetails(Request $request){
        return json_encode($this->getProductItems($request));
    }
}
