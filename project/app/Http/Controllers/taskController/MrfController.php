<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleManagement;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use App\Model\MxpBookingChallan;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;
use App\Notification;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;

class MrfController extends Controller
{
  const CREATE_MRF = "create";
  const UPDATE_MRF = "update";
  const OPEN_MRF = "Open";

  function array_combine_($keys, $values)
  {
      $result = array();
      foreach ($keys as $i => $k) {
          $result[$k][] = isset($values[$i]) ? $values[$i] : 0;
      }
      array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
      return  $result;
  }
    public function addMrf(Request $request){

      $datas = $request->all();
      // $this->print_me($datas);
      $errors = $this->checkQuantity($datas);
      if(!empty($errors)){
        return redirect()->back()->withInput($request->input())->withErrors($errors);
      }
      $booking_order_id = $request->booking_order_id;
      $allId = $datas['allId'];
      $product_qty = $datas['product_qty'];

       /**
      - This Array most important to create challan
      - and update BookingChallan table
      **/
        $combineMrfInputAndDb= '';
        $tempValue = [];
        $tempMrfValue = [];
        $quantity = [];
        $dbValue = [];
        $finalData = [];
        $tempValues = [];

      /**
      - This section check to one booking Challan value
      - empty or not empty ( this mean challan complte).
      - If empty all value then redirect create challan page.
      **/

      $length = sizeof($product_qty);
      $count = 0;
      foreach ($product_qty as $value) {
        if($value == 0 || $value < 0 ){
          $count++;
        }
      }

      if($count == $length){
        StatusMessage::create('erro_challan', 'Ops! MRF booking order value Empty ');

        return \Redirect()->Route('dashboard_view');
      }

      /**
      - This Section create to concat all Get input
      - value by item id and store $tempValue Array.
      **/

      $temp = $this->array_combine_ ($allId ,$product_qty);


      /**
      - This Section add new MRF qty + DB MRF qty.
      **/

      $mrfQuantityDb = [];
      foreach ($temp as $key => $value) {
        $getMrfDbvalue = DB::select(" select mrf_quantity from mxp_booking_challan where id ='".$key."'");
        foreach ($getMrfDbvalue as $Mrfvalue) {
          $mrfQuantityDb[$key] = explode(',', $Mrfvalue->mrf_quantity);
        }
      }
      // self::print_me($mrfQuantityDb);
       $mrfInputValues = [];
        foreach ($temp as $key => $tempsValue) {
          if(sizeof($tempsValue) >1){
            foreach ($tempsValue as $tempItem) {
              $mrfInputValues[] = $tempItem;
            }
          }else{
            $mrfInputValues[] = $tempsValue;
          }
        }

        $mrfDbQty = [];
        foreach ($mrfQuantityDb as $key => $mrfDb) {

          if(sizeof($mrfDb) > 1){
            foreach ($mrfDb as $mrfDbItems) {
              $mrfDbQty[] = $mrfDbItems;
            }
          }else{
            foreach ($mrfDb as $valuess) {
            $mrfDbQty[] = $valuess;
            }
          }
        }

      foreach ($mrfQuantityDb as $mrfQuantity) {
        foreach ($mrfQuantity as $mrf) {

            if(empty($mrf)){
              foreach ($temp as $key => $value) {
                if(sizeof($value) > 1){
                  $tempValue[$key]= implode(',', $value);
                }else{
                  $tempValue[$key] = $value;
                }
              }
            }else{
               $combineMrfInputAndDb = $this->array_combine_($mrfInputValues,$mrfDbQty);
            }
        }
      }
      // self::print_me($combineMrfInputAndDb);
      if(!empty($combineMrfInputAndDb)){

          foreach ($combineMrfInputAndDb as $mrfInputValuesKeys => $mrfDbQtys) {
            if(sizeof($mrfDbQtys) > 1){
              foreach ($mrfDbQtys as $mrfQtys) {
               $tempMrfValue[] = $mrfQtys + $mrfInputValuesKeys;
              }
            }else{
            $tempMrfValue[] = $mrfDbQtys + $mrfInputValuesKeys;  //finalMrfData[] is same as twoArray[]
          }
        }

        $InputMrfAndDbMrfValue = $this->array_combine_($allId,$tempMrfValue);

          foreach ($InputMrfAndDbMrfValue as $key => $value) {
              if(sizeof($value) > 1){
                $tempValue[$key]= implode(',', $value);
              }else{
                $tempValue[$key] = $value;
              }
            }
      }


      /**
        - End Section.
      **/

      /**
        - This section most importent to update all array
        - value. Becouse this section create to array_combine
        - database primary id.
        - @param $maindata
      **/

      $inputMrfValue = $this->array_combine_($allId,$product_qty);
      foreach ($inputMrfValue as $key => $value) {
          if(sizeof($value) > 1){
            $mainData[$key]= implode(',', $value);
          }else{
            $mainData[$key] = $value;
          }
        }
      // $one_uniq = array_unique($allId);
      // $mainData = array_combine($one_uniq, $tempValue);



      /** This code only for mxp_booking_Challan Table update **/

      foreach($tempValue as $key => $value){
        $findChallanUpdate = DB::select(" select left_mrf_ipo_quantity from mxp_booking_challan where id ='".$key."'");
        
        foreach ($findChallanUpdate as $challanValues) {
          $quantity[] = explode(',', $challanValues->left_mrf_ipo_quantity);
        }
      }

      foreach ($quantity as $key => $value) {
        foreach ($value as $item) {
          $dbValue[] = $item;
        }
      }

      $combineUpdateDatas = $this->array_combine_($product_qty,$dbValue);

      foreach ($combineUpdateDatas as $keys => $datas) {
        if(sizeof($datas) > 1){
          foreach ($datas as $value) {
           $finalData[] = $value - $keys;
          }
        }else{
        $finalData[] = $datas - $keys;  //finalData[] is same as twoArray[]
      }
    }
      $tempp = $this->array_combine_($allId, $finalData);
      foreach ($tempp as $key => $value) {
          if(sizeof($value) > 1){
            $tempValues[$key] = implode(',', $value);
          }else{
            $tempValues[$key] = $value;
          }
      }

      // self::print_me($tempValue);

      $makeOneArray = [];
      foreach ($tempValue as $key => $value) {
        $makeOneArray[$key]['mrf_quantity'] = $value;
      }
      foreach ($tempValues as $key => $value) {
        $makeOneArray[$key]['left_mrf_ipo_quantity'] = $value;
      }

      /** Quantity and Mrf value Insert **/
      foreach ($makeOneArray as $key => $minusValues) {
        $challanMinusValueInsert = MxpBookingChallan::find($key);
        $challanMinusValueInsert->left_mrf_ipo_quantity = $minusValues['left_mrf_ipo_quantity'];
        $challanMinusValueInsert->mrf_quantity = $minusValues['mrf_quantity'];
        $challanMinusValueInsert->update();
      }

      $cc = MxpMrf::select('mrf_id')->groupBy('mrf_id')->get();
      $cc = count($cc);
      $count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
      $id = "MRF"."-";
      $date = date('dmY') ;
      $mrf_id = $id.$date."-".$count;

      foreach ($mainData as $key => $value) {
        $getBookingChallanValue = DB::table("mxp_booking_challan")->where('id',$key)->get();
        foreach ($getBookingChallanValue as $bookingChallanValue) {
            $insertMrfValue = new MxpMrf();
            $insertMrfValue->job_id = $bookingChallanValue->job_id;
            $insertMrfValue->user_id = Auth::user()->user_id;
            $insertMrfValue->mrf_id = $mrf_id;
            $insertMrfValue->supplier_id= $request->supplier_id;
            $insertMrfValue->booking_order_id = $bookingChallanValue->booking_order_id;
            $insertMrfValue->mrf_person_name = $request->mrf_person_name;
            $insertMrfValue->erp_code = $bookingChallanValue->erp_code;
            $insertMrfValue->item_code = $bookingChallanValue->item_code;
            $insertMrfValue->item_size = $bookingChallanValue->item_size;
            $insertMrfValue->item_quantity = $bookingChallanValue->left_mrf_ipo_quantity;
            $insertMrfValue->mrf_quantity = $value;
            $insertMrfValue->item_price = $bookingChallanValue->item_price;
            $insertMrfValue->matarial = $bookingChallanValue->matarial;
            $insertMrfValue->gmts_color = $bookingChallanValue->gmts_color;
            $insertMrfValue->orderDate = Carbon::now()->format('d-m-Y');
            $insertMrfValue->orderNo = $bookingChallanValue->orderNo;
            $insertMrfValue->shipmentDate = $request->mrf_shipment_date;
            $insertMrfValue->poCatNo = $bookingChallanValue->poCatNo;
            $insertMrfValue->item_description = $bookingChallanValue->item_description;
            // $insertMrfValue->status = $bookingChallanValue->status;
            $insertMrfValue->action = self::CREATE_MRF;
            $insertMrfValue->mrf_status = MrfFlugs::OPEN_MRF;
            $insertMrfValue->job_id_current_status = MrfFlugs::JOBID_CURRENT_STATUS_OPEN;
            $insertMrfValue->save();
        }
      }
      
      NotificationController::postNotification(Notification::CREATE_MRF, $mrf_id);  

      return \Redirect::route('refresh_mrf_view', ['mrf_id' => $mrf_id,'booking' => $booking_order_id]);
      
    }

