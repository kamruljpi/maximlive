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
        @if(count( $errors ) > 0)
            @foreach ($errors->all() as $error)
                <div><p class="text-danger">{{ $error }}</p></div>
            @endforeach
        @endif
		@if(Session::has('store'))
			@include('widgets.alert', array('class'=>'success', 'message'=> Session::get('store') ))
		@endif

		<div class="row">
			<div class="col-md-12 col-md-offset-0">
				<div class="panel panel-default custom">

					<div class="panel-heading">ADD</div>

					<form action="{{ Route('final_product_store_action')}}" method="POST">
						{{csrf_field()}}

						<div class="panel-body">
							<div class="row">
							<div class="col-sm-8">
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Date</span></label>
									<div class="col-sm-6">
										<input type="date" name="order_date" class="form-control" required>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 date-label"><span>Description</span></label>
									<div class="col-sm-6">
										<textarea type="text" name="description" class="form-control"></textarea>
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
														<input type="text" id="p_item_code" name="p_item_code[]" class="form-control p_item_code" placeholder="Item Code" required>
													</div>
												</td>
												<td>
													<div class="form-group">
														<select class="form-control p_size_range" name="p_size_range[]"required>
															<option value=" ">--Select--</option>
														</select>
													</div>
												</td>
												<td>
													<div class="form-group">
														<select class="form-control p_gmt_color" name="p_gmt_color[]">
															<option value=" ">--Select--</option>
														</select>
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="p_item_qty[]" class="form-control p_item_qty" value="0" placeholder="Qty">
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
											<th>Remaining instock</th>
											<th>Action</th>
										</thead>
										<tbody class="idclone">
											<tr class="tr_clone">
												<td>
													<div class="form-group item_code_parent">
														<input type="hidden" name="raw_item_id[]" class="raw_item_id" >
														<input type="text" name="raw_item_code[]" required class="form-control raw_item_code" placeholder="Item Code">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="raw_item_qty[]" required class="form-control item_qty" placeholder="Qty" value="0">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="raw_total_stock[]" class="form-control total_instock" placeholder="Total Instock">
													</div>
												</td>
												<td>
													<div class="form-group">
														<input type="text" name="raw_item_total_price[]" class="form-control remaining_instock" placeholder="Remaining instock" readonly>
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

                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Waste Raw Materials Used :</label>
                                </div>
                                <div class="add_new_field" style="padding: 10px;">
                                    <table class="table table-bordered" id="copy_table">
                                        <thead>
                                        <th width="20%">Raw Material</th>
                                        <th>Quantity</th>
                                        <th>Total Instock</th>
                                        <th>Remaining instock</th>
                                        <th>Action</th>
                                        </thead>
                                        <tbody class="idclone new_table">
                                        <tr class="tr_clone tr_new">
                                            <td>
                                                <div class="form-group w_item_code_parent">
                                                    <input type="hidden" name="w_raw_item_id[]" class="w_raw_item_id" >
                                                    <input type="text" name="w_raw_item_code[]" required class="form-control w_raw_item_code" placeholder="Item Code">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="w_raw_item_qty[]" required class="form-control w_item_qty" placeholder="Qty" value="0">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="w_raw_total_stock[]" class="form-control w_total_instock" placeholder="Total Instock">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" name="w_raw_item_total_price[]" class="form-control remaining_instock" placeholder="Remaining instock" readonly>
                                                </div>
                                            </td>
                                            <td><button class="btn btn-danger remove_field" disabled="true">X</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group" style="padding-right: 10px;">
                                    <button class="btn btn-danger" type="button" style="float: right;" id="w_add_new_field">
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
{{--    <script src="{{ asset('assets/scripts/purchase/purchase.js') }}"></script>--}}
@endsection