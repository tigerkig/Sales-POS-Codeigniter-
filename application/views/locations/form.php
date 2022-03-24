<?php $this->load->view("partial/header"); ?>


<?php if (isset($needs_auth) && $needs_auth) {?>
	<?php echo form_open('locations/check_auth',array('id'=>'location_form_auth','class'=>'form-horizontal')); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<h3 style="margin-left: 80px;"><a href="http://phppointofsale.com/buy_additional.php" target="_blank"><?php echo lang('locations_purchase_additional_licenses'); ?> &raquo;</a></h3>
					<?php if (validation_errors()) {?>
				        <div class="alert alert-danger">
				            <strong><?php echo lang('common_error'); ?></strong>
				            <?php echo validation_errors(); ?>
				        </div>
			        <?php } ?>
					<div class="form-group">
						<?php echo form_label(lang('locations_purchase_email').':', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'purchase_email',
								'id'=>'purchase_email')
							);?>
						</div>	
					</div>
					<div class="form-actions pull-right">
						<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button floating-button btn btn-primary')
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php form_close(); ?>
<?php } else {?>

	<?php echo form_open_multipart('locations/save/'.$location_info->location_id,array('id'=>'location_form','class'=>'form-horizontal','autocomplete'=> 'off')); ?>
		<div class="row" id="form">
			
			<div class="col-md-12">				
				<div class="panel panel-piluku">
					<div class="panel-heading">
		                <h3 class="panel-title">
		                    <i class="ion-edit"></i> 
		                    <?php echo lang("locations_basic_information"); ?>
	    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
		                </h3>
			        </div>

					<div class="panel-body">

						<div class="form-group">
							<?php echo form_label(lang('locations_name').':', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'name',
									'id'=>'name',
									'value'=>$location_info->name)
								);?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_color').':', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'color',
									'id'=>'color',
									'value'=>$location_info->color)
								);?>
							</div>
						</div>
						
					<div class="form-group">	
						<?php echo form_label(lang('common_company').':', 'company',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'company',
							'id'=>'company',
							'value'=>$location_info->company));?>
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_company_logo').':', 'company_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							
							<input type="file" name="company_logo" id="company_logo" class="filestyle" data-icon="false">  	
						</div>	
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_delete_logo').':', 'delete_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox('delete_logo', '1', null,'id="delete_logo"');?>
							<label for="delete_logo"><span></span></label>
						</div>	
					</div>
					<div class="form-group">	
						<?php echo form_label(lang('common_website').':', 'website',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'website',
							'id'=>'website',
							'value'=>$location_info->website));?>
						</div>
					</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_address').':', 'address',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								
								<?php echo form_textarea(array(
									'name'=>'address',
									'id'=>'address',
									'class'=>'form-control text-area',
									'rows'=>'4',
									'cols'=>'30',
									'value'=>$location_info->address));?>								
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_phone').':', 'phone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'phone',
									'id'=>'phone',
									'value'=>$location_info->phone)
								);?>
							</div>
						</div>
					
						<div class="form-group">
							<?php echo form_label(lang('locations_fax').':', 'fax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'fax',
									'id'=>'fax',
									'value'=>$location_info->fax)
								);?>
							</div>
						</div>

						<div class="form-group">
							<?php echo form_label(lang('locations_email').':', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'type'=>'text',
									'class'=>'form-control form-inps',
									'name'=>'email',
									'id'=>'email',
									'value'=>$location_info->email)
								);?>
							</div>
						</div>
						
						<div class="form-group">	
						<?php echo form_label(lang('common_return_policy').':', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'return_policy',
								'id'=>'return_policy',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$location_info->return_policy));?>
							</div>
						</div>
						
						
						<div class="form-group">
							<?php echo form_label(lang('reports_employees').':', 'email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<!-- <input type="text" id="emp" class="emp form-control" name="emp[]" /> -->
								<select class="form-control" name="employees[]" id="employees" multiple>
									<?php  
										foreach($employees as $person_id => $employee)
										{
											$selected = ($employee['has_access'] == true) ? 'selected' : '';
											echo '<option value="'.$person_id.'" '.$selected.'> '.$employee['name'].'</option>';
										}
									?>
								</select>		
							</div>
						</div>						
						
						<div class="form-group add-register-table">	
							<div class="spinner" id="grid-loader" style="display:none">
							  <div class="rect1"></div>
							  <div class="rect2"></div>
							  <div class="rect3"></div>
							</div>
							
							<?php echo form_label(lang('locations_registers').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="table-responsive m-lr-15">
								<table id="price_registers" class="table">
									<thead>
										<tr>
										<th><?php echo lang('common_register_name'); ?></th>
										<th class="emv_terminal_id register-cc-field"><?php echo lang('locations_terminal_id'); ?></th>
										<th class="register-cc-field"><?php echo lang('locations_ip_tran_device_id'); ?></th>
										<th class="register-cc-field"><?php echo lang('locations_card_reader_actions'); ?></th>
										<th><?php echo lang('common_delete'); ?></th>
										</tr>
									</thead>
									
									<tbody>
									<?php 
									$counter = 0;
									foreach($registers->result() as $register) { ?>
										<tr><td><input type="text" class="form-control" name="registers_to_edit[<?php echo $register->register_id; ?>][name]" value="<?php echo H($register->name); ?>" /></td>
											
											<td><input id="terminal_id_<?php echo $counter;?>" type="text" class="form-control emv_terminal_id register-cc-field" name="registers_to_edit[<?php echo $register->register_id; ?>][emv_terminal_id]" value="<?php echo H($register->emv_terminal_id); ?>" /></td>											
											<td><input type="text" class="form-control iptran register-cc-field" name="registers_to_edit[<?php echo $register->register_id; ?>][iptran_device_id]" value="<?php echo H($register->iptran_device_id); ?>" /></td>
											<td>
												<a class="update_parameters_ip_tran register-cc-field" href="javascript:void(0);"><?php echo lang('locations_update_params_ip_tran'); ?></a><span class="register-cc-field"> / </span>
												<a class="init_ip_tran register-cc-field" href="javascript:void(0);"><?php echo lang('locations_init_mercury_emv'); ?></a> 
												<?php if ((!defined("ENVIRONMENT") or ENVIRONMENT == 'development')) { ?>
												<span class="register-cc-field"> / </span><a class="test_mode_ip_tran register-cc-field" href="javascript:void(0);"><?php echo lang('common_test_mode'); ?></a> 													
												<?php } ?>
											</td>
											<td>
										<a class="delete_register" href="javascript:void(0);" data-register-id='<?php echo $register->register_id; ?>'><?php echo lang('common_delete'); ?></a>
									</td></tr>
									<?php 
									$counter++;
								} 
								?>
									</tbody>
								</table>
								<a href="javascript:void(0);" id="add_register"><?php echo lang('locations_add_register'); ?></a>
							</div>
						</div>
						

						<div class="form-group">
							<?php echo form_label("<a href='https://phppointofsale.com/mercury_activate.php' target='_blank'>".lang('locations_enable_credit_card_processing').'</a>:', 'enable_credit_card_processing',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
								'name'=>'enable_credit_card_processing',
								'id'=>'enable_credit_card_processing',
								'value'=>'1',
								'checked'=>$location_info->enable_credit_card_processing));?>
								<label for="enable_credit_card_processing"><span></span></label>
							</div>
						</div>

						<div id="merchant_information">
							
							<div class="form-group">	
								<?php echo form_label(lang('locations_credit_card_processor').':', 'credit_card_processor',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('credit_card_processor', array('mercury' => 'Vantiv/Mercury','heartland' => 'Heartland', 'evo' => 'EVO','worldpay' => 'Worldpay','firstdata' => 'First Data', 'stripe' => 'Stripe','braintree' => 'Braintree', 'other_usb' => lang('locations_other_emv_processor')), $location_info->credit_card_processor, 'class="form-control" id="credit_card_processor"');
									?>
								</div>
							</div>
							
							<div id="emv_info">
								<div class="form-group">	
								<?php echo form_label(lang('locations_emv_terminal_id').':', 'emv_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'emv_merchant_id',
										'id'=>'emv_merchant_id',
										'autocomplete'=>'off',
										'value'=>$location_info->emv_merchant_id));?>
									</div>
								</div>
						
								<div class="form-group">	
								<?php echo form_label(lang('locations_com_port').':', 'com_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'com_port',
										'id'=>'com_port',
										'autocomplete'=>'off',
										'value'=>$location_info->com_port));?> (<?php echo lang('locations_com_9_is_default');?>)
									</div>
								</div>


								<div class="form-group">	
								<?php echo form_label(lang('locations_listener_port').':', 'listener_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'listener_port',
										'id'=>'listener_port',
										'autocomplete'=>'off',
										'value'=>$location_info->listener_port));?> (<?php echo lang('locations_3333_is_default_port_for_listener');?>)
									</div>
								</div>
						<div class="form-group">	
							<?php echo form_label(lang('locations_use_integrated_ebt').':', 'use_integrated_ebt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'ebt_integrated',
								'id'=>'ebt_integrated',
								'value'=>'1',
								'checked'=>$location_info->ebt_integrated));?>
								<label for="ebt_integrated"><span></span></label>
							</div>
						</div>


								<div class="form-group">	
								<?php echo form_label(lang('locations_net_e_pay_server').':', 'net_e_pay_server',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'net_e_pay_server',
										'id'=>'net_e_pay_server',
										'autocomplete'=>'off',
										'value'=>$location_info->net_e_pay_server));?> (<?php echo lang('locations_net_e_pay_info');?>)
									</div>
								</div>
								
								<div class="form-group">	
								<?php echo form_label(lang('locations_secure_device_override_emv').':', 'secure_device_override_emv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'secure_device_override_emv',
										'id'=>'secure_device_override_emv',
										'autocomplete'=>'off',
										'value'=>$location_info->secure_device_override_emv));?>
									</div>
								</div>
								
								
								<div class="form-group">	
								<?php echo form_label(lang('locations_secure_device_override_non_emv').':', 'secure_device_override_non_emv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'secure_device_override_non_emv',
										'id'=>'secure_device_override_non_emv',
										'autocomplete'=>'off',
										'value'=>$location_info->secure_device_override_non_emv));?>
									</div>
								</div>
								
								
								<div class="form-group" id="init_mercury_emv_wrapper" style="display:none;">	
								<?php echo form_label('&nbsp;', 'locations_init_mercury_emv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
								 <div id="ajax-loader" style="text-align:center;display:none"><?php echo img(array('src' => base_url().'assets/img/ajax-loader.gif')); ?></div>
									<button type="button" id="locations_init_mercury_emv" class="btn btn-primary btn-block"><?php echo lang('locations_init_mercury_emv'); ?></button>
									</div>
								</div>							
						</div>
							
						<div id="mercury_hosted_checkout_info">
							<div class="form-group">	
							<?php echo form_label(lang('locations_hosted_checkout_merchant_id').':', 'hosted_checkout_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'hosted_checkout_merchant_id',
									'id'=>'hosted_checkout_merchant_id',
									'autocomplete'=>'off',
									'value'=>$location_info->hosted_checkout_merchant_id));?>
								</div>
							</div>

							<div class="form-group">	
							<?php echo form_label(lang('locations_hosted_checkout_merchant_password').':', 'hosted_checkout_merchant_password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'name'=>'hosted_checkout_merchant_password',
									'id'=>'hosted_checkout_merchant_password',
									'autocomplete'=>'off',
									'class'=>'form-control form-inps',
									'value'=>$location_info->hosted_checkout_merchant_password));?>
								<span id="hosted_checkout_merchant_password_note"><?php echo lang('locations_mercury_password_note'); ?></span>
								</div>
							</div>
						</div>

						<div id="stripe_info">						
							<div class="form-group">	
								<?php echo form_label('<a href="https://support.stripe.com/questions/which-currencies-does-stripe-support" target="_blank">'.lang('locations_currency_code').'</a>:', 'stripe_currency_code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'stripe_currency_code',
									'id'=>'stripe_currency_code',
									'value'=>$location_info->stripe_currency_code ? $location_info->stripe_currency_code : 'usd'));?>
								</div>
							</div>
						
							<div class="form-group">	
							<?php echo form_label(lang('locations_stripe_private').':', 'stripe_private',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'stripe_private',
									'id'=>'stripe_private',
									'autocomplete'=>'off',
									'value'=>$location_info->stripe_private));?>
								</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('locations_stripe_public').':', 'stripe_public',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'stripe_public',
									'id'=>'stripe_public',
									'autocomplete'=>'off',
									'value'=>$location_info->stripe_public));?>
								</div>
							</div>
						</div>
						
						<div id="braintree_info">						
							<div class="form-group">	
								<?php echo form_label(lang('common_merchant_id').':', 'braintree_merchant_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'braintree_merchant_id',
									'id'=>'braintree_merchant_id',
									'value'=>$location_info->braintree_merchant_id));?>
								</div>
							</div>
						
							<div class="form-group">	
							<?php echo form_label(lang('locations_braintree_public_key').':', 'braintree_public_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'braintree_public_key',
									'id'=>'braintree_public_key',
									'autocomplete'=>'off',
									'value'=>$location_info->braintree_public_key));?>
								</div>
							</div>
							
							<div class="form-group">	
							<?php echo form_label(lang('locations_braintree_private_key').':', 'braintree_private_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'braintree_private_key',
									'id'=>'braintree_private_key',
									'autocomplete'=>'off',
									'value'=>$location_info->braintree_private_key));?>
								</div>
							</div>
						</div>						
					</div>
					
						<div class="form-group">	
							<?php echo form_label(lang('locations_receive_stock_alert').':', 'receive_stock_alert',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'receive_stock_alert',
								'id'=>'receive_stock_alert',
								'value'=>'1',
								'checked'=>$location_info->receive_stock_alert));?>
								<label for="receive_stock_alert"><span></span></label>
							</div>
						</div>

						<div class="form-group" id="stock_alert_email_container">	
						<?php echo form_label(lang('locations_stock_alert_email').':', 'stock_alert_email',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
								'type'=>'text',
								'class'=>'form-control form-inps',
								'name'=>'stock_alert_email',
								'id'=>'stock_alert_email',
								'value'=>$location_info->stock_alert_email));?>
							</div>
						</div>
						
						<div class="form-group">	
							<?php echo form_label(lang('locations_timezone').':', 'timezone',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('timezone', $all_timezones, $location_info->timezone, 'class="form-control" id="timezone"');
								?>
							</div>
						</div>

						<div class="form-group">	
						<?php echo form_label(anchor('http://mailchimp.com', lang('locations_mailchimp_api_key'), array('target' => '_blank')).':', 'mailchimp_api_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'mailchimp_api_key',
								'id'=>'mailchimp_api_key',
								'value'=>$location_info->mailchimp_api_key));?>
							</div>
						</div>
	
	
	
	
	<div class="form-group override-taxes-container">
		<?php echo form_label(lang('common_override_default_tax').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
		<div class="col-sm-9 col-md-9 col-lg-10">
			<?php echo form_checkbox(array(
				'name'=>'override_default_tax',
				'id'=>'override_default_tax',
				'class' => 'override_default_tax_checkbox delete-checkbox',
				'value'=>1,
				'checked'=>$location_info->tax_class_id || $location_info->default_tax_1_rate));
			?>
			<label for="override_default_tax"><span></span></label>
		</div>
	</div>
	
	
	<div class="tax-container main <?php if (!($location_info->tax_class_id || $location_info->default_tax_1_rate)){echo 'hidden';} ?>">	
						<div class="form-group">	
							<?php echo form_label(lang('common_tax_class').': ', 'tax_class',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('tax_class', $tax_classes, $location_info->tax_class_id, array('id' =>'tax_class', 'class' => 'form-control tax_class'));?>
							</div>
						</div>
	
						<div style="display:<?php echo $location_info->tax_class_id ? 'none' : 'block';?>">
						<div class="form-group">
							<h4 class="text-center"><?php echo lang('common_or') ?></h4>
						</div>

						<div class="form-group">	
							<?php echo form_label(lang('common_default_tax_rate_1').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'placeholder' => lang('common_tax_name'),
								'name'=>'default_tax_1_name',
								'id'=>'default_tax_1_name',
								'size'=>'10',
								'value'=>$location_info->default_tax_1_name ? $location_info->default_tax_1_name : lang('common_sales_tax_1')));?>
							</div>

							<div class="col-sm-4 col-md-4 col-lg-5">
								<div class="input-group">
									<?php echo form_input(array(
									'class'=>'form-control form-inps-tax',
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_1_rate',
									'id'=>'default_tax_1_rate',
									'size'=>'4',
									'value'=>$location_info->default_tax_1_rate));?>
								  <span class="input-group-addon">%</span>
								</div>
							</div>
						</div>

						<div class="form-group">	
							<?php echo form_label(lang('common_default_tax_rate_2').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'placeholder' => lang('common_tax_name'),
								'name'=>'default_tax_2_name',
								'id'=>'default_tax_2_name',
								'size'=>'10',
								'value'=>$location_info->default_tax_2_name ? $location_info->default_tax_2_name : lang('common_sales_tax_2')));?>
							</div>

							<div class="col-sm-4 col-md-4 col-lg-5">
								<div class="input-group">
									<?php echo form_input(array(
									'class'=>'form-control form-inps-tax'	,
									'placeholder' => lang('common_tax_percent'),
									'name'=>'default_tax_2_rate',
									'id'=>'default_tax_2_rate',
									'size'=>'4',
									'value'=>$location_info->default_tax_2_rate));?>
								  <span class="input-group-addon">%</span>
								</div>
								<div class="clear"></div>
								<?php echo form_checkbox('default_tax_2_cumulative', '1', $location_info->default_tax_2_cumulative ? true : false, 'class="cumulative_checkbox" id="default_tax_2_cumulative"');  ?>
								<label for="default_tax_2_cumulative"><span></span></label>
								<span class="cumulative_label">
									<?php echo lang('common_cumulative'); ?>
								</span>
							</div>
						</div>
					
						<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="display: <?php echo $location_info->default_tax_3_rate ? 'none' : 'block';?>">
							<a href="javascript:void(0);" class="show_more_taxes btn btn-orange btn-round"><?php echo lang('common_show_more');?> &raquo;</a>
						</div>
					
						<div class="more_taxes_container" style="display: <?php echo $location_info->default_tax_3_rate ? 'block' : 'none';?>">
							
							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_3').':', 'default_tax_3_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_3_name',
									'id'=>'default_tax_3_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_3_name ? $location_info->default_tax_3_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<div class="input-group">
										<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'placeholder' => lang('common_tax_percent'),
										'name'=>'default_tax_3_rate',
										'id'=>'default_tax_3_rate',
										'size'=>'4',
										'value'=>$location_info->default_tax_3_rate));?>
									  <span class="input-group-addon">%</span>
									</div>
								</div>
							</div>

							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_4').':', 'default_tax_4_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_4_name',
									'id'=>'default_tax_4_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_4_name ? $location_info->default_tax_4_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<div class="input-group">
										<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'default_tax_4_rate',
										'placeholder' => lang('common_tax_percent'),
										'id'=>'default_tax_4_rate',
										'size'=>'4',
										'value'=>$location_info->default_tax_4_rate));?>
									  <span class="input-group-addon">%</span>
									</div>
								</div>
							</div>

							<div class="form-group">	
								<?php echo form_label(lang('common_default_tax_rate_5').':', 'default_tax_5_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'placeholder' => lang('common_tax_name'),
									'name'=>'default_tax_5_name',
									'id'=>'default_tax_5_name',
									'size'=>'10',
									'value'=>$location_info->default_tax_5_name ? $location_info->default_tax_5_name : ''));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<div class="input-group">
										<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'default_tax_5_rate',
										'placeholder' => lang('common_tax_percent'),
										'id'=>'default_tax_5_rate',
										'size'=>'4',
										'value'=>$location_info->default_tax_5_rate));?>
									  <span class="input-group-addon">%</span>
									</div>
								</div>
							</div>
						</div><!--End more Taxes Container-->		
					</div>
				</div>
						
						<?php echo form_hidden('redirect', $redirect); ?>

						<div class="form-actions pull-right">
							<?php
							if ($purchase_email)
							{
								echo form_hidden('purchase_email', $purchase_email);
							}
							
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_submit'),
								'class'=>'submit_button floating-button btn btn-primary')
							);
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php echo form_close(); ?>
<?php }?>

		

