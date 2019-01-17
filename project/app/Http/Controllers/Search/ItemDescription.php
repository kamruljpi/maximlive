<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Model\MxpItemDescription;
use Illuminate\Http\Request;
use Auth;
use URL;
use DB;

class ItemDescription extends Controller
{
	public function __invoke(Request $request){
		$i = 1;
		$output = "";
		if ($request->ajax()) {
		    $vendors = MxpItemDescription::where('name','LIKE', '%' .$request->search. "%")->orderBy('name','created_at')->paginate(20);

		    if (!empty($vendors[0]->name)) {
		        foreach ($vendors as $key => $vendor) {
		            $output .= '<tr>' .
			                '<td>' .$i++. '</td>' .
			                '<td>' .$vendor->name. '</td>' .
			                '<td>' .(($vendor->status == 1)? 'Active' : 'Inactive').'</td>'.
			                '<td>' .'<a href="'.URL::to('/').'/update/description/'.$vendor->id.'" class="btn btn-success">Edit</a>'.
			                        '<a href="'.URL::to('/').'/delete/description/'.$vendor->id.'" class="btn btn-danger">Delete</a>'
			                .'</td>'.
		                '</tr>';
		        }
		    }
		    else{
		        $output .= '<tr>' .
		            '<td align="center" colspan="12">' .'Description not Found'. '</td>'.
		            '</tr>';
		    }
		}
		return Response($output);
	}
}