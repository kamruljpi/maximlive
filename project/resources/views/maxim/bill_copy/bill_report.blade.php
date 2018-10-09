@extends('maxim.layouts.layouts')
@section('title','Bill Maxim')
@section('print-body')
<?php 
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
						<img src="{{ asset('upload')}}/{{$value->logo}}" height="100px" width="150px" />
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
						<img src="/upload/{{$value->logo}}" height="100px" width="150px" />
					</div>
				@endif
			@endif
		</div>
	</div>
@endforeach
<div class="row header-bottom">
	<div class="col-md-12 header-bottom-b">
		<span>Bill Copy</span>
	</div>
</div>

<div class="row body-top">
	<div class="col-md-8 col-sm-8 col-xs-7 body-list">
		@php($is=0)
		@foreach($bookingDetails as $Details)
			@for($is;$is <= 0;$is++)
				<ul>
					<li>Buyer : {{$Details->buyer_name}}</li>
					<li>Company Name  : {{$Details->Company_name}}</li>
					<li>Address : {{$Details->address_part1_invoice}}</li>
					<li> {{$Details->address_part2_invoice}}
					</li>
					<li>{{($formatTypes == 1001 )?'Contact ' :'Attn' }} : {{$Details->attention_invoice}}</li>
					<li>{{($formatTypes == 1001 )?'Contact No ' :'Cell No' }} : {{$Details->mobile_invoice}}</li>
				</ul>
			@endfor
		@endforeach
	</div>
	
	<div class="col-md-4 col-sm-4 col-xs-5 valueGenarate">
		@php ($i=0)
		@foreach ($bookingDetails as $details)
			@for($i;$i <= 0;$i++)
				<table class="tables table-bordered" style="width: 100%;">
					<tr>
						<td colspan="2">
							<div style="text-align: right;">
								<p style="padding-left :5px;"> PI No : {{$details->booking_order_id}} </p>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%" style="border-bottom-style:hidden;border-left-style:hidden;"> </td>
						<td width="50%">
							<div style="text-align: right;">
								<p style="padding-left :5px;"> Date : {{Carbon\Carbon::parse($details->created_at)->format('Y-m-d')}}</p>
							</div>
						</td>
					</tr>					
				</table>
			@endfor
		@endforeach		
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-xs-12 col-sm-12">
		<h4>Dear Sir</h4>
		<p>We take the Plasure in issuing PROFORM INVOICE for the following article (s) on the terms and conditions set forth here under :</p>
	</div>
</div>
<?php
	$ih = 0;
	$totalUsdAmount = 0;
	$totalAllqnty = 0;
	$picate = [];

	$countTotalspan = 0;
	if($formatTypes == 1001){
		$countTotalspan = 5;
	}else{
		$countTotalspan = 5;
	}
	$rr = 0;
	foreach($bookingDetails as $Details){
		$picate[$rr][] = $Details->poCatNo;
		$picate[$rr][] = $Details->item_code;
		$rr++;
	}
