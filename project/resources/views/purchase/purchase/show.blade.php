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
									<label class="col-sm-6 date-label"><span>Bilty No</span></label>
									<div class="col-sm-6">
										<input type="text" name="bilty_no" class="form-control" placeholder="Enter bilty no" readonly="true" value="{{$details->bilty_no}}">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Purchase Voucher #</span></label>
									<div class="col-sm-6">
										<input type="text" name="purchase_voucher" class="form-control" placeholder="P-V # 00001" readonly="true" value="{{$details->order_date}}">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Description</span></label>
									<div class="col-sm-6">
										<textarea name="description" class="form-control" style="width:90% !important" readonly="true">{{$details->description}}</textarea>
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

										@if(isset($details->item_details) && ! empty($details->item_details))
											@foreach($details->item_details as $keys => $item)
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
															<select class="form-control location_id" name="location_id[]" required="true">
																<option value=" ">--Select--</option>

																@foreach($locations as $location)
																	<option value="{{$location->id_location}}"> {{$location->location}} </option>
																@endforeach
															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<select class="form-control zone_id" name="zone_id[]">
																<option value=" ">--Select--</option>
															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<select class="form-control" name="warehouse_type_id[]" required="true">
																<option value=" ">--Select--</option>

																@foreach($warehouse_in_types as $types)
																	<option value="{{$types->id_warehouse_type}}"> {{$types->warehouse_type}} </option>
																@endforeach

															</select>
														</div>
													</td>
													<td>
														<div class="form-group">
															<button class="btn btn-primary">Save</button>
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

							<table class="table table-bordered">
								<tbody>
									<tr>
										<td colspan="">
											<div style="text-align: center; font-size: 17px;">Total Price</div>
										</td>
										<td width="30%">
											<div class="form-group">
												<input type="number" name="in_all_total_price" class="form-control" placeholder="Total Price" readonly="true" value="{{$details->in_all_total_price}}">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="">
											<div style="text-align: center; font-size: 17px;">Discount</div>
										</td>
										<td width="30%">
											<div class="form-group">
												<input type="number" name="discount" class="form-control" placeholder="Discount" readonly="true" value="{{$details->discount}}">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Vat</div></td>
										<td width="30%">
											<div class="form-group">
												<input type="number" name="vat" class="form-control" placeholder="Vat" readonly="true" value="{{$details->vat}}">
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Payment Status</div></td>
										<td width="30%">
											<div class="form-group">
												<select class="form-control" name="payment_status" readonly="true">
													<option value=" ">--Select--</option>
													<option value="pendding" {{($details->payment_status == 'pendding') ? 'selected' : ''}}>Pendding</option>
													<option value="confirmed" {{($details->payment_status == 'confirmed') ? 'selected' : ''}}>Confirmed</option>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan=""><div style="text-align: center; font-size: 17px;">Paying By</div></td>
										<td width="30%">
											<div class="form-group">
												<select class="form-control" name="paying_by" readonly>
													<option value=" ">--Select--</option>
													<option value="cash" {{($details->paying_by == 'cash') ? 'selected' : ''}}>Cash</option>
													<option value="bank" {{($details->paying_by == 'bank') ? 'selected' : ''}}>Bank</option>
												</select>
											</div>
										</td>
									</tr>
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

	<script type="text/javascript">
		$(document).ready(function(){
		  $('.tbody_tr').on('change','.location_id',function(){
		  		var selected = $(this).val();
		  		var item_parent_class = $.trim($(this).parent().parent().parent().prop('className'));

		  		$.ajax({
		  		    url:baseURL+"/zone/details",
		  		    type:"GET",
		  		    data:{selected},
		  		    datatype: 'json',
		  		    cache: false,
		  		    async: false,
		  			success:function(result){
			  		    var myObj3 = JSON.parse(result);

			  		    $('.'+item_parent_class+' .zone_id').html($('<option>', {
			  		        value: "",
			  		        text : "--Select--"
			  		    }));
			  		    
			  		    if(myObj3 != null) {
			  		    	var i;
			  		    	for (i = 0; i < myObj3.length; i++) {
			  		    	    $(".zone_id").append('<option value="'+myObj3[i].zone_id+'">'+myObj3[i].zone_name+'</option>');
			  		    	}
			  		    }
		  		    },
		  		    error:function(result){
		  		        alert("ERROR > "+result);
		  		    }
		  		});
		  });
		});
	</script>
@endsection