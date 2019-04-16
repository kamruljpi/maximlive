@extends('maxim.layouts.layouts')
@section('title','MRF Maxim')
@section('print-body')
<?php 
	// print_r("<pre>");
	// print_r($mrfDeatils[0]);
	// print_r("</pre>");
?>
<center><a href="#" onclick="myFunction()"  class="print">Print & Preview</a></center>

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
			@if($value->logo_allignment === "right")
				@if(!empty($value->logo))
					<div class="pull-right">
						<img src="/upload/{{$value->logo}}" height="40px" width="150px" style="margin-top:  15px;" />
					</div>
				@endif
			@endif
		</div>
	</div>
@endforeach
<!-- <div class="report-header">
	<div class="col-md-12 header-bottom-b">
		<h3 align="center" style=" padding:8px; font-weight: bold;">MRF Report</h3>
	</div>
</div> -->
	<div class="row">
		<div class="report-header">
			<h3 align="center" style=" padding:8px; font-weight: bold;">MRF</h3>
		</div>
	</div>

	<div class="row body-top">
		<div class="col-md-8 col-sm-8 col-xs-7 body-list">
			<ul>
				<li>Order Date : {{Carbon\Carbon::parse($mrfDeatils[0]->created_at)->format('d-m-Y')}}</li>
				<li>Buyer : {{$buyerDetails->buyer_name}}</li>
				<li>Vendor Name  : {{$buyerDetails->Company_name}}</li>
				{{--<li>Address : {{$buyerDetails->address_part1_invoice}}</li>
				 <li> {{$buyerDetails->address_part2_invoice}}</li>
				<li>{{($formatTypes == 1001 )?'Contact ' :'Attn' }} : {{$buyerDetails->attention_invoice}}</li>
				<li>{{($formatTypes == 1001 )?'Contact No ' :'Cell No' }} : {{$buyerDetails->mobile_invoice}}</li> --}}
			</ul>
		</div>

		<div class="col-md-4 col-sm-4 col-xs-5 valueGenarate">
			@php ($i=0)
			@foreach ($mrfDeatils as $billdata)
				@for($i;$i <= 0;$i++)
					<table class="tables table-bordered" style="width: 100%;">
						@if($buyerDetails->booking_category)
							<tr>
								<td colspan="2">
									<div style="text-align: right;">
										<p style="padding-left :5px;">Category : {{ucfirst(str_replace('_',' ',$buyerDetails->booking_category))}}</p>
									</div>
								</td>
							</tr>
						@endif

						<tr>
							<td colspan="2">
								<div style="text-align: right;">
									<p style="padding-left :5px;"> MRF No : {{$billdata->mrf_id}}</p>
								</div>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<div style="text-align: right;">
									<p style="padding-left :5px;"> Requested Shipment Date : {{$billdata->shipmentDate}}</p>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="text-align: right;">
									<p style="padding-left :5px;">Booking No : {{$billdata->booking_order_id}}  </p>

									<!-- {{Carbon\Carbon::parse($billdata->created_at)->format('dmY')}}: -->
								</div>
							</td>
						</tr>
						</div>
					</table>
				@endfor
			@endforeach

		</div>
	</div>

<div class="row">
	<div class="col-md-12">
		<h4>Dear Sir</h4>
		<p>We take the Plasure in issuing PROFORMA INVOICE for the following article (s) on the terms and conditions set forth here under</p>
	</div>
</div>


<table class="table table-bordered">
    <thead>
        <tr>
        	<th>Job No.</th>
        	<th width="30%">ERP Code</th>
        	<th width="">Item / Code No.</th>
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
	    @foreach($mrfDeatils as $key => $details)
	    	<?php 
	    		$TotalBookingQty += $details->mrf_quantity; 
	    		$jobId = (8 - strlen($details->job_id));
	    	?>
	    	<tr>
		    	<td>{{ str_repeat('0',$jobId) }}{{ $details->job_id }}</td>
		    	<td width="20%">{{$details->erp_code}}</td>
            	<td width="10%">{{$details->item_code}}</td>
            	<td>{{ ($details->item_size_width_height != '')? '('. $details->item_size_width_height .')mm' : 'N/A' }}</td>
		    	<td width="5%">{{$details->season_code}}</td>
		    	<td width="5%">{{$details->oos_number}}</td>
		    	<td width="5%">{{$details->style}}</td>
		    	<td>{{$details->poCatNo}}</td>
		    	<td>{{$details->item_description}}</td>
		    	<td width="17%">{{$details->gmts_color}}</td>
		    	<td width="17%">{{$details->item_size}}</td>
		        <td>{{$details->sku}}</td>
		        <td>{{$details->mrf_quantity}}</td>
		        <td>PCS</td>
	        </tr>
	    @endforeach
    </tbody>
</table>
<table class="table table-bordered">
	<tr>
		<td>
			<span class="pull-right" style="font-weight: bold; font-size:18px;">MRF Total Qty: {{$TotalBookingQty}}
			</span>
		</td>
		<td style="width: 4%;"></td>
	</tr>	
</table>

<div class="fixed_footer">
	<h5><strong>REMARK</strong></h5>
	<p>If the quantity of goods you recevied is not in confirmity as in packing irst or the qualify, packing problem incurred, please
	inform us in 3days. After this period, you concern about this goods shall not be our responsibility.</p>
	<h5>Please confirm receipt with your signature: </h5><br><br>




	@foreach ($footerData as $value)
	@if(!empty($value->siginingPerson_1))
	<div class="row">
		<div class="col-md-12 col-xs-12" style="padding-bottom: 20px;">


			<div class="col-md-8 col-xs-8" style="padding: 5px; padding-left: 50px;">
				@if(!empty($value->siginingPersonSeal_1))
					<img src="/upload/{{$value->siginingPersonSeal_1}}" height="100px" width="150px" />
				@endif
			</div>

			<div class="col-md-4 col-xs-4"  style="">
				<div align="center">
					@if(!empty($value->siginingSignature_1))
					<img src="/upload/{{$value->siginingSignature_1}}" height="100px" width="150px" />
					@endif
				</div>
				<div align="center" style="margin:auto;
			    	border: 2px solid black;
			    	padding: 5px;margin-top:30px;">
					{{$value->siginingPerson_1}}
				</div>
			</div>

		</div>
	</div>
	@endif
	@endforeach

	@foreach ($footerData as $value)
	@if(!empty($value->siginingPerson_2))
	<div class="row">
		<div class="col-md-12 col-xs-12" style="padding-bottom: 20px;">


			<div class="col-md-8 col-xs-8" style="padding: 5px; padding-left: 50px;">
				@if(!empty($value->siginingPersonSeal_2))
					<img src="/upload/{{$value->siginingPersonSeal_2}}" height="100px" width="150px" />
				@endif
			</div>

			<div class="col-md-4 col-xs-4"  style="">
				<div align="center">
					@if(!empty($value->siginingSignature_2))
						<img src="/upload/{{$value->siginingSignature_2}}" height="100px" width="150px" />
					@endif
				</div>
				<div align="center" style="margin:auto;
			    	border: 2px solid black;
			    	padding: 5px;margin-top:30px;">
					{{$value->siginingPerson_2}}
				</div>
			</div>
		</div>
	</div>
	@endif
	@endforeach

</div>

<script type="text/javascript">
	function myFunction() {
		$(".print").addClass("hidden");
	    window.print();
	}
</script>
@endsection
