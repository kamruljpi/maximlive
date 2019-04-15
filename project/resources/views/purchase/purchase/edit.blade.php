@extends('layouts.dashboard')
@section('page_heading','Add a new Purchase')
@section('section')

	<style type="text/css">
		div.row .custom .panel-heading {
			/*background-color: #fff !important*/
		}
		div.row .custom .panel-body .date-label span {
			float: right;margin-top: 5px;
		}
		#abcdesfd .col-sm-3,
		#abcdesfd .col-sm-2,
		.col-padding .col-sm-6,
		.pad .col-sm-3,
		.pad .col-sm-7{
			padding: 0 !important;
		}

		.table-bordered tbody tr:hover{
			box-sizing: border-box !important;
    		/*box-shadow: '' !important;*/
		}
		
	</style>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group ">
					<a href="{{ Route('purchase_order_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
					<i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="panel panel-default custom">

					<div class="panel-heading">ADD</div>

					<form action="{{ Route('purchase_from_purchase_order_action',['id' => $details->id_purchase_order_wh])}}" method="POST">
						{{csrf_field()}}

						<div class="panel-body">
							<div class="col-sm-8">
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Date</span></label>
									<div class="col-sm-6">
										<input type="date" name="order_date" class="form-control" value="{{$details->order_date}}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Bilty No</span></label>
									<div class="col-sm-6">
										<input type="text" name="bilty_no" class="form-control" placeholder="Enter bilty no" value="{{$details->bilty_no}}">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Purchase Voucher #</span></label>
									<div class="col-sm-6">
										<input type="text" name="purchase_voucher" class="form-control" placeholder="P-V # 00001" value="{{$details->purchase_order_no}}">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Description</span></label>
									<div class="col-sm-6">

										<textarea name="description" class="form-control" style="width: 90%;"> {{$details->description}} </textarea>
									</div>
								</div>
							</div>

							<div style="clear:both;padding-bottom: 5px;"></div>

							<div class="add_new_field">
								<table class="table table-bordered" id="copy_table">
									<thead>
										<th width="20%">Product</th>
										<th>Quantity</th>
										<th>Purchase Price</th>
										<th>Total</th>
										<th>Action</th>
									</thead>
									<tbody class="idclone">
										@if(isset($details->item_details) && count($details->item_details) > 0)

											@foreach($details->item_details as $keys => $items)
												<tr class="{{(($keys == 0)? 'tr_clone' : 'tr_clone_2222'.$keys)}}">
													<td>
														<div class="form-group item_code_parent">
															<input type="hidden" name="raw_item_id[]" class="raw_item_id" value="{{$items->raw_item_id}}">

															<input type="text" name="item_code[]" class="form-control raw_item_code {{(($keys == 0)? '' : 'abc')}}" placeholder="Item Code" value="{{$items->item_code}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text" name="item_qty[]" class="form-control item_qty" placeholder="Qty" value="{{$items->item_qty}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text" name="price[]" class="form-control price purchase_price" placeholder="Purchase Price" value="{{$items->price}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text" name="item_total_price[]" class="form-control total_price" placeholder="0.00" value="{{$items->total_price}}" readonly="true">
														</div>
													</td>
													<td>
														<button class="btn btn-danger remove_field" {{(($keys == 0)? 'disabled="true"' : '')}} >X</button>
													</td>
												</tr>
											@endforeach
										@else
										    <tr>
										        <td colspan="5">
										            <div style="text-align: center;font-size: 16px;font-weight: bold;"> Data not found.</div>
										        </td>
										    </tr>                  
										@endif

										
									</tbody>
								</table>
							</div>

							<div class="form-group">
								<button class="btn btn-danger" style="float: right;" id="add_new_field"><i class="fa fa-plus" style="padding-right: 5px;"></i>Add New</button>
							</div>

							<div style="clear:both; padding-bottom: 20px;"></div>
							{{-- <hr> --}}

							<table class="table table-bordered" id="TableFooter">
								<tbody>
									<tr>
										<td colspan="">
											<div style="text-align: center; font-size: 17px;" >Total Price</div>
										</td>
										<td width="30%">
											<div class="form-group">
												<input type="text" name="in_all_total_price" class="form-control in_all_total_price" placeholder="Total Price" readonly="true" value="{{$details->in_all_total_price}}">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="">
											<div style="text-align: center; font-size: 17px;">Discount</div>
										</td>
										<td width="30%">
											<div class="form-group">
												<input type="text" name="discount" class="form-control discount" value="0">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Vat</div></td>
										<td width="30%">
											<div class="form-group">
												<input type="text" name="vat" class="form-control vat" value="0">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Grand Total</div></td>
										<td width="30%">
											<div class="form-group">
												<input type="text" name="grand_total" class="form-control grand_total" readonly="true" value="{{$details->grand_total}}">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Payment Status</div></td>
										<td width="30%">
											<div class="form-group">
												<select class="form-control" name="payment_status">
													<option value="pendding" selected="true">Pendding</option>
													<option value="confirmed">Confirmed</option>
												</select>
											</div>
										</td>
									</tr>
									{{-- <tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Paying By</div></td>
										<td width="30%">
											<div class="form-group">
												<select class="form-control" name="paying_by">
													<option value=" ">--Select--</option>
													<option value="cash">Cash</option>
													<option value="bank">Bank</option>
												</select>
											</div>
										</td>
									</tr> --}}
								</tbody>
							</table>

							<div class="col-sm-4 col-sm-offset-4">
								<div class="form-group">
									<button class="form-control btn btn-primary">Confirm Add New Purchase</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('LoadScript')
    <script src="{{ asset('assets/scripts/purchase/purchase.js') }}"></script>
@endsection