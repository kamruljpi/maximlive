<?php

namespace App\Http\Controllers\Source\User;

use App\Model\MxpBookingBuyerDetails;
use App\userbuyer;
use Auth;
use DB;

trait UserAccessBuyerList
{
	public function getUserByerList() {
		$userbuyer = userbuyer::where("id_user",Auth::user()->user_id)->get();
		$buyerList = [];
		if(isset($userbuyer) && !empty($userbuyer)){
			foreach ($userbuyer as $buyerusr) {
				$buyerList[$buyerusr->id_buyer] = $buyerusr->id_buyer;
			}
		}
		return $buyerList;
	}

	public function getUserByerNameList() {
		$buyerNameList = [];
		$userbuyerName = DB::table('mxp_buyer')->whereIn('id_mxp_buyer',$this->getUserByerList())->select('id_mxp_buyer','buyer_name')->get();

		if(isset($userbuyerName) && !empty($userbuyerName)){
			foreach ($userbuyerName as $buyerusrname) {
				$buyerNameList[$buyerusrname->id_mxp_buyer] = $buyerusrname->buyer_name;
			}
		}
		return $buyerNameList;
	}

	/**
	 * @param $datas must be get value details and
	 * there was a booking id
	 *
	 * @return void()
	 */
	public function addBuyerDetails($datas = null) {

		if(!is_null($datas)) {
			foreach ($datas as &$data) {

				$booking_order_id = isset($data->booking_order_id) ? $data->booking_order_id : '';

				$booking_order_id = explode(',', $booking_order_id);

				if(is_array($booking_order_id)) {
					$data->buyer_details = MxpBookingBuyerDetails::whereIn('booking_order_id',$booking_order_id)
						->select("*",DB::Raw('group_concat(DISTINCT buyer_name SEPARATOR ", ") as buyer_name'))
						->first();
				}
			}
		}
	}
}