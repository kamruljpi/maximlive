<?php

namespace App\Http\Controllers\taskController;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Http\Controllers\dataget\ListGetController;
use App\Http\Controllers\Message\StatusMessage;
use App\Http\Controllers\RoleManagement;
use App\Model\MxpBookingBuyerDetails;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingChallan;
use Illuminate\Http\Request;
use App\Model\MxpMrf;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;

class MrfListController extends Controller
{
    use UserAccessBuyerList;

    public function mrfListView(){
        $bookingList = DB::table('mxp_mrf_table')
            ->select('*',DB::Raw('sum(mrf_quantity) as mrf_quantity'))
            ->where('is_deleted',BookingFulgs::IS_NOT_DELETED)
            ->groupBy('mrf_id')
            ->orderBy('id','DESC')
            ->paginate(15);

        $this->addBuyerDetails($bookingList);

        // $this->print_me($bookingList);

        return view('maxim.mrf.list.mrfList',compact('bookingList'));
    }

    public function showMrfReport(Request $request){
        $mrfDeatils = MxpMrf::join('mxp_booking as mp','mp.id','job_id')
                        ->select('mxp_mrf_table.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku','mp.item_size_width_height')
                        ->where([
                            ['mrf_id',$request->mid],
                            ['mxp_mrf_table.is_deleted',BookingFulgs::IS_NOT_DELETED]
                        ])
                        ->get();
        $companyInfo = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();
        $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->bid)->first();
        $footerData =[];
        return view('maxim.mrf.mrfReportFile',compact('mrfDeatils','companyInfo','buyerDetails','footerData'));
    }

    public function getMrfListByMrfId(Request $request){

        $mrfList = DB::table('mxp_mrf_table')
            ->where([
                ['mrf_id', 'like', '%'.$request->mrf_id.'%'],
                ['mxp_mrf_table.is_deleted',BookingFulgs::IS_NOT_DELETED]
            ])
            ->groupBy('mrf_id')
            ->orderBy('id','DESC')
            ->get();

        return $mrfList;
    }

    public function getMrfListBySearch(Request $request){

        $mrfList = DB::table('mxp_mrf_table');
        $checkValidation = false;

        if($request->booking_id_search != '')
        {
            $checkValidation = true;
            $mrfList->where('booking_order_id','like','%'.$request->booking_id_search.'%');
        }

        if($request->mrf_status != '')
        {
            $checkValidation = true;
            $mrfList->where('mrf_status','like','%'.$request->mrf_status.'%');
        }
        if($request->from_create_date_search != '' && $request->to_create_date_search != '')
        {
            $checkValidation = true;
            if($request->from_create_date_search == $request->to_create_date_search)
                $mrfList->whereDate('created_at', $request->from_create_date_search);
            else
                $mrfList->whereDate('created_at','>=',$request->from_create_date_search)->whereDate('created_at','<=',$request->to_create_date_search);
        }
        if($request->from_shipment_date_search != '' && $request->to_shipment_date_search != '')
        {
            $checkValidation = true;
            if($request->from_shipment_date_search == $request->to_shipment_date_search)
                $mrfList->whereDate('shipmentDate', $request->from_shipment_date_search);
            else
                $mrfList->whereDate('shipmentDate','>=',$request->from_shipment_date_search)->whereDate('shipmentDate','<=',$request->to_shipment_date_search);
        }

        if($checkValidation)
        {
            $mrfs = $mrfList->groupBy('mrf_id')->orderBy('id','DESC')->get();
            return $mrfs;
        }
        else
            return '';
    }

    /**
     *
     * @return array()
     */

    public function getMrfAllId() {
      $results = array();
      $mrfDetails = MxpMrf::where('is_deleted',BookingFulgs::IS_NOT_DELETED)
                    ->select('mrf_id')
                    ->groupBy('mrf_id')
                    ->orderBy('id',DESC)
                    ->get();

      if(isset($mrfDetails) && !empty($mrfDetails[0]->mrf_id)) {
          foreach ($mrfDetails as $orderKey => $mrf_value) {
              
              $results[]['mrf_id'] = $mrf_value->mrf_id;
          }
      }
      return json_encode($results);
    }
}


