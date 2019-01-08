<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MxpIpo;
use App\MxpStore;
use App\Model\MxpMrf;
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
    	
        $ipo_details = $this->ipoDetails($id);

    	return view('maxim.product.management',compact('ipo_details'));
    }
    public function ipoDetails($id){
        $ipo_details = DB::table("mxp_ipo")
            ->leftjoin('mxp_store as ms','ms.job_id', 'mxp_ipo.job_id')
            ->select('mxp_ipo.*',DB::Raw('sum(ms.item_quantity) as left_quantity'))
            ->where(
                [
                    ['mxp_ipo.job_id',$id],
                    ['mxp_ipo.is_deleted',BookingFulgs::IS_NOT_DELETED],
                ])
            ->get();

        return $ipo_details;
    }
    public function mrfDetails($id){
        $mxp_mrf_details = DB::table("mxp_mrf_table")
            ->leftjoin('mxp_store as ms','ms.job_id', 'mxp_mrf_table.job_id')
            ->select('mxp_mrf_table.*',DB::Raw('(ms.item_quantity) as left_quantity'))
            ->where(
                [
                    ['mxp_mrf_table.job_id',$id],
                    ['mxp_mrf_table.is_deleted',BookingFulgs::IS_NOT_DELETED],
                ])
            ->get();

        return $mxp_mrf_details;
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
    	    'shipment_date' => 'required',
    	    'receive_qty' => 'required'
    	  ],
    	      $validMessages
    	  );

    	if ($validator->fails()) {
    	  return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
    	}

    	$ipo_store = new MxpStore();

    	$ipo_details = $this->ipoDetails($request->job_id);
        if(($ipo_details[0]->ipo_quantity - $ipo_details[0]->left_quantity) == $request->receive_qty ){
            MxpIpo::where('job_id', $request->job_id)->update([
              'ipo_status' => MrfFlugs::ACCEPTED_MAESSAGE
            ]);
        }
        if($request->receive_qty <= ($ipo_details[0]->ipo_quantity - $ipo_details[0]->left_quantity)){
            if(isset($request->job_id) && !empty($request->job_id)){
                $ipo_store->job_id =$request->job_id;
                $ipo_store->product_id =$request->ipo_id;
                $ipo_store->booking_order_id =$request->booking_order_id;
                $ipo_store->erp_code =$request->erp_code;
                $ipo_store->item_code = $request->item_code;
                $ipo_store->item_color = $request->gmts_color;
                $ipo_store->item_size = $request->item_size;
                $ipo_store->item_quantity = $request->receive_qty;
                $ipo_store->is_type =$request->is_type;
                $ipo_store->shipment_date =$request->shipment_date;
                $ipo_store->receive_date = Carbon\Carbon::now();
                $ipo_store->user_id = Auth::user()->user_id;
                $ipo_store->status = MrfFlugs::ACCEPTED_MAESSAGE;
                $ipo_store->save(); 
            }
        }else{
            return redirect()->back()->withInput($request->input())->withErrors("Available Quantity is greater than input Quantity.");
        }

    	return redirect()->route('ipo_list_view');
    }

    public function ipoList(){
        $ipoList = MxpStore::where(
            [   ['is_deleted', BookingFulgs::IS_NOT_DELETED ],
                ['is_type', 'ipo' ]
            ])
            ->get();
        return view('maxim.product.IpoAcceptList',compact('ipoList'));
    }

    public function mrfStore(Request $request){
            $validMessages = [
                  'job_id.required' => 'Job Id field is required.',
                  'is_type.required' => 'Type field is required',
                  'mrf_id.required' => 'Mrf Id field is required',
                  'item_code.required' => 'Item Size field is required',
                  'erp_code.required' => 'Erp Code field is required',
                  'item_description.required' => 'Item Description field is required',
                  'shipment_date.required' => 'Shipment Date field is required',
                  'receive_qty.required' => 'Receive Quantity field is required',
                  ];
            $datas = $request->all();
            // dd($datas);
            $validator = Validator::make($datas, 
                  [
                'job_id' => 'required',
                'is_type' => 'required',
                'mrf_id' => 'required',
                'item_code' => 'required',
                'erp_code' => 'required',
                'item_description' => 'required',
                'shipment_date' => 'required',
                'receive_qty' => 'required'
              ],
                  $validMessages
              );

            if ($validator->fails()) {
              return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
            }

            $mrf_store = new MxpStore();

            $mrf_details = $this->mrfDetails($request->job_id);

            if(($mrf_details[0]->mrf_quantity - $mrf_details[0]->left_quantity) == $request->receive_qty ){
                MxpMrf::where('job_id', $request->job_id)->update([
                  'job_id_current_status' => MrfFlugs::ACCEPTED_MAESSAGE
                ]);
            }
            if($request->receive_qty <= ($mrf_details[0]->mrf_quantity - $mrf_details[0]->left_quantity)){
               if(isset($request->job_id) && !empty($request->job_id)){
                    $mrf_store->job_id =$request->job_id;
                    $mrf_store->product_id =$request->mrf_id;
                    $mrf_store->booking_order_id =$mrf_details[0]->booking_order_id;
                    $mrf_store->erp_code =$request->erp_code;
                    $mrf_store->item_code = $request->item_code;
                    $mrf_store->item_quantity = $request->receive_qty;
                    $mrf_store->item_size = $mrf_details[0]->item_size;
                    $mrf_store->item_color = $mrf_details[0]->gmts_color;
                    $mrf_store->is_type =$request->is_type;
                    $mrf_store->shipment_date =$request->shipment_date;
                    $mrf_store->receive_date = Carbon\Carbon::now();
                    $mrf_store->user_id = Auth::user()->user_id;
                    $mrf_store->status = MrfFlugs::ACCEPTED_MAESSAGE;
                    $mrf_store->save(); 
               }
            }else{
                return redirect()->back()->withInput($request->input())->withErrors("Available Quantity is greater than input Quantity.");
            }

            return redirect()->back();
    }

    public function mrfList(){
        $mrfList = MxpStore::where(
            [   ['is_deleted', BookingFulgs::IS_NOT_DELETED ],
                ['is_type', 'mrf' ]
            ])
            ->get();
        return view('maxim.product.MrfAcceptList',compact('mrfList'));
    }
}
