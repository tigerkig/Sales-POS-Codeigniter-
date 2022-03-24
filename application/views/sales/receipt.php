<?php $this->load->view("partial/header"); ?>

<?php
	$this->load->helper('sale');
	$return_policy = ($loc_return_policy = $this->Location->get_info_for_key('return_policy', isset($override_location_id) ? $override_location_id : FALSE)) ? $loc_return_policy : $this->config->item('return_policy');
	$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
	$website = ($website = $this->Location->get_info_for_key('website', isset($override_location_id) ? $override_location_id : FALSE)) ? $website : $this->config->item('website');
	$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');
	
	$is_integrated_credit_sale = is_sale_integrated_cc_processing();
	$is_sale_integrated_ebt_sale = is_sale_integrated_ebt_sale();
	$is_credit_card_sale = is_credit_card_sale();
	
	$signature_needed = $this->config->item('capture_sig_for_all_payments') || (($is_credit_card_sale && !$is_integrated_credit_sale) ||  is_store_account_sale());
	
	//Check for EMV signature for non pin verified
	if (!$signature_needed && $is_integrated_credit_sale)
	{
		foreach($payments as $payment_id=>$payment)
		{
			if ($payment['cvm'] != 'PIN VERIFIED')
			{
				$signature_needed = TRUE;
				break;
			}
		}
	}
	
	if (isset($error_message))
	{
		echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
		exit;
	}
?>

