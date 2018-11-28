@extends('layouts.dashboard')
@section('page_heading',trans('others.product_list_label'))
@section('section')
<style type="text/css">
	.top-btn-pro{
		padding-bottom: 15px;
	}
    .td-pad{
        padding-left: 15px;
    }
</style>
    @if(Session::has('new_product_create'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('new_product_create') ))
    @endif 
    @if(Session::has('new_product_delete'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('new_product_delete') ))
    @endif
    @if(Session::has('update_product_create'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update_product_create') ))
    @endif

 <div class="col-sm-3 top-btn-pro">
    <a href="{{ Route('add_product_view') }}" class="btn btn-success form-control">{{trans('others.add_product_label')}}</a>
  </div>
  <div class="col-sm-6">
      <div class="form-group custom-search-form">
        <input type="text" class="form-control keyup_preloder" id="search" name="search" placeholder="Item Code"></input>
      </div>
  </div>

<div class="col-sm-12">
  <table class="table table-bordered" id="tblSearch">
      <thead>
          <tr>
              <th>Item No</th>
              <th class="">Brand</th>                        
              <th class="">Item Code</th>
              <th class="">ERP Code</th>
              {{--<th class="">Item Name</th>--}}
              <th class="">Description</th>
              <th class="">Unit Price</th>
              <th width="20%">Item Size</th>
              <th>Size Range</th>
              <th class="">Colors</th>
              <!-- <th class="">Weight Qty</th> -->
              <!-- <th class="">Weight Amt</th> -->
              <th class="">status</th>
              <th class="">Action</th>                        
          </tr>
      </thead>
      <tbody>
           @foreach($products as $product)
              <tr {{ (Session::get('item_id') == $product->product_id) ? 'style=background-color:#DAF6D7' : ''}}>
                <td>{{$product->product_id}}</td>
                <td>{{$product->brand}}</td>
                <td>{{$product->product_code}}</td>
                <td>{{$product->erp_code}}</td>
            {{--<td>{{$product->product_name}} </td>--}}
                <td>{{$product->description->name}}</td>
                
                <td>{{$product->unit_price}}</td>
                <td>{{(!empty($product->item_size_width_height))?$product->item_size_width_height.' mm' :''}}</td>
                <!-- <td>{{$product->weight_qty}}</td> -->
                <!-- <td>{{$product->weight_amt}}</td> -->
                <td>
                    @foreach($product->sizes as $size)
                        {{ $size->product_size }}@if (!$loop->last),@endif
                    @endforeach
                </td>

                <td>
                    @foreach($product->colors as $color)
                        {{$color->color_name}}@if (!$loop->last),@endif
                    @endforeach
                </td>
                <td >
                  {{($product->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
                </td>
                <td >
                    <a href="{{ Route('update_product_view')}}/{{$product->product_id}}" class="btn btn-success">edit</a>
                    <a href="{{ Route('delete_product_action')}}/{{$product->product_id}}" class="btn btn-danger">delete</a>
                </td>
              </tr>
          @endforeach      
                
      </tbody>
  </table>           
  {{$products->links()}}
</div>
@stop

@section('LoadScript')
    <script type="text/javascript">

        $('#search').on('keyup',function(){

            $value=$(this).val();

            $.ajax({

                type : 'get',

                url : '{{URL::to('product/lists')}}',

                data:{'search':$value},

                success:function(data){

                    $('tbody').html(data);
                }

            });
        })

    </script>

    <script type="text/javascript">

        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });

    </script>

@stop