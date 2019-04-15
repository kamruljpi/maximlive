@extends('layouts.dashboard')
@section('page_heading',trans('others.product_list_label'))
@section('section')
  
  <?php
      use App\Http\Controllers\Source\User\RoleDefine;

     /** auth user PID role check **/

      $object = new RoleDefine();
      $role_check_os = $object->getRole('OS');
      $role_check = $object->getRole('Product');
      $role_check_planning = $object->getRole('Planning');

      /** End**/
  ?>

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
  
  @if($role_check == 'product' || Auth::user()->type == 'super_admin')
    <div class="col-sm-3">        
      <a href="{{ Route('itemupload') }}" type="button" class="btn btn-success">
        <i class="fa f == 'super_admin'a-file" style="font-size:`16px;color:white; margin-right:7px;">
      </i> Bulk Upload</a>      
    </div>
  @endif

  <div class="col-sm-12">
    <table class="table table-bordered" id="tblSearch">
        <thead>
            <tr>
                <th>Item No</th>
                <th>Brand</th>                        
                <th>Item Code</th>
                <th>ERP Code</th>
                <th>Description</th>

                @if($role_check_planning == 'planning' || $role_check_os == 'os')
                @else
                  <th>Unit Price</th>
                  <th>Cost Price 1</th>
                @endif

                <th>Item Size</th>
                <th>Size Range</th>
                <th>Colors</th>
                <th>status</th>
                @if($role_check_planning == 'planning' || $role_check_os == 'os')
                @else
                  <th>Action</th>                        
                @endif
            </tr>
        </thead>
        <tbody>
             @foreach($products as $product)
                <tr {{ (Session::get('item_id') == $product->product_id) ? 'style=background-color:#DAF6D7' : ''}}>
                  <td>{{$product->product_id}}</td>
                  <td>{{$product->brand}}</td>
                  <td>{{$product->product_code}}</td>
                  <td>{{$product->erp_code}}</td>
                  <td>{{$product->description->name}}</td>
                  
                  @if($role_check_planning == 'planning' || $role_check_os == 'os')
                  @else
                    <td>{{(isset($product->unit_price) ? number_format($product->unit_price, 5, '.', '') : '') }}</td>

                    <td>{{(isset($product->cost_price->price_1) ? $product->cost_price->price_1 :'')}}</td>
                  @endif

                  <td>{{(!empty($product->item_size_width_height))?$product->item_size_width_height.' mm' :''}}</td>

                  <td>
                    <div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;">
                      <table>
                        <td>
                            @foreach($product->sizes as $size)
                              {{ $size->product_size }}@if (!$loop->last),@endif
                            @endforeach
                        </td>
                      </table>
                    </div>
                  </td>

                  <td>
                    <div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;">
                      <table>
                        <td>
                          @foreach($product->colors as $color)
                            {{$color->color_name}}@if (!$loop->last),@endif
                          @endforeach
                        </td>
                      </table>
                    </div>
                  </td>                  

                  <td>
                    {{($product->status == 1)? trans("others.action_active_label"):trans("others.action_inactive_label")}}
                  </td>

                  @if($role_check_planning == 'planning' || $role_check_os == 'os')
                  @else
                    <td>
                        <a href="{{ Route('update_product_view')}}/{{$product->product_id}}" class="btn btn-success">edit</a>
                        <a href="{{ Route('delete_product_action')}}/{{$product->product_id}}" class="btn btn-danger deleteButton">delete</a>
                    </td>
                  @endif
                </tr>
            @endforeach      
                  
        </tbody>
    </table>           
    {{$products->links()}}
  </div>
@endsection
@section('LoadScript')
  <script type="text/javascript">
      $('#search').on('keyup',function(){
          $value= $(this).val();
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
@endsection