<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Message\StatusMessage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Validator;
use App\buyer;
use Auth;
use DB;

class buyerController extends Controller
{
    public function buyerView(){
    	$buyers = DB::table('mxp_buyer')
                ->where('is_deleted',0)
                ->orderBy('id_mxp_buyer', 'DESC')
                ->paginate(20);
    	return view('buyer.buyer_list',compact('buyers'));
    }

    public function addbuyerView(){
    	return view('buyer.add_buyer');
    }

    public function addbuyer(Request $request){

        $validMessages = [
            'buyer_name.required' => 'buyer Name field is required.',
            'buyer_name.unique' => 'This buyer Name already inserts.',
            ];

        $datas = $request->all();

    	$validator = Validator::make($datas, 
            [
    			// 'buyer_name' => 'required|unique:mxp_buyer,buyer_name',
                'buyer_name' => [
                                'required',
                                Rule::unique('mxp_buyer')->ignore(1, 'is_deleted')
                                ]
		    ],
            $validMessages
        );

		if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		$validationError = $validator->messages();
    	$createbuyer = new buyer();
        $createbuyer->user_id = Auth::User()->user_id;
    	$createbuyer->buyer_name = $request->buyer_name;
        $createbuyer->last_action_at = 'create';
    	$createbuyer->save();
        $lastId = $createbuyer->id_mxp_buyer;
        
		StatusMessage::create('add_buyer', 'New Buyer Created Successfully');

		if(isset($request->request_type) && $request->request_type == 'ajax'){
            return [
                'id_mxp_buyer' => $lastId,
                'buyer_name' => $request->buyer_name
            ];
        }

		return \Redirect()->Route('buyer_list_view');

    }

    public function updatebuyerView(Request $request){
    	$buyer = buyer::where('id_mxp_buyer', $request->id_mxp_buyer)->get();
    	return view('buyer.update_buyer', compact('buyer'));
    }


    public function updatebuyer(Request $request){

        $validMessages = [
            'buyer_name.required' => 'buyer Name field is required.',
            'buyer_name.unique' => 'This buyer Name already inserts.',
            ];

        $datas = $request->all();

    	$validator = Validator::make($datas, 
            [
                'buyer_name' => 'required|unique:mxp_buyer,buyer_name,'.$request->id_mxp_buyer.',id_mxp_buyer',
		    ],
            $validMessages
        );

		if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

    	$updatebuyer = buyer::find($request->id_mxp_buyer);
        $updatebuyer->user_id = Auth::User()->user_id;
    	$updatebuyer->buyer_name = $request->buyer_name;
        $updatebuyer->last_action_at = 'update';
    	$updatebuyer->save();

		StatusMessage::create('update_buyer', ' Update buyer Successfully');

		return \Redirect()->Route('buyer_list_view');
    }

    public function deletebuyer(Request $request){
    	$buyer = buyer::find($request->id_mxp_buyer);
        $buyer->user_id = Auth::User()->user_id;
        $buyer->is_deleted = 1;
        $buyer->last_action_at = 'delete';
		$buyer->save();
		StatusMessage::create('buyer_delete', $buyer->buyer_name .' delete Successfully');
		return redirect()->Route('buyer_list_view');
    }
}
