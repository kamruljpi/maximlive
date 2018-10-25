<?php

namespace App\Http\Controllers\AjaxRequest\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpProductSize;
use Auth;

class Size extends Controller
{
	public function __invoke(Request $request){
		$data = MxpProductSize::where('id_buyer',$request->buyer_id)->select('proSize_id','product_size')->get();
		return json_encode($data);
	}
}