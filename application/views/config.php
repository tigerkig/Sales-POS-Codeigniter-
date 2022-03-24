<?php
$this->load->view("partial/header"); 
$this->load->helper('demo');
$this->load->helper('update');
?>

<div class="manage_buttons">
	<div class="manage-row-options">
		<div class="email_buttons text-center">
			<div class="row">
				<div class="col-md-3 col-sm-2 col-xs-2">
					<div class="search-tpl">
					<div class="input-group">
					  <span class="input-group-addon" id="search-addon"><span class="glyphicon glyphicon-search"></span></span>
						<input aria-describedby="search-addon" type="text" class="form-control" name ="search" id="search"  placeholder="<?php echo lang('common_search') ?>" value="<?php echo H($search); ?>" />
					</div>
					</div>
				</div>
				<div class="col-md-9 col-sm-10 col-xs-10">
					<div class="pull-right">
						<?php echo anchor('config/backup', '<span class="ion-load-a"> </span><span class="">' . lang('config_backup_database') . '</span>', array('class' => 'btn btn-primary btn-lg dbBackup hidden-xs')); ?>
						<?php echo anchor('config/is_update_available', '<span class="glyphicon glyphicon-import"></span> <span class="hidden-xs hidden-sm">' . lang('common_check_for_update'). '</span>', array('class' => 'checkForUpdate btn btn-success btn-lg hidden-xs')); ?> 
						<?php echo anchor('config/optimize','<span class="ion-wand"></span> <span class="hidden-xs hidden-sm">' . lang('config_optimize_database') . '</span>', array('class' => 'dbOptimize btn btn-primary btn-lg')); ?> 
					</div>
				</div>
			</div>
		</div><!-- end email_buttons -->
	</div><!-- manage-row-options -->
</div><!-- manage_buttons -->

<div class="manage_buttons buttons-list config-page container">
	
</div>
<div class="text-center location-settings">
	<?php echo lang('config_looking_for_location_settings').' '.anchor($this->Location->count_all() > 1 ? 'locations' : 'locations/view/1', lang('module_locations').' '.lang('config_module'), 'class="btn btn-info"');?>
