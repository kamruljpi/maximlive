<?php
namespace App\Http\Controllers\AjaxRequest\Task\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpProduct;
use App\Mxp_role;
use App\Http\Controllers\Source\User\UserAccessBuyerList;
use App\Http\Controllers\Source\User\PlanningRoleDefine;
use Auth;

class Booking extends Controller
{
	use UserAccessBuyerList;

	const SUPER_ADMIN = 'super_admin';

	public function checkItem(Request $request){
		$item = MxpProduct::where('product_code',$request->item)->first();
		if(isset($item) && ! empty($item)){			
			if(PlanningRoleDefine::getRoleName() != self::SUPER_ADMIN){
				$checkValue = array_search($item->id_buyer,$this->getUserByerList());
				if(empty($checkValue)){
					return json_encode('not_match');
				}
			}			
		}else{
			return json_encode('empty');
		}
		return json_encode('');
	}
}