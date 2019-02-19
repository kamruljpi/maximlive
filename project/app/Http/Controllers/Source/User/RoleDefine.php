<?php

namespace App\Http\Controllers\Source\User;

use App\Mxp_role;
use Auth;

class RoleDefine
{
	/**
	 * @return string role frist word
	 */
	public function getRole($role_value){
		$roleId  = Auth::user()->user_role_id;
		$roleTable = Mxp_role::where('id',$roleId)->first();

		if(isset($roleTable) && !empty($roleTable)){
		    $name = explode(' ',$roleTable->name);
		    $nameValue = [];
		    foreach ($name as $key => $value) {
		    	$nameValue[$value] = $value;
		    }
		    $checkvalue = array_search($role_value, $nameValue);

		    if(empty($checkvalue)){
		        $checkvalue = array_search(strtolower($role_value), $nameValue);
		        if (empty($checkvalue)) {
		        	$checkvalue = 'empty';
		        }
		    }
		}
		return strtolower($checkvalue);
	}

	public function getRoleName(){
		$role = Mxp_role::where('id',Auth::user()->user_role_id)->first();
		return $role->name;
	}
}