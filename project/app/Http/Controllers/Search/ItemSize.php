<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpProductSize;
use Auth;
use URL;
use DB;

class ItemSize extends Controller
{
	public function __invoke(Request $request){
		$i = 1;
		$output = "";
		if ($request->ajax()) {
		    $vendors = MxpProductSize::where('product_code', '')->where('product_size','LIKE', '%' .$request->search. "%")->orderBy('product_size','created_at')->paginate(20);

		    if (!empty($vendors[0]->product_size)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$vendor->product_size. '</td>' .
			                '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/update/product_size/'.$vendor->proSize_id.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/delete/product_size/'.$vendor->proSize_id.'" class="btn btn-danger">Delete</a>'
			                .'</td>'.
		                '</tr>';
		        }
		    }
		    else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'Item size not Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}