<?php
namespace App\Http\Controllers\taskController\History\Restore\Source;

use App\Http\Controllers\taskController\History\Restore\Source\Resource;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Os\MxpOsPo;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMultipleChallan;
use App\Model\MxpBookingChallan;
use App\Model\MxpBooking;
use App\Model\MxpMrf;
use App\Model\MxpPi;
use App\MxpIpo;
use Session;
use Carbon;
use Auth;
use DB;

class RestoreList extends Controller
{
	protected $type;
	protected $value;
	protected $filed;
	protected $model;
	protected $path;

	/**
	 *	@pram $type which type active now.
	 *	@pram $filed search field name	
	 *	@pram $model which table need to search
	 *	@pram $path which view file need to show
	 */

	public function __construct($type,$filed,$model,$path){
		$this->type = $type;
		$this->filed = $filed;
		$this->model = $model;
		$this->path = $path;
	}

	public function getRestoreList(){
		Session::flash('type',$this->type);
		$data = Resource::getDeletedPiValue($this->model,$this->filed);
		return view('maxim.history.restore.'.$this->path,compact('data'));
	}
}