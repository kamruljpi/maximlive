@extends('layouts.dashboard')
@section('page_heading','Purchase Order List')
@section('section')
	
	<div id="booking_simple_search_form">
	    <div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
	        <form action="{{Route('os_po_single_search')}}" method="POST">
	            {{csrf_field()}}
	            <input type="text" name="os_po_id" class="form-control" placeholder="SPO No." value="{{$inputArray['os_po_id']}}">
	            <button class="btn btn-info " type="submit">Search</button>
	        </form>
	    </div>
	    <button class="btn btn-primary " type="button" id="booking_advanc_search">Advance Search</button>
	    <a href="{{Route('os_po_list_view')}}" class="btn btn-warning">Reset</a>
	</div>

	<div>
	    <form action="{{Route('os_po_list_advance_search')}}" method="POST" class="hidden advance_form">
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
	                <label class="col-sm-12 label-control">Supplier Name</label>
	                <input type="text" name="supplier_name" class="form-control" placeholder="Supplier Name" id="company_name_search" value="{{$inputArray['supplier_name']}}">
	            </div>
	            <br>
	            <div class="col-sm-3">
	                <button type="submit" class="btn btn-info form-control">Search</button>
	            </div>
	        </div>

	    </form>
	    <div class="hidden" id="booking_simple_search_btn">
	        <button class="btn btn-primary" type="button" id="">Simple Search</button>
	        <a href="{{Route('os_po_list_view')}}" class="btn btn-warning">Reset</a>
	    </div>
	</div>

	<br>

	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Serial no</th>
						<th>Supplier Name</th>
						<th>MRF No.</th>
						<th>PO No.</th>
						<th>Order Date</th>
						<th>Requested Shipment Date</th>
						<th>Action</th>
					</tr>
				</thead>

				@php($j=1 + $poList->perPage() * ($poList->currentPage() - 1))
				<tbody id="mrf_list_tbody">
					@if($poList[0]->po_id)
					@foreach($poList as $value)
						<tr id="mrf_list_table">
							<td>{{$j++}}</td>
							<td>{{ $value->name }}</td>
							<td>{{$value->mrf_id}}</td>
							<td>{{$value->po_id}}</td>
							<td>{{Carbon\Carbon::parse($value->created_at)}}</td>
							<td>{{$value->shipment_date}}</td>
							<td>
								<form action="{{Route('os_po_report_view') }}" role="form" target="_blank">
									<input type="hidden" name="poid" value="{{$value->po_id}}">
									<button class="btn btn-success" target="_blank">Report</button>
								</form>
							</td>
						</tr>
					@endforeach
					@else
						<tr id="mrf_list_table">
							<td colspan="7"> <div style="text-align: center; font-weight: bold;font-size: 16px;">Data not found</div></td>
						</tr>
					@endif
				</tbody>
			</table>

			<div id="mrf_list_pagination">{{$poList->links()}}</div>
			<div class="pagination-container">
				<nav>
					<ul class="pagination"></ul>
				</nav>
			</div>

		</div>
	</div>
@endsection
@section('LoadScript')

    @if (!empty($inputArray['from_oder_date']) || !empty($inputArray['to_oder_date']) || !empty($inputArray['from_shipment_date']) || !empty($inputArray['to_shipment_date']) || !empty($inputArray['supplier_name']))

        <script type="text/javascript">
            $('.advance_form').removeClass('hidden');
            $('#booking_simple_search_btn').removeClass('hidden');
            $('#booking_advanc_search').hide();
            $('#booking_simple_search_form').hide();    
        </script>
    
    @endif
@endsection
