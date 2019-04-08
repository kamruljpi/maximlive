@extends('layouts.dashboard')
@section('page_heading','Add a new Raw Item')
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
    		/*box-shadow: '' !important;*/
		}
		
	</style>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group ">
					<a href="{{ Route('raw_item_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
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

					<div class="panel-body">
						<form action="{{ Route('raw_item_create_action')}}" method="POST">

							{{csrf_field()}}

							<div class="col-sm-8 col-md-offset-2">
								<div class="form-group">
									<label class="col-sm-12"><span>Item Name</span></label>
									<div class="col-sm-12">
										<input type="text" name="item_name" class="form-control" placeholder="Item Name" value="{{old('item_name')}}">
									</div>
								</div>
							</div>

							<div class="col-sm-8 col-md-offset-2">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-12" style="padding: 0 !important"><span>Item Code</span> </label>
										<div class="col-sm-12" style="padding: 0 !important">
											<input type="text" name="item_code" class="form-control input_required" placeholder="Item Code" value="{{old('item_code')}}">
										</div>
									</div>								
									
									<div class="form-group">
										<label class="col-sm-12" style="padding: 0 !important"><span>Per Unit Price</span></label>
										<div class="col-sm-12" style="padding: 0 !important">
											<input type="text" name="price" class="form-control" placeholder="Price" value="{{old('price')}}">
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="col-sm-12"><span>Opening Quantity</span></label>
										<div class="col-sm-12">
											<input type="text" name="opening_qty" class="form-control" placeholder="Opening Quantity" value="{{old('opening_qty')}}" style="width: 85% !important">
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-12"><span>Status</span></label>
										<div class="col-sm-12">
											<select class="form-control" name="is_active" style="width: 85% !important">
												<option value="1">Active</option>
												<option value="0">Inactive</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-8 col-md-offset-2">
								<div class="form-group">
									<label class="col-sm-12"><span>Sort Description</span></label>
									<div class="col-sm-11">
										<textarea name="sort_description" class="form-control" placeholder="Write here...">						
										</textarea>
									</div>
								</div>
							</div>

							<div class="col-sm-2 col-sm-offset-7" style="padding-top: 10px !important">
								<div class="form-group">
									<button class="form-control btn btn-primary">Add Item</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
