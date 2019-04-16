@extends('layouts.dashboard')
@section('page_heading', $modal[0]->item_code.' Product details')
@section('section')

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
	    
	    <div class="row">
	        {{-- Product Entry Column --}}
	        <div class="col-md-12">
	        	<a href="{{ URL::previous() }}" class="btn btn-success" style="margin-bottom: 10px;"> Back</a>
	        </div>
	        <div class="col-md-12 col-sm-12">
	            @if(count($errors) > 0)
	                <div class="alert alert-danger" role="alert">
	                    @foreach($errors->all() as $error)
	                      <li><span>{{ $error }}</span></li>
	                    @endforeach
	                </div>
	            @endif

	            <div class="panel panel-default">
	            	
	                <div class="panel-body">
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
                    				</tr>
	                    		</thead>
	                    		<tbody>
	                    			@foreach($modal as $item)	

	                    			<tr>
	                    				<td>PSE-{{ $item->store_id }}</td>
	                    				<td>{{ $item->product_id }}</td>
	                    				<td>{{ $item->booking_order_id }}</td>
	                    				<td>{{ $item->item_code }}</td>
	                    				<td>{{ $item->erp_code }}</td>
	                    				<td>{{ $item->item_size }}</td>
	                    				<td>{{ $item->item_color }}</td>
	                    				<td>{{ $item->item_quantity }}</td>
	                    				<td>{{ $item->location->location }}</td>
	                    				<td>{{ $item->warehouse->warehouse_type }}</td>
                    				</tr>
	                    			@endforeach
	                    		</tbody>
	                    	</table>
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