</div>
<div class="config-panel">
	
	<?php
	//for help window popups
	$popupAtts = array(
    'width'       => 800,
    'height'      => 600,
    'scrollbars'  => 'yes',
    'status'      => 'yes',
    'resizable'   => 'yes',
    'screenx'     => 0,
    'screeny'     => 0,
    'window_name' => '_blank'
	);
	
	function create_section($title, $doc = 'store-configuration-options', $section = '')
	{
		$popupAtts = array(
	    'width'       => 800,
	    'height'      => 600,
	    'scrollbars'  => 'yes',
	    'status'      => 'yes',
	    'resizable'   => 'yes',
	    'screenx'     => 0,
	    'screeny'     => 0,
	    'window_name' => '_blank'
		);
		
		return $title . ' ' . anchor_popup('https://support.phppointofsale.com/v'.
							DOCUMENTATION_VERSION .'/docs/' . $doc . '#' . $section,
							' <span class="glyphicon glyphicon-info-sign"></span>', $popupAtts);
	}
	?>
	
	<div class="row">
		<?php echo form_open_multipart('config/save/',array('id'=>'config_form','class'=>'form-horizontal', 'autocomplete'=> 'off'));  ?>
	<?php 
		$this->load->helper('update');
		if (is_on_phppos_host() && !is_on_demo_host() && !empty($cloud_customer_info)) {?>
		<!-- Billing Information -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo lang("config_billing_info"); ?>
				</div>
				<div class="panel-body">
					<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> <?php echo lang('config_update_billing');?></div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_billing')) ?>">	
						<?php if ($cloud_customer_info['payment_provider'] == 'paypal') { ?>
							
							<div class="row">
								<div class="col-md-10 col-md-offset-2">
									<?php echo lang('config_billing_is_managed_through_paypal');?>
								</div>
							</div>
							
						<?php } else { ?>
							<div class="row">
								<div class="col-md-4 col-md-offset-2">
									<a class="btn btn-block btn-update-billing btn-primary" href="https://phppointofsale.com/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>" target="_blank"><?php echo lang('common_update_billing_info');?></a>				
								</div>
								<div class="col-md-4">
									<a class="btn btn-block btn-update-billing btn-default" href="https://phppointofsale.com/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>&cancel=1" target="_blank"><?php echo lang('config_cancel_account');?></a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
		<!-- Company Information -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang("config_company_info"), 'store-configuration-options', 'section-company-information')  ?>
				</div>
				<div class="panel-body">
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
						<?php echo form_label(lang('common_company_logo').':', 'company_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							
							<input type="file" name="company_logo" id="company_logo" class="filestyle" data-icon="false">  	
						</div>	
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
						<?php echo form_label(lang('common_delete_logo').':', 'delete_logo',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox('delete_logo', '1', null,'id="delete_logo"');?>
							<label for="delete_logo"><span></span></label>
						</div>	
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
						<?php echo form_label(lang('common_company').':', 'company',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'company',
							'id'=>'company',
							'value'=>$this->config->item('company')));?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_company')) ?>">	
						<?php echo form_label(lang('common_website').':', 'website',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'website',
							'id'=>'website',
							'value'=>$this->config->item('website')));?>
						</div>
					</div>
				</div>
			</div>	
		</div>
		
		<!-- Taxes -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_taxes_info'), 'store-configuration-options', 'section-taxes')  ?>
				</div>
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
						<?php echo form_label(lang('common_prices_include_tax').':', 'prices_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'prices_include_tax',
							'id'=>'prices_include_tax',
							'value'=>'prices_include_tax',
							'checked'=>$this->config->item('prices_include_tax')));?>
							<label for="prices_include_tax"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
						<?php echo form_label(lang('config_charge_tax_on_recv').':', 'charge_tax_on_recv',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'charge_tax_on_recv',
							'id'=>'charge_tax_on_recv',
							'value'=>'charge_tax_on_recv',
							'checked'=>$this->config->item('charge_tax_on_recv')));?>
							<label for="charge_tax_on_recv"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
						<?php echo form_label(lang('config_use_tax_value_at_all_locations').':', 'use_tax_value_at_all_locations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'use_tax_value_at_all_locations',
							'id'=>'use_tax_value_at_all_locations',
							'value'=>'use_tax_value_at_all_locations',
							'checked'=>$this->Appconfig->all_locations_use_global_tax()));?>
							<label for="use_tax_value_at_all_locations"><span></span></label>
						</div>
					</div>					
									
					<!-- Tax Classes -->
					<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
						<?php echo form_label(lang('config_tax_classes').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-md-9 col-sm-9 col-lg-10">
							<div class="table-responsive">
								<table id="tax_classes" class="table">
										<thead>
											<tr>
												<th><?php echo lang('common_name'); ?></th>
												<th><?php echo lang('common_tax_name'); ?></th>
												<th><?php echo lang('common_tax_percent'); ?></th>			
												<th><?php echo lang('common_cumulative'); ?></th>	
												<th><?php echo lang('common_default'); ?></th>
												<th><?php echo lang('common_delete'); ?></th>		
												<th><?php echo lang('common_add'); ?></th>
												<th><?php echo lang('config_sort'); ?></th>
											</tr>
										</thead>
							
										<tbody>
								
										<?php
										 foreach($tax_classes as $tax_class_id => $tax_class) { 
											 ?>
											<tr data-index="<?php echo H($tax_class_id); ?>">
												<td class="tax_class_name top">
													<input type="text" class="rates form-control" name="tax_classes[<?php echo H($tax_class_id); ?>][name]" value="<?php echo H($tax_class['name']);?>" />
													<?php foreach($tax_class['taxes'] as $tax_class_tax) { ?>
														<input type="hidden" name="taxes[<?php echo H($tax_class_id); ?>][tax_class_tax_id][]" value="<?php echo H($tax_class_tax['id']); ?>">
													<?php } ?>
												</td>
												<td class="tax_class_rate_name top">
										
													<?php foreach($tax_class['taxes'] as $tax_class_taxes_data) { 
														?>
														<input data-tax-class-tax-id="<?php echo H($tax_class_taxes_data['id']); ?>" type="text" class="rates form-control" name="taxes[<?php echo H($tax_class_id); ?>][name][]" value="<?php echo H($tax_class_taxes_data['name']);?>" />
													<?php } ?>
												</td>
									
												<td class="tax_class_rate_percent top">
													<?php foreach($tax_class['taxes'] as $tax_class_taxes_data) { ?>
														<input type="text" class="rates form-control" name="taxes[<?php echo H($tax_class_id); ?>][percent][]" value="<?php echo H($tax_class_taxes_data['percent']);?>" />
													<?php } ?>
												</td>
												
												<td class="tax_class_rate_cumulative top">
													<?php 
													$tax_class_cum_counter = 0;
													foreach($tax_class['taxes'] as $tax_class_data) { 
														$cum_id = 'tax_class_'.$tax_class_id.'_cumulative_'.$tax_class_cum_counter;
														
														if ($tax_class_cum_counter == 1)
														{
													?>
															<?php echo form_checkbox('taxes['.H($tax_class_id).'][cumulative][]', '1', $tax_class_data['cumulative'],'id="'.$cum_id.'" class="form-control rates cumulative_checkbox"');  ?>
															<label class="tax_class_cumulative_element" for="<?php echo $cum_id; ?>"><span></span></label>			
													<?php
												}
												else
												{
													?>
															<?php 
															echo form_hidden('taxes['.H($tax_class_id).'][cumulative][]', '0');
															echo form_checkbox('taxes['.H($tax_class_id).'][cumulative][]', '1', $tax_class_data['cumulative'],'disabled id="'.$cum_id.'" class="form-control rates cumulative_checkbox invisible"');  ?>
															<label class="tax_class_cumulative_element invisible" for="<?php echo $cum_id; ?>"><span></span></label>
													<?php
												}
													$tax_class_cum_counter++;
												 } ?>
												</td>

												<td class="tax_class_rate_default">
													<?php 
													$tax_class_default_counter = 0;
														$default_id = 'tax_class_'.$tax_class_id.'_default_'.$tax_class_default_counter;
													
														echo form_radio(array(
															'id' => $default_id,
															'name' =>'tax_class_id',
															'value' => $tax_class_id,
															'checked' => $this->config->item('tax_class_id') == $tax_class_id ? 'checked' : '',
														)); 
													?>
													<label class="tax_class_default_element" for="<?php echo $default_id; ?>"><span></span></label>
																												
													<?php
													$tax_class_default_counter++;
													?>
												</td>
												
												
									
												<td>
													<a class="delete_tax_rate tax_table_rate_text_element"><?php echo lang('common_delete'); ?></a>
												</td>
												<td><a href="javascript:void(0);" class="add_tax_rate"><?php echo lang('config_add_rate'); ?></a></td>
												<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
										</tr>
							
										<?php } ?>
										</tbody>
									</table>
								
									<a href="javascript:void(0);" class="add_tax_class"><?php echo lang('config_add_tax_class'); ?></a>
								</div>								
						</div><!-- end col -->
				</div><!-- end form-group -->
					
					<?php if (!$this->config->item('tax_class_id')) {?>
					
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
							<?php echo form_label(lang('common_default_tax_rate_1').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-4 col-md-4 col-lg-5">
								<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'default_tax_1_name',
								'placeholder' => lang('common_tax_name'),
								'id'=>'default_tax_1_name',
								'size'=>'10',
								'value'=>$this->config->item('default_tax_1_name')!==NULL ? $this->config->item('default_tax_1_name') : lang('common_sales_tax_1')));?>
							</div>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<div class="input-group">
										<?php echo form_input(array(
										'class'=>'form-control form-inps-tax',
										'placeholder' => lang('common_tax_percent'),
										'name'=>'default_tax_1_rate',
										'id'=>'default_tax_1_rate',
										'size'=>'4',
										'value'=>$this->config->item('default_tax_1_rate')));?>
									  <span class="input-group-addon">%</span>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						
							<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
								<?php echo form_label(lang('common_default_tax_rate_2').':', 'default_tax_1_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-4 col-md-4 col-lg-5">
									<?php echo form_input(array(
									'class'=>'form-control form-inps',
									'name'=>'default_tax_2_name',
									'placeholder' => lang('common_tax_name'),
									'id'=>'default_tax_2_name',
									'size'=>'10',
									'value'=>$this->config->item('default_tax_2_name')!==NULL ? $this->config->item('default_tax_2_name') : lang('common_sales_tax_2')));?>
								</div>

								<div class="col-sm-4 col-md-4 col-lg-5">
									<div class="input-group">
										<?php echo form_input(array(
										'class'=>'form-control form-inps-tax',	
										'name'=>'default_tax_2_rate',
										'placeholder' => lang('common_tax_percent'),
										'id'=>'default_tax_2_rate',
										'size'=>'4',
										'value'=>$this->config->item('default_tax_2_rate')));?>
									  <span class="input-group-addon">%</span>
									</div>
									<div class="clear"></div>
									<?php echo form_checkbox('default_tax_2_cumulative', '1', $this->config->item('default_tax_2_cumulative') ? true : false, 'id="default_tax_2_cumulative" class="cumulative_checkbox"');  ?>
									<label for="default_tax_2_cumulative"><span></span></label>
									<span class="cumulative_label">
										<?php echo lang('common_cumulative'); ?>
									</span>
								</div>
									<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'none' : 'block';?>">
										<a href="javascript:void(0);" class="show_more_taxes btn btn-orange btn-round"><?php echo lang('common_show_more');?> &raquo;</a>
									</div>
						
									<div class="col-md-12 more_taxes_container" style="display: <?php echo $this->config->item('default_tax_3_rate') ? 'block' : 'none';?>">
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_default_tax_rate_3').':', 'default_tax_3_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'name'=>'default_tax_3_name',
												'placeholder' => lang('common_tax_name'),
												'id'=>'default_tax_3_name',
												'size'=>'10',
												'value'=>$this->config->item('default_tax_3_name')!==NULL ? $this->config->item('default_tax_3_name') : ''));?>
											</div>
						
											<div class="col-sm-4 col-md-4 col-lg-5">
												<div class="input-group">
													<?php echo form_input(array(
													'class'=>'form-control form-inps-tax',
													'placeholder' => lang('common_tax_percent'),
													'name'=>'default_tax_3_rate',
													'id'=>'default_tax_3_rate',
													'size'=>'4',
													'value'=>$this->config->item('default_tax_3_rate')));?>
												  <span class="input-group-addon">%</span>
												</div>
												<div class="clear"></div>
											</div>
										</div>
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_default_tax_rate_4').':', 'default_tax_4_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'placeholder' => lang('common_tax_name'),
												'name'=>'default_tax_4_name',
												'id'=>'default_tax_4_name',
												'size'=>'10',
												'value'=>$this->config->item('default_tax_4_name')!==NULL ? $this->config->item('default_tax_4_name') : ''));?>
											</div>
						
											<div class="col-sm-4 col-md-4 col-lg-5">
												<div class="input-group">
													<?php echo form_input(array(
													'class'=>'form-control form-inps-tax',
													'placeholder' => lang('common_tax_percent'),
													'name'=>'default_tax_4_rate',
													'id'=>'default_tax_4_rate',
													'size'=>'4',
													'value'=>$this->config->item('default_tax_4_rate')));?>
												  <span class="input-group-addon">%</span>
												</div>
												<div class="clear"></div>
											</div>
										</div>
							
										<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_taxes')) ?>">	
											<?php echo form_label(lang('common_default_tax_rate_5').':', 'default_tax_5_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
											<div class="col-sm-4 col-md-4 col-lg-5">
												<?php echo form_input(array(
												'class'=>'form-control form-inps',
												'placeholder' => lang('common_tax_name'),
												'name'=>'default_tax_5_name',
												'id'=>'default_tax_5_name',
												'size'=>'10',
												'value'=>$this->config->item('default_tax_5_name')!==NULL ? $this->config->item('default_tax_5_name') : ''));?>
											</div>
						
											<div class="col-sm-4 col-md-4 col-lg-5">
												<div class="input-group">
													<?php echo form_input(array(
													'class'=>'form-control form-inps-tax',
													'placeholder' => lang('common_tax_percent'),
													'name'=>'default_tax_5_rate',
													'id'=>'default_tax_5_rate',
													'size'=>'4',
													'value'=>$this->config->item('default_tax_5_rate')));?>
												  <span class="input-group-addon">%</span>
												</div>
												<div class="clear"></div>
											</div>
										</div>
									</div>
							</div>
							<?php } ?>
							
					</div><!-- end -->
				</div><!-- end -->
			</div><!-- end panel-->
					
		<!-- Currency -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_currency_info'), 'store-configuration-options', 'section-currency')  ?>
				</div>
				<div class="panel-body">

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
						<?php echo form_label(lang('config_currency_symbol').':', 'currency_symbol',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'currency_symbol',
							'id'=>'currency_symbol',
							'value'=>$this->config->item('currency_symbol')));?>
						</div>
					</div>	
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
						<?php echo form_label(lang('config_currency_code').':', 'currency_code',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'currency_code',
							'id'=>'currency_code',
							'value'=>$this->config->item('currency_code')));?>
						</div>
					</div>	
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
					<?php echo form_label(lang('config_currency_exchange_rates').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="table-responsive col-sm-9 col-md-9 col-lg-10">
						<table id="currency_exchange_rates" class="table">
							<thead>
								<tr>
								<th><?php echo lang('common_exchange_to'); ?></th>
								<th><?php echo lang('config_currency_symbol'); ?></th>
								<th><?php echo lang('config_currency_symbol_location'); ?></th>
								<th><?php echo lang('config_number_of_decimals'); ?></th>
								<th><?php echo lang('config_thousands_separator'); ?></th>
								<th><?php echo lang('config_decimal_point'); ?></th>
								<th><?php echo lang('config_exchange_rate'); ?></th>
								<th><?php echo lang('common_delete'); ?></th>
								</tr>
							</thead>
							
							<tbody>
							<?php foreach($currency_exchange_rates->result() as $currency_exchange_rate) { ?>
								<tr>
									<td><input type="text" name="currency_exchange_rates_to[]" class="form-control" value="<?php echo H($currency_exchange_rate->currency_code_to); ?>" /></td>
									<td><input type="text" name="currency_exchange_rates_symbol[]" class="form-control" value="<?php echo H($currency_exchange_rate->currency_symbol); ?>" /></td>
									<td><?php echo form_dropdown('currency_exchange_rates_symbol_location[]', array(
			 							'before'    => lang('config_before_number'),
			 							'after'    => lang('config_after_number'),
									),$currency_exchange_rate->currency_symbol_location,'class="form-control"');?></td>
									<td><?php echo form_dropdown('currency_exchange_rates_number_of_decimals[]', array(
				 							''  => lang('config_let_system_decide'),
				 							'0'    => '0',
				 							'1'    => '1',
				 							'2'    => '2',
				 							'3'    => '3',
				 							'4'    => '4',
				 							'5'    => '5',
										),$currency_exchange_rate->number_of_decimals
				 							 , 'class="form-control" id="number_of_decimals"');
										?></td>
									<td><input type="text" name="currency_exchange_rates_thousands_separator[]" class="form-control" value="<?php echo H($currency_exchange_rate->thousands_separator); ?>" /></td>
									<td><input type="text" name="currency_exchange_rates_decimal_point[]" class="form-control" value="<?php echo H($currency_exchange_rate->decimal_point); ?>" /></td>
									<td><input type="text" name="currency_exchange_rates_rate[]" class="form-control" value="<?php echo H(to_currency_no_money($currency_exchange_rate->exchange_rate)); ?>" /></td>
									<td><a class="delete_currency_exchange_rate text-primary" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						
						<a href="javascript:void(0);" id="add_exchange_rate"><?php echo lang('config_add_currency_exchange_rate'); ?></a>
						</div>
					</div>
					
					
 					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
 					<?php echo form_label(lang('config_currency_symbol_location').':', 'currency_symbol_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
 						<div class="col-sm-9 col-md-9 col-lg-10">
 						<?php echo form_dropdown('currency_symbol_location', array(
 							'before'    => lang('config_before_number'),
 							'after'    => lang('config_after_number'),
						),
 							$this->config->item('currency_symbol_location')===NULL ? 'before' : $this->config->item('currency_symbol_location') , 'class="form-control" id="currency_symbol_location"');
 							?>
 						</div>						
 					</div>				
							
 					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
 					<?php echo form_label(lang('config_number_of_decimals').':', 'number_of_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
 						<div class="col-sm-9 col-md-9 col-lg-10">
 						<?php echo form_dropdown('number_of_decimals', array(
 							''  => lang('config_let_system_decide'),
 							'0'    => '0',
 							'1'    => '1',
 							'2'    => '2',
 							'3'    => '3',
 							'4'    => '4',
 							'5'    => '5',
						),
 							$this->config->item('number_of_decimals')===NULL ? '' : $this->config->item('number_of_decimals') , 'class="form-control" id="number_of_decimals"');
 							?>
 						</div>						
 					</div>				
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
						<?php echo form_label(lang('config_thousands_separator').':', 'thousands_separator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'thousands_separator',
							'id'=>'thousands_separator',
							'value'=>$this->config->item('thousands_separator') ? $this->config->item('thousands_separator') : ','));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
						<?php echo form_label(lang('config_decimal_point').':', 'decimal_point',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 input-field">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'decimal_point',
							'id'=>'decimal_point',
							'value'=>$this->config->item('decimal_point') ? $this->config->item('decimal_point') : '.'));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_currency')) ?>">	
					<?php echo form_label(lang('config_currency_denoms').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="table-responsive col-sm-9 col-md-4 col-lg-4">
						<table id="currency_denoms" class="table">
							<thead>
								<tr>
								<th><?php echo lang('common_denomination'); ?></th>
								<th><?php echo lang('config_currency_value'); ?></th>
								<th><?php echo lang('common_delete'); ?></th>
								</tr>
							</thead>
							
							<tbody>
							<?php foreach($currency_denoms->result() as $currency_denom) { ?>
								<tr>
									<td><input type="text" name="currency_denoms_name[]" class="form-control" value="<?php echo H($currency_denom->name); ?>" /></td>
									<td><input type="text" name="currency_denoms_value[]" class="form-control" value="<?php echo H(to_currency_no_money($currency_denom->value)); ?>" /></td>
									<td><a class="delete_currency_denom text-primary" href="javascript:void(0);"><?php echo lang('common_delete'); ?></a></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						
						<a href="javascript:void(0);" id="add_denom"><?php echo lang('config_add_currency_denom'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Payment Types -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_payment_types_info'), 'store-configuration-options', 'section-payment-types')  ?>
				</div>
				<div class="panel-body">
						
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
						<?php echo form_label(lang('config_payment_types').':', 'additional_payment_types',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_cash'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_check'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_giftcard'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_debit'); ?></a> 
							<a href="#" class="btn btn-primary payment_types"><?php echo lang('common_credit'); ?></a>
							<br>
							<br>
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
								'name'=>'additional_payment_types',
								'id'=>'additional_payment_types',
								'size'=> 40,
								'value'=>$this->config->item('additional_payment_types')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
						<?php echo form_label(lang('config_default_payment_type').':', 'default_payment_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('default_payment_type', $payment_options, $this->config->item('default_payment_type'),'class="form-control" id="default_payment_type"'); ?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
						<?php echo form_label(lang('config_enable_ebt_payments'). ':', 'enable_ebt_payments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
							'name'=>'enable_ebt_payments',
							'id'=>'enable_ebt_payments',
							'value'=>'1',
							'checked'=>$this->config->item('enable_ebt_payments')));?>
							<label for="enable_ebt_payments"><span></span></label>
						</div>
					</div>

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">
						<?php echo form_label(lang('config_enable_wic'). ':', 'enable_wic',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
							'name'=>'enable_wic',
							'id'=>'enable_wic',
							'value'=>'1',
							'checked'=>$this->config->item('enable_wic')));?>
							<label for="enable_wic"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
						<?php echo form_label(lang('config_prompt_for_ccv_swipe').':', 'prompt_for_ccv_swipe',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'prompt_for_ccv_swipe',
							'id'=>'prompt_for_ccv_swipe',
							'value'=>'1',
							'checked'=>$this->config->item('prompt_for_ccv_swipe')));?>
							<label for="prompt_for_ccv_swipe"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_payment')) ?>">	
					<?php echo form_label(lang('config_do_not_force_http').':', 'do_not_force_http',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_force_http',
							'id'=>'do_not_force_http',
							'value'=>'do_not_force_http',
							'checked'=>$this->config->item('do_not_force_http')));?>
							<label for="do_not_force_http"><span></span></label>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<!-- Price Rules-->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_price_rules_info'), 'store-configuration-options', 'section-price-rules')  ?>
				</div>
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_rules')) ?>">	
						<?php echo form_label(lang('config_disable_price_rules_dialog').':', 'disable_price_rules_dialog',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_price_rules_dialog',
							'id'=>'disable_price_rules_dialog',
							'value'=>'disable_price_rules_dialog',
							'checked'=>$this->config->item('disable_price_rules_dialog')));?>
							<label for="disable_price_rules_dialog"><span></span></label>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<!-- Orders and Deliveries -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_orders_and_deliveries_info'), 'store-configuration-options', 'section-orders_deliveries')  ?>
				</div>
				<div class="panel-body">
					
					
				<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
					<?php echo form_label(lang('config_do_not_tax_service_items_for_deliveries').':', 'do_not_tax_service_items_for_deliveries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_checkbox(array(
						'name'=>'do_not_tax_service_items_for_deliveries',
						'id'=>'do_not_tax_service_items_for_deliveries',
						'value'=>'1',
						'checked'=>$this->config->item('do_not_tax_service_items_for_deliveries')));?>
						<label for="do_not_tax_service_items_for_deliveries"><span></span></label>
					</div>
				</div>
					
				<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
					<?php echo form_label(lang('config_shipping_providers').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-md-9 col-sm-9 col-lg-10">
							<div class="table-responsive">
								<table id="shipping_providers" class="table">
									<thead>
										<tr>
											<th><?php echo lang('common_name'); ?></th>
											<th><?php echo lang('config_rate_name'); ?></th>
											<th><?php echo lang('config_rate_fee'); ?></th>			
											<th><?php echo lang('config_delivery_time'); ?></th>
															
											<th><?php echo lang('common_default'); ?></th>
											<th><?php echo lang('common_delete'); ?></th>
											
											<th><?php echo lang('common_add'); ?></th>
											<th><?php echo lang('config_sort'); ?></th>
										</tr>
									</thead>
									
									<tbody>
										
									<?php
									 foreach($shipping_providers->result_array() as $shipping_provider) { 
										 $provider_id = $shipping_provider['id'];
										 
										 $methods = $this->Shipping_method->get_all($provider_id)->result_array();
										 $sorted_shipping_methods = array();
										 
										 foreach($methods as $method)
										 {
											 $sorted_shipping_methods['id'][] = $method['id'];
											 $sorted_shipping_methods['name'][] = $method['name'];
											 $sorted_shipping_methods['fee'][] = $method['fee'];
											 $sorted_shipping_methods['time_in_days'][] = $method['time_in_days'];
											 $sorted_shipping_methods['is_default'][] = $method['is_default'];
										 }
											
										 //If we don't have any methods the 1st row should have blanks
										 if (empty($sorted_shipping_methods['name']))
										 {
	  										$sorted_shipping_methods['id'][] = '';
	 										 	$sorted_shipping_methods['name'][] = '';
											 	$sorted_shipping_methods['fee'][] = 0;
											 	$sorted_shipping_methods['fee_tax'][] = 0;
											 	$sorted_shipping_methods['time_in_days'][] = '';
											 	$sorted_shipping_methods['is_default'][] = '';
										 }
											
										 ?>
										<tr data-index="<?php echo H($provider_id); ?>">
											<td class="shipping_provider_name top">
												<input type="text" class="rates form-control" name="providers[<?php echo H($provider_id); ?>][name]" value="<?php echo H($shipping_provider['name']);?>" />
												<?php foreach($sorted_shipping_methods['name'] as $index => $name) { ?>
													<input type="hidden" name="methods[<?php echo H($provider_id); ?>][method_id][]" value="<?php echo H($sorted_shipping_methods['id'][$index]); ?>">
												<?php } ?>
											</td>
											<td class="delivery_rate_name top">
												
												<?php foreach($sorted_shipping_methods['name'] as $index => $name) { ?>
													<input data-method-id="<?php echo H($sorted_shipping_methods['id'][$index]); ?>" type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][name][]" value="<?php echo H($name);?>" />
												<?php } ?>
											</td>
											
											<td class="delivery_fee top">
												<?php foreach($sorted_shipping_methods['fee'] as $fee) { ?>
													<input type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][fee][]" value="<?php echo H(to_currency_no_money($fee));?>" />
												<?php } ?>
											</td>
											
											<td class="delivery_time top">
												<?php foreach($sorted_shipping_methods['time_in_days'] as $time_in_days) { ?>
													<input type="text" class="rates form-control" name="methods[<?php echo H($provider_id); ?>][time_in_days][]" value="<?php echo H(to_quantity($time_in_days, ''));?>" />
												<?php } ?>
											</td>
											
											
											<td class="delivery_default top">
												<?php 
												$i = 0;
												foreach($sorted_shipping_methods['is_default'] as $is_default) { 
													echo form_radio(array(
														'id' => 'default_shipping_rate_'. $provider_id . '_' . $i,
														'name' =>'methods['. H($provider_id) .']'.'[is_default][]',
														'value' => '1',
														'checked' => $is_default == 1 ? 'checked' : '',
													)); 
												?>
												<label class="shipping_table_rate_element" for="<?php echo H('default_shipping_rate_' . $provider_id . '_' . $i); ?>"><span></span></label>
												<?php
													$i++;
												} ?>
											</td>
											<td>
												<a class="delete_rate shipping_table_rate_text_element"><?php echo lang('common_delete'); ?></a>
											</td>
											<td><a href="javascript:void(0);" class="add_delivery_rate"><?php echo lang('config_add_rate'); ?></a></td>
											<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
									</tr>
									
									<?php } ?>
									</tbody>
								</table>
								
								<a href="javascript:void(0);" class="add_shipping_provider"><?php echo lang('config_add_shipping_provider'); ?></a>
								</div>
							</div>
						</div>
						
						<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_orders_deliveries')) ?>">	
							<?php echo form_label(lang('config_shipping_zones').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-md-9 col-sm-9 col-lg-10">
									<div class="table-responsive">
										<table id="shipping_zones" class="table">
											<thead>
												<tr>
													<th><?php echo lang('common_name'); ?></th>
													<th><?php echo lang('common_zips'); ?></th>
													<th><?php echo lang('common_fee'); ?></th>
													<th><?php echo lang('config_tax_class'); ?></th>
													<th><?php echo lang('common_delete'); ?></th>
													<th><?php echo lang('config_sort'); ?></th>
												</tr>
											</thead>
									
											<tbody>
										
											<?php
											 foreach($shipping_zones->result_array() as $shipping_zone) { 
												 $zone_id = $shipping_zone['id'];
												 $zips_for_zone = array();
												 $zips_for_zone_str = '';
												 foreach($this->Zip->get_zips_for_zone($zone_id)->result_array() as $zip_row)
												 {
												 	$zips_for_zone[] = $zip_row['name'];
												 }
												 
												 
												 $zips_for_zone_str = implode('|',$zips_for_zone);
												?>
												<tr data-index="<?php echo H($zone_id); ?>">
													<td class="shipping_zone_name top" style="width: 10%; min-width:100px;">
														<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][name]" value="<?php echo H($shipping_zone['name']);?>" />
													</td>
													
													<td class="shipping_zone_zips top" style="width: 50%;">
														<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][zips]" value="<?php echo H($zips_for_zone_str);?>" />
													</td>
													
													<td class="shipping_zone_fee top" style="width: 10%; min-width:100px;">
														<input type="text" class="zones form-control" name="zones[<?php echo H($zone_id); ?>][fee]" value="<?php echo H(to_currency_no_money($shipping_zone['fee']));?>" />
													</td>
													
													<td class="shipping_zone_tax_group top" style="width: 10%; min-width:200px;">
														<select class="zones form-control" name="zones[<?php echo H($zone_id); ?>][tax_class_id]">
															<?php foreach($tax_groups as $tax_group) { ?>
																<option value="<?php echo $tax_group['val'] ?>" <?php echo $shipping_zone['tax_class_id'] == $tax_group['val'] ? 'selected="selected"' : '' ?>><?php echo $tax_group['text'] ?></option>
															<?php } ?>
														</select>
													</td>
													
													<td style="width: 10%;">
														<a class="delete_zone"><?php echo lang('common_delete'); ?></a>
													</td>
													
													<td style="width: 10%;"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
											</tr>
									
											<?php } //end shipping zones ?>
											</tbody>
										</table>
								
										<a href="javascript:void(0);" class="add_shipping_zone"><?php echo lang('config_add_shipping_zone'); ?></a>
										</div>
									</div>
								</div>
					
				</div><!-- end panel-body -->
			</div><!-- end panel-->
		</div><!-- end col -->
		
		<!-- Sales -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_sales_info'), 'store-configuration-options', 'section-sales')  ?>
				</div>
				<div class="panel-body">
								
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_prefix').':', 'sale_prefix',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'sale_prefix',
							'id'=>'sale_prefix',
							'value'=>$this->config->item('sale_prefix')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_id_to_show_on_sale_interface').':', 'id_to_show_on_sale_interface',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('id_to_show_on_sale_interface', array(
							'number'  => lang('common_item_number_expanded'),
							'product_id'    => lang('common_product_id'),
							'id'   => lang('common_item_id')
							),
							$this->config->item('id_to_show_on_sale_interface'), 'class="form-control" id="id_to_show_on_sale_interface"')
							?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_auto_focus_on_item_after_sale_and_receiving').':', 'auto_focus_on_item_after_sale_and_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'auto_focus_on_item_after_sale_and_receiving',
							'id'=>'auto_focus_on_item_after_sale_and_receiving',
							'value'=>'auto_focus_on_item_after_sale_and_receiving',
							'checked'=>$this->config->item('auto_focus_on_item_after_sale_and_receiving')));?>
							<label for="auto_focus_on_item_after_sale_and_receiving"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_capture_sig_for_all_payments').':', 'capture_sig_for_all_payments',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'capture_sig_for_all_payments',
							'id'=>'capture_sig_for_all_payments',
							'value'=>'capture_sig_for_all_payments',
							'checked'=>$this->config->item('capture_sig_for_all_payments')));?>
							<label for="capture_sig_for_all_payments"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_number_of_recent_sales').':', 'number_of_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">

						<?php echo form_dropdown('number_of_recent_sales', 
						 array(
							'10'=>'10',
							'20'=>'20',
							'50'=>'50',
							'100'=>'100',
							'200'=>'200',
							'500'=>'500'
							), $this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : '10', 'class="form-control" id="number_of_recent_sales"');
							?>

						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_hide_customer_recent_sales').':', 'hide_customer_recent_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_customer_recent_sales',
							'id'=>'hide_customer_recent_sales',
							'value'=>'hide_customer_recent_sales',
							'checked'=>$this->config->item('hide_customer_recent_sales')));?>
							<label for="hide_customer_recent_sales"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('disable_confirmation_sale').':', 'disable_confirmation_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_confirmation_sale',
							'id'=>'disable_confirmation_sale',
							'value'=>'disable_confirmation_sale',
							'checked'=>$this->config->item('disable_confirmation_sale')));?>
							<label for="disable_confirmation_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('disable_quick_complete_sale').':', 'disable_quick_complete_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_quick_complete_sale',
							'id'=>'disable_quick_complete_sale',
							'value'=>'disable_quick_complete_sale',
							'checked'=>$this->config->item('disable_quick_complete_sale')));?>
							<label for="disable_quick_complete_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_automatically_calculate_average_cost_price_from_receivings').':', 'calculate_average_cost_price_from_receivings',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'calculate_average_cost_price_from_receivings',
							'id'=>'calculate_average_cost_price_from_receivings',
							'value'=>'1',
							'checked'=>$this->config->item('calculate_average_cost_price_from_receivings')));?>
							<label for="calculate_average_cost_price_from_receivings"><span></span></label>
						</div>
					</div>
					
					<div id="average_cost_price_from_receivings_methods">
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_averaging_method').':', 'averaging_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('averaging_method', array('moving_average' => lang('config_moving_average'), 'historical_average' => lang('config_historical_average'), 'dont_average' => lang('config_dont_average_use_current_recv_price')), $this->config->item('averaging_method'),'class="form-control" id="averaging_method"'); ?>
							</div>
						</div>
					</div>
									
					<?php if ($this->config->item('always_use_average_cost_method')) { ?>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_always_use_average_cost_method').':', 'always_use_average_cost_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'always_use_average_cost_method',
							'id'=>'always_use_average_cost_method',
							'value'=>'1',
							'checked'=>$this->config->item('always_use_average_cost_method')));?>
							<label for="always_use_average_cost_method"><span></span></label>
						</div>
					</div>
					<?php } ?>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_hide_suspended_recv_in_reports').':', 'hide_suspended_recv_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_suspended_recv_in_reports',
							'id'=>'hide_suspended_recv_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_suspended_recv_in_reports')));?>
							<label for="hide_suspended_recv_in_reports"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('common_track_cash').':', 'track_cash',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'track_cash',
							'id'=>'track_cash',
							'value'=>'1',
							'checked'=>$this->config->item('track_cash')));?>
							<label for="track_cash"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_disable_giftcard_detection').':', 'disable_giftcard_detection',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_giftcard_detection',
							'id'=>'disable_giftcard_detection',
							'value'=>'1',
							'checked'=>$this->config->item('disable_giftcard_detection')));?>
							<label for="disable_giftcard_detection"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_always_show_item_grid').':', 'always_show_item_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'always_show_item_grid',
							'id'=>'always_show_item_grid',
							'value'=>'1',
							'checked'=>$this->config->item('always_show_item_grid')));?>
							<label for="always_show_item_grid"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_hide_out_of_stock_grid').':', 'hide_out_of_stock_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_out_of_stock_grid',
							'id'=>'hide_out_of_stock_grid',
							'value'=>'1',
							'checked'=>$this->config->item('hide_out_of_stock_grid')));?>
							<label for="hide_out_of_stock_grid"><span></span></label>
						</div>
					</div>
										
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_default_type_for_grid').':', 'default_type_for_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">

							<?php echo form_dropdown('default_type_for_grid', array(
								'categories'  => lang('reports_categories'), 
								'tags'  => lang('common_tags'),
							),
							$this->config->item('default_type_for_grid'), 'class="form-control" id="default_type_for_grid"');
							?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_require_customer_for_sale').':', 'require_customer_for_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_customer_for_sale',
							'id'=>'require_customer_for_sale',
							'value'=>'1',
							'checked'=>$this->config->item('require_customer_for_sale')));?>
							<label for="require_customer_for_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_select_sales_person_during_sale').':', 'select_sales_person_during_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'select_sales_person_during_sale',
							'id'=>'select_sales_person_during_sale',
							'value'=>'1',
							'checked'=>$this->config->item('select_sales_person_during_sale')));?>
							<label for="select_sales_person_during_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_default_sales_person').':', 'default_sales_person',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10"> 
						<?php echo form_dropdown('default_sales_person', array('logged_in_employee' => lang('common_logged_in_employee'), 'not_set' => lang('common_not_set')), $this->config->item('default_sales_person'),'class="form-control" id="default_sales_person"'); ?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('common_commission_default_rate'). ':', 'commission_default_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<div class="input-group">
								<?php echo form_input(array(
								'name'=>'commission_default_rate',
								'id'=>'commission_default_rate',
								'class'=>'form-control',
								'value'=>$this->config->item('commission_default_rate')));?>
							  <span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$this->config->item('commission_percent_type'),
							array('id' => 'commission_percent_type', 'class' => 'form-control'))
							?>
						</div>
					</div>
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_disable_sale_notifications').':', 'disable_sale_notifications',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_sale_notifications',
							'id'=>'disable_sale_notifications',
							'value'=>'1',
							'checked'=>$this->config->item('disable_sale_notifications')));?>
							<label for="disable_sale_notifications"><span></span></label>
						</div>
					</div>

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_confirm_error_messages_modal').':', 'confirm_error_adding_item',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'confirm_error_adding_item',
							'id'=>'confirm_error_adding_item',
							'value'=>'1',
							'checked'=>$this->config->item('confirm_error_adding_item')));?>
							<label for="confirm_error_adding_item"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_change_sale_date_for_new_sale').':', 'change_sale_date_for_new_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_for_new_sale',
							'id'=>'change_sale_date_for_new_sale',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_for_new_sale')));?>
							<label for="change_sale_date_for_new_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_do_not_group_same_items').':', 'do_not_group_same_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_group_same_items',
							'id'=>'do_not_group_same_items',
							'value'=>'1',
							'checked'=>$this->config->item('do_not_group_same_items')));?>
							<label for="do_not_group_same_items"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
						<?php echo form_label(lang('config_do_not_allow_below_cost').':', 'do_not_allow_below_cost',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_allow_below_cost',
							'id'=>'do_not_allow_below_cost',
							'value'=>'1',
							'checked'=>$this->config->item('do_not_allow_below_cost')));?>
							<label for="do_not_allow_below_cost"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
					<?php echo form_label(lang('config_do_not_allow_out_of_stock_items_to_be_sold').':', 'do_not_allow_out_of_stock_items_to_be_sold',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'do_not_allow_out_of_stock_items_to_be_sold',
							'id'=>'do_not_allow_out_of_stock_items_to_be_sold',
							'value'=>'do_not_allow_out_of_stock_items_to_be_sold',
							'checked'=>$this->config->item('do_not_allow_out_of_stock_items_to_be_sold')));?>
							<label for="do_not_allow_out_of_stock_items_to_be_sold"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_sales')) ?>">	
					<?php echo form_label(lang('config_edit_item_price_if_zero_after_adding').':', 'edit_item_price_if_zero_after_adding',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'edit_item_price_if_zero_after_adding',
							'id'=>'edit_item_price_if_zero_after_adding',
							'value'=>'edit_item_price_if_zero_after_adding',
							'checked'=>$this->config->item('edit_item_price_if_zero_after_adding')));?>
							<label for="edit_item_price_if_zero_after_adding"><span></span></label>
						</div>
					</div>
					
				</div>
			</div>
		</div>
					
		<!-- Suspended Sales/Layaways -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_suspended_sales_layaways_info'), 'store-configuration-options', 'section-suspended-saleslayaways')  ?>
				</div>
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('config_require_customer_for_suspended_sale').':', 'require_customer_for_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_customer_for_suspended_sale',
							'id'=>'require_customer_for_suspended_sale',
							'value'=>'1',
							'checked'=>$this->config->item('require_customer_for_suspended_sale')));?>
							<label for="require_customer_for_suspended_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">
						<?php echo form_label(lang('config_user_configured_layaway_name').':', 'user_configured_layaway_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'user_configured_layaway_name',
							'id'=>'user_configured_layaway_name',
							'value'=>$this->config->item('user_configured_layaway_name')));?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('common_hide_layaways_sales_in_reports').':', 'hide_layaways_sales_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_layaways_sales_in_reports',
							'id'=>'hide_layaways_sales_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_layaways_sales_in_reports')));?>
							<label for="hide_layaways_sales_in_reports"><span></span></label>
						</div>
					</div>

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('config_lock_prices_suspended_sales').':', 'lock_prices_suspended_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'lock_prices_suspended_sales',
							'id'=>'lock_prices_suspended_sales',
							'value'=>'1',
							'checked'=>$this->config->item('lock_prices_suspended_sales')));?>
							<label for="lock_prices_suspended_sales"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('config_change_sale_date_when_suspending').':', 'change_sale_date_when_suspending',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_when_suspending',
							'id'=>'change_sale_date_when_suspending',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_when_suspending')));?>
							<label for="change_sale_date_when_suspending"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('config_change_sale_date_when_completing_suspended_sale').':', 'change_sale_date_when_completing_suspended_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_sale_date_when_completing_suspended_sale',
							'id'=>'change_sale_date_when_completing_suspended_sale',
							'value'=>'1',
							'checked'=>$this->config->item('change_sale_date_when_completing_suspended_sale')));?>
							<label for="change_sale_date_when_completing_suspended_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_suspended_layaways')) ?>">	
						<?php echo form_label(lang('config_show_receipt_after_suspending_sale').':', 'show_receipt_after_suspending_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'show_receipt_after_suspending_sale',
							'id'=>'show_receipt_after_suspending_sale',
							'value'=>'1',
							'checked'=>$this->config->item('show_receipt_after_suspending_sale')));?>
							<label for="show_receipt_after_suspending_sale"><span></span></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Receipt -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_receipt_info'), 'store-configuration-options', 'section-receipt')  ?>
				</div>
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_override_receipt_title').':', 'override_receipt_title',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps',
							'name'=>'override_receipt_title',
							'id'=>'override_receipt_title',
							'value'=>$this->config->item('override_receipt_title')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_show_item_id_on_receipt').':', 'show_item_id_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'show_item_id_on_receipt',
							'id'=>'show_item_id_on_receipt',
							'value'=>'show_item_id_on_receipt',
							'checked'=>$this->config->item('show_item_id_on_receipt')));?>
							<label for="show_item_id_on_receipt"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_number_of_decimals_for_quantity_on_receipt').':', 'number_of_decimals_for_quantity_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('number_of_decimals_for_quantity_on_receipt', array(
		 							''  => lang('config_let_system_decide'),
		 							'0'    => '0',
		 							'1'    => '1',
		 							'2'    => '2',
		 							'3'    => '3',
		 							'4'    => '4',
		 							'5'    => '5',
		 							'6'    => '6',
		 							'7'    => '7',
		 							'8'    => '8',
		 							'9'    => '9',
		 							'10'    => '10',
								),$this->config->item('number_of_decimals_for_quantity_on_receipt')
		 							 , 'class="form-control" id="number_of_decimals_for_quantity_on_receipt"');
								?>
						</div>
					</div>
					
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('common_indicate_taxable_on_receipt').':', 'indicate_taxable_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'indicate_taxable_on_receipt',
							'id'=>'indicate_taxable_on_receipt',
							'value'=>'indicate_taxable_on_receipt',
							'checked'=>$this->config->item('indicate_taxable_on_receipt')));?>
							<label for="indicate_taxable_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_hide_desc_on_receipt').':', 'hide_desc_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_desc_on_receipt',
							'id'=>'hide_desc_on_receipt',
							'value'=>'hide_desc_on_receipt',
							'checked'=>$this->config->item('hide_desc_on_receipt')));?>
							<label for="hide_desc_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_show_orig_price_if_marked_down_on_receipt').':', 'show_orig_price_if_marked_down_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'show_orig_price_if_marked_down_on_receipt',
							'id'=>'show_orig_price_if_marked_down_on_receipt',
							'value'=>'show_orig_price_if_marked_down_on_receipt',
							'checked'=>$this->config->item('show_orig_price_if_marked_down_on_receipt')));?>
							<label for="show_orig_price_if_marked_down_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_print_after_sale').':', 'print_after_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'print_after_sale',
							'id'=>'print_after_sale',
							'value'=>'print_after_sale',
							'checked'=>$this->config->item('print_after_sale')));?>
							<label for="print_after_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_wide_printer_receipt_format').':', 'wide_printer_receipt_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'wide_printer_receipt_format',
							'id'=>'wide_printer_receipt_format',
							'value'=>'wide_printer_receipt_format',
							'checked'=>$this->config->item('wide_printer_receipt_format')));?>
							<label for="wide_printer_receipt_format"><span></span></label>
						</div>
					</div>
					

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_print_after_receiving').':', 'print_after_receiving',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'print_after_receiving',
							'id'=>'print_after_receiving',
							'value'=>'print_after_receiving',
							'checked'=>$this->config->item('print_after_receiving')));?>
							<label for="print_after_receiving"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_hide_signature').':', 'hide_signature',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_signature',
							'id'=>'hide_signature',
							'value'=>'hide_signature',
							'checked'=>$this->config->item('hide_signature')));?>
							<label for="hide_signature"><span></span></label>
						</div>
					</div>
												
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_remove_customer_name_from_receipt').':', 'remove_customer_name_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_customer_name_from_receipt',
							'id'=>'remove_customer_name_from_receipt',
							'value'=>'remove_customer_name_from_receipt',
							'checked'=>$this->config->item('remove_customer_name_from_receipt')));?>
						<label for="remove_customer_name_from_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_remove_customer_company_from_receipt').':', 'remove_customer_company_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_customer_company_from_receipt',
							'id'=>'remove_customer_company_from_receipt',
							'value'=>'remove_customer_company_from_receipt',
							'checked'=>$this->config->item('remove_customer_company_from_receipt')));?>
						<label for="remove_customer_company_from_receipt"><span></span></label>
						</div>
					</div>
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_remove_customer_contact_info_from_receipt').':', 'remove_customer_contact_info_from_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_customer_contact_info_from_receipt',
							'id'=>'remove_customer_contact_info_from_receipt',
							'value'=>'remove_customer_contact_info_from_receipt',
							'checked'=>$this->config->item('remove_customer_contact_info_from_receipt')));?>
						<label for="remove_customer_contact_info_from_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_automatically_email_receipt').':', 'automatically_email_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_email_receipt',
							'id'=>'automatically_email_receipt',
							'value'=>'automatically_email_receipt',
							'checked'=>$this->config->item('automatically_email_receipt')));?>
							<label for="automatically_email_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_automatically_print_duplicate_receipt_for_cc_transactions').':', 'automatically_print_duplicate_receipt_for_cc_transactions',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'id'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'value'=>'automatically_print_duplicate_receipt_for_cc_transactions',
							'checked'=>$this->config->item('automatically_print_duplicate_receipt_for_cc_transactions')));?>
							<label for="automatically_print_duplicate_receipt_for_cc_transactions"><span></span></label>
						</div>
					</div>

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_always_print_duplicate_receipt_all').':', 'always_print_duplicate_receipt_all',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'always_print_duplicate_receipt_all',
							'id'=>'always_print_duplicate_receipt_all',
							'value'=>'always_print_duplicate_receipt_all',
							'checked'=>$this->config->item('always_print_duplicate_receipt_all')));?>
							<label for="always_print_duplicate_receipt_all"><span></span></label>
						</div>
					</div>
					
					
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_automatically_show_comments_on_receipt').':', 'automatically_show_comments_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'automatically_show_comments_on_receipt',
							'id'=>'automatically_show_comments_on_receipt',
							'value'=>'automatically_show_comments_on_receipt',
							'checked'=>$this->config->item('automatically_show_comments_on_receipt')));?>
							<label for="automatically_show_comments_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_hide_barcode_on_sales_and_recv_receipt').':', 'hide_barcode_on_sales_and_recv_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_barcode_on_sales_and_recv_receipt',
							'id'=>'hide_barcode_on_sales_and_recv_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('hide_barcode_on_sales_and_recv_receipt')));?>
							<label for="hide_barcode_on_sales_and_recv_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_group_all_taxes_on_receipt').':', 'group_all_taxes_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'group_all_taxes_on_receipt',
							'id'=>'group_all_taxes_on_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('group_all_taxes_on_receipt')));?>
							<label for="group_all_taxes_on_receipt"><span></span></label>
						</div>
					</div>						
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_redirect_to_sale_or_recv_screen_after_printing_receipt').':', 'redirect_to_sale_or_recv_screen_after_printing_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
							'id'=>'redirect_to_sale_or_recv_screen_after_printing_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt')));?>
							<label for="redirect_to_sale_or_recv_screen_after_printing_receipt"><span></span></label>
						</div>
					</div>
													
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_receipt_text_size').':', 'receipt_text_size',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('receipt_text_size', $receipt_text_size_options, $this->config->item('receipt_text_size'),'class="form-control" id="receipt_text_size"'); ?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_hide_store_account_balance_on_receipt').':', 'hide_store_account_balance_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_balance_on_receipt',
							'id'=>'hide_store_account_balance_on_receipt',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_balance_on_receipt')));?>
							<label for="hide_store_account_balance_on_receipt"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
						<?php echo form_label(lang('config_round_cash_on_sales').':', 'round_cash_on_sales',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'round_cash_on_sales',
							'id'=>'round_cash_on_sales',
							'value'=>'round_cash_on_sales',
							'checked'=>$this->config->item('round_cash_on_sales')));?>
							<label for="round_cash_on_sales"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
					<?php echo form_label(lang('common_return_policy').':', 'return_policy',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'return_policy',
							'id'=>'return_policy',
							'class'=>'form-control text-area',
							'rows'=>'4',
							'cols'=>'30',
							'value'=>$this->config->item('return_policy')));?>
						</div>
					</div>

					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_receipt')) ?>">	
					<?php echo form_label(lang('common_announcement_special').':', 'announcement_special',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'announcement_special',
							'id'=>'announcement_special',
							'class'=>'form-control text-area',
							'rows'=>'4',
							'cols'=>'30',
							'value'=>$this->config->item('announcement_special')));?>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		
		
		<!-- Profit -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_profit_info'), 'store-configuration-options', 'section-profit')  ?>
				</div>
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">
						<?php echo form_label(lang('config_calculate_profit_for_giftcard_when').':', 'calculate_profit_for_giftcard_when',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('calculate_profit_for_giftcard_when', array(
								''  => lang('common_do_nothing'),
								'redeeming_giftcard'   => lang('config_redeeming_giftcard'), 
								'selling_giftcard'  => lang('config_selling_giftcard'),
							),
							$this->config->item('calculate_profit_for_giftcard_when'), 'class="form-control" id="calculate_profit_for_giftcard_when"');
							?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">	
					<?php echo form_label(lang('config_remove_commission_from_profit_in_reports').':', 'remove_commission_from_profit_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_commission_from_profit_in_reports',
							'id'=>'remove_commission_from_profit_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('remove_commission_from_profit_in_reports')));?>
							<label for="remove_commission_from_profit_in_reports"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_profit')) ?>">	
					<?php echo form_label(lang('config_remove_points_from_profit').':', 'remove_points_from_profit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'remove_points_from_profit',
							'id'=>'remove_points_from_profit',
							'value'=>'1',
							'checked'=>$this->config->item('remove_points_from_profit')));?>
							<label for="remove_points_from_profit"><span></span></label>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<!-- Barcodes -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_barcodes_info'), 'store-configuration-options', 'section-barcodes')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_id_to_show_on_barcode').':', 'id_to_show_on_barcode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('id_to_show_on_barcode', array(
							'id'   => lang('common_item_id'),
							'number'  => lang('common_item_number_expanded'),
							'product_id'    => lang('common_product_id'),
							),
							$this->config->item('id_to_show_on_barcode'), 'class="form-control" id="id_to_show_on_barcode"')
							?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_barcode_price_include_tax').':', 'barcode_price_include_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'barcode_price_include_tax',
							'id'=>'barcode_price_include_tax',
							'value'=>'barcode_price_include_tax',
							'checked'=>$this->config->item('barcode_price_include_tax')));?>
							<label for="barcode_price_include_tax"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_hide_price_on_barcodes').':', 'hide_price_on_barcodes',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_price_on_barcodes',
							'id'=>'hide_price_on_barcodes',
							'value'=>'hide_price_on_barcodes',
							'checked'=>$this->config->item('hide_price_on_barcodes')));?>
							<label for="hide_price_on_barcodes"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_enable_scale').':', 'enable_scale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'enable_scale',
							'id'=>'enable_scale',
							'value'=>'enable_scale',
							'checked'=>$this->config->item('enable_scale')));?>
							<label for="enable_scale"><span></span></label>
						</div>
					</div>
								
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_scale_format').':', 'scale_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('scale_format', array(
							'scale_1'   => lang('config_scale_1'),
							'scale_2'  => lang('config_scale_2'),
							'scale_3'    => lang('config_scale_3'),
							'scale_4'    => lang('config_scale_4'),
							),
							$this->config->item('scale_format'), 'class="form-control" id="scale_format"')
							?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_barcodes')) ?>">	
						<?php echo form_label(lang('config_scale_divide_by').':', 'scale_divide_by',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('scale_divide_by', array(
							'100'   => '100',
							'10'  => '10',
							'1'    => '1',
							),
							$this->config->item('scale_divide_by'), 'class="form-control" id="scale_divide_by"')
							?>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<!-- Customer Loyalty -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_customer_loyalty_info'), 'store-configuration-options', 'section-customer-loyalty')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
					<?php echo form_label(lang('config_enable_customer_loyalty_system').':', 'enable_customer_loyalty_system',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'enable_customer_loyalty_system',
							'id'=>'enable_customer_loyalty_system',
							'value'=>'enable_customer_loyalty_system',
							'checked'=>$this->config->item('enable_customer_loyalty_system')));?>
							<label for="enable_customer_loyalty_system"><span></span></label>
						</div>
					</div>
					
					<div id="loyalty_setup">
						
						<div class="form-group" id="loyalty_option_wrapper" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
							<?php echo form_label(lang('config_loyalty_option').':', 'loyalty_option',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('loyalty_option', 
							 array(
								'simple'=> lang('config_simple'),
								'advanced'=>lang('config_advanced'),
							), $this->config->item('loyalty_option') ? $this->config->item('loyalty_option') : '20', 'class="form-control" id="loyalty_option"');
								?>
							</div>
						</div>		
						
						
						<div id="loyalty_setup_simple" style="display: none;">
								<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
								<?php echo form_label(lang('config_number_of_sales_for_discount').':', 'number_of_sales_for_discount',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'validate form-control form-inps',
									'name'=>'number_of_sales_for_discount',
									'id'=>'number_of_sales_for_discount',
									'value'=>$this->config->item('number_of_sales_for_discount')));?>
									</div>
								</div>
								
								<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
								<?php echo form_label(lang('config_discount_percent_earned').':', 'discount_percent_earned',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'validate form-control form-inps',
									'name'=>'discount_percent_earned',
									'id'=>'discount_percent_earned',
									'value'=>$this->config->item('discount_percent_earned')));?>
									</div>
								</div>
								
								<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
								<?php echo form_label(lang('hide_sales_to_discount_on_receipt').':', 'hide_sales_to_discount_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_checkbox(array(
										'name'=>'hide_sales_to_discount_on_receipt',
										'id'=>'hide_sales_to_discount_on_receipt',
										'value'=>'hide_sales_to_discount_on_receipt',
										'checked'=>$this->config->item('hide_sales_to_discount_on_receipt')));?>
										<label for="hide_sales_to_discount_on_receipt"><span></span></label>
									</div>
								</div>
								
								
						</div>
				
						
						<div id="loyalty_setup_advanced" style="display: none;">					
							<?php
							$spend_amount_for_points = '';
							$points_to_earn= '';
							if (strpos($this->config->item('spend_to_point_ratio'),':') !== FALSE)
							{
					      	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
							}
							?>
							<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
							<?php echo form_label(lang('config_spend_to_point_ratio').':', 'spend_amount_for_points',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
						
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
								'name'=>'spend_amount_for_points',
								'id'=>'spend_amount_for_points',
								'placeholder' => lang('config_loyalty_explained_spend_amount'),
								'value'=>$spend_amount_for_points));?>
								<?php echo form_input(array(
									'class'=>'validate form-control form-inps',
									'name'=>'points_to_earn',
									'id'=>'points_to_earn',
									'placeholder' => lang('config_loyalty_explained_points_to_earn'),
									'value'=>$points_to_earn));?>
								</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
						<?php echo form_label(lang('config_point_value').':', 'point_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'validate form-control form-inps',
							'name'=>'point_value',
							'id'=>'point_value',
							'value'=>$this->config->item('point_value') ? $this->config->item('point_value') : ''));?>

							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
						<?php echo form_label(lang('config_loyalty_points_without_tax').':', 'loyalty_points_without_tax',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'loyalty_points_without_tax',
								'id'=>'loyalty_points_without_tax',
								'value'=>'loyalty_points_without_tax',
								'checked'=>$this->config->item('loyalty_points_without_tax')));?>
								<label for="loyalty_points_without_tax"><span></span></label>
							</div>
						</div>

						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
						<?php echo form_label(lang('config_prompt_to_use_points').':', 'prompt_to_use_points',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'prompt_to_use_points',
								'id'=>'prompt_to_use_points',
								'value'=>'prompt_to_use_points',
								'checked'=>$this->config->item('prompt_to_use_points')));?>
								<label for="prompt_to_use_points"><span></span></label>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_customer_loyalty')) ?>">	
						<?php echo form_label(lang('config_hide_points_on_receipt').':', 'hide_points_on_receipt',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'hide_points_on_receipt',
								'id'=>'hide_points_on_receipt',
								'value'=>'hide_points_on_receipt',
								'checked'=>$this->config->item('hide_points_on_receipt')));?>
								<label for="hide_points_on_receipt"><span></span></label>
							</div>
						</div>
						
					</div>
																				
					</div>
				</div>
			</div>
		</div>
		
		<!-- Price Tiers -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_price_tiers_info'), 'store-configuration-options', 'section-price-tiers')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group no-padding-right" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
					<?php echo form_label(lang('config_price_tiers').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-md-9 col-sm-9 col-lg-10">
							<div class="table-responsive">
								<table id="price_tiers" class="table">
									<thead>
										<tr>
										<th><?php echo lang('config_sort'); ?></th>
										<th><?php echo lang('common_tier_name'); ?></th>
										<th><?php echo lang('config_default_percent_off'); ?></th>
										<th><?php echo lang('config_default_cost_plus_percent'); ?></th>
										<th><?php echo lang('config_default_cost_plus_fixed_amount'); ?></th>
										<th><?php echo lang('common_delete'); ?></th>
										</tr>
									</thead>
									
									<tbody>
									<?php foreach($tiers->result() as $tier) { ?>
										<tr>
											<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
											<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control" name="tiers_to_edit[<?php echo $tier->id; ?>][name]" value="<?php echo H($tier->name); ?>" /></td>
											<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_percent_off" name="tiers_to_edit[<?php echo $tier->id; ?>][default_percent_off]" value="<?php echo $tier->default_percent_off ? to_quantity($tier->default_percent_off) : ''; ?>" /></td>
											<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_cost_plus_percent" name="tiers_to_edit[<?php echo $tier->id; ?>][default_cost_plus_percent]" value="<?php echo $tier->default_cost_plus_percent ? to_quantity($tier->default_cost_plus_percent) : ''; ?>" /></td>
											<td><input type="text" data-index="<?php echo $tier->id; ?>" class="tiers_to_edit form-control default_cost_plus_fixed_amount" name="tiers_to_edit[<?php echo $tier->id; ?>][default_cost_plus_fixed_amount]" value="<?php echo $tier->default_cost_plus_fixed_amount ? to_currency_no_money($tier->default_cost_plus_fixed_amount) : ''; ?>" /></td>
										<td>
											<?php if ($this->Employee->has_module_action_permission('items', 'delete', $this->Employee->get_logged_in_employee_info()->person_id) || $this->Employee->has_module_action_permission('item_kits', 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
											<a class="delete_tier" href="javascript:void(0);" data-tier-id='<?php echo $tier->id; ?>'><?php echo lang('common_delete'); ?></a>
											<?php }else { ?>
												&nbsp;
											<?php } ?>
											</td>
									</tr>
									<?php } ?>
									</tbody>
								</table>
								
								<a href="javascript:void(0);" id="add_tier"><?php echo lang('config_add_tier'); ?></a>
								</div>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
							<?php echo form_label(lang('config_override_tier_name').':', 'override_tier_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps',
								'name'=>'override_tier_name',
								'id'=>'override_tier_name',
								'value'=>$this->config->item('override_tier_name')));?>
							</div>
						</div>
						
	 					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
	 					<?php echo form_label(lang('config_default_tier_percent_type_for_excel_import').':', 'default_tier_percent_type_for_excel_import',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
	 						<div class="col-sm-9 col-md-9 col-lg-10">
	 						<?php echo form_dropdown('default_tier_percent_type_for_excel_import', array(
	 							'percent_off'    => lang('common_percent_off'),
	 							'cost_plus_percent'    => lang('common_cost_plus_percent'),
							),
	 							$this->config->item('default_tier_percent_type_for_excel_import')===NULL ? 'before' : $this->config->item('default_tier_percent_type_for_excel_import') , 'class="form-control" id="default_tier_percent_type_for_excel_import"');
	 							?>
	 						</div>						
	 					</div>
						
	 					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
	 					<?php echo form_label(lang('config_default_tier_fixed_type_for_excel_import').':', 'default_tier_fixed_type_for_excel_import',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
	 						<div class="col-sm-9 col-md-9 col-lg-10">
	 						<?php echo form_dropdown('default_tier_fixed_type_for_excel_import', array(
	 							'fixed_amount'    => lang('common_fixed_amount'),
	 							'cost_plus_fixed_amount'    => lang('common_cost_plus_fixed_amount'),
							),
	 							$this->config->item('default_tier_fixed_type_for_excel_import')===NULL ? 'before' : $this->config->item('default_tier_fixed_type_for_excel_import') , 'class="form-control" id="default_tier_fixed_type_for_excel_import"');
	 							?>
	 						</div>						
	 					</div>
						
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_price_tiers')) ?>">	
							<?php echo form_label(lang('config_round_tier_prices_to_2_decimals').':', 'round_tier_prices_to_2_decimals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'round_tier_prices_to_2_decimals',
								'id'=>'round_tier_prices_to_2_decimals',
								'value'=>'1',
								'checked'=>$this->config->item('round_tier_prices_to_2_decimals')));?>
								<label for="round_tier_prices_to_2_decimals"><span></span></label>
							</div>
						</div>
						
				</div>
			</div>
		</div>
		
		<!-- Auto Increment IDs Settings -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_auto_increment_ids_info'), 'store-configuration-options', 'section-id-numbers')  ?>
				</div>	
				<div class="panel-body">
					<div class="">
						<div class="col-sm-offset-3 col-md-offset-3 col-lg-offset-2 col-sm-9 col-md-9 col-lg-10">
							<div class="alert alert-info" role="alert"><strong><?php echo lang('common_note') ?>:</strong> <?php echo lang('config_auto_increment_note') ?></div>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">
						
						<?php echo form_label(lang('config_item_id_auto_increment').':', 'item_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'item_id_auto_increment',
							'id'=>'item_id_auto_increment',
							'value'=>''));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
						<?php echo form_label(lang('config_item_kit_id_auto_increment').':', 'item_kit_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'item_kit_id_auto_increment',
							'id'=>'item_kit_id_auto_increment',
							'value'=>''));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
						<?php echo form_label(lang('config_sale_id_auto_increment').':', 'sale_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'sale_id_auto_increment',
							'id'=>'sale_id_auto_increment',
							'value'=>''));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_auto_increment')) ?>">	
						<?php echo form_label(lang('config_receiving_id_auto_increment').':', 'receiving_id_auto_increment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'validate form-control form-inps',
							'name'=>'receiving_id_auto_increment',
							'id'=>'receiving_id_auto_increment',
							'value'=>''));?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Items Settings -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_items_info'), 'store-configuration-options', 'section-items')  ?>
				</div>	
				<div class="panel-body">
					<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo anchor("items/categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
							</div>
						</div>
					<?php } ?>		
				
					<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo anchor("items/manage_tags",lang('items_manage_tags'),array('target' => '_blank', 'title'=>lang('items_manage_tags')));?>
							</div>
						</div>
					<?php } ?>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
						<?php echo form_label(lang('config_number_of_items_per_page').':', 'number_of_items_per_page',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('number_of_items_per_page', 
						 array(
							'20'=>'20',
							'50'=>'50',
							'100'=>'100',
							'200'=>'200',
							'500'=>'500'
							), $this->config->item('number_of_items_per_page') ? $this->config->item('number_of_items_per_page') : '20', 'class="form-control" id="number_of_items_per_page"');
							?>
						</div>
					</div>		
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
						<?php echo form_label(lang('config_number_of_items_in_grid').':', 'number_of_items_in_grid',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php 
							$numbers = array();
							foreach(range(1, 50) as $number) 
							{ 
								$numbers[$number] = $number;
								
							}
							?> 
						<?php echo form_dropdown('number_of_items_in_grid', 
							 $numbers, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : '14', 'class="form-control" id="number_of_items_in_grid"');
							?>
						</div>
					</div>				
					
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
						<?php echo form_label(lang('config_default_reorder_level_when_creating_items').':', 'default_reorder_level_when_creating_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'default_reorder_level_when_creating_items',
							'id'=>'default_reorder_level_when_creating_items',
							'value'=>$this->config->item('default_reorder_level_when_creating_items')));?>
						</div>
					</div>
					
					
					
								
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
					<?php echo form_label(lang('config_default_new_items_to_service').':', 'default_new_items_to_service',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'default_new_items_to_service',
							'id'=>'default_new_items_to_service',
							'value'=>'default_new_items_to_service',
							'checked'=>$this->config->item('default_new_items_to_service')));?>
							<label for="default_new_items_to_service"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
					<?php echo form_label(lang('config_highlight_low_inventory_items_in_items_module').':', 'highlight_low_inventory_items_in_items_module',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'highlight_low_inventory_items_in_items_module',
							'id'=>'highlight_low_inventory_items_in_items_module',
							'value'=>'highlight_low_inventory_items_in_items_module',
							'checked'=>$this->config->item('highlight_low_inventory_items_in_items_module')));?>
							<label for="highlight_low_inventory_items_in_items_module"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
					<?php echo form_label(lang('config_limit_manual_price_adj').':', 'limit_manual_price_adj',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'limit_manual_price_adj',
							'id'=>'limit_manual_price_adj',
							'value'=>'1',
							'checked'=>$this->config->item('limit_manual_price_adj')));?>
							<label for="limit_manual_price_adj"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_items')) ?>">	
					<?php echo form_label(lang('config_enable_margin_calculator').':', 'enable_margin_calculator',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'enable_margin_calculator',
							'id'=>'enable_margin_calculator',
							'value'=>'1',
							'checked'=>$this->config->item('enable_margin_calculator')));?>
							<label for="enable_margin_calculator"><span></span></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Employee Settings -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_employee_info'), 'store-configuration-options', 'section-employee')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
					<?php echo form_label(lang('config_enable_timeclock').':', 'timeclock',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'timeclock',
							'id'=>'timeclock',
							'value'=>'timeclock',
							'checked'=>$this->config->item('timeclock')));?>
							<label for="timeclock"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
					<?php echo form_label(lang('config_logout_on_clock_out').':', 'logout_on_clock_out',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'logout_on_clock_out',
							'id'=>'logout_on_clock_out',
							'value'=>'logout_on_clock_out',
							'checked'=>$this->config->item('logout_on_clock_out')));?>
							<label for="logout_on_clock_out"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
					<?php echo form_label(lang('config_fast_user_switching').':', 'fast_user_switching',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'fast_user_switching',
							'id'=>'fast_user_switching',
							'value'=>'fast_user_switching',
							'checked'=>$this->config->item('fast_user_switching')));?>
							<label for="fast_user_switching"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
					<?php echo form_label(lang('config_require_employee_login_before_each_sale').':', 'require_employee_login_before_each_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'require_employee_login_before_each_sale',
							'id'=>'require_employee_login_before_each_sale',
							'value'=>'require_employee_login_before_each_sale',
							'checked'=>$this->config->item('require_employee_login_before_each_sale')));?>
							<label for="require_employee_login_before_each_sale"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_employees')) ?>">	
					<?php echo form_label(lang('config_reset_location_when_switching_employee').':', 'reset_location_when_switching_employee',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'reset_location_when_switching_employee',
							'id'=>'reset_location_when_switching_employee',
							'value'=>'reset_location_when_switching_employee',
							'checked'=>$this->config->item('reset_location_when_switching_employee')));?>
							<label for="reset_location_when_switching_employee"><span></span></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Store Accounts -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_store_accounts_info'), 'store-configuration-options', 'section-store-accounts')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
					<?php echo form_label(lang('config_customers_store_accounts').':', 'customers_store_accounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'customers_store_accounts',
							'id'=>'customers_store_accounts',
							'value'=>'customers_store_accounts',
							'checked'=>$this->config->item('customers_store_accounts')));?>
							<label for="customers_store_accounts"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
					<?php echo form_label(lang('config_suppliers_store_accounts').':', 'suppliers_store_accounts',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'suppliers_store_accounts',
							'id'=>'suppliers_store_accounts',
							'value'=>'suppliers_store_accounts',
							'checked'=>$this->config->item('suppliers_store_accounts')));?>
							<label for="suppliers_store_accounts"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
						<?php echo form_label(lang('config_disable_store_account_when_over_credit_limit').':', 'disable_store_account_when_over_credit_limit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_store_account_when_over_credit_limit',
							'id'=>'disable_store_account_when_over_credit_limit',
							'value'=>'1',
							'checked'=>$this->config->item('disable_store_account_when_over_credit_limit')));?>
							<label for="disable_store_account_when_over_credit_limit"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
					<?php echo form_label(lang('config_store_account_statement_message').':', 'store_account_statement_message',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_textarea(array(
							'name'=>'store_account_statement_message',
							'id'=>'store_account_statement_message',
							'class'=>'form-control text-area',
							'rows'=>'4',
							'cols'=>'30',
							'value'=>$this->config->item('store_account_statement_message')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
						<?php echo form_label(lang('config_hide_store_account_payments_in_reports').':', 'hide_store_account_payments_in_reports',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_payments_in_reports',
							'id'=>'hide_store_account_payments_in_reports',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_payments_in_reports')));?>
							<label for="hide_store_account_payments_in_reports"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
						<?php echo form_label(lang('config_hide_store_account_payments_from_report_totals').':', 'hide_store_account_payments_from_report_totals',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'hide_store_account_payments_from_report_totals',
							'id'=>'hide_store_account_payments_from_report_totals',
							'value'=>'1',
							'checked'=>$this->config->item('hide_store_account_payments_from_report_totals')));?>
							<label for="hide_store_account_payments_from_report_totals"><span></span></label>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_store_accounts')) ?>">	
						<?php echo form_label(lang('config_paypal_me').':', 'paypal_me',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'paypal_me',
							'id'=>'paypal_me',
							'placeholder' => 'paypal.me '.lang('common_username'),
							'value'=>$this->config->item('paypal_me')));?>
						</div>
					</div>
									
				</div>
			</div>
		</div>
		
		<!-- Application Settings -->
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_application_settings_info'), 'store-configuration-options', 'section-application-settings')  ?>
				</div>	
				<div class="panel-body">
					<?php if(is_on_demo_host()) { ?>
						<div class="form-group">	
							<div class="col-sm-9 col-md-9 col-lg-10">
							<span class="text-danger"><?php echo lang('config_cannot_change_language'); ?></span>
							</div>
						</div>
					<?php } ?>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
					<?php echo form_label(lang('common_language').':', 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('language', array(
							'english'  => 'English',
							'indonesia'    => 'Indonesia',
							'spanish'   => 'Español', 
							'french'    => 'Fançais',
							'italian'    => 'Italiano',
							'german'    => 'Deutsch',
							'dutch'    => 'Nederlands',
							'portugues'    => 'Portugues',
              'arabic' => 'العَرَبِيةُ‎‎',
							'khmer' => 'Khmer',
							'chinese' => '中文',
							'chinese_traditional' => '繁體中文'
							),
							$this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
							?>
						</div>						
					</div>
					
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_date_format').':', 'date_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('date_format', array(
								'middle_endian'    => '12/30/2000',
								'little_endian'  => '30-12-2000',
								'big_endian'   => '2000-12-30'), $this->config->item('date_format'), 'class="form-control" id="date_format"');
								?>
							</div>
						</div>
						
						

						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_time_format').':', 'time_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('time_format', array(
								'12_hour'    => '1:00 PM',
								'24_hour'  => '13:00'
								), $this->config->item('time_format'), 'class="form-control" id="time_format"');
								?>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
								<?php echo form_label(lang('config_store_opening_time').' :', 'store_opening_time', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
			 					<div class="col-sm-9 col-md-9 col-lg-10">
			 					    <div class="input-group">
			 							<span class="input-group-addon bg"><i class="glyphicon glyphicon-time"></i></span>
			 							<?php echo form_input(array(
		 						        'name'=>'store_opening_time',
		 						        'id'=>'store_opening_time',
		 										'class'=>'form-control timepicker',
		 						        'value'=> $this->config->item('store_opening_time') ? $this->config->item('store_opening_time') : ''
										));?>
			 					    </div>
			 					</div>
			 			</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
								<?php echo form_label(lang('config_store_closing_time').' :', 'store_closing_time', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
			 					<div class="col-sm-9 col-md-9 col-lg-10">
			 					    <div class="input-group">
			 							<span class="input-group-addon bg"><i class="glyphicon glyphicon-time"></i></span>
			 							<?php echo form_input(array(
		 						        'name'=>'store_closing_time',
		 						        'id'=>'store_closing_time',
		 										'class'=>'form-control timepicker',
		 						        'value'=> $this->config->item('store_closing_time') ? $this->config->item('store_closing_time') : ''
										));?>
			 					    </div>
			 					</div>
			 			</div>
						
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
					<?php echo form_label(lang('config_force_https').':', 'force_https',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'force_https',
							'id'=>'force_https',
							'value'=>'force_https',
							'checked'=>$this->config->item('force_https')));?>
							<label for="force_https"><span></span></label>
						</div>
					</div>
					
					
					<?php if (!is_on_demo_host()) { ?>
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('common_test_mode').' ('.lang('config_test_mode_help').'):', 'test_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'test_mode',
								'id'=>'test_mode',
								'value'=>'test_mode',
								'checked'=>$this->config->item('test_mode')));?>
								<label for="test_mode"><span></span></label>
							</div>
						</div>
					<?php } ?>
					
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
					<?php echo form_label(lang('common_disable_test_mode').':', 'disable_test_mode',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_test_mode',
							'id'=>'disable_test_mode',
							'value'=>'disable_test_mode',
							'checked'=>$this->config->item('disable_test_mode')));?>
							<label for="disable_test_mode"><span></span></label>
						</div>
					</div>
					
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_enable_sounds').':', 'enable_sounds',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'enable_sounds',
								'id'=>'enable_sounds',
								'value'=>'enable_sounds',
								'checked'=>$this->config->item('enable_sounds')));?>
								<label for="enable_sounds"><span></span></label>
							</div>
						</div>
					
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">
							<?php echo form_label(lang('config_virtual_keyboard').':', 'virtual_keyboard',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_dropdown('virtual_keyboard', 
								 array(
									''=>'Never',
									'all' => lang('common_all'),
									'desktop' =>lang('common_desktop'),
									'mobile' => lang('common_mobile'),
									), $this->config->item('virtual_keyboard') ? $this->config->item('virtual_keyboard') : '', 'class="form-control" id="virtual_keyboard"');
									?>
							</div>
						</div>
										
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_show_language_switcher').':', 'show_language_switcher',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'show_language_switcher',
								'id'=>'show_language_switcher',
								'value'=>'1',
								'checked'=>$this->config->item('show_language_switcher')));?>
								<label for="show_language_switcher"><span></span></label>
							</div>
						</div>

						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_show_clock_on_header').':', 'show_clock_on_header',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'show_clock_on_header',
								'id'=>'show_clock_on_header',
								'value'=>'1',
								'checked'=>$this->config->item('show_clock_on_header')));?>
								<label for="show_clock_on_header"><span></span></label>
								    <p class="help-block"><?php echo lang('config_show_clock_on_header_help_text'); ?></p>
							</div>
						</div>

						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_legacy_detailed_report_export').':', 'legacy_detailed_report_export',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'legacy_detailed_report_export',
								'id'=>'legacy_detailed_report_export',
								'value'=>'1',
								'checked'=>$this->config->item('legacy_detailed_report_export')));?>
								<label for="legacy_detailed_report_export"><span></span></label>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_report_sort_order').':', 'report_sort_order',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('report_sort_order', array('asc' => lang('config_asc'), 'desc' => lang('config_desc')), $this->config->item('report_sort_order'),'class="form-control" id="report_sort_order"'); ?>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_speed_up_search_queries').' ('.lang('config_speed_up_note').')'.':', 'speed_up_search_queries',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'speed_up_search_queries',
								'id'=>'speed_up_search_queries',
								'value'=>'1',
								'checked'=>$this->config->item('speed_up_search_queries')));?>
								<label for="speed_up_search_queries"><span></span></label>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_enable_quick_edit').':', 'enable_quick_edit',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'enable_quick_edit',
								'id'=>'enable_quick_edit',
								'value'=>'1',
								'checked'=>$this->config->item('enable_quick_edit')));?>
								<label for="enable_quick_edit"><span></span></label>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_enhanced_search_method').':', 'enhanced_search_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php 
								$enhanced_search_options = array(
									'name'=>'enhanced_search_method',
									'id'=>'enhanced_search_method',
									'value'=>'1',
									'checked'=>$this->config->item('enhanced_search_method') && $this->config->item('supports_full_text'));
								
									if (!$this->config->item('supports_full_text'))
									{
										$enhanced_search_options['disabled'] = 'disabled';
									}
								
									echo form_checkbox($enhanced_search_options);
								
									?>
									<label for="enhanced_search_method"><span></span></label>
								</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_include_child_categories_when_searching_or_reporting').':', 'include_child_categories_when_searching_or_reporting',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'include_child_categories_when_searching_or_reporting',
								'id'=>'include_child_categories_when_searching_or_reporting',
								'value'=>'1',
								'checked'=>$this->config->item('include_child_categories_when_searching_or_reporting')));?>
								<label for="include_child_categories_when_searching_or_reporting"><span></span></label>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_spreadsheet_format').':', 'spreadsheet_format',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('spreadsheet_format', array('CSV' => lang('config_csv'), 'XLSX' => lang('config_xlsx')), $this->config->item('spreadsheet_format'),'class="form-control" id="spreadsheet_format"'); ?>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_mailing_labels_type').':', 'mailing_labels_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('mailing_labels_type', array('pdf' => 'PDF', 'excel' => 'Excel'), $this->config->item('mailing_labels_type'),'class="form-control" id="mailing_labels_type"'); ?>
							</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_phppos_session_expiration').':', 'phppos_session_expiration',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_dropdown('phppos_session_expiration',$phppos_session_expirations, $this->config->item('phppos_session_expiration')!==NULL ? $this->config->item('phppos_session_expiration') : 0,'class="form-control" id="phppos_session_expiration"'); ?>
							</div>
						</div>
						
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
						<?php echo form_label(lang('config_always_minimize_menu').':', 'always_minimize_menu',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_checkbox(array(
								'name'=>'always_minimize_menu',
								'id'=>'always_minimize_menu',
								'value'=>'1',
								'checked'=>$this->config->item('always_minimize_menu')));?>
								<label for="always_minimize_menu"><span></span></label>
							</div>
						</div>
						
						<!--here -->
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_application_settings')) ?>">	
							<?php echo form_label(lang('config_item_lookup_order').':', 'item_lookup_order',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								  	<ul id="item_lookup_order_list" class="list-group">
											<?php 
											foreach($item_lookup_order as $item_lookup_number)
											{
											?>
 		                   <li class="list-group-item"><input name="item_lookup_order[]" type="hidden" value="<?php echo $item_lookup_number; ?>"><?php echo lang('common_'.$item_lookup_number); ?><span class="ui-icon ui-icon-arrowthick-2-n-s pull-right"></span></li>
											<?php 
											}
											?>
								    </ul>
								</div>
						</div>
												
				</div>
			</div>
		</div>
		<?php if(!is_on_phppos_host()) { ?>
		
     <!-- Email Settings -->
     <div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_email_settings_info'), 'store-configuration-options', 'section-email-settings')  ?>
				</div>	
				<div class="panel-body">
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
						<?php echo form_label('Select A Provider'.':', 'email_provider',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								$provider_options = array('Use System Default'=>'Use System Default', 'Gmail'=>'Gmail', 'Office 365'=>'Office 365', 'Windows Live Hotmail'=>'Windows Live Hotmail', 'Other'=>'Other');
								echo form_dropdown('email_provider', $provider_options, $this->config->item('email_provider'), 'id="email_provider" class="form-control"');
							?>
						</div>
					</div>
					
				<div class="email_basic">
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_smtp_user').':', 'smtp_user',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'smtp_user',
							'id'=>'smtp_user',
							'placeholder' => 'username@domain.com',
							'value'=>$this->config->item('smtp_user')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_smtp_pass').':', 'smtp_pass',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_password(array(
							'class'=>'form-control form-inps',
							'name'=>'smtp_pass',
							'id'=>'smtp_pass',
							'placeholder'=> 'password',
							'value'=>$this->config->item('smtp_pass')));?>
						</div>
					</div>
				</div>
				<div class="email_advanced">	
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_smtp_crypto').':', 'smtp_crypto',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								$smtp_crypto_options = array(''=>'','ssl'=>'ssl','tls'=>'tls');
								echo form_dropdown('smtp_crypto', $smtp_crypto_options, $this->config->item('smtp_crypto'), 'id="smtp_crypto" class="form-control"');
							?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
						<?php echo form_label(lang('config_email_protocol').':', 'protocol',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								$protocol_options = array(''=>'','smtp'=>'smtp','mail'=>'mail','sendmail'=>'sendmail');
								echo form_dropdown('protocol', $protocol_options, $this->config->item('protocol'), 'id="protocol" class="form-control"');
							?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_smtp_host').':', 'smtp_host',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'smtp_host',
							'id'=>'smtp_host',
							'placeholder' => 'smtp.domain.com',
							'value'=>$this->config->item('smtp_host')));?>
						</div>
					</div>
					
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_smtp_port').':', 'smtp_port',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'smtp_port',
							'id'=>'smtp_port',
							'placeholder'=>'25',
							'value'=>$this->config->item('smtp_port')));?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_email_charset').':', 'email_charset',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'email_charset',
							'id'=>'email_charset',
							'placeholder'=>'utf-8',
							'value'=>$this->config->item('email_charset')));?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_email_newline').':', 'newline',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								$newline_options = array('rn'=>'\r\n','n'=>'\n','r'=>'\r');
								$selected_option = 'rn';
								
								if ($option = $this->config->item('newline'))
								{
									if ($option == "\r\n")
									{
										$selected_option = 'rn';
									}
									elseif($option == "\n")
									{
										$selected_option = 'n';										
									}
									elseif($option == "\r")
									{
										$selected_option='r';
									}
								}
								
								echo form_dropdown('newline', $newline_options,$selected_option, 'id="newline" class="form-control"');
							?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">	
						<?php echo form_label(lang('config_email_crlf').':', 'crlf',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								$crlf_options = array('rn'=>'\r\n','n'=>'\n','r'=>'\r');
								$selected_option = 'rn';
								
								if ($option = $this->config->item('crlf'))
								{
									if ($option == "\r\n")
									{
										$selected_option = 'rn';
									}
									elseif($option == "\n")
									{
										$selected_option = 'n';										
									}
									elseif($option == "\r")
									{
										$selected_option='r';
									}
								}
								
								echo form_dropdown('crlf', $crlf_options,$selected_option, 'id="crlf" class="form-control"');
							?>
						</div>
					</div>
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
						<?php echo form_label(lang('config_smtp_timeout').':', 'smtp_timeout',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'smtp_timeout',
							'id'=>'smtp_timeout',
							'placeholder'=>'5',
							'value'=>$this->config->item('smtp_timeout')));?>
						</div>
					</div>
				</div> <!-- end advanced email -->
					<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_email')) ?>">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<span class="pull-right">
	            	<button id="test_email" type="button" class="btn btn-lg btn-primary"><span id="test_email_icon" class="glyphicon glyphicon-envelope"></span> <?php echo lang('config_send_test_email');?></button>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		
    <!-- Ecommerce Store -->
     <div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_ecommerce_settings_info'), 'store-configuration-options', 'section-ecommerce-platform')  ?>
				</div>	
				<div class="panel-body">
                
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
            	<?php
							echo form_label(lang('config_ecommerce_platform').':', 'ecommerce_platform',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php
										echo form_dropdown('ecommerce_platform', $ecommerce_platforms, $this->config->item('ecommerce_platform'),'id="ecommerce_platform" class="ecommerce_platform form-control"');
									?>          
								</div>
						</div>
                        
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
            	<?php
								echo form_label(lang('config_store_location').':', 'ecom_store_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php
										echo form_dropdown('ecom_store_location', $ecom_store_locations, $this->config->item('ecom_store_location'), 'class="form-control"');
									?>           
								</div>
						</div>
						
						<?php if(count($online_price_tiers) > 1) { ?>
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
            	<?php
								echo form_label(lang('config_online_price_tier').':', 'online_price_tier',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php 
										echo form_dropdown('online_price_tier', $online_price_tiers, $this->config->item('online_price_tier'), 'class="form-control"');
									?>           
								</div>
						</div>
						<?php } ?>
						
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
							<?php echo form_label(lang('config_ecommerce_cron_sync_operations').':', 'ecommerce_cron_sync_operations',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								  	<ul id="check-list-box" data-name="ecommerce_cron_sync_operations[]" class="list-group checked-list-box">
		                   <li class="list-group-item" data-value="import_ecommerce_tags_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_tags_into_phppos'); ?></li>
		                   <li class="list-group-item" data-value="import_ecommerce_categories_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_categories_into_phppos'); ?></li>
		                   <li class="list-group-item" data-value="sync_phppos_item_changes" data-color="success"><?php echo lang('config_sync_phppos_item_changes'); ?></li>
		                   <li class="list-group-item" data-value="sync_inventory_changes" data-color="success"><?php echo lang('config_sync_inventory_changes'); ?></li>
											 <li class="list-group-item" data-requires='["import_ecommerce_tags_into_phppos", "import_ecommerce_categories_into_phppos"]' data-value="import_ecommerce_items_into_phppos" data-color="success"><?php echo lang('config_import_ecommerce_items_into_phppos'); ?></li>
		                   <li class="list-group-item" data-value="export_phppos_tags_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_tags_to_ecommerce'); ?></li>
		                   <li class="list-group-item" data-value="export_phppos_categories_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_categories_to_ecommerce'); ?></li>
											 <li class="list-group-item" data-requires='["export_phppos_tags_to_ecommerce", "export_phppos_categories_to_ecommerce"]' data-value="export_phppos_items_to_ecommerce" data-color="success"><?php echo lang('config_export_phppos_items_to_ecommerce'); ?></li>
								    </ul>
								</div>
						</div>
						
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
            	<?php
								echo form_label(lang('config_ecom_sync_logs').':', 'ecom_store_location',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<ul>
									<?php
									foreach($this->Appfile->get_files_with_name('ecom_log.txt') as $file) 
									{
										echo '<li>'.anchor($this->Appfile->get_url_for_file($file['file_id']),date(get_date_format().' '.get_time_format(), strtotime($file['timestamp'])),array('target' => '_blank')).'</li>';
									} 
									?>           
								</ul>
								</div>
						</div>
						
            <div id="sync_progress" class="form-group hidden" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
							<?php echo form_label(lang('config_ecommerce_progress').':', 'ecommerce_progress',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<div class="well well-sm">
										<div class="progress">
										  <div class="progress-bar progress-bar-striped active" id="progessbar" role="progressbar"
										  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
										    <span id="progress_percent">0</span>% <span id="progress_message"></span>
										  </div>
										</div>
									</div>
								</div>
						</div>
						
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">
							<?php echo form_label(lang('config_last_sync_date').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<div class="input-group">
								  <input readonly type="text" class="form-control form-inps" placeholder="<?php echo lang('config_last_sync_date'); ?>" name="sync_date" id="sync_date" value="<?php echo $this->config->item('last_ecommerce_sync_date') ?  date(get_date_format().' '.get_time_format(),strtotime($this->config->item('last_ecommerce_sync_date'))) : ''; ?>" aria-describedby="input-group-btn">
									<span class = "input-group-btn">
	                  <button id="sync_woo" type="button" class="btn btn-lg btn-primary"><span id="sync_woo_button_icon" class="glyphicon glyphicon-refresh"></span> <?php echo lang('config_sync');?></button>
									</span>
									
									<span class = "input-group-btn hidden" id="cancel-button">
	                  <button id="cancel_woo" type="button" class="btn btn-lg btn-danger"> <?php echo lang('common_cancel');?></button>
									</span>
									
								</div>				
							</div>
						</div>
						
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_ecommerce')) ?>">	
            	<?php
								echo form_label(lang('config_reset_ecommerce').':', 'reset_ecommerce',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
	                  <button id="reset_ecommerce" type="button" class="btn btn-lg btn-danger"> <?php echo lang('config_reset_ecommerce');?></button>
								</div>
						</div>
					
							
				</div>
			</div>
		</div>
    <!-- Woocommerce Settings -->
    <?php
		
		if($this->config->item('ecommerce_platform') == "woocommerce" )
			$hidden_class ="";
		else
			$hidden_class="hidden";
		
		
		?>
      <div class="col-md-12 woo_settings ecom_settings <?php echo $hidden_class; ?>">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<?php echo create_section(lang('config_woocommerce_settings_info'), 'store-configuration-options', 'section-woocommerce-settings')  ?>
				</div>
				
				<div class="panel-body">
					
        	<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
						<?php echo form_label(lang('config_woo_version').':', 'woo_version',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
								echo form_dropdown('woo_version', $woo_versions, $this->config->item('woo_version'),'id="woo_version" class="woo_version form-control"');
							?>
					</div>
				</div>
					
      	<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
						<?php echo form_label(lang('config_woo_api_url').':', 'woo_api_url',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'woo_api_url',
							'id'=>'woo_api_url',
							'value'=>$this->config->item('woo_api_url')));?>
						</div>
				</div>
                        
						<div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
							<?php echo form_label(lang('config_woo_api_key').':', 'woo_api_key',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'woo_api_key',
									'id'=>'woo_api_key',
									'value'=>$this->config->item('woo_api_key')));?>
								</div>
						</div>
                        
            <div class="form-group" data-keyword="<?php echo H(lang('config_keyword_woocommerce')) ?>">	
							<?php echo form_label(lang('config_woo_api_secret').':', 'woo_api_secret',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
									'name'=>'woo_api_secret',
									'id'=>'woo_api_secret',
									'value'=>$this->config->item('woo_api_secret')));?>
								</div>
						</div>
						

										
					</div>
				</div>
				
			</div>
			<div class="form-actions">
			<?php echo form_submit(array(
				'name'=>'submitf',
				'id'=>'submitf',
				'value'=>lang('common_submit'),
				'class'=>'submit_button floating-button btn btn-primary btn-lg pull-right')); ?>
			</div>
        
        <?php
        if($this->config->item('ecommerce_platform') == "none" || $this->config->item('ecommerce_platform') == NULL )
			$display="block";
		else
			$display="none";
		?>
        <!-- No Online Store -->
        <div class="col-md-12 none_settings ecom_settings" style="display:<?php echo $display; ?>">
			
							
						<div class="form-actions">
						<?php echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button floating-button btn btn-primary btn-lg pull-right')); ?>
						</div>			
		</div>
		<?php echo form_close(); ?>	
	</div>
</div>
</div>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{	
	
	date_time_picker_field($('.timepicker'), JS_TIME_FORMAT);
	

	$("#test_email").click(function()
	{
		bootbox.prompt({
			title: <?php echo json_encode(lang('config_please_enter_email_to_send_test_to')); ?>,
			value: <?php echo json_encode($this->Location->get_info_for_key('email')); ?>, 
			callback: function(email) 
			{
				$("#config_form").ajaxSubmit(function()
				{
					$.post(<?php echo json_encode(site_url('config/send_smtp_test_email')); ?>,{email:email},function(response)
					{
						if (response.success)
						{
							show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
						}
						else
						{
							show_feedback('error',<?php echo json_encode(lang('common_error')); ?>,<?php echo json_encode(lang('common_error')); ?>);
							bootbox.alert({
								title: <?php echo json_encode(lang('common_error')); ?>,
								message: response.message
							});
						}
					},'json');
				})
			
			}
		});
	});
	
	var gmail = {
		smtp_crypto : 'ssl',
		protocol : 'smtp', 
		smtp_host : 'smtp.gmail.com',
		smtp_user : 'username@gmail.com',
		smtp_pass : '',
		smtp_port : '465',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var office_365 = {
		smtp_crypto : 'tls',
		protocol : 'smtp', 
		smtp_host : 'smtp.office365.com',
		smtp_user : 'user@domain.com',
		smtp_pass : '',
		smtp_port : '587',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var windows_live_hotmail = {
		smtp_crypto : 'tls',
		protocol : 'smtp', 
		smtp_host : 'smtp.live.com',
		smtp_user : 'user@outlook.com',
		smtp_pass : '',
		smtp_port : '587',
		email_charset : 'utf-8',
		newline : 0,
		crlf : 0,
		smtp_timeout : '10'
	};
	
	var other = {
		smtp_crypto : '',
		protocol : '', 
		smtp_host : '',
		smtp_user : 'user@domain.com',
		smtp_pass : '',
		smtp_port : '',
		email_charset : '',
		newline : 0,
		crlf : 0,
		smtp_timeout : ''
	};
	
	var system_default = {
		smtp_crypto : '',
		protocol : '', 
		smtp_host : '',
		smtp_user : '',
		smtp_pass : '',
		smtp_port : '',
		email_charset : '',
		newline : 0,
		crlf : 0,
		smtp_timeout : ''
	};
	
	if($("#email_provider").val() !== "Other")
	{
		$(".email_advanced").hide();
	}
	
	if($("#email_provider").val() == "Use System Default")
	{
		$(".email_basic").hide();
	}
	
	
	$("#email_provider").change(function(e){
		switch ($("#email_provider").val()) {
				case 'Use System Default':
						$(".email_basic").hide();
						$(".email_advanced").hide();
						var settings = false;
						break;
		    case 'Gmail':
						$(".email_basic").show();
						$(".email_advanced").hide();
						var settings = gmail;
		        break;
		    case 'Office 365':
						$(".email_basic").show();
						$(".email_advanced").hide();
		        var settings = office_365;
		        break;
		    case 'Windows Live Hotmail':
						$(".email_basic").show();
						$(".email_advanced").hide();
		        var settings = windows_live_hotmail;
		        break;
		    case 'Other':
						var settings = other;
						$(".email_basic").show();
		        $(".email_advanced").show();
		        break;
		}
		
		if(settings)
		{
			for (var key in settings)
			{
				if(key == 'smtp_user')
				{
					$("#"+key).val('');
					$("#"+key).attr('placeholder', settings[key]);
				} else if (key == 'newline' || key == 'crlf'){
					$("#"+key).prop('selectedIndex', settings[key]);
				}	else {
					$("#"+key).val(settings[key]);
				}
			}
		} else {
			for (var key in system_default)
			{
				if (key == 'newline' || key == 'crlf'){
					$("#"+key).prop('selectedIndex', settings[key]);
				}	else {
					$("#"+key).val(settings[key]);
				}
			}
		} 
	});
	
	$(".ecommerce_platform").change(function() {
		if ($(".ecommerce_platform").val() == "woocommerce"){
			$(".woo_settings").removeClass('hidden');
		}
		else{
			$(".woo_settings").addClass('hidden');
		}
	});
	
	$(document).on('keyup', ".default_percent_off",function(e){
		
		if ($(this).val())
		{
			$(this).parent().parent().find('.default_cost_plus_percent').val('');
			$(this).parent().parent().find('.default_cost_plus_fixed_amount').val('');
		}
	});

	$(document).on('keyup', ".default_cost_plus_percent",function(e){
		if($(this).val())
		{
			$(this).parent().parent().find('.default_percent_off').val('');
			$(this).parent().parent().find('.default_cost_plus_fixed_amount').val('');
		}
	});

	$(document).on('keyup', ".default_cost_plus_fixed_amount",function(e){
		if($(this).val())
		{
			$(this).parent().parent().find('.default_percent_off').val('');
			$(this).parent().parent().find('.default_cost_plus_percent').val('');
		}
	});
		
	
	$(".delete_tier").click(function()
	{
		$("#config_form").append('<input type="hidden" name="tiers_to_delete[]" value="'+$(this).data('tier-id')+'" />');
		$(this).parent().parent().remove();
	});
	
	$("#price_tiers tbody").sortable();
	
	var add_index = -1;
	
	$("#add_tier").click(function()
	{
		$("#price_tiers tbody").append('<tr><td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td><td><input type="text" class="tiers_to_edit form-control" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][name]" value="" /></td><td><input type="text" class="tiers_to_edit form-control default_percent_off" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_percent_off]" value=""/></td><td><input type="text" class="tiers_to_edit form-control default_cost_plus_percent" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_cost_plus_percent]" value=""/></td><td><input type="text" class="tiers_to_edit form-control default_cost_plus_fixed_amount" data-index="'+add_index+'" name="tiers_to_edit['+add_index+'][default_cost_plus_fixed_amount]" value=""/></td><td>&nbsp;</td></tr>');
		
		add_index--;
	});
	
	$('#additional_payment_types').selectize({
	    delimiter: ',',
	    persist: false,
	    create: function(input) {
	        return {
	            value: input,
	            text: input
	        }
	    }
	});
	
	$(".delete_currency_denom").click(function()
	{
		$(this).parent().parent().remove();
	});
	
	$(".delete_currency_exchange_rate").click(function()
	{
		$(this).parent().parent().remove();	
	});
	
	$("#add_denom").click(function()
	{
		$("#currency_denoms tbody").append('<tr><td><input type="text" class="form-control" name="currency_denoms_name[]" value="" /></td><td><input type="text" class="form-control" name="currency_denoms_value[]" value="" /></td><td>&nbsp;</td></tr>');
	});
	
	$("#add_exchange_rate").click(function()
	{		
		$("#currency_exchange_rates tbody").append('<tr>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_to[]" value="" /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_symbol[]" value="$" /></td>'+
		'<td><select name="currency_exchange_rates_symbol_location[]" class="form-control"><option value="before"><?php echo lang('config_before_number'); ?></option><option value="after"><?php echo lang('config_after_number'); ?></option></select></td>'+
		'<td><select name="currency_exchange_rates_number_of_decimals[]" class="form-control"><option value=""><?php echo lang('config_let_system_decide'); ?></option><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_thousands_separator[]" value="," /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_decimal_point[]" value="." /></td>'+
		'<td><input type="text" class="form-control" name="currency_exchange_rates_rate[]" value="" /></td>'+
		'<td>&nbsp;</td></tr>');
	});
	
	$(".dbOptimize").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(response) 
		{
			$('#ajax-loader').addClass('hidden');
			bootbox.alert(response.message);
		});
		
	});
	
	$(".checkForUpdate").click(function(event)
	{
		event.preventDefault();
		$('#ajax-loader').removeClass('hidden');
		
		$.getJSON($(this).attr('href'), function(update_available) 
		{
			$('#ajax-loader').addClass('hidden');
			if(update_available)
			{
				bootbox.confirm(<?php echo json_encode(lang('common_update_available')); ?>, function(response)
				{
					if (response)
					{
						window.location="http://phppointofsale.com/downloads.php";
					}
				});
			}
			else
			{
				bootbox.alert(<?php echo json_encode(lang('common_not_update_available')); ?>);
			}
		});
		
	});
	
	
	$("#reset_ecommerce").click(function(event)
	{
		bootbox.confirm(<?php echo json_encode(lang('config_confirm_reset_ecom')); ?>, function(response)
		{
			if (response)
			{
				$.getJSON(<?php echo json_encode(site_url('config/reset_ecom')); ?>,function(response)
				{
					if (response.success)
					{
						show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
					}
				});
			}
		});
	});
	var submitting = false;
	$('#config_form').validate({
		submitHandler:function(form)
		{
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			success:function(response)
			{
				
				
				//Don't let the tiers, taxes, providers, methods double submitted, so we change the name
				$('.zones,.tiers_to_edit,.providers,.methods,.taxes,.tax_classes').filter(function() {
				    return parseInt($(this).data('index')) < 0;
				}).attr('name','items_added[]');
				
				if(response.success)
				{
					show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
					
				}
				submitting = false;
			},
			dataType:'json'
		});

		},
		errorClass: "text-danger",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
		},
		rules: 
		{
    		company: "required",
    		sale_prefix: "required",
			return_policy:
			{
				required: true
			},
			item_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			item_kit_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			sale_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			},
			receiving_id_auto_increment:
			{
				number: true,
				min: 1,
				max: 999999999
			}
   	},
		messages: 
		{
     		company: <?php echo json_encode(lang('config_company_required')); ?>,
     		sale_prefix: <?php echo json_encode(lang('config_sale_prefix_required')); ?>,
			return_policy:
			{
				required:<?php echo json_encode(lang('config_return_policy_required')); ?>
			},
	
		}
	});
	
});

