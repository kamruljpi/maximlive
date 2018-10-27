<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\MxpProduct;
use Auth;
use DB;
use Illuminate\Http\Request;

class ItemSearchController extends Controller{

    public function itemSearch(Request $request)
    {
        if ($request->ajax()) {

            $output = "";

            $products = MxpProduct::where('product_code', 'LIKE', '%' . $request->search . "%")->get();

            if (!empty($products[0]->product_code)) {

                foreach ($products as $key => $product) {

                    $output .= '<tr>' .

                        '<td>' .$product->product_id. '</td>' .
                        '<td>' .$product->brand. '</td>' .
                        '<td>' .$product->product_code. '</td>' .
                        '<td>' .$product->erp_code. '</td>' .
                        '<td>' .$product->description->name. '</td>' .
                        '<td>' .''. '</td>' .
                        '<td>' .$product->sizes. '</td>' .
                        '<td>' .$product->colors. '</td>' .
                        '<td>' .''. '</td>'.
                        '<td>' .''. '</td>'.
                        '</tr>';
                }
            }
            else
            {
                $output .= '<tr>' .
                    '<td align="center" colspan="12">' .'No Data Found'. '</td>'.
                    '</tr>';
            }
            return Response($output);
        }
    }
}