?>
<table class="table table-bordered">
	<thead>
	    <tr>
	    	@if(1001 == $formatTypes)
	    	@else
	    		<th width="">SL</th>
	    	@endif
	    	<th width="">PO / NO </th>
	    	<th width="19%">Item code</th>
	    	<th width="15%">Item no/ERP</th>
	    	@if(1001 == $formatTypes)
	    		<th width="">Color</th>
	    	@endif
	        <th width="">Descreption</th>
	        <th width="">Qty / Pcs</th>
	        <th width="">Unit Price / Pcs</th>
	        <th>BDT</th>
	        <th width="15%">USD Amount / USD</th>
	        <th>BDT</th>
	    </tr>
	</thead>
	<tbody>
		@php($j=1)
		@foreach ($bookingDetails as $Details)
			<?php
				$gmtsquantity = explode(',', $Details->quantity);
				$totalQntyValue = 0;
				foreach ($gmtsquantity as $qtyValue) {
					$totalQntyValue = $totalQntyValue + $qtyValue;
				}
				$totalPrice = $totalQntyValue * $Details->item_price;

				$totalAllqnty = $totalAllqnty + $totalQntyValue;
				$totalUsdAmount = $totalUsdAmount + $totalPrice;

				$BDTandUSD = $conversion_rate * $totalUsdAmount;
    			$BDTandUSD = floor($BDTandUSD * 100) / 100;
			?>

			<tr>
				@if(1001 == $formatTypes)
	    		@else
					<td>{{$j++}}</td>
				@endif
				<td>{{$Details->poCatNo}}</td>
				<td width="19%">{{$Details->item_code}}</td>
				<td width="15%">{{$Details->erp_code}}</td>
				@if(1001 == $formatTypes)
					<td>Color</td>
				@endif
				<td>{{$Details->item_description}}</td>
				<td>{{$totalQntyValue}}</td>
				<td>{{(!empty($Details->item_price)? '$'.$Details->item_price: '')}}</td>

				<td>
					@if(!empty(totalPrice))
					<img src="/assets/img/bd_taka_icon.png" width="10px">
					{{ $Details->item_price * $conversion_rate }}
					@endif
				</td>

				<td>{{(!empty($totalPrice)? '$'.$totalPrice: '')}}</td>

				<td>@if(!empty(totalPrice))
					<img src="/assets/img/bd_taka_icon.png" width="10px">
					{{ $totalPrice * $conversion_rate}}
					@endif
				</td>
			</tr>
		@endforeach		
			<tr>
				<td colspan="{{$countTotalspan}}"> <center><b>{{(1003 == $formatTypes)?'TOTAL QTY & VALUE':'Total'}}</b></center></td>
				<td>{{$totalAllqnty}}</td>
				<td colspan="2"></td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="9"><center><b>Total USD</b></center></td>
				<!-- <td> 80</td> -->
				<td>{{(!empty($totalUsdAmount)? '$'.$totalUsdAmount: '')}}</td>
			</tr>
			<tr>
				<td colspan="8"><center><b>Total BDT</b></center></td>
				<td> {{$conversion_rate}}</td>
				<td><img src="/assets/img/bd_taka_icon.png" width="10px"> {{(!empty($BDTandUSD)? $BDTandUSD: '')}}</td>
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

						<span>1. TOTAL AMOUNT : USD : {{$amountInWordUsd}} And {{$fractionInWordUSD}} Cents BDT Only</span>

						<?php }?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="text-align:;font-weight: bold;">
						<?php if(sizeof($fractionBD) == 1){?>
						<span>1. TOTAL AMOUNT : BDT : {{$amountInWordBD}} BDT Only</span>
						<?php }else{?>
						<span>1. TOTAL AMOUNT : BDT : {{$amountInWordBD}} And {{$fractionInWordBD}} Cents BDT Only</span>
						<?php }?>
					</div>
				</td>
			</tr>
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
		<div class="row">
			<div class="col-md-12 col-xs-12" style="padding-bottom: 20px;">
				<div class="col-md-8 col-xs-8" style="padding: 5px; padding-left: 50px;">
					@if(!empty($value->siginingPersonSeal_2))
						<img src="/upload/{{$value->siginingPersonSeal_2}}" height="100px" width="150px" />
					@endif

					@if(!empty($value->siginingPerson_1))
						<div class="col-md-7 col-xs-7"  style="">
							<div align="center" style="margin:auto;
						    	border: 2px solid black;
						    	padding: 5px;margin-top:30px;">
								{{$value->siginingPerson_1}}
							</div>
						</div>
					@endif
				</div>
				
				<div class="col-md-4 col-xs-4"  style="">
					<div align="center">
						@if(!empty($value->siginingSignature_2))
							<img src="/upload/{{$value->siginingSignature_2}}" height="100px" width="150px" />
						@endif
					</div>

					@if(!empty($value->siginingPerson_2))
						<div align="center" style="margin:auto;
					    	border: 2px solid black;
					    	padding: 5px;margin-top:30px;">
							{{$value->siginingPerson_2}}
						</div>
					@endif
				</div>
			</div>
		</div>
	@endif
@endforeach

<script type="text/javascript">
	function myFunction() {
	    window.print();
	}
</script>
@endsection
