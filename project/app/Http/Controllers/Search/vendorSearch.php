<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MaxParty;
use Auth;
use URL;
use DB;

class vendorSearch extends Controller
{
	public function __invoke(Request $request){
		$output = "";
		if ($request->ajax()) {
		    $vendors = MaxParty::where('name','LIKE', '%' .$request->vendor_name. "%")
		    			->orWhere('party_id','LIKE', '%' .$request->vendor_name. "%")
		    			->orWhere('name_buyer','LIKE', '%' .$request->vendor_name. "%")
		    			->orWhere('attention_invoice','LIKE', '%' .$request->vendor_name. "%")
		    			->orWhere('mobile_invoice','LIKE', '%' .$request->vendor_name. "%")
		    			->orderBy('name','created_at')
		    			->paginate(20);

		    if (!empty($vendors[0]->name_buyer)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
		                '<td>' .$vendor->party_id. '</td>' .
		                '<td>' .$vendor->name. '</td>' .
		                '<td>' .$vendor->name_buyer. '</td>' .
		                '<td>' .$vendor->address_part1_invoice. '</td>' .
		                '<td>' .$vendor->address_part1_delivery. '</td>' .
		                '<td>' .$vendor->attention_invoice. '</td>' .
		                '<td>' .$vendor->mobile_invoice. '</td>' .
		                '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
		                '<td>' .'<a href="'.URL::to('/').'/party/edit/'.$vendor->id.'" class="btn btn-success">Edit</a>'.
		                        '<a href="'.URL::to('/').'/party/id/delete/'.$vendor->id.'" class="btn btn-danger">Delete</a>'
		                .'</td>'.
		                '</tr>';
		        }
		    }
		    else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'No Vendor Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}