@extends('layouts.dashboard')
@section('page_heading','Stored Item')
@section('section')
	
	<?php 
		//  print_r("<pre>");
		//  print_r($errors);
		//  print_r("</pre>");
	?>
	<div class="container-fluid">

	    @if(Session::has('store'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('store') ))
	    @endif

	    @if(Session::has('delete'))
	        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete') ))
	    @endif

	    @if(Session::has('update'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update') ))
	    @endif

	    @if(Session::has('messages'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('messages') ))
	    @endif

	    @if(count($errors) > 0)
	        <div class="alert alert-danger" role="alert">
	            @foreach($errors->all() as $error)
	              <li><span>{{ $error }}</span></li>
	            @endforeach
	        </div>
	    @endif

	    <div class="row">
	        <form action="{{Route('item_stored_filter_action')}}" method="POST">
	            {{csrf_field()}}

	            <div class="col-sm-12">
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Receive Date From</label>
	                    <input type="date" name="receive_from_date" class="form-control" value="{{(isset($filter_v['receive_from_date']) ? $filter_v['receive_from_date'] :'')}}">
	                </div>
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Receive Date To</label>
	                    <input type="date" name="receive_to_date" class="form-control" value="{{(isset($filter_v['receive_to_date']) ? $filter_v['receive_to_date'] :'')}}">
	                </div>
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Shipment Date From</label>
	                    <input type="date" name="shipment_from_date" class="form-control" value="{{(isset($filter_v['shipment_from_date']) ? $filter_v['shipment_from_date'] :'')}}">
	                </div>
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Shipment Date To</label>
	                    <input type="date" name="shipment_to_date" class="form-control" value="{{(isset($filter_v['shipment_to_date']) ? $filter_v['shipment_to_date'] :'')}}">
	                </div>
	            </div>

	            <div class="col-sm-12">
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Item Code</label>
	                    <div class="col-sm-12">
                            <select class="select_2" name="item_code" id="item_code">
                                <option value="">-- Select --</option>
                                @foreach($filter['items'] as $item)
                                	<option value="{{$item->product_code}}" {{((isset($filter_v['item_code']) && $filter_v['item_code'] == $item->product_code) ? 'selected' : '')}} >{{$item->product_code}}</option>
                                @endforeach
                            </select>
                        </div>
	                </div>
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Location</label>
	                    <div class="col-sm-12">
	                        <select class="select_2" name="location_id" id="">
	                            <option value="">-- Select --</option>
	                            @foreach($filter['location'] as $item)
	                            	<option value="{{$item->id_location}}" {{((isset($filter_v['location_id']) && $filter_v['location_id'] == $item->id_location) ? 'selected' : '')}}>{{$item->location}}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                </div>
	                <div class="col-sm-3">
	                    <label class="col-sm-12 label-control">Warehouse in type</label>
	                    <div class="col-sm-12">
	                        <select class="select_2" name="id_warehouse_type" id="">
	                            <option value="">-- Select --</option>
	                            @foreach($filter['in_type'] as $item)
	                            	<option value="{{$item->id_warehouse_type}}" {{((isset($filter_v['id_warehouse_type']) && $filter_v['id_warehouse_type'] == $item->id_warehouse_type) ? 'selected' : '')}} >{{$item->warehouse_type}}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                </div>
	                <div class="col-sm-3">
	                	<div style="float: left; width: 48%;margin-right:2.5px;">	                		
		                    <div class="form-group">
		                    	<button type="submit" class="btn btn-info form-control" style="margin-top: 20px;">Search</button>
		                    </div>
	                	</div>
	                	<div style="float: left; width: 48% ;margin-left:2.5px;">	                		
		                    <div class="form-group">
		                    	<a href="{{Route('stored_item')}}" class="btn btn-primary form-control" style="margin-top: 20px;">Reset</a>
		                	</div>
	                    </div>
	                </div>
	            </div>     
	        </form>
	    </div>
	    
	    <div class="row">
	        {{-- Product Entry Column --}}
	        <div class="col-md-12 col-sm-12">
	            <div class="panel panel-default">
	                <div class="panel-body">
	                    <div class="">
	                    	<table class="table table-striped table-bordered">
	                    		<thead>
	                    			<tr>
	                    				<th>Stock Id</th>
	                    				<th>Item Code</th>
	                    				{{-- <th>Erp Code</th> --}}
	                    				<th>Item Size</th>
	                    				<th>Item Color</th>
	                    				<th>Item Quantity</th>
	                    				<th>Location</th>
	                    				<th>Warehouse In Type</th>
	                    				<th>Action</th>
                    				</tr>
	                    		</thead>
	                    		<tbody>
	                    			@if(!empty($product[0]->store_id))
		                    			@foreach($product as $item)
			                    			<tr>
			                    				<td>PSE-{{ $item->store_id }}</td>
			                    				<td>{{ $item->item_code }}</td>
			                    				{{-- <td>{{ $item->erp_code }}</td> --}}
			                    				<td>{{ $item->item_size }}</td>
			                    				<td>{{ $item->item_color }}</td>
			                    				<td>{{ $item->item_quantity }}</td>
			                    				<td>{{ $item->location }}</td>
			                    				<td>{{ $item->warehouse }}</td>
			                    				<td>
			                    					<button class="btn btn-success">
			                    						<i class="fa fa-trash" aria-hidden="true"></i>
			                    					</button>
			                    				</td>
		                    				</tr>
		                    			@endforeach
		                    		@else
		                    			<tr>
		                    				<td colspan="9"><center>Empty</center></td>
		                    			</tr>
	                    			@endif
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

@endsection
