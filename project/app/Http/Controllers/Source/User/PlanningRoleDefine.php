<?php

namespace App\Http\Controllers\Source\User;

use App\Mxp_role;
use Auth;

class PlanningRoleDefine
{
	public function getRole(){
		$roleId  = Auth::user()->user_role_id;
		$roleTable = Mxp_role::where('id',$roleId)->first();

		if(isset($roleTable) && !empty($roleTable)){
		    $name = explode(' ',$roleTable->name);
		    $nameValue = [];
		    foreach ($name as $key => $value) {
		    	$nameValue[$value[$key]] = $value;
		    }
		    $checkvalue = array_search('Planning', $nameValue);

		    if(empty($checkvalue)){
		        $checkvalue = array_search('planning', $nameValue);
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