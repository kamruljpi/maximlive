@extends('layouts.dashboard')
@section('page_heading','Stage Update page')
@section('section')
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group ">
					<a href="{{ Route('stage_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
					<i class="fa fa-arrow-left"></i> Back</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Stage Update page</div>
					<div class="panel-body">
			            
						<form class="form-horizontal" role="form" method="POST" action="{{ Route('stage_edit_action') }}/{{$details->id_stage}}">
							{{csrf_field()}}

							@if ($errors->any())
							    <div class="alert alert-danger">
							        <ul>
							            @foreach ($errors->all() as $error)
							                <li>{{ $error }}</li>
							            @endforeach
							        </ul>
							    </div>
							@endif

							<div class="form-group">
								<label class="col-md-4 control-label">{{trans('others.buyer_name_label')}}</label>
								<div class="col-md-6">
									<input type="text" class="form-control  input_required" name="name" value="{{$details->name}}">
								</div>
							</div>

							<div class="form-group">
							  <label class="col-md-4 col-sm-4 control-label">{{ trans('others.header_status_label') }}</label>
							  <div class="col-md-6 col-sm-6">
							      <select class="form-control" id="sel1" name="is_active">
							        <option value="1" {{$details->is_active == 1 ?'selected':''}}>Active</option>
							        <option value="0" {{$details->is_active == 0 ?'selected':''}}>Inactive</option>
							      </select>
							  </div>
							</div>						
							
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
										{{trans('others.update_button')}}
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
