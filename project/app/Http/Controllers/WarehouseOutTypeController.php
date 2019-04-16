<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MxpWarehouseType;
use Validator;
use App\Http\Controllers\Message\StatusMessage;
use DB;
use Auth;

class WarehouseOutTypeController extends Controller
{
    const CREATE 		= 'create' ;
	const UPDATE 		= 'update' ;
	const DELETE 		= 'deleted' ;
	public function setDefaultValues()
	{
		$value = [
			'user_id' => Auth::user()->user_id,
			'last_action_at' => self::CREATE,
			'is_deleted' => 0,
			'deleted_user_id' => '',
		];
		return $value;
	}
	public function setValuesInObject($object = null, Request $request)
	{
		if(method_exists($this, 'setDefaultValues')){
			$setDefaultValues  = $this->setDefaultValues();
		}else{
			$setDefaultValues = [];
		}
		foreach ($object->getFillable() as $fill) {
			if(isset($request->{$fill})){
				$object->{$fill} = $request->{$fill};
			}else{
				if(isset($setDefaultValues[$fill])){
					$object->{$fill} = $setDefaultValues[$fill];
				}
			}
		}
		return $object;
	}
	public function index(){
        $mxpwarehousetype = new MxpWarehouseType();
    	$primary_key = $mxpwarehousetype->getKeyName();
        $warehouse_type_list = MxpWarehouseType::where('warehouse_in_out_type','out')->paginate(10);
        return view('warehouse_type.warehouse_out_type_list', compact('warehouse_type_list'));
    }
    public function createview(){
    	return view('warehouse_type.warehouse_out_type_create');
    }
	public function store(Request $request){
        $roleManage = new RoleManagement();
        $validMassage = [         
            'warehouse_type.required' => 'Warehouse Type is required',
            'status.required' => 'Status is required',
            
        ];
        $validator = Validator::make($request->all(), [
                'warehouse_type'              => 'required',
                'status'             		  => 'required',
            ],
            $validMassage
        );
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }
        $warehousetype = new MxpWarehouseType();
        $warehousetype = $this->setValuesInObject($warehousetype, $request);
        $warehousetype->save();
        StatusMessage::create('party_added', $request->name.' Warehouse Type Added Successfully');
        return Redirect()->Route('warehouseouttypelist');
	}
    public function updateView(Request $request)
    {
    	$mxpwarehousetype = new MxpWarehouseType();
    	$primary_key = $mxpwarehousetype->getKeyName();
        $warehousetype = MxpWarehouseType::where([
	    	$primary_key => $request->{$primary_key},
	    	'warehouse_in_out_type' => 'out'
    	])->first();
        return view('warehouse_type.warehouse_out_type_edit', compact('warehousetype'));
    }
	public function updatestore(Request $request){
        $roleManage = new RoleManagement();
        $validMassage = [         
            'warehouse_type.required' => 'Warehouse Type is required',
            'status.required' => 'Status is required',
            
        ];
        $validator = Validator::make($request->all(), [
                'warehouse_type'              => 'required',
                'status'             		  => 'required',
            ],
            $validMassage
        );
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }
        $warehousetype = MxpWarehouseType::find($request->id_warehouse_type);
        $warehousetype = $this->setValuesInObject($warehousetype, $request);
        $warehousetype->save();
        StatusMessage::create('party_updated', $request->name.' Warehouse Type Update Successfully');
        return Redirect()->Route('warehouseouttypelist');
	}
    public function delete(Request $request) {
      $warehousetype = MxpWarehouseType::find($request->id_warehouse_type);
      $warehousetype->delete();
      StatusMessage::create('party_delete',$warehousetype->id_warehouse_type .' is deleted Successfully');
      return redirect()->Route('warehouseouttypelist');
    }
}
