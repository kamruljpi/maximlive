<?php

namespace App\Http\Controllers\Supplier;

use App\MxpSupplierPrice;
use App\Supplier;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Message\StatusMessage;

class SupplierController extends Controller
{
    public function supplierList(){
        $suppliers = Supplier::where('is_delete', 0)
                    ->orderBy('supplier_id','DESC')
                    ->paginate(20);
        return view('supplier.supplier_list', compact('suppliers'));
    }

    public function supplierAdd(){
        return view('supplier.supplier_add');
    }

    public function supplierAddAction(Request $req){

        $inputErrorMsg = [
            'name.required' => 'Name is required',
            'person_name.required' => 'Person Name is required',
            'email.required' => 'Email is required',
            'address.required' => 'Address is required',
            'status.required' => 'Please select status'
        ];

        $validate = Validator::make(
            $req->all(),
            [
                'name' => 'required',
                // 'person_name' => 'required',
                // 'email' => 'required',
                // 'address' => 'required',
                'status' => 'required'
            ],
            $inputErrorMsg
        );

        if($validate->fails()){
            return redirect()->back()->withInput($req->input())->withErrors($validate->messages());
        }

        $supplierId = Supplier::create($req->all())->supplier_id;

        StatusMessage::create('add', $req->name .' New Supplier Created Successfully');
        return redirect()->route('supplier_list_view');
    }

    public function supplierUpdate(Request $req){

        $supplier = Supplier::where('is_delete', 0)->where('supplier_id', $req->supplier_id)->get()->first();
        return view('supplier.supplier_update', compact('supplier'));
    }
    public function supplierUpdateAction(Request $req){
        Supplier::where('supplier_id', $req->supplier_id)
            ->update([
                'name' => $req->name,
                'person_name'=> $req->person_name,
                'email'=> $req->email,
                'address'=>$req->address,
                'status' =>$req->status
            ]);
            StatusMessage::create('update', $req->name .' Supplier Updated Successfully');
        return redirect()->route('supplier_list_view');
    }
    public function supplierDeleteAction(Request $req){
        $supplier = Supplier::where('supplier_id', $req->supplier_id)->select('name')->first();
        Supplier::where('supplier_id', $req->supplier_id)
            ->update([
                'is_delete' => 1
            ]);
        StatusMessage::create('delete', $supplier->name . ' Supplier Delete Successfully');

        return redirect()->route('supplier_list_view');
    }

    public static function saveSupplierProductPrice(Request $req, $productId){
        for($i=0; $i<count($req->supplier_id); $i++){
            $sPrice = new MxpSupplierPrice;
            $sPrice->supplier_id = $req->supplier_id[$i];
            $sPrice->product_id = $productId;
            $sPrice->supplier_price = $req->supplier_price[$i];
            $sPrice->save();
        }
        return $req->all();
    }

    public static function updateProductPrice(Request $request){
        for($i=0; $i<count($request->supplier_id); $i++){
            if(count(MxpSupplierPrice::find($request->supplie_price_id[$i])) > 0){
                $sPrice = MxpSupplierPrice::find($request->supplie_price_id[$i]);
            }else{
                $sPrice = new MxpSupplierPrice();
            }

            $sPrice->supplier_id = $request->supplier_id[$i];
            $sPrice->product_id = $request->product_id;
            $sPrice->supplier_price = $request->supplier_price[$i];
            $sPrice->save();
        }

        return true;
    }
}
