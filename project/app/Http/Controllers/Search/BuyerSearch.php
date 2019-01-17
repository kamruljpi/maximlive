<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\buyer;
use Auth;
use URL;
use DB;

class BuyerSearch extends Controller
{
	public function __invoke(Request $request){
		$i = 1;
		$output = "";
		if ($request->ajax()) {
		    $vendors = buyer::where('buyer_name','LIKE', '%' .$request->search. "%")->orderBy('buyer_name','created_at')->paginate(20);

		    if (!empty($vendors[0]->buyer_name)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$vendor->buyer_name. '</td>' .
			                // '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/update/buyer/'.$vendor->id_mxp_buyer.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/delete/buyer/'.$vendor->id_mxp_buyer.'" class="btn btn-danger">Delete</a>'
			                .'</td>'.
		                '</tr>';
		        }
		    }else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'Buyer not Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}