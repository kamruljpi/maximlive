@extends('layouts.dashboard')
@section('page_heading', 'Zone List')
@section('section')
    <style type="text/css">
        .top-btn-pro{
            padding-bottom: 15px;
        }
        .td-pad{
            padding-left: 15px;
        }
    </style>

    @if(Session::has('zone'))
        @include('widgets.alert', array('class'=>'success', 'message'=> Session::get('zone') ))
    @endif
    @if(Session::has('delete'))
        @include('widgets.alert', array('class'=>'danger', 'message'=> Session::get('zone') ))
    @endif

    <div class="col-sm-1 top-btn-pro pull-right">
        <a href="{{ Route('zone_add_view') }}" class="btn btn-success form-control">
            <i class="fa fa-plus-circle"></i></a>
    </div>



    <div class="col-sm-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="vendor_tbody">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Zone Name</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($zonelist as $value)
                    <tr>
                        <td>{{$value->zone_id}}</td>
                        <td>{{$value->zone_name}}</td>
                        <td>{{$value->location_name}}</td>
                        <td>
                            {{($value->status == 1)? 'Active' : 'Inactive'}}
                        </td>

                        <td>
                            <a href="{{ Route('zone_view', ['zone_id' => $value->zone_id]) }}" class="btn btn-success"><i class="fa fa-eye"></i></a>
                            <a href="{{ Route('zone_delete', ['zone_id' => $value->zone_id])}}" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

        </div>
    </div>
@stop
@section('LoadScript')
    <script type="text/javascript" src="{{asset('assets/scripts/vendor/search.js')}}"></script>
@stop