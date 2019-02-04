@extends('layouts.dashboard')
@section('page_heading', trans("others.mxp_menu_challan_boxing_list") )
@section('section')
	<?php
		use App\Http\Controllers\taskController\Flugs\JobIdFlugs;

		 // print_r("<pre>");
		 // print_r($bookingDetails);
		 // print_r("</pre>");
	?>
    <div class="container-fluid">
    	@if(Session::has('erro_challan'))
            @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('erro_challan') ))
		@endif
		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="panel panel-default">
					<div class="panel-heading">{{trans('others.mxp_menu_challan_boxing_list')}}</div>
					<div class="panel-body">
						@if(!empty($bookingDetails))							
							<form class="form-horizontal" role="form" method="POST" action="{{ Route('multiple_challan_action_task') }}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<table class="table table-bordered table-striped" >
									<thead>
										<tr>
											<th>Job Id</th>
											<th>ERP Code</th>
											<th>Item Code</th>
											<th>Item Size</th>
											<th>Item Color</th>
											<th>Challan Quantity</th>
											<th>Booking Quantity</th>
											<th>Delivery Quantity</th>
											<th>Balance Quantity</th>
										</tr>
									</thead>

									<tbody>
										@if(!empty($bookingDetails))
										@foreach ($bookingDetails as $item)
											<?php
	                                            $jobId = (JobIdFlugs::JOBID_LENGTH - strlen($item->job_id));
							    			?>
							    			<tr>
							    				<input type="hidden" name="job_id[]" value="{{$item->job_id}}">
							    				<input type="hidden" name="booking_id[]" value="{{$item->booking_order_id}}">
							    				<td>{{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{$item->job_id }}</td>
							    				<td>{{$item->erp_code }}</td>
							    				<td>{{$item->item_code }}</td>
							    				<td>{{$item->item_size }}</td>
							    				<td>{{$item->item_color }}</td>
							    				<td>
							    					<input type="text" name="challan_quantity[]" class="form-control" value="{{$item->available_challan_quantity}}">
							    					
							    				</td>
							    				<td>{{$item->booking_quantity }}</td>
							    				<td>{{$item->delivery_challan_quantity }}</td>
							    				<td>{{$item->available_challan_quantity }}</td>
							    			</tr>
										@endforeach

										@else
											<tr>
												<td colspan="11"><center>Empty</center></td>
											</tr>
										@endif
									</tbody>
								</table>

								<div class="form-group ">
									<div class="col-md-6 col-md-offset-10">
										<button type="submit" class="btn btn-primary" id="rbutton">
											{{trans('others.genarate_bill_button')}}
										</button>
									</div>
								</div>
							</form>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection