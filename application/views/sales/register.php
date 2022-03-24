<?php $this->load->helper('demo'); ?>
		<?php if($this->sale_lib->get_change_sale_id()) { ?>
			<div class="alert alert-danger">
				<?php echo lang('sales_editing_sale'); ?> <strong><?php echo $this->config->item('sale_prefix').' '.$this->sale_lib->get_change_sale_id(); ?></strong>
			</div>
		<?php } ?>
		
		<?php if (isset($orig_location) && $orig_location != $this->Employee->get_logged_in_employee_current_location_id()) { ?>
			<div class="alert alert-danger">
				<strong><?php echo lang('sales_editing_sale_not_in_wrong_location');?></strong>
			</div>
		<?php } ?>
		
		<?php if($this->config->item('test_mode')) { ?>
			<div class="alert alert-danger">
				<strong><?php echo lang('common_in_test_mode'); ?>. <a href="sales/disable_test_mode"></strong>
				<a href="<?php echo site_url('sales/disable_test_mode'); ?>" id="disable_test_mode"><?php echo lang('common_disable_test_mode');?></a>
			</div>
		<?php } ?>

<div class="row register">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
		<div class="register-box register-items-form">
		<a tabindex="-1" href="#" class="dismissfullscreen <?php echo !$fullscreen ? 'hidden' : ''; ?>"><i class="ion-close-circled"></i></a>
			<div id="itemForm" class="item-form">
				<!-- Item adding form -->
				
				<?php
				$cart_count = 0;
				?>
				<?php echo form_open("sales/add",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
					
					<div class="input-group input-group-mobile contacts">
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/sale","<i class='icon ti-pencil-alt'></i> <span class='register-btn-text'>".lang('common_new_item')."</span>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item-mobile','tabindex'=> '-1')); ?>
						</span>		
						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i> <span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','tabindex'=>'-1','title'=>$modes[$mode], 'id' => 'select-mode-1', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo H($key); ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php }  
						  	} ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons <?php echo $mode == 'store_account_payment' ? 'hidden': '' ;?>">
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_show_grid')."</span>", array('class'=>'none show-grid','tabindex'=>'-1','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'>".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','tabindex'=>'-1','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>
					
					<div class="input-group contacts register-input-group">

						<!-- Css Loader  -->
						<div class="spinner" id="ajax-loader" style="display:none">
						  <div class="rect1"></div>
						  <div class="rect2"></div>
						  <div class="rect3"></div>
						</div>
						
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/sale","<i class='icon ti-pencil-alt'></i>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item', 'tabindex'=> '-1')); ?>
						</span>
						<input type="text" id="item" name="item" <?php echo  ($mode=="store_account_payment") ? 'disabled="disabled"' : '' ?> class="add-item-input pull-left keyboardTop" placeholder="<?php echo lang('common_start_typing_item_name'); ?>" data-title="<?php echo lang('common_item_name'); ?>">
		
						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i>".$modes[$mode], array('class'=>'none active','tabindex'=>'-1','title'=>$modes[$mode], 'id' => 'select-mode-2', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo H($key); ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php }  
						  	} ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons <?php echo $mode == 'store_account_payment' ? 'hidden': '' ;?>">
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_show_grid'), array('class'=>'none show-grid','tabindex'=>'-1','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_hide_grid'), array('class'=>'none hide-grid hidden','tabindex'=>'-1','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>
					
				</form>
			</div>
		</div>
		<!-- Register Items. @contains : Items table -->
		<div class="register-box register-items paper-cut">
			<div class="register-items-holder">
				<?php if ($mode != 'store_account_payment') { ?>					
					<table id="register" class="table table-hover">

					<thead>
						<tr class="register-items-header">
							<th></th>
							<th class="item_name_heading" ><?php echo lang('sales_item_name'); ?></th>
							<th class="sales_price"><?php echo lang('common_price'); ?></th>
							<th class="sales_quantity"><?php echo lang('common_quantity'); ?></th>
							<th class="sales_discount"><?php echo lang('common_discount_percent'); ?></th>
							<th><?php echo lang('common_total'); ?></th>
						</tr>
					</thead>
				
					<tbody class="register-item-content">
						<?php
						if(count($cart)==0)	{ ?>
						<tr class="cart_content_area">
							<td colspan='6'>
								<div class='text-center text-warning' > <h3><?php echo lang('common_no_items_in_cart'); ?><span class="flatGreenc"> [<?php echo lang('module_sales') ?>]</span></h3></div>
							</td>
						</tr>
						<?php 
						}
						else
						{
							
						 foreach(array_reverse($cart, true) as $line=>$item) { 
							 
							 if ($item['quantity'] > 0 && $item['name'] != lang('common_store_account_payment') && $item['name'] != lang('common_discount'))
							 {
						 		$cart_count = $cart_count + $item['quantity'];
							 }
							?>
							<tr class="register-item-details">
								<td class="text-center"> <?php echo anchor("sales/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item', 'tabindex' => '-1'));?> </td>
								<td> 
									<a tabindex="-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" class="register-item-name" ><?php echo H($item['name']); ?><?php echo $item['size'] ? ' ('.H($item['size']).')': ''; ?></a>
								</td>
								<td class="text-center">
									<?php if ($this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
										<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
											<a href="javascript:void(0)" id="price_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-name="price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
										<?php } else { ?>
											<a href="#" id="price_<?php echo $line;?>" class="xeditable xeditable-price" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
										<?php } ?>
									<?php } else { 
											echo to_currency($item['price'],10); 
									 }	?>
								</td>
								<td class="text-center">
										<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
											<a href="javascript:void(0)" id="quantity_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_quantity($item['quantity'])); ?>" data-name="quantity" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
										<?php } else { ?>
											<a href="#" id="quantity_<?php echo $line;?>" class="xeditable" data-type="text"  data-validate-number="true"  data-pk="1" data-name="quantity" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
										<?php } ?>
								</td>
								<td class="text-center">
									<?php if ($line != $line_for_flat_discount_item && $this->Employee->has_module_action_permission('sales', 'give_discount', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
											<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
												<a href="javascript:void(0)" id="discount_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H($item['discount']); ?>" data-name="discount" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>
											<?php } else { ?>
												<a href="#" id="discount_<?php echo $line;?>" class="xeditable" data-type="text" data-validate-number="true"  data-pk="1" data-name="discount" data-value="<?php echo H($item['discount']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>
											<?php } ?>
																	
									<?php }else{ ?>
									
										<?php echo to_quantity($item['discount']); ?>%
										
									<?php }	?>
								</td>
								<td class="text-center">
								<?php 
								
								echo to_currency(($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?></td>
							</tr>
							<tr class="register-item-bottom">
								<td>&nbsp;</td>
								<td colspan="5">
									<dl class="register-item-extra-details dl-horizontal">
										
										
										<?php
										
										if (!$this->config->item('always_use_average_cost_method') && $item['change_cost_price'] && $this->Employee->has_module_action_permission('sales', 'edit_sale_cost_price', $this->Employee->get_logged_in_employee_info()->person_id))
										{
										?>
										 <dt><?php echo lang('common_cost_price');?></dt>
										<dd>
											<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
												<a href="javascript:void(0)" id="cost_price_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_currency_no_money($item['cost_price'])); ?>" data-name="cost_price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_cost_price')); ?>"><?php echo to_currency($item['cost_price']); ?></a>
											<?php } else { ?>
												<a href="#" id="cost_price_<?php echo $line;?>" class="xeditable xeditable-cost-price" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['cost_price'])); ?>" data-pk="1" data-name="cost_price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_cost_price')); ?>"><?php echo to_currency($item['cost_price']); ?></a>
											<?php } ?>
										</dd>										
										<?php
										}
										?>
									  <dt><?php echo lang('common_description') ?></dt>
									  <dd>
		  							  <?php if(isset($item['allow_alt_description']) && $item['allow_alt_description']==1) { ?>
											<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
												<a href="javascript:void(0)" id="description_<?php echo $line;?>" class="edit-on-click editable-click" data-name="description" data-value="<?php echo H($item['description']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_description_abbrv')); ?>" data-emptytext="<?php echo H(lang('common_none')); ?>" ><?php echo character_limiter(H($item['description']), 50); ?></a>
											<?php } else { ?>
												<a href="#" id="description_<?php echo $line;?>" class="xeditable" data-type="text" data-pk="1" data-name="description" data-value="<?php echo H($item['description']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_description_abbrv')); ?>"><?php echo character_limiter(H($item['description']), 50); ?></a>
											<?php } ?>
										<?php	}
											else
											{
												if ($item['description']!='')
												{
													echo $item['description'];
												}
												else
												{
													echo lang('common_none');
												}
											}
										?>
										<dt>
										<?php 
										
										if(isset($item['rule']['name'])) 
										{ 
												echo  $item['rule']['name']; 
										} 
										?>
										</dt>
										<dd>
											<?php
											
											 if(isset($item['rule']['rule_discount']))
												  {
													  echo '-'.to_currency($item['rule']['rule_discount']);
												  }
											?>
										</dd>
										<!-- Serial Number if exists -->
										<?php  if(isset($item['is_serialized']) && $item['is_serialized']==1  && $item['name']!=lang('common_giftcard'))	{ ?>
										<dt  class=""><?php echo lang('sales_serial'); ?> </dt>
										<?php
										$serial_numbers = $this->Item_serial_number->get_all($item['item_id'])->result_array();
										
										if (count($serial_numbers) > 0)
										{
											?>
										  <dd  class=""><a href="#" id="serialnumber_<?php echo $line;?>"data-name="serialnumber" data-type="select" data-pk="1" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_serial')); ?>"><?php echo character_limiter(H($item['serialnumber']), 50); ?></a></dd>
										<?php
										}
										else
										{
										?>
									  <dd  class="">
										<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
											<a href="javascript:void(0)" id="serialnumber_<?php echo $line;?>" class="edit-on-click editable-click" data-value="<?php echo H($item['serialnumber']); ?>" data-name="serialnumber" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-emptytext="<?php echo H(lang('common_none')); ?>" data-title="<?php echo H(lang('sales_serial')); ?>"><?php echo character_limiter(H($item['serialnumber']), 50); ?></a>
										<?php } else { ?>
											<a href="#" id="serialnumber_<?php echo $line;?>" class="xeditable" data-type="text" data-pk="1" data-name="serialnumber" data-value="<?php echo H($item['serialnumber']); ?>" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('sales_serial')); ?>"><?php echo character_limiter(H($item['serialnumber']), 50); ?></a>
										<?php } ?>
										</dd>
										<?php
										}
										?>
										
										<?php
										if (count($serial_numbers) > 0)
										{
											$source_data = array();
											
											foreach($serial_numbers as $serial_number)
											{
												$source_data[] = array('value' => $serial_number['serial_number'], 'text' => $serial_number['serial_number']);
											}
											?>
											<script>
										  $('#serialnumber_<?php echo $line;?>').editable({
												value: <?php echo json_encode(H($item['serialnumber']) ? H($item['serialnumber']) : '');?>,
												source: <?php echo json_encode($source_data); ?>,
									    	success: function(response, newValue) {
												 last_focused_id = $(this).attr('id');
									 			 $("#register_container").html(response);
												}
												
										  });
											</script>
											<?php
												
										}
										?>
										<?php } ?>
										
										<dt class="visible-lg">
										<?php 
										switch($this->config->item('id_to_show_on_sale_interface'))
										{
											case 'number':
											echo lang('common_item_number_expanded'); 
											break;
						
											case 'product_id':
											echo lang('common_product_id'); 
											break;
						
											case 'id':
											echo lang('common_item_id'); 
											break;
						
											default:
											echo lang('common_item_number_expanded'); 
											break;
										}
										?>
										</dt>
										<dd class="visible-lg">
										<?php 
										switch($this->config->item('id_to_show_on_sale_interface'))
										{
											case 'number':
											
												if(array_key_exists('item_number', $item) && $item['item_number'])
												{
													echo H($item['item_number']);
												}
												elseif(array_key_exists('item_kit_number', $item) && $item['item_kit_number'])
												{
													echo H($item['item_kit_number']);
												}
												else
												{
													echo lang('common_none');
												}
														
											break;
				
											case 'product_id':
											echo array_key_exists('product_id', $item) ? H($item['product_id']) : lang('common_none'); 
											break;
				
											case 'id':
											echo array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
											break;
						
											default:
												if(array_key_exists('item_number', $item) && $item['item_number'])
												{
													echo H($item['item_number']);
												}
												elseif(array_key_exists('item_kit_number', $item) && $item['item_kit_number'])
												{
													echo H($item['item_kit_number']);
												}
												else
												{
													echo lang('common_none');
												}
											break;
										}
										?>
									</dd>
										<?php if (isset($item['item_id']) && $item['item_id']) 
										{ 
											$item_location_info = $this->Item_location->get_info($item['item_id'], false, true);
											
											$cur_quantity = $item_location_info->quantity;
											?>
											<dt><?php echo lang('common_stock'); ?></dt>
											<dd><?php echo to_quantity($cur_quantity); ?></dd>
										<?php } ?>
									</dl>
								</td>
							</tr>
						<?php } }  ?>
						</tbody>
					</table>
					
			</div>

			<!-- End of Sales or Return Mode -->
			<?php } else {  ?>

			<table id="register"  class="table table-hover ">

				<thead>
					<tr class="register-items-header">
						<th ><?php echo lang('sales_item_name'); ?></th>
						<th ><?php echo lang('common_payment_amount'); ?></th>
					</tr>
				</thead>
				<tbody id="cart_contents">
					<?php
					foreach(array_reverse($cart, true) as $line=>$item)	
					{
						?>
						 							
						<tr id="reg_item_top" >
							<td class="text text-center text-success"><a tabindex = "-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" ><?php echo H($item['name']); ?></a></td>
							<td class="text-center">
								<?php
								echo form_open("sales/edit_item/$line", array('class' => 'line_item_form', 'autocomplete'=> 'off')); 	

									?>
									<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
										<a href="javascript:void(0)" id="price_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-name="price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency_no_money($item['price'],10); ?></a>
									<?php } else { ?>
										<a href="#" id="price_<?php echo $line; ?>" class="xeditable" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('sales/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency_no_money($item['price'],10); ?></a>
									<?php } ?>
									
									<?php
									echo form_hidden('quantity',to_quantity($item['quantity']));
									echo form_hidden('description','');
									echo form_hidden('serialnumber', '');
								?>
							
								</form>		
							</td>
						</tr>
						
						
				 
				<?php } /*Foreach*/?>
			</tbody>
		</table>

					</div>

			<?php }  ?>
			<!-- End of Store Account Payment Mode -->

		</div>
		<!-- /.Register Items -->
		<?php
		if ($mode == 'store_account_payment')
		{
			if (!empty($unpaid_store_account_sales))
			{
			?>
			<table id="unpaid_sales" class="table table-hover table-condensed">
				<thead>
					<tr class="register-items-header">
						<th class="sp_sale_id"><?php echo lang('common_sale_id');?></th>
						<th class="sp_date"><?php echo lang('common_date');?></th>
						<th class="sp_charge"><?php echo lang('common_total_charge_to_account');?></th>
						<th class="sp_comment"><?php echo lang('common_comment');?></th>
						<th class="sp_pay"><?php echo lang('common_pay');?></th>
					</tr>
				</thead>
				
				<tbody id="unpaid_sales_data">
				
			<?php
				foreach($unpaid_store_account_sales as $unpaid_sale)
				{
					
				$row_class = isset($unpaid_sale['paid']) && $unpaid_sale['paid'] == TRUE ? 'success' : 'active';
				$btn_class = isset($unpaid_sale['paid']) && $unpaid_sale['paid'] == TRUE ? 'btn-danger' : 'btn-primary';
				?>
					<tr class="<?php echo $row_class; ?>">
						<td class="sp_sale_id text-center"><?php echo anchor('sales/receipt/'.$unpaid_sale['sale_id'], ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS').' '.$unpaid_sale['sale_id'], array('target' => '_blank'));?></td>
						<td class="sp_date text-center"><?php echo date(get_date_format(). ' '.get_time_format(),strtotime($unpaid_sale['sale_time']));?></td>
						<td class="sp_charge text-center">
							<?php 
							if(isset($exchange_name) && $exchange_name)
							{
								echo to_currency_as_exchange($unpaid_sale['payment_amount']*$exchange_rate);
							}
							else
							{
								echo to_currency($unpaid_sale['payment_amount']);								
							}
							?>
						</td>
						<td class="sp_comment text-center"><?php echo $unpaid_sale['comment']?></td>						
						<td class="sp_pay text-center">
							<?php echo form_open("sales/".((isset($unpaid_sale['paid']) && $unpaid_sale['paid'] == TRUE) ? "delete" : "pay")."_store_account_sale/".$unpaid_sale['sale_id']."/".to_currency_no_money($unpaid_sale['payment_amount']),array('class'=>'pay_store_account_sale_form', 'autocomplete'=> 'off')); ?>
								<button type="submit" class="btn <?php echo $btn_class; ?> pay_store_account_sale"><?php echo isset($unpaid_sale['paid']) && $unpaid_sale['paid'] == TRUE  ? lang('common_remove_payment') : lang('common_pay');?></button>
							</form>
						</td>
					</tr>
				<?php
				}
			}
			?>
		</tbody>
		</table>
			<?php
		?>
		
		<?php
			
		}			
		?>
	</div>
	<!-- /.Col-lg-8 @end of left Column -->

	<!-- col-lg-4 @start of right Column -->
	<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 no-padding-right">
		<div class="register-box register-right">

			<!-- Sale Top Buttons  -->
			<div class="sale-buttons">
				<!-- Extra links -->
				<div class="btn-group">
					<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="ion-android-more-horizontal"></i>
					</button>
					<ul class="dropdown-menu sales-dropdown" role="menu">						
						<?php if ($mode != 'store_account_payment') { ?>
							<?php if ($this->Employee->has_module_action_permission('giftcards', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<li>
								<?php echo 
								anchor("sales/new_giftcard",
									'<i class="ion-card"></i> '.lang('sales_new_giftcard'),
									array('class'=>'', 
										'title'=>lang('sales_new_giftcard')));
										?>
									</li>
							<?php } ?>
							
							<li>
							<?php echo 
							anchor("sales/suspended",
								'<i class="ion-ios-list-outline"></i> '.lang('sales_suspended_sales'),
								array('class'=>'', 
									'title'=>lang('sales_suspended_sales')));
									?>
								</li>
								<?php if($this->Employee->has_module_action_permission('deliveries', 'search', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<li>
								<?php echo 
								anchor("deliveries",
									'<i class="ion-ios-list-outline"></i> '.lang('deliveries_orders'),
									array('class'=>'', 
										'title'=>lang('deliveries_orders')));
										?>
								</li>
							<?php } ?>
							<?php
								if ($this->Employee->has_module_action_permission('reports', 'view_sales_generator', $this->Employee->get_logged_in_employee_info()->person_id))
								{
								?>
									<li>
									<?php echo 
									anchor("reports/sales_generator",
									'<i class="ion-search"></i> '.lang('sales_search_reports'),
									array('class'=>'', 
										'title'=>lang('sales_search_reports')));
									?> 
									</li>
								<?php } ?>
								
								<?php if ($this->config->item('customers_store_accounts')) { ?>
							
									<li>
										<?php echo anchor("sales/change_mode/store_account_payment/1",
											'<i class="ion-toggle-filled"></i> '.lang('common_store_account_payment'),
											array('class'=>'','title'=>lang('common_store_account_payment')));
										?>
									</li>		
							
								<?php } ?>
								
								<li>
									<?php echo anchor("sales/batch_sale/",
										'<i class="ion-bag"></i> '.lang('batch_sale'),
										array('class'=>'none suspended_sales_btn','title'=>lang('batch_sale')));
									?>
								</li>					
						<?php } ?>

						

						<li>
							<?php echo '<a href="#look-up-receipt" class="look-up-receipt" data-toggle="modal"><i class="ion-document"></i> '.lang('lookup_receipt').'</a>';?>
						</li>						
						
						<?php
						if ($last_sale_id = $this->Sale->get_last_sale_id())
						{
							echo '<li>';
							echo anchor("sales/receipt/$last_sale_id",
								'<i class="ion-document"></i> '.lang('sales_last_sale_receipt'),
								array('target' => '_blank','class'=>'look-up-receipt','title'=>lang('lookup_receipt')));
							
							echo '</li>';
						}
						?>

						<?php
						if ($this->Register->count_all($this->Employee->get_logged_in_employee_current_location_id()) > 1)
						{
						?>
							<li>
								<?php echo anchor(site_url('sales/clear_register'), '<i class="ion-eject"></i> '.lang('sales_change_register'),array('class'=>'')); ?>
							</li>
						<?php 
						} 
						?>
						<li><?php echo anchor(site_url('sales/customer_display/'.$this->Employee->get_logged_in_employee_current_register_id()), '<i class="ion-ios-monitor-outline"></i> '.lang('sales_customer_facing_display'),array('class'=>'', 'target' => '_blank')); ?></li>
						
						<li>
							<?php echo anchor(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
						</li>	
						
						<?php if ($this->config->item('track_cash')) { ?>
						<li><?php echo anchor(site_url('sales/register_add_subtract/add'), '<i class="ion-cash"></i> '.lang('sales_add_cash_to_register'),array('class'=>'')); ?></li>
						<li><?php echo anchor(site_url('sales/register_add_subtract/subtract'), '<i class="ion-log-out"></i> '.lang('common_remove_cash_from_register'),array('class'=>'')); ?></li>
						
						<li  class="danger">
							<?php echo anchor(site_url('sales/closeregister?continue=closeoutreceipt'), '<i class="ion-close-circled"></i> '.lang('sales_close_register'),array('class'=>'')); ?>
						</li>
						<?php } ?>
						
						<?php if (!is_on_demo_host()) { ?>
								
							<li>
							<?php if (!$this->config->item('test_mode') && !$this->config->item('disable_test_mode')) { ?>
								<?php echo anchor(site_url('sales/enable_test_mode'), '<i class="ion-ios-settings-strong"></i> '.lang('common_enable_test_mode'),array('class'=>'')); ?>
							<?php } elseif(!$this->config->item('disable_test_mode')) { ?>								
								<?php echo anchor(site_url('sales/disable_test_mode'), '<i class="ion-ios-settings-strong"></i> '.lang('common_disable_test_mode'),array('class'=>'')); ?>
							<?php } ?>
							</li>	
						<?php } ?>
						

					</ul>
				</div>
				<?php if(count($cart) > 0){ ?>
				<?php echo form_open("sales/cancel_sale",array('id'=>'cancel_sale_form', 'autocomplete'=> 'off')); ?>
				<?php if ($mode != 'store_account_payment') { ?>
					<?php if (!$this->sale_lib->get_change_sale_id()) { ?>
						
						<?php if ($customer_required_check && $suspended_sale_customer_required_check) { ?>
							<div class="btn-group">
								<button type="button" class="btn btn-suspended dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<i class="ion-pause"></i>
									<?php echo lang('sales_suspend_sale');?>
								</button>
								<ul class="dropdown-menu sales-dropdown" role="menu">
									<li><a href="#" id="layaway_sale_button"><i class="ion-pause"></i> <?php echo ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway'));?></a></li>
									<li><a href="#" id="estimate_sale_button"><i class="ion-help-circled"></i> <?php echo lang('common_estimate');?></a></li>
								
								</ul>
							</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
				<a href="#" class="btn btn-<?php echo $this->sale_lib->get_change_sale_id() ||  $this->sale_lib->get_suspended_sale_id() ? 'suspended' : 'cancel';?>"  id="cancel_sale_button" >
					<i class="ion-close-circled"></i>
					<?php echo $this->sale_lib->get_change_sale_id() ||  $this->sale_lib->get_suspended_sale_id() ? lang('common_cancel_edit') : lang('sales_cancel_sale');?>
				</a>
				
				<?php
				if (($this->sale_lib->get_change_sale_id() || $this->sale_lib->get_suspended_sale_id()) && $this->Employee->has_module_action_permission('sales', 'delete_sale', $this->Employee->get_logged_in_employee_info()->person_id))
				{
				?>
				<a href="javascript:void(0)" class="btn btn-cancel"  id="delete_sale_button" >
					<i class="ion-close-circled"></i>
					<?php echo lang('common_void_delete');?>
				</a>
				<?php
				}
				?>
			</form>
			<?php } ?>
			
		</div>

		<!-- If customer is added to the sale -->
		<?php if(isset($customer)) { ?>	

		<!-- Customer Badge when customer is added -->
		<div class="customer-badge">
			<div class="avatar">
				<img src="<?php echo $avatar; ?>" alt="">	
			</div>
			<div class="details">
			<?php if(!$this->config->item('hide_customer_recent_sales') && isset($customer)) { ?>
				<a href="<?php echo site_url('sales/customer_recent_sales/'.$customer_id); ?>"  data-toggle="modal" data-target="#myModal" class="name"><?php echo character_limiter(H($customer), 30); ?></a>
			<?php } else if(isset($customer)) { ?>
				<a href="<?php echo site_url('customers/view/'.$customer_id.'/1'); ?>" class="name"><?php echo character_limiter(H($customer), 30); ?></a>
			<?php }else { ?>
				<?php echo character_limiter(H($customer), 30); ?>
			<?php } ?>
					<?php if ($this->config->item('customers_store_accounts') && isset($customer_balance)) {?>
					<div class="<?php echo $is_over_credit_limit ? 'text-danger' : 'text-success'; ?> balance"><?php echo lang('sales_balance').': '.(isset($exchange_name) && $exchange_name ? (to_currency($customer_balance).' ('.(to_currency_as_exchange($customer_balance*$exchange_rate)).')') : to_currency($customer_balance));?></div>
					<?php } ?>
					
					<?php if (!$disable_loyalty) {?>
						<?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple' && isset($sales_until_discount)) {?>
						<div class="<?php echo $sales_until_discount > 0 ? 'text-danger' : 'text-success'; ?> sales_until_discount" ><?php echo lang('common_sales_until_discount').': '.to_quantity($sales_until_discount).($sales_until_discount <= 0 && !$redeem ?' ['.anchor('sales/redeem_discount', lang('sales_redeem'), array('id' => 'redeem_discount')).']' : ($redeem ? ' ['.anchor('sales/unredeem_discount', lang('sales_unredeem'), array('id' => 'unredeem_discount')).']' : '')) ?></div>
						<?php } ?>
					
						<?php if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && isset($points)) {?>
						<div class="<?php echo $points < 1 ? 'text-danger' : 'text-success'; ?> points"><?php echo lang('common_points').': '.to_quantity($points); ?></div>
						<?php } ?>
					<?php } ?>					

				<!-- Customer Email  -->
				<?php if(!empty($customer_email)) { ?>
				<span class="email">
					<a href="mailto:<?php echo $customer_email; ?>"><?php echo character_limiter(H($customer_email), 25); ?></a>
				</span>
				<?php } ?>

				<!-- Customer edit -->
				<?php echo anchor("customers/view/$customer_id/1", '<i class="ion-ios-compose-outline"></i>',  array('id' => 'edit_customer','class'=>'btn btn-edit btn-primary pull-right','title'=>lang('common_update'))).''; ?>
				
			</div>

		</div>
		<div class="customer-action-buttons  btn-group btn-group-justified ">
				
      <?php if($mode!='store_account_payment' && $this->Employee->has_module_action_permission('deliveries', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
          <a href="<?php echo site_url('sales/view_delivery_modal/') ?>" class="btn <?php echo (boolean)$delivery ? 'checked' : '';?>" id="open_delivery_modal" data-toggle="modal" data-target="#myModal">
              <i class="ion-android-car"></i>
              <?php echo lang('sales_delivery'); ?>
          </a>
      <?php } ?>
			
      <?php if(!empty($customer_email)) { ?>
          <a href="#" class="btn <?php echo (boolean)$email_receipt ? 'checked' : '';?>" id="toggle_email_receipt">
              <i class="ion-android-mail"></i>
              <?php echo lang('common_email_receipt'); ?>
          </a>
      <?php } else { ?>
          <a href="<?php echo site_url('customers/view/'.$customer_id.'/1');  ?>" class="btn" >
              <i class="ion-ios-compose-outline"></i>
             <?php echo lang('common_update'); ?>
          </a>
      <?php } ?>
					

			<?php
				echo form_checkbox(array(
						'name'=>'email_receipt',
						'id'=>'email_receipt',
						'value'=>'1',
						'class'       => 'email_receipt_checkbox hidden',
						'checked'=>(boolean)$email_receipt)
				);
					
				echo form_checkbox(array(
						'name'=>'delivery',
						'id'=>'delivery',
						'value'=>'1',
						'class' => 'delivery_checkbox hidden',
						'checked'=>(boolean)$delivery)
				);
		
			?>
				
			
			<?php echo ''.anchor("sales/delete_customer", '<i class="ion-close-circled"></i> '.lang('common_detach'),array('id' => 'delete_customer','class'=>'btn')); ?>
		</div>

		<?php }  else {  ?>

		<div class="customer-form">

			<!-- if the customer is not set , show customer adding form -->
			<?php echo form_open("sales/select_customer",array('id'=>'select_customer_form', 'autocomplete'=> 'off','class'=>'form-inline')); ?>
				<div class="input-group contacts">
					<span class="input-group-addon">
						<?php echo anchor("customers/view/-1/1","<i class='ion-person-add'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer','tabindex' => '-1')); ?>
					</span>
					<input type="text" id="customer" name="customer" class="add-customer-input keyboardLeft" data-title="<?php echo lang('common_customer_name'); ?>" placeholder="<?php echo lang('sales_start_typing_customer_name').($this->config->item('require_customer_for_sale') ? ' ('.lang('common_required').')' : '');?>">
				</div>
			</form>

		</div> 
		<?php } ?>
	</div>

	<div class="register-box register-summary paper-cut">

		<!-- Tiers if its greater than 1 -->
		<?php if (count($tiers) > 1) {  ?>
		<div class="tier-group">
			<a tabindex="-1" href="#" class="item-tier <?php $this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id) ? 'enable-click' : ''; ?>">
				<?php echo lang('sales_item_tiers'); ?>: <span class="selected-tier"><?php echo H($tiers[$selected_tier_id]); ?></span>
			</a>
			<?php if ($this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id)) {	?>
			<div class="list-group item-tiers">
				<?php foreach ($tiers as $key => $value) { ?>
				<a tabindex="-1" href="#" data-value="<?php echo $key; ?>" class="list-group-item"><?php echo H($value); ?></a>	
				<?php } ?>
			</div>						
			<?php } ?>
		</div>
		<?php  }  ?> 

		<!-- Tiers if its greater than 1 -->
		<?php if ($this->config->item('select_sales_person_during_sale')) {  ?>
		<div class="tier-group">
			<a href="#" class="select-sales-person <?php $this->config->item('select_sales_person_during_sale') ? 'enable-click' : ''; ?>">
				<?php echo lang('common_sales_person'); ?>:  <span class="selected-sales-person"><?php echo H($employees[$selected_sold_by_employee_id]); ?></span>
			</a>

			
			<div class="list-group select-sales-persons">
				<?php foreach ($employees as $key => $employee) { ?>
				<a href="#" data-value="<?php echo $key; ?>" class="list-group-item"><?php echo H($employee); ?></a>	
				<?php } ?>
			</div>						
		
		</div>
		<?php  }  ?> 
		
		<ul class="list-group">
		<?php if ($this->Employee->has_module_action_permission('sales', 'give_discount', $this->Employee->get_logged_in_employee_info()->person_id) && $mode != 'store_account_payment'){ ?>
			<li class="list-group-item global-discount-group">
				<div class="key"><?php echo lang('sales_global_sale_discount_percent').': '; ?>	</div>
				<div class="value pull-right">
					<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
						<a href="javascript:void(0)" id="discount_all_percent" class="edit-on-click editable-click" data-placement="<?php echo $discount_editable_placement; ?>" data-keyboard-type="numpad" data-value="<?php echo H(to_quantity($discount_all_percent)); ?>" data-name="discount_all_percent" data-url="<?php echo site_url('sales/discount_all'); ?>" data-emptytext="<?php echo H(lang('sales_set_discount')); ?>" data-placeholder="<?php echo H(lang('sales_set_discount')); ?>" data-title="<?php echo H(lang('sales_global_sale_discount_percent')); ?>"><?php echo isset($discount_all_percent) &&  $discount_all_percent > 0 ?  to_quantity($discount_all_percent) : '' ?></a>
					<?php } else { ?>
						<a href="#" id="discount_all_percent" class="xeditable"  data-validate-number="false" data-placement="<?php echo $discount_editable_placement; ?>" data-type="text"  data-pk="1" data-name="discount_all_percent" data-url="<?php echo site_url('sales/discount_all'); ?>" data-title="<?php echo H(lang('sales_global_sale_discount_percent')); ?>" data-emptytext="<?php echo H(lang('sales_set_discount')); ?>" data-placeholder="<?php echo H(lang('sales_set_discount')); ?>"><?php echo isset($discount_all_percent) &&  $discount_all_percent > 0 ?  to_quantity($discount_all_percent) : '' ?></a>
					<?php } ?>
					<?php 
					if ( isset($discount_all_percent) &&  $discount_all_percent > 0)
					{
						echo '%';
					}
					?>
				</div>
			</li>
			
			<li class="list-group-item global-discount-group">
				<div class="key"><?php echo lang('sales_global_sale_discount_fixed').': '; ?>	</div>
				 <?php
				 $symbol = "";
				 if(isset($discount_all_fixed) &&  $discount_all_fixed)
				 {
						$symbol = ($this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$');
				 }
				 ?>
				<div class="value pull-right"><span id="TEST"><?php echo $symbol; ?></span>
				<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
					<a href="javascript:void(0)" id="discount_all_flat" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_quantity($discount_all_fixed)); ?>" data-name="discount_all_flat" data-url="<?php echo site_url('sales/discount_all'); ?>" data-placement="<?php echo $discount_editable_placement; ?>" data-title="<?php echo H(lang('sales_global_sale_discount_fixed')); ?>" data-emptytext="<?php echo H(lang('sales_set_discount')); ?>" data-placeholder="<?php echo H(lang('sales_set_discount_fixed_or_percent'));?>"><?php echo isset($discount_all_fixed) &&  $discount_all_fixed ? $discount_all_fixed : ''; ?></a>
				<?php } else { ?>
					<a href="#" id="discount_all_flat" class="xeditable"  data-validate-number="false"  data-placement="<?php echo $discount_editable_placement; ?>" data-type="text"  data-pk="1" data-name="discount_all_flat" data-url="<?php echo site_url('sales/discount_all'); ?>" data-title="<?php echo H(lang('sales_global_sale_discount_fixed')); ?>" data-emptytext="<?php echo H(lang('sales_set_discount')); ?>" data-placeholder="<?php echo H(lang('sales_set_discount_fixed_or_percent'));?>"><?php echo isset($discount_all_fixed) &&  $discount_all_fixed ? $discount_all_fixed : ''; ?></a>
				<?php } ?>
					
				</div>
			</li>

			<?php if ($has_discount) { ?>
				
			<li class="list-group-item global-discount-group" id="discount-reason-group">
				<div class="key"><?php echo lang('common_discount_reason').': '; ?>	</div>
				<div class="value pull-right">
					<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
						<a href="javascript:void(0)" id="discount_reason" class="edit-on-click editable-click" data-placement="<?php echo $discount_editable_placement; ?>" data-value="<?php echo H($discount_reason); ?>" data-name="discount_reason" data-url="<?php echo site_url('sales/discount_reason'); ?>" data-emptytext="<?php echo H(lang('common_comments')); ?>" data-placeholder="<?php echo H(lang('common_discount_reason'));?>" data-title="<?php echo H(lang('common_discount_reason')); ?>"><?php echo isset($discount_reason) &&  $discount_reason ? $discount_reason : ''; ?></a>
					<?php } else { ?>
						<a href="#" id="discount_reason" class="xeditable"  data-validate-number="false"  data-placement="<?php echo $discount_editable_placement; ?>" data-type="textarea"  data-pk="1" data-name="discount_reason" data-url="<?php echo site_url('sales/discount_reason'); ?>" data-title="<?php echo H(lang('common_discount_reason')); ?>" data-emptytext="<?php echo H(lang('common_comments')); ?>" data-placeholder="<?php echo H(lang('common_discount_reason'));?>"><?php echo isset($discount_reason) &&  $discount_reason ? $discount_reason : ''; ?></a>
					<?php } ?>
				</div>
			</li>
			<?php } ?>
			
		<?php } ?>
	
		
		<li class="sub-total list-group-item">
			<span class="key"><?php echo lang('common_sub_total'); ?>:</span>
			<span class="value">
				<?php if (isset($exchange_name) && $exchange_name) { 
					echo to_currency_as_exchange($subtotal);
				?>
				<?php } else {  ?>
				<?php echo to_currency($subtotal); ?>				
				<?php
				}
				?>
				
				
			</span>
		</li>
		
		
		
		<?php foreach($taxes as $name=>$value) { ?>
		<li class="list-group-item">
			<span class="key">
				<?php if (!$is_tax_inclusive && $this->Employee->has_module_action_permission('sales', 'delete_taxes', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
					<?php echo anchor("sales/delete_tax/".rawurlencode($name),'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-tax remove'));?>

				<?php } ?>
				<?php echo $name; ?>:</td>
			</span>
			<span class="value pull-right">
				<?php if (isset($exchange_name) && $exchange_name) { 
					echo to_currency_as_exchange($value*$exchange_rate);
				?>
				<?php } else {  ?>
				<?php echo to_currency($value*$exchange_rate); ?>				
				<?php
				}
				?>
			</span>
		<?php }; ?>
		</ul>

		<div class="amount-block">
			<div class="total amount">
				<div class="side-heading">
					<?php echo lang('common_total'); ?>
				</div>
				<div class="amount total-amount" data-speed="1000" data-currency="<?php echo $this->config->item('currency_symbol'); ?>" data-decimals="<?php echo $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2; ?>">
					<?php if (isset($exchange_name) && $exchange_name) { 
						echo to_currency_as_exchange($total);
						?>
					<?php } else {  ?>
					<?php echo to_currency($total); ?>				
					<?php
					}
					?>
					
					</div>
			</div>
			<div class="total amount-due">
				<div class="side-heading">
					<?php echo lang('common_amount_due'); ?>
				</div>
				<div class="amount">
					<?php if (isset($exchange_name) && $exchange_name) { 
						echo to_currency_as_exchange($amount_due);
						?>
					<?php } else {  ?>
					<?php echo to_currency($amount_due); ?>				
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<!-- ./amount block -->

<?php
$exchange_rates = $this->Appconfig->get_exchange_rates()->result_array();
if(count($exchange_rates))
{
	$exchange_options = array('1|'.$this->config->item('currency_code').'|'.$this->config->item('currency_symbol').'|'.$this->config->item('currency_symbol_location').'|'.$this->config->item('number_of_decimals').'|'.$this->config->item('thousands_separator').'|'.$this->config->item('decimal_point') => $this->config->item('currency_code') ? $this->config->item('currency_code') : lang('common_default'));
	
	foreach($exchange_rates as $exchange_row)
	{
		$exchange_options[$exchange_row['exchange_rate'].'|'.$exchange_row['currency_code_to'].'|'.$exchange_row['currency_symbol'].'|'.$exchange_row['currency_symbol_location'].'|'.$exchange_row['number_of_decimals'].'|'.$exchange_row['thousands_separator'].'|'.$exchange_row['decimal_point']] = $exchange_row['currency_code_to'];
	}
	?>
	<div class="amount-block exchange">
		<div class="side-heading">
			<?php echo lang('common_exchange_to'); ?>
		</div>
		<div class="amount total-amount">
		<?php
		echo form_dropdown('exchange_to',$exchange_options,$exchange_details, 'id="exchange_to" class="form-control"');
		?>
	</div>
	</div>
<?php
}
?>

<?php if(count($cart) > 0){ ?> 
		<!-- Payment Applied -->
		<?php if(count($payments) > 0) { ?>
			<ul class="list-group payments">
				<?php foreach($payments as $payment_id=>$payment) { ?>
					<li class="list-group-item">
						<span class="key">
							<?php echo anchor("sales/delete_payment/$payment_id",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-payment remove','id'=>'delete_payment_'.$payment_id));?>
							<?php echo character_limiter($payment['payment_type'],20); ?> 
							<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
								<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>								
								<?php echo '<span class="giftcard_balance">['.lang('common_balance').' '.to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row)) - $payment['payment_amount']).']</span>';?>
							<?php } ?>
							
						</span>
						<span class="value">
							
							<?php
							if(isset($exchange_name) && $exchange_name)
							{
								echo  to_currency_as_exchange($payment['payment_amount']);
							}
							else
							{
							echo  to_currency($payment['payment_amount']);
							}
							?>
						</span>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		
		<?php if ($has_coupons_for_today) {?>
		<div class="add-coupon">
			<div class="side-heading"><?php echo lang('common_add_coupon'); ?></div>
			
			<div id="coupons" class="input-group" data-title="coupons">
				<span class="input-group-addon xl icon ion-ios-pricetags-outline"></span>
				<?php echo form_input(array('name'=>'coupons','id'=>'coupons','class'=>'coupon_codes input-lg add-input form-control', 'placeholder'=> '', 'data-title' => lang('common_enter_a_coupon'))); ?>
			</div>
			
		</div>
		<?php } ?>
		
		<!-- Add Payment -->
		<?php if ($customer_required_check) { ?>
			<div class="add-payment">
				<div class="side-heading"><?php echo lang('common_add_payment'); ?></div>
				
				<?php
					if (!$selected_payment)
					{
						 $selected_payment = $default_payment_type;
					}	
				?>
				<?php foreach ($payment_options as $key => $value) { 
					$active_payment =  ($selected_payment == $value) ? "active" : "";
				?>
					<a tabindex="-1" href="#" class="btn btn-pay select-payment <?php echo $active_payment; ?>" data-payment="<?php echo H($value); ?>">
					<?php echo $value; ?>
				</a>
				<?php } ?>

			<?php if(isset($customer_giftcards) && count($customer_giftcards) > 0) { ?>
			<div class="available-giftcards">
				<div class="side-heading"><?php echo lang('sales_available_giftcards') ?></div>
				<div class="list-group">
						<?php foreach ($customer_giftcards as $customer_giftcard) { ?>
							<a href="#" class="list-group-item customer-giftcard-item" data-giftcard-number="<?php echo $customer_giftcard->giftcard_number ?>">#<?php echo $customer_giftcard->giftcard_number ?> - <b><?php echo to_currency($customer_giftcard->value) ?></b></a>
						<?php } ?>
				</div>
			</div>
				<?php } ?>

			<?php echo form_open("sales/add_payment",array('id'=>'add_payment_form', 'autocomplete'=> 'off')); ?>

				<div class="input-group add-payment-form">
					<?php echo form_dropdown('payment_type',$payment_options,$selected_payment, 'id="payment_types" class="hidden"');?>
					<?php echo form_input(array('name'=>'amount_tendered','id'=>'amount_tendered','value'=>to_currency_no_money($amount_due),'class'=>'add-input numKeyboard form-control', 'data-title' => lang('common_payment_amount')));	?>
					<span class="input-group-addon">
						<a href="#" class="" id="add_payment_button"><?php echo lang('common_add_payment'); ?></a>
						<a href="#" class="hidden" id="finish_sale_alternate_button"><?php echo lang('sales_complete_sale'); ?></a>
					</span>
						
				</div>
					
			</form>
		</div>
	<?php 
		}  ?>
		
		
		<div class="comment-block">
			<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'2', 'class'=>'form-control', 'data-title' => lang('common_comments'), 'placeholder' => lang('common_comments'))); ?>
			<?php 		echo form_checkbox(array(
		'name'=>'show_comment_on_receipt',
		'id'=>'show_comment_on_receipt',
		'value'=>'1',
		'checked'=>(boolean)$show_comment_on_receipt)
	);
	echo '<label for="show_comment_on_receipt"><span></span>'.lang('sales_comments_receipt').'</label>'; ?>
		</div>
	<?php
	} 
	?>
			<!-- Finish Sale Button Handler -->
			<?php	
				$this->load->helper('sale');
				// Only show this part if there is at least one payment entered.
					if((count($payments) > 0 && !is_sale_integrated_cc_processing() && !is_sale_integrated_ebt_sale())){?>
					<div id="finish_sale" class="finish-sale">
						<?php echo form_open("sales/complete",array('id'=>'finish_sale_form', 'autocomplete'=> 'off')); ?>
						<?php							 
						if ($payments_cover_total && $customer_required_check)
						{
							echo "<input type='button' class='btn btn-success btn-large btn-block' id='finish_sale_button' value='".lang('sales_complete_sale')."' />";
						}
						echo form_close();
						?>
					</div>
				
				<?php }else{?>
				<div id="finish_sale" class="finish-sale">
					<?php echo form_open("sales/start_cc_processing?provider=".rawurlencode($this->Location->get_info_for_key('credit_card_processor')),array('id'=>'finish_sale_form', 'autocomplete'=> 'off')); ?>
					<?php
					if ($this->Location->get_info_for_key('enable_credit_card_processing'))
					{
						echo '<div id="credit_card_options" style="display: none;">';
						if (isset($customer) && $customer_cc_token && $customer_cc_preview)
						{
							echo form_checkbox(array(
								'name'=>'use_saved_cc_info',
								'id'=>'use_saved_cc_info',
								'value'=>'1',
								'checked'=>(boolean)$use_saved_cc_info)
							);
							echo '<label for="use_saved_cc_info"><span></span>'.lang('sales_use_saved_cc_info'). ' '.$customer_cc_preview.'</label>';

						
						}
						elseif(isset($customer))
						{
							echo form_checkbox(array(
								'name'=>'save_credit_card_info',
								'id'=>'save_credit_card_info',
								'value'=>'1',
								'checked'=>(boolean)$save_credit_card_info)
							);
							echo '<label for="save_credit_card_info"><span></span>'.lang('sales_save_credit_card_info').'</label>';
						}
						
						//If we are an EMV processor OR transcloud we need a way to prompt for card
						if ($cc_processor_parent_class_name == 'DATACAPUSBPROCESSOR' || $cc_processor_parent_class_name == 'DATACAPTRANSCLOUDPROCESSOR')
						{
							echo '<div style="text-align: center;">';
							
							if (is_system_integrated_ebt())
							{
								?>
									<div class="btn-group btn-group-lg .btn-group-justified" role="group" aria-label="..." id="ebt-balance-buttons" style="display: none;">
									  <a role="button" href="<?php echo site_url('sales/get_emv_ebt_balance/Foodstamp'); ?>" class="btn btn-default"><span class="icon ti-wallet"></span> <?php echo lang('sales_ebt_balance'); ?></a>
									  <a role="button" href="<?php echo site_url('sales/get_emv_ebt_balance/Cash'); ?>" class="btn btn-default"><span class="icon ti-money"></span> <?php echo lang('sales_ebt_cash_balance'); ?></a>
									</div>
								<?php
							}
							echo '</div>';
							
							echo form_checkbox(array(
								'name'=>'prompt_for_card',
								'id'=>'prompt_for_card',
								'value'=>'1',
								'checked'=>(boolean)$prompt_for_card)
							);
							echo '<label for="prompt_for_card"><span></span>'.lang('sales_prompt_for_card').'</label>';
							
							if (is_system_integrated_ebt())
							{							
								echo '<div id="ebt_voucher_toggle_holder">';
								echo form_checkbox(array(
									'name'=>'ebt_voucher_toggle',
									'id'=>'ebt_voucher_toggle',
									'value'=>'1',
									'checked'=>(boolean)$ebt_voucher)
								);
								echo '<label for="ebt_voucher_toggle"><span></span>'.lang('sales_enter_voucher').'</label>';
								echo '</div>';
							}
						}
						
						echo '<div id="ebt_voucher" style="display:none;">';
						echo '<input value="'.H($ebt_voucher_no).'" type="text" class="form-control text-center" name="ebt_voucher_no" id="ebt_voucher_no" placeholder="'.lang('sales_ebt_voucher_no').'">';
						echo '<input value="'.H($ebt_auth_code).'" type="text" class="form-control text-center" name="ebt_auth_code" id="ebt_auth_code" placeholder="'.lang('sales_ebt_auth_code').'">';
						echo '</div>';
						echo '</div>';
					}
					
					
					if (count($payments) > 0)
					{
						$this->load->helper('sale');
						if ($payments_cover_total && $customer_required_check || (is_sale_integrated_cc_processing() || is_sale_integrated_ebt_sale()))
						{
							echo "<input type='button' class='btn btn-success btn-large btn-block' id='finish_sale_button' value='".lang('sales_process_credit_card')."' />";
						}
					}
					echo form_close();
					?>
				</div>
			
			<?php } ?>
			
			<?php if($this->sale_lib->get_change_sale_id() || $this->config->item('change_sale_date_for_new_sale')) { ?>
			<div class="change-date">
				
			 <?php 	echo form_checkbox(array(
					'name'=>'change_sale_date_enable',
					'id'=>'change_sale_date_enable',
					'value'=>'1',
					'checked'=>(boolean)$change_sale_date_enable)
				);
				echo '<label for="change_sale_date_enable"><span></span>'.lang('sales_change_date').'</label>';

				?>
					
					<div id="change_sale_date_picker" class="input-group date datepicker" >
						<span class="input-group-addon"><i class="ion-calendar"></i></span>

					<?php echo form_input(array(
						'name'=>'change_sale_date',
						'id' => 'change_sale_date',
						'size'=>'8',
						'class' => 'form-control',
						'value'=> date(get_date_format()." ".get_time_format(), $change_sale_date ? strtotime($change_sale_date) : time()),
						)
					);?>       
				</div>
			</div>

			<?php } ?>
			<!-- End of complete sale button -->
	</div>
</div>
</div>

<a href="#" class="pull-right visible-lg" id="keyboard_toggle"><?php echo lang('sales_keyboard_help_title');?></a>

<div id="keyboardhelp" style="display: none;background-color:white;padding:12px;" title="<?php echo lang('sales_keyboard_help_title');?>">
  
  <div>
    <span>[F1,F3]  => <?php echo lang('common_start_new_sale');?></span><br />
  	<span>[F4]  => <?php echo lang('sales_completes_currrent_sale');?></span><br />
  	<span>[F2]  => <?php echo lang('sales_set_focus_item');?></span><br />
  	<span>[F7]  => <?php echo lang('sales_set_focus_payment');?></span><br />
  	<span>[ESC] => <?php echo lang('sales_esc_cancel_sale');?></span><br>
  </div>
  
</div>
</div>

<div class="modal fade look-up-receipt" id="look-up-receipt" role="dialog" aria-labelledby="lookUpReceipt" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
      	<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="lookUpReceipt"><?php echo lang('lookup_receipt') ?></h4>
	        </div>
	        <div class="modal-body">
	          	<?php echo form_open("sales/receipt_validate", array('class'=>'look-up-receipt-form','autocomplete'=> 'off')); ?>				
	          		<span class="text-danger text-center has-error look-up-receipt-error"></span>
					<input type="text" class="form-control text-center" name="sale_id" id="sale_id" placeholder="<?php echo lang('common_sale_id') ?>">
					<?php echo form_submit('submit_look_up_receipt_form',lang("lookup_receipt"),'class="btn btn-block btn-primary"'); ?>
				<?php echo form_close(); ?>
	        </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
if (isset($number_of_points_to_use) && $number_of_points_to_use > 0 && $this->config->item('prompt_to_use_points'))
{
?>
<script>
bootbox.confirm({
    message: <?php echo json_encode(lang("sales_use_points")); ?>,
    buttons: {
        confirm: {
            label: <?php echo json_encode(lang('common_yes'))?>,
            className: 'btn-primary'
        },
        cancel: {
            label: <?php echo json_encode(lang('common_no'))?>,
            className: 'btn-default'
        }
    },
    callback: function (result) {
		if(result)
		{
			$.post('<?php echo site_url("sales/add_payment");?>',{ amount_tendered:<?php echo json_encode($number_of_points_to_use);?>, payment_type:<?php echo json_encode(lang('common_points'));?>}, function(response)
			{
				$("#register_container").html(response);
			});
		}
    }
});

</script>

<?php
}
?>


<!-- price rules prompt-->
<?php
if (isset($number_to_add) && isset($item_to_add))
{
?>

<script>

var dialog = bootbox.confirm({
    message: <?php echo json_encode(lang("sales_price_rules_bogo_prompt")); ?>,
    buttons: {
        confirm: {
            label: <?php echo json_encode(lang('common_yes'))?>,
            className: 'btn-primary'
        },
        cancel: {
            label: <?php echo json_encode(lang('common_no'))?>,
            className: 'btn-default'
        }
    },
    callback: function (result) {
		if(result)
		{
			$.post('<?php echo site_url("sales/add");?>',{ item:<?php echo json_encode($item_to_add);?>, quantity:<?php echo json_encode($number_to_add);?>}, function(response)
			{
				$("#register_container").html(response);
			});
		}
    }
});
</script>

<?php
}
?>

<?php if($this->config->item('confirm_error_adding_item') && isset($error)) { ?>
	<script type="text/javascript">
	bootbox.alert(<?php echo json_encode($error); ?>, function(result)
	{
		setTimeout(function() {
    	     $('#item').focus();
		}, 50);
	});
	</script>
<?php } ?>

<?php if (!$this->config->item('disable_sale_notifications')) { ?>
	<script type="text/javascript">
		<?php
		if(isset($error) && !$this->config->item('confirm_error_adding_item'))
		{
			echo "show_feedback('error', ".json_encode($error).", ".json_encode(lang('common_error')).");";
		}


		if (isset($warning))
		{
			echo "show_feedback('warning', ".json_encode($warning).", ".json_encode(lang('common_warning')).");";

		}

		if (isset($success))
		{
			if (isset($success_no_message))
			{
				?>
				if (ENABLE_SOUNDS)
				{
					$.playSound(BASE_URL + 'assets/sounds/success');
				}
			<?php
			}
			else
			{
				echo "show_feedback('success', ".json_encode($success).", ".json_encode(lang('common_success')).");";
			}
		}
		
	  	if ($this->session->flashdata('cash_drawer_add_subtract_message')) 
		{
			echo "show_feedback('success', ".json_encode($this->session->flashdata('cash_drawer_add_subtract_message')).", ".json_encode(lang('common_success')).");";
	  	}
		
		?>
	</script>
<?php } ?>

<script type="text/javascript" language="javascript">
	
<?php
//EMV Usb Reset
if (isset($reset_params))
{
?>
 var data = {};
 <?php
 foreach($reset_params['post_data'] as $name=>$value)
 {
	 if ($name && $value)
	 {
	 ?>
	 data['<?php echo $name; ?>'] = '<?php echo $value; ?>';
 	 <?php 
	 }
 }
 ?>	

 mercury_emv_pad_reset(<?php echo json_encode($reset_params['post_host']); ?>, <?php echo $this->Location->get_info_for_key('listener_port'); ?>, data);
<?php
}

?>
	
	var submitting = false;		
			
	$(document).ready(function(){
		
		var hasSuggestions = false;
		$('.coupon_codes').tokenfield({
			tokens: <?php echo json_encode($coupon_codes); ?>,
			autocomplete: {
		 		source: '<?php echo site_url("sales/search_coupons");?>',
				delay: 100,
		 		autoFocus: true,
		 	 	minLength: 0,
		 		showAutocompleteOnFocus: false,
				create: function() {
				        $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
       					 	return $("<li class='item-suggestions'></li>")
           			 .data("item.autocomplete", item)
           			 .append('<a class="suggest-item">'+

									'<div class="name">' +
									item.label +
									'</div>'
								)
         				 .appendTo(ul);
   					}
				},
				open: function() {
					hasSuggestions = true;
				},
        close: function() {
          hasSuggestions = false;
        }
			}
		});

		$('.coupon_codes').on("change", function() {
			$.post('<?php echo site_url("sales/set_coupons");?>',{
				coupons: $('.coupon_codes').tokenfield('getTokens'),
				},
				function(response)
				{
					$("#register_container").html(response);
				});
		});

		$('.coupon_codes').on('tokenfield:createtoken', function (event) {
		    var existingTokens = $(this).tokenfield('getTokens');
		    $.each(existingTokens, function(index, token) {
		        if (token.value === event.attrs.value)
						{
	            event.preventDefault();
						}
		    });

				var menu = $("#coupons-tokenfield").data("uiAutocomplete").menu.element,
		    focused = menu.find("li:has(a.ui-state-focus)");

				if(focused.length !== 1 || !hasSuggestions)
				{
					event.preventDefault();
				}
		});
		
		$("#ebt_voucher_no").bind('keyup change', function()
		{
			$.post('<?php echo site_url("sales/set_ebt_voucher_no");?>', {ebt_voucher_no: $(this).val()});
		});
		
		$("#ebt_auth_code").bind('keyup change', function()
		{
			$.post('<?php echo site_url("sales/set_ebt_auth_code");?>', {ebt_auth_code: $(this).val()});
		});
	
		$( "#keyboard_toggle" ).click(function(e) {
			e.preventDefault();
			$( "#keyboardhelp" ).toggle();
		});
		
    $.fn.editable.defaults.mode = 'popup';     

	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen/1");?>');
	});

	$('.customer-giftcard-item').on('click',function (e) {
		e.preventDefault();
			$.post('<?php echo site_url("sales/set_selected_payment");?>', {payment: <?php echo json_encode(lang('common_giftcard')); ?>});

			$('#payment_types').val(<?php echo json_encode(lang('common_giftcard')); ?>);

			$('.select-payment').removeClass('active');
			$('a[data-payment=<?php echo json_encode(lang('common_giftcard')); ?>]').addClass('active');
			$("#amount_tendered").focus();

		$("#amount_tendered").val($(this).data('giftcard-number'));

	});


	$('.dismissfullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen/0");?>');
	});
	    
    $('.xeditable').editable({
    	validate: function(value) {
            if ($.isNumeric(value) == '' && $(this).data('validate-number')) {
					return <?php echo json_encode(lang('common_only_numbers_allowed')); ?>;
            }
        },
    	success: function(response, newValue) {
			 last_focused_id = $(this).attr('id');
 			 $("#register_container").html(response);
		}
    });

    $('.xeditable').on('shown', function(e, editable) {

		$(this).closest('.table-responsive').css('overflow-x','hidden');

    	editable.input.postrender = function() {
				//Set timeout needed when calling price_to_change.editable('show') (Not sure why)
				setTimeout(function() {
	         editable.input.$input.select();
			}, 200);
	    };
	});
	
	$('.xeditable').on('hidden', function(e, editable) {
		$(this).closest('.table-responsive').css('overflow-x','auto');
	});

	 $('.xeditable').on('hidden', function(e, editable) {
		 last_focused_id = $(this).attr('id');
		 $('#'+last_focused_id).focus();
		 $('#'+last_focused_id).select();
	 });
	
		<?php
		if (isset($price_zero) && $price_zero)
		{
		?>
		var price_to_change = $('#register a[data-name="price"]').first();
		price_to_change.editable('show');
		<?php
		}
		?>
		
		// Look up receipt form handling
		
		$('#look-up-receipt').on('shown.bs.modal', function() {
	          $('#sale_id').focus();
	    });
			
		$('.look-up-receipt-form').on('submit',function(e){
			e.preventDefault();

			$('.look-up-receipt-form').ajaxSubmit({
				success:function(response)
				{
					if(response.success)
					{
						window.location.href = '<?php echo site_url("sales/receipt"); ?>/'+response.sale_id;
					}
					else
					{
						$('.look-up-receipt-error').html(response.message);
					}
				},
				dataType:'json'
			});
		});

		//Set Item tier after selection
		$('.item-tiers a').on('click',function(e){
			e.preventDefault();

			$('.selected-tier').html($(this).text());
			$.post('<?php echo site_url("sales/set_tier_id");?>', {tier_id: $(this).data('value')}, function(response)
			{
				$('.item-tiers').slideToggle("fast", function()
				{
					$("#register_container").html(response);					
				});
			});
		});

		//Slide Toggle item tier options
		$('.item-tier').on('click',function(e){
			e.preventDefault();
			$('.item-tiers').slideToggle("fast");
		});

		//Set Item tier after selection
		$('.select-sales-persons a').on('click',function(e){
			e.preventDefault();

			$('.selected-sales-person').html($(this).text());
			$.post('<?php echo site_url("sales/set_sold_by_employee_id");?>', {sold_by_employee_id: $(this).data('value')}, function()
			{
				$('.select-sales-persons').slideToggle("fast");
				$("#register_container").load('<?php echo site_url("sales/reload"); ?>');
			});
		});

		//Slide Toggle item tier options
		$('.select-sales-person').on('click',function(e){
			e.preventDefault();
			$('.select-sales-persons').slideToggle("fast");
		});
		
		checkPaymentTypeGiftcard();
		checkPaymentTypePoints();
		checkPaymentTypeEbt();
		
		$('#toggle_email_receipt').on('click',function(e) {
			e.preventDefault();
	        var checkBox = $("#email_receipt");
	        checkBox.prop("checked", !checkBox.prop("checked")).trigger("change");
	        $(this).toggleClass('checked');
		});
		
		$('#email_receipt').change(function(e) 
		{	
			e.preventDefault();
			$.post('<?php echo site_url("sales/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
		});
		
		$('#delivery').change(function(e) 
		{	
			e.preventDefault();
			$.post('<?php echo site_url("sales/set_delivery");?>', {delivery: $('#delivery').is(':checked') ? '1' : '0'}, function(response) {
				if(!$('#delivery').is(':checked'))
				{
					$("#register_container").html(response);
				}
			});			
		});

		// Customer form script
		$('#item,#customer').click(function()
		{
			$(this).attr('value','');
		});


		// if #mode is changed
		$('.change-mode').click(function(e){
			e.preventDefault();
			if ($(this).data('mode') == "store_account_payment") { // Hiding the category grid
				$('#show_hide_grid_wrapper, #category_item_selection_wrapper').fadeOut();
			}else { // otherwise, show the categories grid
				$('#show_hide_grid_wrapper, #show_grid').fadeIn();
				$('#hide_grid').fadeOut();
			}
			$.post('<?php echo site_url("sales/change_mode");?>', {mode: $(this).data('mode')}, function(response)
			{
				$("#register_container").html(response);
			});
		});
		

		<?php if (!$this->agent->is_mobile()) { ?>
			<?php if (!$this->config->item('auto_focus_on_item_after_sale_and_receiving'))
			{ ?>
				if (last_focused_id && last_focused_id != 'item')
				{
					setTimeout(function(){$('#'+last_focused_id).focus(); $('#'+last_focused_id).select();}, 10);
				}
			<?php 
			}
			else
			{
			?>
				setTimeout(function(){$("#item").focus();}, 10);	
			<?php
			}
			?>
			$(document).off('focusin');
			$(document).focusin(function(event) 
			{
				last_focused_id = $(event.target).attr('id');
			});
		<?php 
		}
		else
		{
			if ($this->config->item('wireless_scanner_support_focus_on_item_field'))
			{
			?>
				setTimeout(function(){$("#item").focus();}, 10);				
			<?php } ?>
		
		
		<?php } ?>	
			
			$(".pay_store_account_sale_form").ajaxForm({target: "#register_container"});
			$( "#item" ).autocomplete({
		 		source: '<?php echo site_url("sales/item_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
					$( "#item" ).val(ui.item.value+'|FORCE_ITEM_ID|');
		 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit, success: itemScannedSuccess});

		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='item-suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="item-image">' +
									'<img src="' + item.image + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' +
									'<span class="attributes">' +
										'<?php echo lang("common_category"); ?>' + ' : <span class="value">' + (item.category ? item.category : <?php echo json_encode(lang('common_none')); ?>) + '</span>' +
									'</span>' +
								'</div>')
		             .appendTo(ul);
		     };


		<?php if(!isset($customer)) { ?>	
		

			$( "#customer" ).autocomplete({
		 		source: '<?php echo site_url("sales/customer_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("sales/select_customer");?>', {customer: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			           .append('<a class="suggest-item"><div class="avatar">' +
									'<img src="' + item.avatar + '" alt="">' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
									'<span class="email">' +
										item.subtitle + 
									'</span>' +
								'</div></a>')
		             .appendTo(ul);
		     };
	     
	     <?php } ?>
		
		$('#change_sale_date_enable').is(':checked') ? $("#change_sale_date_picker").show() : $("#change_sale_date_picker").hide(); 

		$('#change_sale_date_enable').click(function() {
			if( $(this).is(':checked')) {
				$("#change_sale_date_picker").show();
			} else {
				$("#change_sale_date_picker").hide();
			}
		});
		
		$('#comment').change(function() 
		{
			$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()});
		});
						
		$('#show_comment_on_receipt').change(function() 
		{
			$.post('<?php echo site_url("sales/set_comment_on_receipt");?>', {show_comment_on_receipt:$('#show_comment_on_receipt').is(':checked') ? '1' : '0'});
		});
		
				
		date_time_picker_field($("#change_sale_date"), JS_DATE_FORMAT + " "+JS_TIME_FORMAT);
		
      $("#change_sale_date").on("dp.change", function(e) {
			$.post('<?php echo site_url("sales/set_change_sale_date");?>', {change_sale_date: $('#change_sale_date').val()});			
      });
		
		//Input change
		$("#change_sale_date").change(function(){
			$.post('<?php echo site_url("sales/set_change_sale_date");?>', {change_sale_date: $('#change_sale_date').val()});			
		});

		$('#change_sale_date_enable').change(function() 
		{
			$.post('<?php echo site_url("sales/set_change_sale_date_enable");?>', {change_sale_date_enable: $('#change_sale_date_enable').is(':checked') ? '1' : '0'});
		});
		

		$('.delete-item, .delete-payment, #delete_customer, .delete-tax').click(function(event)
		{
			event.preventDefault();
			$("#register_container").load($(this).attr('href'));	
		});
		
		
		$('#redeem_discount,#unredeem_discount').click(function(event)
		{
			event.preventDefault();
			$("#register_container").load($(this).attr('href'));	
		});

		//Layaway Sale
		$("#layaway_sale_button").click(function(e)
		{
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang("sales_confirm_suspend_sale")); ?>, function(result)
			{
				if(result)
				{
					$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
						<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
							window.location = '<?php echo site_url("sales/suspend"); ?>';
							<?php }else { ?>
								$("#register_container").load('<?php echo site_url("sales/suspend"); ?>');
						<?php } ?>
					});
				}
			});
		});

		//Estimate Sale
		$("#estimate_sale_button").click(function(e)
		{
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang("sales_confirm_suspend_sale")); ?>, function(result)
			{
				if(result)
				{
					$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function() {
						<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
							window.location = '<?php echo site_url("sales/suspend/2"); ?>';
						<?php }else { ?>
							$("#register_container").load('<?php echo site_url("sales/suspend/2"); ?>');
						<?php } ?>
					});
				}
			});
		});

		//Cancel Sale
		$("#cancel_sale_button").click(function(e)
		{
			e.preventDefault();
			bootbox.confirm(<?php echo json_encode(lang("sales_confirm_cancel_sale")); ?>, function(result)
			{
				if (result)
				{
					$('#cancel_sale_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit});
				}
			});
		});
		//Select Payment
		$('.select-payment').on('click mousedown',selectPayment);
		
		<?php
		if (($selected_payment == lang('common_credit') && $this->Location->get_info_for_key('enable_credit_card_processing')) || ($selected_payment == lang('common_ebt') && $this->Location->get_info_for_key('enable_credit_card_processing')) || ($selected_payment == lang('common_ebt_cash') && $this->Location->get_info_for_key('enable_credit_card_processing')))
		{
		?>
			$("#credit_card_options").show();
						
		<?php	
		}
		?>
		
		<?php
		if ($selected_payment == lang('common_ebt') && $this->Location->get_info_for_key('enable_credit_card_processing'))
		{
		?>
			if ($('#ebt_voucher_toggle').is(':checked'))
			{
				$("#ebt_voucher").show();
			}
			else
			{
				$("#ebt_voucher").hide();			
			}
		<?php } ?>		
					
					
			<?php
			if (($selected_payment == lang('common_ebt') || $selected_payment == lang('common_ebt_cash')) && $this->Location->get_info_for_key('enable_credit_card_processing'))
			{
			?>
			$("#ebt-balance-buttons").show();
		<?php } ?>		

		function selectPayment(e)
		{
			e.preventDefault();
			$.post('<?php echo site_url("sales/set_selected_payment");?>', {payment: $(this).data('payment')});
			$('#payment_types').val($(this).data('payment'));
			<?php if ($this->Location->get_info_for_key('enable_credit_card_processing')) { ?>
			if($(this).data('payment') == <?php echo json_encode(lang('common_credit')) ?> || $(this).data('payment') ==  <?php echo json_encode(lang('common_ebt')) ?> || $(this).data('payment') ==  <?php echo json_encode(lang('common_ebt_cash')) ?> )
			{
				$("#credit_card_options").show();
			}
			else
			{
				$("#credit_card_options").hide();
			}
			
			if ($(this).data('payment') == <?php echo json_encode(lang('common_ebt')) ?> || $(this).data('payment') ==<?php echo json_encode(lang('common_ebt_cash')) ?>)
			{
				$("#ebt-balance-buttons").show();
			}
			else
			{
				$("#ebt-balance-buttons").hide();
			}
			if ($(this).data('payment') == <?php echo json_encode(lang('common_ebt')) ?>)
			{
				$("#ebt_voucher_toggle_holder").show();	
			}
			else
			{
				$("#ebt_voucher_toggle_holder").hide();	
			}
			
			if ($('#ebt_voucher_toggle').is(':checked') && $(this).data('payment') == <?php echo json_encode(lang('common_ebt')) ?>)
			{
				$("#ebt_voucher").show();
			}
			else
			{
				$("#ebt_voucher").hide();		
			}

			<?php } ?>
			// start_cc_processing
			$('.select-payment').removeClass('active');
			$(this).addClass('active');
			$("#amount_tendered").focus();
			$("#amount_tendered").attr('placeholder','');

			checkPaymentTypeGiftcard();
			checkPaymentTypePoints();
			checkPaymentTypeEbt();
		}

		//Add payment to the sale 
		$("#add_payment_button").click(function(e)
		{
			e.preventDefault();

			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: addPaymentSalesBeforeSubmit});
		});
		
		$('#select_customer_form').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
	 			$('#select_customer_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit});
			}
		});
		
		$('#add_item_form').ajaxForm({target: "#register_container", beforeSubmit: salesBeforeSubmit, success: itemScannedSuccess});
		
		$('#add_item_form').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
	 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit, success: itemScannedSuccess});
			}
		});
		
		//Add payment to the sale when hit enter on amount tendered input
		$('#amount_tendered').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
				
				//Quick complete possible
				if ($("#finish_sale_alternate_button").is(":visible"))
				{
					$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: addPaymentSalesBeforeSubmit, complete: function()
					{
						$('#finish_sale_button').trigger('click');
					}});
				}
				else
				{
					$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: addPaymentSalesBeforeSubmit});
				}
			}
		});

		//Select all text in the input when input is clicked
		$("input:text, textarea").not(".description,#comment").click(function() {
			$(this).select();
		});
		
		// Finish Sale button
		$("#finish_sale_button").click(function(e)
		{
			e.preventDefault();
			
			var confirm_messages = [];
			
			//Prevent double submission of form
			$("#finish_sale_button").hide();
			$('#grid-loader').show();
			
			
			<?php if ($this->sale_lib->get_payment_amount(lang('common_store_account')) >0) { ?> 
 				<?php if ($is_over_credit_limit && $mode!='store_account_payment' && !$this->config->item('disable_store_account_when_over_credit_limit')) { ?>
					confirm_messages.push(<?php echo json_encode(lang('sales_over_credit_limit_warning')); ?>);					
					<?php }elseif($is_over_credit_limit && $mode!='store_account_payment' && $this->config->item('disable_store_account_when_over_credit_limit')) {
						echo "show_feedback('error', ".json_encode(lang('sales_over_credit_limit_error')).", ".json_encode(lang('common_error')).");";
						echo '$("#finish_sale_button").show();';
						echo "$('#grid-loader').hide();";
						echo 'return;';
					} ?>
				<?php } ?>
			<?php if(!$payments_cover_total) { ?>
				confirm_messages.push(<?php echo json_encode(lang('sales_payment_not_cover_total_confirmation')); ?>);
			<?php } ?>
			
			<?php if (!$this->config->item('disable_confirmation_sale')) { ?>
				confirm_messages.push(<?php echo json_encode(lang("sales_confirm_finish_sale")); ?>);
			<?php } ?>
				
				if (confirm_messages.length)						
				{
					bootbox.confirm(confirm_messages.join("<br />"), function(result)
					{
						if (result)
						{
							finishSale();
						}
						else
						{
							//Bring back submit and unmask if fail to confirm
							$("#finish_sale_button").show();
							$('#grid-loader').hide();
						}
					});
				}
				else
				{
					finishSale();
				}
		});
				
		<?php if (!$this->config->item('disable_quick_complete_sale')) { ?>
		
			if ($("#payment_types").val() == <?php echo json_encode(lang('common_giftcard')); ?>)
			{
				$('#finish_sale_alternate_button').removeClass('hidden');
				$('#add_payment_button').addClass('hidden');
			}
			else if($("#payment_types").val() == <?php echo json_encode(lang('common_points')); ?>)
			{
				$('#finish_sale_alternate_button').addClass('hidden');
				$('#add_payment_button').removeClass('hidden');
			}
			else
			{
				if ($('#amount_tendered').val()>=<?php echo $amount_due; ?>)
				{
					$('#finish_sale_alternate_button').removeClass('hidden');
					$('#add_payment_button').addClass('hidden');
				}
				else
				{
					$('#finish_sale_alternate_button').addClass('hidden');
					$('#add_payment_button').removeClass('hidden');
				}
			}
		
		
		$('#amount_tendered').on('input',function(){
			if ($("#payment_types").val() == <?php echo json_encode(lang('common_giftcard')); ?>)
			{
				$('#finish_sale_alternate_button').removeClass('hidden');
				$('#add_payment_button').addClass('hidden');
			}
			else if($("#payment_types").val() == <?php echo json_encode(lang('common_points')); ?>)
			{
				$('#finish_sale_alternate_button').addClass('hidden');
				$('#add_payment_button').removeClass('hidden');
			}
			else
			{
				if ($('#amount_tendered').val()>=<?php echo $amount_due; ?>)
				{
					$('#finish_sale_alternate_button').removeClass('hidden');
					$('#add_payment_button').addClass('hidden');
				}
				else
				{
					$('#finish_sale_alternate_button').addClass('hidden');
					$('#add_payment_button').removeClass('hidden');
				}
			}
			
		});

		$('#finish_sale_alternate_button').on('click',function(e){
			e.preventDefault();
			
			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: salesBeforeSubmit, complete: function()
			{
				$('#finish_sale_button').trigger('click');
			}});
		});		
		
		<?php } ?>
		
		
		
		function show_grid() {
			$("#category_item_selection_wrapper").promise().done(function(){
				$("#category_item_selection_wrapper").slideDown();
				$('.show-grid').addClass('hidden');
				$('.hide-grid').removeClass('hidden');
			});
		}
		
		function hide_grid() {
			$("#category_item_selection_wrapper").promise().done(function(){
				$("#category_item_selection_wrapper").slideUp();
				$('.hide-grid').addClass('hidden');
				$('.show-grid').removeClass('hidden');
			});
		}
		
		// Show or hide item grid
		$("#show_grid, .show-grid").on('click',function(e)
		{
			e.preventDefault();
		  if (!$(':animated').length) {
		  }
			
			show_grid();
		});
		
		$("#hide_grid,#hide_grid_top, .hide-grid").on('click', function(e)
		{
			e.preventDefault();
		  if (!$(':animated').length) {
		  }
			
			hide_grid();
		});
		
		
	
		// Save credit card info
		$('#save_credit_card_info').change(function() 
		{
			$.post('<?php echo site_url("sales/set_save_credit_card_info");?>', {save_credit_card_info:$('#save_credit_card_info').is(':checked') ? '1' : '0'});
		});

		// Use saved cc info
		$('#use_saved_cc_info').change(function() 
		{
			$.post('<?php echo site_url("sales/set_use_saved_cc_info");?>', {use_saved_cc_info:$('#use_saved_cc_info').is(':checked') ? '1' : '0'});
		});

		// Prompt for cc info (EMV integration only)
		$('#prompt_for_card').change(function() 
		{
			$.post('<?php echo site_url("sales/set_prompt_for_card");?>', {prompt_for_card:$('#prompt_for_card').is(':checked') ? '1' : '0'});
		});

		$('#ebt_voucher_toggle').change(function() 
		{
			if($('#ebt_voucher_toggle').is(':checked'))
			{
				$("#ebt_voucher").show();
			}
			else
			{
				$("#ebt_voucher").hide();				
			}
			
			$.post('<?php echo site_url("sales/set_ebt_voucher");?>', {ebt_voucher:$('#ebt_voucher_toggle').is(':checked') ? '1' : '0'});
		});
		
		<?php if (isset($cart_count)) { ?>
	      	$('.cart-number').html(<?php echo $cart_count; ?>);
		<?php } ?>
		  
		
	});
	// end of document ready


