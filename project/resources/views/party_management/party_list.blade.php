@extends('layouts.dashboard')
@section('page_heading',
trans('others.party_list_label'))
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
	<a href="{{ Route('party_create') }}" class="btn btn-success form-control">
    {{trans('others.add_party_label')}}</a>
</div>

<div class="col-sm-8">
    <div class="form-group custom-search-form">
        <input type="text" name="searchFld" class="form-control" placeholder="Vendor Name" id="vendor_search">
    </div>
</div>

<div class="col-sm-12 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered" id="vendor_tbody">
        <thead>
            <tr>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
                <th>Brand</th>
                <th>Address (Invoice)</th>
                <th>Address (Delivery)</th>
                <th>Attention (Invoice)</th>
                <th>Mobile (Invoice)</th>
                <!-- <th>Fax(Invoice)</th>
                <th>Address(Delivery)</th>
                <th>Mobile(Delivery)</th>
                <th>Fax(Delivery)</th>
                <th>Description</th> -->
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($party_list as $party)
            <tr>  
                <td>{{$party->party_id}}</td>
                <td>{{$party->name}}</td>
                <td>{{$party->name_buyer}}</td>
                <td>{{$party->address_part1_invoice}}</td>
                <td>{{$party->address_part1_delivery}}</td>
                <td>{{$party->attention_invoice}}</td>
                <td>{{$party->mobile_invoice}}</td>
                <!-- <td>{{$party->fax_invoice}}</td>
                <td>{{$party->address_part1_delivery}}</td>
                <td>{{$party->mobile_delivery}}</td>
                <td>{{$party->fax_delivery}}</td>
                <td>{{$party->description_1}}</td> -->
                <td>
                    {{($party->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
                </td>

                <td>
                    <a href="{{ Route('party_edit_view')}}/{{$party->id}}" class="btn btn-success">edit</a>
                    <a href="{{ Route('party_delete_action')}}/{{$party->id}}" class="btn btn-danger">delete</a>
                </td>
            </tr>
        @endforeach 
            
        </tbody>
    </table>
     {{$party_list->links()}}
    </div>    
</div>
@stop
@section('LoadScript')
<script type="text/javascript" src="{{asset('assets/scripts/vendor/search.js')}}"></script>
@stop