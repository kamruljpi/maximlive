@extends('layouts.dashboard')
@section('page_heading',trans('others.Gmts_color_list_label'))
@section('section')
    <style type="text/css">
    	.top-btn-pro{
    		padding-bottom: 15px;
    	}
        .td-pad{
            padding-left: 15px;
        }
    </style>
    @if(Session::has('add_gmtscolor'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('add_gmtscolor') ))
    @endif
    @if(Session::has('update_gmtscolor'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update_gmtscolor') ))
    @endif
    @if(Session::has('delete_gmts_color'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete_gmts_color') ))
    @endif

    <div class="row">        
        <div class="col-md-12 col-xs-12">
    		<div class="col-sm-3 top-btn-pro">
    		 	<a href="{{ Route('add_color_view') }}" class="btn btn-success form-control">
    		        {{trans('others.add_color_label')}}
    		    </a>
    		</div>
    		<div class="col-sm-9">
    		    <div class="form-group custom-search-form">
    		        <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="Search" id="search">
    		        <button class="btn btn-default" type="button">
    		            <i class="fa fa-search"></i>
    		        </button>
    		    </div>
    		</div>
    	</div>
    	<div class="col-xs-12 col-md-12 ">
            <table class="table table-bordered" id="tblSearch">
                <thead>
                    <tr>
                    	<th>Color No</th>
                    	{{--<th>Item Code</th>--}}
                    	<th>GMTS Color</th>
                    	<th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php($j=1 + $gmtsColor->perPage() * ($gmtsColor->currentPage() - 1))
                    @foreach($gmtsColor as $color)                  
                        <tr>                        	
                        	<td>{{$j++}}</td>
                        {{--<td>{{$color->item_code}}</td>--}}        	
                        	<td>{{$color->color_name}}</td>                	
                        	<td>
                            {{($color->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
                          </td>                	
                        	<td>
                                 <a href="{{ Route('update_gmtscolor_view')}}/{{$color->id}}" class="btn btn-success">edit</a>

                                <a href="{{ Route('delete_gmtscolor_action')}}/{{$color->id}}" class="btn btn-danger">delete</a>                                
                        	</td>
                         </tr>                    
                    @endforeach 
                </tbody>
            </table>
            {{$gmtsColor->links()}}
        </div>
    </div>
@endsection
@section('LoadScript')
  <script type="text/javascript">
      $('#search').on('keyup',function(){
          $value = $(this).val();
          $.ajax({
              type : 'get',
              url : '{{ Route('item_color_simple_searchs') }}',
              data:{'search':$value},
              success:function(data){
                  $('tbody').html(data);
              }
          });
      })
  </script>
@endsection