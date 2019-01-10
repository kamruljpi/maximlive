<?php

namespace App\Http\Controllers;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\OpeningProductController;
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

class OpenningProductSearch extends Controller
{
	public function filterStoredProduct(Request $request) {
        $filter = OpeningProductController::filterOptionValue();
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

        $this->print_me($request->all());
    }
}