<div class="manage_buttons hidden-print">
	<div class="row">
		<div class="col-md-6">
			<div class="hidden-print search no-left-border">
				<ul class="list-inline print-buttons">
					<li></li>
					
						<li>
							<?php 
							 if ($sale_id_raw != lang('sales_test_mode_transaction') && !$store_account_payment && $this->Employee->has_module_action_permission('sales', 'edit_sale', $this->Employee->get_logged_in_employee_info()->person_id)){

						   		$edit_sale_url = (isset($sale_type) && ($sale_type == ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway')) || $sale_type == lang('common_estimate'))) ? 'unsuspend' : 'change_sale';
								echo form_open("sales/$edit_sale_url/".$sale_id_raw,array('id'=>'sales_change_form')); ?>
								<button class="btn btn-primary btn-lg hidden-print" id="edit_sale"> <?php echo lang('sales_edit'); ?> </button>

							<?php }	?>
							</form>		
						</li>
						
					<?php 
					if ($sale_id_raw != lang('sales_test_mode_transaction')){
					?>	
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/fulfillment/$sale_id_raw"); ?>', 'blank');" > <?php echo lang('sales_fulfillment_sheet'); ?></button>
						</li>
					<?php } ?>
					
					<li>
						<button class="btn btn-primary btn-lg hidden-print gift_receipt" id="gift_receipt_button" onclick="toggle_gift_receipt()" > <?php echo lang('sales_gift_receipt'); ?> </button>
					</li>
						<?php if ($sale_id_raw != lang('sales_test_mode_transaction') && !empty($customer_email)) { ?>
							<li>
									<?php echo anchor('sales/email_receipt/'.$sale_id_raw, lang('common_email_receipt'), array('id' => 'email_receipt','class' => 'btn btn-primary btn-lg hidden-print'));?>
							</li>
						<?php }?>
					
					<?php if ($sale_id_raw != lang('sales_test_mode_transaction')) { ?>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/create_po/$sale_id_raw"); ?>', 'blank');" > <?php echo lang('common_create_po'); ?></button>
						</li>
						<?php } ?>					
				</ul>
			</div>
		</div>
		<div class="col-md-6">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline print-buttons">
						<li>
							<?php
							echo form_checkbox(array(
								'name'        => 'print_duplicate_receipt',
								'id'          => 'print_duplicate_receipt',
								'value'       => '1',
							)).'&nbsp;<label for="print_duplicate_receipt"><span></span>'.lang('sales_duplicate_receipt').'</label>';
								?>		
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="print_receipt()" > <?php echo lang('common_print'); ?> </button>		
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="new_sale_button_1" onclick="window.location='<?php echo site_url('sales'); ?>'" > <?php echo lang('sales_new_sale'); ?> </button>	
						</li>
					</ul>
				</div>
			</div>				
		</div>
	</div>
</div>
<div class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small';?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<div class="panel-body panel-pad">
			    <div class="row">
			        <!-- from address-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			            <ul class="list-unstyled invoice-address" style="margin-bottom:2px;">
			                <?php if($company_logo) {?>
			                	<li class="invoice-logo">
									<?php echo img(array('src' => $this->Appfile->get_url_for_file($company_logo))); ?>
			                	</li>
			                <?php } ?>
			                <li class="company-title"><?php echo $company; ?></li>
							
							<?php if ($this->Location->count_all() > 1) { ?>
								<li><?php echo $this->Location->get_info_for_key('name', isset($override_location_id) ? $override_location_id : FALSE); ?></li>
							<?php } ?>
							
			                <li><?php echo nl2br($this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
			                <li><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></li>
			                <?php if($website) { ?>
								<li><?php echo $website;?></li>
							<?php } ?>
							<li class="title">
								<span class="pull-left"> <?php echo $receipt_title; ?><?php echo ($total) < 0 ? ' ('.lang('sales_return').')': '';?></span>
								<span class="pull-right"><?php echo $transaction_time ?></span>
							</li>
			            </ul>
			        </div>
			        <!--  sales-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			            <ul class="list-unstyled invoice-detail" style="margin-bottom:2px;">
							<li class="big-screen-title">
								 <?php echo $receipt_title; ?><?php echo ($total) < 0 ? ' ('.lang('sales_return').')': '';?>
								 <br>
								 <strong><?php echo $transaction_time ?></strong>
							</li>
			            <li><span><?php echo lang('common_sale_id').":"; ?></span><?php echo $sale_id; ?></li>
							<?php if (isset($deleted) && $deleted) {?>
			            	<li><span class="text-danger" style="color: #df6c6e;"><strong><?php echo lang('sales_deleted_voided'); ?></strong></span></li>
							<?php } ?>
							<?php if (isset($sale_type)) { ?>
								<li><?php echo $sale_type; ?></li>
							<?php } ?>
							<?php
							if ($this->Register->count_all(isset($override_location_id) ? $override_location_id : FALSE) > 1 && $register_name)
							{
							?>
								<li><span><?php echo lang('common_register_name').':'; ?></span><?php echo $register_name; ?></li>		
							<?php
							}
							?>				
							
							<?php
							if ($tier)
							{
							?>
								<li><span><?php echo $this->config->item('override_tier_name') ? $this->config->item('override_tier_name') : lang('common_tier_name').':'; ?></span><?php echo $tier; ?></li>		
							<?php
							}
							?>
							
							<li><span><?php echo lang('common_employee').":"; ?></span><?php echo $employee; ?></li>
							<?php 
							if($this->Location->get_info_for_key('enable_credit_card_processing',isset($override_location_id) ? $override_location_id : FALSE))
							{
								echo '<li id="merchant_id"><span>'.lang('common_merchant_id').':</span> '.$this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE).'</li>';
							}
							?>
			            </ul>
			        </div>
			        <!-- to address-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
						
			          <?php if(isset($customer)) { ?>
				        <ul class="list-unstyled invoice-address invoiceto" style="margin-bottom:2px;">
								
								<?php if (!$this->config->item('remove_customer_name_from_receipt')) { ?>
									<li class="invoice-to"><?php echo lang('sales_invoice_to');?>:</li>
									<li><?php echo lang('common_customer').": ".$customer; ?></li>
									
								<?php } ?>

								<?php if (!$this->config->item('remove_customer_company_from_receipt')) { ?>
									<?php if(!empty($customer_company)) { ?><li><?php echo lang('common_company').": ".$customer_company; ?></li><?php } ?>
								<?php } ?>
									
									<?php if (!$this->config->item('remove_customer_contact_info_from_receipt')) { ?>
										<?php if(!empty($customer_address_1) || !empty($customer_address_2)){ ?><li><?php echo lang('common_address'); ?> : <?php echo $customer_address_1. ' '.$customer_address_2; ?></li><?php } ?>
										<?php if (!empty($customer_city)) { echo '<li>'.$customer_city.' '.$customer_state.', '.$customer_zip.'</li>';} ?>
										<?php if (!empty($customer_country)) { echo '<li>'.$customer_country.'</li>';} ?>			
										<?php if(!empty($customer_phone)){ ?><li><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?></li><?php } ?>
										<?php if(!empty($customer_email)){ ?><li><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?></li><?php } ?>
									<?php } ?>
				        </ul>
								<?php } ?>
			        </div>
							
			        <!-- delivery address-->
			        <div class="col-md-12 col-sm-12 col-xs-12">
					
			          <?php if(isset($delivery_person_info)) { ?>
				        <ul class="list-unstyled invoice-address" style="margin-bottom:10px;">
								
								
									<li class="invoice-to"><?php echo lang('deliveries_shipping_address');?>:</li>
									<li><?php echo lang('common_name').": ".$delivery_person_info['first_name'].' '.$delivery_person_info['last_name']; ?></li>
									
									<?php if(!empty($delivery_person_info['address_1']) || !empty($delivery_person_info['address_2'])){ ?><li><?php echo lang('common_address'); ?> : <?php echo $delivery_person_info['address_1']. ' '.$delivery_person_info['address_2']; ?></li><?php } ?>
									<?php if (!empty($delivery_person_info['city'])) { echo '<li>'.$delivery_person_info['city'].' '.$delivery_person_info['state'].', '.$delivery_person_info['zip'].'</li>';} ?>
									<?php if (!empty($delivery_person_info['country'])) { echo '<li>'.$delivery_person_info['country'].'</li>';} ?>			
									<?php if(!empty($delivery_person_info['phone'])){ ?><li><?php echo lang('common_phone_number'); ?> : <?php echo $delivery_person_info['phone']; ?></li><?php } ?>
									<?php if(!empty($delivery_person_info['email'])){ ?><li><?php echo lang('common_email'); ?> : <?php echo $delivery_person_info['email']; ?></li><?php } ?>
												
				        </ul>
								<?php } ?>
								
								<?php if(!empty($delivery_info['estimated_delivery_or_pickup_date']) || !empty($delivery_info['tracking_number']) ||  !empty($delivery_info['comment'])) {?>
									<ul class="list-unstyled invoice-address" style="margin-bottom:10px;">
										<li class="invoice-to"><?php echo lang('deliveries_delivery_information');?>:</li>
										<?php if(!empty($delivery_info['estimated_delivery_or_pickup_date'])){ ?><li><?php echo lang('deliveries_estimated_delivery_or_pickup_date'); ?> : <?php echo date(get_date_format().' '.get_time_format(),strtotime($delivery_info['estimated_delivery_or_pickup_date'])); ?></li><?php } ?>
										<?php if(!empty($delivery_info['tracking_number'])){ ?><li><?php echo lang('deliveries_tracking_number'); ?> : <?php echo $delivery_info['tracking_number']; ?></li><?php } ?>
										<?php if(!empty($delivery_info['comment'])){ ?><li><?php echo lang('common_comment'); ?> : <?php echo $delivery_info['comment']; ?></li><?php } ?>
											
											
									</ul>
								<?php } ?>
			        </div>
							
			    </div>
					<?php
		    		$x_col = 6;
		    		$xs_col = 4;
		    		if($discount_exists)
		    		{
		    			$x_col = 4;
		    			$xs_col = 3;

							if($this->config->item('wide_printer_receipt_format'))
							{
				    		$x_col = 4;
								$xs_col = 2;
							}
		    		}
						else
						{
							if($this->config->item('wide_printer_receipt_format'))
							{
				    		$x_col = 6;
								$xs_col = 2;
							}
						}
					?>
			    <!-- invoice heading-->
			    <div class="invoice-table">
			        <div class="row">
			            <div class="<?php echo $this->config->item('wide_printer_receipt_format') ? 'col-md-'.$x_col . ' col-sm-' .$x_col . ' col-xs-'.$x_col : 'col-md-12 col-sm-12 col-xs-12' ?>">
			                <div class="invoice-head item-name"><?php echo lang('common_item_name'); ?></div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
			                <div class="invoice-head text-right item-price"><?php echo lang('common_price'); ?></div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head text-right item-qty"><?php echo lang('common_quantity'); ?></div>
			            </div>

						<?php if($discount_exists) { ?>
				            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
				                <div class="invoice-head text-right item-discount"><?php echo lang('common_discount_percent'); ?></div>
				            </div>
				           
			      <?php } ?>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head pull-right item-total gift_receipt_element"><?php echo lang('common_total'); ?></div>
			            </div>
						
			        </div>
			    </div>
			    <?php
					if ($discount_item_line = $this->sale_lib->get_line_for_flat_discount_item())
					{
						$discount_item = $cart[$discount_item_line];
						unset($cart[$discount_item_line]);
						array_unshift($cart,$discount_item);
					}
				 
				$number_of_items_sold = 0;
				$number_of_items_returned = 0;
					
				foreach(array_reverse($cart, true) as $line=>$item)
				{
					
					 if ($item['quantity'] > 0 && $item['name'] != lang('common_store_account_payment') && $item['name'] != lang('common_discount'))
					 {
				 		 $number_of_items_sold = $number_of_items_sold + $item['quantity'];
					 }
					 elseif ($item['quantity'] < 0 && $item['name'] != lang('common_store_account_payment') && $item['name'] != lang('common_discount'))
					 {
				 		 $number_of_items_returned = $number_of_items_returned + abs($item['quantity']);
					 }
					 
					$item_number_for_receipt = false;
					
					if ($this->config->item('show_item_id_on_receipt'))
					{
						switch($this->config->item('id_to_show_on_sale_interface'))
						{
							case 'number':
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
							break;
						
							case 'product_id':
							$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
							break;
						
							case 'id':
							$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : 'KIT '.H($item['item_kit_id']); 
							break;
						
							default:
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : H($item['item_kit_number']);
							break;
						}
					}
					
				?>
			    <!-- invoice items-->
			    <div class="invoice-table-content">
			        <div class="row">
			            <div class="<?php echo $this->config->item('wide_printer_receipt_format') ? 'col-md-'.$x_col . ' col-sm-' .$x_col . ' col-xs-'.$x_col : 'col-md-12 col-sm-12 col-xs-12' ?>">
			                <div class="invoice-content invoice-con">
			                    <div class="invoice-content-heading"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?>
													</div>
			                    	<div class="invoice-desc">
															
															
															<?php if (!$this->config->item('hide_desc_on_receipt') && !$item['description']=="" ) {?>
																<?php 
																	echo $item['description']; 
              									}
																
									
																?>
															</div>
			                 			 <div class="invoice-desc">
					                    <?php 
																if(isset($item['serialnumber']) && $item['serialnumber'] !="")
																{
																	echo $item['serialnumber']; 
																}
																
															?>
													</div>
													<?php
													
													
													if(isset($item['rule']['type']))
													{	
														
														echo '<br class="gift_receipt_element"><i class="gift_receipt_element">'.$item['rule']['name'].'</i>';
														if(isset($item['rule']['rule_discount']))
														{
															echo '<br class="gift_receipt_element"><i class="gift_receipt_element"><u class="gift_receipt_element">'.lang('common_discount'). '</u>: ' .to_currency($item['rule']['rule_discount']) . '</i>';
														}																	
													}
														
													?>
			                </div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
			                <div class="invoice-content item-price text-right">
								<?php if ($this->config->item('show_orig_price_if_marked_down_on_receipt') && $item['regular_price'] > $item['price']) { ?>
									<span class="strikethrough"><?php echo to_currency($item['regular_price']); ?></span>
								<?php } ?>
								
								<?php echo to_currency($item['price']); ?>
							</div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> ">
			                <div class="invoice-content item-qty text-right">
												<?php 
												if ($this->config->item('number_of_decimals_for_quantity_on_receipt') && floor($item['quantity']) != $item['quantity'])
												{
													echo to_currency_no_money($item['quantity'],$this->config->item('number_of_decimals_for_quantity_on_receipt')); 
												}
												else
												{
													echo to_quantity($item['quantity']); 
												}
												?>
											
											</div>
			            </div>
			      <?php if($discount_exists) { ?>
									<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
			              <div class="invoice-content item-discount text-right"><?php echo to_quantity($item['discount']); ?></div>
			            </div>
						<?php } ?>
									<div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">      
					          <div class="invoice-content item-total pull-right">
											
											<?php if ($this->config->item('indicate_taxable_on_receipt') && $item['taxable'] && !empty($taxes))
											{
												echo '<small>*'.lang('common_taxable').'</small>';
											}
											?>
											
											<?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?>
										
										</div>
						      </div>
						
			     </div>					
			    </div>
			    <?php } ?>
					
			    <div class="invoice-footer gift_receipt_element">
						<?php if ($exchange_name) { ?>
						
							<div class="row">
					            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
					                <div class="invoice-footer-heading"><?php echo lang('common_exchange_to').' '.$exchange_name; ?></div>
					            </div>
					            <div class="col-md-2 col-sm-2 col-xs-4">
					                <div class="invoice-footer-value">x <?php echo to_currency_no_money($exchange_rate); ?></div>
					            </div>
					        </div>
											
						<?php } ?>
						
			        <div class="row">
			            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
			                <div class="invoice-footer-heading"><?php echo lang('common_sub_total'); ?></div>
			            </div>
			            <div class="col-md-2 col-sm-2 col-xs-4">
			                <div class="invoice-footer-value">
			                	
												<?php if (isset($exchange_name) && $exchange_name) { 
													echo to_currency_as_exchange($subtotal);
												?>
												<?php } else {  ?>
												<?php echo to_currency($subtotal); ?>				
												<?php
												}
												?>
			                </div>
			            </div>
			        </div>
			        <?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
						<?php 
						$total_tax = 0;
						foreach($taxes as $name=>$value) 
						{
							$total_tax+=$value;
					 	}
						?>	
						<div class="row">
				            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
				                <div class="invoice-footer-heading"><?php echo lang('common_tax'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value">
								
												<?php if (isset($exchange_name) && $exchange_name) { 
													echo to_currency_as_exchange($total_tax*$exchange_rate);					
												?>
												<?php } else {  ?>
												<?php echo to_currency($total_tax*$exchange_rate); ?>				
												<?php
												}
												?>
												
												</div>
				            </div>
				        </div>
						
					<?php }else {?>
						<?php foreach($taxes as $name=>$value) { ?>
							<div class="row">
					            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
					                <div class="invoice-footer-heading"><?php echo $name; ?></div>
					            </div>
					            <div class="col-md-2 col-sm-2 col-xs-4">
					                <div class="invoice-footer-value">
													
													
													<?php if (isset($exchange_name) && $exchange_name) { 
														echo to_currency_as_exchange($value*$exchange_rate);					
													?>
													<?php } else {  ?>
													<?php echo to_currency($value); ?>				
													<?php
													}
													?>
													
													
													</div>
					            </div>
					        </div>
						<?php }; ?>
					<?php } ?>
			        <div class="row">
			            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
			                <div class="invoice-footer-heading"><?php echo lang('common_total'); ?></div>
			            </div>
			            <div class="col-md-2 col-sm-2 col-xs-4">
			                <div class="invoice-footer-value invoice-total">
																							
											
											<?php if (isset($exchange_name) && $exchange_name) { 
												?>
												<?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($total)) : to_currency_as_exchange($total); ?>				
											<?php } else {  ?>
											<?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($total)) : to_currency($total); ?>				
											<?php
											}
											?>
											
											</div>
			            </div>
			        </div> 
					
			        <div class="row">
						<?php if ($number_of_items_sold) { ?>
				            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
				                <div class="invoice-footer-heading"><?php echo lang('common_items_sold'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo to_quantity($number_of_items_sold); ?></div>
				            </div>
						<?php } ?>
						
						<?php if ($number_of_items_returned) { ?>
							
				            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
				                <div class="invoice-footer-heading"><?php echo lang('common_items_returned'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo to_quantity($number_of_items_returned); ?></div>
				            </div>
						<?php } ?>
						
			        </div> 
					
			        <?php
						foreach($payments as $payment_id=>$payment)
						{ 
					?>
						<div class="row">
				            <div class="col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-md-4 col-sm-4 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				            	<?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment() || $is_sale_integrated_ebt_sale || sale_has_partial_ebt_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit') || $payment['payment_type'] == lang('common_ebt') || $payment['payment_type'] == lang('common_partial_ebt') ||  $payment['payment_type'] == lang('common_ebt_cash') ||  $payment['payment_type'] == lang('common_partial_ebt_cash'))) { ?>
									<div class="invoice-footer-value"><?php echo $is_sale_integrated_ebt_sale ? 'EBT ' : '';?><?php echo $payment['card_issuer']. ': '.$payment['truncated_card']; ?></div>
								<?php } else { ?>
									<div class="invoice-footer-value"><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?></div>																				
								<?php } ?>								
				            </div>
							
				            <div class="col-md-2 col-sm-2 col-xs-4">
								<div class="invoice-footer-value invoice-payment">
									
									
									
									<?php 
									
									if (isset($exchange_name) && $exchange_name) { 
										?>
										<?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  to_currency_as_exchange(round_to_nearest_05($payment['payment_amount'])) : to_currency_as_exchange($payment['payment_amount']); ?>				
									<?php } else {  ?>
									<?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  to_currency(round_to_nearest_05($payment['payment_amount'])) : to_currency($payment['payment_amount']); ?>				
									<?php
									}
									
									
									?>
								
								
								</div>
				            </div>
							
			            	<?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment() || $is_sale_integrated_ebt_sale || sale_has_partial_ebt_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit') || $payment['payment_type'] == lang('common_ebt') || $payment['payment_type'] == lang('common_partial_ebt') ||  $payment['payment_type'] == lang('common_ebt_cash') ||  $payment['payment_type'] == lang('common_partial_ebt_cash'))) { ?>
							
				           <div class="col-md-offset-6 col-sm-offset-6 col-xs-offset-3 col-md-6 col-sm-6 col-xs-9">
								<?php if ($payment['entry_method']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo lang('sales_entry_method'). ': '.$payment['entry_method']; ?></div>
								<?php } ?>

								<?php if ($payment['tran_type']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo lang('sales_transaction_type'). ': '.($is_sale_integrated_ebt_sale ? 'EBT ' : '').$payment['tran_type']; ?></div>
								<?php } ?>
							
								<?php if ($payment['application_label']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo lang('sales_application_label').': '.$payment['application_label']; ?></div>
								<?php } ?>
							
								<?php if ($payment['ref_no']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo lang('sales_ref_no'). ': '.$payment['ref_no']; ?></div>
								<?php } ?>
								<?php if ($payment['auth_code']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo lang('sales_auth_code'). ': '.$payment['auth_code']; ?></div>
								<?php } ?>
															
							
								<?php if ($payment['aid']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo 'AID: '.$payment['aid']; ?></div>
								<?php } ?>
							
								<?php if ($payment['tvr']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo 'TVR: '.$payment['tvr']; ?></div>
								<?php } ?>
							
							
								<?php if ($payment['tsi']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo 'TSI: '.$payment['tsi']; ?></div>
								<?php } ?>
							
							
								<?php if ($payment['arc']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo 'ARC: '.$payment['arc']; ?></div>
								<?php } ?>

								<?php if ($payment['cvm']) { ?>
								<div class="invoice-footer-value invoice-footer-value-cc"><?php echo 'CVM: '.$payment['cvm']; ?></div>
								<?php } ?>
							</div>
							<?php } ?>							
							
						</div>
					<?php
						}
					?>

					<?php foreach($payments as $payment) {?>
						<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
							<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
							
							<div class="row">
					            <div class="col-md-offset-4 col-sm-offset-4 col-md-4 col-sm-4 col-xs-4">
					                <div class="invoice-footer-heading"><?php echo lang('sales_giftcard_balance'); ?></div>
					            </div>
					            <div class="col-md-2 col-sm-2 col-xs-4">
										<div class="invoice-footer-value"><?php echo $payment['payment_type'];?></div>											
					            </div>
					            <div class="col-md-2 col-sm-2 col-xs-4">
									<div class="invoice-footer-value invoice-payment"><?php echo to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></div>
					            </div>
					        </div>
						<?php }?>
					<?php }?> 

					<?php if ($amount_change >= 0) {?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
				                <div class="invoice-footer-heading"><?php echo lang('common_change_due'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total">
													
													<?php if (isset($exchange_name) && $exchange_name) { 
														$amount_change_default_currency = $amount_change*pow($exchange_rate,-1);
														
														?>
														
														<?php
															
														if ($amount_change_default_currency != $amount_change) {
														?>
														<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($amount_change)) : to_currency_as_exchange($amount_change); ?>
														<br /><?php echo lang('common_or');?><br />
														<?php
													}
														?>
														<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change_default_currency)) : to_currency($amount_change_default_currency); ?>				
														
													<?php } else {  ?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change)) : to_currency($amount_change); ?>				
													<?php
													}
													?>
													
												
												</div>
				            </div>
				        </div>
					<?php
					}
					else
					{
					?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo lang('common_amount_due'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total">
													<?php if (isset($exchange_name) && $exchange_name) { 
														?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($amount_change * -1)) : to_currency_as_exchange($amount_change * -1); ?>
													<?php } else {  ?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change * -1)) : to_currency($amount_change * -1); ?>
													<?php
													}
													?>
												
												</div>
				            </div>
				        </div>
					<?php
					} 
					?>  
					
					<?php if (isset($ebt_balance) && ($ebt_balance) !== FALSE) {?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
				                <div class="invoice-footer-heading"><?php echo lang('sales_ebt_balance_amount'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo to_currency($ebt_balance); ?></div>
				            </div>
				        </div>
					<?php
					}
					?>					
					
					<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
				                <div class="invoice-footer-heading"><?php echo lang('sales_customer_account_balance'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo to_currency($customer_balance_for_sale); ?></div>
				            </div>
				        </div>
					<?php
					}
					?>
					
					<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
				                <div class="invoice-footer-heading"><?php echo lang('common_sales_until_discount'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?></div>
				            </div>
				        </div>
					<?php
					}
					?>
					

					<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
t				                <div class="invoice-footer-heading"><?php echo lang('common_points'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo to_quantity($customer_points); ?></div>
				            </div>
				        </div>
					<?php
					}
					?>


					<?php
					if ($ref_no)
					{
					?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo lang('sales_ref_no'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo $ref_no; ?></div>
				            </div>
				        </div>
					<?php
					}
					if (isset($auth_code) && $auth_code)
					{
					?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo lang('sales_auth_code'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-total"><?php echo $auth_code; ?></div>
				            </div>
				        </div>
					<?php
					}
					?>

					<div class="row">
			            <div class="col-md-12 col-sm-12 col-xs-12">
			                <div class="text-center">
			                	<?php if($show_comment_on_receipt==1)
									{
										echo $comment ;
									}
								?>
			                </div>
			            </div>
			        </div>
			    </div>
			    <!-- invoice footer-->						 
			    <div class="row">
			        <div class="col-md-12 col-sm-12 col-xs-12">
			            <div class="invoice-policy">
			                <?php echo nl2br($return_policy); ?>
			            </div>
			            <div id="receipt_type_label" style="display: none;" class="receipt_type_label invoice-policy">
							<?php echo lang('sales_merchant_copy'); ?>
						</div>
			            <?php if (!$this->config->item('hide_barcode_on_sales_and_recv_receipt')) {?>
							<div id='barcode' class="invoice-policy">
							<?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' alt=''/>"; ?>
							</div>
						<?php } ?>
						
						<?php 
						$this->load->model('Price_rule');
						$coupons = $this->Price_rule->get_coupons_for_receipt();
						if (count($coupons) > 0)
						{
							?>
							
					    <div class="row">
					        <div class="col-md-12 col-sm-12 col-xs-12">
					            <div class="invoice-policy">
												<h3 class='text-center'><?php echo lang('common_coupons');?></h3>
												
					            </div>
									</div>
							</div>
							<?php
								
						
							foreach($coupons as $coupon)
							{
								?>
								<div class="invoice-policy coupon">
									<?php
									$coupon_text = H($coupon['name'].' - '.$coupon['description']);
									$coupon_barcode = H($coupon['coupon_code']);
									$begins = date(get_date_format(),strtotime($coupon['start_date']));
									$expires = date(get_date_format(),strtotime($coupon['end_date']));
									?>
									<div><strong><?php echo $coupon_text;?></strong></div>
									<?php echo "<img src='".site_url('barcode')."?barcode=$coupon_barcode' alt=''/>"; ?>
									<div><?php echo lang('common_coupon_code').': '.$coupon_barcode;?></div>
									<div><?php echo lang('common_begins').': '.$begins;?></div>
									<div><?php echo lang('common_expires').': '.$expires;?></div>
								</div><br />
								
								<?php
							}
						?>
							
						<?php
						}?>
						<div id="announcement" class="invoice-policy">
							<?php echo nl2br($this->config->item('announcement_special')) ?>
						</div>
							<?php if ($signature_needed && !$this->config->item('hide_signature')) {?>
								<button class="btn btn-primary text-white hidden-print" id="capture_digital_sig_button"> <?php echo lang('sales_capture_digital_signature'); ?> </button>
								<br />
							<?php
							}
							?>
			      </div>
					
					<?php if(!$this->config->item('hide_signature')) { ?>
			        <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
						<div id="signature">
								<?php if ($signature_needed) {?>
									
									<div id="digital_sig_holder">
										<canvas id="sig_cnv" name="sig_cnv" class="signature" width="500" height="100"></canvas>
										<div id="sig_actions_container" class="pull-right">
											<?php
											if ($this->agent->is_mobile()) //Display done button first
											{
											?>
												<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_done_button"> <?php echo lang('sales_done_capturing_sig'); ?> </button>
												<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_clear_button"> <?php echo lang('sales_clear_signature'); ?> </button>
											<?php
											}
											else  //Display done button 2nd
											{
											?>
												<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_clear_button"> <?php echo lang('sales_clear_signature'); ?> </button>
												<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_done_button"> <?php echo lang('sales_done_capturing_sig'); ?> </button>
											<?php	
											}
											?>
										</div>
									</div>
									<div id="signature_holder">
										<?php 
											if(isset($signature_file_id) && $signature_file_id)
											{
								      		echo img(array('src' => app_file_url($signature_file_id), 'width' => 250));
											}
											else
											{
												echo lang('sales_signature'); ?> ---------------------------------	
											<?php
											}
										?>
									</div>
								<?php } ?>
								
								<?php 
								$this->load->helper('sale');
								if ($is_credit_card_sale)
								{	
									echo lang('sales_card_statement');
								}
								?>
								
						</div>
			        </div>
			        <?php } ?>
			    </div>
			</div>
			<!--container-->
		</div>		
	</div>
</div>
</div>


<div id="duplicate_receipt_holder" style="display: none;">
	
</div>

<?php if ($this->config->item('print_after_sale') && $this->uri->segment(2) == 'complete')
{
?>
<script type="text/javascript">
$(window).bind("load", function() {
	print_receipt();
});
</script>
<?php }  ?>

<script type="text/javascript">

$(document).ready(function(){
	
	$("#edit_sale").click(function(e)
	{
		e.preventDefault();
		bootbox.confirm(<?php echo json_encode(lang('sales_sale_edit_confirm')); ?>,function(result)
		{
			if (result)
			{
				$("#sales_change_form").submit();
			}
		});
	});
	$("#email_receipt").click(function()
	{
		$.get($(this).attr('href'), function()
		{
			show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
			
		});
		
		return false;
	});
});

$('#print_duplicate_receipt').click(function()
{
	if ($('#print_duplicate_receipt').prop('checked'))
	{
	   var receipt = $('#receipt_wrapper').clone();
	   $('#duplicate_receipt_holder').html(receipt);
		$("#duplicate_receipt_holder").addClass('visible-print-block');
		$("#duplicate_receipt_holder .receipt_type_label").text(<?php echo json_encode(lang('sales_duplicate_receipt')); ?>);
		$(".receipt_type_label").show();		
		$(".receipt_type_label").addClass('show_receipt_labels');		
	}
	else
	{
		$("#duplicate_receipt_holder").empty();
		$("#duplicate_receipt_holder").removeClass('visible-print-block');
		$(".receipt_type_label").hide();
		$(".receipt_type_label").removeClass('show_receipt_labels');	
	}
});

<?php
$this->load->helper('sale');
if ($this->config->item('always_print_duplicate_receipt_all') || ($this->config->item('automatically_print_duplicate_receipt_for_cc_transactions') && $is_credit_card_sale))
{
?>
	$("#print_duplicate_receipt").trigger('click');
<?php
}
?>

function print_receipt()
 {
 	window.print();
 	<?php
 	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt'))
 	{
 	?>
 	window.location = '<?php echo site_url('sales'); ?>';
 	<?php
 	}
 	?>
 }
 
 function toggle_gift_receipt()
 {
	 var gift_receipt_text = <?php echo json_encode(lang('sales_gift_receipt')); ?>;
	 var regular_receipt_text = <?php echo json_encode(lang('sales_regular_receipt')); ?>;
	 
	 if ($("#gift_receipt_button").hasClass('regular_receipt'))
	 {
		 $('#gift_receipt_button').addClass('gift_receipt');	 	
		 $('#gift_receipt_button').removeClass('regular_receipt');
		 $("#gift_receipt_button").text(gift_receipt_text);	
		 $('.gift_receipt_element').show();	
	 }
	 else
	 {
		 $('#gift_receipt_button').removeClass('gift_receipt');	 	
		 $('#gift_receipt_button').addClass('regular_receipt');
		 $("#gift_receipt_button").text(regular_receipt_text);
		 $('.gift_receipt_element').hide();	
	 }
 	
 }
 
//timer for sig refresh
var refresh_timer;
var sig_canvas = document.getElementById('sig_cnv');

<?php
//Only use Sig touch on mobile
if ($this->agent->is_mobile())
{
?>
	var signaturePad = new SignaturePad(sig_canvas);
<?php
}
?>
$("#capture_digital_sig_button").click(function()
{	
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
		signaturePad.clear();
	<?php
	}
	else
	{
	?>
		try
		{
			if (TabletConnectQuery()==0)
			{
				bootbox.alert(<?php echo json_encode(lang('sales_unable_to_connect_to_signature_pad')); ?>);
				return;
			}	
		}
		catch(exception) 
		{
			bootbox.alert(<?php echo json_encode(lang('sales_unable_to_connect_to_signature_pad')); ?>);
			return;			
		}
		
	   var ctx = document.getElementById('sig_cnv').getContext('2d');
	   SigWebSetDisplayTarget(ctx);
	   SetDisplayXSize( 500 );
	   SetDisplayYSize( 100 );
	   SetJustifyMode(0);
	   refresh_timer = SetTabletState(1,ctx,50);
	   KeyPadClearHotSpotList();
	   ClearSigWindow(1);
	   ClearTablet();
	<?php
	}
	?>
	
	$("#capture_digital_sig_button").hide();
	$("#digital_sig_holder").show();
});

$("#capture_digital_sig_clear_button").click(function()
{
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
		signaturePad.clear();
	<?php
	}
	else
	{
	?>
   	ClearTablet();	
	<?php
	}
	?>
});

$("#capture_digital_sig_done_button").click(function()
{
	<?php
	//Only use Sig touch on mobile
	if ($this->agent->is_mobile())
	{
	?>
	   if(signaturePad.isEmpty())
	   {
	      bootbox.alert(<?php echo json_encode(lang('sales_no_sig_captured')); ?>);
	   }
	   else
	   {
			SigImageCallback(signaturePad.toDataURL().split(",")[1]);
			$("#capture_digital_sig_button").show();
	   }	
	<?php
	}
	else
	{
	?>
		if(NumberOfTabletPoints() == 0)
		{
		   bootbox.alert(<?php echo json_encode(lang('sales_no_sig_captured')); ?>);
		}
		else
		{
		   SetTabletState(0,refresh_timer);
		   //RETURN TOPAZ-FORMAT SIGSTRING
		   SetSigCompressionMode(1);
			var sig = GetSigString();

		   //RETURN BMP BYTE ARRAY CONVERTED TO BASE64 STRING
		   SetImageXSize(500);
		   SetImageYSize(100);
		   SetImagePenWidth(5);
		   GetSigImageB64(SigImageCallback);
			$("#capture_digital_sig_button").show();
		}
	<?php
	}
	?>
});

function SigImageCallback( str )
{
 $("#digital_sig_holder").hide();
 $.post('<?php echo site_url('sales/sig_save'); ?>', {sale_id: <?php echo json_encode($sale_id_raw); ?>, image: str}, function(response)
 {
	 $("#signature_holder").empty();
	 $("#signature_holder").append('<img src="'+SITE_URL+'/app_files/view/'+response.file_id+'?timestamp='+response.file_timestamp+'" width="250" />');
 }, 'json');

}
 
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
if (isset($trans_cloud_reset) && $trans_cloud_reset)
{
?>
	$.get(<?php echo json_encode(site_url('sales/reset_pin_pad')); ?>);
<?php
}
?>
</script>

<?php if(($is_integrated_credit_sale || $is_sale_integrated_ebt_sale) && $is_sale) { ?>
<script type="text/javascript">
show_feedback('success', <?php echo json_encode(lang('sales_credit_card_processing_success')); ?>, <?php echo json_encode(lang('common_success')); ?>);	
</script>
<?php } ?>

<!-- This is used for mobile apps to print receipt-->
<script type="text/print" id="print_output"><?php echo $company; ?>

<?php echo $this->Location->get_info_for_key('address',isset($override_location_id) ? $override_location_id : FALSE); ?>

<?php echo $this->Location->get_info_for_key('phone',isset($override_location_id) ? $override_location_id : FALSE); ?>

<?php if($website) { ?>
<?php echo $website; ?>
<?php } ?>

<?php echo $receipt_title; ?>

<?php echo $transaction_time; ?>

<?php if(isset($customer))
{
?>
<?php echo lang('common_customer').": ".$customer; ?>
<?php if (!$this->config->item('remove_customer_contact_info_from_receipt')) { ?>
	
<?php if(!empty($customer_address_1)){ ?><?php echo lang('common_address'); ?>: <?php echo $customer_address_1. ' '.$customer_address_2; ?>
	
<?php } ?>
<?php if (!empty($customer_city)) { echo $customer_city.' '.$customer_state.', '.$customer_zip; ?>

<?php } ?>
<?php if (!empty($customer_country)) { echo $customer_country; ?>
	
<?php } ?>
<?php if(!empty($customer_phone)){ ?><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?>
	
<?php } ?>
<?php if(!empty($customer_email)){ ?><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?><?php } ?>

<?php
}
else
{
?>
	
<?php
}
}
?>
<?php echo lang('common_sale_id').": ".$sale_id; ?>
<?php if (isset($sale_type)) { ?>
<?php echo $sale_type; ?>
<?php } ?>

<?php echo lang('common_employee').": ".$employee; ?>

<?php 
if($this->Location->get_info_for_key('enable_credit_card_processing',isset($override_location_id) ? $override_location_id : FALSE))
{
	echo lang('common_merchant_id').': '.$this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE);
}
?>

