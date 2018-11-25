<?php
namespace App\Http\Controllers\Source;

use App\Http\Controllers\Controller;
use Auth;
use DB;
/**
 * 
 */
class source
{
	public function getUserDetails( $bookingId ){
        $getBookingUserDetails = DB::table('mxp_booking as mb')
            ->join('mxp_users as ms','mb.user_id','=','ms.user_id')
            ->select('ms.first_name','ms.middle_name','ms.last_name')
            ->where('mb.booking_order_id',$bookingId)
            ->first();
        return $getBookingUserDetails;
    }
}