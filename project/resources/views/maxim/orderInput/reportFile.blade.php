@extends('maxim.layouts.layouts')
@section('title','Booking Maxim')
@section('print-body')
	<?php

		use App\Http\Controllers\taskController\Flugs\JobIdFlugs;

		$mn = 1;
		$getBuyerName = '';
		$Company_name = '';
		$TotalBookingQty = 0;
		foreach($bookingBuyer as $details){
			$getBuyerName = $details->buyer_name;
			$Company_name = $details->Company_name;
		}
	?>

	<center>
		<div class="topPreviews">
			<a href="#" onclick="myFunction()"  class="print" id="print">Print & Preview</a>
		</div>
	</center>

	@foreach($companyInfo as $value)
		<div class="row">
			<div class="col-md-2 col-sm-2 col-xs-2">
				@if($value->logo_allignment == "left")
					@if(!empty($value->logo))
						<div class="pull-left">
							<img src="{{ asset('upload')}}/{{$value->logo}}" width="160px" style="margin-top:  25px;" />
						</div>
					@endif
				@endif
			</div>
			<div class="col-md-10 col-sm-10 col-xs-10" >
				<h2 align="center" style="font-size: 27px;">{{ $value->header_title}}</h2>
				<div align="center">
					<p>OFFICE ADDRESS :  {{$value->address1}} {{$value->address2}} {{$value->address3}}</p>
				</div>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				@if($value->logo_allignment == "right")
					@if(!empty($value->logo))
						<div class="pull-right">
							<img src="/upload/{{$value->logo}}" height="40px" width="150px" style="margin-top:  15px;" />
						</div>
					@endif
				@endif
			</div>
		</div>
	@endforeach

	<div class="row">
		<div class="report-header">
			<h2 align="center" style=" padding:8px; font-weight: bold;">BOOKING FORM</h2>
		</div>

		<div style="padding-top: 10px;">
			@php ($k =0)
			@foreach ($bookingReport as $details)
				@for ($k;$k <= 0; $k++)
				<div class="col-xs-6 col-md-6" style="padding-left: -40px">
					<span >Booking Date: {{Carbon\Carbon::parse($details->created_at)->format('d-m-Y')}}</span><br>
					<span>Vendor Name: {{$Company_name}}</span>
				</div>
				<div class="col-xs-6 col-md-6">
					<div class="pull-right">
						<ul>
							@if($bookingBuyer[0]->booking_category)
								<li>
									<label>Category: {{ucfirst(str_replace('_',' ',$bookingBuyer[0]->booking_category))}}</label>
								</li>
							@endif
							<li>Booking No: {{$details->booking_order_id}}</li>
							<li>Requested Delivery Date: {{Carbon\Carbon::parse($details->shipmentDate)->format('d-m-Y')}}</li>
						</ul>
					@if($details->is_type == 'fsc')
						<ul style="border: 1px solid #ddd;padding: 5px; width:90%;float: right;">
							<li>
								<span style="padding-left: 5px; font-weight: bold;">FSC-MIX</span>
							</li>
							<li>
								<span style="padding-left: 5px; font-weight: bold;">License Code: FSC-C121666</span>
							</li>
						</ul>
					@endif
	                    <ul>
	                        <li>
	                            <span>Prepared By: {{ ucwords($getBookingUserDetails->first_name) }} {{ ucwords($getBookingUserDetails->last_name) }} </span>
	                        </li>
	                    </ul>
					</div>
				</div>
				@endfor
			@endforeach
		</div>
	</div>

	<div class="row body-top">
		<div class="col-md-8 col-sm-8 col-xs-7 body-list">
			<ul>
				<li style="font-weight: bold;">Buyer: {{$getBuyerName}}</li>
			</ul>
		</div>
		<div class="col-md-4 col-sm-4 col-xs-5">
		</div>
	</div>

	<table class="table table-bordered">
	    <thead>
	        <tr>
	        	<th>Job No.</th>
	        	<th width="15%" id="erp_code">ERP Code</th>
	        	<th width="20%">Item / Code No.</th>
	        	<th id="item_size" width="20%">Item Size</th>
	        	<th width="5%">Season Code</th>
	        	<th>OOS No.</th>
	        	<th>Style</th>
	        	<th>PO/Cat No.</th>
	        	<th>Description</th>
	        	<th>GMTS Color</th>
	        	<th width="15%">Size Range</th>
	        	<th>Sku</th>
	        	<th>Order Qty</th>
	        	<th>Unit</th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php 
	    		$rowspanValue = 0;
	    		$itemcodestatus = '';
	    	 ?>
		    @foreach($bookingReport as $key => $details)
		    	<?php 
		    		$TotalBookingQty += $details->item_quantity; 
		    		$jobId = (JobIdFlugs::JOBID_LENGTH - strlen($details->id));
		    	?>
		    	<tr>
			    	<td>{{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{ $details->id }}</td>
			    	<td width="20%">{{$details->erp_code}}</td>
                	<td width="10%">{{$details->item_code}}</td>
                	<td >{{ ($details->item_size_width_height != '')? '('. $details->item_size_width_height .')mm' : '' }}</td>
			    	<td width="5%">{{$details->season_code}}</td>
			    	<td width="5%">{{$details->oos_number}}</td>
			    	<td width="5%">{{$details->style}}</td>
			    	<td>{{$details->poCatNo}}</td>
			    	<td>{{$details->item_description}}</td>
			    	<td width="17%">{{$details->gmts_color}}</td>
			    	<td width="17%">{{$details->item_size}}</td>
			        <td>{{$details->sku}}</td>
			        <td>{{$details->item_quantity}}</td>
			        <td>PCS</td>
		        </tr>
		        <?php $itemcodestatus = $details->item_code; ?>
		    @endforeach
			    <tr>
		    		<td colspan="13">
		    			<span class="pull-right" style="font-weight: bold; font-size:18px;">Total Booking Qty: {{$TotalBookingQty}}
		    			</span>
		    		</td>
		    		<td style="width: 4%;"></td>
		    	</tr>
	    </tbody>
	</table>

	<script type="text/javascript">
		function myFunction() {
			$(".print").addClass("hidden");
		    window.print();
		}
	</script>
@endsection