$("#calculate_average_cost_price_from_receivings").change(check_calculate_average_cost_price_from_receivings).ready(check_calculate_average_cost_price_from_receivings);

function check_calculate_average_cost_price_from_receivings()
{
	if($("#calculate_average_cost_price_from_receivings").prop('checked'))
	{
		$("#average_cost_price_from_receivings_methods").show();
	}
	else
	{
		$("#average_cost_price_from_receivings_methods").hide();
	}
}

$("#enable_customer_loyalty_system,#loyalty_option").change(check_loyalty_setup).ready(check_loyalty_setup);

function check_loyalty_setup()
{
	if($("#enable_customer_loyalty_system").prop('checked'))
	{
		$("#loyalty_setup").show();
	}
	else
	{
		$("#loyalty_setup").hide();
	}
	
	if ($("#loyalty_option").val() == 'simple')
	{
		$("#loyalty_setup_simple").show();
		$("#loyalty_setup_advanced").hide();
	}
	else
	{
		$("#loyalty_setup_simple").hide();	
		$("#loyalty_setup_advanced").show();	
	}
}

<?php
if ($search = $this->input->get('search')) { ?>
	$("#search").val(<?php echo json_encode($this->input->get('search')); ?>);	
<?php } ?>

$(".config-panel").sieve({
	itemSelector: "div.form-group",
	searchInput: $('#search'),
	complete: function() {

		$(".panel-body").each(function(index) {
			var $this = $(this);
			var $visible_element = $this.find('.form-group').filter(function() 
			{
				return $(this).css('display') != 'none'
			});
			if($visible_element.length == 0)
			{
				$this.closest('.col-md-12').hide();
			}
			else
			{
				$this.closest('.col-md-12').show();
			}

		})
	}
});

