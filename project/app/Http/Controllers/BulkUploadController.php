<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Excel;
use Hash;
use Session;
use File;
use Redirect;
use DateTime;
use App\MxpProduct;
use Carbon\Carbon;
use App\MxpProductSize;
use App\Model\MxpGmtsColor;

class BulkUploadController extends Controller
{
    public function bulkUploadView(Request $request)
    {
       return view('bulk_upload.viewBulkUpload');
    }
    public function uploadactionview(Request $request)
    {
        $this->validate($request, array(
            'bulk'        => 'required'
        ));
        if($request->hasFile('bulk')){
            $extension = File::extension($request->file('bulk')->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file('bulk')->getRealPath();
                $data = Excel::load($path, function($reader) {
                })->get();
                
                $insert = [];
                $err_insert = [];
                $temp_insert = [];
                if(!empty($data) && $data->count()){
                    $i = 0;
                    foreach ($data as $ykey => $yvalue) {
                        if(!empty($yvalue)){
                            $temp_insert[] = self::generateArray($yvalue);
                        }
                    }
                }
                if(isset($temp_insert) && !empty($temp_insert)){
                    foreach ($temp_insert as $key_i => $temp_i) {
                        if(empty($temp_i)){
                            unset($temp_insert[$key_i]);
                        }
                    }
                }else{
                    Session::flash('bulkerror', 'Excel File Data is Empty.');
                    return back();
                }
                
                if(empty($temp_insert)){
                    Session::flash('bulkerror', 'Excel File Data is Empty.');
                    return back();
                }
                Session::flash('err_item_list', $temp_insert);
                $members_item_list  = json_encode($data);
                return view('bulk_upload.itemuploadactionview',['err_item_list'=>$temp_insert,'members_item_list'=>$members_item_list]);
                return back();
            }else {
                Session::flash('bulkerror', 'File is a '.$extension.' file.!! Please upload a valid xls file..!!');
                return back();
            }
        }
    }
    public function itemcodeValidate($item_code = null) 
    {
        if($item_code == null){
            return false;
        }
        $item_codeCheck = MxpProduct::get()->where('product_code', $item_code)->count();
        if ($item_codeCheck > 0) {
            return false;
        }else{
            return true;
        }
    }
    public function erpcodeValidate($erp_code = null) 
    {
        if($erp_code == null){
            return false;
        }
        $erp_codeCheck = MxpProduct::get()->where('erp_code', $erp_code)->count();
        if ($erp_codeCheck > 0) {
            return false;
        }else{
            return true;
        }
    }
    public function itemuploadactionfinal(Request $request)
    {
        $user_id = Auth::user()->user_id;
        $getBrand = $this->getBrand();
        $getItem_description = $this->getItem_description();
        $getGmts_Color = $this->getGmts_Color();
        $getProductSize = $this->getProductSize();
        $data_temp = $request->input('upload_item_members');
        if(!empty($data_temp)){
            $data__ = json_decode($data_temp);
        }else{
             $data__  = [];
        }
        $data = [];
        if(isset($data__) && !empty($data__)){
            foreach ($data__ as $dkey => $dvalue) {
                if(!empty($dvalue)){
                    $data[] = $dvalue;
                }
            }
        }
        $insert = [];
        $err_insert = [];
        if(!empty($data) && count($data) > 0){
            $i = 0;
            foreach ($data as $ykey => $yvalue) {
                if(!empty($yvalue->item_code)){
                    if(!empty(self::generateArray($yvalue))){
                        $temp_insert[] =  self::generateArray($yvalue);
                    }
                }
            }
        }
        $colors = [];
        $sizes = [];
        $items_lists = [];
        $itemcode_lists = [];
        $countc = 0;
        $counts = 0;
        if(!empty($data) && count($data) > 0){
            foreach ($data as $cskey => $csvalue) {
                if(isset($csvalue->item_code) && !empty($csvalue->item_code)){
                    $getGmts_Color = $this->getGmts_Color($csvalue->item_code);
                    $getProductSize = $this->getProductSize($csvalue->item_code);
                    if(isset($csvalue->color) && !empty($csvalue->color))
                    {
                        $csvalue_color = str_replace(", ",",",$csvalue->color);
                        $cscolor = explode(",",$csvalue_color);
                        if(isset($cscolor) && !empty($cscolor)){
                            foreach ($cscolor as $csclr) {
                                if(isset($csclr) && !empty($csclr)){
                                    if(!isset($getGmts_Color[strtolower($csclr)])){
                                        $colors[$countc]['color_name'] = $csclr;
                                        $colors[$countc]['status'] = 1;
                                        $colors[$countc]['item_code'] = $csvalue->item_code;
                                        $colors[$countc]['user_id'] = $user_id;
                                        $getGmts_Color[strtolower($csclr)] = $csclr;
                                        $countc++;
                                    }
                                }
                            }
                        }
                    }
                    if(isset($csvalue->size_range) && !empty($csvalue->size_range))
                    {
                        $csvalue_size_range = str_replace(", ",",",$csvalue->size_range);
                        $cssizes = explode(",",$csvalue_size_range);
                        if(isset($cssizes) && !empty($cssizes)){
                            foreach ($cssizes as $csszs) {
                                if(isset($csszs) && !empty($csszs)){
                                    if(!isset($getProductSize[strtolower($csszs)])){
                                        $sizes[$counts]['product_size'] = $csszs;
                                        $sizes[$counts]['status'] = 1;
                                        $sizes[$counts]['product_code'] = $csvalue->item_code;
                                        $sizes[$counts]['user_id'] = $user_id;
                                        $sizes[$counts]['action'] = 'create';
                                        $getProductSize[strtolower($csszs)] = $csszs;
                                        $counts++;
                                    }
                                }
                            }
                        }
                    }
                    if(isset($csvalue->item_code) && !empty($csvalue->item_code)){
                        $items_lists[strtolower($csvalue->item_code)] = $csvalue->item_code;
                    }
                    $getGmts_Color = array();
                    $getProductSize = array();
                }
            }
        }
        if(isset($colors) && !empty($colors)){
            DB::table('mxp_gmts_color')->insert($colors);
        }
        if(isset($sizes) && !empty($sizes)){
            DB::table('mxp_productsize')->insert($sizes);
        }
        $filters_array = self::unique_multidim_array($temp_insert,'item_code');
        $filters_array = self::unique_multidim_array($filters_array,'erp_code');
        if(count($filters_array) == count($temp_insert)){
            if(!empty($data) && count($data) > 0){
                $i = 0;
                // start item foreach
                foreach ($data as $xkey => $xvalue) {
                        if(isset($xvalue->item_code) && !empty($xvalue->item_code) && isset($xvalue->erp_code) && !empty($xvalue->erp_code)){
                            if(!$this->itemcodeValidate($xvalue->item_code) && !$this->erpcodeValidate($xvalue->erp_code)){
                                $err_insert[] = self::generateArray($xvalue,"Item Code & ERP Code Already Exist.");
                                continue;
                            }else if(!$this->itemcodeValidate($xvalue->item_code)) {
                                $err_insert[] = self::generateArray($xvalue,"Item Code Already Exist.");
                                continue;
                            }else if(!$this->erpcodeValidate($xvalue->erp_code)) {
                                $err_insert[] = self::generateArray($xvalue,"ERP Code Already Exist.");
                                continue;
                            }
                        }
                        if(isset($xvalue->item_code) && !empty($xvalue->item_code)){
                            $insert[$i]['product_code'] = $xvalue->item_code;
                        }
                        if(isset($xvalue->erp_code) && !empty($xvalue->erp_code)){
                            $insert[$i]['erp_code'] = $xvalue->erp_code;
                        }
                        if(isset($xvalue->unit_price)){
                            $insert[$i]['unit_price'] = $xvalue->unit_price;
                        }else{
                            $insert[$i]['unit_price'] = '';
                        }
                        if(isset($xvalue->unit)){
                            $insert[$i]['weight_qty'] = $xvalue->unit;
                        }else{
                            $insert[$i]['weight_qty'] = '';
                        }
                        if(isset($xvalue->item_size)){
                            $insert[$i]['item_size_width_height'] = $xvalue->item_size;
                        }else{
                            $insert[$i]['item_size_width_height'] = '';
                        }
                        if(isset($xvalue->item_color)){
                            $insert[$i]['other_colors'] = $xvalue->item_color;
                        }else{
                            $insert[$i]['other_colors'] = '';
                        }
                        if(isset($xvalue->weight_amount)){
                            $insert[$i]['weight_amt'] = $xvalue->weight_amount;
                        }else{
                            $insert[$i]['weight_amt'] = '';
                        }
                        if(isset($xvalue->product_status) && !empty($xvalue->product_status)){
                            if(strtolower($xvalue->product_status) == 'active' || strtolower($xvalue->product_status) == 'enable'){
                                $insert[$i]['status'] = 1;
                            }else{
                                $insert[$i]['status'] = 0;
                            }
                        }else{
                            $insert[$i]['status'] = 1;
                        }
                        if(isset($xvalue->material)){
                            $insert[$i]['material'] = $xvalue->material;
                        }else{
                            $insert[$i]['material'] = '';
                        }
                        if(isset($xvalue->brand)){
                            $brand = strtolower($xvalue->brand);
                            if(isset($getBrand[$brand])){
                                $insert[$i]['brand'] = $xvalue->brand;
                                $insert[$i]['id_buyer'] = $getBrand[$brand];
                            }else{
                                $insert[$i]['id_buyer'] = 0;
                                $insert[$i]['brand'] = '';
                            }
                        }else{
                            $insert[$i]['id_buyer'] = 0;
                            $insert[$i]['brand'] = '';
                        }
                        if(isset($xvalue->item_description)){
                            $item_description = strtolower(trim($xvalue->item_description));
                            if(isset($getItem_description[$item_description])){
                                $insert[$i]['product_description'] = trim($xvalue->item_description);
                                $insert[$i]['item_description_id'] = $getItem_description[$item_description];
                            }else{
                                $insert[$i]['product_description'] = '';
                                $insert[$i]['item_description_id'] = 0;
                            }
                        }else{
                            $insert[$i]['product_description'] = '';
                            $insert[$i]['item_description_id'] = 0;
                        }
                        $i = $i+1;
                    }
                //end item foreach
                    if(isset($insert) && !empty($insert)){
                        foreach ($insert as $insrtkey => &$insrt) {
                            if(empty($insrt['unit_price'])
                                 && empty($insrt['weight_qty'])
                                 && empty($insrt['item_size_width_height'])
                                 && ($insrt['id_buyer'] == 0)
                                 && empty($insrt['brand'])
                                 && empty($insrt['product_description'])
                                 && ($insrt['item_description_id'] == 0)
                        ){
                                unset($insert[$insrtkey]);
                            }else{
                                $insrt['user_id'] = Auth::user()->user_id;
                                $insrt['action'] = 'create';
                                
                            }
                        }
                    }
                if(isset($insert) && !empty($insert)){
                    if(DB::table('mxp_product')->insert($insert)){
                        if(isset($items_lists) && !empty($items_lists)){
                            foreach ($items_lists as $items_list) {
                                if($item_code = $this->getProductCode($items_list)){
                                    $itemcode_lists[strtolower($items_list)] = $item_code;
                                }
                            }
                        }

                        if(!empty($data) && count($data) > 0){
                            $itc = 0;
                            $its = 0;
                            $itco = 0;
                            $product_costs = [];
                            $product_colors = [];
                            $product_sizes = [];
                            foreach ($data as $xkey => $xvalue) {
                                if(isset($xvalue->item_code) && !empty($xvalue->item_code)){
                                    if(isset($itemcode_lists[strtolower($xvalue->item_code)])){
                                        $getGmts_Color = $this->getGmts_Color($xvalue->item_code);
                                        $getProductSize = $this->getProductSize($xvalue->item_code);
                                        $item_id = $itemcode_lists[strtolower($xvalue->item_code)];
                                        if(isset($xvalue->color) && !empty($xvalue->color)){
                                            $xvalue_color = str_replace(", ",",",$xvalue->color);
                                            $item_colors = explode(",", $xvalue_color);
                                            if(isset($item_colors) && !empty($item_colors)){
                                                foreach ($item_colors as $item_color) {
                                                    if(isset($getGmts_Color[strtolower($item_color)])){
                                                        $product_colors[$itc]['color_id'] = $getGmts_Color[strtolower($item_color)];
                                                        $product_colors[$itc]['product_id'] = $item_id;
                                                        $product_colors[$itc]['status'] = 1;
                                                        $itc++;
                                                    }
                                                }
                                            }
                                        }

                                        if(isset($xvalue->size_range) && !empty($xvalue->size_range)){
                                            $xvalue_size_range = str_replace(", ", ",", $xvalue->size_range);
                                            $item_sizes = explode(",", $xvalue_size_range);
                                            if(isset($item_sizes) && !empty($item_sizes)){
                                                foreach ($item_sizes as $item_size) {
                                                    if(isset($getProductSize[strtolower($item_size)])){
                                                        $product_sizes[$itc]['size_id'] = $getProductSize[strtolower($item_size)];
                                                        $product_sizes[$itc]['product_id'] = $item_id;
                                                        $product_sizes[$itc]['status'] = 1;
                                                        $itc++;
                                                    }
                                                }
                                            }
                                        }
                                        if(isset($xvalue->cost_price) && !empty($xvalue->cost_price)){
                                            $xvalue_cost_price = str_replace(", ", ",", $xvalue->cost_price);
                                            $cost_prices = explode(",", $xvalue_cost_price);
                                            if(isset($cost_prices[0]) &!empty($cost_prices[0])){
                                                $product_costs[$itco]['price_1'] = $cost_prices[0];
                                            }else{
                                                $product_costs[$itco]['price_1'] = 0;
                                            }
                                            if(isset($cost_prices[1]) &!empty($cost_prices[1])){
                                                $product_costs[$itco]['price_2'] = $cost_prices[1];
                                            }else{
                                                $product_costs[$itco]['price_2'] = 0;
                                            }
                                            $product_costs[$itco]['id_product'] = $item_id;
                                            $product_costs[$itco]['last_action'] = 'create';
                                            $product_costs[$itco]['user_id'] = $user_id;
                                            $itco++;
                                        }
                                        $getGmts_Color = array();
                                        $getProductSize = array();
                                    }
                                }
                            }
                            if(isset($product_costs) && !empty($product_costs)){
                                DB::table('mxp_item_cost_price')->insert($product_costs);
                            }
                            if(isset($product_colors) && !empty($product_colors)){
                                DB::table('mxp_products_colors')->insert($product_colors);
                            }
                            if(isset($product_sizes) && !empty($product_sizes)){
                                DB::table('mxp_products_sizes')->insert($product_sizes);
                            }
                        }
                        if(isset($err_insert) && !empty($err_insert)){
                            Session::flash('bulkerror', 'Successfully Inserted Partial data');
                            Session::flash('err_item_list', $err_insert);
                            return Redirect::route('itemupload')->with(['err_item_list', $err_insert]);
                        }else{
                            Session::flash('bulksuccess', 'Successfully Inserted All Data');
                            return Redirect::route('itemupload');
                        }
                        
                    }else{
                        Session::flash('bulkerror', 'Somethings Wrong !!');
                        if(isset($err_insert) && !empty($err_insert)){
                            Session::flash('err_item_list', $err_insert);
                            return Redirect::route('itemupload')->with(['err_item_list', $err_insert]);
                        }else{
                            return Redirect::route('itemupload')->with(['bulkerror', "Something Wrong!!!"]);
                        }
                    }
                }elseif (isset($err_insert) && !empty($err_insert)) {
                    Session::flash('bulkerror', 'Somethings Wrong !!');
                    Session::flash('err_item_list', $err_insert);
                    return Redirect::route('itemupload')->with(['err_item_list', $err_insert]);
                }
            }
        }else{
            Session::flash('bulkerror', 'There It Has Some Of Duplicate Erp code and Item Code..!!');
        }
        return Redirect::route('itemupload');
    }
    public static function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if(isset($val[$key])){
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $temp_array[$i] = $val;
                }
                $i++;
            }
        }
        return $temp_array;
    }
    public static function generateArray($values = null,$error = null){
        $results = array();
        if($values == null){
            return $results;
        }
        if(isset($values) && !empty($values)){
            foreach ($values as $key => $value) {
                if(!empty($value)){
                    $results[$key] = $value;
                }
            }
        }
        if(isset($error) && !empty($error)){
            $results['error'] = $error;
        }
        return $results;
    }
    public function getBrand()
    {
        $brands_ = DB::table('mxp_buyer')->get();
        $brands = array();
        if(isset($brands_) && !empty($brands_)){
            foreach ($brands_ as $brand) {
                if(isset($brand->buyer_name) && !empty($brand->buyer_name) && isset($brand->id_mxp_buyer) && !empty($brand->id_mxp_buyer)){
                    $brands[strtolower($brand->buyer_name)] = $brand->id_mxp_buyer;
                }
            }
        }
        return $brands;
    }
    public function getItem_description()
    {
        $itemdescriptions_ = DB::table('mxp_item_description')->where('is_active', 1)->get();
        $itemdescriptions = array();
        if(isset($itemdescriptions_) && !empty($itemdescriptions_)){
            foreach ($itemdescriptions_ as $itemdescription) {
                if(isset($itemdescription->name) && !empty($itemdescription->name) && isset($itemdescription->id) && !empty($itemdescription->id)){
                    $itemdescriptions[strtolower($itemdescription->name)] = $itemdescription->id;
                }
            }
        }
        return $itemdescriptions;
    }
    public function getGmts_Color($item_code = null)
    {
        if($item_code == null){
            $gmtscolors_ = DB::table('mxp_gmts_color')->where('status', 1)->get();
        }else{
            $gmtscolors_ = MxpGmtsColor::where('item_code', '')->orWhere('item_code',$item_code)->get();
        }
        $gmtscolors = array();
        if(isset($gmtscolors_) && !empty($gmtscolors_)){
            foreach ($gmtscolors_ as $gmtscolor) {
                if(isset($gmtscolor->color_name) && !empty($gmtscolor->color_name) && isset($gmtscolor->id) && !empty($gmtscolor->id)){
                    $gmtscolors[strtolower($gmtscolor->color_name)] = $gmtscolor->id;
                }
            }
        }
        return $gmtscolors;
    }
    public function getProductSize($item_code = null)
    {

        if($item_code == null){
            $productsizes_ = DB::table('mxp_productsize')->where('status', 1)->get();
        }else{
            $productsizes_ = MxpProductSize::where('product_code', '')->orWhere('product_code', $item_code)->get();
        }
        
        $productsizes = array();
        if(isset($productsizes_) && !empty($productsizes_)){
            foreach ($productsizes_ as $productsize) {
                if(isset($productsize->product_size) && !empty($productsize->product_size) && isset($productsize->proSize_id) && !empty($productsize->proSize_id)){
                    $productsizes[strtolower($productsize->product_size)] = $productsize->proSize_id;
                }
            }
        }
        return $productsizes;
    }
    public function getProductCode($item_code = null)
    {
        if($item_code == null)
            return false;
        $products_ = DB::table('mxp_product')->select('product_id')->where('product_code', $item_code)->get();
        $products = array();
        if(isset($products_) && !empty($products_)){
            foreach ($products_ as $product) {
                if(isset($product->product_id)){
                    return $product->product_id;
                }else{
                    return false;
                }
            }
        }
        return false;
    }
}
