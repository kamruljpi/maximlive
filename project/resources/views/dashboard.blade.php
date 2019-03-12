@extends('layouts.dashboard')
@section('section')
	<div id="welcome">
		<div class="col-md-8 col-md-offset-2">
			@if(count($errors) > 0)
			    <div class="alert alert-danger" role="alert">
			        @foreach($errors->all() as $error)
			          <li><span>{{ $error }}</span></li>
			        @endforeach
			    </div>
			@endif
			<div class="panel panel-default">
			  	<div class="panel-body">
			  		<div class="header">
			  			<h3>Welcome Back <b>{{ Auth::user()->first_name }} {{Auth::user()->last_name}}</b></h3>
			  		</div>
			  		<div class="body">
			  			<h3>Welcome to <b>Maxim Order Management System!</b></h3>
			  		</div>
				</div>
			</div>
		</div>
	</div>
@stop
