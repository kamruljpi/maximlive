@extends('layouts.dashboard')
@section('page_heading', 'Notifications' )
@section('section')
<?php
  //print_r('<pre>');
  //print_r($not['booking']);die();
?> 
	<div class="row">
		<div class="col-md-12"></div>

		<div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default panel-table">
              <div class="panel-heading">
                <div class="row">
                  <div class="col col-xs-6">
                    <h3 class="panel-title">Notifications List</h3>
                  </div>
                  <div class="col col-xs-6 text-right">
                    
                  </div>
                </div>
              </div>
              <div class="panel-body">
                <table class="table table-striped table-bordered table-list">
                  <thead>
                    <tr>
                        {{-- <th><em class="fa fa-cog"></em></th> --}}
                        <th class="hidden-xs">Serial</th>
                        <th>Descriptions</th>
                        <th>Time</th>
                    </tr> 
                  </thead>
                  <tbody>
                        <?php
                            $k= 1;
                            ?>
                        @foreach($not as $key => $nots)
                            @foreach($nots as $noti)
                            <tr>
                              
                              <td class="hidden-xs">{{ $k++ }}</td>
                              <td>{{ $noti->type_id }} Created 
                              </td>
                              <td><span class="pull-left text-bold small">{{ $noti->created_at->diffForHumans() }}</span></td>
                            </tr>
                          @endforeach
                        @endforeach 
                  </tbody>
                </table>
            
              </div>

            </div>

		</div>

	</div>
@endsection