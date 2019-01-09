@extends('layouts.dashboard')
@section('page_heading','Product Entry')
@section('section')
	<div class="container-fluid">
	    <!-- <div class="row">
	        <div class="col-sm-2">
	            <div class="form-group ">
	                <a href="{{ Route('location_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
	                <i class="fa fa-arrow-left"></i> Back</a>
	            </div>
	        </div>
	    </div> -->

	    @if(Session::has('store'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('store') ))
	    @endif

	    @if(Session::has('delete'))
	        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete') ))
	    @endif

	    @if(Session::has('update'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update') ))
	    @endif

	    <div class="row">
	        {{-- Product Entry Column --}}
	        <div class="col-md-12 col-sm-12">
	            @if(count($errors) > 0)
	                <div class="alert alert-danger" role="alert">
	                    @foreach($errors->all() as $error)
	                      <li><span>{{ $error }}</span></li>
	                    @endforeach
	                </div>
	            @endif
	            <div class="panel panel-default">
	                <!-- <div class="panel-heading">Create Location</div> -->
	                <div class="panel-body">
	                    {{-- <form class="form-horizontal" action="{{ Route('store_opening_stock_action') }}" role="form" method="POST" >

	                        {{csrf_field()}}

	                        <div class="row">
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Product Id</label>
	                        			<div class="col-sm-12">
	                                        <select class="select_2" name="product_id" id="product_id">
	                                            <option value="">-- Select --</option>
	                                            @foreach($product as $item)
	                                            	<option value="{{$item->id}}">{{$item->product_id}}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>
                                    </div>
	                        	</div>

	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Booking Id</label>
	                        			<div class="col-sm-12">
	                                        <input type="text" name="booking_order_id" id="booking_order_id" class="form-control">
	                                    </div>
                                    </div>
	                        	</div>
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Item Code</label>
	                        			<div class="col-sm-12">
	                                        <input type="text" name="item_code" id="item_code"class="form-control">
	                                    </div>
                                    </div>
	                        	</div>
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Erp Code</label>
	                        			<div class="col-sm-12">
	                                        <input type="text" name="erp_code" id="erp_code" class="form-control">
	                                    </div>
                                    </div>
	                        	</div>

	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Item Quantity</label>
	                        			<div class="col-sm-12">
	                                        <input type="text" name="item_quantity" id="item_quantity" class="form-control">
	                                    </div>
                                    </div>
	                        	</div>

	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Warehouse Type</label>
	                        			<div class="col-sm-12">
	                                        <select class="select_2" name="warehouse_type" id="warehouse_type">
	                                            <option value="">-- Select --</option>
	                                            @foreach($product as $item)
	                                            	<option value="{{$item->id}}">{{$item->product_id}}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>
                                    </div>
	                        	</div>

	                        </div>

	                        <div class="form-group">
	                            <div class="col-sm-3 col-sm-offset-9 col-xs-offset-9">
	                                <button type="submit" class="btn btn-primary form-control">Add</button>
	                            </div>
	                        </div>                        
	                    </form> --}}
	                    <div class="">
	                    	<table class="table table-striped table-bordered">
	                    		<thead>
	                    			<tr>
	                    				<th>Stock Id</th>
	                    				<th>Product Id</th>
	                    				<th>Booking Order Id</th>
	                    				<th>Item Code</th>
	                    				<th>Erp Code</th>
	                    				<th>Item Size</th>
	                    				<th>Item Color</th>
	                    				<th>Item Quantity</th>
	                    				<th>Location</th>
	                    				<th>Warehouse In Type</th>
	                    				<th>Action</th>
                    				</tr>
	                    		</thead>
	                    		<tbody>
	                    			<?php $i=1; ?>
	                    			@foreach($product as $item)
	                    			<form action="{{ route('store_product_entry_action') }}" method="POST">
	                    				<input type="hidden" name="product_id" value="{{ $item->store_id }}">
	                    				{{ csrf_field() }}
		                    			<tr>
		                    				<td>PSE-{{ $item->store_id }}</td>
		                    				<td> {{ $item->product_id }}</td>
		                    				<td><input type="hidden" name="booking_order_id" value="{{ $item->booking_order_id }}">{{ $item->booking_order_id }}</td>
		                    				<td><input type="hidden" name="item_code" value="{{ $item->item_code }}">{{ $item->item_code }}</td>
		                    				<td><input type="hidden" name="erp_code" value="{{ $item->erp_code }}">{{ $item->erp_code }}</td>
		                    				<td><input type="hidden" name="item_size" value="{{ $item->item_size }}">{{ $item->item_size }}</td>
		                    				<td><input type="hidden" name="item_color" value="{{ $item->item_color }}">{{ $item->item_color }}</td>
		                    				<td><input type="hidden" name="item_quantity" value="{{ $item->item_quantity }}">{{ $item->item_quantity }}</td>
		                    				<td>
		                    					<select class="form-control" name="location">
		                    						<option value="n/a">-- Select --</option>
		                    						@foreach($location as $loc)
		                    					    	<option value="{{$loc->id_location}}">{{ $loc->location}}</option>
		                    					    @endforeach
		                    					</select>
		                    				</td>
		                    				<td>
		                    					<select class="form-control" name="warehouse">
		                    						<option value="n/a">-- Select --</option>
		                    					    @foreach($warehouse as $ware)
		                    					    	<option value="{{$ware->id_warehouse_type}}">{{ $ware->warehouse_type }}</option>
		                    					    @endforeach
		                    					</select>
		                    				</td>
		                    				<td>
		                    					<button class="btn btn-success">
		                    						<i class="fa fa-cloud-upload" aria-hidden="true"></i>
		                    					</button>
		                    				</td>
	                    				</tr>
	                    			</form>
	                    			<?php $i++ ;?>
	                    			@endforeach
	                    		</tbody>
	                    	</table>
	                    	{{ $product->links() }}
	                    </div>
	                </div>
	            </div>
	        </div>	
	    </div>
	</div>

	<script type="text/javascript">
	    $(".select_2").select2();	    
	</script>

	<script type="text/javascript">

	    $('#product_id').on('change',function(){
	    	var selected_value = $(this).val();

	    	$.ajax({
	          type: "GET",
	          url: baseURL+"/get/product",
	          data: "product_id="+selected_value,
	          datatype: 'json',
	          cache: true,
	          async: true,
	          success: function(result) {
	              $('#item_code').html(result.item_code);
	              // $('#size_range').html(result.sizes);
	              console.log(result);
	    	  },error:function(result){
    	          alert("Something is wrong.");
    	      }

    	      });
	    });	    
	</script>
@endsection
