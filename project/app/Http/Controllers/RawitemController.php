<?php

namespace App\Http\Controllers;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Model\MxpRawItem;
use Validator;
use Session;
use Auth;
use DB;

class RawitemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = MxpRawItem::where('is_deleted',BookingFulgs::IS_NOT_DELETED)->paginate(20);

        return view('purchase.raw_item.index',compact('details'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $details = [];

        return view('purchase.raw_item.create',compact('details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->print_me($request->all());

        $datas = $request->all();

        $validMessages = [
            'item_code.required' => 'The Item Code field is required.',
            'item_code.unique' => 'The Item Code has already been taken.',
            'price.numeric' => 'The Price may only contain numbers.',
            'opening_qty.numeric' => 'The Opening Qty may only contain numbers.',
            ];


        $validator = Validator::make($datas,
            [
                'item_code' => [
                            'required',
                            Rule::unique('mxp_raw_item')->ignore(1, 'is_deleted')
                          ],
                'price' => ['numeric'],
                'opening_qty' => ['numeric'],
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $create = new MxpRawItem();
        $create->item_code = $request->item_code;
        $create->user_id = Auth::User()->user_id;
        $create->item_name = $request->item_name;
        $create->price = $request->price;
        $create->opening_quantity = $request->opening_qty;
        $create->sort_description = $request->sort_description;
        $create->is_active = $request->is_active;
        $create->last_action_at = LastActionFlugs::CREATE_ACTION;
        $create->save();
        
        Session::flash('create', 'New Raw Item Created Successfully');

        return \Redirect()->Route('raw_item_view');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $details = MxpRawItem::where([
                    ['id_raw_item',$id],
                    ['is_deleted',BookingFulgs::IS_NOT_DELETED]
                ])
                ->first();

        return view('purchase.raw_item.edit',compact('details'));
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
        $datas = $request->all();

        $validMessages = [
            'item_code.required' => 'The Item Code field is required.',
            'item_code.unique' => 'The Item Code has already been taken.',
            'price.numeric' => 'The Price may only contain numbers.',
            'opening_qty.numeric' => 'The Opening Qty may only contain numbers.',
            ];


        $validator = Validator::make($datas, 
            [
                'item_code' => [
                            'required',
                            Rule::unique('mxp_raw_item')->ignore(1, 'is_deleted')->ignore($id,'id_raw_item')
                          ],
                'price' => ['numeric'],
                'opening_qty' => ['numeric'],
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $update = MxpRawItem::find($id);
        $update->item_code = $request->item_code;
        $update->user_id = Auth::User()->user_id;
        $update->item_name = $request->item_name;
        $update->price = $request->price;
        $update->opening_quantity = $request->opening_qty;
        $update->sort_description = $request->sort_description;
        $update->is_active = $request->is_active;
        $update->last_action_at = LastActionFlugs::UPDATE_ACTION;
        $update->save();
        
        Session::flash('update', 'Raw Item Update Successfully');

        return \Redirect()->Route('raw_item_view');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destroy = MxpRawItem::find($id);
        $destroy->user_id = Auth::User()->user_id;
        $destroy->is_deleted = BookingFulgs::IS_DELETED;
        $destroy->last_action_at = LastActionFlugs::DELETE_ACTION;
        $destroy->save();

        Session::flash('delete', 'Raw Item Successfully Delete.');

        return \Redirect()->Route('raw_item_view');
    }

    /**
     * get ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function getRawItemCode() {
        $results = array();

        $productDetails = MxpRawItem::where('is_deleted',BookingFulgs::IS_NOT_DELETED)->select(DB::Raw('DISTINCT item_code'))->get();

        if (isset($productDetails) && !empty($productDetails)) {
            foreach ($productDetails as $itemKey => $itemValue) {
                $results[]['name'] = $itemValue->item_code;
            }
        }

        return json_encode($results);
    }


    /**
     * get ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function getRawItemByItemCode(Request $request) {

        $productDetails = MxpRawItem::where([
                            ['is_deleted',BookingFulgs::IS_NOT_DELETED],
                            ['item_code',$request->item_code]
                        ])
                        // ->select('id_raw_item','item_code',)
                        ->first();

        return json_encode($productDetails);
    }
}
