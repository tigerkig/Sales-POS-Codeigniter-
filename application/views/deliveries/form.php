<?php $this->load->view("partial/header"); ?>
		<?php echo form_open('deliveries/save/'.$this->uri->segment('3'),array('id'=>'edit_delivery_form','class'=>'form-horizontal')); 	?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang("deliveries_basic_info"); ?> (<small><?php echo lang('common_fields_required_message'); ?></small>)
			</div>
			
			
			
			<div class="panel-body">
				
				<div class="form-group">
					<?php echo form_label(lang('common_actions').':', 'edit_sale',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php
							echo anchor(site_url('sales/change_sale/'.$delivery_info['sale_id']), lang('deliveries_edit_sale'), array('id' => 'edit_sale', 'class' => 'btn btn-primary'));
						?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('deliveries_status').':', 'status',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php 
						$status =$delivery_info['status']; 
						
						$status_types['not_scheduled'] = lang('deliveries_not_scheduled');
						$status_types['scheduled'] = lang('deliveries_scheduled');
						$status_types['shipped'] = lang('delivieries_shipped');
						$status_types['delivered'] = lang('deliveries_delivered');
						
						echo form_dropdown('status', $status_types, $status, 'class="form-control form-inps" id="status"'); ?>
								
					</div>
					
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_first_name').':', 'first_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'first_name',
							'id'=>'first_name',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['first_name'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_last_name').':', 'last_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'last_name',
							'id'=>'last_name',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['last_name'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_address_1').':', 'address_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'address_1',
							'id'=>'address_1',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['address_1'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_address_2').':', 'address_2',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'address_2',
							'id'=>'address_2',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['address_2'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_city').':', 'city',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'city',
							'id'=>'city',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['city'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_state').':', 'state',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'state',
							'id'=>'state',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['state'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_zip').':', 'zip',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'zip',
							'id'=>'zip',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['zip'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_country').':', 'country',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'country',
							'id'=>'country',
							'class'=>'form-control form-inps',
							'value'=>$delivery_person_info['country'])
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('deliveries_tracking_number').':', 'tracking_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'tracking_number',
							'id'=>'tracking_number',
							'class'=>'form-control form-inps',
							'value'=>$delivery_info['tracking_number'])
						);?>
					</div>
				</div>
				
					<div class="form-group">	
				<?php echo form_label(lang('common_comments').':', 'comment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_textarea(array(
						'name'=>'comment',
						'id'=>'comment',
						'class'=>'form-control text-area',
						'value'=>$delivery_info['comment'],
						'rows'=>'5',
						'cols'=>'17')		
					);?>
					</div>
				</div>
				
				
				
				<div id="is_pickup_field" class="form-group">	
					<?php echo form_label(lang('deliveries_is_pickup').':', 'is_pickup',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					
						<?php 	
						
						$data = array(
										'class'					=> 'form-control form-inps',
										'readonly'			=> true,
						        'id'            => 'is_pickup',
						        'value'         => $delivery_info['is_pickup'] === '1' ?  lang('common_yes') : lang('common_no'),
										'data-toggle'		=> 'tooltip',
										'data-placement' => 'top',
										'title' 				=> lang('deliveries_edit_sale_tool_tip')
						);

						echo form_input($data);

						?>
	
					</div>	
				</div>
				
				<div id="provider_field" class="form-group <?php echo $delivery_info['is_pickup'] === '1' ? 'hidden' : '' ?>">
					<?php echo form_label(lang('deliveries_shipping_provider').':', 'shipping_provider',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php 
						$selected_method = $delivery_info['shipping_method_id']; 
						
						
						$providers = array();
						$providers[''] = lang('common_none');
						
						$selected_provider = '';
						foreach($providers_with_methods as $provider)
						{
							if($selected_provider === '')
							{
								foreach($provider['methods'] as $method)
								{
									if($method['id'] == $selected_method)
									{
										$selected_provider = $method['shipping_provider_id'];
										break;
									}	
								}
							}
							
							
							$providers[$provider['id']] = $provider['name'];
						}
						
						$data = array(
										'class'					=> 'form-control form-inps',
										'readonly'			=> true,
						        'id'            => 'provider',
						        'value'         => $providers[$selected_provider],
										'data-toggle'		=> 'tooltip',
										'data-placement' => 'top',
										'title' 				=> lang('deliveries_edit_sale_tool_tip')
						);

						echo form_input($data);
						
						?>
						 
					</div>
				</div>
				
				<div id="method_field" class="form-group <?php echo $delivery_info['is_pickup'] === '1' ? 'hidden' : '' ?>">
					<?php echo form_label(lang('deliveries_shipping_method').':', 'shipping_method',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php
						
						$selected_method = $delivery_info['shipping_method_id']; 
						
						$methods = array();
						$methods[''] = lang('common_none');
						
						foreach($providers_with_methods as $provider)
						{
							if($provider['id'] == $selected_provider)
							{
								foreach($provider['methods'] as $method)
								{
									$methods[$method['id']] = $method['name'];
								}
							}
						}
						
						$data = array(
										'class'					=> 'form-control form-inps',
										'readonly'			=> true,
						        'id'            => 'method',
						        'value'         => $methods[$selected_method],
										'data-toggle'		=> 'tooltip',
										'data-placement' => 'top',
										'title' 				=> lang('deliveries_edit_sale_tool_tip')
						);

						echo form_input($data);
						?>
						
					</div>
				</div>
				
				<div id="estimated_shipping_date_field" class="form-group <?php echo $delivery_info['is_pickup'] === '1' ? 'hidden' : '' ?>">
					<?php echo form_label(lang('deliveries_estimated_shipping_date').':', 'estimated_shipping_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $delivery_info['estimated_shipping_date'] ? date(get_date_format(), strtotime($delivery_info['estimated_shipping_date'])) : ''; ?>">
							<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
							<?php echo form_input(array(
						        'name'=>'estimated_shipping_date',
						        'id'=>'estimated_shipping_date',
										'class'=>'form-control datepicker',
						        'value'=>$delivery_info['estimated_shipping_date'] ? date(get_date_format().' '.get_time_format(), strtotime($delivery_info['estimated_shipping_date'])) : ''
						    ));?> 
					    </div>
				    </div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(($delivery_info['is_pickup'] === '1' ? lang('deliveries_estimated_pickup_date') : lang('deliveries_estimated_delivery_date')) . ':', 'estimated_delivery_or_pickup_date',array('id' => 'estimated_delivery_or_pickup_date_label', 'class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $delivery_info['estimated_delivery_or_pickup_date'] ? date(get_date_format(), strtotime($delivery_info['estimated_delivery_or_pickup_date'])) : ''; ?>">
							<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
							<?php echo form_input(array(
					        'name' => 'estimated_delivery_or_pickup_date',
					        'id' => 'estimated_delivery_or_pickup_date',
									'class' => 'form-control datepicker',
					        'value' => $delivery_info['estimated_delivery_or_pickup_date'] ? date(get_date_format().' '.get_time_format(), strtotime($delivery_info['estimated_delivery_or_pickup_date'])) : ''
						    ));?> 
					    </div>
				    </div>
				</div>
				
				<div id="actual_shipping_date_field" class="form-group <?php echo $delivery_info['is_pickup'] === '1' ? 'hidden' : '' ?>">
					<?php echo form_label(lang('deliveries_actual_shipping_date').':', 'actual_shipping_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $delivery_info['actual_shipping_date'] ? date(get_date_format(), strtotime($delivery_info['actual_shipping_date'])) : ''; ?>">
							<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
							<?php echo form_input(array(
						        'name'=>'actual_shipping_date',
						        'id'=>'actual_shipping_date',
										'class'=>'form-control datepicker',
						        'value'=>$delivery_info['actual_shipping_date'] ? date(get_date_format().' '.get_time_format(), strtotime($delivery_info['actual_shipping_date'])) : ''
						    ));?> 
					    </div>
				    </div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(($delivery_info['is_pickup'] === '1' ? lang('deliveries_actual_pickup_date') : lang('deliveries_actual_delivery_date')).':', 'actual_delivery_or_pickup_date',array('id' => 'actual_delivery_or_pickup_date_label', 'class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					    <div class="input-group date" data-date="<?php echo $delivery_info['actual_delivery_or_pickup_date'] ? date(get_date_format(), strtotime($delivery_info['actual_delivery_or_pickup_date'])) : ''; ?>">
							<span class="input-group-addon bg"><i class="ion ion-ios-calendar-outline"></i></span>
							<?php echo form_input(array(
					        'name' => 'actual_delivery_or_pickup_date',
					        'id' => 'actual_delivery_or_pickup_date',
									'class' => 'form-control datepicker',
					        'value' => $delivery_info['actual_delivery_or_pickup_date'] ? date(get_date_format().' '.get_time_format(), strtotime($delivery_info['actual_delivery_or_pickup_date'])) : ''
						    ));?> 
					    </div>
				    </div>
				</div>
						
				<div class="form-controls">	
					<ul class="list-inline pull-right">
						<li>
							<?php
								echo form_submit(array(
									'name'=>'submitf',
									'id'=>'submitf',
									'value'=>lang('common_submit'),
									'class'=>' btn btn-primary')
								);
							?>
						</li>
					</ul>
				</div>
			</div> <!-- close pannel body -->
			<?php echo form_close(); ?>
			<script>
				$(document).ready(function(){
				    $('[data-toggle="tooltip"]').tooltip(); 
				});
				
				date_time_picker_field($('.datepicker'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
				
				$status = $("#status");
				
				$('#estimated_shipping_date, #estimated_delivery_or_pickup_date, #actual_shipping_date, #actual_delivery_or_pickup_date').on('dp.change input', function (e) {
					if($("#actual_delivery_or_pickup_date").val())
					{
						$status.val('delivered');
					}
					else if($("#actual_shipping_date").val())
					{
						$status.val('shipped');
					}
					else if($("#estimated_shipping_date").val() || $("#estimated_delivery_or_pickup_date").val())
					{
						$status.val('scheduled');
					}
					else
					{
						$status.val('not_scheduled');
					}
				});
								
				
			</script>
<?php $this->load->view("partial/footer"); ?>
		
