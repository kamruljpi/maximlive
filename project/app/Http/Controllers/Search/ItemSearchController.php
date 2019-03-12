<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Source\User\RoleDefine;
use App\Model\Product\ItemCostPrice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MxpProduct;
use Auth;
use URL;
use DB;

class ItemSearchController extends Controller
{
    public function itemSearch(Request $request){
        $output = "";

        $object = new RoleDefine();
        $role_check_os = $object->getRole('OS');
        $role_check_planning = $object->getRole('Planning');

        $serach_value = $request->search;

        if ($request->ajax()) {
            $products = MxpProduct::leftJoin('mxp_products_sizes as mpss','mpss.product_id', '=','mxp_product.product_id')
                ->leftJoin('mxp_productsize as mps','mps.proSize_id', '=','mpss.size_id')
                ->leftJoin('mxp_gmts_color as mgc','mxp_product.product_code','mgc.item_code')
                ->select('mxp_product.*',DB::raw('GROUP_CONCAT(DISTINCT mps.product_size SEPARATOR ", ") as size'),DB::raw('GROUP_CONCAT(DISTINCT mgc.color_name SEPARATOR ", ") as gmts_color'))                
                ->where(function($query) use ($serach_value){
                    $query->where('mxp_product.product_code', 'LIKE', '%' . $serach_value . "%")
                            ->orWhere('mxp_product.erp_code', 'LIKE', '%' . $serach_value . "%")
                            ->orWhere('mxp_product.brand', 'LIKE', '%' . $serach_value . "%")
                            ->orWhere('mxp_product.product_description', 'LIKE', '%' . $serach_value . "%");
                })
                ->groupBy('mxp_product.product_code','mxp_product.created_at')
                ->orderBy('mxp_product.product_code')
                ->paginate(20);

            if(isset($products) && !empty($products)){   
                foreach ($products as &$productValue) {

                    $productValue->cost_price = ItemCostPrice::where('id_product',$productValue->product_id)->first();
                }
                
            }

            if (!empty($products[0]->product_code)) {
                foreach ($products as $key => $product) {

                    if($role_check_planning == 'planning' || $role_check_os == 'os'){
                        $output .= '<tr>' .
                        '<td>' .$product->product_id. '</td>' .
                        '<td>' .$product->brand. '</td>' .
                        '<td>' .$product->product_code. '</td>' .
                        '<td>' .$product->erp_code. '</td>' .
                        '<td>' .$product->product_description. '</td>' .
                        '<td>' .((!empty($product->item_size_width_height))?$product->item_size_width_height.' mm' :''). '</td>' .
                        '<td><div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;"><table><td>'.$product->size.'</td></table></div></td>'.
                        '<td><div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;"><table><td>'.$product->gmts_color.'</td></table></div></td>'.
                        '<td>' .(($product->status == 1)? 'Active' : 'Inactive').'</td>'.
                        '</tr>';

                    }else{
                        $output .= '<tr>' .
                        '<td>' .$product->product_id. '</td>' .
                        '<td>' .$product->brand. '</td>' .
                        '<td>' .$product->product_code. '</td>' .
                        '<td>' .$product->erp_code. '</td>' .
                        '<td>' .$product->product_description. '</td>' .
                        '<td>' .$product->unit_price. '</td>' .
                        '<td>' .$product->cost_price->price_1. '</td>' .
                        '<td>' .((!empty($product->item_size_width_height))?$product->item_size_width_height.' mm' :''). '</td>' .
                        '<td><div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;"><table><td>'.$product->size.'</td></table></div></td>'.
                        '<td><div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;"><table><td>'.$product->gmts_color.'</td></table></div></td>'.
                        '<td>' .(($product->status == 1)? 'Active' : 'Inactive').'</td>'.
                        '<td>' .
                            '<a href="'.URL::to('/').'/updateProduct/'.$product->product_id.'" class="btn btn-success">Edit</a>'.
                            '<a href="'.URL::to('/').'/deleteProduct/'.$product->product_id.'" class="btn btn-danger">Delete</a>'.
                        '</td>'.
                        '</tr>';
                    }
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