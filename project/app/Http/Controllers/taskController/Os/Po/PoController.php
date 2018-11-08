<?php
namespace App\Http\Controllers\taskController\Os\Po;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMrf;
use App\MxpSupplierPrice;
use Auth;
use DB;

class PoController extends Controller
{
	public function poGenarateView(Request $request){

		if(!isset($request->job_id) || !isset($request->supplier_id)){
			return redirect()->back()->with('data','Please select a Item and Suppliers');
		}

		if(isset($request->job_id) && !empty($request->job_id)){
			$check_open = '';
			foreach ($request->job_id as $key_value) {
				$jobid_value = MxpMrf::where('job_id',$key_value)->select('job_id_current_status')->first();
				if ($jobid_value->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_OPEN) {
					$check_open = MrfFlugs::JOBID_CURRENT_STATUS_OPEN;
				}
			}
			if(!empty($check_open)){
				return redirect()->back()->with('datas','.modal');
			}
		}

		$jobid_values = [];
		if(isset($request->job_id) && !empty($request->job_id)){
			foreach ($request->job_id as $key_values) {
				$jobid_values[] = MxpMrf::join('mxp_product as mp','mp.product_code','mxp_mrf_table.item_code')
						// ->join('')
						->select('mxp_mrf_table.*','mp.product_id')
						->where('job_id',$key_value)
						->first();
			}
		}

		if(isset($jobid_values) && !empty($jobid_values) && !empty($request->supplier_id)){
			foreach ($jobid_values as &$item_price) {
				$item_price->item_price = DB::table('mxp_supplier_prices')
					->where([
						['supplier_id',$request->supplier_id],
						['product_id',$item_price->product_id]
					])
					->select('supplier_price')
					->first();
			}
		}

		// $this->print_me($request->all());
		return view('maxim.os.po.po_genarate',compact('jobid_values'));
	}

	public function storeOsPo(Request $request){
		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
		return view('maxim.os.po.po_report',compact('companyInfo'));
	}
}