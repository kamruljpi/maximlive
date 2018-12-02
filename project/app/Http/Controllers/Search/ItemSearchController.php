<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\MxpProduct;
use Auth;
use DB;
use URL;
use Illuminate\Http\Request;

class ItemSearchController extends Controller
{
    public function itemSearch(Request $request){
        $output = "";
        if ($request->ajax()) {
            $products = MxpProduct::leftJoin('mxp_productsize as mps','mxp_product.product_code','mps.product_code')
                ->leftJoin('mxp_gmts_color as mgc','mxp_product.product_code','mgc.item_code')
                ->select('mxp_product.*',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size SEPARATOR ", ") as size'),DB::raw('GROUP_CONCAT(DISTINCT mgc.color_name SEPARATOR ", ") as gmts_color'))
                ->where('mxp_product.product_code', 'LIKE', '%' . $request->search . "%")
                ->groupBy('mxp_product.product_code')
                ->get();

            if (!empty($products[0]->product_code)) {
                foreach ($products as $key => $product) {
                    $output .= '<tr>' .
                    '<td>' .$product->product_id. '</td>' .
                    '<td>' .$product->brand. '</td>' .
                    '<td>' .$product->product_code. '</td>' .
                    '<td>' .$product->erp_code. '</td>' .
                    '<td>' .$product->product_description. '</td>' .
                    '<td>' .$product->unit_price. '</td>' .
                    '<td>' .((!empty($product->item_size_width_height))?$product->item_size_width_height.' mm' :''). '</td>' .
                    '<td>' .$product->size. '</td>' .
                    '<td>' .$product->gmts_color. '</td>' .
                    '<td>' .(($product->status == 1)? 'Active' : 'Inactive').'</td>'.
                    '<td>' .
                        '<a href="'.URL::to('/').'/updateProduct/'.$product->product_id.'" class="btn btn-success">Edit</a>'.
                        '<a href="'.URL::to('/').'/deleteProduct/'.$product->product_id.'" class="btn btn-danger">Delete</a>'.
                    '</td>'.
                    '</tr>';
                }
            }else{
                $output .= '<tr>' .
                    '<td align="center" colspan="13">' .'No Item Found'. '</td>'.
                    '</tr>';
            }
        }
        return Response($output);
    }
}