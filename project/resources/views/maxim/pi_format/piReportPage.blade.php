@extends('maxim.layouts.layouts')
@section('title','PI Maxim')
@section('print-body')
<?php
// print_r("<pre>");
// print_r($buyerDetails);
// print_r("</pre>");
	$objectConvertController = new App\Http\Controllers\Source\ConverterText();
?>
<center>
	<a href="#" onclick="myFunction()" class="print">Print & Preview</a>
</center>

@foreach($companyInfo as $value)
	<div class="row">
		<div class="col-md-2 col-sm-12 col-xs-12">
			@if($value->logo_allignment === "left")
				@if(!empty($value->logo))
					<div class="pull-left">
						<img src="{{ asset('upload')}}/{{$value->logo}}" height="40px" width="150px" style="margin-top:  15px;" />
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
<div class="row header-bottom">
	<div class="col-md-12 header-bottom-b">
		<span>PROFORMA INVOICE</span>
	</div>
</div>

<div class="row body-top">
	<div class="col-md-8 col-sm-8 col-xs-7 body-list">

		<ul>
			<li>Buyer : {{$buyerDetails->buyer_name}}</li>
			<li>Company Name  : {{$buyerDetails->Company_name}}</li>
			<li>Address : {{$buyerDetails->address_part1_invoice}}</li>
			<li> {{$buyerDetails->address_part2_invoice}}</li>
			<li>{{($formatTypes == 1001 )?'Contact ' :'Attn' }} : {{$buyerDetails->attention_invoice}}</li>
			<li>{{($formatTypes == 1001 )?'Contact No ' :'Cell No' }} : {{$buyerDetails->mobile_invoice}}</li>
		</ul>
	</div>
	
	<div class="col-md-4 col-sm-4 col-xs-5 valueGenarate">
		@php ($i=0)
		@foreach ($bookingDetails as $details)
			@for($i;$i <= 0;$i++)
				<table class="tables table-bordered" style="width: 100%;">
					<tr>
						<td colspan="2">
							<div style="text-align: right;">
								<p style="padding-left :5px;"> PI No: {{$details->p_id}} </p>
							</div>
						</td>
					</tr>
					<tr>
						<!-- <td width="50%" style="border-bottom-style:hidden;border-left-style:hidden;"> </td> -->
						<td colspan="2">
							<div style="text-align: right;">
								<p style="padding-left :5px;"> Date: {{Carbon\Carbon::parse($details->created_at)->format('Y-m-d')}}</p>
							</div>
						</td>
					</tr>
					@if($is_type == 'fsc')
					<!-- <tr>
						<td width="50%" style="border-bottom-style:hidden;border-left-style:hidden;"> </td>
						<td width="50%">
							<div style="text-align: right;">
								<p style="padding-left :5px;">FSC-MIX</p>
							</div>
						</td>
					</tr> -->
					<tr>
						<td colspan="2">
							<div style="text-align: right;">
								<p>Certificate Code: CU-COC-828568</p>
							</div>
						</td>
					</tr>
					<tr>
						<!-- <td width="0%" style="border-bottom-style:hidden;border-left-style:hidden;"> </td> -->
						<td colspan="2">
							<div style="text-align: right;">
								<p>Licence Code: FSC-C121666</p>
							</div>
						</td>
					</tr>
					
					@endif
				</table>
			@endfor
		@endforeach		
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-xs-12 col-sm-12">
		<h4>Dear Sir</h4>
		<p>We take the Plasure in issuing PROFORMA INVOICE for the following article (s) on the terms and conditions set forth here under :</p>
	</div>
