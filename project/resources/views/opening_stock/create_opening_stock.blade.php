<?php 
	// print_r("<pre>");
	// print_r($item->product_id);
	// print_r("<pre>");die();
?>
@extends('layouts.dashboard')
@section('page_heading','Opening Stock')
@section('section')
	<style type="text/css">
		select{
			/*padding: 15px 0px;*/
		}
	</style>
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
	                    <form class="form-horizontal" action="{{ Route('store_opening_stock_action') }}" role="form" method="POST" >

	                        {{csrf_field()}}

	                        <div class="row">
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
	                        			<label class="col-sm-12">Item Code</label>
	                        			<div class="col-sm-12">
	                                        <select class="select_2" name="item_code" id="item_code">
	                                            <option value="">-- Select --</option>
	                                            @foreach($items as $item)
	                                            	<option value="{{$item->product_code}}">{{$item->product_code}}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>
                                    </div>
	                        	</div>
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
		                        		<label class="col-sm-12">Color</label>
		                        		<div class="col-sm-12">
		                        		    <select class="select_2" name="item_color" id="item_color">
	                                            <option value="">-- Select --</option>
	                                        </select>
		                        		</div>
	                        		</div>
	                        	</div>
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
		                        		<label class="col-sm-12">Size Range</label>
		                        		<div class="col-sm-12">
		                        		    <select class="select_2" name="size_range" id="size_range">
	                                            <option value="">-- Select --</option>
	                                        </select>
		                        		</div>
	                        		</div>
	                        	</div>
	                        	<div class="col-sm-3">
	                        		<div class="form-group">
		                        		<label class="col-sm-12">Quantity</label>
		                        		<div class="col-sm-12">
		                        		    <input type="text" class="form-control" name="quantity" value="{{ old('quantity') }}" placeholder="Quantity">
		                        		</div>
	                        		</div>
	                        	</div>
	                        </div>

	                     	<div class="row">
	                     		<div class="col-sm-3">
	                     			<div class="form-group">
		                        		<label class="col-sm-12 label-control">Location</label>
		                        		<div class="col-sm-12">
		                        		    <select class="select_2" name="location_id" id="">
		                        		    	
		                        		        <option value="">-- Select --</option>
		                        		        @foreach($locations as $location)
		                        		        	<option value="{{$location->id_location}}">{{$location->location}}</option>
		                        		        @endforeach
		                        		    </select>
		                        		</div>
	                        		</div>
	                     		</div>
	                     		<div class="col-sm-3">
	                     			<div class="form-group">
		                        		<label class="col-sm-12 label-control">Warehouse in type</label>
		                        		<div class="col-sm-12">
		                        		    <select class="select_2" name="id_warehouse_type" id="">
		                        		        <option value="">-- Select --</option>
		                        		        @foreach($warehouses as $warehouse)
		                        		        	<option value="{{$warehouse->id_warehouse_type}}">{{$warehouse->warehouse_type}}</option>
		                        		        @endforeach
		                        		    </select>
		                        		</div>
	                        		</div>
	                     		</div>
	                     		<div class="form-group">
	                     		    <div class="col-sm-3" style="padding-top:22px;">
	                     		        <button type="submit" class="btn btn-primary form-control">Add</button>
	                     		    </div>
	                     		</div> 
	                     	</div>

	                                               
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<script type="text/javascript">
	    $(".select_2").select2();	    
	</script>

	<script type="text/javascript">

	    $('#item_code').on('change',function(){
	    	var selected_value = $(this).val();

	    	$.ajax({
	          type: "GET",
	          url: baseURL+"/get/item/waise/color/size", // App\Http\Controllers\OpeningStockController.php
	          data: "item_code="+selected_value,
	          datatype: 'json',
	          cache: true,
	          async: true,
	          success: function(result) {
	              $('#item_color').html(result.colors);
	              $('#size_range').html(result.sizes);
	    	  },error:function(result){
    	          alert("Something is wrong.");
    	      }

    	      });
	    });	    
	</script>
@endsection