<?php echo lang('common_item'); ?>            <?php echo lang('common_price'); ?> <?php echo lang('common_quantity'); ?><?php if($discount_exists){echo ' '.lang('common_discount_percent');}?> <?php echo lang('common_total'); ?>

---------------------------------------
<?php
foreach(array_reverse($cart, true) as $line=>$item)
{
?>
<?php echo character_limiter($item['name'], 14,'...'); ?><?php echo strlen($item['name']) < 14 ? str_repeat(' ', 14 - strlen($item['name'])) : ''; ?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item['price'])); ?> <?php echo to_quantity($item['quantity']); ?><?php if($discount_exists){echo ' '.$item['discount'];}?> <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)); ?>

  <?php echo $item['description']; ?>  <?php echo isset($item['serialnumber']) ? $item['serialnumber'] : ''; ?>
	

<?php
}
?>

<?php echo lang('common_sub_total'); ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($subtotal)); ?>


<?php foreach($taxes as $name=>$value) { ?>
<?php echo $name; ?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($value)); ?>

<?php }; ?>

<?php echo lang('common_total'); ?>: <?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($total))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($total)); ?>

<?php echo lang('common_items_sold'); ?>: <?php echo to_quantity($number_of_items_sold); ?>

<?php
	foreach($payments as $payment_id=>$payment)
{ ?>

<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?>  <?php if (($is_integrated_credit_sale || sale_has_partial_credit_card_payment() || sale_has_partial_ebt_payment()) && ($payment['payment_type'] == lang('common_credit') ||  $payment['payment_type'] == lang('sales_partial_credit') || $payment['payment_type'] == lang('common_ebt') || $payment['payment_type'] == lang('common_partial_ebt') ||  $payment['payment_type'] == lang('common_ebt_cash') ||  $payment['payment_type'] == lang('common_partial_ebt_cash'))) { echo $payment['card_issuer']. ': '.$payment['truncated_card']; ?> <?php } else { ?><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> <?php } ?><?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($payment['payment_amount']))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($payment['payment_amount'])); ?>

