@extends('layouts.dashboard')
@section('page_heading', trans("others.mxp_menu_booking_list") )
@section('section')
<?php
    use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
    $object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
    $roleCheck = $object->getRole();
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
        button{
            background-color: white;
            border: transparent !important;
            border-color: transparent;
        }
        button:focus,button:hover, button:active{
            background-color: white;
            border: transparent !important;
            border-color: transparent;
        }
        .popoverOption:hover{
            text-decoration: underline;
        }
        /*.popper-content ul{
            list-style-type: none;
        }*/
    </style>
    @if(Session::has('empty_booking_data'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_booking_data') ))
    @endif

    {{ $msg }}
    @if (!empty($msg))
        <div class="alert alert-success">
            <ul>
                {{ $msg }}
            </ul>
        </div>
    @endif

    @if(Session::has('message'))
        <div class="alert alert-danger">
            <ul>
                {{ Session::get('message') }}
            </ul>
        </div>
    @endif

    <button class="btn btn-warning" type="button" id="booking_reset_btn">Reset</button>
    <div id="booking_simple_search_form">
        <div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
            <input type="text" name="bookIdSearchFld" class="form-control" placeholder="Booking No." id="booking_id_search">
            <button class="btn btn-info click_preloder" type="button" id="booking_list_simple_search">
                Search
            </button>
        </div>

        <button class="btn btn-primary " type="button" id="booking_list_advance_search">Advance Search</button>
    </div>
    <div>
        <form id="booking_list_advance_search_form"  style="display: none" method="post">
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
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Attention</label>
                    <input type="text" name="attention_search" class="form-control" placeholder="Attention" id="attention_search">
                </div>
                <br>
                <div class="col-sm-3">
                    <input class="btn btn-info click_preloder" type="submit" value="Search" name="booking_advanceSearch_btn" id="booking_advanceSearch_btn">
                </div>
            </div>
            <button class="btn btn-primary" type="button" id="booking_simple_search_btn">Simple Search</button>
        </form>
    </div>
    <br>

    <div class="row">
        <div class="col-md-xs col-md-offset-0">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Serial no</th>
                    <th>Buyer Name</th>
                    <th>Company Name</th>
                    <th>Attention</th>
                    <th>booking No.</th>
                    <th>PO No.</th>
                    <th>PO/CAT No.</th>
                    <th width="10%">Order Date</th>
                    <th width="10%">Requested Date</th>
                    <th>Status</th>
                    <th width="">Action</th>
                </tr>
                </thead>

                @php($j=1 + $bookingList->perPage() * ($bookingList->currentPage() - 1))
                <tbody id="booking_list_tbody">
                @foreach($bookingList as $value)
                    <tr id="booking_list_table">
                        <td>{{$j++}}</td>
                        <td>{{$value->buyer_name}}</td>
                        <td>{{$value->Company_name}}</td>
                        <td>{{$value->attention_invoice}}</td>
                        <td>{{$value->booking_order_id}}</td>
                        <td>{{$value->po->ipo_id }}</td>
                        <td>{{$value->bookingDetails->po_cat }}</td>
                        <td>{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}</td>
                        <td>{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}</td>
                        <td>
                            <button id="popoverOption" class=" popoverOption"   rel="popover" data-placement="top" data-original-title="" style="color:black;">{{$value->booking_status}}</button>

                            <div class="popper-content hide">
                                <label>Booking Prepared by: {{$value->booking->first_name}} {{$value->booking->last_name}} ({{Carbon\Carbon::parse($value->created_at)->format('d-m-Y H:i:s')}})</label><br>

                                <label>Booking Accepted by: {{$value->accepted->first_name}} {{$value->accepted->last_name}}
                                    {{(!empty($value->accepted_date_at)?'('.Carbon\Carbon::parse($value->accepted_date_at)->format('d-m-Y H:i:s').')':'')}}
                                </label><br>

                                <label>MRF Issue by: {{$value->mrf->first_name}} {{$value->mrf->last_name}}
                                    {{(!empty($value->mrf->created_at)?'('.Carbon\Carbon::parse($value->mrf->created_at)->format('d-m-Y H:i:s').')':'')}}
                                </label><br>

                                <label>PO Issue by: {{$value->ipo->first_name}} {{$value->ipo->last_name}} {{(!empty($value->ipo->created_at)?'('.Carbon\Carbon::parse($value->ipo->created_at)->format('d-m-Y H:i:s').')':'')}}</label><br>
                            </div>
                        </td>
                        <td width="12%">
                            <div class="btn-group">
                                <form action="{{ Route('booking_list_action_task') }}" target="_blank">
                                    <input type="hidden" name="bid" value="{{$value->booking_order_id}}">
                                    <button class="btn btn-success b1">Report</button>

                                    <button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>

                                    <ul class="dropdown-menu" style="left:-45px !important;">
                                        <li>
                                            <a href="{{ Route('booking_list_details_view', $value->booking_order_id) }}">Views</a>
                                        </li>
                                        @if($roleCheck != 'p')
                                            @if($value->booking_status == BookingFulgs::BOOKED_FLUG)
                                                <li>
                                                    <a href="{{ Route('booking_details_cancel_action', $value->booking_order_id) }}" class="deleteButton">Cancel</a>
                                                </li>
                                            @endif
                                        @endif
                                        <li>
                                            <a href="{{ Route('booking_files_download', $value->id) }}" class="btn btn-info">Download Files</a>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div id="booking_list_pagination">{{$bookingList->links()}}</div>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
@endsection
@section('LoadScript')
    <script type="text/javascript">
        $('.popoverOption').popover({
            trigger: "toggle",
            container: 'body',
            html: true,
            content: function () {
                return $(this).next('.popper-content').html();
            }
        });
    </script>
<script type="text/javascript" src="{{asset('assets/scripts/booking/booking_list/simple_search.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/scripts/booking/booking_list/advance_search.js')}}"></script>
@stop
