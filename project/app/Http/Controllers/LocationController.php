<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Message\ActionMessage;
use App\Model\Location\MxpLocation;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;

class LocationController extends Controller 
{
	public function index() {
		$details = MxpLocation::where("is_deleted",0)->orderBy('id_location','DESC')->paginate(10);
		return view('location.list_location',compact('details'));
	}

	public function create() {
		$details = [];
		return view('location.create_location',compact('details'));
	}

	public function edit(Request $request) {
		$details = MxpLocation::where([['is_deleted',0],['id_location',$request->id]])->first();
		return view('location.edit_location',compact('details'));
	}

	public function store(Request $request) {

		$datas = $request->all();
		$validMessages = [
            'location.required' => 'location field is required.',
            'location.unique' => 'location field is already entered.'
            ];
    	$validator = Validator::make($datas, 
            [
    			// 'location' => 'required||unique:mxp_location,location',
    			'location' => 'required',
		    ],
            $validMessages
        );

        $store = new MxpLocation();
        $store->user_id  = Auth::user()->user_id;
        $store->location = $request->location;
		$store->status   = $request->status;
		$store->last_action   = ActionMessage::CREATE;
		$store->save();

		if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		StatusMessage::create('store', 'New Location Created Successfully');

		return \Redirect()->Route('location_list_view');
	}

	public function update(Request $request) {

		$datas = $request->all();
		$validMessages = [
            'location.required' => 'location field is required.'
            ];
    	$validator = Validator::make($datas, 
            [
    			'location' => 'required',
		    ],
            $validMessages
        );

        $update = MxpLocation::find($request->id);
        $update->user_id  = Auth::user()->user_id;
        $update->location = $request->location;
		$update->status   = $request->status;
		$update->last_action   = ActionMessage::UPDATE;
		$update->update();

		StatusMessage::create('update', 'Update Location Successfully');

		return \Redirect()->Route('location_list_view');
	}

	public function delete(Request $request) {

		$delete = MxpLocation::find($request->id);
        $delete->is_deleted = 1;
        $delete->user_id  = Auth::user()->user_id;
		$delete->last_action   = ActionMessage::DELETE;
		$delete->update();

		StatusMessage::create('delete', $delete->location.' delete Successfully');

		return \Redirect()->Route('location_list_view');
	}
}