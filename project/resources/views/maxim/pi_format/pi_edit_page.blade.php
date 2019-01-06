@extends('layouts.dashboard')
@section('page_heading','Reverse PI Job ID')
@section('section')

	<?php 
		use App\Http\Controllers\taskController\Flugs\JobIdFlugs;

		$jobId = (JobIdFlugs::JOBID_LENGTH - strlen($pi_jobid_details->job_no));
	?>

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
	            <a href="{{ Route('pi_reverse_view', !empty($pi_jobid_details->p_id) ? $pi_jobid_details->p_id : '0') }}" class="btn btn-primary" style="width: 15%; margin: 10px 0px 5px 0px;">Back</a>
	        </div>

	        <div class="col-md-12">
	            <h3>Update PI: Job ID {{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{ $pi_jobid_details->job_no }} </h3>
	        </div>
	    </div>
	    <div style="padding-top: 20px;"></div>

	    <form action="{{route('pi_reverse_edit_action')}}" method="POST">
	            {{csrf_field()}}
	        <input type="hidden" name="p_id" value="{{ $pi_jobid_details->p_id }}">
	        <input type="hidden" name="job_id" value="{{ $pi_jobid_details->job_no }}">
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
	                    <input type="hidden" name="others_color" class="others_color" id="others_color" value="">

	                    <!-- PO/Cat No -->
	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="poCatNo" class="form-control" placeholder="PO Cat No" title ="PO Cat No" id="item_po_cat_no"  value="{{ $pi_jobid_details->poCatNo }}" required>
	                        </div>
	                    </td>
	                    <!-- end -->

	                    <!-- OOS Number -->
	                    <td>
	                        <div class="form-group ">
	                            <input type="text" name="oos_number" class="form-control" placeholder="OOS Number" title="OOS Number" id="item_oos_number" value="{{ $pi_jobid_details->oos_number }}">
	                        </div>
	                    </td>
	                    <!-- end -->

	                    <td width="15%" style="">
	                        <div class="form-group item_codemxp_parent">
	                            {{-- <input class="booking_item_code item_code easyitemautocomplete" type="text" name="item_code"  id="item_codemxp" data-parent="tr_clone" value="{{$pi_jobid_details->item_code}}" readonly="true"> --}}

	                            <input type="text" name="item_code" class="form-control" value="{{$pi_jobid_details->item_code}}" readonly="true">

	                        </div>
	                    </td>
	                    <td>
	                        <div class="form-group" style="width: 200px !important;">
	                            <input type="text" name="erp" class="form-control erpNo" id="erpNo" readonly = "true" value="{{ $pi_jobid_details->erp_code }}">
	                            <!-- <select name="erp[]" class="form-control erpNo" id="erpNo" readonly = "true"> -->
	                            </select>
	                        </div>
	                    </td>

	                    <!-- description -->
	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="item_description" class="item_description form-control" id="item_description" value="{{ $pi_jobid_details->item_description }}" readonly>
	                        </div>
	                    </td>
	                    <!--end -->

	                    <td>
	                        <div class="form-group" style="    width: 145px !important;">
	                        	<input type="text" name="gmts_color" class="form-control" value="{{ $pi_jobid_details->gmts_color }}" readonly="true">

	                            {{-- <select name="gmts_color" class="form-control itemGmtsColor" id="itemGmtsColor" placeholder="Empty color">
	                                 <option value="">Empty color</option>
	                                <option value="{{ $pi_jobid_details->gmts_color }}">{{ $pi_jobid_details->gmts_color }}</option>
	                            </select> --}}

	                        </div>
	                    </td>
	                    <td>
	                        <div class="form-group" style="    width: 200px !important;">
	                            <input type="text" name="item_size" class="form-control" value="{{ $pi_jobid_details->item_size }}" readonly="true">

	                            {{-- <select name="item_size" class="form-control itemSize" id="itemSize"  required>
	                                <option value="{{ $pi_jobid_details->item_size }}">{{ $pi_jobid_details->item_size }}</option>
	                            </select> --}}

	                        </div>
	                    </td>


	                    <!-- Style -->
	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="style" class="form-control item_style" id="item_style" value="{{ $pi_jobid_details->style }}" required>
	                        </div>
	                    </td>
	                    <!-- end -->

	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="sku" class="form-control item_sku" id="item_sku" value="{{ $pi_jobid_details->sku }}" required>
	                        </div>
	                    </td>

	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="item_qty" class="form-control easyitemautocomplete item_qty" id="item_qtymxp" value="{{ $pi_jobid_details->item_quantity }}" required>
	                        </div>
	                    </td>

	                    <td>
	                        <div class="form-group">
	                            <input type="text" name="item_price" class="form-control item_price" readonly="true" value="{{ $pi_jobid_details->item_price }}" required>
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
    {{-- <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/booking.js') }}"></script> --}}
@stop
