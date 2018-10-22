@extends('layouts.dashboard')
@section('page_heading', trans("others.new_mrf_create_label"))
@section('section')
<?php 
	// print_r("<pre>");
	// print_r($bookingDetails[0]->booking_order_id);
	// print_r("</pre>");
?>
<div class="container-fluid">

	<div class="row">
		<div class="col-sm-2">
			<div class="form-group ">
				<a href="{{ URL::previous() }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
				<i class="fa fa-arrow-left"></i> Back</a>
			</div>
		</div>
	</div>

	@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
	@if(Session::has('erro_challan'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('erro_challan') ))
	@endif
	<div class="row">
		<div class="col-md-12 col-md-offset-0">

			@if(!empty($MrfDetails))
				<div class="panel showMrfList">
					<div class="panel-heading">MRP list</div>
					<div class="panel-body">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>Booking Id</th>
									<th>MRF Id</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@php($i=1)
								@foreach($MrfDetails as $details)
								<tr>
									<td>{{$i++}}</td>
									<td>{{$details->booking_order_id}}</td>
									<td>{{$details->mrf_id}}</td>
									<td>
										<form action="{{Route('mrf_list_action_task') }}" role="form" target="_blank">
											<input type="hidden" name="mid" value="{{$details->mrf_id}}">
											<input type="hidden" name="bid" value="{{$details->booking_order_id}}">
											<button class="btn btn-success" >View</button>
										</form>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			@endif

			<div class="panel panel-default">
				<div class="panel-heading">{{trans('others.new_mrf_create_label')}}</div>
				<div class="panel-body aaa">
					<form class="form-horizontal" role="form" method="POST" action="{{ Route('mrf_action_task') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="booking_order_id" value="{{$bookingDetails[0]->booking_order_id}}">

						<div class="col-sm-6">
							<div class="form-group">
								<label class="col-sm-12 label-control">Supplier</label>
								<div class="col-sm-12">
									{{--<input class="form-control" type="text" name="mrf_person_name" placeholder="Enter Name" required>--}}
									<select class="form-control" name="supplier_id">
										<option value="">Choose a Option</option>
										@foreach($suppliers as $supplier)
											<option value="{{$supplier->supplier_id}}">{{$supplier->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="col-sm-12 label-control">Shipment Date</label>
								<div class="col-sm-12">
									<input id="datePickerDate" class="form-control" type="Date" name="mrf_shipment_date" required>
								</div>
							</div>
						</div>

						<table class="table table-bordered table-striped" >
							<thead>
								<tr>
									<th width="8%">Job Id</th>
									<th width="16%">ERP Code</th>
									<th width="">Item Code</th>
									<!-- <th width="">Season Code</th> -->
									<!-- <th width="">OOS No</th> -->
									<!-- <th width="">Style</th> -->
									<!-- <th width="">PO/Cat No.</th> -->
									<th width="15%">Size</th>
									<th width="">GMTS Color</th>
									<th width="">Quantity Left</th>
									<th width="">MRF QTY</th>
								</tr>
							</thead>
							@foreach ($bookingDetails as $item)
								<?php 
									$itemsize = explode(',', $item->item_size);
									$gmts_color = explode(',', $item->gmts_color);
									$left_qty = explode(',', $item->left_mrf_ipo_quantity);
									$mrf_quantity = explode(',', $item->mrf_quantity);
									$idstrcount = (8 - strlen($item->job_id)); 
								?>
								<tbody>
								<input type="hidden" name="allId[]" value="{{$item->id}}">	
								<tr>
									
									<td>{{ str_repeat('0',$idstrcount) }}{{$item->job_id}}</td>
									<td>{{$item->erp_code}}</td>
									<td>{{$item->item_code}}</td>
									<!-- <td>{{$item->item_code}}</td> -->
									<!-- <td>{{$item->item_code}}</td> -->
									<!-- <td>{{$item->item_code}}</td> -->
									<!-- <td>{{$item->item_code}}</td> -->
									@foreach($itemsize as $keys => $sizes)
									<td>{{$sizes}}</td>
									<td>{{$gmts_color[$keys]}}</td>
									<td>
										<input style="" type="text" class="form-control item_quantity" name="product_qty[]" value="{{$left_qty[$keys]}}" >
									</td>
									<td>
										<input type="text" class="form-control item_mrf" name="item_mrf[]" value="{{$mrf_quantity[$keys]}}" disabled="true">
									</td>
									@endforeach
								</tr>
							</tbody>
							@endforeach
						</table>
						<div class="form-group ">
							<div class="col-md-6 col-md-offset-10">
								<button type="submit" class="btn btn-primary" id="rbutton">
									Genarate
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('input[name="product_qty[]"]').on("keyup",function () {
		var qnty = parseFloat($(this).val());
		var availQnty = parseFloat($(this).attr("value"));
		if(qnty > availQnty){
			alert("Qunatity should be less than balance quantity "+availQnty);
	        $(this).val(availQnty);
		}
	});
</script>
@stop
@section('LoadScript')
  <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/custom.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/booking.js') }}"></script>
@stop