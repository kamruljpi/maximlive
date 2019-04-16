@extends('layouts.plane')
@section('body')
<?php 
    use App\Http\Controllers\taskController\Flugs\JobIdFlugs;
    use App\Http\Controllers\Source\User\RoleDefine;
    use App\Notification;
    $object = new RoleDefine();
    $csRoleCheck = $object->getRole('Customer');
    $os_team = $object->getRole('OS');

    // foreach (session()->get('notification') as $values) {
        // foreach ($values as $key => $value) {
            // if($value->type == Notification::CREATE_SPO){
                // print_r("<pre>");
                // print_r($value);
            // }
       //  }
    // }
     
     // print_r("<pre>");die();
?>
 <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route ('dashboard_view') }}">
                <!-- {{ trans('others.company_name')}} -->
                <img src="{{asset('assets/img/logo.png')}}" width="180" height="">
            </a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <div class="">
                {{-- <li class="dropdown ">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>John Smith</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>

                                        <div>
                                        @include('widgets.progress', array('animated'=> true, 'class'=>'success', 'value'=>'40'))
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>

                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 2</strong>
                                        <span class="pull-right text-muted">20% Complete</span>
                                    </p>

                                        <div>
                                        @include('widgets.progress', array('animated'=> true, 'class'=>'info', 'value'=>'20'))
                                            <span class="sr-only">20% Complete</span>
                                        </div>

                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 3</strong>
                                        <span class="pull-right text-muted">60% Complete</span>
                                    </p>

                                        <div>
                                        @include('widgets.progress', array('animated'=> true, 'class'=>'warning', 'value'=>'60'))
                                            <span class="sr-only">60% Complete (warning)</span>
                                        </div>

                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 4</strong>
                                        <span class="pull-right text-muted">80% Complete</span>
                                    </p>

                                        <div>
                                        @include('widgets.progress', array('animated'=> true,'class'=>'danger', 'value'=>'80'))
                                            <span class="sr-only">80% Complete (danger)</span>
                                        </div>

                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Tasks</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li> --}}
                <!-- /.dropdown -->
                 <style type="text/css">
                    .badge-notify{
                       background:red;
                       position:relative;
                       top: -14px;
                       left: -43px;
                      }
                </style>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <span class="badge badge-notify" id="badge"></span>
                    
                    <ul class="dropdown-menu dropdown-alerts">
               
                        <?php
                            $i=0;
                            $k= 0;
                            ?>
                        @foreach(session()->get('notification') as $key => $nots)
                            @foreach($nots as $noti)
                                <li class="{{ ($noti->seen == 1)? 'seen' : 'unseen' }}">
                                    @if($noti)
                                        <a href="
                                            @if( $noti->type == Notification::CREATE_BOOKING )
                                                {{ Route('booking_list_details_view',['booking_id' => $noti->type_id]) }}
                                            @elseif($noti->type == Notification::CREATE_MRF )
                                                {{ Route('os_mrf_details_view',['mid' => $noti->type_id]) }}
                                            @elseif($noti->type == Notification::CREATE_SPO )
                                                {{ Route('os_po_report_view',['poid' => $noti->type_id]) }}
                                            @else 
                                                # 
                                            @endif
                                        ">
                                            <div style="font-size: 12px">
                                                @if($noti->type == Notification::GOODS_RECEIVE )
                                                <?php $jobId = (JobIdFlugs::JOBID_LENGTH - strlen($noti->type_id)); ?>
                                                    {{ str_repeat(JobIdFlugs::STR_REPEAT,$jobId) }}{{ $noti->type_id }} Job id goods receive.
                                                @else
                                                    {{ $noti->type_id }} Created 
                                                @endif

                                                <span class="pull-right text-muted small">{{ $noti->created_at->diffForHumans() }}</span>
                                            </div>
                                        </a>
                                        @if($noti->seen == '0')
                                            <?php $k++;?>
                                        @endif
                                    
                                    @endif
                                </li>                                
                                
                                <?php
                                    $i++;
                                ?>
                            @endforeach
                        @endforeach
                        <li class="divider"></li>   
                        <li>
                            <a class="text-center" href="{{ Route('getNotification') }}">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                                <input type="text" name="badge" value="{{ $k++ }}" hidden>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{{ Route('user_profile_view') }}"><i class="fa fa-user fa-fw"></i>

                            {{ Auth::user()->first_name }}


                         </a>
                        </li>

                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>

                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>

                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
            </div>
                <!-- /.dropdown -->
                
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

             <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        @if($csRoleCheck == 'customer' 
                            || session::get('user_type') == "super_admin"
                            || $os_team == 'os')

                            <li {{ (Request::is('*dashboard') ? 'class="active"' : '') }}>
                                <a href="{{ Route ('task_dashboard_view') }}">
                                    <i class="fa fa-dashboard fa-fw"></i>
                                    {{ trans('others.task_label') }}
                                </a>
                            </li>

                        @endif

                        @if(!is_array(session()->get('UserMenus')) || is_object(session()->get('UserMenus')) )
                                <script type="text/javascript">
                                    window.location = "{{ url('/dashboard') }}";
                                </script>
                        @else

                                @foreach(session()->get('UserMenus') as $sl=>$menu)
                                <li>
                                    <a href="#"><i class="fa fa-cogs"></i>  {{$menu['name']}}<span class="fa arrow"></span></a>
                                    <ul class="nav nav-second-level">

                                        @if($menu['subMenu'])
                                            @foreach($menu['subMenu'] as $sl=>$sub_menu)
                                            @php
                                                $explode_route = explode("_",$sub_menu['route_name']);
                                                $action_route = $explode_route[count($explode_route)-1];
                                            @endphp

                                                    @if($action_route!='action')
                                                        <li class="{{ (Route::current()->getName() == $sub_menu['route_name']) ? 'active' : '' }}">
                                                            <a href="{{ ($sub_menu['route_name'])? Route($sub_menu['route_name']):'' }}">{{$sub_menu['name']}}</a> </li>
                                                    @endif

                                            @endforeach
                                            <!-- /.nav-second-level -->
                                        </li>
                                    @endif
                                    </ul>
                                    <!-- /.nav-second-level -->
                                </li>
                                @endforeach

                        @endif




                        {{-- <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Second Level Item</a>
                                </li>
                                <li>
                                    <a href="#">Second Level Item</a>
                                </li>
                                <li>
                                    <a href="#">Third Level <span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                        <li>
                                            <a href="#">Third Level Item</a>
                                        </li>
                                    </ul>
                                    <!-- /.nav-third-level -->
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li> --}}

                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
			 <div class="row">
                <div class="col-sm-6">
                    <h3 class="page-header">@yield('page_heading')</h3>
                </div>
                <div class="col-sm-6">
                    <h3 class="page-header pull-right">@yield('page_heading_right')</h3>
                </div>
                <!-- /.col-lg-12 -->
           </div>
			<div class="row">
				@yield('section')

            </div>
            <!-- /#page-wrapper -->
        </div>
    </div>
    <!-- Footer -->
    <footer style="padding: 10px 0;background:#e0e0e0;color:#333; margin-left: 250px;">

          <!-- Copyright -->
        <div class="text-center" style="font-family:roboto-serif;font-size: 15px;">Copyright© 2018,All Right Reserved – by <a href="#" target="_blank">MAXIM</a> 
            Powered by <a href="http://maxproit.solutions/" target="_blank"> maxproIT.solutions</a>
        </div>
          <!-- Copyright -->

    </footer>
    <!-- Footer -->
@stop

@section('LoadScript')
    <script>
    $(document).ready(function() {
        var b = $('input[name=badge]').val();
        if (b > 0) {
            $('#badge').text(b);
        }
    });
    </script>
@stop

