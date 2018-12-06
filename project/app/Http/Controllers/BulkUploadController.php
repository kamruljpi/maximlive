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

        $getBrand = $this->getBrand();
        $getItem_description = $this->getItem_description();
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
                if(!empty(self::generateArray($yvalue))){
                    $temp_insert[] =  self::generateArray($yvalue);
                }
            }
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
                            $item_description = strtolower($xvalue->item_description);
                            if(isset($getItem_description[$item_description])){
                                $insert[$i]['product_description'] = $xvalue->item_description;
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
                                $insrt['status'] = 1;
                            }
                        }
                    }
                if(isset($insert) && !empty($insert)){
                    if(DB::table('mxp_product')->insert($insert)){
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
            Session::flash('bulkerror', 'There It Has Some Of Duplicate Mobile or Email or NID..!!');
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
}
