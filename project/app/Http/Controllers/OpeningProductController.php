<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MxpStore;
use App\MxpWarehouseType;
use App\Model\Location\MxpLocation;
use Carbon;
use Validator;
use Auth;
use Session;

class OpeningProductController extends Controller
{
    public function index(){
    	$product = $this->getProduct($type=0);
    	$location = $this->getLocation();
    	$warehouse = $this->getWarehouseType($type='in');
    	$details = [];
    	return view('opening_stock.create_opening_product',compact('details','product','location','warehouse'));
    }

    public function productStore(Request $request)
    {
    		$validMessages = [
    		      'product_id.required' => 'Product Id field is required.',
    		      'warehouse.required' => 'warehouse field is required',
    		      'location.required' => 'location field is required',
    		      ];
    		$datas = $request->all();

    		$validator = Validator::make($datas, 
    		      [
    		    'product_id' => 'required',
    		    'location' => 'required',
    		    'warehouse' => 'required',
    		  ],
    		      $validMessages
    		  );

    		if ($validator->fails()) {
    		  return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
    		}

    		
	        if(isset($request->product_id) && !empty($request->product_id)){

	            MxpStore::where('store_id', $request->product_id)
	            	->update([
	            		'stock_type' => 1,
	            		'location_id' => $request->location,
	            		'warehouse_type_id' => $request->warehouse,
	            		'warehouse_entry_date' => Carbon\Carbon::now() ,
	            		'warehouse_user_id' => Auth::user()->user_id ,
	            	]); 
	            Session::flash('store','PSE-'.$request->product_id." Product Successfully stored.");
        	}else{
        		Session::flash('delete',"Opps something went wrong.");
        	}

    	return redirect()->back();
    }

    public function getProduct($stock_type, $product_stype=null ){

    	if($product_stype==null){
    		$product = MxpStore::where([
    				['stock_type', $stock_type],
    				['is_deleted', 0]
				])
    			->where(function($is_type){
    				$is_type->where('is_type', 'ipo')->orwhere('is_type', 'mrf');
    			})
    			->paginate(10);
    			
    	}
    	elseif($product_stype){
    		$product = MxpStore::where([
    				['stock_type', $stock_type],
    				['is_deleted', 0],
    				['is_type', $product_stype]
				])
    		->paginate(10);
    	}else{
    		return "ops Something went wrong.";
    	}
    	
		return $product;
    }

    public function getLocation(){
    	$location = MxpLocation::where([
    					['status', 1],
    					['is_deleted', 0],
    				])
    				->get();
    	return $location;
    }

    public function getWarehouseType( $type ){

    	$warehouse = MxpWarehouseType::where([
    			['warehouse_in_out_type', $type],
    			['status', 1],
    			['is_deleted', 0],
    		])
    		->get();
		return $warehouse;
    }

    public function storedProduct(){
    	$product = $this->getProduct($stock_type=1);
    	foreach ($product as &$value) {
    		$value->location = MxpLocation::findOrFail( $value->location_id);
    		$value->warehouse = MxpWarehouseType::findOrFail( $value->warehouse_type_id);
    	}
    	// $this->print_me($product);
    	return view('opening_stock.stored_product', compact('product'));
    }
}
