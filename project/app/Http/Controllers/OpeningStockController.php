<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OpeningProductController;
use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use Illuminate\Http\Request;
use App\MxpProductsColors;
use App\MxpProductsSizes;
use App\MxpProduct;
use App\MxpStore;
use Validator;
use Auth;
use DB;

class OpeningStockController extends Controller
{
	public function index() {
		$items = MxpProduct::where('status', 1)->select('product_id','product_code')->get();
    	$locations = OpeningProductController::getLocation();
    	$warehouses = OpeningProductController::getWarehouseType($type='in');
		$details = [];
		return view('opening_stock.create_opening_stock',compact('details','locations','warehouses','items'));
	}

	public function store(Request $request) {

		$datas = $request->all();
		$validMessages = [
            'item_code.required' => 'Item Code field is required.',
            'color.required' => 'Color field is required.',
            'size_range.required' => 'Size Range field is required.',
            'quantity.required' => 'Quantity field is required.',
            'location_id.required' => 'Location field is required.',
            'id_warehouse_type.required' => 'Warehouse type field is required.',
            ];
    	$validator = Validator::make($datas, 
            [
    			'item_code' => 'required',
    			// 'color' => 'required',
    			'size_range' => 'required',
    			'quantity' => 'required',
    			'location_id' => 'required',
    			'id_warehouse_type' => 'required',
		    ],
            $validMessages
        );

        if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		$store = new MxpStore();
		$store->user_id = Auth::user()->user_id;
		$store->item_code = $request->item_code;
		$store->item_size = $request->size_range;
		$store->item_color = $request->item_color;
		$store->item_quantity = $request->quantity;

		$store->warehouse_type_id = $request->warehouse_type_id;
		$store->location_id = $request->location_id;

		$store->last_action_at = 'create';
		$store->is_type = 'opening_stock';
		$store->stock_type = 1;
		$store->status = 1;
		$store->save();

		StatusMessage::create('store', 'New Opening Stock Created Successfully');

		return \Redirect()->Route('opening_stock_view');
	}

	/**
	 * @return string
	 */
	public function getColorSizeByitemCode(Request $request) {
		$item_id = (DB::table('mxp_product')->where('product_code',$request->item_code)->select('product_id')->first())->product_id;

		$item_colors = MxpProductsColors::join('mxp_gmts_color as mgc','mgc.id','mxp_products_colors.color_id')
							->select('mgc.color_name')
							->where([['product_id',$item_id],['mxp_products_colors.status',1]])
							->get();

		$item_sizes = MxpProductsSizes::join('mxp_productsize as mps','mps.proSize_id','mxp_products_sizes.size_id')
							->select('mps.product_size')
							->where([['product_id',$item_id],['mxp_products_sizes.status',1]])
							->get();

		$html['colors'] = $this->optionHtmlMaker('color_name',$item_colors);
		$html['sizes'] = $this->optionHtmlMaker('product_size',$item_sizes);

		return $html;
	}

	/**
	 * @pram $keys select field
	 * @pram $datas get value
	 * @return string
	 */
	public function optionHtmlMaker($keys , $datas = []) {
		$html = '';
		$html .= '<option value=" "> -- Select -- </option>';
		if(!empty($datas)) {
			foreach ($datas as $data) {				
				$html .= '<option value="'.$data[$keys].'">'.$data[$keys].'</option>';
			}
		}

		return $html;
	}
	
}