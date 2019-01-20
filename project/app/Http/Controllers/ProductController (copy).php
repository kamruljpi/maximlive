<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\RoleManagement;
use App\MaxParty;
use App\MxpProduct;
use App\MxpBrand;
use App\MxpProductsColors;
use App\MxpSupplierPrice;
use App\Supplier;
use App\VendorPrice;
use App\buyer;
use App\Model\MxpItemDescription;
use Auth;
use Illuminate\Http\Request;
use Validator;
use App\Model\MxpGmtsColor;
use App\MxpProductSize;
use App\MxpProductsSizes;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Supplier\SupplierController;
use App\userbuyer;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Model\Product\ItemCostPrice;
use Session;

class ProductController extends Controller
{
    use UserAccessBuyerList;
    
    const CREATE_PRODUCT = "create";
    const UPDATE_PRODUCT = "update";
	const ACTIVE_BRAND = 1;

    Public function productList(){
        $products = $this->allProducts();
        if(isset($products) && !empty($products)){   
            foreach ($products as &$productValue) {
                $productValue->description = MxpItemDescription::where('id',$productValue->item_description_id)->first();
            }
        }
        if(empty($products)){
            $products = MxpProduct::where('product_id',0)->paginate(20);
        }
    	return view('product_management.product_list',compact('products'));
    }

    public function allProducts($productId = null){
        $buyerList = $this->getUserByerList();

        if(isset($buyerList) && !empty($buyerList)){
            if($productId == null ){
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->whereIn('id_buyer',$buyerList)->orderBy('product_id','DESC')->paginate(20);
            }else{
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->where('product_id', $productId)->whereIn('id_buyer',$buyerList)->orderBy('product_id','DESC')->paginate(20);
            }
        }else if(Auth::user()->type == 'super_admin'){
            if($productId == null ){
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->orderBy('product_id','DESC')->paginate(20);
            }else{
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->where('product_id', $productId)->orderBy('product_id','DESC')->paginate(20);
            }
        }

        $i=0;
        foreach ($proWithSizeColors as $proWithSizeColor) {
            $j = 0;
            foreach ($proWithSizeColor->colors as $colorids) {
                $proWithSizeColors[$i]->colors[$j]->setAttribute('color_name', MxpGmtsColor::select('color_name')->where('id', '=', $colorids->color_id)->get()[0]->color_name);
                $j++;
            }

            $j = 0;
            foreach ($proWithSizeColor->sizes as $sizeids) {
                $proWithSizeColors[$i]->sizes[$j]->setAttribute('product_size', MxpProductSize::select('product_size')->where('proSize_id', '=', $sizeids->size_id)->get()[0]->product_size);
                $j++;
            }

            $i++;
        }

        return  $proWithSizeColors;
    }

    Public function addProductListView(){

        $brands = MxpBrand::where('status', self::ACTIVE_BRAND)->get();
    	$colors = MxpGmtsColor::where('status', '=', 1)->get();
        $sizes = MxpProductSize::where('status', '=', 1)->get();
        $vendorCompanyList = MaxParty::select('id', 'name', 'name_buyer')->get()->sortBy('name_buyer');
        $supplierList = Supplier::where('status', 1)
                        ->where('is_delete', 0)
                        ->get();
        $itemList = MxpItemDescription::where('is_active', '1')->get();
        $buyers = DB::table('mxp_buyer')->select('id_mxp_buyer','buyer_name')->orderBy('buyer_name', ASC)->get();

       return view('product_management.add_product',compact('brands', 'colors', 'sizes', 'vendorCompanyList', 'supplierList','itemList','buyers'));
    }

