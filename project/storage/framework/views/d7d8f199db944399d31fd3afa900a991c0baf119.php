<?php $__env->startSection('section'); ?>
	<div id="welcome">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
			  	<div class="panel-body">
			  		<div class="header">
			  			<h3>Welcome Back <b><?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?></b></h3>
			  		</div>
			  		<div class="body">
			  			<h3>Welcome to <b>Maxim Order Management System!</b></h3>
			  		</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>