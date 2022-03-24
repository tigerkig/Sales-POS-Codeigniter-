<?php $this->load->view("partial/header"); ?>

	<div class="row" id="form">
		<div class="spinner" id="grid-loader" style="display:none">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		</div>
		<div class="col-md-12">


		<?php if($person_info->person_id && !isset($is_clone))  { ?>
			<div class="panel">
				<div class="panel-body ">
					<div class="user-badge">
						<?php echo $person_info->image_id ? '<div class="user-badge-avatar">'.img(array('src' => app_file_url($person_info->image_id),'class'=>'img-polaroid img-polaroid-s')).'</div>' : '<div class="user-badge-avatar">'.img(array('src' => base_url('assets/assets/images/avatar-default.jpg'),'class'=>'img-polaroid')).'</div>'; ?>
						<div class="user-badge-details">
						<?php echo $person_info->first_name.' '.$person_info->last_name; ?>
						<p><?php echo $person_info->username; ?></p>
						</div>
						<ul class="list-inline pull-right">
							<?php
								$six_months_ago = date('Y-m-d', strtotime('-6 months'));
								$today = date('Y-m-d').'%2023:59:59';	
							?>
							<li><a href="<?php echo site_url('reports/specific_employee/'.$six_months_ago.'/'.$today.'/'.$person_info->person_id.'/all/0'); ?>" class="btn btn-success"><?php echo lang('common_view_report'); ?></a></li>
							<?php if ($person_info->email) { ?>
								<li><a href="mailto:<?php echo $person_info->email; ?>" class="btn btn-primary"><?php echo lang('common_send_email'); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		<?php } ?>

			<?php 	$current_employee_editing_self = $this->Employee->get_logged_in_employee_info()->person_id == $person_info->person_id;
					echo form_open('employees/save/'.(!isset($is_clone) ? $person_info->person_id: ''),array('id'=>'employee_form','class'=>'form-horizontal'));
			?>
			
			
			<div class="panel panel-piluku">
				<div class="panel-heading">
	                <h3 class="panel-title">
	                    <i class="ion-edit"></i> 
	                    <?php echo lang("employees_basic_information"); ?>
    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
		        </div>

				<div class="panel-body">

					<?php $this->load->view("people/form_basic_info"); ?>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_commission_default_rate').':', 'commission_percent',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'commission_percent',
							'id'=>'commission_percent',
							'class'=>'form-control',
							'value'=>to_quantity($person_info->commission_percent,FALSE)));?>%
						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$person_info->commission_percent_type,
							array('class' => 'form-control',
									'id' => 'commission_percent_type'))
							?>
						</div>
					</div>
					
				
					<?php if ($this->config->item('timeclock')) {?>
						<div class="form-group">	
							<?php echo form_label(lang('common_hourly_pay_rate'), 'hourly_pay_rate',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<div class="input-group">
							      <div class="input-group-addon"><?php echo $this->config->item('currency_symbol'); ?></div>
							      <?php echo form_input(array(
									'name'=>'hourly_pay_rate',
									'id'=>'hourly_pay_rate',
									'class'=>'form-control',
									'value'=>$person_info->hourly_pay_rate? to_currency_no_money($person_info->hourly_pay_rate, 2) : ''));?>
							    </div>

								
							</div>
						</div>
					<?php 
					}
					else
					{
						echo form_hidden('hourly_pay_rate', 0);
					}
					?>
					
					
					<div class="form-group offset1">
						<?php echo form_label(lang('employees_hire_date').':', 'hire_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						    <div class="input-group date">
								<span class="input-group-addon bg">
		                           <i class="ion ion-ios-calendar-outline"></i>
		                       	</span>
								<?php echo form_input(array(
							        'name'=>'hire_date',
							        'id'=>'hire_date',
										'class'=>'form-control datepicker',
							        'value'=>$person_info->hire_date ? date(get_date_format(), strtotime($person_info->hire_date)) : '')
							    );?> 
						    </div>
					    </div>
					</div>
					
					
					<div class="form-group offset1">
						<?php echo form_label(lang('employees_birthday').':', 'birthday',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						    <div class="input-group date">
								<span class="input-group-addon bg">
		                           <i class="ion ion-ios-calendar-outline"></i>
		                       	</span>
								<?php echo form_input(array(
							        'name'=>'birthday',
							        'id'=>'birthday',
									'class'=>'form-control datepicker',
							        'value'=>$person_info->birthday ? date(get_date_format(), strtotime($person_info->birthday)) : '')
							    );?> 
						    </div>
					    </div>
					</div>
					
					
					<div class="form-group">	
						<?php echo form_label(lang('common_employees_number').':', 'employee_number',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'employee_number',
							'id'=>'employee_number',
							'class'=>'form-control',
							'value'=>$person_info->employee_number));?>
						</div>
					</div>
					
					<div class="form-heading">
						<?php echo lang("common_login_info"); ?>
					</div>
					<div class="form-group">	
					<?php echo form_label(lang('common_username').':', 'username',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'username',
							'id'=>'username',
							'class'=>'form-control',
							'value'=>$person_info->username));?>
						</div>
					</div>

					<div class="form-group">	
					<?php echo form_label(lang('common_password').':', 'password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name'=>'password',
							'id'=>'password',
							'class'=>'form-control',
							'autocomplete'=>'off',
						));?>
						</div>
					</div>

					<div class="form-group">	
					<?php echo form_label(lang('common_repeat_password').':', 'repeat_password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_password(array(
							'name'=>'repeat_password',
							'id'=>'repeat_password',
							'class'=>'form-control',
							'autocomplete'=>'off',
						));?>
						</div>
					</div>
					
					
					<div class="form-group">	
					<?php echo form_label(lang('employees_force_password_change_upon_login').':', 'force_password_change',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'force_password_change',
								'id' => 'force_password_change',
								'value' => 1,
								'checked' => $person_info->force_password_change,
								));
								echo '<label for="force_password_change"><span></span></label>';;
							?>
						</div>
					</div>
					
					<div class="form-group">	
					<?php echo form_label(lang('employees_always_require_password').':', 'always_require_password',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'always_require_password',
								'id' => 'always_require_password',
								'value' => 1,
								'checked' => $person_info->always_require_password,
								));
								echo '<label for="always_require_password"><span></span></label>';;
							?>
						</div>
					</div>
					
				
					<?php if ($this->config->item('timeclock')) {?>
					<div class="form-group">	
					<?php echo form_label(lang('employees_not_required_to_clock_in').':', 'not_required_to_clock_in',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'not_required_to_clock_in',
								'id' => 'not_required_to_clock_in',
								'value' => 1,
								'checked' => $person_info->not_required_to_clock_in,
								));
								echo '<label for="not_required_to_clock_in"><span></span></label>';;
							?>
						</div>
					</div>
					<?php } ?>
					
					<div class="form-group">	
					<?php echo form_label(lang('employees_inactive').':', 'inactive',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php
							echo	form_checkbox(array(
								'name' => 'inactive',
								'id' => 'inactive',
								'value' => 1,
								'checked' => $person_info->inactive,
								));
								echo '<label for="inactive"><span></span></label>';;
							?>
						</div>
					</div>
					
					<div id="inactive_info">
						<div class="form-group">	
						<?php echo form_label(lang('employees_reason_inactive').':', 'reason_inactive',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'reason_inactive',
								'id'=>'reason_inactive',
								'class'=>'form-control text-area',
								'value'=>$person_info->reason_inactive,
								'rows'=>'5',
								'cols'=>'17')		
							);?>
							</div>
						</div>
						
						<div class="form-group offset1">
							<?php echo form_label(lang('employees_termination_date').':', 'termination_date',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label text-info wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							    <div class="input-group date">
									<span class="input-group-addon bg">
			                           <i class="ion ion-ios-calendar-outline"></i>
			                       	</span>
									<?php echo form_input(array(
								        'name'=>'termination_date',
								        'id'=>'termination_date',
										'class'=>'form-control datepicker',
								        'value'=>$person_info->termination_date ? date(get_date_format(), strtotime($person_info->termination_date)) : '')
								    );?> 
							    </div>
						    </div>
						</div>
					</div>
					
					<div class="form-group">	
					<?php echo form_label(lang('common_language').':', 'language',array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
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
							$person_info->language ? $person_info->language : $this->Appconfig->get_raw_language_value(), 'class="form-control" id="language"');
							?>
						</div>
					</div>
					
					<?php if (count($locations) == 1) { ?>
						<?php
							echo form_hidden('locations[]', current(array_keys($locations)));
						?>
					<?php }else { ?>
						<div class="form-group">	
						<?php echo form_label(lang('common_locations').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<ul id="locations_list" class="list-inline">
							<?php
								foreach($locations as $location_id => $location) 
								{
									$checkbox_options = array(
									'name' => 'locations[]',
									'id' => 'locations'.$location_id,
									'value' => $location_id,
									'checked' => $location['has_access'],
									);
									
									if (!$location['can_assign_access'])
									{
										$checkbox_options['disabled'] = 'disabled';
										
										//Only send permission if checked
										if ($checkbox_options['checked'])
										{
											echo form_hidden('locations[]', $location_id);
										}
									}
																
									echo '<li>'.form_checkbox($checkbox_options). '<label for="locations'.$location_id.'"><span></span></label> '.$location['name'].'</li>';
								}
							?>
							</ul>
							</div>
						</div>
					<?php } ?>

 				 <?php for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++) { ?>
 					<?php
 					 $custom_field = $this->Employee->get_custom_field($k);
 					 if($custom_field !== FALSE)
 					 { ?>
 						 <div class="form-group">
 						 <?php echo form_label($custom_field . ' :', "custom_field_${k}_value", array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						 							
 						 <div class="col-sm-9 col-md-9 col-lg-10">
 								<?php if ($this->Employee->get_custom_field($k,'type') == 'checkbox') { ?>
									
 									<?php echo form_checkbox("custom_field_${k}_value", '1', (boolean)$person_info->{"custom_field_${k}_value"},"id='custom_field_${k}_value'");?>
 									<label for="<?php echo "custom_field_${k}_value"; ?>"><span></span></label>
									
 								<?php } elseif($this->Employee->get_custom_field($k,'type') == 'date') { ?>
									
 										<?php echo form_input(array(
 										'name'=>"custom_field_${k}_value",
 										'id'=>"custom_field_${k}_value",
 										'class'=>"custom_field_${k}_value".' form-control',
 										'value'=>is_numeric($person_info->{"custom_field_${k}_value"}) ? date(get_date_format(), $person_info->{"custom_field_${k}_value"}) : '')
 										);?>									
 										<script>
 											var $field = <?php echo "\$('#custom_field_${k}_value')"; ?>;
 									    $field.datetimepicker({format: JS_DATE_FORMAT, locale: LOCALE, ignoreReadonly: IS_MOBILE ? true : false});	
											
 										</script>
											
 								<?php } elseif($this->Employee->get_custom_field($k,'type') == 'dropdown') { ?>
										
 										<?php 
 										$choices = explode('|',$this->Employee->get_custom_field($k,'choices'));
 										$select_options = array();
 										foreach($choices as $choice)
 										{
 											$select_options[$choice] = $choice;
 										}
 										echo form_dropdown("custom_field_${k}_value", $select_options, $person_info->{"custom_field_${k}_value"}, 'class="form-control"');?>
										
 								<?php } else {
								
 										echo form_input(array(
 										'name'=>"custom_field_${k}_value",
 										'id'=>"custom_field_${k}_value",
 										'class'=>"custom_field_${k}_value".' form-control',
 										'value'=>$person_info->{"custom_field_${k}_value"})
 										);?>									
 								<?php } ?>
 							</div>
 						</div>
 					<?php } //end if?>
 					<?php } //end for loop?>
					
					<div class="form-heading">
						<?php echo lang("employees_permission_info"); ?><br>
						<p class="text-center"><?php echo lang("employees_permission_desc"); ?></p>
					</div>

					<div class="panel-body form-group">
					
						<ul id="permission_list" class="list-unstyled">
						<?php
						foreach($all_modules->result() as $module)
						{
							$checkbox_options = array(
							'name' => 'permissions[]',
							'id' => 'permissions'.$module->module_id,
							'value' => $module->module_id,
							'checked' => $this->Employee->has_module_permission($module->module_id,$person_info->person_id),
							'class' => 'module_checkboxes '
							);
							
							if ($logged_in_employee_id != 1)
							{
								if(($current_employee_editing_self && $checkbox_options['checked']) || !$this->Employee->has_module_permission($module->module_id,$logged_in_employee_id))
								{
									$checkbox_options['disabled'] = 'disabled';
									
									//Only send permission if checked
									if ($checkbox_options['checked'])
									{
										echo form_hidden('permissions[]', $module->module_id);
									}
								}
							}
						?>
						<li>	
						<?php echo form_checkbox($checkbox_options).'<label for="permissions'.$module->module_id.'"><span></span></label>'; ?>
						<span class="text-success"><?php echo lang('module_'.$module->module_id);?>:</span>
						<span class="text-warning"><?php echo lang('module_'.$module->module_id.'_desc');?></span>
							<ul class="list-unstyled list-permission-actions">
							<?php
							foreach($this->Module_action->get_module_actions($module->module_id)->result() as $module_action)
							{
								$checkbox_options = array(
								'name' => 'permissions_actions[]',
								'data-module-checkbox-id' => 'permissions'.$module->module_id,
								'class' => 'module_action_checkboxes',
								'id' => 'permissions_actions'.$module_action->module_id."|".$module_action->action_id,
								'value' => $module_action->module_id."|".$module_action->action_id,
								'checked' => $this->Employee->has_module_action_permission($module->module_id, $module_action->action_id, $person_info->person_id)
								);
			
								if ($logged_in_employee_id != 1)
								{
									if(($current_employee_editing_self && $checkbox_options['checked']) || (!$this->Employee->has_module_action_permission($module->module_id,$module_action->action_id,$logged_in_employee_id)))
									{
										$checkbox_options['disabled'] = 'disabled';
										
										//Only send permission if checked
										if ($checkbox_options['checked'])
										{
											echo form_hidden('permissions_actions[]', $module_action->module_id."|".$module_action->action_id);
										}
									}							
								}
								?>
								<li>
								<?php echo form_checkbox($checkbox_options).'<label for="permissions_actions'.$module_action->module_id."|".$module_action->action_id.'"><span></span></label>'; ?>
								<span class="text-info"><?php echo lang($module_action->action_name_key);?></span>
								</li>
							<?php
							}
							?>
							</ul>
						</li>
						<?php
						}
						?>
						</ul>
					
					</div>

					<?php echo form_hidden('redirect_code', $redirect_code); ?>

					<div class="form-actions pull-right">
					<?php
							echo form_submit(array(
								'name'=>'submitf',
								'id'=>'submitf',
								'value'=>lang('common_submit'),
								'class'=>'btn floating-button btn-primary float_right')
							);

					?>
					</div>
					
			</div>
		</div>
		<?php 
					echo form_close();
					?>
	</div>
</div>
</div>					

<script type='text/javascript'>
$('#image_id').imagePreview({ selector : '#avatar' }); // Custom preview container

//validation and submit handling
$(document).ready(function()
{	
	date_time_picker_field($(".datepicker"), JS_DATE_FORMAT + " "+ JS_TIME_FORMAT);	
	$("#inactive").change(check_inactive);
	
	check_inactive();
	
	function check_inactive()
	{
		if ($("#inactive").prop('checked'))
		{
			$("#inactive_info").show();
		}
		else
		{
			$("#inactive_info").hide();
		}
	}
	
	
	
    setTimeout(function(){$(":input:visible:first","#employee_form").focus();},100);
	$(".module_checkboxes").change(function()
	{
		if ($(this).prop('checked'))
		{
			$(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', true);
		}
		else
		{
			$(this).parent().find('input[type=checkbox]').not(':disabled').prop('checked', false);			
		}
	});
	
	$(".module_action_checkboxes").change(function()
	{
		if ($(this).prop('checked'))
		{
			$('#'+$(this).data('module-checkbox-id')).prop('checked', true);
		}
	});	

	$('#employee_form').validate({
		submitHandler:function(form)
		{
			$.post('<?php echo site_url("employees/check_duplicate");?>', {term: $('#first_name').val()+' '+$('#last_name').val()},function(data) {
			<?php if(!$person_info->person_id) { ?>
			if(data.duplicate)
			{					
				bootbox.confirm(<?php echo json_encode(lang('employees_duplicate_exists'));?>, function(result)
				{
					if (result)
					{
						doEmployeeSubmit(form);
					}
				});					
			}
			else
			{
				doEmployeeSubmit(form);
			}
			<?php } else { ?>
				doEmployeeSubmit(form);
			<?php } ?>
			} , "json")
			.error(function() { 
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
			first_name: "required",
			
			username:
			{
				<?php if(!$person_info->person_id) { ?>
				remote: 
			    { 
					url: "<?php echo site_url('employees/exmployee_exists');?>", 
					type: "post"
			    }, 
				<?php } ?>
				required:true,
				minlength: 5
			},

			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:true,
				<?php
				}
				?>
				minlength: 8
			},	
			repeat_password:
			{
 				equalTo: "#password"
			},
    		email: {
				"required": true
			},
			"locations[]": "required"
   		},
		messages: 
		{
     		first_name: <?php echo json_encode(lang('common_first_name_required')); ?>,
     		last_name: <?php echo json_encode(lang('common_last_name_required')); ?>,
     		username:
     		{
				<?php if(!$person_info->person_id) { ?>
	     			remote: <?php echo json_encode(lang('employees_username_exists')); ?>,
				<?php } ?>
     			required: <?php echo json_encode(lang('common_username_required')); ?>,
     			minlength: <?php echo json_encode(lang('common_username_minlength')); ?>
     		},
			password:
			{
				<?php
				if($person_info->person_id == "")
				{
				?>
				required:<?php echo json_encode(lang('employees_password_required')); ?>,
				<?php
				}
				?>
				minlength: <?php echo json_encode(lang('common_password_minlength')); ?>
			},
			repeat_password:
			{
				equalTo: <?php echo json_encode(lang('common_password_must_match')); ?>
     		},
     		email: <?php echo json_encode(lang('common_email_invalid_format')); ?>,
			"locations[]": <?php echo json_encode(lang('employees_one_location_required')); ?>
		}
	});
});

var submitting = false;

function doEmployeeSubmit(form)
{
	$("#grid-loader").show();
	if (submitting) return;
	submitting = true;

	$(form).ajaxSubmit({
	success:function(response)
		{
			$("#grid-loader").hide();
			submitting = false;
			if(response.redirect_code==1 && response.success)
			{
				if (response.success)
				{
					show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				}
				else
				{
					show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
				}
			}
			else if(response.redirect_code==2 && response.success)
			{
				window.location.href = '<?php echo site_url('employees'); ?>';
			}
			else if(response.success)
			{
				show_feedback('success',response.message,<?php echo json_encode(lang('common_success')); ?>);
				$("html, body").animate({ scrollTop: 0 }, "slow");
				$(".form-group").removeClass('has-success has-error');
			}
			else
			{
				show_feedback('error',response.message,<?php echo json_encode(lang('common_error')); ?>);
				$("html, body").animate({ scrollTop: 0 }, "slow");
				$(".form-group").removeClass('has-success has-error');
			}
		},
	<?php if(!$person_info->person_id) { ?>
	resetForm: true,
	<?php } ?>
	dataType:'json'
	});
}
</script>
<?php $this->load->view("partial/footer"); ?>
