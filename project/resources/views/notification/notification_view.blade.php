@extends('layouts.dashboard')
@section('page_heading', 'Notifications' )
@section('section')
<?php
  //print_r('<pre>');
  //print_r($not['booking']);die();
?>
<?php 
    use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
    use App\Http\Controllers\Source\User\RoleDefine;
    use App\Notification;
    $object = new RoleDefine();
    $csRoleCheck = $object->getRole('Customer');
?> 
	<div class="row">
		<div class="col-md-12"></div>

		<div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default panel-table">
              <div class="panel-heading">
                <div class="row">
                  <div class="col col-xs-8">
                    <h3 class="panel-title">Notifications List</h3>
                  </div>
                  <div class="col col-xs-4 text-right">
                      <a type="button" href="{{ Route('notification_seen') }}" class="btn btn-success hidden">Mark all as seen</a>
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
                        <th>Status</th>
                    </tr> 
                  </thead>
                  <tbody>
                        <?php
                            $k= 1;
                            ?>
                        @foreach($not as $key => $nots)
                            @foreach($nots as $noti)

                            
                              <tr class="{{ ($noti->seen == 1)? 'seen' : 'unseen' }}" >
                                
                                <td class="hidden-xs">{{ $k++ }}</td>
                                <td>
                                    <a href="
                                        @if( $noti->type == Notification::CREATE_BOOKING )
                                            {{ Route('booking_list_details_view',['booking_id' => $noti->type_id]) }}
                                        @elseif($noti->type == Notification::CREATE_MRF )
                                            {{ Route('os_mrf_details_view',['mid' => $nots[$i]->type_id]) }}
                                        @elseif($noti->type == Notification::CREATE_SPO )
                                             # 
                                        @else 
                                             # 
                                        @endif
                                    "> 
                                      @if($noti->type == Notification::GOODS_RECEIVE )
                                      <?php $jobId = (JobIdFlugs::JOBID_LENGTH - strlen($noti->type_id)); ?>
                                          {{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{ $noti->type_id }} Job id goods receive.
                                      @else
                                          {{ $noti->type_id }} Created 
                                      @endif
                                    </a> 
                                </td>
                                <td><span class="pull-left text-bold small">{{ $noti->created_at->diffForHumans() }}</span></td>
                                <td>{{ ($noti->seen == 1)? 'Seen' : 'Unseen' }}</td>
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