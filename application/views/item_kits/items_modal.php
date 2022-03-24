<div class="modal-dialog customer-recent-sales">
	<div class="modal-content">
		<div class="modal-header">
			<button data-dismiss="modal" class="close" type="button">×</button>
			<h3><?php echo lang("items_basic_information"); ?></h3>
		</div>
		<div class="modal-body nopadding">			
			<table class="table table-bordered table-hover table-striped">
				
					<tr> <td><?php echo lang('common_item_kit_id'); ?></td> <td> <?php echo H($item_kit_info->item_kit_id); ?></td></tr>
					<tr> <td><h4><?php echo lang('common_item_name'); ?></h4></td> <td> <h4><?php echo lang('item_kits_quantity');?></h4></td></tr>
					 
					<?php foreach ($this->Item_kit_items->get_info($item_kit_info->item_kit_id) as $item_kit_item) {?>
						<tr>
							<?php
							$item_info = $this->Item->get_info($item_kit_item->item_id);
							?>
							<td><?php echo H($item_info->name); ?></td>
							<td> <?php echo to_quantity($item_kit_item->quantity) ?></td>
						</tr>
					<?php } ?>
					
				<tr> <td><?php echo lang('common_item_number_expanded'); ?></td> <td> <?php echo (isset($item_kit_info->item_kit_number) && $item_kit_info->item_kit_number != '') ? H($item_kit_info->item_kit_number) : lang('common_none'); ?></td></tr>

				<tr> <td><?php echo lang('common_product_id'); ?></td> <td> <?php echo (isset($item_kit_info->product_id) && $item_kit_info->product_id != '') ? H($item_kit_info->product_id) : lang('common_none'); ?></td></tr>
								
				<tr> <td><?php echo lang('item_kits_name'); ?></td> <td> <?php echo (isset($item_kit_info->name) && $item_kit_info->name != '') ? H($item_kit_info->name) : lang('common_none'); ?></td></tr>
				
				<tr> <td><?php echo lang('common_category'); ?></td> <td> <?php echo (isset($category) && $category != '') ? H($category) : lang('common_none'); ?></td></tr>
				<tr> <td><?php echo lang('common_manufacturer'); ?></td> <td> <?php echo H($manufacturer); ?></td></tr>
				
				<?php if ($this->Employee->has_module_action_permission('item_kits','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_kit_info->name=="")	{ ?>
				<tr> <td><?php echo lang('common_cost_price'); ?></td> <td> <?php echo (isset($item_kit_info->cost_price) && $item_kit_info->cost_price != '') ? to_currency($item_kit_info->cost_price, 10) : lang('common_none'); ?></td></tr>
				<?php } ?>
				
				<tr> <td><h4><?php echo lang('common_unit_price'); ?></h4></td> <td><h4><?php echo (isset($item_kit_info->unit_price) && $item_kit_info->unit_price != '') ? to_currency($item_kit_info->unit_price, 10) : lang('common_none'); ?></h4></td></tr>
				<?php 
				foreach($tier_prices as $tier_price)
				{
				?>
 				<tr> <td><?php echo H($tier_price['name']) ?></td> <td> <?php echo $tier_price['value']; ?></td></tr>
					
				<?php
				}
				?>
			 	
				<tr> <td><?php echo lang('item_kits_description'); ?></td> <td> <?php echo (isset($item_kit_info->description) && $item_kit_info->description != '') ? H($item_kit_info->description) : lang('common_none'); ?></td></tr>
			 	 
		 	</table>
		</div>
	</div>
</div>



