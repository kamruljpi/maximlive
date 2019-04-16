@extends('layouts.dashboard')
@section('page_heading','Purchase Details')
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
					<a href="{{ Route('purchase_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
					<i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</div>
		</div>


		{{-- this section put message on purshase_show.js file --}}

		<div class="alert alert-success hidden message_body" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<p class="put_message"></p>
		</div>

		{{-- end --}}

		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="panel panel-default custom">

					<div class="panel-heading">ADD</div>

					<form action="{{ Route('purchase_show_store_action',['id' => $details->id_purchase_order_wh])}}" method="POST">
						{{csrf_field()}}

						<div class="panel-body">
							<div class="col-sm-8">
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Date</span></label>
									<div class="col-sm-6">
										<input type="date" name="order_date" class="form-control" readonly="true" value="{{$details->order_date}}">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Purchase Voucher #</span></label>
									<div class="col-sm-6">
										<input type="text" name="purchase_voucher" class="form-control" placeholder="P-V # 00001" readonly="true" value="{{$details->purchase_voucher}}">
									</div>
								</div>
							</div>

							<div style="padding-top: 10px;clear: both;">
								<table class="table table-bordered">
									<thead>
										<th>Product</th>
										<th>Quantity</th>
										<th>Purchase Price</th>
										<th>Total</th>
										<th>Location</th>
										<th>Zone</th>
										<th>Warehouse in type</th>
										<th>Action</th>
									</thead>
									<tbody class="tbody_tr">
										<input type="hidden" name="id_purchase_order_wh" value="{{$details->id_purchase_order_wh}}">
										@if(isset($details->item_details) && ! empty($details->item_details))
											@foreach($details->item_details as $keys => $item)

												<?php 
													$locations_id = isset($item->locations_id) ? $item->locations_id : ''; 
													$zone_id = isset($item->zone_id) ? $item->zone_id : '';
													$zones = isset($item->zones) ? $item->zones : '';
													$warehouse_type_id = isset($item->warehouse_type_id) ? $item->warehouse_type_id : '';
												?>

												<tr class="tr_{{$keys}}">
													<td>
														<div class="form-group item_code_parent">
															<input type="hidden" name="raw_item_id[]" class="raw_item_id" readonly="true" value="{{$item->raw_item_id}}">
															<input type="text" name="item_code[]" class="form-control raw_item_code" placeholder="Item Code" readonly="true" value="{{$item->item_code}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="number" name="item_qty[]" class="form-control item_qty" placeholder="Qty" readonly="true" value="{{$item->item_qty}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text" name="price[]" class="form-control price" placeholder="Purchase Price" readonly="true" value="{{$item->price}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<input type="text" name="item_total_price[]" class="form-control total_price" placeholder="0.00" readonly="true" value="{{$item->total_price}}">
														</div>
													</td>
													<td>
														<div class="form-group">
															<select class="form-control location_id" name="location_id[]" {{ (! empty($locations_id)) ? 'readonly' :''}}>
																<option value=" ">--Select--</option>

																@foreach($locations as $location)
																	<option value="{{$location->id_location}}" {{ ($location->id_location == $locations_id) ? 'selected' : ''}}> {{$location->location}} </option>
																@endforeach
															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<select class="form-control zone_id" name="zone_id[]" {{ (! empty($zone_id)) ? 'readonly' :''}}>
																<option value=" ">--Select--</option>

																@if( !empty($zones))
																	@foreach($zones as $zone_z)
																		<option value="{{$zone_z->zone_id}}" {{ ($zone_z->zone_id == $zone_id) ? 'selected' : ''}}>{{$zone_z->zone_name}}</option>
																	@endforeach
																@endif
															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<select class="form-control warehouse_type_id" name="warehouse_type_id[]" {{ (! empty($warehouse_type_id)) ? 'readonly' :''}}>
																<option value=" ">--Select--</option>

																@foreach($warehouse_in_types as $types)
																	<option value="{{$types->id_warehouse_type}}" {{ ($types->id_warehouse_type == $warehouse_type_id) ? 'selected' : ''}}> {{$types->warehouse_type}} </option>
																@endforeach

															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<button class="btn btn-primary click_preloder store_purchase_submit" {{ (! empty($locations_id)) ? 'disabled' :''}}>Save</button>
														</div>
													</td>
												</tr>
											@endforeach
										@else
											<tr>
											    <td colspan="8">
											        <div style="text-align: center;font-size: 16px;font-weight: bold;"> Data not found.</div>
											    </td>
											</tr>
										@endif
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('LoadScript')
    <script src="{{ asset('assets/scripts/purchase/purchase_show.js') }}"></script>
@endsection
