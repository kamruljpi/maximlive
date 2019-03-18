<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\RoleManagement;
use App\Model\Product\ItemCostPrice;
use App\Model\MxpItemDescription;
use Illuminate\Http\Request;
use App\Model\MxpGmtsColor;
use App\MxpProductsColors;
use App\MxpSupplierPrice;
use App\MxpProductsSizes;
use App\MxpProductSize;
use App\VendorPrice;
use App\MxpProduct;
use Carbon\Carbon;
use App\userbuyer;
use App\MaxParty;
use App\MxpBrand;
use App\Supplier;
use Validator;
use App\buyer;
use Session;
use Auth;
use DB;

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
                $productValue->cost_price = ItemCostPrice::where('id_product',$productValue->product_id)->first();
            }
        }else {
            $products = MxpProduct::where('product_id',0)->paginate(20);
        }

        return view('product_management.product_list',compact('products'));
    }

    public function allProducts($productId = null){

        
        $buyerList = $this->getUserByerList();
        
        if(isset($buyerList) && !empty($buyerList)){
            if($productId == null ){
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->whereIn('id_buyer',$buyerList)->orWhere('id_buyer','')->orderBy('product_id','DESC')->paginate(20);
            }else{
                $proWithSizeColors = MxpProduct::with('colors', 'sizes')->where('product_id', $productId)->whereIn('id_buyer',$buyerList)->orderBy('product_id','DESC')->paginate(20);

                /** when product id_buyer field is empty
                 *  and need to update
                 *  only PID-1 & PID-2 user acces this Item
                 */
                if(empty($proWithSizeColors[0]->id_buyer)){
                    if(Auth::user()->email == 'PID-1bd@maxim-group.com' || Auth::user()->email == 'PID-2bd@maxim-group.com') {
                        $proWithSizeColors = MxpProduct::with('colors', 'sizes')->where('product_id', $productId)->orderBy('product_id','DESC')->paginate(20);
                    }
                }

                /** End**/
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

        $colors = MxpGmtsColor::where([['item_code',null],['status', '=', 1]])->groupBy('color_name')->get();

        $sizes = MxpProductSize::where([['product_code',''],['status', '=', 1]])->groupBy('product_size')->get();

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
        $product_code_ = $product[0]->product_code;    
        $sizesJs = [];
        $colorsJs = [];  
        if(empty($product_code_)){
            Session::flash('new_product_delete', "This Product Is not Available or You Don't have permission to edit this page.");
            return \Redirect()->Route('product_list_view');  
        }

        $sizes = MxpProductSize::where('product_code', '')->orWhere('product_code', $product_code_)->get();
        $allSizes = [];
        $allColors = [];

        if(isset($sizes) && !empty($sizes)){
            foreach ($sizes as $siz) { 
                $allSizes[$siz['proSize_id']]['proSize_id'] = $siz['proSize_id'];
                $allSizes[$siz['proSize_id']]['product_size'] = $siz['product_size'];
            }
        }else{
            $allSizes = array();
        }
        $colors = MxpGmtsColor::where('item_code', NULL)->orWhere('item_code',$product_code_)->get();

        if(isset($colors) && !empty($colors)){
            foreach ($colors as $colr) { 
                $allColors[$colr['id']]['id'] = $colr['id'];
                $allColors[$colr['id']]['color_name'] = $colr['color_name'];
            }
        }else{
            $allColors = array();
        }

        $xxsizes = [];
        $allSelectedSizes = [];
        $SelectedSizes = [];
        $xxcolors = [];
        $allSelectedColors = [];
        $SelectedColors = [];

        foreach ($product as $color){
            array_push($xxcolors, $color->colors);
            foreach ($color->colors as $data){
                $allSelectedColors[$data->color_id]['color_id'] = $data->color_id;
                $allSelectedColors[$data->color_id]['color_name'] = $data->color_name;
                $SelectedColors[] = $data->color_id;
            }
        }

        foreach ($product as $size){
            array_push($xxsizes, $size->sizes);
            foreach ($size->sizes as $data){
                $allSelectedSizes[$data->size_id]['size_id'] = $data->size_id;
                $allSelectedSizes[$data->size_id]['product_size'] = $data->product_size;
                $SelectedSizes[] = $data->size_id;
            }
        }
        if(isset($allSelectedSizes) && !empty($allSelectedSizes)){
            foreach ($allSelectedSizes as $ass) {
                $allSizes[$ass['size_id']]['proSize_id'] = $ass['size_id'];
                $allSizes[$ass['size_id']]['product_size'] = $ass['product_size'];
            }
        }
        if(isset($allSelectedColors) && !empty($allSelectedColors)){
            foreach ($allSelectedColors as $asc) {
                $allColors[$asc['color_id']]['id'] = $asc['color_id'];
                $allColors[$asc['color_id']]['color_name'] = $asc['color_name'];
            }
        }
        $sizes = $allSizes;
        $colors = $allColors;

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
             $vendorCompanyListPrice->missingParty = MaxParty::whereIn('id',$missing_vendor)->get();
        }

        /* end*/

        $supplierPrices = MxpSupplierPrice::with('supplier')->where('product_id', $request->product_id)->get();

        $vendorCompanyList = MaxParty::select('mxp_party.id', 'mxp_party.name', 'mxp_party.name_buyer')
                            ->leftJoin('mxp_vendor_prices', 'mxp_vendor_prices.party_table_id', '=', 'mxp_party.id')
                            ->where([['mxp_party.status', '=', 1],['id_buyer',$product[0]->id_buyer]])
                            ->where('mxp_vendor_prices.party_table_id', null)
                            ->get();

        if(count($vendorCompanyListPrice)==0){
            $vendorCompanyList = MaxParty::select('id', 'name', 'name_buyer')->where('id_buyer',$product[0]->id_buyer)->get()->sortBy('name_buyer');
        }

        // $supplierList = Supplier::select('suppliers.supplier_id', 'suppliers.name', 'suppliers.phone', 'suppliers.address', 'suppliers.status')
        //                 ->leftJoin('mxp_supplier_prices', 'mxp_supplier_prices.supplier_id', '=', 'suppliers.supplier_id')
        //                 ->where('suppliers.status', '=', 1)
        //                 ->where('suppliers.is_delete', '=', 0)
        //                 ->where('mxp_supplier_prices.supplier_id', null)
        //                 ->get();
        
        // if(count($supplierPrices) == 0){
        //     $supplierList = Supplier::get()->sortBy('name');
        // }

        $itemList = MxpItemDescription::where('is_active', '1')->get();

        foreach ($product as &$productValues) {
            $productValues->cost_price = ItemCostPrice::where('id_product',$productValues->product_id)->first();
        }

        /**
         *
         */
        $suppliers_id = [];
        if(!empty($supplierPrices)){
            foreach ($supplierPrices as $keys => $suppliers_) {
               $suppliers_id[$keys] = $suppliers_->supplier_id;
            }
        }
        
        $supplierList = Supplier::where('status', 1)
                        ->where('is_delete', 0)
                        ->whereNotIn('supplier_id',$suppliers_id)
                        ->get();

        //end

        $buyers = DB::table('mxp_buyer')->select('id_mxp_buyer','buyer_name')->orderBy('buyer_name', ASC)->get(); 

       return view('product_management.update_product', compact('product', 'vendorCompanyListPrice', 'supplierPrices', 'supplierList', 'vendorCompanyList',  'colors', 'sizes', 'colorsJs', 'sizesJs', 'buyers', 'allSelectedSizes', 'allSelectedColors', 'SelectedColors', 'SelectedSizes'))->with('brands',$brands)->with('itemList',$itemList);
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

        $str_price = str_replace('$', '', $request->p_unit_price);
        $p_unit_price = trim($str_price, '$');

        $createProduct = new MxpProduct();
        $createProduct->product_code = $request->p_code;
        $createProduct->product_name = $request->p_name;
        $createProduct->product_type = $request->product_type;
        $createProduct->product_description = $description_name->name;
        $createProduct->item_description_id = $request->p_description;
        $createProduct->brand = htmlspecialchars($buyer_name->buyer_name);
        $createProduct->erp_code = $request->p_erp_code;
        $createProduct->item_inc_percentage = $request->item_inc_percentage;
        $createProduct->unit_price = $p_unit_price;
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

            // $this->saveColor($colorData[1], $request->p_code);
        }

        for ($i=0; $i<count($request->sizes); $i++){
            $sizeData = explode(',', $request->sizes[$i]);
            $storeSize = new MxpProductsSizes();
            $storeSize->product_id = $lastProId;
            $storeSize->size_id = $sizeData[0];
            $storeSize->status = 1;
            $storeSize->save();

            // $this->saveSize($sizeData[1], $request->p_code);
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
            'p_code.unique' => 'Item Code has been entered before.',
            'p_erp_code.required' => 'ERP Code field is required.',

            ];
        $validator = Validator::make($request->all(), 
            [
            'p_code' => 'required|unique:mxp_product,product_code,'.$request->product_id.',product_id',
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

        $str_price = str_replace('$', '', $request->p_unit_price);
        $p_unit_price = trim($str_price, '$');

        $updateProduct = MxpProduct::find($request->product_id);
        $updateProduct->product_code = $request->p_code;
        $updateProduct->product_name = $request->p_name;
        $updateProduct->product_type = $request->product_type;
        $updateProduct->product_description = $description_name->name;
        $updateProduct->brand = $buyer_name->buyer_name;
        $updateProduct->erp_code = $request->p_erp_code;
        $updateProduct->item_inc_percentage = $request->item_inc_percentage;
        $updateProduct->item_description_id = $request->p_description;
        $updateProduct->unit_price = $p_unit_price;
        $updateProduct->weight_qty = $request->p_weight_qty;
        $updateProduct->weight_amt = $request->p_weight_amt;
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

        if(isset($request->colors)) {
            for ($i = 0; $i < count($request->colors); $i++) {
                $id = explode(',', $request->colors[$i])[0];
                $color_name = explode(',', $request->colors[$i])[1];
                $color = new MxpProductsColors();
                $color->product_id = $lastProductID;
                $color->color_id = $id;
                $color->status = 1;
                $color->save();

            }
        }
        
        MxpProductsSizes::where('product_id', $lastProductID)->delete();
        // MxpProductSize::where('product_code', $request->p_code)->delete();

        if (isset($request->sizes)) {
            for ($i = 0; $i < count($request->sizes); $i++) {

                $id = explode(',', $request->sizes[$i])[0];
                $size_name = explode(',', $request->sizes[$i])[1];
                $size = new MxpProductsSizes();
                $size ->product_id = $lastProductID;
                $size ->size_id = $id;
                $size ->status = 1;
                $size->save();

                // $this->saveSize($size_name , $request->p_code);
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
