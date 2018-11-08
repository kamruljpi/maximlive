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
                    {{session('data')}}.
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
            <label>Prepared By : {{ ucwords($mrfDetails[0]->first_name)}} {{ ucwords($mrfDetails[0]->last_name)}}</label><br>
            <label>Order Date : {{ $mrfDetails[0]->orderDate }}</label><br>
            <label>Accepted : <span style="color:red;">{{ ucwords($mrfDetails[0]->mrf_accpeted->first_name) }} {{ ucwords($mrfDetails[0]->mrf_accpeted->last_name) }}</span></label><br>
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

            <div class="row">
                <div class="col-sm-8">
                    <div>
                        {{-- <span>Remaining</span> --}}
                    </div>
                </div>
                <div class="col-sm-4 pull-right">
                    <div class="form-group">
                        <label class="col-sm-12 label-control">Suppliers</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="supplier_id">
                                <option value="">Choose a Option</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->supplier_id}}">{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

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
                    @foreach($mrfDetails as $keys => $values)
                    <?php 
                        $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($values->job_id));
                    ?>
                    <tr>
                        <td width="4%">
                            <input type="checkbox" name="job_id[]" class="form-control" value="{{$values->job_id}}" {{($values->job_id_current_status != MrfFlugs::JOBID_CURRENT_STATUS_OPEN && Auth::user()->user_id != $values->current_status_accepted_user_id) ? 'disabled' :''}}>
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
                            @if($values->job_id_current_status != MrfFlugs::JOBID_CURRENT_STATUS_OPEN && Auth::user()->user_id == $values->current_status_accepted_user_id)
                                <a href="{{Route('os_mrf_jobid_cancel')}}/{{$values->job_id}}" class="btn btn-primary">
                                    {{-- {{ucwords($values->job_id_current_status)}} --}}
                                    Cancel
                                </a>
                            @elseif($values->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT)
                                <label style="font-weight: bold;background-color: #F1F1F1;padding: 3px;">{{ ucwords($values->jobid_accpeted->first_name) }} {{ ucwords($values->jobid_accpeted->last_name) }}</label>
                            @else
                                <div style="z-index: 9999;">
                                    <a href="{{Route('os_mrf_jobid_accept')}}/{{$values->job_id}}" class="btn btn-primary">
                                        {{ucwords($values->job_id_current_status)}}
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>         
            </table>

            <div class="form-group">
                <div class="col-sm-2 pull-right">
                    <button class="btn btn-success form-control abc"> Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" style="margin-top: 150px;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <div style="text-align: center;">
                <i class="fa fa-warning" style="font-size:100px;color:red"></i><br>
                <label style="font-size: 25px;">Please accept selected item.</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close__" data-dismiss="modal">Close</button>
        </div>
    </div>
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
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                    <td>011</td>
                </tr>
            </tbody>         
        </table>
    </div>
</div>

<script type="text/javascript">
    $('{{session('datas')}}').show();
</script>
@endsection
