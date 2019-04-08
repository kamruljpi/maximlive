@extends('layouts.dashboard')
@section('page_heading','Add Final Product')
@section('section')
	<style type="text/css">
		div.row .custom .panel-heading {
			/*background-color: #fff !important*/
		}
		div.row .custom .panel-body .date-label span {
			float: right;margin-top: 5px;
		}
		#abcdesfd .col-sm-3,
		#abcdesfd .col-sm-2,
		.col-padding .col-sm-6,
		.pad .col-sm-3,
		.pad .col-sm-7{
			padding: 0 !important;
		}

		.table-bordered tbody tr:hover{
			box-sizing: border-box !important;
		}
		
	</style>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group ">
					<a href="{{ Route('final_production_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
					<i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="panel panel-default custom">

					<div class="panel-heading">ADD</div>

					<form action="{{ Route('purchase_store_action')}}" method="POST">
						{{csrf_field()}}

						<div class="panel-body">
							<div class="row">
							<div class="col-sm-8">
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Date</span></label>
									<div class="col-sm-6">
										<input type="date" name="order_date" class="form-control">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Select Section</span></label>
									<div class="col-sm-6">
										<select class="form-control" name="">
											<option value=" ">--Select--</option>
										</select>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Description</span></label>
									<div class="col-sm-6">
										<input type="text" name="description" class="form-control" placeholder="Description">
									</div>
								</div>
							</div>
							</div>

							<div class="row">
								<div class="col-sm-2">
									<label>Products Details :</label>									
								</div>
								<div class="add_new_field_product" style="padding: 10px;">
									<table class="table table-bordered" id="copy_table">
										<thead>
											<th width="30%">Product</th>
											<th>Size Range</th>
											<th>Color</th>
											<th>Quantity</th>
											<th>Action</th>
										</thead>
										<tbody class="idclone_product">
											<tr class="p_tr_clone">
												<td>
													<div class="form-group p_item_code_parent">
														<input type="hidden" name="p_item_id[]" class="p_item_id" >
														<input type="text" id="p_item_code" name="p_item_code[]" class="form-control p_item_code" placeholder="Item Code">
													</div>
												</td>
												<td>
													<div class="form-group">
														<select class="form-control p_size_range" name="p_size_range">
															<option value=" ">--Select--</option>
														</select>
													</div>
												</td>
												<td>
													<div class="form-group">
														<select class="form-control p_gmt_color" name="p_gmt_color">
															<option value=" ">--Select--</option>
														</select>
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="number" name="p_item_qty[]" class="form-control p_item_qty" value="0" placeholder="Qty">
													</div>
												</td>
												<td><button class="btn btn-danger remove_field" disabled="true">X</button></td>
											</tr>
										</tbody>
									</table>
								</div>
							

								<div class="form-group" style="padding-right: 10px;">
									<button class="btn btn-danger" style="float: right;" id="add_new_field_product">
										<i class="fa fa-plus" style="padding-right: 5px;"></i>
										Add New
									</button>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<label>Raw Materials Used :</label>									
								</div>
								<div class="add_new_field" style="padding: 10px;">
									<table class="table table-bordered" id="copy_table">
										<thead>
											<th width="20%">Raw Material</th>
											<th>Quantity</th>
											<th>Total Instock</th>
											<th>Remaining insto</th>
											<th>Action</th>
										</thead>
										<tbody class="idclone">
											<tr class="tr_clone">
												<td>
													<div class="form-group item_code_parent">
														<input type="hidden" name="raw_item_id[]" class="raw_item_id" >
														<input type="text" name="item_code[]" class="form-control raw_item_code" placeholder="Item Code">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="number" name="item_qty[]" class="form-control item_qty" placeholder="Qty" value="0">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="" class="form-control total_instock" placeholder="Total Instock">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="item_total_price[]" class="form-control remaining_insto" placeholder="Remaining insto" readonly>
													</div>
												</td>
												<td><button class="btn btn-danger remove_field" disabled="true">X</button></td>
											</tr>
										</tbody>
									</table>
								</div>

								<div class="form-group" style="padding-right: 10px;">
									<button class="btn btn-danger" style="float: right;" id="add_new_field">
										<i class="fa fa-plus" style="padding-right: 5px;"></i>
										Add New
									</button>
								</div>
							</div>

							<div class="col-sm-4 col-sm-offset-4">
								<div class="form-group">
									<button class="form-control btn btn-primary">Confirm Add Final Product</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('LoadScript')
    <script src="{{ asset('assets/scripts/purchase/final_product.js') }}"></script>
@endsection