<?php $this->load->view("partial/header"); ?>
<div class="row" id="form">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('common_custom_field_config'); ?>
			</div>
			
			<div class="panel-body">
				<div class="row form-group">
				   <div class="col-xs-12">
						 
						 
						 
						 <?php echo form_open($controller_name.'/save_custom_fields',array('id'=>'save_custom_fields','class'=>'form-horizontal')); ?>
						 
						 <?php for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++) { ?>
							 	    
								<div class="panel panel-piluku">
								  <div class="panel-heading">
								    <h3 class="panel-title"><?php echo lang('common_custom_field') . ' ' . $k ?></h3>
								  </div>
								  <div class="panel-body">
								   
							 
 							<div class="form-group">
 								<?php echo form_label(lang("common_name").' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
 								<div class="col-sm-9 col-md-9 col-lg-10">
 									<?php echo form_input(array(
 										'name'=>"custom_field_${k}_name",
										'class '=>'form-control form-inps',
 										'value'=> isset(${"custom_field_${k}_name"}) ? ${"custom_field_${k}_name"} : '',
 										)
 									);?>
 								</div>
 							</div>
							
 							<div class="form-group">
 								<?php echo form_label(lang("common_type").' :', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
 								<div class="col-sm-9 col-md-9 col-lg-10">
									
			 						<?php echo form_dropdown("custom_field_${k}_type", array(
		 							'text'    => lang('common_text'),
		 							'dropdown'    => lang('common_dropdown'),
									'checkbox'    => lang('common_checkbox'),
		 							'email'    => lang('common_email'),
		 							'url'    => lang('common_website'),
		 							'phone'    => lang('common_phone_number'),
		 							'date'    => lang('common_date'),
									),
			 							isset(${"custom_field_${k}_type"}) ? ${"custom_field_${k}_type"} : '' , 'class="form-control field_type"');
			 							?>
										
									
 									<?php echo form_input(array(
 										'name'=>"custom_field_${k}_choices",
										'class '=>'form-control form-inps choices '.(empty(${"custom_field_${k}_type"}) || ${"custom_field_${k}_type"} != 'dropdown' ? 'hidden' : ''),
 										'value'=> isset(${"custom_field_${k}_choices"}) ? ${"custom_field_${k}_choices"} : '',
 										)
 									);?>

 								</div>
 							</div>
							
						   </div>
						 </div>
						 
						 <?php } ?>
 						<div class="form-actions">
 						<?php echo form_submit(array(
 							'name'=>'submitf',
 							'id'=>'submitf',
 							'value'=>lang('common_submit'),
 							'class'=>'submit_button btn btn-primary btn-lg pull-right')); ?>
 						</div>			
						 
						 <?php
						 echo form_close();
						 ?>
						 
					</div>
				</div>
			</div>
	</div>
 </div>
</div>

<script type='text/javascript'>
	$('.choices').selectize({
		delimiter: '|',
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_add_value')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		},
	});
	
	$(".field_type").change(function()
	{
		if ($(this).val() == 'dropdown')
		{
			$(this).parent().find('.choices').removeClass('hidden');
		}
		else
		{
			$(this).parent().find('.choices').addClass('hidden');			
		}
		
	});
	$("#save_custom_fields").ajaxForm({success: function()
	{
		show_feedback('success',<?php echo json_encode(lang('common_saved_successfully')); ?>,<?php echo json_encode(lang('common_success')); ?>);
		setTimeout(function(){
			window.location = '<?php echo site_url($controller_name.'/'); ?>';
		}, 1000);
	}});
</script>
<?php $this->load->view('partial/footer'); ?>