    Public function updateProductView(Request $request){
        $brands = MxpBrand::where('status', self::ACTIVE_BRAND)->get();
        $product = $this->allProducts($request->product_id);
        $product_code_ = $product[0]->product_code ;        

        // $sizes = MxpProductSize::where('product_code', '')->get();
        // $colors = MxpGmtsColor::where('item_code', NULL)->get();

        $sizes = MxpProductSize::where('product_code', '')->orWhere('product_code', $product_code_)->get();
        $colors = MxpGmtsColor::where('item_code', NULL)->orWhere('item_code',$product_code_)->get();

        // $this->print_me($sizes);

        $sizesJs = [];
        $colorsJs = [];

        foreach ($product as $color){
            foreach ($color->colors as $data){

                array_push($colorsJs, $data->color_id.','.$data->color_name);
            }
        }

        foreach ($product as $size){
            foreach ($size->sizes as $data){
                array_push($sizesJs, $data->size_id.','.$data->product_size);
            }
        }

        $vendorCompanyListPrice = VendorPrice::with('party')->where('product_id', $request->product_id)->get();

        /* find missing vendor in vendor list*/
        $array1 = [];
        $array2 = [];

        $party_id_product_wise = MaxParty::select('id')->where('id_buyer',$product[0]->id_buyer)->get();

        if(isset($party_id_product_wise) && !empty($party_id_product_wise))
        {
            foreach ($party_id_product_wise as $value_product_wise) {
                $array1[] = $value_product_wise->id;
            }    
        }                  
        if(isset($vendorCompanyListPrice) && !empty($vendorCompanyListPrice)){
            foreach ($vendorCompanyListPrice as $value) {
                $array2[] = $value->party->id;
            }    
        }
        
        $missing_vendor = array_diff($array1,$array2);

        if(isset($missing_vendor) && !empty($missing_vendor)){
            foreach ($missing_vendor as $key => $missing_vendor_value) {
                # code...
            }
            $vendorCompanyListPrice->missingParty = MaxParty::where('id',$missing_vendor_value)->get();
        }

        /* end*/

        // $this->print_me($vendorCompanyListPrice);
        $supplierPrices = MxpSupplierPrice::with('supplier')->where('product_id', $request->product_id)->get();

        $vendorCompanyList = MaxParty::select('mxp_party.id', 'mxp_party.name', 'mxp_party.name_buyer')
                            ->leftJoin('mxp_vendor_prices', 'mxp_vendor_prices.party_table_id', '=', 'mxp_party.id')
                            ->where([['mxp_party.status', '=', 1],['id_buyer',$product[0]->id_buyer]])
                            ->where('mxp_vendor_prices.party_table_id', null)
                            ->get();

        if(count($vendorCompanyListPrice)==0){
            $vendorCompanyList = MaxParty::select('id', 'name', 'name_buyer')->where('id_buyer',$product[0]->id_buyer)->get()->sortBy('name_buyer');
        }

        $supplierList = Supplier::select('suppliers.supplier_id', 'suppliers.name', 'suppliers.phone', 'suppliers.address', 'suppliers.status')
                        ->leftJoin('mxp_supplier_prices', 'mxp_supplier_prices.supplier_id', '=', 'suppliers.supplier_id')
                        ->where('suppliers.status', '=', 1)
                        ->where('suppliers.is_delete', '=', 0)
                        ->where('mxp_supplier_prices.supplier_id', null)
                        ->get();

        $itemList = MxpItemDescription::where('is_active', '1')->get();

        if(count($supplierPrices) == 0){
            $supplierList = Supplier::get()->sortBy('name');
        }

        foreach ($product as &$productValues) {
            $productValues->cost_price = ItemCostPrice::where('id_product',$productValues->product_id)->first();
        }
        $buyers = DB::table('mxp_buyer')->select('id_mxp_buyer','buyer_name')->orderBy('buyer_name', ASC)->get(); 

        // $this->print_me($product);
       return view('product_management.update_product', compact('product', 'vendorCompanyListPrice', 'supplierPrices', 'supplierList', 'vendorCompanyList',  'colors', 'sizes', 'colorsJs', 'sizesJs', 'buyers'))->with('brands',$brands)->with('itemList',$itemList);
    }

