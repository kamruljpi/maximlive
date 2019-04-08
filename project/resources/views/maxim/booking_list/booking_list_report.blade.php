@extends('layouts.dashboard')
@section('page_heading', "CS Tracking Report List")
@section('section')
    <?php 
    //	 print_r("<pre>");
    //	 print_r($bookingList);
    //	 print_r("</pre>");
    ?>
    <?php
        use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
        use App\Http\Controllers\taskController\Flugs\Mrf\MrfFlugs;
        use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
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
    </style>

	@if(Session::has('empty_booking_data'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_booking_data') ))
    @endif
    
    <div id="booking_simple_search_form">
        <div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
            <form action="{{Route('booking_advance_search_list')}}" method="POST">
                {{csrf_field()}}
                <input type="text" name="booking_id" class="form-control" placeholder="Booking No." value="{{$inputArray['booking_id']}}">
                <button class="btn btn-info " type="submit">Search</button>
            </form>
        </div>
        <button class="btn btn-primary " type="button" id="booking_advanc_search">Advance Search</button>
        <a href="{{Route('booking_list_report')}}" class="btn btn-warning">Reset</a>
    </div>

    <div>
        <form action="{{Route('booking_advance_search_list')}}" method="POST" class="hidden advance_form">
            {{csrf_field()}}

            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order Date From</label>
                    <input type="date" name="from_oder_date_search" class="form-control" id="from_oder_date_search" value="{{$inputArray['from_oder_date']}}">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order Date To</label>
                    <input type="date" name="to_oder_date_search" class="form-control" id="to_oder_date_search" value="{{$inputArray['to_oder_date']}}">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment Date From</label>
                    <input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search" value="{{$inputArray['from_shipment_date']}}">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment Date To</label>
                    <input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search" value="{{$inputArray['to_shipment_date']}}">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Buyer Name</label>
                    <input type="text" name="buyer_name_search" class="form-control" placeholder="Buyer Name" id="buyer_name_search" value="{{$inputArray['buyer_name']}}">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Vendor Name</label>
                    <input type="text" name="company_name_search" class="form-control" placeholder="Vendor Name" id="company_name_search" value="{{$inputArray['company_name']}}">
                </div>
                <!-- <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Attention</label>
                    <input type="text" name="attention_search" class="form-control" placeholder="Attention search" id="attention_search">
                </div> -->
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Po/Cat No.</label>
                    <input type="text" name="po_cat_no" class="form-control" placeholder="PO/Cat No." value="{{$inputArray['po_cat_no']}}">
                </div>
                <br>
                <div class="col-sm-3">
                    {{-- <input class="btn btn-info click_preloder" type="submit" value="Search" name="booking_advanceSearch_btn"> --}}
                    <button type="submit" class="btn btn-info form-control">Search</button>
                </div>
            </div>       
        </form>

        <div class="hidden" id="booking_simple_search_btn">
            <button class="btn btn-primary" type="button" id="">Simple Search</button>

            <a href="{{Route('booking_list_report')}}" class="btn btn-warning">Reset</a>
        </div>
    </div>

    <br>

    <div class="booking_report_details_view" id="booking_report_details_view"></div>

    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <form method="post" action="{{ URL('tracking/export') }}" enctype="multipart/form-data" >
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <div class="table-responsive" style="max-width: 100%; max-height: 500px;overflow: auto;">	
                    <table class="table table-bordered vi_table" style="min-width: 600px;" >
                        <thead>
                        <tr>
                            <th>Job No.</th>
                            <th>Category</th>
                            <th>Order Status</th>
                            <th>Buyer Name</th>
                            <th style="width:50% !important;">Vendor Name</th>
                            <th>Attention</th>
                            <th>Booking No.</th>
                            <th>PO/CAT No.</th>
                            <th>PI No.</th>
                            <th>Challan No.</th>
                            <th>PO No.</th>
                            <th>MRF No.</th>
                            <th>Order Date</th>
                            <th>Requested Shipment Date</th>
                            <th>Item Code</th>
                             <th id="item_size">Item Size</th>
                            <th width="">ERP Code</th>
                            <th>Size Range</th>
                            <th>Item Description</th>
                            <th>Sku</th>
                            <th>Quantity</th>
                            <th>Price/Pcs.</th>
                            <th>Total Price</th>
                        </tr>
                        </thead>
                        <?php
                            $j = 1 ;
                            $ccc = 0;
                            $fullTotalAmount = 0;
                        ?>
                        <tbody id="booking_list_tbody">
                            @if($bookingList[0]->booking_order_id)
                                @foreach($bookingList as $value)
                                    <?php $TotalAmount = 0; ?>
                                    @foreach($value->itemLists as $valuelist)
                                        <?php
                                            $idstrcount = (JobIdFlugs::JOBID_LENGTH - strlen($valuelist->id));
                                            
                                            $str = str_replace('$', '', $valuelist->item_price);
                                            $str_item_price = trim($str, '$');

                                            $TotalAmount += $valuelist->item_quantity*$str_item_price;
                                            $fullTotalAmount += $valuelist->item_quantity*$str_item_price;
                                        ?>
                                        <tr id="booking_list_table">
                                            <td><input name="job_id[]" value="{{ str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount) }}{{ $valuelist->id }}" hidden> {{ str_repeat(JobIdFlugs::STR_REPEAT,$idstrcount) }}{{ $valuelist->id }}</td>
                                            <td><input name="category[]" value=" {{ucfirst(str_replace('_',' ',$value->booking_category))}}" hidden>{{ucfirst(str_replace('_',' ',$value->booking_category))}}</td>
                                            <td>
                                                @if($value->booking_status == BookingFulgs::BOOKED_FLUG)

                                                    <input type="hidden" name="order_status[]" value="Booked" >

                                                    <span class="{{ $value->booking_status }}">{{ 'Booked' }}</span>

                                                @elseif($value->booking_status == BookingFulgs::ON_HOLD_FLUG)
                                                    <input type="hidden" name="order_status[]" value="{{ $value->booking_status }}" >

                                                    <span class="{{ $value->booking_status }}">Hold</span>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG ) && ($valuelist->mrf->mrf_status == '') && ($valuelist->ipo->ipo_status == ''))
                                                    <input type="hidden" name="order_status[]" value="Processing" > 
                                                 
                                                    <span class="{{ $value->booking_status }}">Process</span>
                                                                                
                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->mrf->mrf_status == MrfFlugs::OPEN_MRF))

                                                    <input type="hidden" name="order_status[]" value="Mrf Issued" >

                                                    <p class="{{ $value->booking_status }}">Mrf Issued</p>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->ipo->ipo_status== MrfFlugs::OPEN_MRF))

                                                    <input type="hidden" name="order_status[]" value="Ipo Issued" >

                                                    <p class="{{ $value->booking_status }}">Ipo Issued</p>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->mrf->mrf_status == MrfFlugs::ACCEPT_MRF) && ($valuelist->mrf->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_WAITING_FOR_GOODS) )

                                                    <input type="hidden" name="order_status[]" value="Processed to supplier">

                                                    <p class="{{ $value->booking_status }}">Processed to supplier</p>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->mrf->mrf_status == MrfFlugs::ACCEPT_MRF) && ($valuelist->mrf->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_PARTIAL_GOODS_RECEIVE) )

                                                    <input type="hidden" name="order_status[]" value="Partials Goods Receives">

                                                    <p class="{{ $value->booking_status }}">Partials Goods Receives</p>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->mrf->mrf_status == MrfFlugs::ACCEPT_MRF) && ($valuelist->mrf->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_GOODS_RECEIVE) )

                                                    <input type="hidden" name="order_status[]" value="Goods Receives">

                                                    <p class="{{ $value->booking_status }}">Goods Receives</p>

                                                @elseif( ($value->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG) && ($valuelist->mrf->mrf_status == MrfFlugs::ACCEPT_MRF) && ($valuelist->mrf->job_id_current_status == MrfFlugs::JOBID_CURRENT_STATUS_ACCEPT) )
                                                
                                                    <input type="hidden" name="order_status[]" value="Mrf Accepted" >

                                                    <p class="{{ $value->booking_status }}">Mrf Accepted</p>
                                                 
                                                @else
                                                    <input type="hidden" name="order_status[]" value="{{$value->booking_status}}" >
                                                    <span class="{{ $value->booking_status }}">{{ $value->booking_status }} </span>    
                                                @endif
                                            </td>
                                            <td><input name="buyer_name[]" value="{{$value->buyer_name}}" hidden>{{$value->buyer_name}}</td>
                                            <td><input name="company_name[]" value="{{$value->Company_name}}" hidden>{{$value->Company_name}}</td>
                                            <td><input name="attention_invoice[]" value="{{$value->attention_invoice}}" hidden>{{$value->attention_invoice}}</td>
                                            <td><input name="booking_order_id[]" value="{{$value->booking_order_id}}" hidden>{{$value->booking_order_id}}</td>
                                            <td><input name="po_cat_no[]" value="{{$valuelist->poCatNo}}" hidden>{{$valuelist->poCatNo}}</td>
                                            <td><input name="p_ids[]" value="{{$valuelist->pi->p_ids}}" hidden>{{$valuelist->pi->p_ids}}</td>
                                            <td><input name="challan_ids[]" value="{{$valuelist->challan->challan_ids}}" hidden>{{$valuelist->challan->challan_ids}}</td>
                                           <td>
                                                <input name="ipo_ids[]" value="{{$valuelist->ipo->ipo_ids}}" type="hidden">
                                                {{ (($valuelist->ipo->ipo_ids != '')? $valuelist->ipo->ipo_ids : (($valuelist->mrf->mrf_ids != '')?'N/A':'')) }}
                                            </td>
                                            <td>
                                                <input name="mrf_ids[]" value="{{$valuelist->mrf->mrf_ids}}" type="hidden">
                                                {{ (($valuelist->mrf->mrf_ids != '')? $valuelist->mrf->mrf_ids : (($valuelist->ipo->ipo_ids != '')?'N/A': '')) }}
                                            </td>
                                            <td>
                                                <input name="order_date[]" value="{{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}" type="hidden">
                                                {{Carbon\Carbon::parse($value->created_at)->format('d-m-Y')}}
                                            </td>
                                            <td>
                                                <input name="requested_date[]" value="{{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}" type="hidden">
                                                {{Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')}}
                                            </td>
                                            <td>
                                                <input name="item_code[]" value="{{$valuelist->item_code}}" type="hidden">{{$valuelist->item_code}}
                                            </td>
                                            <td>
                                                <input name="item_size[]" value="{{$valuelist->item_size_width_height}}" type="hidden">
                                                {{ ($valuelist->item_size_width_height!= '')? '('. $valuelist->item_size_width_height .')mm': 'N/A' }}
                                            </td>
                                            <td>
                                                <input name="erp_code[]" value="{{$valuelist->erp_code}}" type="hidden">{{$valuelist->erp_code}}
                                            </td>
                                            <td>
                                                <input name="size_range[]" value="{{$valuelist->item_size}}" type="hidden">{{$valuelist->item_size}}
                                            </td>
                                            <td><input name="item_description[]" value="{{$valuelist->item_description}}" hidden>{{$valuelist->item_description}}</td>

                                            <td><input name="sku[]" value="{{$valuelist->sku}}" hidden>{{$valuelist->sku}}</td>
                                            <td><input name="item_quantity[]" value="{{$valuelist->item_quantity}}" hidden>{{$valuelist->item_quantity}}</td>
                                            <td><input name="item_price[]" value="{{$str_item_price}}" hidden>{{(strtolower($valuelist->item_price) != 'n/a'? '$'.$valuelist->item_price : $valuelist->item_price)}}</td>
                                            <td><input name="total_price[]" value="{{ $valuelist->item_quantity*$str_item_price }}" hidden>${{ $valuelist->item_quantity*$str_item_price }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="23">
                                        <center>Empty value</center>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="20"></td>
                                <td colspan="2"><strong style="font-size: 12px;">Total Price:</strong></td>
                                <td><strong><input name="total" value="{{ round($fullTotalAmount,2) }}" hidden>${{ round($fullTotalAmount,2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="booking_list_pagination">{{$bookingList->links()}}</div>
                <div class="pagination-container">
                    <nav>
                        <ul class="pagination"></ul>
                    </nav>
                </div>

                <input type="hidden" name="type" value="cs">
                <button class="btn btn-primary pull-right">Export as Excel</button>
                <div class="col-md-12" style="margin-bottom: 50px;"></div>
            </form>
        </div>
    </div>
@endsection

@section('LoadScript')

    @if(!empty($inputArray['buyer_name'])  || !empty($inputArray['company_name']) || !empty($inputArray['from_oder_date']) || !empty($inputArray['to_oder_date']) || !empty($inputArray['from_shipment_date']) || !empty($inputArray['to_shipment_date']) || !empty($inputArray['po_cat_no']))

        <script type="text/javascript">
            $('.advance_form').removeClass('hidden');
            $('#booking_simple_search_btn').removeClass('hidden');
            $('#booking_advanc_search').hide();
            $('#booking_simple_search_form').hide();    
        </script>

    @endif
@endsection
