<?php

namespace App\Http\Controllers\taskController\History\Restore\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use Session;
use Carbon;
use Auth;
use DB;

class FindRestore extends Controller
{
	protected $type;
	protected $value;
	protected $filed_name;
	protected $model;
	protected $path;

	/**
		@pram $filter_value search value
		@pram $filed_name search field name	
		@pram $model which table need to search
		@pram $path which view file need to show
	**/

	public function __construct($type,$filter_value,$filed_name,$model,$path){
		$this->type = $type;
		$this->value = $filter_value;
		$this->model = $model;
		$this->filed_name = $filed_name;
		$this->path = $path;
	}

	public function search(){
		Session::flash('type',$this->type);
		$data = $this->model->where([
			[$this->filed_name,$this->value],
			['is_deleted',BookingFulgs::IS_DELETED]
			])
			->select('*',DB::Raw('GROUP_CONCAT(DISTINCT booking_order_id SEPARATOR ", ") as booking_order_id'))
			->groupBy($this->filed_name)
			->paginate(2);

		return view('maxim.history.restore.'.$this->path,compact('data'));
	}
}