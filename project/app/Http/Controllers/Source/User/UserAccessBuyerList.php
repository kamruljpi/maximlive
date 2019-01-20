<?php

namespace App\Http\Controllers\Source\User;

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
}