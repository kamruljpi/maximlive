@extends('layouts.dashboard')
@section('page_heading','New PO Genarate')
@section('section')
<?php 
	//print_r("<pre>");
   	//print_r($jobid_values[0]);
    //print_r("</pre>");

    use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
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

						<input type="hidden" name="mrf_id" value="{{$jobid_values[0]->mrf_id}}">
						<input type="hidden" name="supplier_id" value="{{$supplier->supplier_id}}">

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
									<input type="text" class="form-control" value="{{$supplier->name}}" readonly="true">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="col-sm-12 label-control">Requested Shipment Date</label>
								<div class="col-sm-12">
									<input id="datePickerDate" class="form-control" type="Date" name="shipment_date" required>
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
									<th width="30%">Supplier Price</th>
									<th width="50%">Material</th>
								</tr>
							</thead>
							<tbody>
								@foreach($jobid_values as $values)
								<?php 
								    $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($values->job_id));
								?>
								<input type="hidden" name="job_id[]" value="{{$values->job_id}}">
									<tr>
										<td>{{ str_repeat(JobIdFlugs::STR_REPEAT ,$idstrcount) }}{{$values->job_id}}</td>
										<td>{{$values->oos_number}}</td>
										<td>{{$values->poCatNo}}</td>
										<td>{{$values->item_code}}</td>
										<td>{{$values->erp_code}}</td>
										<td>{{$values->item_description}}</td>
										<td>{{$values->gmts_color}}</td>
										<td>{{$values->item_size}}</td>
										<td>{{$values->mrf_quantity}}</td>
										<td>
											<input type="text" name="supplier_price[]" class="form-control" value="{{$values->item_price->supplier_price}}" readonly="true">
										</td>
										<td>
											<input id="material" class="form-control " type="text" name="material[]" placeholder="Material">
										</td>
									</tr>
								@endforeach
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