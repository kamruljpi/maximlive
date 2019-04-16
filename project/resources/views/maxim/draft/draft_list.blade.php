@extends('layouts.dashboard')
@section('page_heading', trans("Draft List") )
@section('section')
	
	<?php 
		// print_r("<pre>");
		// print_r($draft_list);
		// print_r("</pre>");die();
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

	<div class="row">
		<div class="col-md-12 col-md-offset-0">
			<table class="table table-bordered">
				<head>
					<tr>
						<th>#</th>
						{{-- <th>Buyer Name</th> --}}
						<th>Booking No</th>
						<th>Category</th>
						<th>Action</th>
					</tr>
				</head>
				<tbody>
					@php($j=1 + $draft_list->perPage() * ($draft_list->currentPage() - 1))
					@foreach($draft_list as $drafts)
					<tr>
						<td>{{ $j++ }}</td>
						{{-- <td>{{$value->buyer_details->buyer_name}}</td> --}}
						<td>{{ $drafts->booking_order_id }}</td>
						<td>{{ ucfirst(str_replace('_',' ',$drafts->booking_category))}}</td>
						<td>
							<a href="{{ Route('getDraft',['id' => $drafts->booking_order_id ]) }}" class="btn btn-success">Edit</a>
							<a href="{{Route('draft_delete_action',$drafts->booking_order_id)}}" class="btn btn-danger deleteButton">Delete</a>
						</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			{{$draft_list->links()}}
		</div>
	</div>
@endsection
