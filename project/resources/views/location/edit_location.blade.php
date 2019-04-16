@extends('layouts.dashboard')
@section('page_heading','Edit Location')
@section('section')
	<div class="container-fluid">		
	    <div class="row">
	        <div class="col-sm-2">
	            <div class="form-group ">
	                <a href="{{ Route('location_list_view') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
	                <i class="fa fa-arrow-left"></i> Back</a>
	            </div>
	        </div>
	    </div>

	    <div class="row">
	        <div class="col-md-12 col-sm-12">
	            @if(count($errors) > 0)
	                <div class="alert alert-danger" role="alert">
	                    @foreach($errors->all() as $error)
	                      <li><span>{{ $error }}</span></li>
	                    @endforeach
	                </div>
	            @endif
	            <div class="panel panel-default">
	                <div class="panel-heading">Edit Location</div>
	                <div class="panel-body">
	                    <form class="form-horizontal" action="{{ Route('location_update_action',$details->id_location) }}" role="form" method="POST" >

	                        {{csrf_field()}}

	                        <div class="row">
	                            <div style="" class="col-md-12 col-sm-12 ">
	                                <div class="form-group">
	                                    <label class="col-md-5 col-sm-5 control-label">Location</label>
	                                    <div class="col-md-6 col-sm-6">
	                                        <input type="text" class="form-control input_required" name="location" value="{{ $details->location }}" placeholder="Location">
	                                    </div>
	                                </div>                      

	                                <div class="form-group">
	                                  <label class="col-md-5 col-sm-5 control-label">{{ trans('others.header_status_label') }}</label>
	                                  <div class="col-md-6 col-sm-6">
	                                      <select class="form-control" id="sel1" name="status">
	                                        <option value="1" {{(($details->status == 1) ? 'selected' : '')}} >Active</option>
	                                        <option value="0" {{(($details->status == 0) ? 'selected' : '')}} >Inactive</option>
	                                      </select>
	                                  </div>
	                                </div>
	                            </div>
	                        </div>

	                        <div class="form-group">
	                            <div class="col-sm-3 col-sm-offset-7 col-xs-offset-7">
	                                <button type="submit" class="btn btn-primary form-control" style="margin-right: 15px;">
	                                    {{ trans('others.save_button') }}
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