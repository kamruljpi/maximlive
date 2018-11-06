@extends('layouts.dashboard')
@section('page_heading', 'MRF Details')
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
            <a href="{{ Route('os_mrf_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
            <i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="col-sm-8"></div>
    <div class="col-sm-2">
        <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #fff; border:0;">
                    <span style="font-size: 25px;">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" style="left:-142px !important;">
                    <li><a href="#">Cencel</a></li>
                </ul>
            </div>
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
        
{{-- @if($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) --}}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" style="font-size: 18px;box-shadow: 0 10px 20px rgba(0,0,0,0.10), 0 6px 15px rgba(0,0,0,0.15);
                z-index: 999;">
              <center><strong>Accept!</strong> this Order and go to proccessing. <a href="#" style="font-size: 20px;font-weight: bold;" title="Click Me"> Accept</a></center>
            </div>
        </div>
    </div>

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
{{-- @endif --}}

<div class="panel panel-default">
    <div class="panel-heading">
        <div style="font-size: 120%">MRF Details</div>
    </div>
    <div class="panel-body">
        <div class="panel panel-default col-sm-7">
            <br>
            <label>Vendor Name : {{ $bookingDetails->buyer_name }}</label><br>
            <label>Prepared By : {{ $bookingDetails->buyer_name }}</label><br>
            <label>Order Date : {{ $bookingDetails->buyer_name }}</label><br>
            <label>Accepted : {{ $bookingDetails->buyer_name }}</label><br>
        </div>
        <div class="panel panel-default col-sm-5">
            <br>
            <label>MRF No. :{{ $bookingDetails->booking_order_id }}</label><br>
            <label>Booking No. :{{ $bookingDetails->booking_order_id }}</label><br>
            <label>Requested Shipment Date :{{ $bookingDetails->booking_order_id }}</label><br>
            <label>MRF : <span style="color:red;">Processing </span></label><br>
        </div>

        <form action="{{Route('os_po_genarate_view')}}" method="POST">
            {{csrf_field()}}
            <table class="table table-bordered vi_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Job No.</th>
                        <th>OOS No.</th>
                        <th>PO/Cat No.</th>
                        <th width="">Item Code</th>
                        <th width="">ERP Code</th>
                        <th>Description</th>
                        <th width="">Season Code</th>
                        <th>GMTS Color</th>
                        <th width="">Size</th>
                        <th>Style</th>
                        <th>Sku</th>
                        <th>Order Qty</th>
                        <th width="">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="4%">
                            <input type="checkbox" name="job_id" value="" class="form-control">
                        </td>
                        <td>001</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>dsd</td>
                        <td>
                            <a href="#" class="btn btn-primary"> Accept</a>
                        </td>
                    </tr>
                </tbody>         
            </table>

            <div class="form-group">
                <div class="col-sm-2 pull-right">
                    <button class="btn btn-success form-control"> Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        PO Details
    </div>
    <div class="panel-body">
        <table class="table table-bordered vi_table">
            <thead>
                <tr>
                    <th>Job No.</th>
                    <th>Order Date</th>
                    <th>Shipment date</th>
                    <th width="">Item Code</th>
                    <th>ERP</th>
                    <th>Order Qty</th>
                    <th>Supplier Name</th>
                    <th width="">Current Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>

                </tr>
            </tbody>         
        </table>
    </div>
</div>

@endsection
