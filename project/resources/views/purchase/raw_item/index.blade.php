@extends('layouts.dashboard')
@section('page_heading','Raw Item')
@section('section')
    <style type="text/css">
    	.top-btn-pro{
    		padding-bottom: 15px;
    	}
        .td-pad{
            padding-left: 15px;
        }
    </style>

    @if(Session::has('create'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('create') ))
    @endif 
    @if(Session::has('delete'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete') ))
    @endif
    @if(Session::has('update'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update') ))
    @endif   

    <div class="col-sm-3 top-btn-pro">
        <a href="{{ Route('raw_item_create_view') }}" class="btn btn-success form-control" style="font-weight: bold;">
        New Item</a>
    </div>

    <div class="col-sm-6">
        <div class="form-group custom-search-form">
            <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="Search" id="search">
            <button class="btn btn-default" type="button">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="row"> 
        	<div class="col-sm-1"></div>
    		<div class="col-sm-10">
            	<table class="table table-bordered">
	                <thead>
	                    <tr>
	                    	<th>Sr#</th>
                            <th>Item Code</th>
                            <th>Opening Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
	                    </tr>
	                </thead>
                    <tbody>
                        @if(count($details) > 0)
                            @php($j=1 + $details->perPage() * ($details->currentPage() - 1))
                            @foreach($details as $detail)                  
                                <tr>                        	
                                	<td>{{$j++}}</td>
                                    <td>{{$detail->item_code}}</td>                         
                                    <td>{{$detail->opening_quantity}}</td>                       
                                    <td>{{$detail->price}}</td>                      
                                	<td>{{$detail->is_active == 1 ? 'Active' : 'Inactive'}}</td>            	            	
                                	<td>
                                        <a href="{{ Route('raw_item_edit_view')}}/{{$detail->id_raw_item}}" class="btn btn-success">edit</a>

                                        <a href="{{ Route('raw_item_delete_action')}}/{{$detail->id_raw_item}}" class="btn btn-danger">delete</a>
                                        
                                	</td>
                                </tr>                    
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">
                                    <div style="text-align: center;font-size: 16px;font-weight: bold;"> Data not found.</div>
                                </td>
                            </tr>                  
                        @endif                   
                    </tbody>
                </table>
                @if(!empty($details))
                    {{$details->links()}}
                @endif
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
@endsection