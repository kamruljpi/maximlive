<?php $__env->startSection('page_heading', 'Proforma Invoice'); ?>
<?php $__env->startSection('section'); ?>
<?php
	// print_r("<pre>");
	// print_r($bookingDetails);
	// print_r("</pre>");
	$TotalBookingQty =0;
?>
<div class="container-fluid">
	<?php if(Session::has('erro_challan')): ?>
        <?php echo $__env->make('widgets.alert', array('class'=>'danger', 'message'=> Session::get('erro_challan') ), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php endif; ?>
	<div class="row">
		<form action="<?php echo e(route('pi_generate_action')); ?>">
			<table class="table table-bordered vi_table">
				<thead>
					<th>#</th>
					<th>Job No</th>
					<th>PO/Cat No</th>
					<th>Item OOS</th>
					<th>Item Code</th>
					<th>ERP Code</th>
					<th>Item Description</th>
					<th>GMTS / item Color</th>
					<th>Item Size</th>
					<th>Style</th>
					<th>SKU</th>
					<th>Item Qty</th>
				</thead>
				<tbody>
					<input type="hidden" name="is_type" value="<?php echo e($is_type); ?>">
					<?php if(!empty($bookingDetails[0]->id)): ?>
					<?php $itemcodestatus = ''; ?>
					<?php $__currentLoopData = $bookingDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailsValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php
							$gmtsColor = explode(',', $detailsValue->gmts_color);
							$itemSize = explode(',', $detailsValue->item_size);
							$quantity = explode(',', $detailsValue->item_quantity);
						?>
						<?php $rowspanValue = 0; ?>

						<?php $__currentLoopData = $quantity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $qtyValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							
							<?php 
								$TotalBookingQty += $qtyValue; 
								$rowspanValue += $rowspanValue +1; 
								$idstrcount = (8 - strlen($detailsValue->id));

							?>
							<tr>
								<td width="3.5%">
									<input type="checkbox" name="job_id[]" value="<?php echo e($detailsValue->id); ?>" class="form-control" checked>
								</td>
								<td><?php echo e(str_repeat('0',$idstrcount)); ?><?php echo e($detailsValue->id); ?></td>
								<td><?php echo e($detailsValue->poCatNo); ?></td>
								<td><?php echo e($detailsValue->oos_number); ?></td>
								<td><?php echo e($detailsValue->item_code); ?></td>
								<td><?php echo e($detailsValue->erp_code); ?></td>

								

								<td><?php echo e($detailsValue->item_description); ?></td>
								<td><?php echo e($gmtsColor[$key]); ?></td>
								<td><?php echo e($itemSize[$key]); ?></td>
								<td><?php echo e($detailsValue->style); ?></td>
								<td><?php echo e($detailsValue->sku); ?></td>
								<td><?php echo e($qtyValue); ?></td>
							</tr>
						<?php $itemcodestatus = $detailsValue->item_code; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php else: ?>
						<tr>
							<td colspan="12"><center>PI has been compelete.</center></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<div class="form-group ">
				<div class="col-md-2 pull-right">
					
					<button type="submit" class="btn btn-primary" >
						Generate
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>