<?php

namespace App\Http\Controllers;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Message\StatusMessage;
use App\Model\Location\MxpLocation;
use Illuminate\Http\Request;
use App\MxpWarehouseType;
use App\MxpProduct;
use App\MxpStore;
use Validator;
use Session;
use Carbon;
use Auth;

class OpeningProductController extends Controller
{
    public function index(){
    	$product = $this->getProduct($type=0);
    	$location = $this->getLocation();
    	$warehouse = $this->getWarehouseType($type='in');
    	$details = [];
    	return view('opening_stock.create_opening_product',compact('details','product','location','warehouse'));
    }

    public function filterOptionValue() {
        $filter['items'] = MxpProduct::where('status', 1)->select('product_id','product_code')->get();
        $filter['location'] = MxpLocation::where('status', 1)->select('id_location','location')->get();
        $filter['in_type'] = MxpWarehouseType::where([['warehouse_in_out_type','in'],['status', 1]])->select('id_warehouse_type','warehouse_type')->get();

        return $filter;
    }

    /**
     * @return void
     */
    public function joiningValue($product){
        if(isset($product) && !empty($product)) {
            foreach ($product as &$value) {
                $value->location = (MxpLocation::find( $value->location_id))->location;
                $value->warehouse = (MxpWarehouseType::find( $value->warehouse_type_id))->warehouse_type;
            }
        }
    }

    public function storedItem(){
        $filter = $this->filterOptionValue();
        $product = $this->getProduct($stock_type=1, $product_stype='opening_stock');
        $this->joiningValue($product);
        return view('opening_stock.stored_item', compact('product','filter'));
    }

    public function filterStoredItem(Request $request) {
        $filter = $this->filterOptionValue();
        $item_code = isset($request->item_code) ? $request->item_code : '';
        $location_id = isset($request->location_id) ? $request->location_id : '';
        $id_warehouse_type = isset($request->id_warehouse_type) ? $request->id_warehouse_type : '';
        $receive_from_date = isset($request->receive_from_date) ? $request->receive_from_date : '';
        $receive_to_date = isset($request->receive_to_date) ? $request->receive_to_date : '';
        $shipment_from_date = isset($request->shipment_from_date) ? $request->shipment_from_date : '';
        $shipment_to_date = isset($request->shipment_to_date) ? $request->shipment_to_date : '';

        $filter_v['item_code'] = $item_code;
        $filter_v['location_id'] = $location_id;
        $filter_v['id_warehouse_type'] = $id_warehouse_type;
        $filter_v['receive_from_date'] = $receive_from_date;
        $filter_v['receive_to_date'] = $receive_to_date;
        $filter_v['shipment_from_date'] = $shipment_from_date;
        $filter_v['shipment_to_date'] = $shipment_to_date;

        $store_table = MxpStore::where([['mxp_store.is_deleted',BookingFulgs::IS_NOT_DELETED],['mxp_store.stock_type',1],['is_type','opening_stock']]);

        if(!empty($item_code) || !empty($location_id) || !empty($id_warehouse_type) || !empty($receive_from_date) || !empty($receive_to_date) || !empty($shipment_from_date) || !empty($shipment_to_date) ) {

            if(!empty($item_code) && empty($location_id) && empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('item_code',$item_code)->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && !empty($location_id) && empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('location_id',$location_id)->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && empty($location_id) && !empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('warehouse_type_id',$id_warehouse_type)->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && empty($location_id) && empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->paginate(20);

                /** 
                 *  if database date format different in get
                 *  input value format then use the hidden code and 
                 *   change format
                 */

                // $product = $store_table->whereDate('receive_date','>=',date("d-m-Y", strtotime($receive_from_date)))
                //     ->whereDate('receive_date','<=',date("d-m-Y",strtotime($receive_to_date)))
                //     ->paginate(20);

                /**End don't remove**/

                $this->joiningValue($product);

            }else if(empty($item_code) && empty($location_id) && empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && !empty($shipment_from_date) && !empty($shipment_to_date)) {

                $product = $store_table->whereDate('shipment_date','>=',$shipment_from_date)
                    ->whereDate('shipment_date','<=',$shipment_to_date)
                    ->paginate(20);

                /** 
                 *  if database date format different in get
                 *  input value format then use the hidden code and 
                 *   change format
                 */

                // $this->print_me('ddddd');
                // $product = $store_table->whereDate('shipment_date','>=',date("d-m-Y", strtotime($shipment_from_date)))
                //     ->whereDate('shipment_date','<=',date("d-m-Y",strtotime($shipment_to_date)))
                //     ->paginate(20);

                /**End don't remove**/

                $this->joiningValue($product);

            }else if(!empty($item_code) && !empty($location_id) && empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where([['item_code',$item_code],['location_id',$location_id]])->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && !empty($location_id) && !empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where([['warehouse_type_id',$id_warehouse_type],['location_id',$location_id]])->paginate(20);
                $this->joiningValue($product);

            }else if(!empty($item_code) && empty($location_id) && !empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where([['item_code',$item_code],['warehouse_type_id',$id_warehouse_type]])->paginate(20);
                $this->joiningValue($product);

            }else if(!empty($item_code) && !empty($location_id) && !empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where([['item_code',$item_code],['location_id',$location_id],['warehouse_type_id',$id_warehouse_type]])->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && empty($location_id) && empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && !empty($shipment_from_date) && !empty($shipment_to_date)) {

                $product = $store_table->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->whereDate('shipment_date','>=',$shipment_from_date)
                    ->whereDate('shipment_date','<=',$shipment_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else if(!empty($item_code) && !empty($location_id) && !empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && !empty($shipment_from_date) && !empty($shipment_to_date)) {

                $product = $store_table->where([['item_code',$item_code],['location_id',$location_id],['warehouse_type_id',$id_warehouse_type]])
                    ->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->whereDate('shipment_date','>=',$shipment_from_date)
                    ->whereDate('shipment_date','<=',$shipment_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else if(!empty($item_code) && empty($location_id) && empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('item_code',$item_code)
                    ->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && !empty($location_id) && empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('location_id',$location_id)
                    ->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else if(empty($item_code) && empty($location_id) && !empty($id_warehouse_type) && !empty($receive_from_date) && !empty($receive_to_date) && empty($shipment_from_date) && empty($shipment_to_date)) {

                $product = $store_table->where('warehouse_type_id',$id_warehouse_type)
                    ->whereDate('receive_date','>=',$receive_from_date)
                    ->whereDate('receive_date','<=',$receive_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else if(!empty($item_code) && empty($location_id) && empty($id_warehouse_type) && empty($receive_from_date) && empty($receive_to_date) && !empty($shipment_from_date) && !empty($shipment_to_date)) {

                $product = $store_table->where('item_code',$item_code)
                    ->whereDate('shipment_date','>=',$shipment_from_date)
                    ->whereDate('shipment_date','<=',$shipment_to_date)
                    ->paginate(20);
                $this->joiningValue($product);

            }else {

                $product = $store_table->where('item_code','')->paginate(20);
            }

            return view('opening_stock.stored_item', compact('product','filter','filter_v'));
        }else {

            StatusMessage::create('messages', 'Please select a option');

            return \Redirect()->Route('stored_item');
        }
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
