@extends('maxim.layouts.layouts')
@section('title','Production Order')
@section('print-body')

<center><a href="#" onclick="myFunction()"  class="print">Print & Preview</a></center>

@foreach($companyInfo as $value)
	<div class="row">
		<div class="col-md-2 col-sm-2 col-xs-8">
			@if($value->logo_allignment === "left")
				@if(!empty($value->logo))
					<div class="pull-left">
						<!-- <a href="{{ route ('dashboard_view') }}"> -->
							<img src="{{ asset('upload')}}/{{$value->logo}}" height="40px" width="150px" style="margin-top:  15px;" />
						<!-- </a> -->
					</div>
				@endif
			@endif
		</div>
		<div class="col-md-8 col-sm-12 col-xs-12" style="padding-left: 40px;">
			<h2 align="center">{{ $value->header_title}}</h2>
			<div align="center">
				<p>OFFICE ADDRESS :  {{$value->address1}} {{$value->address2}} {{$value->address3}}</p>
			</div>
		</div>
		<div class="col-md-2 col-sm-8 col-xs-8">
			@if($value->logo_allignment === "right")
				@if(!empty($value->logo))
					<div class="pull-right">
						<!-- <a href="{{ route ('dashboard_view') }}"> -->
							<img src="/upload/{{$value->logo}}" height="40px" width="150px" style="margin-top:  15px;" />
						<!-- </a> -->
					</div>
				@endif
			@endif
		</div>
	</div>
@endforeach

<div class="row header-bottom">
    <div class="col-md-12 header-bottom-b">
        <span>Production Order</span>
    </div>
</div>
<div class="row body-top">
	<div class="col-md-8 col-sm-8 col-xs-7 body-list">
		<ul>
			<li>Maxim Production Order: </li>
			<li>Brand : {{$buyerDetails->buyer_name}}</li>
		</ul>
	</div>
	
	<div class="col-md-4 col-sm-4 col-xs-5 valueGenarate">
		<table class="tables table-bordered" style="width: 100%;">
			<tr>
				<td colspan="2">
					<div style="text-align: right;">
						<p style="padding-left :5px;">Vendor ID: {{(!empty($buyerDetails->party_id)? $buyerDetails->party_id : ' ')}}</p>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<div class="row body-top">
    <table class="table table-bordered">
        <tr>
            <thead>
	            <th>Job No.</th>
	        	<th width="15%">ERP Code</th>
	        	<th width="20%">Item / Code No.</th>
	        	<!-- <th width="5%">Season Code</th> -->
	        	<!-- <th>OOS No.</th> -->
	        	<!-- <th>Style</th> -->
	        	<!-- <th>PO/Cat No.</th> -->
	        	<th>Description</th>
	        	<th>GMTS Color</th>
	        	<th width="15%">Size</th>
	        	<!-- <th>Sku</th> -->
	        	<th>Order Qty</th>
	        	<th>Unit</th>
	        	<th>Remarks</th>
            </thead>
        </tr>

        <?php $TotalBookingQty = 0; ?>
        
        <tbody>
        	@foreach($ipoDetails as $details)
	        	<?php 
	        		$TotalBookingQty += $details->item_quantity; 
	        		$jobId = (8 - strlen($details->job_id));
	        	?>
	        	<tr>
	        		<td>{{ str_repeat('0',$jobId) }}{{ $details->id }}</td>
			    	<td width="20%">{{$details->erp_code}}</td>
                	<td width="10%">{{$details->item_code}}</td>
			    	<!-- <td width="5%">{{$details->season_code}}</td> -->
			    	<!-- <td width="5%">{{$details->oos_number}}</td> -->
			    	<!-- <td width="5%">{{$details->style}}</td> -->
			    	<!-- <td>{{$details->poCatNo}}</td> -->
			    	<td>{{$details->item_description}}</td>
			    	<td width="17%">{{$details->gmts_color}}</td>
			    	<td width="17%">{{$details->item_size}}</td>
			        <!-- <td>{{$details->sku}}</td> -->
			        <td>{{$details->item_quantity}}</td>
			        <td>PSC</td>
			        <td></td>
	        	</tr>
        	@endforeach
        	<tr style="height: 30px;">
        		<td colspan="6"><span style="font-weight: bold;" class="pull-right">Total Quantity</span></td>
        		<td> {{$TotalBookingQty}}</td>
        		<td></td>
        		<td></td>
        	</tr>
        </tbody>
    </table>
</div>
<style type="text/css">
	.body-top .body-list label{
		font-size: 16px;
	}
	.body-top .body-list ul li {
		margin-left: -13px;
	}
</style>
<div class="row body-top">
	<div class="col-md-9 col-xs-9 body-list">
		<label >Special Requirements/Notes: 特殊要求／备注：</label>
		<ul>
			<li>1. This order is: Normal order:  Urgent  Order: Top Urgent  Order: Export goods.</li>
			<li>2. Provide PPS PCS , Or provide production samples for sales 提供产前样_______PCS，或产后业务留样_______PCS</li>
			<li>3. Special requirements for shipment:</li>
			<li style="margin-left: 6px;">出货时有特别要求：</li>
			<li>4.</li>
			<li>5.</li>
			<li>6.</li>
			<li>7.</li>
			<li>8.</li>
			<li>9.</li>
		</ul>
	</div>

	<div class="col-md-3 col-sm-3 -col-xs-3" style="border:1px solid #DCDCDC;height: 250px;">
		<label>Special requirements for production: <br\>生产上的特别要求：</label>
	</div>
</div>

<div class="row body-top">
	<div class="col-md-3 col-sm-3 col-xs-3">
		<span style="font-weight: bold;">CS:
			<div style="border-bottom: 2px solid black; "></div>
		</span>
	</div>
	<div class="col-md-3 col-sm-3 col-xs-3">
		<span style="font-weight: bold;">CS Team Leader:
			<div style="border-bottom: 2px solid black; "></div>
		</span>
	</div>
	<div class="col-md-3 col-sm-3 col-xs-3">
		<span style="font-weight: bold;">CS Manager:
			<div style="border-bottom: 2px solid black; "></div>
		</span>
	</div>
	<div class="col-md-3 col-sm-3 col-xs-3">
		<span style="font-weight: bold;">Planing Team：
			<div style="border-bottom: 2px solid black;"></div>
		</span>
	</div>	
</div>

<div class="row body-top" style="margin-top: 15px;margin-bottom: 20px;">
	<div class="col-md-3 col-sm-3 col-xs-3"></div>
	<div class="col-md-3 col-sm-3 col-xs-3"></div>
	<div class="col-md-3 col-sm-3 col-xs-3"></div>
	<div class="col-md-3 col-sm-3 col-xs-3">
		<span style="font-weight: bold;">Order receiving Date：
			<div style="border-bottom: 2px solid black;"></div>
		</span>
	</div>
</div>
<script type="text/javascript">
    function myFunction() {
    	// $('.print').hide();
        window.print();
    }
</script>
@endsection
