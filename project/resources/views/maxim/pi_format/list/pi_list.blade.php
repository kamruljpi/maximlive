@extends('layouts.dashboard')
@section('page_heading','PI List' )
@section('page_heading_right',  Carbon\Carbon::now()->format('d-m-Y'))
@section('section')
<?php
use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
$object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
$roleCheck = $object->getRole();
?>
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
<style type="text/css">
    .b1{
        border-bottom-left-radius: 4px;
        border-top-right-radius: 0px;
    }
    .b2{
        border-bottom-left-radius: 0px;
        border-top-right-radius: 4px;
    }
    .btn-group .btn + .btn,
    .btn-group .btn + .btn-group,
    .btn-group .btn-group + .btn,
    .btn-group .btn-group + .btn-group {
        margin-left: -5px;
    }
    .popoverOption:hover{
        text-decoration: underline;
    }
    /*.popper-content ul{
        list-style-type: none;
    }*/
</style>

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
<br>
<div class="row">
	<div class="col-md-12 col-md-offset-0">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Serial No.</th>
					<th>Booking No.</th>
					<th>Pi No.</th>
					<th width="15%">Action</th>
				</tr>
			</thead>
			<tbody>
			@php($j=1 + $piDetails->perPage() * ($piDetails->currentPage() - 1))
			@foreach($piDetails as $value)
				<?php 
					$booking_id = explode(',', $value->booking_order_id);
				?>
				<tr id="mrf_list_table">
					<td>{{$j++}}</td>
					<td>{{$value->booking_order_id}}</td>
					<td>{{$value->p_id}}</td>
					<td>
                        <div class="btn-group">
                            <form action="{{ Route('pi_list_report_view') }}" target="_blank">
                                {{ csrf_field() }}
                                <input type="hidden" name="pid" value="{{$value->p_id}}">
                                <input type="hidden" name="is_type" value="{{$value->is_type}}">
                                <input type="hidden" name="bid" value="{{$booking_id[0]}}">
                                <button class="btn btn-success b1">Report</button>

                                <button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>

                                <ul class="dropdown-menu" style="left:-45px !important;">
                                    <li>
                                        <a  type="button" class="deleteButton" href="{{ Route('pi_edit_action', $value->p_id) }}" >Delete</a>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<div id="">{{$piDetails->links()}}</div>
		<div class="pagination-container">
			<nav>
				<ul class="pagination"></ul>
			</nav>
		</div>
	</div>
</div>
@endsection