</div>
<table class="table table-bordered">
	<thead>
	    <tr>
	    	<th>Job No</th>
	    	<th>PO/Cat No. </th>
	    	<th>OOS No. </th>
	    	<th width="10%">Item code</th>
	    	<th width="18%">ERP Code</th>
	    	<th>GMTS/Item Color</th>
	        <th>Item Descreption</th>
	        <th>Style</th>
	        <th width="10%">Item Size</th>
	        <th>Qty / Pcs</th>
	        <th>Unit Price / Pcs</th>
	        <th>USD Amount / USD</th>
	    </tr>
	</thead>
	<tbody>
		<?php 
			$itemcodestatus = ''; 
			$totalUsdAmount = 0;
		?>
		@foreach($bookingDetails as $detailsValue)
		<?php 
			$jobId = (8 - strlen($detailsValue->job_no));

			$totalQtyAmt = $detailsValue->item_quantity * $detailsValue->item_price;
			$totalUsdAmount += $totalQtyAmt;
			$totalAllqnty += $detailsValue->item_quantity;
		?>
		<tr>
			<td>{{ str_repeat('0',$jobId) }}{{ $detailsValue->job_no}}</td>
			<td>{{ $detailsValue->poCatNo }}</td>
			<td>{{ $detailsValue->oos_number }}</td>
			<td>{{ $detailsValue->item_code }}
				<br>
				{{ ($is_type == 'fsc')? '( FSC-MIX )':'' }}
			</td>
			<td>{{ $detailsValue->erp_code }}</td>
			<!-- @if($itemcodestatus != $detailsValue->item_code)
		    	<td width="10%" rowspan="">
		    		<div>{{$detailsValue->item_code}}</div>
		    	</td>
		    @else
		    <td></td>
	    	@endif -->

	    	<!-- @if($itemcodestatus != $detailsValue->item_code)
		    	<td width="18%" rowspan="">
		    		{{ $detailsValue->erp_code }}
		    	</td>
		    @else
		    <td></td>
	    	@endif -->
			<td>{{ $detailsValue->gmts_color }}</td>
			<td>{{ $detailsValue->item_description }}</td>
			<td style="width: 10%;">
				{{ $detailsValue->style }}
				
			</td>
			<td>{{ $detailsValue->item_size }}</td>
			<td>{{ $detailsValue->item_quantity}}</td>
			<td>{{(!empty($detailsValue->item_price)? '$'.$detailsValue->item_price: '')}}</td>
			<td>{{(!empty($detailsValue->item_quantity)? '$'. $totalQtyAmt: '')}}</td>
		</tr>
			<?php $itemcodestatus = $detailsValue->item_code; ?>
		@endforeach		
			<tr>
				<td colspan="9">
					<span style="font-weight: bold; font-size: 18px; float: right;">Total Quantity:</span>
				</td>
				<td>{{$totalAllqnty}}</td>
				<td></td>
				<td>{{(!empty($totalUsdAmount)? '$'.$totalUsdAmount: '')}}</td>
			</tr>
	
	</tbody>
</table>
	<?php
		 $fractionUSD = explode('.', $totalUsdAmount);
		 $amountInWordUsd = $objectConvertController->convertNumberToWord($fractionUSD[0]);
		 if(sizeof($fractionUSD) > 1){
		 	$fractionInWordUSD = $objectConvertController->convertNumberToWord($fractionUSD[1]);
		 }
		 

		 $fractionBD = explode('.', $BDTandUSD);
		 $amountInWordBD = $objectConvertController->convertNumberToWord($fractionBD[0]);
		 if(sizeof($fractionBD) > 1){
		 	$fractionInWordBD = $objectConvertController->convertNumberToWord($fractionBD[1]);
		 }
	?>
<div class="row">
	<div class="col-md-12 col-xs-12">
		<table  border="5px solid #DBDBDB" class="table table-bordered">
			<tr>
				<td>					
					<div style="text-align:;font-weight: bold;">
						<?php if(sizeof($fractionUSD) == 1){ ?>

						<span>1. TOTAL AMOUNT : USD : {{$amountInWordUsd}} {{(empty($amountInWordUsd))?'':'USD Only'}}</span>

						<?php }else{?>

						<span>1. TOTAL AMOUNT : USD : {{$amountInWordUsd}} And {{$fractionInWordUSD}} Cents USD Only</span>

						<?php }?>
					</div>
				</td>
			</tr>
			<!-- <tr>
				<td>
					<div style="text-align:;font-weight: bold;">
						<?php if(sizeof($fractionBD) == 1){?>
						<span>1. TOTAL AMOUNT : BDT : {{$amountInWordBD}} Only</span>
						<?php }else{?>
						<span>1. TOTAL AMOUNT : BDT : {{$amountInWordBD}} And {{$fractionInWordBD}} Cents Only</span>
						<?php }?>
					</div>
				</td>
			</tr> -->
		</table>
	</div>
</div>

