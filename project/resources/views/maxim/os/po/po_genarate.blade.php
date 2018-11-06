@extends('layouts.dashboard')
@section('page_heading','New PO Genarate')
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
			<div class="panel panel-default">
				<div class="panel-heading">New PO Genarate</div>
				<div class="panel-body aaa">
					<form class="form-horizontal" role="form" method="POST" action="{{ Route('os_po_genarate_report_action') }}">
						{{ csrf_field() }}

						<input type="hidden" name="mrf_id" value="">

						<div class="col-sm-4">
							<div class="form-group">
								<label class="col-sm-12 label-control">Order Date</label>
								<div class="col-sm-12">
									<input id="order_date" class="form-control " type="text" name="order_date" required placeholder="Order Date" readonly="true" value="{{carbon\carbon::today()->format('d-m-Y')}}">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="col-sm-12 label-control">Supplier</label>
								<div class="col-sm-12">
									<select class="form-control" name="supplier_id" required>
										<option value="">Choose a Option</option>
										<option value="d">Choose a Option</option>
										@foreach($suppliers as $supplier)
											<option value="{{$supplier->supplier_id}}">{{$supplier->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="col-sm-12 label-control">Requested Shipment Date</label>
								<div class="col-sm-12">
									<input id="datePickerDate" class="form-control" type="Date" name="mrf_shipment_date" required>
								</div>
							</div>
						</div>						

						<table class="table table-bordered table-striped" >
							<thead>
								<tr>
									<th width="">Job Id</th>
									<th width="">OOS No</th>
									<th width="">PO/Cat No.</th>
									<th width="">Item Code</th>
									<th width="">ERP Code</th>
									<th width="">Description</th>
									{{-- <th width="">Season Code</th> --}}
									{{-- <th width="">Style</th> --}}
									{{-- <th width="">sku</th> --}}
									<th width="">GMTS Color</th>
									<th width="">Size</th>
									<th width="">Quantity</th>
									<th width="">Price</th>
									<th width="20%">Material</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>0001
										<input type="hidden" name="job_id[]" value="">
									</td>
									<td>0001</td>
									<td>0001</td>
									<td>0001</td>
									<td>0001</td>
									{{-- <td>0001</td> --}}
									{{-- <td>0001</td> --}}
									{{-- <td>0001</td> --}}
									<td>0001</td>
									<td>0001</td>
									<td>0001</td>
									<td>0001</td>
									<td>
										<input type="text" name="supplier_price[]" class="form-control" value="" readonly="true">
									</td>
									<td>
										<input id="material" class="form-control " type="text" name="material[]" placeholder="Material">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="form-group ">
							<div class="col-md-2 pull-right">
								<button type="submit" class="btn btn-primary form-control deleteButton" id="rbutton">
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