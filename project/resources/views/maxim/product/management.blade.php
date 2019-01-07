@extends('layouts.dashboard')
@section('page_heading', "Product Management" )
@section('section')

<div class="row">
	<div class="col-md-12 col-md-offset-0">
		@if ($errors->any())
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
		<div class="panel panel-default">
			<div class="panel-heading">Ipo Details</div>
			<div class="panel-body aaa">
				<table class="table table-bordered table-striped" >
					<thead>
						<tr>
							<th>Job Id</th>
							<th>IPO Id</th>
							<th>Booking Id</th>
							<th>Item Code</th>
							<th>ERP Code</th>
							<th>Description</th>
							<th>GMTS Color</th>
							<th>Size</th>
							<th>Shipment Date</th>
							<th>IPO Quantity</th>
							<th>Receive QTY</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($ipo_details as $values)
						<?php
							$idstrcount = (8 - strlen($values->job_id)); 
						?>
						<form action="{{ Route('store_ipo') }}" method="POST">
						{{csrf_field()}}
						<input type="hidden" name="job_id" value="{{ $values->job_id }}">
						<input type="hidden" name="is_type" value="ipo">
						<tr>
							<td><input type="hidden" name="job_id" value="{{ $values->job_id }}">{{ str_repeat('0',$idstrcount) }}{{$values->job_id}}</td>
							<td><input type="hidden" name="ipo_id" value="{{ $values->ipo_id }}">{{ $values->ipo_id }}</td>
							<td><input type="hidden" name="booking_order_id" value="{{ $values->booking_order_id }}">{{ $values->booking_order_id }}</td>
							<td><input type="hidden" name="item_code" value="{{ $values->item_code }}">{{ $values->item_code }}</td>
							<td><input type="hidden" name="erp_code" value="{{ $values->erp_code }}">{{ $values->erp_code }}</td>
							<td><input type="hidden" name="item_description" value="{{ $values->item_description }}">{{ $values->item_description }}</td>
							<td><input type="hidden" name="gmts_color" value="{{ $values->gmts_color }}">{{ $values->gmts_color }}</td>
							<td><input type="hidden" name="item_size" value="{{ $values->item_size }}">{{ $values->item_size }}</td>
							<td><input type="hidden" name="shipment_date" value="{{ $values->shipmentDate }}">{{ $values->shipmentDate }}</td>
							<td><input type="hidden" name="ipo_quantity" value="{{ $values->ipo_quantity }}">{{ $values->ipo_quantity }}</td>
							<td><input type="text" name="receive_qty"></td>
							<td><button class="btn btn-success">Accept</button></td>
						</tr>
						</form>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection