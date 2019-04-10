<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Carbon;
use Auth;
use DB;
use Excel;
use File;
use Redirect;
use App\MxpProduct;
use App\Model\MxpBookingBuyerDetails;
use App\MxpDraft;
use App\MxpProductSize;
use App\Model\MxpGmtsColor;

class BulkBookingController extends Controller
{
	public function bookingBulkUpload(Request $request) {
		$this->validate($request, array(
		    'booking_bulk_file'        => 'required'
		));
		$buyerdetails = json_decode($request->input('buyerDetails'));
		if(isset($buyerdetails[0]->id)){
			$id_vendor = $buyerdetails[0]->id;
		}else{
			$id_vendor = 0;
		}
		if(isset($buyerdetails[0]->id_buyer)){
			$id_buyer = $buyerdetails[0]->id_buyer;
		}else{
			$id_buyer = 0;
		}
		if(isset($buyerdetails[0]->sort_name)){
			$sort_name = $buyerdetails[0]->sort_name;
		}else{
			$sort_name = '';
		}
		if($request->hasFile('booking_bulk_file')){
		    $extension = File::extension($request->file('booking_bulk_file')->getClientOriginalName());
		    if ($extension == "xlsx" || $extension == "xls") {
		        $path = $request->file('booking_bulk_file')->getRealPath();
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
		            return redirect('dashboard_view')->with('bulkerror', 'Excel File Data is Empty.');
		        }
		        if(empty($temp_insert)){
		            Session::flash('bulkerror', 'Excel File Data is Empty.');
		            return redirect('dashboard_view')->with('bulkerror', 'Excel File Data is Empty.');
		        }
		        Session::flash('err_item_list', $temp_insert);
		        $members_item_list  = json_encode($data);
		        return view('bulk_upload.orderuploadactionview',['err_item_list'=>$temp_insert,'members_item_list'=>$members_item_list,'id_buyer'=>$id_buyer,'id_vendor'=>$id_vendor,'sort_name'=>$sort_name]);
		    }else {

		        Session::flash('bulkerror', 'File is a '.$extension.' file.!! Please upload a valid xls file..!!');
		        return redirect('dashboard_view')->with('bulkerror', 'File is a '.$extension.' file.!! Please upload a valid xls file..!!');
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
	public function orderuploadactionfinal(Request $request)
	{
		$getMaskingFields = self::getMaskingFields();
	    $user_id = Auth::user()->user_id;
	    $getGmts_Color = $this->getGmts_Color();
	    $getProductSize = $this->getProductSize();
	    $id_buyer = $request->input('id_buyer');
	    $id_vendor = $request->input('id_vendor');
	    $companySortName = $request->input('sort_name');
	    $getDefaultFieldsValue = $this->getDefaultFieldsValue($id_buyer,$id_vendor);
	    $data_temp = $request->input('upload_item_members');
	    // start generating booking
		    $cc_1 = MxpBookingBuyerDetails::count();
		    $cc_2 = MxpDraft::select('booking_order_id')->groupBy('booking_order_id')->get();
		    $cc_3 = count($cc_2);
		    $cc = $cc_1 + $cc_3;
		    $count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
		    $id = "BK"."-";
		    $date = date('dmY');
		    $company_sort_name = str_replace('/', '', $companySortName);
		    $company_sort_name =  trim($company_sort_name, '/');
		    $booking_order_id = $id.$date."-".$company_sort_name."-".$count;
	    // end generating booking
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
	    $not_found_items = [];
	        if(!empty($data) && count($data) > 0){
	            $i = 0;
	            // start item foreach
	            foreach ($data as $xkey => $xvalue) {
	            		if(isset($xvalue->item_code) && !empty($xvalue->item_code)){
	            			$item_details = MxpProduct::where('product_code',$xvalue->item_code)->first();
	            			if(isset($item_details) && !empty($item_details)){
	            				if(isset($xvalue->gmts_item_color) && isset($xvalue->item_size)){
	            						if((!empty($xvalue->gmts_item_color) && isset($getGmts_Color[strtolower($xvalue->gmts_item_color)])) || empty($xvalue->gmts_item_color)){
	            							if((!empty($xvalue->item_size) && isset($getProductSize[strtolower($xvalue->item_size)])) || empty($xvalue->item_size)){
						            				if(isset($item_details->item_size_width_height) && !empty($item_details->item_size_width_height)){
						            					$insert[$i]['item_size_width_height'] = $item_details->item_size_width_height;
						            				}else{
						            					$insert[$i]['item_size_width_height'] = '';
						            				}
					        						if(isset($booking_order_id) && !empty($booking_order_id)){
					        							$insert[$i]['booking_order_id'] = $booking_order_id;
					        						}
					        				        if(isset($getMaskingFields) && !empty($getMaskingFields)){
					        				        	foreach ($getMaskingFields as $maskKey => $maskField) {
					        				        		if(isset($xvalue->{$maskField})){
					        				        		    $insert[$i][$maskKey] = $xvalue->{$maskField};
					        				        		}else{
					        				        		    $insert[$i][$maskKey] = '';
					        				        		}
					        				        	}
					        				        }
					        				        if(isset($getDefaultFieldsValue) && !empty($getDefaultFieldsValue)){
					        				        	foreach ($getDefaultFieldsValue as $maskdefKey => $maskdefField) {
					        				        		if(isset($maskdefField)){
					        				        		    $insert[$i][$maskdefKey] = $maskdefField;
					        				        		}else{
					        				        		    $insert[$i][$maskdefKey] = '';
					        				        		}
					        				        	}
					        				        }
					        				        $i = $i+1;
	            							}else{
	            								$not_found_items[] = $xvalue->item_size.' size not exist for item: '.$xvalue->item_code;
	            							}
	            						}else{
	            							$not_found_items[] = $xvalue->gmts_item_color.' color not exist for item: '.$xvalue->item_code;
	            						}
	            				}
	            			}else{
	            				$not_found_items[] = $xvalue->item_code.' Item not exists.';
	            			}
	            		}
	                }
	                $err_messages = '';
	                if(!empty($not_found_items)){
	                	$err_messages .= implode(", <br>", $not_found_items);
	                }
	
	            //end item foreach
	            if(isset($insert) && !empty($insert)){
	                if(DB::table('mxp_draft')->insert($insert)){
	                	Session::flash('bulkerror', 'Partial data has been imported !!');
	                	if(!empty($not_found_items)){
	                		return Redirect::route('getDraft',['id' => $booking_order_id])->with(['err_messages', $err_messages]);
	                	}else{
	                		return Redirect::route('getDraft',['id' => $booking_order_id]);
	                	}
	                }else{
	                    Session::flash('bulkerror', 'Somethings Wrong !!');
	                    return Redirect::route('dashboard_view');
	                }
	            }else{
	            	Session::flash('bulkerror', 'Somethings Wrong !!');
	            	return Redirect::route('dashboard_view');
	            }
	        }
	    return Redirect::route('itemupload');
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
    public static function getMaskingFields()
    {
    	return array(
    		'poCatNo' => 'po_cat_no',
    		'oos_number' => 'oos_no',
    		'item_code' => 'item_code',
    		'erp_code' => 'erp_code',
    		'item_description' => 'item_description',
    		'gmts_color' => 'gmts_item_color',
    		'item_size' => 'item_size',
    		'style' => 'style',
    		'sku' => 'sku',
    		'item_quantity' => 'item_qty',
    		'item_price' => 'item_price',
    	);
    }
    public function getDefaultFieldsValue($id_buyer,$id_vendor)
    {
    	return array(
    		'user_id' => Auth::user()->user_id,
    		'id_buyer' => $id_buyer,
    		'vendor_id' => $id_vendor,
    		'is_deleted' => 0,
    		'last_action_at' => 'create',
    		'deleted_user_id' => 0,
    		'orderDate' => Carbon::now()->format('d-m-Y'),
    		'deleted_date_at' => '',
    		'season_code' => '',
    		'shipmentDate' => '',
    		'booking_category' => '',
    		'is_pi_type' => 'unstage',
    		'is_type' => 'general',
    		'material' => '',
    		'orderNo' => '',
    		'others_color' => '',
    	);
    }
}