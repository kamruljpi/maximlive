<?php

namespace App\Http\Controllers\taskController\Ipo;

use App\Http\Controllers\Message\ActionMessage;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MxpIpo;
use Validator;
use Auth;
use DB;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\taskController\Flugs\HeaderType;

class IpoController extends Controller
{
  public function ipoReportView(Request $request){
    $headerValue = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();
    $buyerDetails = DB::table("mxp_bookingbuyer_details")->where('booking_order_id',$request->bid)->get();
    $footerData =[];
    $ipoDetails = DB::table("mxp_ipo")->where([['ipo_id', $request->ipoid],['booking_order_id',$request->bid]])->get();
    return view('maxim.ipo.ipoBillPage', [
        'headerValue'  => $headerValue,
        'initIncrease' => $request->ipoIncrease,
        'buyerDetails' => $buyerDetails,
        'sentBillId'   => $ipoDetails,
        'footerData'   => $footerData
      ]
    );
  }

  function array_combine_($keys, $values){
      $result = array();
      foreach ($keys as $i => $k) {
          $result[$k][] = isset($values[$i]) ? $values[$i] : 0;
      }
      array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
      return  $result;
  }

    public function storeIpo(Request $request){

    $datas = $request->all();
    $errors = $this->checkQuantity($datas);
    if(!empty($errors)){
      return redirect()->back()->withInput($request->input())->withErrors($errors);
    }
    $booking_order_id = $request->booking_order_id;
    $allId = $datas['ipo_id'];
    $product_qty = $datas['product_qty'];
    $ipoIncrease = (!empty($datas['ipo_increase_percentage']) ? $datas['ipo_increase_percentage'] : 0);

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
        StatusMessage::create('erro_challan', 'Ops! Challan has been complte ');

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
          $getMrfDbvalue = DB::select(" select ipo_quantity from mxp_booking_challan where id ='".$key."'");
          foreach ($getMrfDbvalue as $Mrfvalue) {
              $mrfQuantityDb[$key] = explode(',', $Mrfvalue->ipo_quantity);
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
        $makeOneArray[$key]['ipo_quantity'] = $value;
      }
      foreach ($tempValues as $key => $value) {
        $makeOneArray[$key]['left_mrf_ipo_quantity'] = $value;
      }

      /** Quantity and Mrf value Insert **/
      foreach ($makeOneArray as $key => $minusValues) {
        $challanMinusValueInsert = MxpBookingChallan::find($key);
        $challanMinusValueInsert->left_mrf_ipo_quantity = $minusValues['left_mrf_ipo_quantity'];
        $challanMinusValueInsert->ipo_quantity = $minusValues['ipo_quantity'];
        $challanMinusValueInsert->update();
      }

      $cc = MxpIpo::select('ipo_id')->groupBy('ipo_id')->get();
      $cc = count($cc);
      $count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
      $id = "PO"."-";
      $date = date('dmY') ;
      $ipo_id = $id.$date."-".$count;

      $mainData = $this->increaseIpoValue($allId, $ipoIncrease,$mainData);

      foreach ($mainData as $key => $value) {
        $getBookingChallanValue = DB::table("mxp_booking_challan")->where('id',$key)->get();
        foreach ($getBookingChallanValue as $bookingChallanValue) {
            $createIpo                   = new MxpIpo();
            $createIpo->user_id          = Auth::user()->user_id;
            $createIpo->job_id           = $bookingChallanValue->job_id;
            $createIpo->ipo_id           = $ipo_id;
            $createIpo->booking_order_id = $bookingChallanValue->booking_order_id;
            $createIpo->erp_code         = $bookingChallanValue->erp_code;
            $createIpo->item_code        = $bookingChallanValue->item_code;
            $createIpo->item_size        = $bookingChallanValue->item_size;
            $createIpo->item_description = $bookingChallanValue->item_description;
            $createIpo->item_quantity    = $value['item_quantity'];
            $createIpo->initial_increase = $value['increaseValue'];
            $createIpo->item_price       = $bookingChallanValue->item_price;
            $createIpo->matarial         = $bookingChallanValue->matarial;
            $createIpo->gmts_color       = $bookingChallanValue->gmts_color;
            $createIpo->others_color     = $bookingChallanValue->others_color;
            $createIpo->orderDate        = $bookingChallanValue->orderDate;
            $createIpo->orderNo          = $bookingChallanValue->orderNo;
            $createIpo->shipmentDate     = $bookingChallanValue->shipmentDate;
            $createIpo->poCatNo          = $bookingChallanValue->poCatNo;
            $createIpo->ipo_quantity     = $value['item_quantity'];
            $createIpo->sku              = $bookingChallanValue->sku;
            $createIpo->status           = ActionMessage::CREATE;
            $createIpo->save();
        }
      }

      return \Redirect::route('refresh_ipo_view', ['ipo_id' => $ipo_id,'booking' => $booking_order_id]);
    }
    public function redirectIpoReport(Request $request)
    {
      $companyInfo = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();
      $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->booking)->first();
      $footerData =[];
      $ipoDetails = MxpIpo::join('mxp_booking as mp','mp.id','job_id')
                  ->select('mxp_ipo.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku')
                  ->where('ipo_id',$request->ipo_id)
                  ->get();
                  
      return view('maxim.ipo.ipoBillPage', [
          'companyInfo'  => $companyInfo,
          'initIncrease' => $request->ipoIncrease,
          'buyerDetails' => $buyerDetails,
          'ipoDetails'   => $ipoDetails,
          'footerData'   => $footerData
        ]
      );
    }

    public function increaseIpoValue(array $ipo_id = [], array $increase = [], array $maindata = []){
      $ipoAndIncreaseValue = [];
      $temp = $this->array_combine_ ($ipo_id ,$increase);
      foreach ($temp as $key => $values) {
        $ipoAndIncreaseValue[$key]['increaseValue']= (sizeof($temp[$key]) == 1)? $values :implode(',', $values);
      }
      foreach ($maindata as $keys => $valuess) {
        $ipoAndIncreaseValue[$keys]['item_quantity']= $valuess;
      }
      return $ipoAndIncreaseValue;
    }

    protected function checkQuantity($data){
      $errors = 'You try to invalid and grater than quantity.';
      $datas['ipo_id'] = $data['ipo_id'];
      $datas['product_qty'] = $data['product_qty'];
      $oneArray = [];
      foreach ($datas['ipo_id'] as $key => $value) {
        $oneArray[$key]['ipo_id'] = $value;
        $oneArray[$key]['product_qty'] = $datas['product_qty'][$key];
      }      
      // $this->print_me($oneArray);
      $dbValue = [];
      foreach ($oneArray as $key => $oneArrayValue) {
        $idstrcount = (8 - strlen($oneArrayValue['ipo_id']));
        $job_id_id = str_repeat('0',$idstrcount).$oneArrayValue['ipo_id'];
        $dbValue = MxpBookingChallan::where('job_id',$oneArrayValue['ipo_id'])->select('left_mrf_ipo_quantity')->first();

      // $this->print_me($dbValue->left_mrf_ipo_quantity);
        if($oneArrayValue['product_qty'] > $dbValue->left_mrf_ipo_quantity)
          $errors .= ' Job id '.$job_id_id.' available quantity '.$dbValue->left_mrf_ipo_quantity.' and entered quantity '.$oneArrayValue['product_qty'].'.';
      }
      if(isset($errors) && $errors === 'You try to invalid and grater than quantity.'){
        $errors = '';
      }
      return $errors;
    }
}