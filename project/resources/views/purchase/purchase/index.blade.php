@extends('layouts.dashboard')
@section('page_heading','Purchase')
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
        <a href="{{ Route('purchase_create_view') }}" class="btn btn-success form-control" style="font-weight: bold;">
        New Purchase</a>
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
                            <th>ID</th>
                            <th>Date</th>
                            {{-- <th>Account Name</th> --}}
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Payment Method</th>
	                        <th>Manage</th>
	                    </tr>
	                </thead>
                    <tbody>
                        @if(count($details) > 0)
                            @php($j=1 + $details->perPage() * ($details->currentPage() - 1))
                            @foreach($details as $detail)                  
                                <tr>                        	
                                	<td>{{$j++}}</td>
                                    <td>{{$detail->purchase_voucher}}</td>         	            	
                                    <td>{{$detail->order_date}}</td>                                
                                    <td>{{$detail->discount}}</td>                              
                                    <td>{{$detail->in_all_total_price}}</td>
                                    <td>{{$detail->paying_by}}</td>                              
                                	<td>
                                        <a href="{{ Route('stage_edit_view')}}/{{$detail->id_stage}}" class="btn btn-success">edit</a>

                                        <a href="{{ Route('stage_delete_action')}}/{{$detail->id_stage}}" class="btn btn-danger">delete</a>
                                        
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