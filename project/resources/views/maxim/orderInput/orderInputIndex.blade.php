@extends('layouts.dashboard')
@section('page_heading',$taskType)
@section('section')
  <?php 
    $general = 'Create Booking';
    $fsc     = 'Create FSC Booking'
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

  <form class="" action="{{ Route('booking_order_action') }}" role="form" method="POST" enctype="multipart/form-data" target="_blank">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="buyerDetails" value="{{$buyerDetails}}">
    @if($taskType == $general)
      <input type="hidden" name="is_type" value="general">
    @elseif($taskType == $fsc)
    <input type="hidden" name="is_type" value="fsc">
    @endif
    <div class="col-md-12" style="margin-top: 10px;">
      <div class="pull-left">
        <div class="form-group button_add pull-right">
          {{--<input type="file" name="booking_files[]" class="btn btn-success" id="" ><i class="fa fa-file" style="font-size:`16px;color:white; margin-right:7px;"></i>Add Files</input>--}}

          <label class="btn btn-default btn btn-success">
            <i class="fa fa-file" style="font-size:`16px;color:white; margin-right:7px;"></i>
            Select Files <input type="file" style="display: none;" name="booking_files[]" class="" id="" multiple disabled>
          </label>
        </div>
      </div>
    </div>

    <div class="top-div">
      <div class="row">
        <div class="col-md-4 col-xs-4">
          <div class="form-group">
            <label class="col-md-12">Buyer name</label>
            <div class="col-md-12">
              <input type="text" name="buyerName" class="form-control" readonly="true" value="{{$buyerDetails[0]->name_buyer}}" title="Buyer Name">
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-4">
          <div class="form-group " >
            <label class="col-md-12">Vendor name</label>
            <div class="col-md-12">
              <input type="text" name="CompanyName" class="form-control" readonly="true" value="{{$buyerDetails[0]->name}}" title="Company Name">
              <input type="hidden" name="companyIdForBookingOrder" value="{{$buyerDetails[0]->id}}">
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-4">
          <div class="form-group " >
            <label class="col-md-12">Category</label>
            <div class="col-md-12">
              <select name="booking_category" class="form-control" required="true">
                <option value="">Choose a option</option>
                <option value="normal_order">Normal order</option>
                <option value="urgent_order">Urgent order</option>
                <option value="top_urgent_order">Top Urgent order</option>
                <option value="top_urgent_order">Top Urgent order</option>
                <option value="export_goods">Export goods</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 col-xs-4">
          <div class="form-group">
            <label class="col-md-12">Order Date</label>
            <div class="col-md-12">
              <input type="text" name="orderDate" class="form-control" placeholder="Order Date" title="Order Date" value="{{carbon\carbon::today()->format('d-m-Y')}}" readonly>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-4">
          <div class="form-group " >
            <label class="col-md-12">Requested Delivery Date</label>
            <div class="col-md-12">
              <input type="date" id="datePickerDate" name="shipmentDate" class="form-control" placeholder="Request Date" title="Request Date" required>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-4">
          <div class="form-group " >
            <label class="col-md-12">Season Code</label>
            <div class="col-md-12">
              <input type="text" name="season_code" class="form-control" placeholder="Season Code" title ="Season Code">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div style="padding-top: 20px;"></div>

    <div class="table-responsive" style="height: 400px;">
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
                <input type="text" name="poCatNo[]" class="form-control" placeholder="PO/Cat No." title ="PO/Cat No." id="item_po_cat_no" required>
              </div>
          </td>
          <!-- end -->

          <!-- OOS Number -->
          <td>
            <div class="form-group ">
                <input type="text" name="oos_number[]" class="form-control" placeholder="OOS Number" title="OOS Number" id="item_oos_number" >
              </div>
          </td>
          <!-- end -->

          <td width="15%" style="padding-top: 15px;">
            <div class="form-group item_codemxp_parent">
              <input class="booking_item_code item_code easyitemautocomplete" type="text" name="item_code[]"  id="item_codemxp" data-parent="tr_clone" required placeholder="Item Code">

            </div>
          </td>
          <td>
            <div class="form-group" style="    width: 200px !important;">
              <input type="text" name="erp[]" class="form-control erpNo" id="erpNo" placeholder="ERP code" readonly = "true" >
              <!-- <select name="erp[]" class="form-control erpNo" id="erpNo" readonly = "true"> -->
              </select>
            </div>
          </td>

          <!-- description -->
          <td>
            <div class="form-group">
              <input type="text" name="item_description[]" class="item_description form-control" id="item_description" placeholder="Description" readonly required>
            </div>
          </td>
          <!--end -->

          <td>
            <div class="form-group" style="    width: 145px !important;">
              <select name="item_gmts_color[]" class="form-control itemGmtsColor" id="itemGmtsColor">
              <option value=" ">GMT/Color</option>
              </select>
            </div>
          </td>
          <td>
            <div class="form-group" style="    width: 200px !important;">
              {{--<input type="text" name="item_size[]" class="form-control">--}}

              <select name="item_size[]" class="form-control itemSize" id="itemSize" disabled = "true" required>
                <option value=" ">Item Size</option>
              </select>
            </div>
          </td>


          <!-- Style -->
          <td>
            <div class="form-group">
              <input type="text" name="style[]" class="form-control item_style" id="item_style" placeholder="Style" required>
            </div>
          </td>
          <!-- end -->

          <td>
            <div class="form-group">
              <input type="text" name="sku[]" class="form-control item_sku" id="item_sku" placeholder="Sku" required>
            </div>
          </td>

          <td>
            <div class="form-group">
              <input type="text" name="item_qty[]" class="form-control easyitemautocomplete item_qty" id="item_qtymxp" placeholder="Quantity" required>
            </div>
          </td>

          <td>
            <div class="form-group">
              <input type="text" name="item_price[]" class="form-control item_price" readonly="true" placeholder="Price" required>
              <!-- readonly -->
            </div>
          </td>
          <td></td>
        </tr>
      </tbody>
    </table>
   </div>

    <div class="form-group button_add pull-left" style="margin-top: 20px;margin-bottom: 20px; ">
      <button type="submit" class="btn btn-success" id="add"><i class="fa fa-copy" style="padding-right: 5px;"></i>Copy Item</button>
      <button type="submit" class="btn btn-success" id="order_copy"><i class="fa fa-plus" style="padding-right: 5px;"></i>Add New Item</button>
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>
@endsection

@section('LoadScript')
  <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/custom.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/scripts/date_compare/booking.js') }}"></script>
@stop
