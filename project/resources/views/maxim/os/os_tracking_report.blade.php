@extends('layouts.dashboard')
@section('page_heading', "OS Tracking Report List")
@section('section')
<?php 
    use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
    use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
    use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
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

<div id="booking_simple_search_form">
    <div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
        <form action="{{Route('os_advance_search_list')}}" method="POST">
            {{csrf_field()}}
            <input type="text" name="os_po_id" class="form-control" placeholder="SPO No." value="{{$inputArray['os_po_id']}}">
            <button class="btn btn-info " type="submit">Search</button>
        </form>
    </div>
    <button class="btn btn-primary " type="button" id="booking_advanc_search">Advance Search</button>
    <a href="{{Route('os_tracking_list')}}" class="btn btn-warning">Reset</a>
</div>

<div>
    <form action="{{Route('os_advance_search_list')}}" method="POST" class="hidden advance_form">
        {{csrf_field()}}
        <div class="col-sm-12">
            <div class="col-sm-3">
                <label class="col-sm-12 label-control">Order Date From</label>
                <input type="date" name="from_oder_date_search" class="form-control" id="from_oder_date_search" value="{{$inputArray['from_oder_date']}}">
            </div>
            <div class="col-sm-3">
                <label class="col-sm-12 label-control">Order Date To</label>
                <input type="date" name="to_oder_date_search" class="form-control" id="to_oder_date_search" value="{{$inputArray['to_oder_date']}}">
            </div>
            <div class="col-sm-3">
                <label class="col-sm-12 label-control">Shipment Date From</label>
                <input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search" value="{{$inputArray['from_shipment_date']}}">
            </div>
            <div class="col-sm-3">
                <label class="col-sm-12 label-control">Shipment Date To</label>
                <input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search" value="{{$inputArray['to_shipment_date']}}">
            </div>
        </div>

        <div class="col-sm-12">

            {{-- <div class="col-sm-3">
                <label class="col-sm-12 label-control">Buyer Name</label>
                <input type="text" name="buyer_name_search" class="form-control" placeholder="Buyer Name" id="buyer_name_search">
            </div> --}}

            <div class="col-sm-3">
                <label class="col-sm-12 label-control">Supplier Name</label>
                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" id="company_name_search" value="{{$inputArray['supplier_name']}}">
            </div>
            <!-- <div class="col-sm-3">
                <label class="col-sm-12 label-control">Attention</label>
                <input type="text" name="attention_search" class="form-control" placeholder="Attention search" id="attention_search">
            </div> -->
            <br>
            <div class="col-sm-3">
                {{-- <input class="btn btn-info click_preloder" type="submit" value="Search" name="booking_advanceSearch_btn"> --}}
                <button type="submit" class="btn btn-info form-control">Search</button>
            </div>
        </div>

    </form>
    <div class="hidden" id="booking_simple_search_btn">
        <button class="btn btn-primary" type="button" id="">Simple Search</button>

        <a href="{{Route('os_tracking_list')}}" class="btn btn-warning">Reset</a>
    </div>
</div>

<br>

<div class="booking_report_details_view" id="booking_report_details_view"></div>

