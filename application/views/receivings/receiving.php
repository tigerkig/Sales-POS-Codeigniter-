<a tabindex="-1" href="#" class="dismissfullscreen <?php echo !$fullscreen ? 'hidden' : ''; ?>"><i class="ion-close-circled"></i></a>
	<?php if($this->receiving_lib->get_change_recv_id()) { ?>
		<div class="alert alert-danger">
			<?php echo lang('receivings_editing_recv'); ?> <strong><?php echo 'RECV '.$this->receiving_lib->get_change_recv_id(); ?></strong>
		</div>
	<?php } ?>

<div class="row register">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12 no-padding-right no-padding-left">
		<div class="register-box register-items-form">
			
			<div class="item-form">
				<!-- Item adding form -->
				<?php echo form_open("receivings/add",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
					<div class="input-group input-group-mobile contacts">
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i> <span class='register-btn-text'>".lang('common_new_item')."</span>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item-mobile')); ?>
						</span>
						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i><span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','title'=>$modes[$mode], 'id' => 'register-mode-mobile', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo $key; ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php } 
							  } ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons <?php echo $mode == 'store_account_payment' ? 'hidden': '' ;?>">
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_show_grid')."</span>", array('class'=>'none show-grid','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> <span class='register-btn-text'> ".lang('common_hide_grid')."</span>", array('class'=>'none hide-grid hidden','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>

					<div class="input-group contacts  register-input-group">
					<!-- Css Loader  -->
						<div class="spinner" id="ajax-loader" style="display:none">
						  <div class="rect1"></div>
						  <div class="rect2"></div>
						  <div class="rect3"></div>
						</div>
						<span class="input-group-addon">
							<?php echo anchor("items/view/-1/1/receiving","<i class='icon ti-pencil-alt'></i>", array('class'=>'none add-new-item','title'=>lang('common_new_item'), 'id' => 'new-item')); ?>
						</span>
						
						<input type="text" id="item" name="item" <?php echo  ($mode=="store_account_payment") ? 'disabled="disabled"' : '' ?> class="add-item-input pull-left keyboardTop" placeholder="<?php echo lang('common_start_typing_item_name'); ?>" data-title="<?php echo lang('common_item_name'); ?>">
						

						<div class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
							<?php echo anchor("#","<i class='icon ti-shopping-cart'></i>".$modes[$mode], array('class'=>'none active','title'=>$modes[$mode], 'id' => 'register-mode', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
					        <ul class="dropdown-menu sales-dropdown">
					        <?php foreach ($modes as $key => $value) {
					        	if($key!=$mode){
					        ?>
					        	<li><a tabindex="-1" href="#" data-mode="<?php echo $key; ?>" class="change-mode"><?php echo $value;?></a></li>
					        <?php } 
							  } ?>
        					</ul>
						</div>
						
						<span class="input-group-addon grid-buttons <?php echo $mode == 'store_account_payment' ? 'hidden': '' ;?>">
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_show_grid'), array('class'=>'none show-grid','title'=>lang('common_show_grid'))); ?>
							<?php echo anchor("#","<i class='icon ti-layout'></i> ".lang('common_hide_grid'), array('class'=>'none hide-grid hidden','title'=>lang('common_hide_grid'))); ?>
						</span>
					</div>
				</form>
			</div>

		</div>
		<!-- /.Item Form -->

			<!-- Register Items. @contains : Items table -->
		<div class="register-box register-items paper-cut">
			<div class="register-items-holder">
				
					<?php if ($mode != 'store_account_payment') { ?>	
					<table id="register" class="table table-hover">

						<thead>
							<tr class="register-items-header">
								<th></th>
								<th class="item_name_heading"><?php echo lang('receivings_item_name'); ?></th>
								<th class="sales_price"><?php echo lang('receivings_cost'); ?></th>
								<th class="sales_quantity"><?php echo lang('common_quantity'); ?></th>
								<th class="sales_discount"><?php echo lang('receivings_discount'); ?></th>
								<th><?php echo lang('receivings_total'); ?></th>								
							</tr>
						</thead>

				
					<tbody class="register-item-content">
						<?php
						$cart_count = 0;
						if(count($cart)==0)	{ ?>
						<tr class="cart_content_area">
							<td colspan='6'>
								<div class='text-center text-warning' > <h3><?php echo lang('common_no_items_in_cart'); ?> <span class="flatRedc"> [<?php echo lang('module_receivings') ?>]</span></h3></div>
							</td>
						</tr>
						<?php 
						}
						else
						{
						 foreach(array_reverse($cart, true) as $line=>$item) { 
							 
							 if ($item['quantity'] > 0 && $item['name'] != lang('common_store_account_payment'))
							 {
	 						 	$cart_count = $cart_count + $item['quantity'];
							 }
							 
							?>
							<tr class="register-item-details">
								<td class="text-center"> <?php echo anchor("receivings/delete_item/$line",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-item'));?> </td>
								<td> 
									<a tabindex = "-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" class="register-item-name" ><?php echo H($item['name']); ?><?php echo $item['size'] ? ' ('.H($item['size']).')': ''; ?></a>
								</td>
								
								
								<td class="text-center">
									<?php if ($items_module_allowed) { ?>
										<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
											<a href="javascript:void(0)" id="price_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
										<?php } else { ?>
											<a href="#" id="price_<?php echo $line;?>" class="xeditable xeditable-price" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency($item['price'],10); ?></a>
										<?php } ?>
									<?php } else { 
											echo to_currency($item['price']); 
									 }	?>
								</td>

								<td class="text-center">
									<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
										<a href="javascript:void(0)" id="quantity_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_quantity($item['quantity'])); ?>" data-name="quantity" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
									<?php } else { ?>
										<a href="#" id="quantity_<?php echo $line; ?>" class="xeditable" data-type="text"  data-validate-number="true" data-value="<?php echo H(to_quantity($item['quantity'])); ?>" data-pk="1" data-name="quantity" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_quantity') ?>"><?php echo to_quantity($item['quantity']); ?></a>
									<?php } ?>
								</td>
																
								<td class="text-center">
									<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
										<a href="javascript:void(0)" id="discount_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H($item['discount']); ?>" data-name="discount" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>
									<?php } else { ?>
										<a href="#" id="discount_<?php echo $line;?>" class="xeditable" data-type="text" data-validate-number="true"  data-pk="1" data-name="discount" data-value="<?php echo H($item['discount']); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo lang('common_discount_percent') ?>"><?php echo to_quantity($item['discount']); ?>%</a>
									<?php } ?>
								</td>
								<td class="text-center"><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100, 10); ?></td>
							</tr>
							<tr class="register-item-bottom">
								<td>&nbsp;</td>
								<td colspan="5">
									<dl class="register-item-extra-details dl-horizontal">
										
										<?php if ($this->receiving_lib->get_suspended_receiving_id()) {?>
											<dt><?php echo lang('common_qty_received'); ?></dt>
											<dd><a href="#" id="quantity_received_<?php echo $line;?>" class="xeditable" data-type="text" data-validate-number="true" data-pk="1" data-name="quantity_received" data-value="<?php echo H(to_quantity($item['quantity_received'])); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_qty_received')); ?>"><?php echo H(to_quantity($item['quantity_received']));?></a></dd>
										<?php } ?>
										
										<?php if (isset($item['item_id']) && $item['item_id']) 
										{ 
											$item_location_info = $this->Item_location->get_info($item['item_id'], false, true);
											$cur_quantity = $item_location_info->quantity;
											
											?>
											<dt><?php echo lang('common_stock'); ?></dt>
											<dd><?php echo to_quantity($cur_quantity); ?></dd>
											
											<?php if ($this->Employee->has_module_action_permission('sales', 'edit_sale_price', $this->Employee->get_logged_in_employee_info()->person_id)) {	?>
												<dt><?php echo lang('common_unit_price'); ?></dt>
												<dd>
													<a href="#" id="selling_price_<?php echo $line;?>" class="xeditable" data-type="text" data-pk="1" data-name="selling_price" data-value="<?php echo to_currency_no_money($item['selling_price'], 10); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_unit_price')); ?>"><?php echo to_currency_no_money($item['selling_price'], 10) ?></a>
												</dd>
												
												
											<?php } ?>
											
										<?php } ?>
										
										
										<?php
										if ($this->config->item('calculate_average_cost_price_from_receivings'))
										{
										?>
											<dt><?php echo lang('receivings_cost_price_preview'); ?></dt>
											<dd><?php echo $item['cost_price_preview']; ?></dd>
										<?php
										}
										?>
										
										
									  <dt><?php echo lang('common_description'); ?></dt>
									  <dd>
										  <?php
											if ($item['description']!='')
											{
												echo $item['description'];
											}
											else
											{
												echo lang('common_none');
											}
											
										?>
									</dd>
									
										<?php if ($item['expire_date']) {?>
										  <dt><?php echo lang('common_expire_date'); ?></dt>
										  <dd><a href="#" id="expire_date_<?php echo $line;?>" class="expire_date" data-type="combodate" data-template="<?php echo get_js_date_format(); ?>" data-pk="1" data-name="expire_date" data-value="<?php echo date('Y-m-d', strtotime($item['expire_date'])); ?>" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_expire_date')); ?>"><?php echo H($item['expire_date']);?></a></dd>
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
											echo array_key_exists('item_number', $item) ? H($item['item_number']) : lang('common_none'); 
											break;
				
											case 'product_id':
											echo array_key_exists('product_id', $item) ? H($item['product_id']) : lang('common_none'); 
											break;
				
											case 'id':
											echo array_key_exists('item_id', $item) ? H($item['item_id']) : lang('common_none'); 
											break;
						
											default:
											echo array_key_exists('item_number', $item) ? H($item['item_number']) : lang('common_none'); 
											break;
										}
										?>
									</dd>	 
										 
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
							<th ><?php echo lang('receivings_item_name'); ?></th>
							<th ><?php echo lang('common_payment_amount'); ?></th>
						</tr>
					</thead>
					<tbody id="cart_contents">
						<?php
						$cart_count = 0;
						foreach(array_reverse($cart, true) as $line=>$item)	
						{
							?>
						 							
							<tr id="reg_item_top" >
								<td class="text text-center text-success"><a tabindex = "-1" href="<?php echo isset($item['item_id']) ? site_url('home/view_item_modal/'.$item['item_id']) : site_url('home/view_item_kit_modal/'.$item['item_kit_id']) ; ?>" data-toggle="modal" data-target="#myModal" ><?php echo H($item['name']); ?></a></td>
								<td class="text-center">
									<?php
									echo form_open("receivings/edit_item/$line", array('class' => 'line_item_form', 'autocomplete'=> 'off')); 	

										?>
										
										<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
											<a href="javascript:void(0)" id="price_<?php echo $line;?>" class="edit-on-click editable-click" data-keyboard-type="numpad" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency_no_money($item['price'],10); ?></a>
										<?php } else { ?>
											<a href="#" id="price_<?php echo $line; ?>" class="xeditable" data-validate-number="true" data-type="text" data-value="<?php echo H(to_currency_no_money($item['price'],10)); ?>" data-pk="1" data-name="price" data-url="<?php echo site_url('receivings/edit_item/'.$line); ?>" data-title="<?php echo H(lang('common_price')); ?>"><?php echo to_currency_no_money($item['price'],10); ?></a>
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
			if (!empty($unpaid_store_account_receivings))
			{
			?>
			<table id="unpaid_sales" class="table table-hover table-condensed">
				<thead>
					<tr class="register-items-header">
						<th class="sp_sale_id"><?php echo lang('receivings_id');?></th>
						<th class="sp_date"><?php echo lang('common_date');?></th>
						<th class="sp_charge"><?php echo lang('common_total_charge_to_account');?></th>
						<th class="sp_comment"><?php echo lang('common_comment');?></th>
						<th class="sp_pay"><?php echo lang('common_pay');?></th>
					</tr>
				</thead>
				
				<tbody id="unpaid_sales_data">
				
			<?php
				foreach($unpaid_store_account_receivings as $unpaid_receiving)
				{
					
				$row_class = isset($unpaid_receiving['paid']) && $unpaid_receiving['paid'] == TRUE ? 'success' : 'active';
				$btn_class = isset($unpaid_receiving['paid']) && $unpaid_receiving['paid'] == TRUE ? 'btn-danger' : 'btn-primary';
				?>
					<tr class="<?php echo $row_class; ?>">
						<td class="sp_receiving_id text-center"><?php echo anchor('receivings/receipt/'.$unpaid_receiving['receiving_id'],'RECV '.$unpaid_receiving['receiving_id'], array('target' => '_blank'));?></td>
						<td class="sp_date text-center"><?php echo date(get_date_format(). ' '.get_time_format(),strtotime($unpaid_receiving['receiving_time']));?></td>
						<td class="sp_charge text-center"><?php echo to_currency($unpaid_receiving['payment_amount']);?></td>
						<td class="sp_comment text-center"><?php echo $unpaid_receiving['comment']?></td>						
						<td class="sp_pay text-center">
							<?php echo form_open("receivings/".((isset($unpaid_receiving['paid']) && $unpaid_receiving['paid'] == TRUE) ? "delete" : "pay")."_store_account_receiving/".$unpaid_receiving['receiving_id']."/".to_currency_no_money($unpaid_receiving['payment_amount']),array('class'=>'pay_store_account_receiving_form', 'autocomplete'=> 'off')); ?>
								<button type="submit" class="btn <?php echo $btn_class; ?> pay_store_account_receiving"><?php echo isset($unpaid_receiving['paid']) && $unpaid_receiving['paid'] == TRUE  ? lang('common_remove_payment') : lang('common_pay');?></button>
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

	<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
		<div class="register-box register-right">
			

	<!-- Receive  Top Buttons  -->
			<div class="sale-buttons">
				<!-- Extra links -->
				<div class="btn-group">
					<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="ion-android-more-horizontal"></i>
					</button>
					<ul class="dropdown-menu sales-dropdown" role="menu">
						
						
						<li>
							<?php echo anchor("receivings/suspended/",
								'<i class="ion-ios-list-outline"></i> '.lang('common_suspended_receivings'). ' '.lang('common_and').' <br /> '.lang('receivings_purchase_orders'),
								array('class'=>'none suspended_sales_btn','title'=>lang('common_suspended_receivings')));
							?>
						</li>
						
						<li>
							<?php echo anchor("receivings/po/",
								'<i class="ion-ios-paper"></i> '.lang('receivings_create_purchase_order'),
								array('class'=>'none suspended_sales_btn','title'=>lang('receivings_create_purchase_order')));
							?>
						</li>
					
						
						<li>
							<?php echo anchor("receivings/batch_receiving/",
								'<i class="ion-bag"></i> '.lang('batch_receivings'),
								array('class'=>'none suspended_sales_btn','title'=>lang('batch_receivings')));
							?>
						</li>
					</ul>
				</div>
				<?php if(count($cart) > 0){ ?>
				<?php echo form_open("receivings/cancel_receiving",array('id'=>'cancel_sale_form', 'autocomplete'=> 'off')); ?>
				
				<?php
					if (!$this->receiving_lib->get_change_recv_id()) {?>
						<a href="" class="btn btn-suspended"  id="suspend_recv_button" >
							<i class="ion-pause"></i>
							<?php echo lang('receivings_suspend_recv');?>
						</a>
				<?php } ?>
				<a href="" class="btn btn-cancel"  id="cancel_sale_button" >
					<i class="ion-close-circled"></i>
					<?php echo $this->receiving_lib->get_change_recv_id() ? lang('common_cancel_edit') : lang('receivings_cancel_receiving');?>
				</a>
			</form>				

			<?php } ?>

		</div>
		<!-- /.End of receive Buttons -->

		<?php if($mode=="transfer") { ?>

			<?php if(isset($location)) {  ?>
				<!-- Customer Badge when customer is added -->
				<div class="customer-badge location">
					<div class="details">

						<a tabindex="-1" href="<?php echo site_url("locations/view/$location_id/1"); ?>" class="name">
						<span class="badge" style="background-color:<?php echo $location_color; ?>">&nbsp;</span>
							<?php echo character_limiter(H($location), 30); ?> 
						</a>
						
					</div>

				</div>
				<div class="customer-action-buttons btn-group btn-group-justified">
					<a tabindex="-1" href="<?php echo site_url("locations/view/$location_id/1"); ?>" class="btn success">
						<i class="ion-ios-compose-outline"></i>
						Edit		
					</a>
					<?php echo ''.anchor("receivings/delete_location", '<i class="ion-close-circled"></i> '.lang('common_detach'),array('id' => 'delete_location','class'=>'btn')); ?>

				</div>
			<?php }
			else {  ?>

			<div class="customer-form">

				<!-- if the location is not set , show location adding form -->
				<?php echo form_open("receivings/select_location",array('id'=>'select_location_form', 'autocomplete'=> 'off')); ?>
					<div class="input-group contacts">
						<span class="input-group-addon">
							<?php echo anchor("locations/view/-1","<i class='ion-plus'></i>", array('class'=>'none','title'=>lang('common_new_customer'), 'id' => 'new-customer')); ?>
						</span>
						<input type="text" id="location" name="location" class="add-customer-input" placeholder="<?php echo lang('receivings_start_typing_location_name'); ?>" data-title="<?php echo lang('common_location'); ?>" />

					</div>
				</form>

			</div> 
				

			<?php }  ?>


		<?php }  else {  ?>
			<?php if(isset($supplier)) {  ?>
				<!-- Customer Badge when customer is added -->
				<div class="customer-badge">
					<div class="avatar">
						<img src="<?php echo $avatar; ?>" alt="">	
					</div>
					<div class="details">
						<a tabindex="-1" href="<?php echo site_url("suppliers/view/$supplier_id/1"); ?>" class="name">
							<?php echo character_limiter(H($supplier), 30); ?> 
							<?php if ($this->config->item('suppliers_store_accounts') && isset($supplier_balance)) {?>
							<span class="<?php echo $has_balance ? 'text-danger' : 'text-success'; ?> balance">(<?php echo to_currency($supplier_balance); ?>)</span>
							<?php } ?>
						</a>

						<!-- supplier Email  -->
						<?php if(!empty($supplier_email)) { ?>
						<span class="email">
							<?php echo character_limiter(H($supplier_email), 25); ?>
						</span>
						<?php } ?>

						<!-- supplier edit -->
						<?php echo anchor("suppliers/view/$supplier_id/1", '<i class="ion-ios-compose-outline"></i>',  array('id' => 'edit_supplier','class'=>'btn btn-edit btn-primary pull-right','title'=>lang('receivings_update_supplier'))).''; ?>
						
					</div>

				</div>
				
				
				<div class="customer-action-buttons btn-group btn-group-justified ">
		            <?php if(!empty($supplier_email)) { ?>
		                <a href="#" class="btn <?php echo (boolean)$email_receipt ? 'checked' : '';?>" id="toggle_email_receipt">
		                    <i class="ion-android-mail"></i>
		                    <?php echo $is_po ? lang('receivings_email_po') : lang('common_email_receipt'); ?>?
		                </a>
		            <?php } else { ?>
		                <a href="<?php echo site_url('suppliers/view/'.$supplier_id.'/1');  ?>" class="btn" >
		                    <i class="ion-ios-compose-outline"></i>
		                    <?php echo lang('receivings_update_supplier'); ?>
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
		
							?>
				
			
					<?php echo ''.anchor("receivings/delete_supplier", '<i class="ion-close-circled"></i> '.lang('common_detach'),array('id' => 'delete_supplier','class'=>'btn')); ?>
				</div>
			<?php }
			else {  ?>

			<div class="customer-form">

				<!-- if the supplier is not set , show supplier adding form -->
				<?php echo form_open("receivings/select_supplier",array('id'=>'select_supplier_form', 'autocomplete'=> 'off')); ?>
					<div class="input-group contacts">
						<span class="input-group-addon">
							<?php echo anchor("suppliers/view/-1/1","<i class='ion-plus'></i>", array('class'=>'none','title'=>lang('receivings_new_supplier'), 'id' => 'new-customer')); ?>
						</span>
						<input type="text" id="supplier" name="supplier" class="add-customer-input keyboardLeft" data-title="<?php echo lang('common_supplier'); ?>" placeholder="<?php echo lang('receivings_start_typing_supplier_name'); ?>" /> 

					</div>
				</form>

			</div> 
				
				
			<?php }  ?>

		
		<?php } ?>
	</div>



		<!-- Summary -->
		<div class="register-box register-summary paper-cut">

		
		<ul class="list-group">

		<li class="sub-total list-group-item receivings">
			<span class="key"><?php echo lang('common_sub_total'); ?>:</span>
			<span class="value"><?php echo to_currency($subtotal); ?></span>
		</li>
		
		<?php foreach($taxes as $name=>$value) { ?>
		<li class="list-group-item">
			<span class="key">
				<?php if ($this->Employee->has_module_action_permission('receivings', 'delete_taxes', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
					<?php echo anchor("receivings/delete_tax/".rawurlencode($name),'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-tax remove'));?>

				<?php } ?>
				<?php echo $name; ?>:</td>
			</span>
			<span class="value pull-right">
				<?php echo to_currency($value); ?>
			</span>
		<?php }; ?>
		</ul>

		<div class="amount-block">
			<div class="total amount receiving">
				<div class="side-heading">
					<?php echo lang('common_total'); ?>
				</div>
				<div class="amount total-amount" data-speed="1000" data-currency="<?php echo $this->config->item('currency_symbol'); ?>" data-decimals="<?php echo $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2; ?>">
					<?php echo to_currency($total); ?>
				</div>
			</div>
			<div class="total amount-due">
				<div class="side-heading">
					<?php echo lang('common_amount_due'); ?>
				</div>
				<div class="amount">
					<?php echo to_currency($amount_due); ?>
				</div>
			</div>
		</div>
		<!-- ./amount block -->

		
		

			<?php
				// Only show this part if there are Items already in the Table.
				if(count($cart) > 0){ 
				?>
					<?php if(count($payments) > 0) { ?>
						<ul class="list-group payments">
							<?php foreach($payments as $payment_id=>$payment) { ?>
								<li class="list-group-item">
									<span class="key">
										<?php echo anchor("receivings/delete_payment/$payment_id",'<i class="icon ion-android-cancel"></i>', array('class' => 'delete-payment remove','id'=>'delete_payment_'.$payment_id));?>
										<?php echo character_limiter($payment['payment_type'],20); ?> 							
									</span>
									<span class="value">
										<?php echo  to_currency($payment['payment_amount']); ?>
									</span>
								</li>
							<?php } ?>
						</ul>
					<?php }
				
				if($mode!="transfer") 
				{
				?>
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

					<?php echo form_open("receivings/add_payment",array('id'=>'add_payment_form', 'autocomplete'=> 'off')); ?>

						
						<div class="input-group add-payment-form">
							<?php echo form_dropdown('payment_type',$payment_options,$selected_payment, 'id="payment_types" class="hidden"');?>
							<?php echo form_input(array('name'=>'amount_tendered','id'=>'amount_tendered','value'=> to_currency_no_money($amount_due),'class'=>'add-input numKeyboard form-control', 'data-title' => lang('common_payment_amount')));	?>
							<span class="input-group-addon">
								<a href="#" class="" id="add_payment_button"><?php echo lang('common_add_payment'); ?></a>
								<a href="#" class="hidden" id="finish_sale_alternate_button"><?php echo (!$is_po ? lang('receivings_complete_receiving') : lang('receivings_suspend_and_complete_po'));?></a>
							</span>
						
						</div>
					</form>
				</div>
				<?php
				}
				?>
					<div class="change-date">
						<?php if ($mode=='transfer' && isset($location_id)) { ?>
							
						<div id="finish_sale" class="receivings-finish-sale">
							<div class="input-group add-payment-form">
								<span class="input-group-addon">
									<a href="#" id="finish_sale_button" class="finish-transfer-button"><?php echo lang('receivings_complete_transfer'); ?></a>
								</span>
							</div>
						</div>
						
						<?php } ?>
						
				<?php
				echo form_checkbox(array(
					'name'=>'change_receiving_date_enable',
					'id'=>'change_receiving_date_enable',
					'value'=>'1',
					'checked'=>(boolean)$change_recv_date_enable)
				);
				echo '<label for="change_receiving_date_enable"><span></span>'.lang('receivings_change_recv_date').'</label>';
				?>
				<div id="change_receiving_date_picker" class="input-group date datepicker" >
					<span class="input-group-addon"><i class="ion-calendar"></i></span>

				<?php echo form_input(array(
					'name'=>'change_receiving_date',
					'id' => 'change_receiving_date',
					'size'=>'8',
					'class' => 'form-control',
					'value'=> date(get_date_format().' '.get_time_format(), $change_receiving_date ? strtotime($change_receiving_date) : time()),
					)
				);?>       
			</div>
			
					<div id="finish_sale" class="finish-sale receivings-finish-sale">
						<?php echo form_open("receivings/".(!$is_po ? 'complete' : 'suspend'),array('id'=>'finish_sale_form', 'autocomplete'=> 'off')); ?>
						<?php							 
						if (count($payments) > 0 && $payments_cover_total)
						{
							echo "<input type='button' class='btn btn-success btn-large btn-block' id='finish_sale_button' value='".(!$is_po ? lang('receivings_complete_receiving') : lang('receivings_suspend_and_complete_po'))."' />";
						}
						?>

				</div>
				<div class="comment-block">
					<div class="side-heading"><label id="comment_label" for="comment"><?php echo lang('common_comments'); ?> : </label></div>
					<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'2', 'class'=>'form-control', 'data-title' => lang('common_comments'))); ?>
				</div>
				
			</div>
			</div>
					</div>
				</form>
				<?php } ?>
			</div>
		<!-- /.Summary -->
		</div>
	</div>
</div>


<?php if($this->config->item('confirm_error_adding_item') && isset($error)) { ?>
	<script type="text/javascript">
	bootbox.confirm(<?php echo json_encode($error); ?>, function(result)
	{
		setTimeout(function() {
    	     $('#item').focus();
		}, 50);
	});
	</script>
<?php } ?>

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
		echo "show_feedback('success', ".json_encode($success).", ".json_encode(lang('common_success')).");";
	}
	?>
</script>


<script type="text/javascript" language="javascript">
	
var submitting = false;

$(document).ready(function()
{
	$(".pay_store_account_receiving_form").ajaxForm({target: "#register_container"});
	
	$('#toggle_email_receipt').on('click',function(e) {
		e.preventDefault();
        var checkBoxes = $("#email_receipt");
        checkBoxes.prop("checked", !checkBoxes.prop("checked")).trigger("change");
        $(this).toggleClass('checked');

	})

	$('#email_receipt').change(function(e) 
	{	
		e.preventDefault();
		$.post('<?php echo site_url("receivings/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});
	
	
	$('#change_receiving_date_enable').is(':checked') ? $("#change_receiving_date_picker").show() : $("#change_receiving_date_picker").hide(); 

	$('#change_receiving_date_enable').click(function() {
		if( $(this).is(':checked')) {
			$("#change_receiving_date_picker").show();
		} else {
			$("#change_receiving_date_picker").hide();
		}
	});
	
	date_time_picker_field($("#change_receiving_date"), JS_DATE_FORMAT + " "+ JS_TIME_FORMAT);
	
   $("#change_receiving_date").on("dp.change", function(e) {
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
   });
	
	//Input change
	$("#change_receiving_date").change(function(){
		$.post('<?php echo site_url("receivings/set_change_receiving_date");?>', {change_receiving_date: $('#change_receiving_date').val()});			
	});

	$('#change_receiving_date_enable').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_change_receiving_date_enable");?>', {change_receiving_date_enable: $('#change_receiving_date_enable').is(':checked') ? '1' : '0'});
	});

	//Here just in case the loader doesn't go away for some reason
	$("#ajax-loader").hide();
	
	<?php if (!$this->agent->is_mobile()) { ?>
		<?php if (!$this->config->item('auto_focus_on_item_after_sale_and_receiving'))
		{
		?>
			if (last_focused_id && last_focused_id != 'item')
			{
				setTimeout(function(){$('#'+last_focused_id).focus(); $('#'+last_focused_id).select();}, 10);
			}
			<?php 
		}
		else
		{
		?>
			setTimeout(function(){$('#item').focus();}, 10);	
		<?php
		}
		?>
	
		$(document).focusin(function(event) 
		{
			last_focused_id = $(event.target).attr('id');
		});
	<?php }
	else
	{
		if ($this->config->item('wireless_scanner_support_focus_on_item_field'))
		{
		?>
			setTimeout(function(){$('#item').focus();}, 10);				
		<?php
		}
	} ?>
	
	$('#select_supplier_form,#select_location_form').ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});

			$( "#item" ).autocomplete({
		 		source: '<?php echo site_url("receivings/item_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
					$( "#item" ).val(ui.item.value+'|FORCE_ITEM_ID|');
		 			
		 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
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
		// if #mode is changed
		$('.change-mode').click(function(e){
			e.preventDefault();
			if ($(this).data('mode') == "store_account_payment") { // Hiding the category grid
				$('#show_hide_grid_wrapper, #category_item_selection_wrapper').fadeOut();
			}else { // otherwise, show the categories grid
				$('#show_hide_grid_wrapper, #show_grid').fadeIn();
				$('#hide_grid').fadeOut();
			}
			$.post('<?php echo site_url("receivings/change_mode");?>', {mode: $(this).data('mode')}, function(response)
			{
				$("#register_container").html(response);
			});
		});


    //make username editable
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
	 
	 $(".expire_date").editable({
     	validate: function(value) {
          if (!value) 
			 {
             return <?php echo json_encode(lang('receivings_invalid_date')); ?>;
          }
       },
		 combodate: {
			 maxYear: <?php echo date("Y", strtotime('+3 years'));?>,
			 minYear: <?php echo date("Y");?>,
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
	
	<?php if (isset($cart_count)) { ?>
      	$('.cart-number').html(<?php echo $cart_count; ?>);
	<?php } ?>

		$('#location').change(function(){
			$('#select_location_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
		});

		// Select Location 
		<?php if($mode=="transfer" and !isset($location)) { ?>
		

			$( "#location" ).autocomplete({
		 		source: '<?php echo site_url("receivings/location_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_location");?>', {location: ui.item.value }, function(response)
					{
						$("#register_container").html(response);
					});	
		 		},
			}).data("ui-autocomplete")._renderItem = function (ul, item) {
		         return $("<li class='customer-badge suggestions'></li>")
		             .data("item.autocomplete", item)
			         .append('<a class="suggest-item location-suggest"><div class="avatar">' +
									'<span class="badge" style="background-color:' + item.color + '">&nbsp;</span>' +
								'</div>' +
								'<div class="details">' +
									'<div class="name">' + 
										item.label +
									'</div>' + 
								'</div></a>')
		             .appendTo(ul);

		     };
	     <?php } ?>


		// Select Supplier 
		<?php if($mode!="transfer" and !isset($supplier)) { ?>

			$( "#supplier" ).autocomplete({
		 		source: '<?php echo site_url("receivings/supplier_search");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
		 			$.post('<?php echo site_url("receivings/select_supplier");?>', {supplier: ui.item.value }, function(response)
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



		//Add payment to the sale 
		$("#add_payment_button").click(function(e)
		{
			e.preventDefault();

			$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
		});
		
		$('#select_supplier_form').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
	 			$('#select_supplier_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
			}
		});
		
		$('#select_location_form').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
	 			$('#select_location_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
			}
		});
		
		$('#add_item_form').ajaxForm({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
		
		$('#add_item_form').bind('keypress', function(e) {
			if(e.keyCode==13)
			{
				e.preventDefault();
	 			$('#add_item_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, success: itemScannedSuccess});
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
					$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, complete: function()
					{
						$('#finish_sale_button').trigger('click');
					}});
				}
				else
				{
					$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
				}
			}
		});
		
		//Select all text in the input when input is clicked
		$("input:text, textarea").not(".description,#comment").click(function() {
			$(this).select();
		});
		
		<?php if (!$this->config->item('disable_quick_complete_sale')) { ?>
		
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
	
	
	$('#amount_tendered').on('input',function(){
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
		
	});

	$('#finish_sale_alternate_button').on('click',function(e){
		e.preventDefault();
		$('#add_payment_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit, complete: function()
		{
			$('#finish_sale_button').trigger('click');
		}});
	});
		
	<?php } ?>
		
		// Show or hide item grid
		$("#show_grid, .show-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideDown();

			$('.show-grid').addClass('hidden');
			$('.hide-grid').removeClass('hidden');
		});

		$("#hide_grid,#hide_grid_top, .hide-grid").on('click',function(e)
		{
			e.preventDefault();
			$("#category_item_selection_wrapper").slideUp();

			$('.hide-grid').addClass('hidden');
			$('.show-grid').removeClass('hidden');
		});
	

	$("#cart_contents input").change(function()
	{
		$(this.form).ajaxSubmit({target: "#register_container",beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#item,#supplier,#location').click(function()
    {
    	$(this).attr('value','');
    });

	$('#mode').change(function()
	{
		$('#mode_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
	});
	
	$('#comment').change(function() 
	{
		$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()});
	});



	<?php if (!$is_po) { ?>
	    $("#finish_sale_form").submit(function()
		{
			<?php if($mode=="transfer" and !isset($location)) { ?>
				bootbox.alert(<?php echo json_encode(lang("receivings_location_required")); ?>);
				$('#location').focus();
				return;
				<?php } ?>
			
				var finishForm = this;
				
				bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_finish_receiving")); ?>, function(result)
	    		{
					if (result)
					{
						//Prevent double submission of form
						$("#finish_sale_button").hide();
						finishForm.submit();
					}
				});
				return false;
		});
	    $("#finish_sale_button").click(function(e)
	    {
	    	e.preventDefault();
			
			if ($("#comment").val())
			{
				$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()}, function()
				{
					$('#finish_sale_form').submit();						
				});						
			}
			else
			{
				$('#finish_sale_form').submit();						
			}
		
			
		});
	<?php } ?>
    $("#cancel_sale_button").click(function(e)
    {
     	e.preventDefault();		 
    	bootbox.confirm(<?php echo json_encode(lang("receivings_confirm_cancel_receiving")); ?>, function(result)
    	{
			if (result)
			{
				$('#cancel_sale_form').ajaxSubmit({target: "#register_container", beforeSubmit: receivingsBeforeSubmit});
			}
		});
    });
	
	//Select Payment
	$('.select-payment').on('click',selectPayment);
	
	$('.delete-item, .delete-payment, #delete_supplier, #delete_location,.delete-tax').click(function(event)
	{
		event.preventDefault();
		$("#register_container").load($(this).attr('href'));	
	});

	$("input[type=text]").click(function() {
		$(this).select();
	});
		
	$("#suspend_recv_button<?php echo $is_po ? ', #finish_sale_button': '';?>").click(function(e)
	{
		e.preventDefault();
		bootbox.confirm(<?php echo json_encode(lang("receivings_confim_suspend_recv")); ?>, function(result)
		{
			if (result)
			{
				if ($("#comment").val())
				{
					$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()}, function()
					{
						doSuspendRecv();
					});						
				}
				else
				{
					doSuspendRecv();	
				}
			}
		});
	});

	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen/1");?>');
	});

	$('.dismissfullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen/0");?>');
	});
});

