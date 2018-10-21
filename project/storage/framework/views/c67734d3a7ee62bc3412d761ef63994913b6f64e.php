<?php $__env->startSection('page_heading', trans("others.mxp_menu_booking_view_details") ); ?>
<?php $__env->startSection('section'); ?>
<?php 
    // print_r("<pre>");
    // print_r($bookingDetails->bookings_challan_table);
    // print_r(session('data'));
    // print_r("</pre>");
    use App\Http\Controllers\taskController\Flugs\Role\PlaningFlugs;
    use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
    $object = new App\Http\Controllers\Source\User\PlanningRoleDefine();
    $roleCheck = $object->getRole();
?>
<div class="row">
    <div class="col-sm-2">
        <div class="form-group ">
            <a href="<?php echo e(URL::previous()); ?>" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
            <i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>

<?php if(Session::has('empty_message')): ?>
        <?php echo $__env->make('widgets.alert', array('class'=>'danger', 'message'=> Session::get('empty_message') ), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
<?php if(Session::has('message')): ?>
    <div class="alert alert-success">
        <ul>
            <?php echo e(Session::get('message')); ?>

        </ul>
    </div>
<?php endif; ?>
<?php if(Session::has('error-m')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php echo e(Session::get('error-m')); ?>

        </ul>
    </div>
<?php endif; ?>
        
<?php if($roleCheck == 'p'): ?>
    <?php if($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info" style="font-size: 18px;box-shadow: 0 10px 20px rgba(0,0,0,0.10), 0 6px 15px rgba(0,0,0,0.15);
                    z-index: 999;">
                  <center><strong>Accept!</strong> this Order and go to proccessing. <a href="<?php echo e(route('accepted_booking')); ?>/<?php echo e($bookingDetails->booking_order_id); ?>" style="font-size: 20px;font-weight: bold;" title="Click Me"> Accept</a></center>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('data') == BookingFulgs::BOOKING_PROCESS_FLUG): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="normal-btn-success">
                    <button type="button" class="close">×</button>
                    Booking Accepted.
                </div>
            </div>
        </div>        
    <?php endif; ?>
<?php endif; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div style="font-size: 120%">Booking Details</div>
    </div>
    <div class="panel-body aaa">
        <div class="panel panel-default col-sm-7">
            <br>
            <p >Buyer name:<b> <?php echo e($bookingDetails->buyer_name); ?></b></p>
            <p >Company name:<b> <?php echo e($bookingDetails->Company_name); ?></b></p>
            <p >Buyer address:<b> <?php echo e($bookingDetails->address_part1_invoice); ?><?php echo e($bookingDetails->address_part2_invoice); ?></p>
            <p >Mobile num:<b> <?php echo e($bookingDetails->mobile_invoice); ?></b></p>
        </div>
        <div class="panel panel-default col-sm-5">
            <br>
            <p >Booking Id:<b> <?php echo e($bookingDetails->booking_order_id); ?></b></p>
            <p >Booking status:<b> <?php echo e($bookingDetails->booking_status); ?></b></p>
            <p >Oreder Date:<b> <?php echo e($bookingDetails->bookings[0]->orderDate); ?></b></p>
            <p >Shipment Date:<b> <?php echo e($bookingDetails->bookings[0]->shipmentDate); ?></b></p>
            <?php if($roleCheck == 'p'): ?>
                <?php if($bookingDetails->booking_status == BookingFulgs::BOOKING_PROCESS_FLUG): ?>
                    <p style="font-size: 15px;"><strong>Accepted by</strong> <span style="color:red;"><?php echo e($bookingDetails->first_name); ?><?php echo e($bookingDetails->last_name); ?></span></p>
               <?php endif; ?>
            <?php endif; ?>
        </div>
        
            <table class="table table-bordered vi_table">
                <thead>
                    <tr>
                        <?php if($roleCheck == 'p'): ?>
                        <th>#</th>
                        <?php endif; ?>
                        <th>Job No.</th>
                        <th width="15%">ERP Code</th>
                        <th width="20%">Item Code</th>
                        <th width="5%">Season Code</th>
                        <th>OOS No.</th>
                        <th>Style</th>
                        <th>PO/Cat No.</th>
                        <th>GMTS Color</th>
                        <th width="15%">Size</th>
                        <th>Sku</th>
                        <th>Order Qty</th>
                        <th width="15%">Action</th>
                        <?php if($roleCheck == 'p'): ?>
                        <th>IPO QTY</th>
                        <th>MRF QTY</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                
                <?php if($roleCheck == 'empty'): ?>
                    <tbody>
                        <?php $__currentLoopData = $bookingDetails->bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bookedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $jobId = (8 - strlen($bookedItem->id)); ?>
                        <tr style="">
                            <td><?php echo e(str_repeat('0',$jobId)); ?><?php echo e($bookedItem->id); ?></td>                
                            <td><?php echo e($bookedItem->erp_code); ?></td>
                            <td><?php echo e($bookedItem->item_code); ?></td>
                            <td><?php echo e($bookedItem->season_code); ?></td>
                            <td><?php echo e($bookedItem->oos_number); ?></td>
                            <td><?php echo e($bookedItem->style); ?></td>
                            <td><?php echo e($bookedItem->poCatNo); ?></td>
                            <td><?php echo e($bookedItem->gmts_color); ?></td>
                            <td><?php echo e($bookedItem->item_size); ?></td>
                            <td><?php echo e($bookedItem->sku); ?></td>
                            <td><?php echo e($bookedItem->item_quantity); ?></td>                         
                            <td>
                                <div style="float: left;width: 46%;">
                                <form method="POST" action="<?php echo e(route('booking_details_update_view')); ?>" target="_blank">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="hidden" name="job_id" value="<?php echo e($bookedItem->id); ?>">
                                    <button class="form-control" <?php echo e(($bookingDetails->booking_status != BookingFulgs::BOOKED_FLUG) ? 'disabled' :''); ?>>Edit</button>
                                </form>
                                </div>
                                <div style="float: right;width: 54%;">
                                <button class="form-control deleteButton" <?php echo e(($bookingDetails->booking_status != BookingFulgs::BOOKED_FLUG) ? 'disabled' :''); ?>>Delete</button>
                                </div>
                            </td>                    
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                <form action="<?php echo e(route('ipo_mrf_define')); ?>" method="post">
           <?php echo e(csrf_field()); ?>

           <input type="hidden" name="booking_order_id" value="<?php echo e($bookingDetails->booking_order_id); ?>">
                <?php elseif($roleCheck == 'p'): ?>                        
                    <tbody>              
                        <?php $__currentLoopData = $bookingDetails->bookings_challan_table; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bookedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $jobId = (8 - strlen($bookedItem->id)); ?>
                        <tr style="">
                            <label for="job_id">
                            <td width="3.5%">
                                <input type="checkbox" name="job_id[]" value="<?php echo e($bookedItem->id); ?>" class="form-control" id="select_check" <?php echo e(($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ($bookedItem->left_mrf_ipo_quantity <= 0)?'disabled' :''); ?>>
                            </td>
                            <td><?php echo e(str_repeat('0',$jobId)); ?><?php echo e($bookedItem->id); ?></td>           
                            <td><?php echo e($bookedItem->erp_code); ?></td>
                            <td><?php echo e($bookedItem->item_code); ?></td>
                            <td><?php echo e($bookedItem->season_code); ?></td>
                            <td><?php echo e($bookedItem->oos_number); ?></td>
                            <td><?php echo e($bookedItem->style); ?></td>
                            <td><?php echo e($bookedItem->poCatNo); ?></td>
                            <td><?php echo e($bookedItem->gmts_color); ?></td>
                            <td><?php echo e($bookedItem->item_size); ?></td>
                            <td><?php echo e($bookedItem->sku); ?></td>
                            <td><?php echo e($bookedItem->left_mrf_ipo_quantity); ?></td>
                            <td><?php echo e($bookedItem->ipo_quantity); ?></td>
                            <td><?php echo e($bookedItem->mrf_quantity); ?></td>
                            </label>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                        
                    </tbody>
                <?php endif; ?>               
            </table>
            <?php if($roleCheck == 'p'): ?>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" name="increase_value" class="form-control increase_field hidden" placeholder="Increase Value">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group pull-right">
                        <label class="radio-inline">
                            <input type="radio" name="ipo_or_mrf" value="ipo" style="margin: 2px -30px 0px" <?php echo e(($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''); ?>>IPO
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="ipo_or_mrf" value="mrf" style="margin: 2px -30px 0px" <?php echo e(($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''); ?>>MRF
                        </label>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px" <?php echo e(($bookingDetails->booking_status == BookingFulgs::BOOKED_FLUG) ? 'disabled' : ''); ?>>
                            Submit
                        </button>
                    </div>
                </div>
            </div>                    
            <?php endif; ?>
        </form>
    </div>
</div>

    <?php if($roleCheck == 'p'): ?>
    <div class="panel panel-default">
        <div class="panel-heading" style="font-size: 120%">Mrf Details</div>
        <div class="panel-body aaa">
            <table class="table table-bordered">
                <tr>
                    <thead>
                        <th>Job No.</th>
                        <th width="17%">MRF No.</th>
                        <th>Item Code</th>
                        <th>GMTS Color</th>
                        <th width="8%">Item Size</th>
                        <th>Quantity</th>
                        <th>Delivered Quantity</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                </tr>
                <tbody>
                <?php $__currentLoopData = $bookingDetails->mrf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                    $idstrcount = (8 - strlen($value->job_id));
                    // $gmts_color = explode(',', $value->gmts_color);
                    // $itemsize = explode(',', $value->item_size);
                    // $mrf_quantity = explode(',', $value->mrf_quantity);
                ?>
                <tr>
                    <td><?php echo e(str_repeat('0',$idstrcount)); ?><?php echo e($value->job_id); ?></td>
                    <td><?php echo e($value->mrf_id); ?></td>
                    <td><?php echo e($value->item_code); ?></td>
                    <td><?php echo e($value->gmts_color); ?></td>                    
                    <td width="18%"><?php echo e($value->item_size); ?></td>
                    <td><?php echo e($value->mrf_quantity); ?></td>
                    <td>Delivered Quantity</td>
                    <td><?php echo e($value->shipmentDate); ?></td>
                    <td><?php echo e($value->mrf_status); ?></td>
                    <td>Action</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading" style="font-size: 120%">IPO Details</div>
        <div class="panel-body aaa">
            <table class="table table-bordered">
                <tr>
                    <thead>
                        <th>Job No.</th>
                        <th>IPO No.</th>
                        <th>Item Code</th>
                        <th>Color</th>
                        <th>Item Size</th>
                        <th>Quantity</th>
                        <th>Delivered Quantity</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                </tr>
                <?php ($j=1); ?>
                <tbody>

                <?php $__currentLoopData = $bookingDetails->ipo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $idstrcount = (8 - strlen($value->job_id));
                    // $gmts_color = explode(',', $value->gmts_color);
                    // $itemsize = explode(',', $value->item_size);
                    // $ipo_quantity = explode(',', $value->ipo_quantity);
                ?>
                <tr>
                    <td><?php echo e(str_repeat('0',$idstrcount)); ?><?php echo e($value->job_id); ?></td>
                    <td><?php echo e($value->ipo_id); ?></td>
                    <td><?php echo e($value->item_code); ?></td>
                    <td><?php echo e($value->gmts_color); ?></td>                    
                    <td width="18%"><?php echo e($value->item_size); ?></td>
                    <td><?php echo e($value->ipo_quantity); ?></td>
                    <td>Delivered Quantity</td>
                    <td><?php echo e($value->shipmentDate); ?></td>
                    <td><?php echo e($value->ipo_status); ?></td>
                    <td>Action</td>
                    <!-- <td><?php echo e(Carbon\Carbon::parse($value->created_at)); ?></td> -->
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>