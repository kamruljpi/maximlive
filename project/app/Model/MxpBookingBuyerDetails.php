<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use  App\Model\MxpBookingBuyerDetails;
use App\MxpIpo;
use App\Model\MxpPi;
use App\Model\MxpMrf;
use App\Model\MxpBooking;
use App\Model\MxpMultipleChallan;
use App\Model\MxpBookingChallan;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;

class MxpBookingBuyerDetails extends Model
{
    protected $table = "mxp_bookingbuyer_details";

    protected $fillable = [
    			'user_id',
    			'booking_order_id',
    			'Company_name',
    			'C_sort_name',
    			'buyer_name',
    			'address_part1_invoice',
    			'address_part2_invoice',
    			'attention_invoice',
    			'mobile_invoice',
    			'telephone_invoice',
    			'fax_invoice',
    			'address_part1_delivery',
    			'address_part2_delivery',
    			'attention_delivery',
    			'mobile_delivery',
    			'telephone_delivery',
    			'fax_delivery'];

    function bookings(){
        return $this->hasMany(MxpBooking::class, 'booking_order_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);
    }

    function bookings_challan_table(){
        return $this->hasMany(MxpBookingChallan::class, 'booking_order_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);
    }

    function pi(){
        return $this->hasMany(MxpPi::class, 'booking_order_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);;
    }

    function ipo(){
        return $this->hasMany(MxpIpo::class, 'booking_order_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);;
    }

    function mrf(){
        return $this->hasMany(MxpMrf::class, 'booking_order_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);;
    }

    function challan(){
        return $this->hasMany(MxpMultipleChallan::class, 'checking_id','booking_order_id')->where('is_deleted',BookingFulgs::IS_NOT_DELETED);;
    }
}
