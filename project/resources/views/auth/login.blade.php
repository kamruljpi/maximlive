@extends('layouts.app')
@section('content')
<div class="container-fluid">
	@if(Session::has('message'))
	        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('message') ))
	@endif
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default" style="margin-top: 80px;">
				<div class="panel-heading">{{ trans('others.login_label') }}</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>{{ trans('others.validationerror_there_were_some_problems_with_your_input') }}<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
								<li>{{ trans('others.validationerror_or_you_are_not_active_yet') }}</li>
							</ul>
						</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="{{ Route('login') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">User Name</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ trans('others.enter_email_address') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ trans('others.enter_password') }}</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password" placeholder="Enter Password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember">
										{{ trans('others.login_rememberme_label') }}
									</label>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;background-color: #e0007c;
    border-color: #e0007c;">
									{{ trans('others.login_label') }}
								</button>

								<a href="">{{ trans('others.forgot_your_password') }}</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Footer -->
<footer style="padding: 10px 0;background: #e0e0e0;color: #333;margin-top: 150px;border-top: 1px solid #C4C4C4;border-radius: 4px;">

      <!-- Copyright -->
    <div class="text-center" style="font-family:roboto-serif;font-size: 15px;">Copyright© 2018,All Right Reserved – by <a href="#">MAXIM</a> 
        Powered by <a href="http://maxproit.solutions/"> maxproIT.solutions</a>
    </div>
      <!-- Copyright -->

</footer>
<!-- Footer -->
@endsection
