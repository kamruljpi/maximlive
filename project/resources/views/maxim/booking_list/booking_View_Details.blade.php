@extends('layouts.dashboard')
@section('page_heading', trans("others.mxp_menu_booking_view_details") )
@section('section')
<?php 
    // print_r("<pre>");
    // print_r($bookingDetails->bookings_challan_table);
    // print_r(session('data'));
    // print_r("</pre>");
    use App\Http\Controllers\taskController\Flugs\Role\PlaningFlugs;
    use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
    $object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
    $roleCheck = $object->getRole();
?>
<div class="row">
    <div class="col-sm-2">
        <div class="form-group "> {{--URL::previous()--}}
            <a href="{{ Route('booking_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
            <i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

@if(Session::has('empty_message'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_message') ))
@endif
@if(Session::has('message'))
    <div class="alert alert-success">
        <ul>
            {{ Session::get('message') }}
        </ul>
    </div>
@endif
@if(Session::has('error-m'))
    <div class="alert alert-danger">
        <ul>
            {{ Session::get('error-m') }}
        </ul>
    </div>
@endif
        
@if($roleCheck == 'p')
    @if($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info" style="font-size: 18px;box-shadow: 0 10px 20px rgba(0,0,0,0.10), 0 6px 15px rgba(0,0,0,0.15);
                    z-index: 999;">
                  <center><strong>Accept!</strong> this Order and go to proccessing. <a href="{{route('accepted_booking')}}/{{$bookingDetails->booking_order_id}}" style="font-size: 20px;font-weight: bold;" title="Click Me"> Accept</a></center>
                </div>
            </div>
        </div>
    @endif

    @if(session('data') == BookingFulgs::BOOKING_PROCESS_FLUG)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="normal-btn-success">
                    <button type="button" class="close">×</button>
                    Booking Accepted.
                </div>
            </div>
        </div>        
    @endif
@endif

<div class="panel panel-default">
    <div class="panel-heading">
        <div style="font-size: 120%">Booking Details</div>
    </div>
    <div class="panel-body aaa">
        <div class="panel panel-default col-sm-7">
            <br>
            <p >Buyer name:<b> {{ $bookingDetails->buyer_name }}</b></p>
            <p >Company name:<b> {{ $bookingDetails->Company_name }}</b></p>
            <p >Buyer address:<b> {{ $bookingDetails->address_part1_invoice }}{{ $bookingDetails->address_part2_invoice }}</p>
            <p >Mobile num:<b> {{ $bookingDetails->mobile_invoice }}</b></p>
        </div>
        <div class="panel panel-default col-sm-5">
            <br>
            <p >Booking Id:<b> {{ $bookingDetails->booking_order_id }}</b></p>
            <p >Booking status:<b> {{ $bookingDetails->booking_status }}</b></p>
            <p >Oreder Date:<b> {{ $bookingDetails->bookings[0]->orderDate }}</b></p>
            <p >Shipment Date:<b> {{ $bookingDetails->bookings[0]->shipmentDate }}</b></p>
            @if($roleCheck == 'p')
                @if($bookingDetails->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG)
                    <p style="font-size: 15px;"><strong>Accepted by</strong> <span style="color:red;">{{$bookingDetails->first_name}}{{$bookingDetails->last_name}}</span></p>
               @endif
            @endif
        </div>
        
            <table class="table table-bordered vi_table">
                <thead>
                    <tr>
                        @if($roleCheck == 'p')
                        <th>#</th>
                        @endif
                        <th>Job No.</th>
                        <th width="15%">ERP Code</th>
                        <th width="20%">Item Code</th>
                        <th width="5%">Season Code</th>
                        <th>OOS No.</th>
                        <th>Style</th>
                        <th>PO/Cat No.</th>
                        <th>GMTS Color</th>
                        <th width="15%">Size</th>
                        <th>Sku</th>
                        <th>Order Qty</th>
                        @if($roleCheck != 'p')
                        <th width="25%">Action</th>
                        @endif
                        @if($roleCheck == 'p')
                        <th>IPO QTY</th>
                        <th>MRF QTY</th>
                        @endif
                    </tr>
                </thead>
                
                @if($roleCheck == 'empty')
                    <tbody>
                        @foreach($bookingDetails->bookings as $bookedItem)
                        <?php $jobId = (8 - strlen($bookedItem->id)); ?>
                        <tr style="">
                            <td>{{ str_repeat('0',$jobId) }}{{ $bookedItem->id }}</td>                
                            <td>{{$bookedItem->erp_code}}</td>
                            <td>{{$bookedItem->item_code}}</td>
                            <td>{{$bookedItem->season_code}}</td>
                            <td>{{$bookedItem->oos_number}}</td>
                            <td>{{$bookedItem->style}}</td>
                            <td>{{$bookedItem->poCatNo}}</td>
                            <td>{{$bookedItem->gmts_color }}</td>
                            <td>{{$bookedItem->item_size}}</td>
                            <td>{{$bookedItem->sku}}</td>
                            <td>{{$bookedItem->item_quantity}}</td>                         
                            <td>
                                <div style="float: left;width: 46%;">
                                <form method="POST" action="{{route('booking_details_update_view')}}" >
                                    {{csrf_field()}}
                                    <input type="hidden" name="job_id" value="{{$bookedItem->id}}">
                                    <input type="hidden" name="party_id" value="{{$bookingDetails->party_id_->party_id_}}">
                                    <button class="form-control btn btn-primary" {{($bookingDetails->booking_status != BookingFulgs::BOOKED_FLUG) ? 'disabled' :''}}>Edit</button>
                                </form>
                                </div>
                                <div style="float: right;">
                                <a href="{{Route('booking_job_id_delete_action')}}/{{$bookedItem->id}}" class="form-control deleteButton btn btn-danger" {{($bookingDetails->booking_status != BookingFulgs::BOOKED_FLUG) ? 'disabled' :''}}>Delete</a>
                                </div>
                            </td>                    
                        </tr>
                        @endforeach
                    </tbody>
                @elseif($roleCheck == 'p')                        
                <form action="{{route('ipo_mrf_define')}}">
           {{csrf_field()}}
           <input type="hidden" name="booking_order_id" value="{{$bookingDetails->booking_order_id}}">
                    <tbody>              
                        @foreach($bookingDetails->bookings_challan_table as $bookedItem)
                        <?php $jobId = (8 - strlen($bookedItem->id)); ?>
                        <tr style="">
                            <label for="job_id">
                            <td width="3.5%">
                                <input type="checkbox" name="job_id[]" value="{{$bookedItem->id}}" class="form-control" id="select_check" {{($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ($bookedItem->left_mrf_ipo_quantity <= 0)?'disabled' :''}}>
                            </td>
                            <td>{{ str_repeat('0',$jobId) }}{{ $bookedItem->id }}</td>           
                            <td>{{$bookedItem->erp_code}}</td>
                            <td>{{$bookedItem->item_code}}</td>
                            <td>{{$bookedItem->season_code}}</td>
                            <td>{{$bookedItem->oos_number}}</td>
                            <td>{{$bookedItem->style}}</td>
                            <td>{{$bookedItem->poCatNo}}</td>
                            <td>{{$bookedItem->gmts_color }}</td>
                            <td>{{$bookedItem->item_size}}</td>
                            <td>{{$bookedItem->sku}}</td>
                            <td>{{$bookedItem->left_mrf_ipo_quantity}}</td>
                            <td>{{$bookedItem->ipo_quantity}}</td>
                            <td>{{$bookedItem->mrf_quantity}}</td>
                            </label>
                        </tr>
                        @endforeach                        
                    </tbody>
                @endif               
            </table>
            @if($roleCheck == 'p')
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" name="increase_value" class="form-control increase_field hidden" placeholder="Increase Value">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group pull-right">
                        <label class="radio-inline">
                            <input type="radio" name="ipo_or_mrf" value="ipo" style="margin: 2px -30px 0px" {{($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''}}>IPO
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="ipo_or_mrf" value="mrf" style="margin: 2px -30px 0px" {{($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''}}>MRF
                        </label>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px" {{($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''}}>
                            Submit
                        </button>
                    </div>
                </div>
            </div>                    
            @endif
        </form>
    </div>
</div>

    @if($roleCheck == 'p')
    <div class="panel panel-default">
        <div class="panel-heading" style="font-size: 120%">Mrf Details</div>
        <div class="panel-body aaa">
            <table class="table table-bordered">
                <tr>
                    <thead>
                        <th>Job No.</th>
                        <th width="17%">MRF No.</th>
                        <th>Item Code</th>
                        <th>GMTS Color</th>
                        <th width="8%">Item Size</th>
                        <th>Quantity</th>
                        <th>Delivered Quantity</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                </tr>
                <tbody>
                @foreach($bookingDetails->mrf as $value)
                <?php 
                    $idstrcount = (8 - strlen($value->job_id));
                    // $gmts_color = explode(',', $value->gmts_color);
                    // $itemsize = explode(',', $value->item_size);
                    // $mrf_quantity = explode(',', $value->mrf_quantity);
                ?>
                <tr>
                    <td>{{ str_repeat('0',$idstrcount) }}{{$value->job_id}}</td>
                    <td>{{$value->mrf_id}}</td>
                    <td>{{$value->item_code}}</td>
                    <td>{{$value->gmts_color}}</td>                    
                    <td width="18%">{{$value->item_size}}</td>
                    <td>{{$value->mrf_quantity}}</td>
                    <td>Delivered Quantity</td>
                    <td>{{$value->shipmentDate}}</td>
                    <td>{{$value->mrf_status}}</td>
                    <td>Action</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading" style="font-size: 120%">IPO Details</div>
        <div class="panel-body aaa">
            <table class="table table-bordered">
                <tr>
                    <thead>
                        <th>Job No.</th>
                        <th>IPO No.</th>
                        <th>Item Code</th>
                        <th>Color</th>
                        <th>Item Size</th>
                        <th>Quantity</th>
                        <th>Delivered Quantity</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                </tr>
                @php($j=1)
                <tbody>

                @foreach($bookingDetails->ipo as $value)
                <?php
                    $idstrcount = (8 - strlen($value->job_id));
                    // $gmts_color = explode(',', $value->gmts_color);
                    // $itemsize = explode(',', $value->item_size);
                    // $ipo_quantity = explode(',', $value->ipo_quantity);
                ?>
                <tr>
                    <td>{{ str_repeat('0',$idstrcount) }}{{$value->job_id}}</td>
                    <td>{{$value->ipo_id}}</td>
                    <td>{{$value->item_code}}</td>
                    <td>{{$value->gmts_color}}</td>                    
                    <td width="18%">{{$value->item_size}}</td>
                    <td>{{$value->ipo_quantity}}</td>
                    <td>Delivered Quantity</td>
                    <td>{{$value->shipmentDate}}</td>
                    <td>{{$value->ipo_status}}</td>
                    <td>Action</td>
                    <!-- <td>{{Carbon\Carbon::parse($value->created_at)}}</td> -->
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