function doSuspendRecv()
{
	<?php if (!$is_po) { ?>
		<?php if ($this->config->item('show_receipt_after_suspending_sale')) { ?>
			window.location = '<?php echo site_url("receivings/suspend"); ?>';
		<?php }else { ?>
			$("#register_container").load('<?php echo site_url("receivings/suspend"); ?>');
		<?php } ?>
		<?php 
		}
		else
		{
		?>
		window.location = '<?php echo site_url("receivings/suspend"); ?>';			
		<?php	
		} 
		?>

}
function receivingsBeforeSubmit(formData, jqForm, options)
{
	if (submitting)
	{
		return false;
	}
	submitting = true;
	
	$('.cart-number').html(<?php echo $cart_count; ?>);
	$("#ajax-loader").show();
	$("#finish_sale_button").hide();
}

function itemScannedSuccess(responseText, statusText, xhr, $form)
{
	setTimeout(function(){$('#item').focus();}, 10);
}

function selectPayment(e)
{
	e.preventDefault();
	$.post('<?php echo site_url("receivings/set_selected_payment");?>', {payment: $(this).data('payment')});
	$('#payment_types').val($(this).data('payment'));
	$('.select-payment').removeClass('active');
	$(this).addClass('active');
	$("#amount_tendered").focus();
	$("#amount_tendered").select();
	$("#amount_tendered").attr('placeholder','');	
}
</script>

<?php if($this->config->item('virtual_keyboard') == 'all' || ($this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'mobile') ||  (!$this->agent->is_mobile() && $this->config->item('virtual_keyboard')  == 'desktop')) { ?>
<script>COMMON_NONE = <?php echo json_encode(lang('common_none')); ?>;</script>
<script src="<?php echo base_url().'assets/js/onscreen_keyboard.js'. '?' . ASSET_TIMESTAMP;?>" type="text/javascript" charset="UTF-8"></script>
<?php } ?>