<div class="row">
    <div class="col-md-12" >
        <form method="post" action="{{ URL('tracking/export') }}" enctype="multipart/form-data" >
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
           <div class="table-responsive" style="max-width: 100%; max-height: 500px;overflow: auto;">    
                <table class="table table-bordered" >
                    <thead>
                    <tr>
                        <th>Job No.</th>
                        <th>Category</th>
                        <th>Order Status</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Booking No.</th>
                        <th>Mrf No.</th>
                        <th>SPO No.</th>
                        <th>PO/CAT No.</th>
                        <th>Item Code</th>
                        <th id="item_size">Item Size</th>
                        <th>ERP Code</th>
                        <th >Size Range</th>
                        
                        <th>Description</th>
                        <th>Material</th>
                        <th width="10%">Order Date</th>
                        <th>Requested Shipment Date</th>
                        <th>Quantity</th>
                        <th>Supplier Price</th>
                        <th>Total Price</th>
                    </tr>
                    </thead>
                    <tbody id="booking_list_tbody">
                    <?php $total_qty = 0;$total_price = 0; ?>
                    @if (!empty($bookingList[0]->job_id))
                        @foreach($bookingList as $value)
                            <?php
                                $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($value->job_id));
                                $total_qty += $value->mrf_quantity;
                                $price = $value->mrf_quantity * $value->os_po->supplier_price;
                                $total_price += $price;
                            ?>
                            <tr id="booking_list_table">
                                <td>
                                    <input name="job_id[]" value="{{ str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount) }}{{ $valuelist->job_id }}" type="hidden">
                                    {{ str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount) }}{{ $value->job_id }}
                                </td>
                                <td>
                                    <input name="category[]" value=" {{ucfirst(str_replace('_',' ',$value->booking_details->booking_category))}}" hidden>{{ucfirst(str_replace('_',' ',$value->booking_details->booking_category))}}</td>
                                </td>
                                <td>
                                    @if( $value->mrf_status == MrfFlugs::OPEN_MRF )
                                        {{ 'Available' }}
                                        <input type="hidden" name="order_status[]" value="Available">
                                    @elseif( ($value->mrf_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT) && ($value->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT))
                                        {{ 'Mrf Accepted' }}    
                                        <input type="hidden" name="order_status[]" value="Mrf Accepted">                           
                                    @elseif( ($value->mrf_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT) && ($value->job_id_current_status == MrfFlugs::OPEN_MRF))
                                        {{ 'Mrf Issued' }}
                                        <input type="hidden" name="order_status[]" value="Mrf Issued"> 
                                    @elseif( ($value->mrf_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT ) && ($value->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_WAITING_FOR_GOODS))
                                        {{ 'Processed to supplier' }}    
                                        <input type="hidden" name="order_status[]" value="Processed to supplier"> 
                                    @else
                                        {{ 'N/A' }}
                                        <input type="hidden" name="order_status[]" value="N/A">    
                                    @endif
                                </td>
                                <td><input type="hidden" name="supplier_name[]" value="{{$value->os_po->name}}">{{$value->os_po->name}}</td>
                                <td><input type="hidden" name="contact_person[]" value="{{$value->os_po->person_name}}">{{$value->os_po->person_name}}</td>

                                <td><input name="booking_no[]" value="{{$value->booking_order_id}}" type="hidden">{{$value->booking_order_id}}</td>

                                <td><input name="mrf_no[]" value="{{$value->mrf_id}}" type="hidden">{{$value->mrf_id}}</td>

                                <td><input name="po_no[]" value="{{$value->os_po->po_id}}" type="hidden">{{$value->os_po->po_id}}</td>

                                <td><input name="po_cat_no[]" value="{{$value->poCatNo}}" type="hidden">{{$value->poCatNo}}</td>

                                <td><input name="item_code[]" value="{{$value->item_code}}" type="hidden">{{$value->item_code}}</td>
                                <td><input name="item_size[]" value="{{$value->booking_values->item_size_width_height}}" type="hidden">
                                {{ ($value->booking_values->item_size_width_height!= '')? '('. $value->booking_values->item_size_width_height .')mm': 'N/A' }}
                                </td>
                                <td><input name="erp_code[]" value="{{$value->erp_code}}" type="hidden">{{$value->erp_code}}</td>

                                <td><input name="size_range[]" value="{{$value->item_size}}" type="hidden">{{$value->item_size}}</td>

                                
                                <td><input name="item_description[]" value="{{$value->item_description}}" type="hidden">{{$value->item_description}}</td>

                                <td><input name="matarial[]" value="{{$value->os_po->material}}" type="hidden">{{$value->os_po->material}}</td>

                                <td><input name="shipmentDate[]" value="{{Carbon\Carbon::parse($value->orderDate)->format('d-m-Y')}}" type="hidden">{{Carbon\Carbon::parse($value->orderDate)->format('d-m-Y')}}</td>

                                <td><input name="shipmentDate[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" type="hidden">{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}</td>

                                <td><input name="mrf_quantity[]" value="{{$value->mrf_quantity}}" type="hidden">{{$value->mrf_quantity}}</td>

                                <td><input name="supplier_price[]" value="{{$value->os_po->supplier_price}}" type="hidden">{{($value->os_po->supplier_price != '')?'$'.$value->os_po->supplier_price : ''}}</td>
                                <td><input name="total_price[]" value="{{$price}}" type="hidden">{{($price != 0)?'$'.$price : ''}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="21"><center>Empty Value</center></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="17"><strong style="font-size: 16px; float: right;"> All Total</strong></td>
                        {{-- <td colspan="2"></strong></td> --}}
                        <td><strong><input name="total_qty" value="" type="hidden"><strong style="">Qty:{{$total_qty}}</strong></td>

                        <td colspan="2"><strong><input name="total_price" value="" type="hidden">Price: {{($total_price != 0)?'$'.$total_price : ''}}</strong></td>
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
@section('LoadScript')

    @if (!empty($inputArray['from_oder_date']) || !empty($inputArray['to_oder_date']) || !empty($inputArray['from_shipment_date']) || !empty($inputArray['to_shipment_date']) || !empty($inputArray['supplier_name']))

        <script type="text/javascript">
            $('.advance_form').removeClass('hidden');
            $('#booking_simple_search_btn').removeClass('hidden');
            $('#booking_advanc_search').hide();
            $('#booking_simple_search_form').hide();    
        </script>
    
    @endif
@endsection
