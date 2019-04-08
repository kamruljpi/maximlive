<?php

namespace App\Http\Controllers\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class FinalProduction extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = [];

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
        //
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

        return view('production.final_production.show',compact('details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $details = [];

        return view('production.final_production.edit',compact('details'));
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
        //
    }

    public function getProductDetails(Request $request) {

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

        return json_encode($value);
    }
}