<script type='text/javascript'>
	var submitting = false;
	var add_register_index = 0;
		//validation and submit handling
		$(document).ready(function()
		{			
			//Turn on Credit card processing if we type in IP Tran
	  		 $(document).on('keyup', ".iptran",function()
			 {
					var is_ip_tran_on = false;

					$(".iptran").each(function( index ) 
					{
						if ($(this).val()!= '')
						{
							is_ip_tran_on = true;
						}
					});

					if (is_ip_tran_on && !$("#enable_credit_card_processing").prop('checked'))
					{
						$("#enable_credit_card_processing").click();
					}
					
					check_ip_tran_init();
			  });
			
			check_ip_tran_init();
			
			function check_ip_tran_init()
			{
				var hide_init = false;
			
				$(".iptran").each(function()
				{
					if ($(this).val())
					{
						hide_init = true;
					}
				});
			
				if (hide_init)
				{
					$("#init_mercury_emv_wrapper").hide();		  						
				}
				else
				{
					$("#init_mercury_emv_wrapper").show();		  	
				}
			}
			
			$(document).on('click','.update_parameters_ip_tran', function()
			{
				var emv_merchant_id = $("#emv_merchant_id").val();
				var ip_tran_id = $(this).parent().prev().find('.iptran').val();
				$("#grid-loader").show();	
				
				$.post('<?php echo site_url("locations/mercury_ip_tran_update_parameters");?>',{device_id: ip_tran_id, merchant_id : emv_merchant_id}, function(response)
				{
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					$("#grid-loader").hide();
				},'json');
			});
			
			
			$(document).on('click','.init_ip_tran', function()
			{
				var emv_merchant_id = $("#emv_merchant_id").val();
				var ip_tran_id = $(this).parent().prev().find('.iptran').val();
				$("#grid-loader").show();	
				
				$.post('<?php echo site_url("locations/mercury_ip_tran_emv_param_download");?>',{device_id: ip_tran_id, merchant_id : emv_merchant_id}, function(response)
				{
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					$("#grid-loader").hide();
				},'json');
			});


			$(document).on('click','.test_mode_ip_tran', function()
			{
				var emv_merchant_id = $("#emv_merchant_id").val();
				var ip_tran_id = $(this).parent().prev().find('.iptran').val();
				$("#grid-loader").show();	
				
				$.post('<?php echo site_url("locations/mercury_ip_tran_emv_test_mode");?>',{device_id: ip_tran_id, merchant_id : emv_merchant_id}, function(response)
				{
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
					$("#grid-loader").hide();
				},'json');
			});
			
					
					
			$(document).on('keyup', "#emv_merchant_id",function()
			{
				check_emv_merchant_id();
				
			});
			check_emv_merchant_id();
			
			
			function check_emv_merchant_id()
			{
				if ($("#emv_merchant_id").val()!='')
				{
					$("#init_mercury_emv_wrapper").show();
				}
				else
				{
					$("#init_mercury_emv_wrapper").hide();
				}
			} 	
			
			$("#locations_init_mercury_emv").click(function()
			{
				$("#ajax-loader").show();
				$("#locations_init_mercury_emv").hide();							
				
				var credit_card_processor = $("#credit_card_processor").val();
				var emv_merchant_id = $("#emv_merchant_id").val();
				var com_port = $("#com_port").val();
				var listener_port = $("#listener_port").val();
				var net_e_pay_server = $("#net_e_pay_server").val();
				var secure_device_override_emv= $('#secure_device_override_emv').val();
				var secure_device_override_non_emv = $('#secure_device_override_non_emv').val();
 				var terminal_id_0 = $("#terminal_id_0").val();
				var ebt_integrated = $("#ebt_integrated").prop('checked') ? 1 : 0;
				
				$.post('<?php echo site_url("locations/save_emv_data/".$location_info->location_id);?>', 
				{ebt_integrated:ebt_integrated, terminal_id: terminal_id_0, secure_device_override_emv: secure_device_override_emv, secure_device_override_non_emv: secure_device_override_non_emv, credit_card_processor: credit_card_processor, emv_merchant_id: emv_merchant_id, com_port: com_port, listener_port:listener_port, net_e_pay_server:net_e_pay_server}, function(response) {
					
					if(response.success)
					{
						var emv_param_download_init_params = response.emv_param_download_init_params;

 				   	emv_param_download_init_params['post_data']['ComPort'] = com_port;
				   	emv_param_download_init_params['post_data']['MerchantID'] = emv_merchant_id;
						
		 				terminal_id_0 = $("#terminal_id_0").val();
						
						if (terminal_id_0)
						{
					   	emv_param_download_init_params['post_data']['TerminalID'] = terminal_id_0;
						}
						
						emv_param_download(emv_param_download_init_params['post_host'], listener_port, emv_param_download_init_params.post_data, <?php echo json_encode(lang('locations_init_device_success')); ?>, <?php echo json_encode(lang('locations_unable_to_init_device'));?>, function()
						{
							$("#ajax-loader").hide();
							$("#locations_init_mercury_emv").show();							
						});
					}
					else
					{
						$("#ajax-loader").hide();
						$("#locations_init_mercury_emv").show();
					}
				}, 'json');
			});
			$('#employees').selectize();
			
         $('#color').colorpicker();
			
			$(".delete_register").click(function()
			{
				$("#location_form").append('<input type="hidden" name="registers_to_delete[]" value="'+$(this).data('register-id')+'" />');
				$(this).parent().parent().remove();
			});
	
			$("#add_register").click(function()
			{
				$("#price_registers tbody").append('<tr><td><input type="text" class="registers_to_add form-control" name="registers_to_add['+add_register_index+'][name]" value="" /></td><td><input type="text" class="registers_to_add form-control register-cc-field" name="registers_to_add['+add_register_index+'][emv_terminal_id]" value="" /></td><td><input type="text" class="registers_to_add form-control iptran register-cc-field" name="registers_to_add['+add_register_index+'][iptran_device_id]" value="" /></td><td><a class="update_parameters_ip_tran register-cc-field" href="javascript:void(0);"><?php echo lang('locations_update_params_ip_tran'); ?></a><span class="register-cc-field"> / </span><a class="init_ip_tran register-cc-field" href="javascript:void(0);"><?php echo lang('locations_init_mercury_emv'); ?></a> </td><td>&nbsp;</td></tr>');
				register_cc_fields_show_hide();
				add_register_index++;
			});
						

			if ($("#location_form_auth").length == 1)
			{
			    setTimeout(function(){$(":input:visible:first","#location_form_auth").focus();},100);
			}
			else
			{
			    setTimeout(function(){$(":input:visible:first","#location_form").focus();},100);				
			}
			var submitting = false;
			$('#location_form').validate({
				submitHandler:function(form)
				{
					if (submitting) return;
					submitting = true;
					$('#grid-loader').show();
					$(form).ajaxSubmit({
					success:function(response)
					{
						//Don't let the registers be double submitted, so we change the name
						$(".registers_to_add").attr('name', 'registers_added[]');
						
						$('#grid-loader').hide();
						submitting = false;						
						show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> +' #' + response.location_id : <?php echo json_encode(lang('common_error')); ?>);
						
						
						if(response.redirect==2 && response.success)
						{
							window.location.href = '<?php echo site_url('locations'); ?>';
						}
						else
						{
							$("html, body").animate({ scrollTop: 0 }, "slow");
							$(".form-group").removeClass('has-success has-error');
						}
										
					},
					<?php if(!$location_info->location_id) { ?>
					resetForm: true,
					<?php } ?>
					dataType:'json'
				});

				},
				ignore: '',
				errorClass: "text-danger",
				errorElement: "p",
				errorPlacement: function(error, element) {
				    error.insertBefore(element);
				},
					highlight:function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
					},
					unhighlight: function(element, errorClass, validClass) {
						$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
					},
				rules:
				{
					name:
					{
						required:true,
					},
					phone:
					{
						required:true
					},
					address:
					{
						required:true
					},
					timezone:
					{
						required: true
					},
					"employees[]": "required"
					
		   		},
				messages:
				{
					name:
					{
						required:<?php echo json_encode(lang('locatoins_name_required')); ?>,

					},
					phone:
					{
						required:<?php echo json_encode(lang('locations_phone_required')); ?>,
						number:<?php echo json_encode(lang('locations_phone_valid')); ?>
					},
					address:
					{
						required:<?php echo json_encode(lang('locations_address_required')); ?>
					},
					timezone:
					{
						required:<?php echo json_encode(lang('locations_timezone_required_field')); ?>
					},
					"employees[]": <?php echo json_encode(lang('locations_one_employee_required')); ?>
					
				}
			});
			
			$("#enable_credit_card_processing").change(check_enable_credit_card_processing).ready(check_enable_credit_card_processing);

			$("#credit_card_processor").change(check_credit_card_processor).ready(check_credit_card_processor);
			
			function check_enable_credit_card_processing()
			{
				register_cc_fields_show_hide();
				
				if($("#enable_credit_card_processing").prop('checked'))
				{
					$("#merchant_information").show();
				}
				else
				{
					$("#merchant_information").hide();
				}

			}
			
			function register_cc_fields_show_hide()
			{
				if($("#enable_credit_card_processing").prop('checked'))
				{
					$(".register-cc-field").show();
				}
				else
				{
					$(".register-cc-field").hide();
				}
			}
			
			function check_credit_card_processor()
			{
				var cc_processor = $("#credit_card_processor").val();
				if (cc_processor == 'mercury')
				{
					$("#emv_info").show();
					$("#mercury_hosted_checkout_info").show();
					$("#stripe_info").hide();
					$("#braintree_info").hide();
					
				}
				else if(cc_processor == 'heartland' || cc_processor == 'evo' || cc_processor == 'worldpay' || cc_processor == 'firstdata' || cc_processor == 'other_usb')
				{
					$("#emv_info").show();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").hide();
					$("#braintree_info").hide();
					
				}
				else if (cc_processor == 'stripe')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").show();
					$("#braintree_info").hide();
				}
				else if (cc_processor == 'braintree')
				{
					$("#emv_info").hide();
					$("#mercury_hosted_checkout_info").hide();
					$("#stripe_info").hide();
					$("#braintree_info").show();
				}
			}
			
			$("#receive_stock_alert").change(check_enable_stock_alert).ready(check_enable_stock_alert);
			
			function check_enable_stock_alert()
			{
				if($("#receive_stock_alert").prop('checked'))
				{
					$("#stock_alert_email_container").show();
				}
				else
				{
					$("#stock_alert_email_container").hide();
				}

			}
			
		});
	

		$(".override_default_tax_checkbox").change(function()
		{
			$(this).parent().parent().next().toggleClass('hidden')
		});

</script>
<?php $this->load->view('partial/footer'); ?>