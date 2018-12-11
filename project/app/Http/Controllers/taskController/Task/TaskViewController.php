<?php

namespace App\Http\Controllers\taskController\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpTaskRole;
use App\userbuyer;
use Auth;
use DB;

class TaskViewController extends Controller
{
	public function view(){
		$taskRoleData = array();
		$user_role_id = session()->get('user_role_id');
		if(isset($user_role_id) && !empty($user_role_id)){
			$selectedTaskRole = DB::select('SELECT * FROM `mxp_task_role` WHERE `role_id` = '.$user_role_id);
			if(isset($selectedTaskRole) && !empty($selectedTaskRole)){
			    foreach ($selectedTaskRole as $srk => $srvalue) {
			        if(isset($srvalue->task) && !empty($srvalue->task)){
			            $taskRoleData = explode(",", $srvalue->task);
			        }
			    }
			}
		}
		$taskAccessList = array();
		if(isset($taskRoleData) && !empty($taskRoleData)){
			foreach ($taskRoleData as $taskKey => $taskValue) {
				$selectedTaskRole = DB::select('SELECT `name` FROM `mxp_task` WHERE `id_mxp_task` = '.$taskValue);
				if(isset($selectedTaskRole[0]->name) && !empty($selectedTaskRole[0]->name)){
					$taskAccessList[] = $selectedTaskRole[0]->name;
				}
			}
		}

		$userbuyer = userbuyer::where("id_user",Auth::user()->user_id)->get();
		$buyerList = [];
		if(isset($userbuyer) && !empty($userbuyer)){
			foreach ($userbuyer as $buyerusr) {
				$buyerList[] = $buyerusr->id_buyer;
			}
		}

		if(isset($buyerList) && !empty($buyerList)){
			$selectBuyer = DB::table('mxp_party')->where('status',1)->whereIn('id_buyer',$buyerList)->orderBy('name_buyer', ASC)->get();
		}else if(Auth::user()->type == 'super_admin'){
			$selectBuyer = DB::table('mxp_party')->where('status',1)->orderBy('name_buyer', ASC)->get();
		}else{
			$selectBuyer = [];
		}

		return view('task_page',compact('selectBuyer','taskAccessList'));
	}
}