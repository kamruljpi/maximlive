@extends('layouts.dashboard')
@section('page_heading', 'Warehouse In Type List')
@section('section')
<style type="text/css">
	.top-btn-pro{
		padding-bottom: 15px;
	}
    .td-pad{
        padding-left: 15px;
    }
</style>

@if(Session::has('party_added'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('party_added') ))
@endif

@if(Session::has('party_delete'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('party_delete') ))
@endif

@if(Session::has('party_updated'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('party_updated') ))
@endif

<div class="col-sm-3 top-btn-pro">
	<a href="{{ Route('warehouseintype') }}" class="btn btn-success form-control">
    Add Warehouse In Type</a>
</div>



<div class="col-sm-12 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered" id="vendor_tbody">
        <thead>
            <tr>
                <th>ID</th>
                <th>Warehouse Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($warehouse_type_list as $warehouseList)
            <tr>  
                <td>{{$warehouseList->id_warehouse_type}}</td>
                <td>{{$warehouseList->warehouse_type}}</td>
                <td>
                    {{($warehouseList->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
                </td>

                <td>
                    <a href="{{ Route('warehouseintypeupdateView') }}/{{$warehouseList->id_warehouse_type}}" class="btn btn-success">edit</a>
                    <a href="{{ Route('warehouseintypedelete')}}/{{$warehouseList->id_warehouse_type}}" class="btn btn-danger">delete</a>
                </td>
            </tr>
        @endforeach 
            
        </tbody>
    </table>
     
    </div>    
</div>
@stop
@section('LoadScript')
<script type="text/javascript" src="{{asset('assets/scripts/vendor/search.js')}}"></script>
@stop