<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Supplier;
use Auth;
use URL;
use DB;

class SupplierSearch extends Controller
{
	public function __invoke(Request $request){
		$i = 1;
		$output = "";
		if ($request->ajax()) {
		    $vendors = Supplier::where('is_delete',0)->where('name','LIKE', '%' .$request->search. "%")->orderBy('name','created_at')->paginate(20);

		    if (!empty($vendors[0]->name)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$vendor->name. '</td>' .
			                '<td>' .$vendor->email. '</td>' .
			                '<td>' .$vendor->person_name. '</td>' .
			                '<td>' .$vendor->address. '</td>' .
			                '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/supplier/update/'.$vendor->supplier_id.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/supplier/delete/'.$vendor->supplier_id.'" class="btn btn-danger">Delete</a>'
			                .'</td>'.
		                '</tr>';
		        }
		    }
		    else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'Supplier not Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}