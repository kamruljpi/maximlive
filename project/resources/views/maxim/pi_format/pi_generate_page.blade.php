@extends('layouts.dashboard')
@section('page_heading', ($is_type == 'fsc')?'FSC Proforma Invoice':' Proforma Invoice')
@section('page_heading_right',  Carbon\Carbon::now()->format('d-m-Y'))
@section('section')
	<?php
		// print_r("<pre>");
		// print_r($bookingDetails);
		// print_r("</pre>");
		$TotalBookingQty =0;
	?>
	<style type="text/css">
		div.abcd{
			color:red;
			font-size: 18px;
			padding-top: -5px;
			padding-bottom: 5px;
		}
	</style>

	<div class="container-fluid">
		@if(Session::has('erro_challan'))
	        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('erro_challan') ))
		@endif

		<div class="row">
			<form action="{{ Route('pi_generate_action') }}" method="POST">
				{{csrf_field()}}

				<div class="col-sm-4">
					<div class="form-group" class="payment_days">
						<label class="col-sm-12">Payment days</label>

						<div class="col-sm-12">
							<select class="form-control" name="payment_days" id="payment_days">
								<option value=" ">-- Select --</option>
								<option value="0">0</option>
								<option value="30">30</option>
								<option value="45">45</option>
								<option value="60">60</option>
								<option value="90">90</option>
								<option value="90/60/45/30">90/60/45/30</option>
								<option value="90/60/45/30/0">90/60/45/30/0</option>
							</select>

							<div class="abcd"></div>
						</div>
					</div>
				</div>

				<div class="col-sm-4"></div>
				<div class="col-sm-4"></div>
				<table class="table table-bordered vi_table">
					<thead>
						<th>#</th>
						<th>Job No</th>
						<th>PO/Cat No</th>
						<th>Item OOS</th>
						<th>Item Code</th>
						<th width="20%">ERP Code</th>
						<th>Item Description</th>
						<th>GMTS / item Color</th>
						<th>Item Size</th>
						<th>Style</th>
						<th>SKU</th>
						<th>Item Qty</th>
					</thead>
					<tbody>
						<input type="hidden" name="is_type" value="{{$is_type}}">
						@if(!empty($bookingDetails[0]->id))
						<?php $itemcodestatus = ''; ?>
						@foreach($bookingDetails as $detailsValue)
							<?php
								$gmtsColor = explode(',', $detailsValue->gmts_color);
								$itemSize = explode(',', $detailsValue->item_size);
								$quantity = explode(',', $detailsValue->item_quantity);
							?>
							<?php $rowspanValue = 0; ?>

							@foreach($quantity as $key => $qtyValue)
								
								<?php 
									$TotalBookingQty += $qtyValue; 
									$rowspanValue += $rowspanValue +1; 
									$idstrcount = (8 - strlen($detailsValue->id));

								?>
								<tr>
									<td width="3.5%">
										<input type="checkbox" name="job_id[]" value="{{$detailsValue->id}}" class="form-control" checked>
									</td>
									<td>{{ str_repeat('0',$idstrcount) }}{{ $detailsValue->id }}</td>
									<td>{{$detailsValue->poCatNo}}</td>
									<td>{{$detailsValue->oos_number}}</td>
									<td>{{$detailsValue->item_code}}</td>
									<td>{{$detailsValue->erp_code}}</td>

									{{--<!-- @if($itemcodestatus != $detailsValue->item_code)
								    	<td rowspan="{{count($quantity)}}">
								    		<div>{{$detailsValue->item_code}}</div>
								    	</td>
							    	@endif -->

							    	<!-- @if($itemcodestatus != $detailsValue->item_code)
								    	<td rowspan="{{count($quantity)}}">
								    		<div>{{$detailsValue->erp_code}}</div>
								    	</td>
							    	@endif -->--}}

									<td>{{$detailsValue->item_description}}</td>
									<td>{{$gmtsColor[$key]}}</td>
									<td>{{$itemSize[$key]}}</td>
									<td>{{$detailsValue->style}}</td>
									<td>{{$detailsValue->sku}}</td>
									<td>{{$qtyValue}}</td>
								</tr>
							<?php $itemcodestatus = $detailsValue->item_code; ?>
							@endforeach
						@endforeach
						@else
							<tr>
								<td colspan="12">
									<center>
										<span style="font-size: 18px;">PI has been complete.</span>
									</center>
								</td>
							</tr>
						@endif
					</tbody>
				</table>
				<div class="form-group ">
					<div class="col-md-2 pull-right" style="margin-bottom: 30px;">
						{{--id="rbutton"--}}
						<button type="submit" class="btn btn-primary form-control" id="generate_pi" >
							Generate
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script type="text/javascript">
		$('#generate_pi').on('click',function(){
			var payment_days = $('#payment_days').val();

			if(payment_days == ' ') {
				$('#payment_days').css('border-color','red');
				$('.abcd').html('Payment days field is required.');

				 $('html,body').animate({
                    scrollTop: $("#payment_days").offset().top},
                    'slow');
				return false;
			}else{
				return true;
			}
			return false;

		});
	</script>
@endsection
