<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpGmtsColor;
use Auth;
use URL;
use DB;

class ItemColor extends Controller
{
	public function __invoke(Request $request){
		$i = 1;
		$output = "";
		if ($request->ajax()) {
		    $vendors = MxpGmtsColor::where('item_code', NULL)->where('color_name','LIKE', '%' .$request->search. "%")->orderBy('color_name','created_at')->paginate(20);

		    if (!empty($vendors[0]->color_name)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$vendor->color_name. '</td>' .
			                '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/update/gmts/color/view/'.$vendor->id.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/delete/gmts/color/action/'.$vendor->id.'" class="btn btn-danger">Delete</a>'
			                .'</td>'.
		                '</tr>';
		        }
		    }
		    else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'Item color not Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}