<?php $__env->startSection('page_heading', trans("others.mxp_menu_booking_list") ); ?>
<?php $__env->startSection('section'); ?>
    <?php
    use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
    $object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
    $roleCheck = $object->getRole();

//     print_r("<pre>");
//     print_r($bookingList);
//     print_r("</pre>");
    ?>
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
        .popoverOption:hover{
            text-decoration: underline;
        }
        /*.popper-content ul{
            list-style-type: none;
        }*/
    </style>
    <?php if(Session::has('empty_booking_data')): ?>
        <?php echo $__env->make('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_booking_data') ), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>

    <?php echo e($msg); ?>

    <?php if(!empty($msg)): ?>
        <div class="alert alert-success">
            <ul>
                <?php echo e($msg); ?>

            </ul>
        </div>
    <?php endif; ?>

    <?php if(Session::has('message')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php echo e(Session::get('message')); ?>

            </ul>
        </div>
    <?php endif; ?>

    <button class="btn btn-warning" type="button" id="booking_reset_btn">Reset</button>
    <div id="booking_simple_search_form">
        <div class="form-group custom-search-form col-sm-9 col-sm-offset-2">
            <input type="text" name="bookIdSearchFld" class="form-control" placeholder="Booking Id search" id="booking_id_search">
            <button class="btn btn-info" type="button" id="booking_simple_search">
                Search
            </button>
        </div>
        
        
        
        <button class="btn btn-primary " type="button" id="booking_advanc_search">Advance Search</button>
    </div>
    <div>
        <form id="advance_search_form"  style="display: none" method="post">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order date from</label>
                    <input type="date" name="from_oder_date_search" class="form-control" id="from_oder_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Order date to</label>
                    <input type="date" name="to_oder_date_search" class="form-control" id="to_oder_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment date from</label>
                    <input type="date" name="from_shipment_date_search" class="form-control" id="from_shipment_date_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Shipment date to</label>
                    <input type="date" name="to_shipment_date_search" class="form-control" id="to_shipment_date_search">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Buyer name</label>
                    <input type="text" name="buyer_name_search" class="form-control" placeholder="Buyer name search" id="buyer_name_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Company name</label>
                    <input type="text" name="company_name_search" class="form-control" placeholder="Company name search" id="company_name_search">
                </div>
                <div class="col-sm-3">
                    <label class="col-sm-12 label-control">Attention</label>
                    <input type="text" name="attention_search" class="form-control" placeholder="Attention search" id="attention_search">
                </div>
                <br>
                <div class="col-sm-3">
                    <input class="btn btn-info" type="submit" value="Search" name="booking_advanceSearch_btn" id="booking_advanceSearch_btn">
                </div>
            </div>

            
            <button class="btn btn-primary" type="button" id="booking_simple_search_btn">Simple Search</button>
        </form>
    </div>
    <br>

    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Serial no</th>
                    <th>Buyer Name</th>
                    <th>Company Name</th>
                    <th>Attention</th>
                    <th>booking No.</th>
                    <th>PO NO.</th>
                    <th width="10%">Order Date</th>
                    <th width="10%">Requested Date</th>
                    <th>Status</th>
                    <th width="">Action</th>
                </tr>
                </thead>

                <?php ($j=1); ?>
                <tbody id="booking_list_tbody">
                <?php $__currentLoopData = $bookingList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="booking_list_table">
                        <td><?php echo e($j++); ?></td>
                        <td><?php echo e($value->buyer_name); ?></td>
                        <td><?php echo e($value->Company_name); ?></td>
                        <td><?php echo e($value->attention_invoice); ?></td>
                        <td><?php echo e($value->booking_order_id); ?></td>
                        <td>
                            <?php $__currentLoopData = $value->po; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($values->ipo_id); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td><?php echo e(Carbon\Carbon::parse($value->created_at)->format('d-m-Y')); ?></td>
                        <td><?php echo e(Carbon\Carbon::parse($value->shipmentDate)->format('d-m-Y')); ?></td>
                        <td>
                            <a id="popoverOption" class="btn popoverOption" href="#"  rel="popover" data-placement="top" data-original-title="" style="color:black;"><?php echo e($value->booking_status); ?></a>

                            <div class="popper-content hide">
                                <label>Booking Prepared by: <?php echo e($value->booking->first_name); ?> <?php echo e($value->booking->last_name); ?> (<?php echo e(Carbon\Carbon::parse($value->created_at)->format('d-m-Y')); ?>)</label><br>

                                <label>Booking Accepted by: <?php echo e($value->accepted->first_name); ?> <?php echo e($value->accepted->last_name); ?>

                                    <?php echo e((!empty($value->accepted_date_at)?'('.Carbon\Carbon::parse($value->accepted_date_at)->format('d-m-Y').')':'')); ?>

                                </label><br>

                                <label>MRF Issue by: <?php echo e($value->mrf->first_name); ?> <?php echo e($value->mrf->last_name); ?>

                                    <?php echo e((!empty($value->mrf->created_at)?'('.Carbon\Carbon::parse($value->mrf->created_at)->format('d-m-Y').')':'')); ?>

                                </label><br>

                                <label>PO Issue by: <?php echo e($value->ipo->first_name); ?> <?php echo e($value->ipo->last_name); ?> <?php echo e((!empty($value->ipo->created_at)?'('.Carbon\Carbon::parse($value->ipo->created_at)->format('d-m-Y').')':'')); ?></label><br>
                            </div>
                        </td>
                        <td width="12%">
                            <div class="btn-group">
                                <form action="<?php echo e(Route('booking_list_action_task')); ?>" target="_blank">
                                    <input type="hidden" name="bid" value="<?php echo e($value->booking_order_id); ?>">
                                    <button class="btn btn-success b1">Report</button>

                                    <button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo e(Route('booking_list_details_view', $value->booking_order_id)); ?>">Views</a>
                                        </li>
                                        <?php if($roleCheck != 'p'): ?>
                                            <?php if($value->booking_status == BookingFulgs::BOOKED_FLUG): ?>
                                                <li>
                                                    <a href="<?php echo e(Route('booking_details_cancel_action', $value->booking_order_id)); ?>" class="deleteButton">Cancel</a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <li>
                                            <a href="<?php echo e(Route('booking_files_download', $value->id)); ?>" class="btn btn-info">Download Files</a>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div id="booking_list_pagination"><?php echo e($bookingList->links()); ?></div>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('LoadScript'); ?>
    <script type="text/javascript">
        $('.popoverOption').popover({
            trigger: "hover",
            container: 'body',
            html: true,
            content: function () {
                return $(this).next('.popper-content').html();
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>