<?php if ($payment['entry_method']) { ?>
	
<?php echo lang('sales_entry_method'). ': '.$payment['entry_method']; ?>
	
<?php } ?>
<?php if ($payment['tran_type']) { ?><?php echo lang('sales_transaction_type'). ': '.$payment['tran_type']; ?>
	
<?php } ?>
<?php if ($payment['application_label']) { ?><?php echo lang('sales_application_label'). ': '.$payment['application_label']; ?>
	
<?php } ?>
<?php if ($payment['ref_no']) { ?><?php echo lang('sales_ref_no'). ': '.$payment['ref_no']; ?>
	
<?php } ?>
<?php if ($payment['auth_code']) { ?><?php echo lang('sales_auth_code'). ': '.$payment['auth_code']; ?>
	
<?php } ?>
<?php if ($payment['aid']) { ?><?php echo 'AID: '.$payment['aid']; ?>
	
<?php } ?>
<?php if ($payment['tvr']) { ?><?php echo 'TVR: '.$payment['tvr']; ?>

<?php } ?>
<?php if ($payment['tsi']) { ?><?php echo 'TSI: '.$payment['tsi']; ?>
	
<?php } ?>
<?php if ($payment['arc']) { ?><?php echo 'ARC: '.$payment['arc']; ?>
	
<?php } ?>
<?php if ($payment['cvm']) { ?><?php echo 'CVM: '.$payment['cvm']; ?>
<?php } ?>
<?php
}
?>	
<?php foreach($payments as $payment) { $giftcard_payment_row = explode(':', $payment['payment_type']);?>
<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?><?php echo lang('sales_giftcard_balance'); ?>  <?php echo $payment['payment_type'];?>: <?php echo str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row)))); ?>
	<?php }?>
