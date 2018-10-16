@extends('layouts.dashboard')
@section('page_heading', 'Booking Details update' )
@section('section')
<div class="row">
	<form action="{{route('booking_details_update_action')}}" method="POST">
		{{csrf_field()}}
		<div class="table-responsive" style="">
			<table class="table table-striped" width="100%">
			    <thead>
			        <tr>
			            <th width="15%">Job No.</th>
			            <th width="15%">ERP Code</th>
			            <th width="12%">Item Code</th>
			            <th width="5%">Season Code</th>
			            <th width="15%">Item Description</th>
			            <th width="15%">OOS No.</th>
			            <th width="15%">Style</th>
			            <th width="15%">PO/Cat No.</th>
			            <th width="15%">GMTS Color</th>
			            <th width="10%">Size</th>
			            <th width="15%">Sku</th>
			            <th width="15%">Order Qty</th>
			        </tr>
			    </thead>
			    <tbody>
			    	<tr>
			    		<?php $jobId = (8 - strlen($mxpBooking->id)); ?>
			    		<td><input type="text" name="" class="form-control" disabled="true" value="{{ str_repeat('0',$jobId) }}{{ $mxpBooking->id }}
"></td>
			    		<td><input type="text" name="erp_code" class="form-control" value="{{$mxpBooking->erp_code}}" disabled></td>
			    		<td><input type="text" name="item_code" class="form-control" value="{{$mxpBooking->item_code}}" disabled></td>
			    		<td><input type="text" name="" class="form-control" disabled="true" value="{{$mxpBooking->season_code}}"></td>
			    		<td>
			    			<select class="form-control" name="description">
			    				<option>Choose a option</option>
			    				@foreach($description as $values)
			    					<option>{{$values->name}}</option>
			    				@endforeach
			    			</select>
			    		</td>
			    		<td><input type="text" name="oos_no" class="form-control" value="{{$mxpBooking->oos_number}}"></td>
			    		<td><input type="text" name="style" class="form-control" value="{{$mxpBooking->style}}"></td>
			    		<td><input type="text" name="po_cat_no" class="form-control" value="{{$mxpBooking->poCatNo}}"></td>
			    		<td><input type="text" name="gmts_color" class="form-control" value="{{$mxpBooking->gmts_color}}"></td>
			    		<td><input type="text" name="item_size" class="form-control" value="{{$mxpBooking->item_size}}"></td>
			    		<td><input type="text" name="sku" class="form-control" value="{{$mxpBooking->sku}}"></td>
			    		<td><input type="text" name="quantity" class="form-control" value="{{$mxpBooking->item_quantity}}"></td>
			    	</tr>
			    </tbody>
			</table>
		</div>
		<div class="form-group">
		    <div class="col-sm-2 col-md-2 pull-right">
		        <button type="submit" class="btn btn-primary" style="margin-right: 15px; width: 100%;">
		            {{ trans('others.update_button') }}
		        </button>
		    </div>
		</div>
	</form>
</div>
@endsection
