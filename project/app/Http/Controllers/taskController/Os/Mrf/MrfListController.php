<?php
namespace App\Http\Controllers\taskController\Os\Mrf;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMrf;
use Auth;
use DB;

class MrfListController extends Controller
{
	public function mrfListView(){
        $bookingList = MxpMrf::select('*',DB::Raw('sum(mrf_quantity) as mrf_quantity'))
            ->groupBy('mrf_id')
            ->orderBy('id','DESC')
            ->paginate(15);
        return view('maxim.os.mrf.list.mrfList',compact('bookingList'));
    }

    public function showMrfReport(Request $request){
        $mrfDeatils = MxpMrf::join('mxp_booking as mp','mp.id','job_id')
                        ->select('mxp_mrf_table.*','mp.season_code','mp.oos_number','mp.style','mp.item_description','mp.sku')
                        ->where('mrf_id',$request->mid)
                        ->get();
        $companyInfo = DB::table("mxp_header")->where('header_type',HeaderType::COMPANY)->get();
        $buyerDetails = MxpBookingBuyerDetails::where('booking_order_id',$request->bid)->first();
        $footerData =[];
        return view('maxim.os.mrf.mrfReportFile',compact('mrfDeatils','companyInfo','buyerDetails','footerData'));
    }

    public function detailsViewForm(Request $request){

        $bookingDetails = MxpBookingBuyerDetails::with('bookings', 'ipo', 'mrf')
                            ->leftjoin('mxp_users as mu','mu.user_id','accepted_user_id')
                            ->select('mxp_bookingbuyer_details.*','mu.first_name','mu.last_name')
                            ->where('booking_order_id', $request->booking_id)
                            ->first();

        $bookingDetails->party_id_ = DB::table('mxp_party')->select('id as party_id_')->where('name',$bookingDetails->Company_name)->first();
        $booking_id = $request->booking_id;
        return view('maxim.os.mrf.mrf_Details_View', compact('booking_id','bookingDetails'));
    }
}