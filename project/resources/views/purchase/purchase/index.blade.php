@extends('layouts.dashboard')
@section('page_heading','Purchase')
@section('section')
    <?php 
        use App\Http\Controllers\Purchase\PurchaseFlugs;
    ?>
    <style type="text/css">
    	.top-btn-pro{
    		padding-bottom: 15px;
    	}
        .td-pad{
            padding-left: 15px;
        }
    </style>

    @if(Session::has('create'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('create') ))
    @endif 
    @if(Session::has('delete'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('delete') ))
    @endif
    @if(Session::has('update'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('update') ))
    @endif   

    <div class="col-sm-3 top-btn-pro">
        <a href="{{ Route('purchase_create_view') }}" class="btn btn-success form-control" style="font-weight: bold;">
        New Purchase</a>
    </div>

    <div class="col-sm-6">
        <div class="form-group custom-search-form">
            <input type="text" name="searchFld" class="form-control keyup_preloder" placeholder="Search" id="search">
            <button class="btn btn-default" type="button">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="row">
    		<div class="col-sm-12">
            	<table class="table table-bordered">
	                <thead>
	                    <tr>
	                    	<th>Sr#</th>
                            <th>From User</th>
                            <th>To User</th>
                            <th>Purchase Voucher</th>
                            <th>Total Qty</th>
                            <th>Discount</th>
                            <th>Total Price</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Date</th>
	                        <th>Manage</th>
	                    </tr>
	                </thead>
                    <tbody>
                        @if(count($details) > 0)
                            @php($j=1 + $details->perPage() * ($details->currentPage() - 1))
                            @foreach($details as $detail)                  
                                <tr>                        	
                                	<td>{{$j++}}</td>
                                    <td>{{$detail->from_user_name}}</td>
                                    <td>{{$detail->to_user_name}}</td>
                                    <td>{{$detail->purchase_voucher}}</td>
                                    <td>{{$detail->item_total_qty}}</td>
                                    <td>{{$detail->discount}}</td>
                                    <td>{{$detail->in_all_total_price}}</td>
                                    <td>{{ucfirst(str_replace('_', ' ', $detail->payment_status))}}</td>
                                    <td>
                                        @if($detail->status == PurchaseFlugs::PURCHASE)
                                            New Purchase
                                        @elseif($detail->status == PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER)
                                            Form Purchase order
                                        @endif
                                    </td>
                                    <?php 
                                        $str_date = str_replace('/', '-', $detail->created_at);
                                        $created_at = new DateTime($str_date, new DateTimezone('Asia/Dhaka'));
                                    ?>                   
                                    <td>{{$created_at->format('d-m-Y, g:i a')}}</td>
                                    
                                	<td>
                                        <a href="{{Route('purchase_show_view')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-success" title="Show">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                	</td>
                                </tr>                    
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10">
                                    <div style="text-align: center;font-size: 16px;font-weight: bold;"> Data not found.</div>
                                </td>
                            </tr>                 
                        @endif                    
                    </tbody>
                </table>
                @if(!empty($details))
                    {{$details->links()}}
                @endif
            </div>
        </div>
    </div>
@endsection