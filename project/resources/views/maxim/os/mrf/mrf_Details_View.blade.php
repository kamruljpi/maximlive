@extends('layouts.dashboard')
@section('page_heading', 'MRF Details')
@section('section')
<?php 
    // print_r("<pre>");
    // print_r($shipmentDate);
    // print_r(session('data'));
    // print_r("</pre>");
    use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
    use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;

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
                    <li><a href="{{Route('os_cancel_mrf_action')}}/{{$mrfDetails[0]->mrf_id}}">Cencel</a></li>
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
        
 @if($mrfDetails[0]->mrf_status == MrfFlugs::OPEN_MRF)
    <div class="row">
        <div class="col-md-12">
            <center><div class="alert alert-info" style="font-size: 18px;box-shadow: 0 10px 20px rgba(0,0,0,0.10), 0 6px 15px rgba(0,0,0,0.15);
                z-index: 999;">
              <strong>Accept!</strong> this Order and go to proccessing. <a href="{{Route('os_accepted_mrf_action')}}/{{$mrfDetails[0]->mrf_id}}" style="font-size: 20px;font-weight: bold;" title="Click Me"> Accept</a>
            </div></center>
        </div>
    </div>
@endif

    @if(session('data'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="normal-btn-success">
                    <button type="button" class="close">×</button>
                    Booking {{session('data')}}.
                </div>
            </div>
        </div>        
    @endif

<div class="panel panel-default">
    <div class="panel-heading">
        <div style="font-size: 120%">MRF Details</div>
    </div>
    <div class="panel-body">
        <div class="panel panel-default col-sm-7">
            <br>
            <label>Vendor Name : {{ $mrfDetails[0]->buyer_name }}</label><br>
            <label>Prepared By : {{$mrfDetails[0]->first_name}} {{$mrfDetails[0]->last_name}}</label><br>
            <label>Order Date : {{ $mrfDetails[0]->orderDate }}</label><br>
            <label>Accepted : <span style="color:red;">{{ $mrfDetails[0]->accpeted->first_name }} {{ $mrfDetails[0]->accpeted->last_name }}</span></label><br>
        </div>
        <div class="panel panel-default col-sm-5">
            <br>
            <label>MRF No : {{ $mrfDetails[0]->mrf_id }}</label><br>
            <label>Booking No : {{ $mrfDetails[0]->booking_order_id }}</label><br>
            <label>Requested Shipment Date : {{ $mrfDetails[0]->shipmentDate }}</label><br>
            <label>MRF : <span style="color:red;">{{ ucwords($mrfDetails[0]->mrf_status) }}</span></label><br>
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
                        {{-- <th width="">Season Code</th> --}}
                        <th>GMTS Color</th>
                        <th width="">Size</th>
                        <th>Style</th>
                        <th>Sku</th>
                        <th>Order Qty</th>
                        <th width="">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mrfDetails as $values)
                    <?php 
                        $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($values->job_id));
                    ?>
                    <tr>
                        <td width="4%">
                            <input type="checkbox" name="job_id" value="" class="form-control" value="{{$values->job_id}}">
                        </td>
                        <td>{{ str_repeat(JobIdFlugs::STR_REPEAT ,$idstrcount) }}{{$values->job_id}}</td>
                        <td>{{$values->oos_number}}</td>
                        <td>{{$values->poCatNo}}</td>
                        <td>{{$values->item_code}}</td>
                        <td>{{$values->erp_code}}</td>
                        <td>{{$values->item_description}}</td>
                        {{-- <td>{{$values->season_code}}</td> --}}
                        <td>{{$values->gmts_color}}</td>
                        <td>{{$values->item_size}}</td>
                        <td>{{$values->style}}</td>
                        <td>{{$values->sku}}</td>
                        <td>{{$values->mrf_quantity}}</td>
                        <td>
                            <a href="#" class="btn btn-primary" style="z-index:999;">{{$values->job_id_current_status}}</a>
                        </td>
                    </tr>
                    @endforeach
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
