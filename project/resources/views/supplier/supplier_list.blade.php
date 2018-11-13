@extends('layouts.dashboard')
{{--@section('page_heading', trans('others.party_list_label'))--}}
@section('page_heading', 'Supplier List')
@section('section')
<style type="text/css">
	.top-btn-pro{
		padding-bottom: 15px;
	}
    .td-pad{
        padding-left: 15px;
    }
</style>


<!-- <div class="row"> -->
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
 	<a href="{{ Route('supplier_add_view') }}" class="btn btn-success form-control">
        Add Supplier
    </a>
 </div>
<div class="col-sm-6">
    <div class="form-group custom-search-form">
        <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="search" id="user_search">
        <button class="btn btn-default" type="button">
            <i class="fa fa-search"></i>
        </button>
    </div>
</div>
<div class="col-sm-12 col-md-12">
  <div class="table-responsive">
      <table class="table table-bordered" id="tblSearch">
      <thead>
          <tr>
              <th class="">Sl</th>
              <th class="">Supplier Name</th>
              <th class="">Email Address</th>
              <th class="">Person Name</th>
              <th class="">Address</th>
              <th class="">Status</th>
              <th class="">Action</th>
          </tr>
      </thead>
      <tbody>
        @php($j=1 + $suppliers->perPage() * ($suppliers->currentPage() - 1))
        @foreach($suppliers as $key => $supplier)
          <tr>
            <td>{{$j++}}</td>
            <td>{{$supplier->name}}</td>
            <td>{{$supplier->email}}</td>
            <td>{{$supplier->person_name}}</td>
            <td>{{$supplier->address}}</td>
            <td>
                {{($supplier->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
            </td>
            <td>
                <a href="{{ Route('supplier_update')}}/{{$supplier->supplier_id}}" class="btn btn-success">edit</a>
                <a href="{{ Route('supplier_delete_action')}}/{{$supplier->supplier_id}}" class="btn btn-danger">delete</a>
            </td>
          </tr>
        @endforeach           
      </tbody>
  </table>
  {{$suppliers->links()}}
  </div> 
</div>
@stop