    Public function addProduct(Request $request){
        $item_size_width_height = '';
        // if(isset($request->item_size_width) && isset($request->item_size_height)){
            $item_size_width_height = (!empty($request->item_size_width) ? $request->item_size_width :'0') .'-'. (!empty($request->item_size_height) ? $request->item_size_height :'0');            
        // }

    	$roleManage = new RoleManagement();

        $validMessages = [
            'p_code.required' => 'Item Code field is required.',
            'product_type.required' => 'Product Type is required.',
            'p_code.unique' => 'Item Code has been entered before.',
            'p_erp_code.required' => 'ERP Code field is required.',
            'p_unit_price.required' => 'Unit Price field is required.',
            'p_weight_qty.required' => 'Weight Qty field is required.',
            'p_weight_qty.integer' => 'Weight Qty field is required.',
            'p_weight_amt.required' => 'Weight Amt field is required.',
            'p_weight_amt.integer' => 'Weight Amt field is required.',
            'id_buyer.required' => 'Brand field is required.',
            // 'p_brand.required' => 'Brand field is required.'
            ];
        $datas = $request->all();
    	$validator = Validator::make($datas,
            [
    			'p_code' => 'required|unique:mxp_product,product_code',
    			'p_erp_code' => 'required',
    			'id_buyer' => 'required'
    			// 'p_brand' => 'required'
		    ],
            $validMessages
        );

		if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		$validationError = $validator->messages();
        $description_name = MxpItemDescription::where('id',$request->p_description)->select('name')->first();
        $buyer_name = buyer::where('id_mxp_buyer',$request->id_buyer)->select('buyer_name')->first();
    	$createProduct = new MxpProduct();
    	$createProduct->product_code = $request->p_code;
    	$createProduct->product_name = $request->p_name;
        $createProduct->product_type = $request->product_type;
    	$createProduct->product_description = $description_name->name;
        $createProduct->item_description_id = $request->p_description;
    	$createProduct->brand = htmlspecialchars($buyer_name->buyer_name);
    	$createProduct->erp_code = $request->p_erp_code;
    	$createProduct->item_inc_percentage = $request->item_inc_percentage;
    	$createProduct->unit_price = $request->p_unit_price;
    	$createProduct->weight_qty = $request->p_weight_qty;
    	$createProduct->weight_amt = $request->p_weight_amt;
        $createProduct->user_id = Auth::user()->user_id;
        $createProduct->status = $request->is_active;
        $createProduct->id_buyer = $request->id_buyer;
        $createProduct->other_colors = isset($request->other_colors) ? $request->other_colors :'';
        $createProduct->material = isset($request->material) ? $request->material :'';
        $createProduct->item_size_width_height = $item_size_width_height;
    	$createProduct->action = self::CREATE_PRODUCT;
    	$createProduct->save();

        $lastProId = $createProduct->product_id;
        $this->addVendorPrice($request, $lastProId);
        SupplierController::saveSupplierProductPrice($request, $lastProId);

       for ($i=0; $i<count($request->colors); $i++){

           $colorData = explode(',', $request->colors[$i]);

           $storeColor = new MxpProductsColors();
            $storeColor->product_id = $lastProId;
            $storeColor->color_id = $colorData[0];
            $storeColor->status = 1;
            $storeColor->save();

            $this->saveColor($colorData[1], $request->p_code);
        }

        for ($i=0; $i<count($request->sizes); $i++){

           $sizeData = explode(',', $request->sizes[$i]);

            $storeSize = new MxpProductsSizes();
            $storeSize->product_id = $lastProId;
            $storeSize->size_id = $sizeData[0];
            $storeSize->status = 1;
            $storeSize->save();

            $this->saveSize($sizeData[1], $request->p_code);
        }

        ItemCostPrice::create([
            'id_product'    => $lastProId,
            'user_id'       => Auth::user()->user_id,
            'price_1'       => $request->cost_price_1,
            'price_2'       => $request->cost_price_2,
            'last_action'   => self::CREATE_PRODUCT
        ]);

		StatusMessage::create('new_product_create', $request->p_code .' New product Created Successfully');
		Session::flash('item_id', $lastProId);
		return \Redirect()->Route('product_list_view');    	
    }