$("#search").focus().trigger('keyup');



<?php
$deleted_payment_types = $this->config->item('deleted_payment_types');
$deleted_payment_types = explode(',',$deleted_payment_types);

foreach($deleted_payment_types as $deleted_payment_type)
{
?>
	$( ".payment_types" ).each(function() {
		if ($(this).text() == <?php echo json_encode($deleted_payment_type); ?>)
		{
			$(this).removeClass('btn-primary');			
			$(this).addClass('deleted btn-danger');			
		}
	});
<?php
}
?>
save_deleted_payments();

$(".payment_types").click(function(e)
{
	e.preventDefault();
	$(this).toggleClass('btn-primary');
	$(this).toggleClass('deleted btn-danger');
	save_deleted_payments();
});

function save_deleted_payments()
{
	$(".deleted_payment_types").remove();
	
	var deleted_payment_types = [];
	$( ".payment_types.deleted" ).each(function() {
		deleted_payment_types.push($(this).text());
	});
	$("#config_form").append('<input class="deleted_payment_types" type="hidden" name="deleted_payment_types" value="'+deleted_payment_types.join()+'" />');
	
}

$("#cancel_woo").click(function()
{
	
	bootbox.confirm({
		message: <?php echo json_encode(lang('confirmation_woocommerce_cron_cancel')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$.get(<?php echo json_encode(site_url('ecommerce/cancel'));?>);
				}
		}
	});
});

