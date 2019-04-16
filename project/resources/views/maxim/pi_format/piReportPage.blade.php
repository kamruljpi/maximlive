@extends('maxim.layouts.layouts')
@section('title','PI Maxim')
@section('print-body')
	<?php
		use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
		$objectConvertController = new App\Http\Controllers\Source\ConverterText();
	?>

	<center>
		<a href="#" onclick="myFunction()" class="print">Print & Preview</a>
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
			<h3 align="center" style=" padding:8px; font-weight: bold;">PROFORMA INVOICE</h3>
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
		        @if($buyerDetails->buyer_name == 'Gymboree')
		            <th width="5%">Serial No</th>
				@else
		    	    <th width="5%">Job No</th>
		    	@endif
		    	<th width="" id="poCatNo">PO/Cat No. </th>
		    	<th width="8%">OOS No. </th>
		    	<th width="10%">Item code</th>
		    	<th width="18%" id="erp_code">ERP Code</th>
		    	<!-- <th>GMTS / Item Color</th> -->
		        <th>Item Descreption</th>

		        <th>Style</th>
		        @if($buyerDetails->buyer_name == 'Gymboree')
				@else
		        	<!--<th width="10%">Item Size</th>-->
				@endif

		        <th>Qty / Pcs</th>
		        <th>Unit Price / Pcs</th>
		        <th>USD Amount / USD</th>
		    </tr>
		</thead>
		<tbody>
			<?php 
				$j = 1;
				$itemcodestatus = ''; 
				$totalUsdAmount = 0;
			?>
			@foreach($bookingDetails as $detailsValue)
			<?php 
				$jobId = (JobIdFlugs::JOBID_LENGTH - strlen($detailsValue->job_no));

				$str = str_replace('$', '', $detailsValue->item_price);
                $str_item_price = trim($str, '$');

				$totalQtyAmt = $detailsValue->item_quantity * (is_numeric($str_item_price)?$str_item_price:'');
				$totalQtyAmt = number_format($totalQtyAmt, 2, '.', '');
				$totalUsdAmount += $totalQtyAmt;
				$totalAllqnty += $detailsValue->item_quantity;
				$item_price = number_format($str_item_price, 5, '.', '');
			?>
			<tr>

				@if($buyerDetails->buyer_name == 'Gymboree')
					<td>{{ $j++ }}</td>
				@else
					<td>{{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{ $detailsValue->job_no}}</td>
				@endif

				<td>{{ $detailsValue->poCatNo }}</td>
				<td>{{ $detailsValue->oos_number }}</td>
				<td>{{ $detailsValue->item_code }}
					<br>
					{{ ($is_type == 'fsc')? '( FSC-MIX )':'' }}
				</td>
				<td>{{ $detailsValue->erp_code }}</td>

				<!-- <td>{{ $detailsValue->gmts_color }}</td> -->
				<td>{{ $detailsValue->item_description }}</td>
				<td style="width: 10%;">
					{{ $detailsValue->style }}
					
				</td>

				@if($buyerDetails->buyer_name == 'Gymboree')
				@else
					<!--<td>{{ $detailsValue->item_size }}</td>-->
				@endif
				<td>{{ $detailsValue->item_quantity}}</td>
				<td>{{(!empty($item_price)?((is_numeric($item_price))?'$':'').$item_price: '')}}</td>
				<td>{{(!empty($detailsValue->item_quantity)? '$'. $totalQtyAmt: '')}}</td>
			</tr>
				<?php $itemcodestatus = $detailsValue->item_code; ?>
			@endforeach		
				<tr>
				    <?php 
				    
				        $colspan_value = 7 ;
				        
				        if($buyerDetails->buyer_name == 'Gymboree'){
        					$colspan_value = 7 ;
        				}else{
        					$colspan_value = 7 ;
        				}
				    ?>
					<td colspan="{{ $colspan_value }}">
						<span style="font-weight: bold; font-size: 18px; float: right;">Total Quantity:</span>
					</td>
					<td>{{$totalAllqnty}}</td>
					<td></td>
					<?php 
					    $totalUsdAmount = number_format($totalUsdAmount, 2, '.', '');
					?>
					<td>{{(!empty($totalUsdAmount)? '$'.$totalUsdAmount: '')}}</td>
				</tr>
		
		</tbody>
	</table>

	<?php
	   //  $totalUsdAmount = floor($totalUsdAmount);
	     
		 $fractionUSD = explode('.', $totalUsdAmount);
		 $amountInWordUsd = $objectConvertController->convertNumberToWord($fractionUSD[0]);

		 if(!empty($amountInWordUsd)) {
		 	$amountInWordUsd .= " Dollar";
		 }

		 if(sizeof($fractionUSD) > 1){
		 	$fractionInWordUSD = $objectConvertController->convertNumberToWord($fractionUSD[1]);
		 }
		 

		 // $fractionBD = explode('.', $BDTandUSD);
		 // $amountInWordBD = $objectConvertController->convertNumberToWord($fractionBD[0]);
		 // if(sizeof($fractionBD) > 1){
		 //	$fractionInWordBD = $objectConvertController->convertNumberToWord($fractionBD[1]);
		 // }
	?>

	<div class="row">
		<div class="col-md-12 col-xs-12">
			<table  border="5px solid #DBDBDB" class="table table-bordered">
				<tr>
					<td>					
						<div style="text-align:;font-weight: bold;">

							@if(sizeof($fractionUSD) == 1)
								<span>1. TOTAL AMOUNT : USD : {{$amountInWordUsd}} {{(empty($amountInWordUsd))?'':'USD Only'}}</span>
							@else
								<span>1. TOTAL AMOUNT : USD : {{$amountInWordUsd}} {{((!empty($amountInWordUsd))? 'And' : '')}} {{$fractionInWordUSD}} {{((!empty($fractionInWordUSD))? ' Cents USD Only' : '')}}</span>
							@endif
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

	<div class="row body-top fixed_footer">
		<div class="col-md-12 col-xs-12 body-list">
			<ul>
				<li>1. Shipment: BY COURIER/ CARGO</li>		
				<li>2. Packing: MAXIM STANDARD PACKING</li>
				<li>3. Beneficiary Details:</li>
				<li><b>i) Beneficiary Bank: EASTERN BANK LTD.</b></li>				
				<li><b>ii) Head Office,100 Gulshan Avenue,Gulshan-02,Dhaka-1212,Bangladesh</b></li>	 		
				<li><b> iii) Telephone : (880 2) 9553053-6(Direct), 7113711-2, 7113714-8 Ext: 149-162</b></li>							
				<li><b>iv) Beneficiary:MAXIM LABEL & PACKAGING (BD) PVT., LTD</b></li>						 
				<li><b>v) Account Number: 1041060234447</b></li>								 
				<li><b>vi) SWIFT:   EBLDBDDH</b></li>					
				<li><b>vii) HS.Code: 4821.10.00</b></li>					
				<li><b>viii) VAT/ BTN Registration: 17011037475</b></li>							
				<li><b>ix) E-BIN/ VAT NO: 000412786</b></li>
				<li><b>x) Origin : BANGLADESH</b> </li>
				<li>4. Payment Terms：BBLC/ CHAQUE/ CASH/ FDD BEFORE SHIPMENT</li>
				<li>i) Payment : By Irrevocable Letter of Credit (L/C) to be opened in our favor to be Advised through " Eastern bank Ltd, Bangladesh ,Head Office,100 Gulshan Avenue,Gulshan-02,Dhaka-1212,Bangladesh  and Original L/C must be received to Our Bank. SWIFT CODE : EBLDBDDH </li>
				<li>ii) Bill of Exchange will be Signed by the Applicant before Submitting to the Applicant's Bank.</li>
				<li>iii) Payment to be made in US Dollar within {{((isset($bookingDetails[0])) ? (!empty($bookingDetails[0]->payment_days) ? $bookingDetails[0]->payment_days: ($bookingDetails[0]->payment_days =='0' ? $bookingDetails[0]->payment_days :'90/60/45/30/0')): '90/60/45/30/0')}} days from the Date of Delivery not Acceptance or At Sight .</li>
				<li>iv) Payment reimbursement proceeds through FDD/Cheque in Foreign Currency (US Dollar) Drawn on Bangladesh Bank.	</li>								
				<li style="text-decoration: underline;">v) Overdue interest to be paid for delayed period at 15% p@ from the date of Maturity .</li>		
				<li style="text-decoration: underline;">vi) All charge (Swift,Payment ,Reimbursement,Handling fee,etc) will bear by applicant.</li>							
				<li style="text-decoration: underline;">vii) Maturity date will be calculated from the date of delivery.</li>			
				<li style="text-decoration: underline;">viii) No. discrepancy clause will be accepted into BBLC.</li>					
				<li style="text-decoration: underline;">ix) L/C   value  should   be  minimum    US$  1500.00 otherwise  L/C   to  be  opened   at Sight & $50 will be  added  to  the  invoice   as collection  and  bank  charge.</li>
				<li style="text-decoration: underline;">x) The  Bill  of  exchange and  Delivery  Challan/Truck Receipt   are need  to be  Signed by Customer Signatory within 07-10 days from the date of Submission. </li>									
													
				<li>xi) Information: Master Export L/C No & Date be clearly Mentioned in the Back to Back L/C.</li>							
													
				<li>xii) Quality Complain: Any Claim of Quality & Quantity Should be informed within 15 days after Shipment.</li>

			</ul>
		</div>
	</div>


	@foreach ($footerData as $value)
		@if(!empty($value->siginingPerson_2))
			<div class="report-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-sm-4">
							<div class="pull-left">
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
						</div>
						<div class="col-md-4 col-sm-4"></div>
						<div class="col-md-4 col-sm-4">
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
									<h4 class="hidden">Preapred By: <b>{{$getUserDetails[0]->first_name}}</b></h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	    @endif
	@endforeach

	<script type="text/javascript">
		function myFunction() {
		$(".print").addClass("hidden");
		    window.print();
		}
	</script>
@endsection
