@extends('layouts.dashboard')
@section('page_heading', trans("others.mxp_menu_booking_list") )
@section('section')
    <?php
        use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
        use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
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

    
    @if (!empty($msg))
        <div class="alert alert-success">
            <ul>
                {{ $msg }}
            </ul>
        </div>
    @endif

    @if(Session::has('message'))
      @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('message') ))
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
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Serial no</th>
                    <th width=""> Category</th>
                    <th>Buyer Name</th>
                    <th>Company Name</th>
                    <th>Attention</th>
                    <th>Booking No.</th>
                    <th>IPO / MRF No.</th>
                    <th>PO/CAT No.</th>
                    <th width="10%">Order Date</th>
                    <th width="10%">Requested Shipment Date</th>
                    <th>Status</th>
                    {{-- <th>Order Status</th> --}}
                    <th>Action</th>
                </tr>
                </thead>

                @php($j=1 + $bookingList->perPage() * ($bookingList->currentPage() - 1))
                <tbody id="booking_list_tbody">
                    @foreach($bookingList as $value)
                        <tr id="booking_list_table">
                            <td>{{$j++}}</td>
                            <td>{{ucfirst(str_replace('_',' ',$value->booking_category))}}</td>
                            <td>{{$value->buyer_name}}</td>
                            <td>{{$value->Company_name}}</td>
                            <td>{{$value->attention_invoice}}</td>
                            <td>{{$value->booking_order_id}}</td>
                            <td>
                                @if(!empty($value->po->ipo_id) && !empty($value->mrf->mrf_id))
                                    {{$value->po->ipo_id }} , {{ $value->mrf->mrf_id }}
                                @elseif(!empty($value->po->ipo_id) && empty($value->mrf->mrf_id))
                                    {{$value->po->ipo_id }}
                                @elseif(empty($value->po->ipo_id) && !empty($value->mrf->mrf_id))
                                    {{ $value->mrf->mrf_id }}
                                @endif
                            </td>
                            <td>
                                <div class="table-responsive" style="max-width: 100%;max-height: 100px;overflow: auto;">
                                  <table>
                                    <td>{{$value->bookingDetails->po_cat }}</td>
                                  </table>
                                </div>
                            </td>

                            <?php 
                                $dt = new DateTime($value->created_at, new DateTimezone('Asia/Dhaka'));
                            ?>

                            <td>{{$dt->format('d-m-Y, g:i a')}}</td>

                            <?php 
                                $str_date = str_replace('/', '-', $value->bookingDetails->shipmentDate);
                                $shipmentDate = new DateTime($str_date, new DateTimezone('Asia/Dhaka'));
                            ?>
                            
                            <td>{{$shipmentDate->format('d-m-Y')}}</td>
                            
                            <td>
                                <button id="popoverOption" class=" popoverOption {{ $value->booking_status }}"   rel="popover" data-placement="top" data-original-title="" >{{$value->booking_status}}</button>

                                <div class="popper-content hide">
                                    <label>Booking Prepared by: {{$value->booking->first_name}} {{$value->booking->last_name}} ({{$dt->format('d-m-Y, g:i a')}})</label><br>

                                    <label>Booking Accepted by: {{$value->accepted->first_name}} {{$value->accepted->last_name}}

                                        <?php 
                                            $dtt = new DateTime($value->accepted_date_at, new DateTimezone('Asia/Dhaka'));
                                        ?>

                                        {{(!empty($value->accepted_date_at)?'('.$dtt->format('d-m-Y, g:i a').')':'')}}
                                    </label><br>

                                    <label>MRF Issue by: {{$value->mrf->first_name}} {{$value->mrf->last_name}}
                                        <?php 
                                            $dtm = new DateTime($value->mrf->created_at, new DateTimezone('Asia/Dhaka'));
                                        ?>

                                        {{(!empty($value->mrf->created_at)?'('.$dtm->format('d-m-Y, g:i a').')':'')}}
                                    </label><br>

                                    <label>PO Issue by: {{$value->ipo->first_name}} {{$value->ipo->last_name}} 
                                        <?php 
                                            $dtipo = new DateTime($value->ipo->created_at, new DateTimezone('Asia/Dhaka'));
                                        ?>

                                        {{(!empty($value->ipo->created_at)?'('.$dtipo->format('d-m-Y, g:i a').')':'')}}</label><br>
                                </div>
                            </td>
                            <td width="12%">
                                <div class="btn-group">
                                    <form action="{{ Route('booking_list_action_task') }}" target="_blank">
                                        <input type="hidden" name="bid" value="{{$value->booking_order_id}}">
                                        <button class="btn btn-success b1">Report</button>
                                    </form>
                                        <button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <ul class="dropdown-menu" style="left:-45px !important;">
                                            <li>
                                                <a href="{{ Route('booking_list_details_view', $value->booking_order_id) }}">Views</a>
                                            </li>
                                            @if($roleCheck != 'p')
                                                @if($value->booking_status == BookingFulgs::BOOKED_FLUG 
                                                    || $value->booking_status == BookingFulgs::ON_HOLD_FLUG )
                                                    <li>
                                                        <form method="post" action="{{ Route('change_booking_status') }}">

                                                            {{csrf_field()}}

                                                            <input type="hidden" name="bid" value="{{$value->booking_order_id}}">

                                                            @if($value->booking_status == BookingFulgs::BOOKED_FLUG)
                                                                <button class="deleteButton changes_status" value="{{ BookingFulgs::ON_HOLD_FLUG }}" name="change_status">
                                                                    On Hold
                                                                </button>
                                                            @elseif($value->booking_status == BookingFulgs::ON_HOLD_FLUG)
                                                                <button class="deleteButton changes_status" value="{{ BookingFulgs::BOOKED_FLUG }}" name="change_status">
                                                                    On Booked
                                                                </button>
                                                            @endif
                                                        </form>                                           
                                                    </li>
                                                    <li>
                                                        <a href="{{ Route('booking_details_cancel_action', $value->booking_order_id) }}" class="deleteButton">Cancel</a>
                                                    </li>
                                                @endif
                                            @endif
                                            {{-- <li>
                                                <a href="{{ Route('booking_files_download', $value->id) }}" class="btn btn-info">Download Files</a>
                                            </li> --}}
                                        </ul>
                                    
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
