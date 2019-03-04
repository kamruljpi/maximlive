@extends('layouts.dashboard')
@section('page_heading', 'MRF Details')
@section('section')
    <?php
        use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
        use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
    ?>

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

    <div class="row">
        <div class="col-sm-2">
            <div class="form-group "> {{--URL::previous()--}}
                <a href="{{ Route('mrf_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
                <i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="col-sm-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
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
                        <li>
                            <form action="{{Route('os_cancel_mrf_action')}}">

                                {{csrf_field()}}

                                @if(is_array($mrf_ids) && !empty($mrf_ids))
                                    @foreach($mrf_ids as $mrf_idssss)
                                        <input type="hidden" name="mrf_ids[]" value="{{$mrf_idssss}}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="mrf_ids[]" value="{{$mrf_ids}}">
                                @endif

                                <button type="submit" style="background-color: #fff; border: 1px solid #fff; padding-left: 10px; " class="form-control">Cencel</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @foreach($mrfDetails as $mrf_details)
        @if($mrf_details->mrf_status == MrfFlugs::OPEN_MRF)
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-info" style="font-size: 18px;box-shadow: 0 10px 20px rgba(0,0,0,0.10), 0 6px 15px rgba(0,0,0,0.15);
                        z-index: 999;">                  

                        <form action="{{Route('os_accepted_mrf_action')}}">
                            {{csrf_field()}}

                            @if(is_array($mrf_ids) && !empty($mrf_ids))
                                @foreach($mrf_ids as $mrf_idssss)
                                    <input type="hidden" name="mrf_ids[]" value="{{$mrf_idssss}}">
                                @endforeach
                            @else
                                <input type="hidden" name="mrf_ids[]" value="{{$mrf_ids}}">
                            @endif

                            <strong>Accept!</strong> this Order and go to proccessing. 
                            <button style="font-size: 20px;font-weight: bold; background-color: #d4ecf7; border: 1px solid #d4ecf7;text-decoration: underline;"
                            title="Click me" >Accept</button>
                        </form>
                    </div>
                </div>
            </div>
            @break
        @endif
    @endforeach

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
                @if($mrfDetails[0]->booking_category)
                <label>Category: <b>{{$categorys}}</label><br>
                @endif
                <label>MRF No : {{(is_array($mrf_ids) ? implode(' , ', $mrf_ids) : $mrf_ids)}}</label><br>
                <label>Booking No : {{ $mrfDetails[0]->booking_order_id }}</label><br>
                <label>Requested Shipment Date : {{ Carbon\Carbon::Parse($mrfDetails[0]->shipmentDate)->format('d-m-Y') }}</label><br>
                <label>MRF : <span style="color:red;">{{ ucwords($mrfDetails[0]->mrf_status) }}</span></label><br>
            </div>

                    <?php

                        $mrf_idssa = [] ;
                    ?>
                <form action="{{Route('os_po_genarate_view')}}" method="POST">
                    {{csrf_field()}}

                    @if(is_array($mrf_ids) && !empty($mrf_ids))
                        @foreach($mrf_ids as $mrf_idssss)
                            <input type="hidden" name="mrf_ids[]" value="{{$mrf_idssss}}">
                        <?php  $mrf_idssa[] = $mrf_idssss; ?>
                        @endforeach
                    @else
                        <input type="hidden" name="mrf_ids[]" value="{{$mrf_ids}}">
                    @endif
                <div class="row">
                    <div class="panel panel-default col-sm-12" style="background-color: #000a12; color: #ffffff; text-align: center">
                        <h4>Available Jobs: {{isset($mrfDetails->available_jobs) ? $mrfDetails->available_jobs : ''}}</h4>
                    </div>
                </div>

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
                                <select class="form-control" name="supplier_id" required="true">
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
                            <th>Item Color</th>
                            <th width="">Size</th>
                            <th>Style</th>
                            <th>Sku</th>
                            <th>Order Qty</th>
                            <th width="20%">Action</th>
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
                            <td>{{$values->other_colors}}</td>
                            <td>{{$values->item_size}}</td>
                            <td>{{$values->style}}</td>
                            <td>{{$values->sku}}</td>
                            <td>{{$values->mrf_quantity}}</td>
                            <td>
                                @if($values->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_WAITING_FOR_GOODS)

                                    <label style="font-weight: bold;background-color: #F1F1F1;padding: 3px;">
                                        Request sent ({{ucwords($values->jobid_accpeted->first_name)}}) 
                                    </label>

                                @elseif($values->job_id_current_status != MrfFlugs::JOBID_CURRENT_STATUS_OPEN && Auth::user()->user_id == $values->current_status_accepted_user_id)

                                    <a href="{{Route('os_mrf_jobid_cancel')}}/{{$values->job_id}}" class="btn btn-primary">
                                        Cancel
                                    </a>

                                    <?php Session::flash('mrf_ids',$mrf_idssa);?>

                                @elseif($values->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT)

                                    <label style="font-weight: bold;background-color: #F1F1F1;padding: 3px;">
                                        {{ ucwords($values->jobid_accpeted->first_name) }} {{ ucwords($values->jobid_accpeted->last_name) }} (Accpeted)
                                    </label>
                                @else
                                    <div style="z-index: 9999;">

                                        <a href="{{Route('os_mrf_jobid_accept')}}/{{$values->job_id}}" class="btn btn-primary">
                                            {{ucwords($values->job_id_current_status)}}
                                        </a>

                                        <?php Session::flash('mrf_ids',$mrf_idssa);?>

                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>         
                </table>

                <div class="form-group">
                    <div class="col-sm-7"></div>
                    <div class="col-sm-3">
                        <input type="text" name="po_increase" class="form-control po_increase_field" id="po_increase" placeholder="Increase value" style="border-color: red;">
                    </div>
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
            SPO Details
        </div>
        <div class="panel-body">
            <table class="table table-bordered vi_table">
                <thead>
                    <tr>
                        <th>Job No.</th>
                        <th width="15%">SPO No.</th>
                        <th>Supplier Name</th>
                        <th>Person Name</th>
                        <th width="">Item Code</th>
                        <th>Size</th>
                        <th>Order Qty</th>
                        <th>Available Qty</th>
                        <th>Material</th>
                        <th>Shipment date</th>
                        <th width="">Current Status</th>
                        <th >Recieve Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mrfDetails as $poValues)
                        <form action="{{ Route('store_mrf') }}" method="POST">
                        {{csrf_field()}}
                        
                        <input type="hidden" name="is_type" value="mrf">
                        @if($poValues->po_details->job_id)
                            <?php 
                                $jobId = (JobIdFlugs::JOBID_LENGTH - strlen($poValues->po_details->job_id));
                            ?>
                            <tr>
                                <td><input type="hidden" name="job_id" value="{{$poValues->po_details->job_id}}">{{ str_repeat(JobIdFlugs::STR_REPEAT ,$jobId) }}{{$poValues->po_details->job_id}}</td>
                                <td>{{$poValues->po_details->po_id}}</td>
                                <td>{{$poValues->po_details->name}}</td>
                                <td>{{$poValues->po_details->person_name}}</td>
                                <td><input type="hidden" name="item_code" value="{{$poValues->po_details->item_code}}">{{$poValues->po_details->item_code}}</td>
                                <td><input type="hidden" name="item_size_width_height" value="{{$poValues->po_details->item_size_width_height}}">{{$poValues->po_details->item_size_width_height}}</td>
                                <td><input type="hidden" name="mrf_quantity" value="{{$poValues->po_details->mrf_quantity}}">{{$poValues->po_details->mrf_quantity}}</td>
                                <td>{{ $poValues->po_details->mrf_quantity-$poValues->po_details->left_quantity }}</td>
                                <td><input type="hidden" name="">{{$poValues->po_details->material}}</td>
                                <td><input type="hidden" name="shipment_date" value="{{ $poValues->po_details->shipment_date }}">{{Carbon\Carbon::Parse($poValues->po_details->shipment_date)->format('d-m-Y')}}</td>
                                <td>{{ucfirst(str_replace('_',' ',$poValues->po_details->job_id_current_status))}}</td>
                                
                                <td><input type="text" name="receive_qty" class="form-control" value="{{ $poValues->po_details->mrf_quantity-$poValues->po_details->left_quantity }}"></td>
                                
                                <input type="hidden" name="erp_code" value="{{ $poValues->po_details->erp_code }}">
                                <input type="hidden" name="item_description" value="{{ $poValues->po_details->item_description }}">
                                <input type="hidden" name="gmts_color" value="{{ $poValues->po_details->gmts_color }}">
                                <input type="hidden" name="mrf_id" class="form-control" value="{{ $poValues->po_details->mrf_id }}">
                                <input type="hidden" name="booking_order_id" class="form-control" value="{{ $poValues->po_details->booking_order_id }}">
                                
                                <td><button class="btn btn-success">Accept</button></td>
                            </tr>
                            </form>
                        @endif
                    @endforeach
                </tbody>         
            </table>
        </div>
    </div>

    <script type="text/javascript">
        $('{{session('datas')}}').show();
    </script>
@endsection
