@extends('layouts.dashboard')
@section('page_heading', trans("Draft List") )
@section('section')

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
					<tr></tr>
				</head>
				<tbody>
					<tr></tr>
				</tbody>
			</table>
			<div id=""></div>
			<div class="pagination-container">
				<nav>
					<ul class="pagination"></ul>
				</nav>
			</div>
		</div>
	</div>
@endsection