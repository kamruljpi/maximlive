@extends('layouts.dashboard')
@section('page_heading','Purchase Order List')
@section('section')

	<button class="btn btn-warning" type="button" id="mrf_reset_btn">Reset</button>
	<div id="mrf_simple_search_form">
		<div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
			<input type="text" name="mrfIdSearchFld" class="form-control" placeholder="PO No Search" id="mrf_id_search">
			<button class="btn btn-info" type="button" id="mrf_simple_search">
				Search{{--<i class="fa fa-search"></i>--}}
			</button>
		</div>
		<button class="btn btn-primary " type="button" id="mrf_advanc_search">Advance Search</button>
	</div>
	<div>
		<form id="mrf_advance_search_form"  style="display: none" method="post">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="row">
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Create date from</label>
					<input type="date" name="from_create_date_search" class="form-control" id="from_create_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Create date to</label>
					<input type="date" name="to_create_date_search" class="form-control" id="to_create_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Shipment date from</label>
					<input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Shipment date to</label>
					<input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label class="col-sm-12 label-control">Booking No.</label>
					<input type="text" name="booking_id_search" class="form-control" placeholder="Booking Id search" id="booking_id_search">
				</div>
				<div class="col-sm-4">
					<label class="col-sm-12 label-control">Mrf Status</label>
					<select class="form-control selections" name="mrf_status" id="mrf_status">
						<option value="Open">Open</option>
						<option value="Waiting_for_Goods">Waiting for Goods</option>
						<option value="Delivered">Delivered</option>
					</select>
				</div>
				<br>
				<div class="col-sm-4">
					<input class="btn btn-info" type="submit" value="Search" name="mrf_advanceSearch_btn" id="mrf_advanceSearch_btn">
				</div>
			</div>
			<button class="btn btn-primary" type="button" id="mrf_simple_search_btn">Simple Search</button>
		</form>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Serial no</th>
						{{-- <th>Booking No.</th> --}}
						<th>MRF No.</th>
						<th>PO No.</th>
						<th>Order Date</th>
						<th>Requested Shipment Date</th>
						<th>Action</th>
					</tr>
				</thead>

				@php($j=1 + $poList->perPage() * ($poList->currentPage() - 1))
				<tbody id="mrf_list_tbody">
				@foreach($poList as $value)
					<tr id="mrf_list_table">
						<td>{{$j++}}</td>
						{{-- <td>{{$value->booking_order_id}}</td> --}}
						<td>{{$value->mrf_id}}</td>
						<td>{{$value->po_id}}</td>
						<td>{{Carbon\Carbon::parse($value->created_at)}}</td>
						<td>{{$value->shipment_date}}</td>
						<td>
							<form action="{{Route('os_po_report_view') }}" role="form" target="_blank">
								{{-- <input type="hidden" name="mid" value="{{$value->mrf_id}}"> --}}
								<input type="hidden" name="poid" value="{{$value->po_id}}">
								<button class="btn btn-success" target="_blank">Report</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div id="mrf_list_pagination">{{$poList->links()}}</div>
			<div class="pagination-container">
				<nav>
					<ul class="pagination"></ul>
				</nav>
			</div>
		</div>
	</div>
@endsection
