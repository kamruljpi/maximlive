<?php

namespace App\Http\Controllers\taskController\BookingView\Mrf;

use App\Model\MxpMrf;
use App\Model\MxpBooking;

class MrfController
{
	public function getBookingValue($id){
		$getDbValue = [];
		if(is_array($id) && !empty($id)){
			foreach ($id as $idValue) {
				$getDbValue[] = MxpBooking::where('id',$idValue)->get();
			}
		}

		$pi_details = [];
		if(!empty($getDbValue)){
			foreach ($getDbValue as $key => $aaavalue) {
				foreach ($aaavalue as $key => $bbbvalue) {
					$pi_details[] =  $bbbvalue;
				}
			}
		}

		return (!empty($pi_details)) ? $pi_details : '';
	}
}
