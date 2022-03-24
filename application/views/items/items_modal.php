<div class="modal-dialog customer-recent-sales">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true" class="ti-close"></span></button>
			<h4 class="modal-title"><?php echo lang("items_basic_information"); ?></h4>
		</div>
		<div class="modal-body">
			<div class="modal-item-info">
				<div class="modal-item-avatar">
					<?php echo $item_info->image_id ? img(array('src' => app_file_url($item_info->image_id),'class'=>' img-polaroid')) : img(array('src' => base_url().'assets/img/avatar.png','class'=>' img-polaroid','id'=>'image_empty')); ?>
				</div>
				<div class="modal-item-details">
					<span class="modal-item-name"><?php echo H($item_info->name); ?></span>
					<span class="modal-item-category"><?php echo H($category); ?></span>
				</div>
			</div>
			
			<table class="table table-bordered table-hover table-striped">
				<tr> <td><?php echo lang('common_item_id'); ?></td> <td> <?php echo H($item_info->item_id); ?></td></tr>
				<tr> <td><?php echo lang('common_item_number_expanded'); ?></td> <td> <?php echo H($item_info->item_number); ?></td></tr>
				<tr> <td><?php echo lang('common_product_id'); ?></td> <td> <?php echo H($item_info->product_id); ?></td></tr>
				<tr> <td><h4><?php echo lang('common_item_name'); ?></h4></td> <td> <h4><?php echo H($item_info->name); ?></h4></td></tr>
				<tr> <td><?php echo lang('common_category'); ?></td> <td> <?php echo H($category); ?></td></tr>
				<tr> <td><?php echo lang('common_manufacturer'); ?></td> <td> <?php echo H($manufacturer); ?></td></tr>
				<tr> <td><?php echo lang('common_size'); ?></td> <td> <?php echo H($item_info->size); ?></td></tr>
				<tr> <td><?php echo lang('common_supplier'); ?></td> 
					<td> <?php if (isset($supplier) && $supplier != '' ){
							echo $supplier;
						}else {
						   echo lang('common_none');  
						}
						?></td>
				</tr>
				<?php if ($this->Employee->has_module_action_permission('items','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_info->name=="")	{ ?>
				<tr> <td><?php echo lang('common_cost_price'); ?></td> <td> <?php echo to_currency($item_info->cost_price, 10); ?></td></tr>
				<?php } ?>
				<tr> <td><h4><?php echo lang('common_unit_price'); ?></h4></td> <td> <h4><?php echo to_currency($item_info->unit_price, 10); ?></h4></td></tr>
				<?php 
				foreach($tier_prices as $tier_price)
				{
				?>
 				<tr> <td><?php echo H($tier_price['name']) ?></td> <td> <?php echo $tier_price['value']; ?></td></tr>
					
				<?php
				}
				?>
 				<tr> <td><?php echo lang('items_promo_price'); ?></td> <td> <?php echo to_currency($item_info->promo_price, 10); ?></td></tr>
				<tr> <td><?php echo lang('items_quantity'); ?></td> <td> <?php echo to_quantity($item_location_info->quantity); ?></td></tr>
				<tr> <td><?php echo lang('items_reorder_level'); ?></td> <td> <?php echo to_quantity($reorder_level); ?></td></tr>
				<tr> <td><?php echo lang('common_location'); ?></td> <td> <?php echo $item_location_info->location; ?></td></tr>
				<tr> <td><?php echo lang('common_description'); ?></td> <td> <?php echo H($item_info->description); ?></td></tr>
				<tr> <td><?php echo lang('items_allow_alt_desciption'); ?></td> <td> <?php echo $item_info->allow_alt_description ? lang('common_yes') : lang('common_no'); ?></td></tr>
				<tr> <td><?php echo lang('items_is_serialized'); ?></td> <td> <?php echo $item_info->is_serialized ? lang('common_yes') : lang('common_no'); ?></td></tr>
				<?php if($this->config->item("ecommerce_platform"))
				{
				?>
				<tr> <td><?php echo lang('items_is_ecommerce'); ?></td> <td> <?php echo $item_info->is_ecommerce ? lang('common_yes') : lang('common_no'); ?></td></tr>
				<?php
				}
				?>
				
				<?php if (isset($additional_item_numbers) && $additional_item_numbers->num_rows() > 0) {?>
					<tr> <td colspan="2"><strong><?php echo lang('common_additional_item_numbers'); ?></strong></td></tr>
					<?php foreach($additional_item_numbers->result() as $additional_item_number) { ?>
						<tr><td colspan="2"><?php echo H($additional_item_number->item_number); ?></td></tr>
					<?php } ?>
				<?php } ?>
				
			</table>
			
			<table class="table table-bordered table-hover table-striped" width="1200px">
				<tr>
					<td colspan="2"><h3><?php echo lang('receivings_list_of_suspended'); ?></h3></th>
				</tr>
				<tr>
					<th><?php echo lang('receivings_id');?></th>
					<th><?php echo lang('items_quantity');?></th>
				</tr>
				
				<?php foreach($suspended_receivings as $receiving_item) {?>
					<tr>
						<td style="text-align: center;"><?php echo anchor('receivings/receipt/'.$receiving_item['receiving_id'], 'RECV '.$receiving_item['receiving_id'], array('target' => '_blank'));?></td>
						<td style="text-align: center;"><?php echo to_quantity($receiving_item['quantity_purchased']);?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>



