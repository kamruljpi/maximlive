<?php
namespace App\Http\Controllers\taskController\Os\Po;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingBuyerDetails;
use App\Model\MxpMrf;
use App\MxpSupplierPrice;
use App\Supplier;
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
						->join('mxp_booking as mb','mb.id','mxp_mrf_table.job_id')
						->select('mxp_mrf_table.*','mp.product_id','mb.item_size_width_height','mb.oos_number','mb.season_code','mb.sku','mb.style')
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
		$supplier = Supplier::where('supplier_id',$request->supplier_id)->select('name')->first();
		}
		// $this->print_me($supplier);
		return view('maxim.os.po.po_genarate',compact('jobid_values','supplier'));
	}

	public function storeOsPo(Request $request){

		$job_id = $request['job_id'];
		$supplier_price = $request['supplier_price'];
		$material = $request['material'];
		$datas = [];
		if(isset($job_id) && isset($supplier_price) && isset($material)){
			if(!empty($job_id) && !empty($supplier_price) && !empty($material)){
				foreach ($job_id as $key => $value) {
					$datas[$key]['job_id'] = $value;
					$datas[$key]['supplier_price'] = $supplier_price[$key];
					$datas[$key]['material'] = $material[$key];
				}
			}
		}

		$this->print_me($datas);

		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
		return view('maxim.os.po.po_report',compact('companyInfo'));
	}
}