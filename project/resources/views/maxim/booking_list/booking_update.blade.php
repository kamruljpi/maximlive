@extends('layouts.dashboard')
@section('page_heading','Update Booking')
@section('section')
    <style type="text/css">
        .top-div{
            background-color: #f9f9f9;
            padding:5px 0px 5px 10px;
            border-radius: 7px;
        }

        .btn-file {
            position: relative;
            overflow: hidden;
        }

        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
        .idclone .form-group{
            width: 130px !important;
        }
    </style>

<?php $jobId = (8 - strlen($mxpBooking->id)); ?>

<input type="hidden" name="companyIdForBookingOrder" value="{{$party_id}}">
<input type="hidden" name="check_item_size" value="{{$mxpBooking->item_size}}">
<input type="hidden" name="check_gmts_color" value="{{$mxpBooking->gmts_color}}">

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        @if(!empty($pi_value->p_id))
            <h3><label>Did you know ? Job Id <span style="color:red;">{{ str_repeat('0',$jobId) }}{{ $mxpBooking->id }}</span> already make a PI. PI id is <span style="color:red;">{{$pi_value->p_id}}</span>. If you update it's not changing your PI. You need to create new PI.</label>
                </h3>
        @endif
    </div>
</div>
<div class="col-md-12">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(Session::has('error_code'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('error_code') ))
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <a href="{{ Route('booking_list_details_view', $mxpBooking->booking_order_id) }}" class="btn btn-primary" style="width: 15%; margin: 10px 0px 5px 0px;">Back</a>
        </div>
        <div class="col-md-12">
            <h3>Update booking: Job ID {{ str_repeat('0',$jobId) }}{{ $mxpBooking->id }} </h3>
        </div>
    </div>
    <div style="padding-top: 20px;"></div>

    <form action="{{route('booking_details_update_action')}}" method="POST">
            {{csrf_field()}}
        <input type="hidden" name="booking_id" value="{{ $mxpBooking->id }}">
        <div class="table-responsive" style="height: 120px;">
            <table class="table-striped " style="overflow-y: scroll;" id="filed_increment">
                <thead>
                <tr>
                    <th width="10%">PO/Cat No</th>
                    <th width="10%">OOS No</th>
                    <th width="10%">Item Code</th>
                    <th width="10%">ERP Code</th>
                    <th width="10%">Item Description</th>
                    <th width="15%">GMTS / Item Color</th>
                    <th width="15%">Item Size</th>
                    <th width="15%">Style</th>
                    <th width="15%">SKU</th>
                    <th width="15%">Item Qty</th>
                    <th width="15%">Item price</th>
                    <!-- <th></th> -->
                </tr>
                </thead>
                <tbody class="idclone" >
                <tr class="tr_clone">
                    <input type="hidden" name="others_color[]" class="others_color" id="others_color" value="">

                    <!-- PO/Cat No -->
                    <td>
                        <div class="form-group">
                            <input type="text" name="poCatNo" class="form-control" placeholder="PO Cat No" title ="PO Cat No" id="item_po_cat_no"  value="{{ $mxpBooking->poCatNo }}" required>
                        </div>
                    </td>
                    <!-- end -->

                    <!-- OOS Number -->
                    <td>
                        <div class="form-group ">
                            <input type="text" name="oos_number" class="form-control" placeholder="OOS Number" title="OOS Number" id="item_oos_number" value="{{ $mxpBooking->oos_number }}">
                        </div>
                    </td>
                    <!-- end -->

                    <td width="15%" style="padding-top: 15px;">
                        <div class="form-group item_codemxp_parent">
                            <input class="booking_item_code item_code easyitemautocomplete" type="text" name="item_code"  id="item_codemxp" data-parent="tr_clone" value="{{$mxpBooking->item_code}}" required>

                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="    width: 200px !important;">
                            <input type="text" name="erp" class="form-control erpNo" id="erpNo" readonly = "true" value="{{ $mxpBooking->erp_code }}">
                            <!-- <select name="erp[]" class="form-control erpNo" id="erpNo" readonly = "true"> -->
                            </select>
                        </div>
                    </td>

                    <!-- description -->
                    <td>
                        <div class="form-group">
                            <input type="text" name="item_description" class="item_description form-control" id="item_description" value="{{ $mxpBooking->item_description }}" readonly>
                        </div>
                    </td>
                    <!--end -->

                    <td>
                        <div class="form-group" style="    width: 145px !important;">
                            <select name="item_gmts_color" class="form-control itemGmtsColor" id="itemGmtsColor" readonly="true">
                                <option value="{{ $mxpBooking->gmts_color }}">{{ $mxpBooking->gmts_color }}</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="    width: 200px !important;">
                            {{--<input type="text" name="item_size[]" class="form-control">--}}

                            <select name="item_size" class="form-control itemSize" id="itemSize" disabled = "true" required>
                                <option value="{{ $mxpBooking->item_size }}">{{ $mxpBooking->item_size }}</option>
                            </select>
                        </div>
                    </td>


                    <!-- Style -->
                    <td>
                        <div class="form-group">
                            <input type="text" name="style" class="form-control item_style" id="item_style" value="{{ $mxpBooking->style }}" required>
                        </div>
                    </td>
                    <!-- end -->

                    <td>
                        <div class="form-group">
                            <input type="text" name="sku" class="form-control item_sku" id="item_sku" value="{{ $mxpBooking->sku }}" required>
                        </div>
                    </td>

                    <td>
                        <div class="form-group">
                            <input type="text" name="item_qty" class="form-control easyitemautocomplete item_qty" id="item_qtymxp" value="{{ $mxpBooking->item_quantity }}" required>
                        </div>
                    </td>

                    <td>
                        <div class="form-group">
                            <input type="text" name="item_price" class="form-control item_price" readonly="true" value="{{ $mxpBooking->item_price }}" required>
                            <!-- readonly -->
                        </div>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="form-group" style="margin-top: 20px;">
            <div class="col-sm-2 col-md-2 pull-right">
                <button type="submit" class="btn btn-primary deleteButton form-control" style="margin-right: 15px; width: 100%;">
                    {{ trans('others.update_button') }}
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@section('LoadScript')
    <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/booking.js') }}"></script>
@stop
