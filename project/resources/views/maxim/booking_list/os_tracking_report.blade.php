@extends('layouts.dashboard')
@section('page_heading', "Planning Tracking Report List")
@section('section')
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
            <input type="text" name="bookIdSearchFld" class="form-control" placeholder="Booking No." id="booking_id_search">
            <button class="btn btn-info click_preloder" type="button" id="os_simple_search">
                Search
            </button>
        </div>
        <button class="btn btn-primary " type="button" id="os_report_advance_search">Advance Search</button>
    </div>
    <div>
        <form id="os_advance_search_form"  style="display: none" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order Date From</label>
                    <input type="date" name="from_oder_date_search" class="form-control" id="from_oder_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order Date To</label>
                    <input type="date" name="to_oder_date_search" class="form-control" id="to_oder_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment Date From</label>
                    <input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment Date To</label>
                    <input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Buyer Name</label>
                    <input type="text" name="buyer_name_search" class="form-control" placeholder="Buyer Name" id="buyer_name_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Vendor Name</label>
                    <input type="text" name="company_name_search" class="form-control" placeholder="Vendor Name" id="company_name_search">
                </div>
                <!-- <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Attention</label>
                    <input type="text" name="attention_search" class="form-control" placeholder="Attention search" id="attention_search">
                </div> -->
                <br>
                <div class="col-sm-3">
                    <input class="btn btn-info click_preloder" type="submit" value="Search" name="booking_advanceSearch_btn" id="booking_advanceSearch_btn">
                </div>
            </div>
            <button class="btn btn-primary" type="button" id="os_report_simple_search_btn">Simple Search</button>
        </form>
    </div>

    <br>

    <div class="booking_report_details_view" id="booking_report_details_view"></div>

    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <form method="post" action="{{ URL('tracking/export') }}" enctype="multipart/form-data" >
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <div class="table-responsive" style="max-width: 100%;max-height: 500px;overflow: auto;">
                    <table class="table table-bordered" style="min-width: 600px;" >
                        <thead>
                        <tr>
                            <th>Job No.</th>
                            <th>Booking Id</th>
                            <th>Mrf Id</th>
                            <th>ERP Code</th>
                            <th>Item Code</th>
                            <th>Item Size</th>
                            <th>Item Description</th>
                            <th>Material</th>
                            <th width="10%">Order Date</th>
                            <th>Requested Shipment Date</th>
                            <th>Mrf Status</th>
                            <th>Mrf Quantity</th>
                        </tr>
                        </thead>
                        <tbody id="booking_list_tbody">
                        <?php $total_qty = 0; ?>
                        @foreach($bookingList as $value)
                                <?php
                                $idstrcount = (8 - strlen($value->job_id));
                                $total_qty += $value->mrf_quantity;
                                ?>
                                <tr id="booking_list_table">
                                    <td>
                                        <input name="job_id[]" value="{{ str_repeat('0',$idstrcount) }}{{ $valuelist->job_id }}" type="hidden">
                                        {{ str_repeat('0',$idstrcount) }}{{ $value->job_id }}
                                    </td>
                                    <td>
                                        <input name="booking_order_id[]" value="{{$value->booking_order_id}}" type="hidden">
                                        {{$value->booking_order_id}}
                                    </td>
                                    <td>
                                        <input name="mrf_id[]" value="{{$value->mrf_id}}" type="hidden">
                                        {{$value->mrf_id}}
                                    </td>

                                    <td>
                                        <input name="erp_code[]" value="{{$value->erp_code}}" type="hidden">
                                        {{$value->erp_code}}
                                    </td>
                                    <td>
                                        <input name="item_code[]" value="{{$value->item_code}}" type="hidden">
                                        {{$value->item_code}}
                                    </td>
                                    <td>
                                        <input name="item_size[]" value="{{$value->item_size}}" type="hidden">
                                        {{$value->item_size}}
                                    </td>
                                    <td>
                                        <input name="item_description[]" value="{{$value->item_description}}" type="hidden">
                                        {{$value->item_description}}
                                    </td>
                                    <td>
                                        <input name="matarial[]" value="{{$value->matarial}}" type="hidden">
                                        {{$value->matarial}}
                                    </td>
                                    <td>
                                        <input name="shipmentDate[]" value="{{Carbon\Carbon::parse($value->orderDate)->format('d-m-Y')}}" type="hidden">
                                        {{Carbon\Carbon::parse($value->orderDate)->format('d-m-Y')}}
                                    </td>
                                    <td>
                                        <input name="shipmentDate[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" type="hidden">
                                        {{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}
                                    </td>
                                    <td>
                                        <input name="mrf_quantity[]" value="{{$value->mrf_status}}" type="hidden">
                                        {{$value->mrf_status}}
                                    </td>
                                    <td>
                                        <input name="mrf_quantity[]" value="{{$value->mrf_quantity}}" type="hidden">
                                        {{$value->mrf_quantity}}
                                    </td>
                                </tr>
                        @endforeach
                        <tr>
                            <td colspan="9"></td>
                            <td colspan="2"><strong style="font-size: 12px;">Total Qty:</strong></td>
                            <td><strong><input name="total_qty" value="" type="hidden">{{$total_qty}}</strong></td>
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
    <script type="text/javascript" src="{{asset('assets/scripts/tracking_report/os/simple_search.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/scripts/tracking_report/os/advance_search.js')}}"></script>
@endsection