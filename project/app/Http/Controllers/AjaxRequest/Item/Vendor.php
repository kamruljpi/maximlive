<?php

namespace App\Http\Controllers\AjaxRequest\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MaxParty;
use Auth;

class Vendor extends Controller
{
	public function __invoke(Request $request){
		$data = MaxParty::where('id_buyer',$request->buyer_id)->select('id','party_id','name','name_buyer')->get();
		return json_encode($data);
	}
}