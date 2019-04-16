@extends('layouts.dashboard')
@section('page_heading', trans("others.add_product_size_label") )
@section('section')

@section('section')
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-2">
			<div class="form-group ">
				<a href="{{ URL::previous() }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
				<i class="fa fa-arrow-left"></i> Back</a>
			</div>
		</div>
	</div>
	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">{{trans('others.add_size_label')}}</div>
				<div class="panel-body">
		            
					<form class="form-horizontal" role="form" method="POST" action="{{ Route('create_size_action') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">							

					
					<div class="col-md-12">

						<!-- <div class="form-group col-md-6">
							<label class="col-md-4 control-label">{{trans('others.product_code_label')}}</label>
							<div class="col-md-8">
								<select class="form-control" type="select" name="p_code" >
									<option value="{{old('p_code')}}">{{old('p_code')}}</option>
									@foreach($products as $product)
									
									<option value="{{$product->product_code}}">{{$product->product_code}}</option>
									@endforeach
								</select>
							</div>
						</div>	 -->

						<div class="form-group col-md-6">
							<label class="col-md-4 control-label">{{trans('others.product_size_label')}}</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="p_size" value="{{ old('p_size')  }}">
							</div>
						</div>
						<div class="col-md-3">
								<div class="select">
									<select class="form-control" type="select" name="isActive" >
										<option  value="1" name="isActive" >{{ trans("others.action_active_label") }}</option>
										<option value="0" name="isActive" >{{ trans("others.action_inactive_label") }}</option>
								    </select>
								</div>
							</div>
					
							<div class="col-md-3">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									{{trans('others.save_button')}}
								</button>
							</div>
					</div>
					

							
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

