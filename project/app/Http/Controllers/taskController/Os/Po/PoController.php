<?php
namespace App\Http\Controllers\taskController\Os\Po;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpBookingBuyerDetails;
use App\Model\Os\MxpOsPo;
use App\MxpSupplierPrice;
use App\Model\MxpMrf;
use Carbon\Carbon;
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
			foreach ($request->job_id as $keyvalues) {
				$jobid_values[] = MxpMrf::join('mxp_product as mp','mp.product_code','mxp_mrf_table.item_code')
						->join('mxp_booking as mb','mb.id','mxp_mrf_table.job_id')
						->select('mxp_mrf_table.*','mp.product_id','mb.item_size_width_height','mb.oos_number','mb.season_code','mb.sku','mb.style')
						->where([
							['job_id',$keyvalues],
							['job_id_current_status',MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT]
						])
						->first();
			}
		}

		if(isset($jobid_values->job_id) && !empty($jobid_values->job_id) && !empty($request->supplier_id)){
			foreach ($jobid_values as &$item_price) {
				$item_price->item_price = DB::table('mxp_supplier_prices')
					->where([
						['supplier_id',$request->supplier_id],
						['product_id',$item_price->product_id]
					])
					->select('supplier_price')
					->first();
			}
		$supplier = Supplier::where('supplier_id',$request->supplier_id)->select('supplier_id','name')->first();
		}
		if(!isset($jobid_values[0]->job_id)){$jobid_values = [];}
		// $this->print_me($jobid_values);
		return view('maxim.os.po.po_genarate',compact('jobid_values','supplier'));
	}

	public function storeOsPo(Request $request){

		$job_id = $request['job_id'];
		$mrf_id = $request['mrf_id'];
		$material = $request['material'];
		$supplier_id = $request['supplier_id'];
		$shipment_date = $request['shipment_date'];
		$supplier_price = $request['supplier_price'];

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

		$cc = MxpOsPo::select('po_id')->groupBy('po_id')->get();
		$cc = count($cc);
		$count = str_pad($cc + 1, 4, 0, STR_PAD_LEFT);
		$id = "PO"."-";
		$date = date('dmY') ;
		$po_id = $id.$date."-".$count;

		if(is_array($datas) && !empty($datas)){
			foreach ($datas as $datasValue) {
				MxpOsPo::insert(
				 	[
				 		'po_id' => $po_id,
					 	'user_id' => Auth::user()->user_id,
					 	'mrf_id' => $mrf_id,
					 	'job_id' => $datasValue['job_id'],
					 	'supplier_id' => $supplier_id,
					 	'supplier_price' => $datasValue['supplier_price'],
					 	'material' => $datasValue['material'],
					 	'order_date' => Carbon::today()->format('Y-m-d'),
					 	'shipment_date' => $shipment_date,
					 	'last_action_at' => LastActionFlugs::CREATE_ACTION
				 	]
				 );

				MxpMrf::where('job_id',$datasValue['job_id'])->update([
					'job_id_current_status' => MrfFlugs::JOBID_CURRENT_STATUS_WAITING_FOR_GOODS
				]);
			}
		}
		return \Redirect::route('refresh_os_po_view',['pid' => $po_id]);
	}

	public function getOsPoValues($po_id,$order_by = null){
		$datas = MxpOsPo::join('mxp_mrf_table as mrf','mrf.job_id','mxp_os_po.job_id')
						->join('mxp_booking as mp','mp.id','mxp_os_po.job_id')
						->Leftjoin('suppliers as s','s.supplier_id','mxp_os_po.supplier_id')
						->select('mxp_os_po.user_id','mrf.mrf_id','mrf.booking_order_id','mrf.erp_code',
							'mrf.item_code','mrf.item_size','mrf.item_description','mrf.gmts_color','mrf.poCatNo','mrf.mrf_quantity','mp.sku','mp.season_code','mp.oos_number','mp.style','mp.item_size_width_height','mxp_os_po.supplier_price','mxp_os_po.material','mxp_os_po.order_date','mxp_os_po.shipment_date','s.name','s.person_name'
						)
						->where([
							['mxp_os_po.po_id',$po_id]],
							['mxp_os_po.is_deleted',BookingFulgs::IS_NOT_DELETED]
						)
						->orderBy('mxp_os_po.job_id',$order_by)
						->get();
		return $datas;
	}

	public function redirectOsPoReport(Request $request){
		$poDetails = $this->getOsPoValues($request->pid);
		$this->print_me($poDetails);

		$companyInfo  = DB::table("mxp_header")
			->where('header_type',HeaderType::COMPANY)
			->get();
		return view('maxim.os.po.po_report',compact('companyInfo'));
	}
}