@extends('layouts.dashboard')
@section('page_heading','Purchase Order')
@section('section')
    <?php 
        use App\Http\Controllers\Source\User\RoleDefine;
        use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
        use App\Http\Controllers\Purchase\PurchaseFlugs;

        $object = new RoleDefine();
        $role_check_planning = $object->getRole('Planning');
        $role_check_purchase = $object->getRole('Purchase');

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
        <a href="{{ Route('purchase_order_create_view') }}" class="btn btn-success form-control" style="font-weight: bold;">
        New Purchase Order</a>
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
        	<div class="col-sm-1"></div>
    		<div class="col-sm-10">
            	<table class="table table-bordered">
	                <thead>
	                    <tr>
	                    	<th>Sr#</th>
                            <th>User</th>
                            <th>Purchase Order No</th>
                            <th>Created Date</th>
                            <th>Status</th>
	                        <th>Manage</th>
	                    </tr>
	                </thead>
                    <tbody>
                        @if(count($details) > 0)
                            @php($j=1 + $details->perPage() * ($details->currentPage() - 1))
                            @foreach($details as $detail)                  
                                <tr>                        	
                                	<td>{{$j++}}</td>
                                    <td>{{$detail->created_user_name}}</td> 	            	
                                    <td>{{$detail->purchase_order_no}}</td>
                                    <?php 
                                        $str_date = str_replace('/', '-', $detail->created_at);
                                        $created_at = new DateTime($str_date, new DateTimezone('Asia/Dhaka'));
                                    ?>                   
                                    <td>{{$created_at->format('d-m-Y, g:i a')}}</td>
                                    <td>
                                        @if($detail->is_rejected == BookingFulgs::IS_REJECTED)
                                            Rejected
                                        @else
                                            @if($detail->status == PurchaseFlugs::PURCHASE_FROM_PURCHASE_ORDER)
                                                Order Accepted
                                            @else
                                                New Order
                                            @endif
                                        @endif
                                    </td>                     
                                	<td width="23%">

                                        @if($role_check_planning == 'planning')
                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_delete_action')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-danger deleteButton" title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                            </div>

                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_report_view')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-success" title="Show Report">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        @elseif($role_check_purchase == 'purchase')

                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_edit_view')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-info" title="Accept">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            </div>

                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_reject_action',['id' => $detail->id_purchase_order_wh])}}" class="btn btn-primary" title="Reject">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            </div>
                                        @elseif(Auth::user()->type = 'super_admin')
                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_delete_action')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-danger deleteButton" title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                            </div>

                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_report_view')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-success" title="Show Report">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_edit_view')}}/{{$detail->id_purchase_order_wh}}" class="btn btn-info" title="Accept">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            </div>

                                            <div style="padding: 1px; float: left;">
                                                <a href="{{Route('purchase_order_reject_action',['id' => $detail->id_purchase_order_wh])}}" class="btn btn-primary" title="Reject">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            </div>
                                        @endif                                        
                                	</td>
                                </tr>                    
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">
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
            <div class="col-sm-1"></div>
        </div>
    </div>
@endsection