<div class="row body-top">
	<div class="col-md-12 col-xs-12 body-list">
		<ul>
			<li>1.Payment Terms：BBLC/ CHAQUE/ CASH BEFORE SHIPMENT</li>
			<li>2.Shipment: BY COURIER/ CARGO</li>		
			<li>3.Packing: MAXIM STANDARD PACKING</li>									
			<li>Beneficiary Bank: EBL Bangladesh</li>								
			<li>Head Office,100 Gulshan Avenue,Gulshan-02,Dhaka-1212,Bangladesh	</li>	 		
			<li>Telephone : (880 2) 9553053-6(Direct), 7113711-2, 7113714-8 Ext: 149-162</li>							
			<li>Beneficiary:MAXIM LABEL & PACKAGING (BD) PVT., LTD</li>						 
			<li>Account Number: 1041060234447</li>								 
			<li>SWIFT:   EBLDBDDH</li>					
			<li>Origin : BANGLADESH</li>								
			<li>VAT Registration: 17011037475</li>
			<li>E-BIN NO: 000412786</li>					
			<li>01. Payment : By Irrevocable Letter of Credit (L/C) to be opened in our favor to be</li>
			<li>Advised through " Eastern bank Ltd, Bangladesh ,Head Office,100 Gulshan Avenue,Gulshan-02,Dhaka-1212,Bangladesh  and Original L/C must be received to Our Bank. SWIFT CODE : EBLDBDDH </li>								
			<li>02. Bill of Exchange will be Signed by the Applicant before Submitting to the Applicant's Bank.</li>
			<li>03. Payment to be made in US Dollar within 90/60/45/30 days or Sight from the Date of Delivery not Acceptance .</li>
			<li>04. Payment reimbursement proceeds through FDD/Cheque in Foreign Currency (US Dollar) Drawn on Bangladesh Bank.	</li>								
			<li>05. Overdue interest to be paid for delayed period at 15% p@ from the date of Maturity .</li>		
			<li>06. All charge (Swift,Payment ,Reimbursement,Handling fee,etc) will bear by applicant.</li>							
			<li>07. Maturity date will be calculated from the date of delivery.</li>			
			<li>08. No. discrepancy clause will be accepted into BBLC.</li>					
			<li>09.L/C   value  should   be  minimum    US$  1500.00 otherwise  L/C   to  be  opened   at Sight & $75 will be  added  to  the  invoice   as collection  and  bank  charge.</li>
			<li>10.The  Bill  of  exchange and  Delivery  Challan/Truck Receipt   are need  to be  Signed by Customer Signatory within 07-10 days from the date of Submission. </li>									
												
			<li>Information: Master Export L/C No & Date be clearly Mentioned in the Back to Back L/C.</li>							
												
			<li>Quality Complain: Any Claim of Quality & Quantity Should be informed within 15 days after Shipment.</li>
		</ul>
	</div>
</div>


@foreach ($footerData as $value)
	@if(!empty($value->siginingPerson_2))
		<section class="report-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-4 ">
						<div class="col-md-8" style="margin-bottom: 20px; height: 90px;">
							@if(!empty($value->siginingPersonSeal_2))
								<img src="/upload/{{$value->siginingPersonSeal_2}}" height="150px" width="160px" style="display: block;margin-left: auto;margin-right: auto;"/>
							@endif
						</div>
						<div class="col-md-8" style="border-top: 2px solid #000;">
							<h4 style="text-align: center">Accepted</h4>
						</div>
						<div class="col-md-8">
							<h4 style="text-align: center; margin-top: 0px;">Seal & Signature of Buyer</h4>
						</div>
					</div>
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<div class="authorized pull-right">
							<h4 style="margin-bottom: 0px">For Maxim</h4></br>
							<div class="col-md-12" style="margin-bottom: 10px; height: 50px;">
								@if(!empty($value->siginingSignature_2))
									<img src="/upload/{{$value->siginingSignature_2}}" height="80px" width="180px" style="display: block;margin-left: auto;margin-right: auto;"/>
								@endif
							</div>
							<div></div>
							<div class="col-md-12" style="border-top: 2px solid #000;">
								<h4>Authorized Signature</h4>
								<h4>Preapred By: <b>{{$getUserDetails[0]->first_name}}</b></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	@endif
@endforeach

<script type="text/javascript">
	function myFunction() {
	    window.print();
	}
</script>
@endsection