<?php }?>
<?php if ($amount_change >= 0) {?>
<?php echo lang('common_change_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change)); ?>
<?php
}
else
{
?>
<?php echo lang('common_amount_due'); ?>: <?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency(round_to_nearest_05($amount_change * -1))) : str_replace('<span style="white-space:nowrap;">-</span>', '-', to_currency($amount_change * -1)); ?>
<?php
} 
?>
<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt')) {?>
	
<?php echo lang('common_points'); ?>: <?php echo to_currency_no_money($customer_points); ?>
<?php } ?>

<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>

<?php echo lang('sales_customer_account_balance'); ?>: <?php echo to_currency($customer_balance_for_sale); ?>
<?php
}
?>
<?php
if ($ref_no)
{
?>

<?php echo lang('sales_ref_no'); ?>: <?php echo $ref_no; ?>
<?php
}
if (isset($auth_code) && $auth_code)
{
?>

<?php echo lang('sales_auth_code'); ?>: <?php echo $auth_code; ?>
<?php
}
?>
<?php if($show_comment_on_receipt==1){echo $comment;} ?>

<?php if(!$this->config->item('hide_signature')) { ?>
<?php if ($signature_needed) {?>		
<?php echo lang('sales_signature'); ?>: 
---------------------------------------
<?php 
if ($is_credit_card_sale)
{
	echo lang('sales_card_statement');
}
?><?php }?><?php } ?>
<?php  if ($return_policy) { echo wordwrap($return_policy,40);} ?></script>
<?php $this->load->view("partial/footer"); ?>
