@extends('layouts.dashboard')
@section('page_heading','PI Reverse View')
@section('section')
	<?php
	    // print_r("<pre>");
	    // print_r($piDetails[0]);
	    // print_r(session('data'));
	    // print_r("</pre>");

		use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
	?>
	<style type="text/css">
	    .impomrf{
	        background-color: gainsboro;
	    }
	</style>

	<div class="row">
	    <div class="col-sm-2">
	        <div class="form-group "> {{--URL::previous()--}}
	            <a href="{{ Route('pi_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
	            <i class="fa fa-arrow-left"></i> Back</a>
	        </div>
	    </div>
	</div>

	@if(Session::has('message'))
        <div class="alert alert-success">
            <ul>
                {{ Session::get('message') }}
            </ul>
        </div>
    @endif
    @if(Session::has('error-m'))
        <div class="alert alert-danger">
            <ul>
                {{ Session::get('error-m') }}
            </ul>
        </div>
    @endif

	<div class="panel panel-default">
	    <div class="panel-heading">
	        <div style="font-size: 120%">PI {{$piDetails[0]->p_id}} Details</div>
	    </div>
	    <div class="panel-body aaa">
	        <div class="panel panel-default col-sm-7">
	            <br>
	            <p>Buyer Name:<b> {{ $buyerDetails->buyer_name }}</b></p>
	            <p>Company Name:<b> {{ $buyerDetails->Company_name }}</b></p>
	            <p>Buyer Address:<b> {{ $buyerDetails->address_part1_invoice }}{{ $buyerDetails->address_part2_invoice }}</b></p>
	            <p>Mobile No:<b> {{ $buyerDetails->mobile_invoice }}</b></p>
	            <p>Prepared By:<b> {{ ucwords($buyerDetails->prepared_by->first_name)}} {{ ucwords($buyerDetails->prepared_by->last_name)}}</b></p>
	        </div>
	        <div class="panel panel-default col-sm-5">
	            <br>
	            @if($buyerDetails->booking_category)
	            <p>Category: <b>{{ucfirst(str_replace('_',' ',$buyerDetails->booking_category))}}</b></p>
	            @endif
	            <p>Booking No:<b> {{ $buyerDetails->booking_order_id }}</b></p>
	            <p>PI No:<b> {{ $piDetails[0]->p_id }}</b></p>
	            <p>Booking Status:<b> {{ $buyerDetails->booking_status }}</b></p>
	            <p>Oreder Date:<b> {{ Carbon::Parse($buyerDetails->created_at)->format('d-m-Y') }}</b></p>
	            <p>Shipment Date:<b> {{ $buyerDetails->shipmentDate }}</b></p>
	        </div>

	    </div>
	</div>

	<div class="row">
	    <div class="col-sm-6"></div>
	    <div class="col-sm-6">
	        <div class="form-group custom-search-form">
	            <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="Search" id="user_search">
	        </div>
	    </div>
	</div>

	<table class="table table-bordered" id="tblSearch">
	    <thead>
		    <tr>
		        <th>Job No.</th>
		        <th>ERP Code</th>
		        <th>Item Code</th>
		        <th>Season Code</th>
		        <th>OOS No.</th>
		        <th>Style</th>
		        <th>PO/Cat No.</th>
		        <th>GMTS Color</th>
		        <th>Description</th>
		        <th>Size</th>
		        <th>Sku</th>
		        <th>Order Qty</th>
		        <th width="20%">Action</th>

		    </tr>
	    </thead>

	    <tbody>
	    	@if($piDetails)
		    	@foreach($piDetails as $pi_value)
		    		<?php
		    		    $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($pi_value->job_no));
		    		?>

		    		<tr>
		    			<td>{{ str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount) }}{{ $pi_value->job_no }}</td>
		    			<td>{{$pi_value->erp_code}}</td>
		    			<td>{{$pi_value->item_code}}</td>
		    			<td>{{$pi_value->season_code}}</td>
		    			<td>{{$pi_value->oos_number}}</td>
		    			<td>{{$pi_value->style}}</td>
		    			<td>{{$pi_value->poCatNo}}</td>
		    			<td>{{$pi_value->gmts_color}}</td>
		    			<td>{{$pi_value->item_description}}</td>
		    			<td>{{$pi_value->item_size}}</td>
		    			<td>{{$pi_value->sku}}</td>
		    			<td>{{$pi_value->item_quantity}}</td>
		    			<td width="100%">
		    				<div style="float: left;width: 45%;">
		    					<a href="{{Route('pi_reverse_edit_view',$pi_value->job_no)}}" class="btn btn-primary" >Edit</a>
		    				</div>

		    				<div style="float: left;width: 45%; padding-right: : 5px;">
		    					<a href="{{Route('pi_delete_action',$pi_value->job_no)}}" class="btn btn-primary deleteButton" >Cancel</a>
		    				</div>
		    			</td>
		    		</tr>
		    	@endforeach
	    	@endif
	    </tbody>
	</table>

@endsection
