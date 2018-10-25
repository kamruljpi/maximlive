@extends('layouts.dashboard')
@section('page_heading', "Tracking List")
@section('section')
<?php 
//	 print_r("<pre>");
//	 print_r($bookingList[0]->itemLists);
//	 print_r("</pre>");
?>
<style type="text/css">
	.b1{
		border-bottom-left-radius: 4px;
		border-top-right-radius: 0px;
	}
	.b2{
		border-bottom-left-radius: 0px;
		border-top-right-radius: 4px;
	}
	.btn-group .btn + .btn,
	 .btn-group .btn + .btn-group,
	  .btn-group .btn-group + .btn,
	   .btn-group .btn-group + .btn-group {
    margin-left: -5px;
}
</style>
	@if(Session::has('empty_booking_data'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_booking_data') ))
    @endif
    
	<button class="btn btn-warning" type="button" id="booking_reset_btn">Reset</button>
	<div id="booking_simple_search_form">
		<div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
			<input type="text" name="bookIdSearchFld" class="form-control" placeholder="Booking Id search" id="booking_id_search">
			<button class="btn btn-info" type="button" id="booking_simple_search_report">
				Search{{--<i class="fa fa-search"></i>--}}
			</button>
		</div>
		{{--<div class="col-sm-2">--}}
		{{--<input class="btn btn-primary" type="submit" value="Advanced Search" name="booking_advanc_search" id="booking_advanc_search">--}}
		{{--</div>--}}
		<button class="btn btn-primary " type="button" id="booking_advanc_search">Advance Search</button>
	</div>
	<div>
		<form id="advance_search_form"  style="display: none" method="post">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="col-sm-12">
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Order date from</label>
					<input type="date" name="from_oder_date_search" class="form-control" id="from_oder_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Order date to</label>
					<input type="date" name="to_oder_date_search" class="form-control" id="to_oder_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Shipment date from</label>
					<input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Shipment date to</label>
					<input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search">
				</div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Buyer name</label>
					<input type="text" name="buyer_name_search" class="form-control" placeholder="Buyer name search" id="buyer_name_search">
				</div>
				<div class="col-sm-3">
					<label class="col-sm-12 label-control">Vendor name</label>
					<input type="text" name="company_name_search" class="form-control" placeholder="Company name search" id="company_name_search">
				</div>
				<!-- <div class="col-sm-3">
					<label class="col-sm-12 label-control">Attention</label>
					<input type="text" name="attention_search" class="form-control" placeholder="Attention search" id="attention_search">
				</div> -->
				<br>
				<div class="col-sm-3">
					<input class="btn btn-info" type="submit" value="Search" name="booking_advanceSearch_btn" id="booking_advanceSearch_btn">
				</div>
			</div>

			{{--<div class="col-sm-2">
				<input type="text" name="searchFld" class="form-control" placeholder="Booking Id search" id="booking_id_search">
			</div>
			<div class="col-sm-2">
				<input type="text" name="searchFld" class="form-control" placeholder="Booking Id search" id="booking_id_search">
			</div>
			<div class="col-sm-2">
				<input type="text" name="searchFld" class="form-control" placeholder="Booking Id search" id="booking_id_search">
			</div>--}}
			<button class="btn btn-primary" type="button" id="booking_simple_search_btn">Simple Search</button>
		</form>
	</div>
	<br>
<div class="booking_report_details_view" id="booking_report_details_view">
	

</div>

	<div class="row">


            <div class="col-md-12 col-md-offset-0">
                <form method="post" action="{{ URL('tracking/export') }}" enctype="multipart/form-data" >
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <div class="table-responsive" style="max-width: 100%;
		max-height: 500px;
		overflow: auto;">
                    <table class="table table-bordered vi_table" style="min-width: 600px;" >
                        <thead>
                        <tr>
                            <th>Job No.</th>
                            <th>Buyer Name</th>
                            <th style="width:50% !important;">Vendor Name</th>
                            <th>Attention</th>
                            <th>Booking No.</th>
                            <th>PO/CAT No.</th>
                            <th>PI No.</th>
                            <th>Challan No.</th>
                            <th>PO No.</th>
                            <th>MRF No.</th>
                            <th>Order Date</th>
                            <th>Requested Date</th>
                            <th>Item Code</th>
                            <th width="">ERP Code</th>
                            <th>Size</th>
                            <th>Item Description</th>
                            <th>Quantity</th>
                            <th>Price/Pcs.</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <?php
                        $fullTotalAmount = 0;
                        ?>
                        @php($j=1)
                        @php($ccc=0)
                        <tbody id="booking_list_tbody">
                        @foreach($bookingList as $value)
                            <?php
                            $ilc = 1;
                            $TotalAmount = 0;
                            ?>
                            @foreach($value->itemLists as $valuelist)
                                <?php
                                $idstrcount = (8 - strlen($valuelist->id));
//                                	 print_r("<pre>");
//                                	 print_r($valuelist->poCatNo);
//                                	 print_r("</pre>");die();

                                if(count($value->itemLists) == 1){
                                ?>

                                <tr id="booking_list_table">
                                    <td><input name="job_id[]" value="{{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}" hidden> {{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}</td>
                                    <td><input name="buyer_name[]" value="{{$value->buyer_name}}" hidden>{{$value->buyer_name}}</td>
                                    <td><input name="company_name[]" value="{{$value->Company_name}}" hidden><{{$value->Company_name}}</td>
                                    <td><input name="attention_invoice[]" value="{{$value->attention_invoice}}" hidden>{{$value->attention_invoice}}</td>
                                    <td><input name="booking_order_id[]" value="{{$value->booking_order_id}}" hidden>{{$value->booking_order_id}}</td>
                                    <td><input name="po_cat_no[]" value="{{$valuelist->poCatNo}}" hidden>{{$valuelist->poCatNo}}</td>
                                    <td><input name="p_ids[]" value="{{$valuelist->pi->p_ids}}" hidden>{{$valuelist->pi->p_ids}}</td>
                                    <td><input name="challan_ids[]" value="{{$valuelist->challan->challan_ids}}" hidden>{{$valuelist->challan->challan_ids}}</td>
                                    <td><input name="ipo_ids[]" value="{{$valuelist->ipo->ipo_ids}}" hidden>{{$valuelist->ipo->ipo_ids}}</td>
                                    <td><input name="mrf_ids[]" value="{{$valuelist->mrf->mrf_ids}}" hidden>{{$valuelist->mrf->mrf_ids}}</td>
                                    <td>
                                        <input name="order_date[]" value="{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}</td>
                                    <td>
                                        <input name="requested_date[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}
                                    </td>
                                <?php
                                }else if(count($value->itemLists) > 1){
                                if($ilc == 1){
                                ?>
                                <tr id="booking_list_table">
                                    <td><input name="job_id[]" value="{{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}" hidden>{{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}</td>
                                    <td><input name="buyer_name[]" value="{{$value->buyer_name}}" hidden>{{$value->buyer_name}}</td>
                                    <td><input name="company_name[]" value="{{$value->Company_name}}" hidden>{{$value->Company_name}}</td>
                                    <td><input name="attention_invoice[]" value="{{$value->attention_invoice}}" hidden>{{$value->attention_invoice}}</td>
                                    <td><input name="booking_order_id[]" value="{{$value->booking_order_id}}" hidden>{{$value->booking_order_id}}</td>
                                    <td><input name="po_cat_no[]" value="{{$valuelist->poCatNo}}" hidden>{{$valuelist->poCatNo}}</td>
                                    <td><input name="p_ids[]" value="{{$valuelist->pi->p_ids}}" hidden>{{$valuelist->pi->p_ids}}</td>
                                    <td><input name="challan_ids[]" value="{{$valuelist->challan->challan_ids}}" hidden>{{$valuelist->challan->challan_ids}}</td>
                                    <td><input name="ipo_ids[]" value="{{$valuelist->ipo->ipo_ids}}" hidden>{{$valuelist->ipo->ipo_ids}}</td>
                                    <td><input name="mrf_ids[]" value="{{$valuelist->mrf->mrf_ids}}" hidden>{{$valuelist->mrf->mrf_ids}}</td>
                                    <td><input name="order_date[]" value="{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}</td>
                                    <td>
                                        <input name="requested_date[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}
                                    </td>
                                    <?php
                                    }else{
                                    ?>
                                </tr>
                                <tr id="booking_list_table">

                                    <?php
                                    }
                                    }
                                    ?>

                                    <?php
                                    if(count($value->itemLists) == 1){
                                        ?>

						<?php
                                    }else if(count($value->itemLists) > 1){
                                    if($ilc == 1){
                                        ?>

								<?php
                                    }else{
                                    ?>
                                    <td><input name="job_id[]" value="{{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}" hidden>{{ str_repeat('0',$idstrcount) }}{{ $valuelist->id }}</td>
                                    <!-- <td colspan="7"></td> -->
                                    <td><input name="buyer_name[]" value="{{$value->buyer_name}}" hidden>{{$value->buyer_name}}</td>
                                    <td><input name="company_name[]" value="{{$value->Company_name}}" hidden>{{$value->Company_name}}</td>
                                    <td><input name="attention_invoice[]" value="{{$value->attention_invoice}}" hidden>{{$value->attention_invoice}}</td>
                                    <td><input name="booking_order_id[]" value="{{$value->booking_order_id}}" hidden>{{$value->booking_order_id}}</td>
                                    <td><input name="po_cat_no[]" value="{{$valuelist->poCatNo}}" hidden>{{$valuelist->poCatNo}}</td>
                                    <td><input name="p_ids[]" value="{{$valuelist->pi->p_ids}}" hidden>{{$valuelist->pi->p_ids}}</td>
                                    <td><input name="challan_ids[]" value="{{$valuelist->challan->challan_ids}}" hidden>{{$valuelist->challan->challan_ids}}</td>
                                    <td><input name="ipo_ids[]" value="{{$valuelist->ipo->ipo_ids}}" hidden>{{$valuelist->ipo->ipo_ids}}</td>
                                    <td><input name="mrf_ids[]" value="{{$valuelist->mrf->mrf_ids}}" hidden>{{$valuelist->mrf->mrf_ids}}</td>
                                    <td><input name="order_date[]" value="{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}</td>
                                    <td>
                                        <input name="requested_date[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" hidden>{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}
                                    </td>
                                    <?php
                                    }
                                    }
                                    ?>
                                    <td><input name="item_code[]" value="{{$valuelist->item_code}}" hidden>{{$valuelist->item_code}}</td>
                                    <td><input name="erp_code[]" value="{{$valuelist->erp_code}}" hidden>{{$valuelist->erp_code}}</td>
                                    <td><input name="item_size[]" value="{{$valuelist->item_size}}" hidden>{{$valuelist->item_size}}</td>
                                    <td><input name="item_description[]" value="{{$valuelist->item_description}}" hidden>{{$valuelist->item_description}}</td>
                                    <td><input name="item_quantity[]" value="{{$valuelist->item_quantity}}" hidden>{{$valuelist->item_quantity}}</td>
                                    <td><input name="item_price[]" value="{{$valuelist->item_price}}" hidden>${{$valuelist->item_price}}</td>
                                    <td><input name="total_price[]" value="{{ $valuelist->item_quantity*$valuelist->item_price }}" hidden>${{ $valuelist->item_quantity*$valuelist->item_price }}</td>
                                    <?php
                                    $fullTotalAmount += $valuelist->item_quantity*$valuelist->item_price;
                                    $TotalAmount += $valuelist->item_quantity*$valuelist->item_price;
                                    ?>
                                </tr>
                                <?php
                                if(count($value->itemLists) == 1){
                                ?>
                                <!-- <tr>
									<td colspan="13"></td>
									<td><strong>Total :</strong></td>
									<td><strong>${{ round($TotalAmount,2) }}</strong></td>
									<td></td>
								</tr> -->
                                <?php
                                }else if(count($value->itemLists) > 1){
                                if($ilc == count($value->itemLists)){
                                ?>
                                <!-- <tr>
											<td colspan="13"></td>
											<td><strong>Total :</strong></td>
											<td><strong>${{ round($TotalAmount,2) }}</strong></td>
											<td></td>
										</tr> -->
                                <?php
                                }
                                }
                                ?>
                                <?php
                                $ilc = $ilc+1;
                                ?>
                            @endforeach
                            <?php $ccc++; ?>
                        @endforeach
                        <tr>
                            <td colspan="15"></td>
                            <td><strong style="font-size: 12px;">All Total :</strong></td>
                            <td><strong><input name="total" value="{{ round($fullTotalAmount,2) }}" hidden>${{ round($fullTotalAmount,2) }}</strong></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="booking_list_pagination">{{$bookingList->links()}}</div>
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination"></ul>
                    </nav>
                </div>
                    <button class="btn btn-primary pull-right">Export as Excel</button>
                    <div class="col-md-12" style="margin-bottom: 50px;"></div>
                </form>
            </div>


	</div>
@endsection
