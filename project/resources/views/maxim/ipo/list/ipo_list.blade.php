@extends('layouts.dashboard')
@section('page_heading','Ipo List' )
@section('section')
<style type="text/css">

</style>
<!-- <button class="btn btn-warning" type="button" id="mrf_reset_btn">Reset</button>
<div id="mrf_simple_search_form">
	<div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
		<input type="text" name="mrfIdSearchFld" class="form-control" placeholder="MRF Id search" id="mrf_id_search">
		<button class="btn btn-info" type="button" id="mrf_simple_search">
			Search {{--<i class="fa fa-search"></i>--}}
		</button>
	</div>
	<button class="btn btn-primary " type="button" id="mrf_advanc_search">Advance Search</button>
</div> -->

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
				<label class="col-sm-12 label-control">Booking Id</label>
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
<div class="col-sm-2">
	<a href="" class="btn btn-warning form-control" type="button" id="mrf_reset_btn">Reset</a>
</div>

<div class="form-group custom-search-form col-sm-10">
	<form action="{{ Route('ipo_list_view') }}" method="post">
		{{csrf_field()}}
		<input type="text" name="p_id" class="form-control" placeholder="Search" id="mrf_id_search">
		<button class="btn btn-info" type="submit" id="mrf_simple_search"><i class="fa fa-search"></i></button>
	</form>
</div>
<br>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Serial No.</th>
					<th>Booking No.</th>
					<th>Ipo No.</th>
					<th>Total QTY</th>
					<th>Left QTY</th>
					<th>Ipo Status</th>
					{{-- <th>Total Increased QTY</th> --}}
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php if(isset($ipoDetails) && !empty($ipoDetails)) { ?>
			@php($j=1 + $ipoDetails->perPage() * ($ipoDetails->currentPage() - 1))
			<?php $increase_total_qnty = 0 ?>
			@foreach($ipoDetails as $value)
				<tr id="mrf_list_table">
					<td>{{$j++}}</td>
					<td>{{$value->booking_order_id}}</td>
					<td>{{$value->ipo_id}}</td>
					<td>{{ $value->ipo }}</td>
					<td>{{ $value->left_quantity }}</td>
					<td>{{ ($value->ipo_status == '') ? 'N/A' : $value->ipo_status }}</td>
					{{-- <td> --}}
                        <?php
                            //$p = ( ($value->ipo_quantity * $value->initial_increase)/100) + $value->ipo_quantity;
                          //  echo floor($p);
                        ?>
                    {{-- </td> --}}
					<td>
						<div class="btn-group">
							<form action="{{ Route('ipo_list_report_view') }}" role="form" target="_blank">
								{{ csrf_field() }}
								<input type="hidden" name="ipoid" value="{{$value->ipo_id}}">
								<input type="hidden" name="bid" value="{{$value->booking_order_id}}">
								<button class="btn btn-success" target="_blank">Report</button>
								
								@if( ($value->ipo -  $left_quantity) >= 1 )
								<button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    <span class="caret"></span>
								    <span class="sr-only">Toggle Dropdown</span>
								</button>
								
								<ul class="dropdown-menu" style="left:-45px !important;">
								    <li>
								        <a href="{{ Route('ipo_view', ['id' => $value->job_id]) }}" target="_blank">Views</a>
								    </li>
								</ul>
								@endif
							</form>
						</div>
					</td>
				</tr>
			@endforeach
			<?php } ?>
			</tbody>
		</table>
		<?php if(isset($ipoDetails) && !empty($ipoDetails)) { ?>
			<div id="">{{$ipoDetails->links()}}</div>
		<?php } ?>
		<div class="pagination-container">
			<nav>
				<ul class="pagination"></ul>
			</nav>
		</div>
	</div>
</div>
@endsection