function check_ecommerce_status()
{
	$.getJSON(SITE_URL+'/home/get_ecommerce_sync_progress', function(response)
	{
		if (response.running)
		{
			$("#cancel-button").removeClass('hidden');
			set_progress(response.percent_complete,response.message);
			setTimeout(check_ecommerce_status,5000);
		}
		else
		{
			$("#cancel-button").addClass('hidden');;
		}
	});
}

function set_progress(percent, message)
{
	$("#sync_progress").toggleClass('hidden', false);
	$('#progessbar').attr('aria-valuenow', percent).css('width',percent+'%');
	$('#progress_percent').html(percent);
	if (message !='')
	{
		$("#progress_message").html('('+message+')');
	}
	else
	{
		$("#progress_message").html('');
	}
	
}

check_ecommerce_status();


$('#sync_woo').click(function()
{
	bootbox.confirm({
		message: <?php echo json_encode(lang('confirmation_woocommerce_cron')); ?>,
		buttons: {
      cancel: {
          label: <?php echo json_encode(lang('common_no')); ?>,
          className: 'btn-default'
      	},
        confirm: {
            label: <?php echo json_encode(lang('common_yes')); ?>,
            className: 'btn-primary'
        }
    },
		callback: function(response)
		{
				if (response)
				{	
					$('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", true);
					
					
					$("#sync_progress").toggleClass("hidden", false);
					
					
					$("#config_form").ajaxSubmit(function()
					{
					 //Wait 3 seconds before checking status for first time
					 setTimeout(function(){ check_ecommerce_status();}, 3000);
						var href = '<?php echo site_url("ecommerce/manual_sync");?>'
						$.ajax(href, {
	  					dataType: "json",
						  success: function(data) {
							  $('#ajax-loader').addClass('hidden');
							  if(data.success){
									
									if (data.cancelled)
									{
										show_feedback('error',<?php echo json_encode(lang('common_cron_cancelled')); ?>,<?php echo json_encode(lang('common_error')); ?>);
									}
									else
									{
										show_feedback('success', <?php echo json_encode(lang('common_cron_success')); ?>, <?php echo json_encode(lang('common_success')); ?>);
									}
									
									$('#sync_woo').parents('.form-group').addClass('has-success').removeClass('has-error');
									
								  $('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
									
									setTimeout(function(){
										$("#sync_progress").toggleClass("hidden", true);
									}, 1000);
									
									$("#sync_date").val(<?php echo json_encode(lang('common_just_now')); ?>);
							  }
							  else{
								  show_feedback('error', data.message);
									$('#sync_woo').parents('.form-group').removeClass('has-success').addClass('has-error');
									
									$('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
									$("#sync_progress").toggleClass("hidden", true);
									
							  }
						  },
						  error: function() {
							  show_feedback('error', <?php echo json_encode(lang('common_access_denied')); ?>);
								$('#sync_woo').parents('.form-group').removeClass('has-success').addClass('has-error');
							  $('#sync_woo_button_icon').toggleClass("glyphicon-refresh-animate", false);
								$("#sync_progress").toggleClass("hidden", true);
						  }
					   });
						 						
					});
	   		}
		}
	});
});

// here

$("#item_lookup_order_list").sortable();

var checklist = <?php echo json_encode(unserialize($this->config->item('ecommerce_cron_sync_operations'))); ?>;

$(function () {
		$group = $('.list-group.checked-list-box');
    $group.find('.list-group-item').each(function () {
        // Settings
        var $widget = $(this),
            $checkbox = $('<input type="checkbox" class="hidden" />'),
						value = ($widget.data('value') ? $widget.data('value') : '1'),
            color = ($widget.data('color') ? $widget.data('color') : "primary"),
            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };
           					 
				$widget.css('cursor', 'pointer');
				$checkbox.val(value).attr('name', $group.data('name'));
        $widget.append($checkbox);

        // Event Handlers
        $widget.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
        });
				
        $checkbox.on('change', function () {
            updateDisplay();
        });
          

        // Actions
        function updateDisplay() {
						
            var isChecked = $checkbox.is(':checked');

            // Set the button's state
            $widget.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $widget.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$widget.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $widget.addClass(style + color);
            } else {
                $widget.removeClass(style + color);
            }
						
						if (isChecked) {	
							if(typeof $widget.data('requires') == 'object')
							{
								$.each($widget.data('requires'), function(key, value)
								{
									$(":checkbox[value="+value+"]").prop("checked",true).trigger('change');
								});
							}
						} else {
							$group.find('.list-group-item').each(function(){
								if(typeof $(this).data('requires') == 'object')
								{
									var that = this;
									$.each($(this).data('requires'), function(key, value) {
										
										if(value == $widget.data('value'))
										{
											$(that).find(":checkbox").prop("checked",false).trigger('change');
										}
									});
								}
							});
						}

        }

        // Initialization
        function init() {
					
					if($.inArray($widget.data('value'), checklist) !== -1)
					{
						$widget.data('checked', true);
					}
					
          if ($widget.data('checked') == true) {
              $checkbox.prop('checked', !$checkbox.is(':checked'));
          }
            
          updateDisplay();

          // Inject the icon if applicable
          if ($widget.find('.state-icon').length == 0) {
              $widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
          }
        }
        init();
    });
});

	$("#tax_classes tbody").sortable();

	var tax_class_index = -1;

	$(document).on('click', '.add_tax_class', function(e) {
		var $tbody = $("#tax_classes").find("tbody");

		var tax_rate_index = 0;
											
		var checkbox_template = '<input type="hidden" name="taxes['+ tax_class_index +'][cumulative][]" value="0" /><input disabled data-index="-1" type="checkbox" class="taxes invisible" id="tax_rate_'+ tax_class_index +'_0_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
		+ '<label class="tax_class_cumulative_element invisible" for="tax_rate_'+ tax_class_index +'_0_cumulative"><span></span></label>';

		var radio_template = '<input data-index="-1" type="radio" id="tax_class_'+ tax_class_index +'_0_default" name="tax_class_id" value="'+tax_class_index+'">'
		+ '<label class="tax_class_default_element" for="tax_class_'+ tax_class_index +'_0_default"><span></span></label>';


		$("#tax_classes").find("tbody").append('<tr data-index="' + tax_class_index +'">' +
			'<td class="tax_class_name top">' +
				'<input type="text" data-index="-1" class="rates form-control tax_classes" name="tax_classes['+ tax_class_index +'][name]" value="" />' +
			'</td>' +
			'<td class="tax_class_rate_name top">' +
				'<input data-index="-1" data-tax-class-id="-1" type="text" class="rates form-control tax_classes" name="taxes['+ tax_class_index +'][name][]" />' +
			'</td>' +
			'<td class="tax_class_rate_percent top">' +
				'<input data-index="-1" data-tax-class-id="-1" type="text" class="rates form-control tax_classes" name="taxes['+ tax_class_index +'][percent][]" />' +
			'</td>' +
			'<td class="tax_class_rate_cumulative top">' + 
				checkbox_template + 
			'</td>' +
			'<td class="tax_class_rate_default">' + 
				radio_template + 
			'</td>' +
			'<td>' +
				'<a class="delete_tax_rate tax_table_rate_text_element"><?php echo lang('common_delete'); ?></a>' +
			'</td>' +
			'<td>' +
				'<a href="javascript:void(0);" class="add_tax_rate"><?php echo lang('config_add_rate'); ?></a>' +
			'</td>' +
			'<td>' +
				'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
			'</td>' +
		'</tr>');

		tax_class_index -= 1;
	});

	$(document).on('click', '.delete_tax_rate', function(e) {
		var $tr = $(this).closest("tr");

		var tax_class_index = $tr.data('index');
		var tax_rate_index = $tr.find('td.tax_class_rate_name > input').length;
						
		if(tax_rate_index > 1)
		{
			var tax_class_tax_id = parseInt($tr.find('.tax_class_rate_name input').last().data('tax-class-tax-id'));
			$tr.find('.tax_class_rate_name input').last().remove();
			$tr.find('.tax_class_rate_percent input').last().remove();
			$tr.find('.tax_class_rate_cumulative input').last().remove();

			if(tax_class_tax_id > 0)
			{
				$("#config_form").append('<input type="hidden" name="taxes_to_delete[]" value="'+ tax_class_tax_id +'" />');
			}
		}
		else
		{

			$tr.remove();
			
			if(tax_class_index > 0)
			{
				$("#config_form").append('<input type="hidden" name="tax_classes_to_delete[]" value="'+ tax_class_index +'" />');
			}
		}
	});

	$(document).on('click', '.add_tax_rate', function(e) {
			var $tr = $(this).closest("tr");
			var tax_class_index = $tr.data('index');
			var tax_rate_index = $tr.find('td.tax_class_rate_name > input').length;
																
			$tr.find('.tax_class_rate_name').append('<input data-index="-1" type="text" data-tax-class-tax-id="-1" class="rates form-control taxes" name="taxes['+ tax_class_index +'][name][]" >');
			$tr.find('.tax_class_rate_percent').append('<input data-index="-1" type="text" class="rates form-control taxes" name="taxes['+ tax_class_index +'][percent][]" >');
			
			if(tax_rate_index == 1)
			{
				var checkbox_template = '<input data-index="-1" type="checkbox" class="taxes" id="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
				+ '<label class="tax_class_cumulative_element" for="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative"><span></span></label>';
				$tr.find('.tax_class_rate_cumulative').append(checkbox_template);
			} else {
				var checkbox_template = '<input type="hidden" name="taxes['+ tax_class_index +'][cumulative][]" value="0" /><input disabled data-index="-1" type="checkbox" class="taxes invisible" id="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative" name="taxes['+ tax_class_index +'][cumulative][]">'
				+ '<label class="tax_class_cumulative_element invisible" for="tax_rate_'+ tax_class_index +'_'+tax_rate_index+'_cumulative"><span></span></label>';
				$tr.find('.tax_class_rate_cumulative').append(checkbox_template);
			}
								
		});
		
		//delivery stuff
		$("#shipping_zones tbody").sortable();
		
		$('.shipping_zone_zips input').selectize({
			delimiter: '|',
			create: true,
			render: {
		      option_create: function(data, escape) {
					var add_new = <?php echo json_encode(lang('common_add_value')) ?>;
		        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
		      }
			},
		});

		var zone_index = -1;
		
		$(document).on('click', '.add_shipping_zone', function(e) {
			
			var $tbody = $("#shipping_zones").find("tbody");
			
			$tbody.append('<tr data-index="' + zone_index +'">' +
				'<td class="shipping_zone_name top">' +
					'<input type="text" data-index="-1" class="zones form-control name" name="zones['+ zone_index +'][name]" value="" />' +
				'</td>' +
				'<td class="shipping_zone_zips top">' +
					'<input type="text" data-index="-1" class="zones form-control name" name="zones['+ zone_index +'][zips]" value="" />' +
				'</td>' +
				'<td class="shipping_zone_fee top">' +
					'<input data-index="-1" type="text" class="zones form-control fee" name="zones['+ zone_index +'][fee]" />' +
			'</td>');
			
			$tr = $tbody.find('tr').last();
			
			$tr.find('.shipping_zone_zips input').selectize({
				delimiter: '|',
				create: true,
				render: {
			      option_create: function(data, escape) {
						var add_new = <?php echo json_encode(lang('common_add_value')) ?>;
			        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
			      }
				},
			});
			
			var tax_groups = <?php echo json_encode($tax_groups); ?>
						
			var tax_group_select = $('<select>').addClass('zones form-control').attr('name', 'zones['+ zone_index +'][tax_class_id]').attr('data-index', zone_index);
			$('<td class="shipping_zone_tax_group top" >').append(tax_group_select).appendTo($tr);
			
			$(tax_groups).each(function() {
			 tax_group_select.append($("<option>").attr('value',this.val).text(this.text));
			});
			
			$tr.append(
				'<td>' +
					'<a class="delete_rate"><?php echo lang('common_delete'); ?></a>' +
				'</td>' +
				'<td>' +
					'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
				'</td>'
				);
				
				zone_index --;
			});
		
		$("#shipping_providers tbody").sortable();
		
		
		var provider_index = -1;
		
		$(document).on('click', '.add_shipping_provider', function(e) {
			var $tbody = $("#shipping_providers").find("tbody");
			
			var rate_index = 0;
			var radio_template = '<input data-index="-1" type="radio" class="methods" id="default_shipping_rate_'+ provider_index +'_0" name="methods['+ provider_index +'][is_default][]" checked="checked">'
			+ '<label class="shipping_table_rate_element" for="default_shipping_rate_'+ provider_index +'_0"><span></span></label>';
			
			$tbody.append('<tr data-index="' + provider_index +'">' +
				'<td class="shipping_provider_name top">' +
					'<input type="text" data-index="-1" class="rates form-control providers" name="providers['+ provider_index +'][name]" value="" />' +
				'</td>' +
				'<td class="delivery_rate_name top">' +
					'<input data-index="-1" data-method-id="-1" type="text" class="rates form-control methods" name="methods['+ provider_index +'][name][]" />' +
				'</td>' +
				'<td class="delivery_fee top">' + 
					'<input type="text" data-index="-1" class="rates form-control methods" name="methods['+ provider_index +'][fee][]" />' +
				'</td>' +
				'<td class="delivery_time top">' +
					'<input type="text" data-index="-1" class="rates form-control methods" name="methods['+ provider_index +'][time_in_days][]" />' +
				'</td>' +
				'<td class="delivery_default top">' + 
					radio_template + 
				'</td>' +
				'<td>' +
					'<a class="delete_rate shipping_table_rate_text_element"><?php echo lang('common_delete'); ?></a>' +
				'</td>' +
				'<td>' +
					'<a href="javascript:void(0);" class="add_delivery_rate"><?php echo lang('config_add_rate'); ?></a>' +
				'</td>' +
				'<td>' +
					'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
				'</td>' +
			'</tr>');
			
			provider_index -= 1;
		});
		
		$(document).on('click', '.delete_zone', function(e) {
			var $tr = $(this).closest("tr");
			var index = $tr.data('index');
						
			$tr.remove();
			
			if(index > 0)
			{
				$("#config_form").append('<input type="hidden" class="delete_zone" name="zones_to_delete[]" value="'+ index +'" />');
			}
		});
		
		$(document).on('click', '.delete_rate', function(e) {
			var $tr = $(this).closest("tr");
			
			var index = $tr.data('index');
			var rate_index = $tr.find('td.delivery_rate_name > input').length;
			var method_id = parseInt($tr.find('.delivery_rate_name input').last().data('method-id'));
			
			if(rate_index > 1)
			{
				$tr.find('.delivery_rate_name input').last().remove();
				$tr.find('.delivery_fee input').last().remove();
				$tr.find('.delivery_time input').last().remove();
				$tr.find('.delivery_default input').last().remove();
				$tr.find('.delivery_default label').last().remove();
				
				if(method_id > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_method" name="methods_to_delete[]" value="'+ method_id +'" />');
				}
			}
			else
			{
				
				$tr.remove();
				
				if(method_id > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_method" name="methods_to_delete[]" value="'+ method_id +'" />');
				}
										
				if(index > 0)
				{
					$("#config_form").append('<input type="hidden" class="delete_provider" name="providers_to_delete[]" value="'+ index +'" />');
				}
			}
		});
		
		$(document).on('click', '.add_delivery_rate', function(e) {
				var $tr = $(this).closest("tr");
				var index = $tr.data('index');
				var rate_index = $tr.find('td.delivery_rate_name > input').length;
																				
				$tr.find('.delivery_rate_name').append('<input data-index="-1" type="text" data-method-id="-1" class="rates form-control methods" name="methods['+ index +'][name][]" >');
				$tr.find('.delivery_fee').append('<input data-index="-1" type="text" class="rates form-control methods" name="methods['+ index +'][fee][]" >');
				$tr.find('.delivery_time').append('<input data-index="-1" type="text" class="rates form-control methods" name="methods['+ index +'][time_in_days][]" >');
				
				var radio_template = '<input data-index="-1" type="radio" class="methods" id="default_shipping_rate_'+ index +"_"+ rate_index + '" name="methods['+ index +'][is_default][]">'
				+ '<label class="shipping_table_rate_element" for="default_shipping_rate_'+ index +"_"+ rate_index + '"><span></span></label>';
				
				$tr.find('.delivery_default').append(radio_template);
												
			});

</script>
<?php $this->load->view("partial/footer"); ?>
