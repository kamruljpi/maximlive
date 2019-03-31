@extends('layouts.dashboard')
@section('page_heading','Stage List Page')
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

    <div class="col-sm-2 top-btn-pro">
        <a href="{{ Route('stage_create_view') }}" class="btn btn-success form-control">
        Create</a>
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
	                    	<th width="10%">No.</th>
                            <th width="30%">Name</th>
	                    	<th width="30%">Status</th>
	                        <th width="25%">Action</th>
	                    </tr>
	                </thead>
                    <tbody>  
                        @php($j=1 + $details->perPage() * ($details->currentPage() - 1))
                        @foreach($details as $detail)                  
                            <tr>                        	
                            	<td>{{$j++}}</td>
                                <td>{{$detail->name}}</td>                              
                            	<td>{{$detail->is_active == 1 ? 'Active' : 'Inactive'}}</td>            	            	
                            	<td>
                                    <a href="{{ Route('stage_edit_view')}}/{{$detail->id_stage}}" class="btn btn-success">edit</a>

                                    <a href="{{ Route('stage_delete_action')}}/{{$detail->id_stage}}" class="btn btn-danger">delete</a>
                                    
                            	</td>
                            </tr>                    
                        @endforeach                      
                    </tbody>
                </table>
                {{$details->links()}}
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
@endsection
@section('LoadScript')
  <script type="text/javascript">
      $('#search').on('keyup',function(){
          $value = $(this).val();
          $.ajax({
              type : 'get',
              url : '{{ Route('buyer_simple_searchs') }}',
              data:{'search':$value},
              success:function(data){
                  $('tbody').html(data);
              }
          });
      })
  </script>
@endsection