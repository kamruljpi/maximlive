@extends('layouts.dashboard')
@section('page_heading','Generate IPO')
@section('section')

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
@if(sizeof($ipoListValue) >= 1)
	<div class="panel showMrfList">
		<div class="panel-heading">IPO list</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Booking Id</th>
						<th>IPO Id</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php($i=1)
					@foreach($ipoListValue as $details)
					<tr>
						<td>{{$i++}}</td>
						<td>{{$details->booking_order_id}}</td>
						<td>{{$details->ipo_id}}</td>
						<td>
							<form action="{{Route('ipo_list_action_task') }}" role="form" target="_blank">
								<input type="hidden" name="ipoid" value="{{$details->ipo_id}}">
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

<!-- <form action="{{ Route('task_action') }}" method="POST"> -->
<form action="{{ Route('task_ipo_action') }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="booking_order_id" value="{{$sentBillId[0]->booking_order_id}}">
	<table class="table table-bordered mainBody">
	    <thead>
	    	<tr>
	        	<th width="5%">Job No.</th>
	        	<th width="16%">ERP Code</th>
	        	<th width="10%">Item Code</th>
	        	<th>PO/CAT No</th>
	        	<th>Color</th>
	        	<th width="10%">Size</th>
	        	<th width="10%">TOTAL PCS/MTR</th>
	        	<th width="10%">Initial Incrise(%)</th>
	        	<th>1ST DELIVERY</th>
	            <th >Request Date</th>
	            <th>Confirmation Date</th>
	        </tr>
	    </thead>
	    <tbody>
			<?php $increase = $ipoIncrease;?>
    		@foreach ($sentBillId as $key => $item)
				<?php
					$itemsize = explode(',', $item->item_size);
					$gmts_color = explode(',', $item->gmts_color);
					$left_qty = explode(',', $item->left_mrf_ipo_quantity);
					$idstrcount = (8 - strlen($item->job_id));					
				?>
				<input type="hidden" name="ipo_id[]" value="{{$item->id}}">			
    			<tr>
    				<td>{{ str_repeat('0',$idstrcount) }}{{ $item->job_id }}</td>
    				<td>{{$item->erp_code}}</td>
    				<td>{{$item->item_code}}</td>
    				<td>{{$item->poCatNo}}</td>
    				@foreach($itemsize as $keys => $items)
		    		<td>{{$gmts_color[$keys]}}</td>
		    		<td>{{$items}}</td>
		    		<td>
		    			<input style="" type="text" class="form-control item_quantity" name="product_qty[]" value="{{$left_qty[$keys]}}" >
		    		</td>
		    		@endforeach
		    		<td>
		    			<input type="text" name="ipo_increase_percentage[]" value="{{$increase}}" placeholder="Percentage" class="form-control">
		    		</td>
					<td></td>
					<td style="padding-top: 20px;">
						{{Carbon\Carbon::parse($billdata->created_at)->format('d-m-Y')}}
					</td>
					<td></td>
    			</tr>
	    	@endforeach
		</tbody>
	</table>
	<div class="form-group">
		<div class="col-md-2 pull-right">
			<button type="submit" class="btn btn-primary form-control deleteButton" style="margin-right: 15px; font-weight: bold;">Genarate
			</button>
		</div>
	</div>
</form>
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
