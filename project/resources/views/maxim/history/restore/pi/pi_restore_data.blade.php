@extends('layouts.dashboard')
@section('page_heading','PI Restore data')
@section('page_heading_right',  Carbon\Carbon::now()->format('d-m-Y'))
@section('section')
	@if (!empty($msg))
	    <div class="alert alert-success">
	        <ul>
	            {{ $msg }}
	        </ul>
	    </div>
	@endif

	@if(Session::has('message'))
	    <div class="alert alert-danger">
	        <ul>
	            {{ Session::get('message') }}
	        </ul>
	    </div>
	@endif

	@if(Session::has('error-m'))
	    <div class="alert alert-danger">
	        <ul>
	            {{ Session::get('error-m') }}
	        </ul>
	    </div>
	@endif

	@include('maxim.history.restore.menu')

	<div class="row" style="background-color: #F1F1F1">

		@include('maxim.history.restore.find')

		<div class="col-md-12 col-md-offset-0">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Serial No.</th>
						<th>Booking No.</th>
						<th>Pi No.</th>
						<th width="14%">Created Date</th>
						<th width="15%">Action</th>
					</tr>
				</thead>
				<tbody>
				@php($j=1 + $data->perPage() * ($data->currentPage() - 1))
				@foreach($data as $value)
					<?php 
						$booking_id = explode(',', $value->booking_order_id);
					?>
					<tr id="mrf_list_table">
						<td>{{$j++}}</td>
						<td>{{$value->booking_order_id}}</td>
						<td>{{$value->p_id}}</td>
						<td>{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}</td>
						<td>
	                        <a href="{{Route('restore_request',[Session::get('type'),$value->p_id])}}" class="btn btn-primary deleteButton"> Restore</a>
	                    </td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div id="">{{$data->links()}}</div>
			<div class="pagination-container">
				<nav>
					<ul class="pagination"></ul>
				</nav>
			</div>
		</div>
	</div>
@endsection
