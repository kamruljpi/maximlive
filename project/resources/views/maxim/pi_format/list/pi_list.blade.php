@extends('layouts.dashboard')
@section('page_heading','PI List' )
@section('page_heading_right',  Carbon\Carbon::now()->format('d-m-Y'))
@section('section')
	<?php
		use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
		$object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
		$roleCheck = $object->getRole();
	?>
	
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

	<div class="col-sm-2">
		<a href="{{Route('pi_list_view')}}" class="btn btn-warning form-control" type="button" id="mrf_reset_btn">Reset</a>
	</div>

	<div class="form-group custom-search-form col-sm-10">
		<form action="{{Route('pi_list_search')}}" method="post">
			{{csrf_field()}}
			<input type="text" name="p_id" class="form-control" placeholder="PI No." id="mrf_id_search">
			<button class="btn btn-info" type="submit" id="mrf_simple_search"><i class="fa fa-search"></i></button>
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
