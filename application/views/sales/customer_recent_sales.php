<div class="modal-dialog customer-recent-sales">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h5 class="modal-title"><?php echo lang('sales_recent_sales').' '.H($customer);?></h5>
			<h6><?php echo $customer_comments;?></h6>
		</div>
		<div class="modal-body nopadding">
			<table id="recent_sales" class="table">
				<tr>
					<th><?php echo lang('common_date');?></th>
					<th><?php echo lang('common_payments');?></th>
					<th><?php echo lang('common_items_purchased');?></th>
					<th><?php echo lang('common_recp');?></th>
					<th><?php echo lang('common_comment');?></th>
				</tr>
				
				<?php foreach($recent_sales as $sale) {?>
					<tr class="table-row-hover">
						<td><?php echo date(get_date_format().' @ '.get_time_format(), strtotime($sale['sale_time']));?></td>
						<td><?php echo $sale['payment_type'];?></td>
						<td><?php echo to_quantity($sale['items_purchased']);?></td>
						<td><?php echo anchor('sales/receipt/'.$sale['sale_id'], lang('sales_receipt'), array('target' =>'_blank')); ?></td>
						<td><?php echo $sale['comment'];?></td>
					</tr>
				<?php } ?>
			</table>
	
		</div>
	</div>
</div>