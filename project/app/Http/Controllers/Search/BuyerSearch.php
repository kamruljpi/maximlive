<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
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
		    $buyers = buyer::where([
			    		['buyer_name','LIKE', '%' .$request->search. "%"],
			    		['is_deleted',BookingFulgs::IS_NOT_DELETED]
		    		])
		    		->orderBy('buyer_name','created_at')
		    		->paginate(20);

		    if (!empty($buyers[0]->buyer_name)) {
		        foreach ($buyers as $key => $buyer) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$buyer->buyer_name. '</td>' .
			                // '<td>' .(($buyer->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/update/buyer/'.$buyer->id_mxp_buyer.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/delete/buyer/'.$buyer->id_mxp_buyer.'" class="btn btn-danger">Delete</a>'
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