// Re-usable Functions 

	function checkPaymentTypeGiftcard()
	{
		if ($("#payment_types").val() == <?php echo json_encode(lang('common_giftcard')); ?>)
		{
			$("#amount_tendered").val('');
			$("#amount_tendered").attr('placeholder',<?php echo json_encode(lang('sales_swipe_type_giftcard')); ?>);
		
				<?php if (!$this->config->item('disable_giftcard_detection')) { ?>
					giftcard_swipe_field($("#amount_tendered"));
					<?php
				}
				?>
		}
	}
	
	function checkPaymentTypeEbt()
	{
		if ($("#payment_types").val() == <?php echo json_encode(lang('common_ebt')); ?> || $("#payment_types").val() == <?php echo json_encode(lang('common_wic')); ?>)
		{
			$("#amount_tendered").val(<?php echo json_encode(to_currency_no_money($ebt_total)); ?>);
		}		
	}

	function checkPaymentTypePoints()
	{
		if ($("#payment_types").val() == <?php echo json_encode(lang('common_points')); ?>)
		{
			$("#amount_tendered").val('');
			$("#amount_tendered").attr('placeholder',<?php echo json_encode(lang('sales_enter_amount_of_points')); ?>);
		}
	}

	function salesBeforeSubmit(formData, jqForm, options)
	{
		if (submitting)
		{ 	
			return false; 
		}
		submitting = true;
		
		<?php if(isset($cart_count)) { ?>
			$('.cart-number').html(<?php echo $cart_count; ?>);
		<?php } ?>
		 $("#ajax-loader").show();
		 $("#add_payment_button").hide();
		$("#finish_sale_button").hide();
	}
	
	function addPaymentSalesBeforeSubmit(formData, jqForm, options)
	{
		if (submitting)
		{ 	
			return false; 
		}
		submitting = true;
		
		if (<?php echo $amount_due; ?> == 0 )
		{
			bootbox.confirm(<?php echo json_encode(lang("sales_no_amount_due_confirm")); ?>, function(result)
			{
				if(result)
				{
					$('#add_payment_form').ajaxSubmit({target: "#register_container"});
				}
			});
			
			submitting = false;
			//Prevent form form submitting
			return false;
		}
		
		<?php if(isset($cart_count)) { ?>
			$('.cart-number').html(<?php echo $cart_count; ?>);
		<?php } ?>
		 $("#ajax-loader").show();
		 $("#add_payment_button").hide();
		$("#finish_sale_button").hide();		
	}

	function itemScannedSuccess(responseText, statusText, xhr, $form)
	{
		setTimeout(function(){$('#item').focus();}, 10);
	}
	
	function finishSale()
	{
		if ($("#comment").val())
		{
			$.post('<?php echo site_url("sales/set_comment");?>', {comment: $('#comment').val()}, function()
			{
				$('#finish_sale_form').submit();						
			});						
		}
		else
		{
			$('#finish_sale_form').submit();						
		}
		
	}
	
	<?php 
	if(isset($prompt_convert_sale_to_return) && $prompt_convert_sale_to_return == TRUE)
	{
	?>
	
	bootbox.confirm({
	    message: <?php echo json_encode(lang("sales_confirm_convert_sale_to_return")); ?>,
	    buttons: {
	        confirm: {
	            label: <?php echo json_encode(lang('common_yes'))?>,
	            className: 'btn-primary'
	        },
	        cancel: {
	            label: <?php echo json_encode(lang('common_no'))?>,
	            className: 'btn-default'
	        }
	    },
	    callback: function (result) {
			if(result)
			{
				$.get('<?php echo site_url("sales/convert_sale_to_return");?>', function(response)
				{
					$("#register_container").html(response);
				});
			}
	    }
	});
	
	<?php
	}
	?>
	
	
	<?php 
	if(isset($prompt_convert_return_to_sale) && $prompt_convert_return_to_sale == TRUE)
	{
	?>
	
	bootbox.confirm({
	    message: <?php echo json_encode(lang("sales_confirm_convert_return_to_sale")); ?>,
	    buttons: {
	        confirm: {
	            label: <?php echo json_encode(lang('common_yes'))?>,
	            className: 'btn-primary'
	        },
	        cancel: {
	            label: <?php echo json_encode(lang('common_no'))?>,
	            className: 'btn-default'
	        }
	    },
	    callback: function (result) {
			if(result)
			{
				$.get('<?php echo site_url("sales/convert_return_to_sale");?>', function(response)
				{
					$("#register_container").html(response);
				});
			}
	    }
	});
	
	<?php
	}
	?>
	
	$("#exchange_to").change(function()
	{
		var rate = $(this).val();
		$.post('<?php echo site_url("sales/exchange_to");?>',{'rate': rate}, function(response)
		{
			$("#register_container").html(response);
		});
	});
	
	$("#delete_sale_button").click(function()
	{
		bootbox.confirm({
		    message: <?php echo json_encode(lang("sales_confirm_void_delete")); ?>,
		    buttons: {
		        confirm: {
		            label: <?php echo json_encode(lang('common_yes'))?>,
		            className: 'btn-primary'
		        },
		        cancel: {
		            label: <?php echo json_encode(lang('common_no'))?>,
		            className: 'btn-default'
		        }
		    },
		    callback: function (result) {
				if(result)
				{
					var post_data = [];
					
			    post_data.push({
					'name': 'sales_void_and_refund_credit_card', 
					'value': <?php echo json_encode($was_cc_sale); ?>
				 });

			    post_data.push({
					'name': 'sales_void_and_cancel_return', 
					'value': <?php echo json_encode($was_cc_return); ?>
				 });

			    post_data.push({
					'name': 'do_delete', 
						'value': 1
				 });

			    post_data.push({
					'name': 'clear_sale', 
						'value': 1
				 	});
					
				 post_submit('<?php echo site_url("sales/delete/$sale_id_of_edit_or_suspended_sale"); ?>', 'POST',post_data);
				}
		   }
	
	});
});

function post_submit(action, method, values) 
{
    var form = $('<form/>', {
        action: action,
        method: method
    });
    $.each(values, function() {
        form.append($('<input/>', {
            type: 'hidden',
            name: this.name,
            value: this.value
        }));    
    });
    form.appendTo('body').submit();
}

</script>
<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
<script>COMMON_NONE = <?php echo json_encode(lang('common_none')); ?>;</script>
<script src="<?php echo base_url().'assets/js/onscreen_keyboard.js'. '?' . ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
<?php } ?>