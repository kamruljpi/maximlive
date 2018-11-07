@extends('layouts.dashboard')
@section('page_heading', trans("others.new_mrf_create_label"))
@section('section')
<?php 
	// print_r("<pre>");
	// print_r($bookingDetails[0]);
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
				<div class="panel-heading">{{trans('others.new_mrf_create_label')}}</div>
				<div class="panel-body aaa">
					<form class="form-horizontal" role="form" method="POST" action="{{ Route('mrf_action_task') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="booking_order_id" value="{{$bookingDetails[0]->booking_order_id}}">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="col-sm-12 label-control">Order Date</label>
								<div class="col-sm-12">
									<input id="order_date" class="form-control" type="text" name="order_date" required readonly="true" value="{{carbon\carbon::today()->format('d-m-Y')}}">
								</div>
							</div>
						</div>

						<div class="col-sm-6">
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
									<th>Job Id</th>
									<th>OOS No</th>
									<th>PO/Cat No.</th>
									<th>Item Code</th>
									<th>ERP Code</th>
									<th>Description</th>
									<th>Style</th>
									<th>sku</th>
									<th>GMTS Color</th>
									<th>Size</th>
									<th>Quantity</th>
									<th>MRF QTY</th>
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
									<td>{{$item->oos_number}}</td>
									<td>{{$item->poCatNo}}</td>
									<td>{{$item->item_code}}</td>
									<td>{{$item->erp_code}}</td>
									<td>{{$item->item_description}}</td>
									<td>{{$item->style}}</td>
									<td>{{$item->sku}}</td>
									@foreach($itemsize as $keys => $sizes)
									<td>{{$gmts_color[$keys]}}</td>
									<td>{{$sizes}}</td>
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
							<div class="col-md-2 pull-right">
								<button type="submit" class="btn btn-primary deleteButton form-control" id="rbutton">
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