    public function redirectMrfReport(Request $request){
      $footerData =[];
      $companyInfo = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();
      $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->booking)->first();
      $mrfDeatils = MxpMrf::join('mxp_booking as mp','mp.id','job_id')
                      ->select('mxp_mrf_table.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku','mp.item_size_width_height')
                      ->where('mrf_id',$request->mrf_id)
                      ->get();
      return view('maxim.mrf.mrfReportFile',compact('mrfDeatils','companyInfo','buyerDetails','footerData'));
    }

    protected function checkQuantity($data){
      $errors = 'You try to invalid and grater than quantity.';
      $datas['ipo_id'] = $data['allId'];
      $datas['product_qty'] = $data['product_qty'];
      $oneArray = [];
      foreach ($datas['ipo_id'] as $key => $value) {
        $oneArray[$key]['ipo_id'] = $value;
        $oneArray[$key]['product_qty'] = $datas['product_qty'][$key];
      }
      $dbValue = [];
      foreach ($oneArray as $key => $oneArrayValue) {
        $idstrcount = (8 - strlen($oneArrayValue['ipo_id']));
        $job_id_id = str_repeat('0',$idstrcount).$oneArrayValue['ipo_id'];
        $dbValue = MxpBookingChallan::where('job_id',$oneArrayValue['ipo_id'])->select('left_mrf_ipo_quantity')->first();

        if($oneArrayValue['product_qty'] > $dbValue->left_mrf_ipo_quantity)
          $errors .= ' Job id '.$job_id_id.' available quantity '.$dbValue->left_mrf_ipo_quantity.' and entered quantity '.$oneArrayValue['product_qty'].'.';
      }
      if(isset($errors) && $errors === 'You try to invalid and grater than quantity.'){
        $errors = '';
      }
      return $errors;
    }
}
