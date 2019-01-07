<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MxpIpo;
use App\MxpStore;
Use DB;
Use Auth;
use Validator;
use Carbon;
use Session;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;

class StoreController extends Controller
{
    public function ipoView($id){
    	$ipo_details = DB::table("mxp_ipo")
			->where(
				[
					['job_id',$id],
					['is_deleted',BookingFulgs::IS_NOT_DELETED],
				])
			->get();
				
    	return view('maxim.product.management',compact('ipo_details'));
    }
    public function ipoStore(Request $request){

    	$validMessages = [
    	      'job_id.required' => 'Job Id field is required.',
    	      'is_type.required' => 'Type field is required',
    	      'ipo_id.required' => 'IPO Id field is required',
    	      'item_code.required' => 'Item Size field is required',
    	      'erp_code.required' => 'Erp Code field is required',
    	      'item_description.required' => 'Item Description field is required',
    	      'item_size.required' => 'Item Size field is required',
    	      'shipment_date.required' => 'Shipment Date field is required',
    	      'ipo_quantity.required' => 'Ipo Quantity field is required',
    	      'receive_qty.required' => 'Receive Quantity field is required',
    	      ];
    	$datas = $request->all();

    	$validator = Validator::make($datas, 
    	      [
    	    'job_id' => 'required',
    	    'is_type' => 'required',
    	    'ipo_id' => 'required',
    	    'item_code' => 'required',
    	    'erp_code' => 'required',
    	    'item_description' => 'required',
    	    'item_size' => 'required',
    	    'shipment_date' => 'required',
    	    'ipo_quantity' => 'required',
    	    'receive_qty' => 'required'
    	  ],
    	      $validMessages
    	  );

    	if ($validator->fails()) {
    	  return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
    	}

    	$ipo_store = new MxpStore();
    	
    	if(isset($request->job_id) && !empty($request->job_id)){
    		$ipo_store->job_id =$request->job_id;
    		$ipo_store->product_id =$request->ipo_id;
    		$ipo_store->booking_order_id =$request->booking_order_id;
    		$ipo_store->erp_code =$request->erp_code;
    		$ipo_store->item_code = $request->item_code;
    		$ipo_store->item_quantity = $request->ipo_quantity;
    		$ipo_store->is_type =$request->is_type;
    		$ipo_store->shipment_date =$request->shipment_date;
    		$ipo_store->receive_date = Carbon\Carbon::now();
    		$ipo_store->user_id = Auth::user()->user_id;
    		$ipo_store->status = MrfFlugs::ACCEPTED_MAESSAGE;
    		$ipo_store->save();

    		MxpIpo::where('job_id', $request->job_id)->update([
    		  'ipo_status' => MrfFlugs::ACCEPTED_MAESSAGE
    		]);
    	}

    	return redirect()->route('ipo_list_view');
    }
}
