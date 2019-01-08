@extends('layouts.dashboard')
@section('page_heading', 'Location')
@section('section')
	<style type="text/css">
		.top-btn-pro{
			padding-bottom: 15px;
		}
	    .td-pad{
	        padding-left: 15px;
	    }
	</style>

	@if(Session::has('store'))
	    @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('store') ))
	@endif

	@if(Session::has('delete'))
	    @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete') ))
	@endif

	@if(Session::has('update'))
	    @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update') ))
	@endif

	<div class="col-sm-3 top-btn-pro">
		<a href="{{ Route('location_create_view') }}" class="btn btn-success form-control">Create</a>
	</div>

	<div class="col-sm-8">
	    <div class="form-group custom-search-form">
        <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="search" id="user_search">
	    </div>
	</div>

	<div class="col-sm-12 col-md-12">
	    <div class="table-responsive">
	        <table class="table table-bordered" id="tblSearch">
		        <thead>
		            <tr>
		                <th>Location ID</th>
		                <th>Location</th>
		                <th>Status</th>
		                <th>Action</th>
		            </tr>
		        </thead>
		        <tbody>
			        @foreach($details as $detail)
			            <tr>  
			                <td>{{$detail->id_location}}</td>
			                <td>{{$detail->location}}</td>
			                <td>
			                    {{($detail->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
			                </td>

			                <td>
			                    <a href="{{ Route('location_edit_view')}}/{{$detail->id_location}}" class="btn btn-success">edit</a>
			                    <a href="{{ Route('location_delete_action')}}/{{$detail->id_location}}" class="btn btn-danger">delete</a>
			                </td>
			            </tr>
			        @endforeach 
		            
		        </tbody>
	    	</table>
	     	{{$details->links()}}
	    </div>    
	</div>
@stop
@section('LoadScript')
	<script type="text/javascript" src="{{asset('assets/scripts/vendor/search.js')}}"></script>
@stop