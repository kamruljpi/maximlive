<?php

namespace App\Http\Controllers\AjaxRequest\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpProduct;
use Auth;

class Color extends Controller
{
	public function __invoke(Request $request){
		return json_encode("value");
	}
}