    public function updateProduct(Request $request){  
        $item_size_width_height = '';
        // if(isset($request->item_size_width) && isset($request->item_size_height)){
            $item_size_width_height = (!empty($request->item_size_width) ? $request->item_size_width :'0') .'-'. (!empty($request->item_size_height) ? $request->item_size_height :'0');            
        // }
        // $this->print_me($item_size_width_height);
        $this->addVendorPrice($request, $request->product_id);

        SupplierController::updateProductPrice($request);
        
    	$roleManage = new RoleManagement();

        $validMessages = [
            'p_code.required' => 'The Product Code field is required.',
            'p_erp_code.required' => 'ERP Code field is required.',

            ];
    	$validator = Validator::make($request->all(), 
            [
			'p_code' => 'required',
			'p_erp_code' => 'required',

		   ],
           $validMessages
        );

		if ($validator->fails()) {
			return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
		}

		$validationError = $validator->messages();

        $description_name = MxpItemDescription::where('id',$request->p_description)->select('name')->first();
        $buyer_name = buyer::where('id_mxp_buyer',$request->id_buyer)->select('buyer_name')->first();
    	$updateProduct = MxpProduct::find($request->product_id);
    	$updateProduct->product_code = $request->p_code;
    	$updateProduct->product_name = $request->p_name;
        $updateProduct->product_type = $request->product_type;
   	    $updateProduct->product_description = $description_name->name;
    	$updateProduct->brand = $buyer_name->buyer_name;
    	$updateProduct->erp_code = $request->p_erp_code;
        $updateProduct->item_inc_percentage = $request->item_inc_percentage;
        $updateProduct->item_description_id = $request->p_description;
    	$updateProduct->unit_price = $request->p_unit_price;
    	$updateProduct->weight_qty = $request->p_weight_qty;
    	$updateProduct->weight_amt = $request->p_weight_amt;
    	// $updateProduct->description_1 = $request->p_description1;
    	// $updateProduct->description_2 = $request->p_description2;
    	// $updateProduct->description_3 = $request->p_description3;
        // $updateProduct->description_4 = $request->p_description4;
        $updateProduct->user_id = Auth::user()->user_id;
        $updateProduct->status = $request->is_active;
        $updateProduct->id_buyer = $request->id_buyer;
        $updateProduct->other_colors = isset($request->other_colors) ? $request->other_colors :'';
        $updateProduct->material = isset($request->material) ? $request->material :'';
    	$updateProduct->item_size_width_height = $item_size_width_height;
        $updateProduct->action = self::UPDATE_PRODUCT;
    	$updateProduct->save();
        $lastProductID = $updateProduct->product_id;

        MxpProductsColors::where('product_id', $lastProductID)->delete();
        MxpGmtsColor::where('item_code', $request->p_code)->delete();

        if(isset($request->colors)) {
            for ($i = 0; $i < count($request->colors); $i++) {
                $id = explode(',', $request->colors[$i])[0];
                $color_name = explode(',', $request->colors[$i])[1];
                $color = new MxpProductsColors();
                $color->product_id = $lastProductID;
                $color->color_id = $id;
                $color->status = 1;
                $color->save();

                $this->saveColor($color_name, $request->p_code);
            }
        }
        
        MxpProductsSizes::where('product_id', $lastProductID)->delete();
        MxpProductSize::where('product_code', $request->p_code)->delete();

        if (isset($request->sizes)) {
            for ($i = 0; $i < count($request->sizes); $i++) {

                $id = explode(',', $request->sizes[$i])[0];
                $size_name = explode(',', $request->sizes[$i])[1];
                $size = new MxpProductsSizes();
                $size ->product_id = $lastProductID;
                $size ->size_id = $id;
                $size ->status = 1;
                $size->save();

                $this->saveSize($size_name , $request->p_code);
            }
        }
        $cost_table_value = DB::table('mxp_item_cost_price')->where('id_product',$request->product_id)->get();

        if(empty($cost_table_value[0]->id_product)){
            ItemCostPrice::create([
                'id_product'    => $request->product_id,
                'user_id'       => Auth::user()->user_id,
                'price_1'       => $request->cost_price_1,
                'price_2'       => $request->cost_price_2,
                'last_action'   => self::CREATE_PRODUCT
            ]);
        }else{
            ItemCostPrice::where('id_product',$request->product_id)->update([
                'user_id'     => Auth::user()->user_id,
                'price_1'     => $request->cost_price_1,
                'price_2'     => $request->cost_price_2,
                'last_action' => self::UPDATE_PRODUCT
            ]);
        }        

		StatusMessage::create('update_product_create', 'Item No ( '.$lastProductID .' ) Successfully update');
		
		Session::flash('item_id', $lastProductID);

        $url = $request->only('redirects_to');
        return redirect()->to($url['redirects_to']);

		// return \Redirect()->Route('product_list_view');
    }

    public function deleteProduct(Request $request) {
		$product = MxpProduct::find($request->product_id);
		$product->delete();
		StatusMessage::create('new_product_delete',$product->product_name .' delete Successfully');
		return redirect()->Route('product_list_view');
	}


    public function saveColor($color, $productCode){
        $insertGmtsColor = new MxpGmtsColor();
        $insertGmtsColor->user_id = Auth::user()->user_id;
       $insertGmtsColor->item_code = $productCode;
        $insertGmtsColor->color_name = $color;
        $insertGmtsColor->action = 'create';
        $insertGmtsColor->status = 1;
        $insertGmtsColor->save();
        return 0;
    }
    public function saveSize($size, $productCode){
        $createSize = new MxpProductSize();
        $createSize->user_id = Auth::user()->user_id;
       $createSize->product_code = $productCode;
        $createSize->product_size = $size;
        $createSize->status = 1;
        $createSize->action = 'create';
        $createSize->save();
        return 0;
    }

    public function addVendorPrice(Request $request, $productId){

        for($i=0; $i<count($request->party_table_id); $i++){

            if(count(VendorPrice::find($request->price_id[$i])) > 0){
                $sPrice = VendorPrice::find($request->price_id[$i]);
            }else{
                $sPrice = new VendorPrice();
            }
            $sPrice->party_table_id = $request->party_table_id[$i];
            $sPrice->product_id = $productId;
            $sPrice->vendor_com_price = $request->v_com_price[$i];
            $sPrice->save();

        }
        return true;
    }

}