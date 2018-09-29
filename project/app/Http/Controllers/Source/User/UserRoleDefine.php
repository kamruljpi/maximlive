<?php

namespace App\Http\Controllers\Source\User;

use App\Mxp_role;
use Auth;

class UserRoleDefine
{
	public function getRole(){
		$roleId  = Auth::user()->user_role_id;
		$roleTable = Mxp_role::where('id',$roleId)->get();

		if(isset($roleTable) && !empty($roleTable)){
		    $name = explode(' ',$roleTable[0]->name);
		    $nameValue = [];
		    foreach ($name as $key => $value) {
		    	$nameValue[$value[$key]] = $value;
		    }
		    $checkvalue = array_search('Planing', $nameValue);

		    if(empty($checkvalue)){
		        $checkvalue = array_search('planing', $nameValue);
		        if (empty($checkvalue)) {
		        	$checkvalue = 'empty';
		        }
		    }
		}
		return $checkvalue;
	}
}