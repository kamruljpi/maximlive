@extends('layouts.dashboard')
@section('page_heading','Add a new Purchase Order')
@section('section')

	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group ">
					<a href="{{ Route('purchase_order_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
					<i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-md-offset-0">

				@if(count($errors) > 0)
				    <div class="alert alert-danger" role="alert">
				        @foreach($errors->all() as $error)
				          <li><span>{{ $error }}</span></li>
				        @endforeach
				    </div>
				@endif
				
				<div class="panel panel-default custom">

					<div class="panel-heading">ADD</div>

					<form action="{{ Route('purchase_order_store_action')}}" method="POST">
						{{csrf_field()}}

						<div class="panel-body">
							<div class="col-sm-10">
								<div class="form-group">
									<label class="col-sm-4 date-label"><span style="float: right;">Date</span></label>
									<div class="col-sm-6">
										<input type="date" name="order_date" class="form-control" value="{{ old('order_date')}}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 date-label"><span style="float: right;">Purchase Order No</span></label>
									<div class="col-sm-6">
										<input type="text" name="purchase_order_no" class="form-control" placeholder="P-O # 00001" value="{{ old('purchase_order_no')}}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 date-label"><span style="float: right;">Description</span></label>
									<div class="col-sm-6">
										<textarea name="description" class="form-control" style="width: 90%;"> {{old('description')}}</textarea>
									</div>
								</div>
							</div>

							<div style="clear:both;padding-bottom: 5px;"></div>

							<div class="add_new_field">
								<table class="table table-bordered" id="copy_table">
									<thead>
										<th>Product</th>
										<th>Quantity</th>
										<th>Action</th>
									</thead>
									<tbody class="idclone">
										<tr class="tr_clone">
											<td width="50%">
												<div class="form-group item_code_parent">
													<input type="hidden" name="raw_item_id[]" class="raw_item_id" >
													<input type="text" name="item_code[]" class="form-control raw_item_code" placeholder="Item Code">
												</div>
											</td>
											<td>
												<div class="form-group">
													<input type="number" name="item_qty[]" class="form-control item_qty" placeholder="Qty">
												</div>
											</td>
											<td><button class="btn btn-danger remove_field" disabled="">X</button></td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="form-group">
								<button class="btn btn-primary" style="float: right;padding-left: 5px;"><i class="fa fa-plus" style="padding-right: 5px;"></i>Submit</button>
							</div>
							<div class="form-group">
								<button type="button" class="btn btn-danger" style="float: right;" id="add_new_field"><i class="fa fa-plus" style="padding-right: 5px;"></i>Add New</button>
							</div>					
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('LoadScript')
    <script src="{{ asset('assets/scripts/purchase/purchase.js') }}"></script>
@endsection
