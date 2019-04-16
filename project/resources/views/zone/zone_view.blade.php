@extends('layouts.dashboard')
@section('page_heading','Edit Zone')
@section('section')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group ">
                    <a href="{{ route('zone_list') }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
                        <i class="fa fa-arrow-left"></i></a>
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
                    <div class="panel-heading">Edit Zone</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ Route('zone_update_action') }}" role="form" method="POST" >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                @foreach($zone_data as $value)
                                <input type="hidden" name="zone_id" value="{{ $value->zone_id }}">
                                <div style="" class="col-md-12 col-sm-12 ">
                                    <div class="form-group">
                                        <label class="col-md-3 col-sm-3 control-label">Zone Name</label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="zone_name" value="{{ $value->zone_name }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-sm-3 control-label">Location</label>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" id="location" name="location">
                                                <option value="">Select Location</option>
                                                @foreach($location as $loc_value)
                                                    <option value="{{ $loc_value->id_location }}" {{ ($loc_value->id_location == $value->location_id)? selected : ''}}>{{ $loc_value->location }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-sm-3 control-label">{{ trans('others.header_status_label') }}</label>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" id="sel1" name="status">
                                                <option value="1" {{ ($value->status == 1)? selected : ''}}>Active</option>
                                                <option value="0" {{ ($value->status == 0)? selected : ''}}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>


                            <div class="form-group text-center">
                                <div class="col-sm-2 col-sm-offset-8 col-xs-offset-8">
                                    <button type="submit" class="btn btn-primary form-control" style="margin-right: 15px;">
                                        <i class="fa fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(".selections").select2();
    </script>
@endsection
@section("LoadScript")
    <script>
        $(document).ready(function(){
            $("#name_buyer").on('change',function(){
                $("#id_buyer").val($(this).find(':selected').attr('data-id'));
            });
        });
    </script>
@endsection