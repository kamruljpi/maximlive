@extends('layouts.dashboard')
@section('page_heading','Accepted Ipo List' )
@section('section')
<style type="text/css">

</style>

<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Job Id.</th>
					<th>Booking No.</th>
					<th>Ipo No.</th>
					<th>Quantity</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($ipoList as $value)
				<?php
					$idstrcount = (8 - strlen($value->job_id)); 
				?>
					<tr>
						<td>{{ str_repeat('0',$idstrcount) }}{{ $value->job_id }}</td>
						<td>{{ $value->booking_order_id }}</td>
						<td>{{ $value->product_id }}</td>
						<td>{{ $value->item_quantity }}</td>
						<td>{{ $value->status }}</td>
						<td><button class="btn btn-danger">Report</button></td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<div id=""></div>
		<div class="pagination-container">
			<nav>
				<ul class="pagination"></ul>
			</nav>
		</div>
	</div>
</div>
@endsection