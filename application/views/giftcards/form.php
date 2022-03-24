<?php $this->load->view("partial/header"); ?>
<div class="row">
	<div class="col-md-12">

				<?php echo form_open('giftcards/save/'.(!isset($is_clone) ? $giftcard_info->giftcard_id : ''),array('id'=>'giftcard_form','class'=>'form-horizontal')); ?>
			<div class="panel panel-piluku">
				<div class="panel-heading">
	                <h3 class="panel-title">
	                    <i class="ion-edit"></i> 
	                    <?php echo lang("common_giftcards_basic_information"); ?>
    					<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
		        </div>

			<div class="panel-body">
					<div class="form-group">	
						<?php echo form_label(lang('common_giftcards_giftcard_number').':', 'giftcard_number',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'name'=>'giftcard_number',
								'size'=>'8',
								'id'=>'giftcard_number',
								'class'=>'form-control form-inps',
								'value'=>$giftcard_info->giftcard_number)
								);?>
						</div>
					</div>


						<div class="form-group">	
						<?php echo form_label(lang('common_description').':', 'description',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_textarea(array(
								'name'=>'description',
								'id'=>'description',
								'class'=>'form-control text-area',
								'rows'=>'4',
								'cols'=>'30',
								'value'=>$giftcard_info->description));?>
							</div>
						</div>

				<?php if ($this->Employee->has_module_action_permission('giftcards','edit_giftcard_value', $this->Employee->get_logged_in_employee_info()->person_id)  || $giftcard_id == -1) { ?>

					<div class="form-group">	
						<?php echo form_label(lang('common_giftcards_card_value').':', 'value',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
							'name'=>'value',
							'size'=>'8',
							'class'=>'form-control form-inps ',
							'id'=>'value',
							'value'=>$giftcard_info->value ? to_currency_no_money($giftcard_info->value, 10) : '')
							);?>
						</div>
					</div>
					
					<?php } else { ?>
						
						<div class="form-group">	
							<?php echo form_label(lang('common_giftcards_card_value').':', '',array('class'=>'required wide col-sm-3 col-md-3 col-lg-2 control-label required wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<h5><?php echo $giftcard_info->value ? to_currency_no_money($giftcard_info->value, 10) : ''; ?></h5>
							</div>
						</div>
					
					<?php	
						echo form_hidden('value', $giftcard_info->value);
					}
					?> 
					<div class="form-group">	
						<?php echo form_label(lang('common_customer_name').':', 'choose_customer',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
                            
						<input type="text" name="choose_customer" id="choose_customer" class="form-control" value="<?php echo $giftcard_info->customer_id ? $selected_customer_name : ''; ?>">
						
						<input type="hidden" id="customer_id" name="customer_id" class="form-control" value="<?php echo $giftcard_info->customer_id ? $giftcard_info->customer_id : ''; ?>">

						</div>
					</div>
					
					<div class="form-group">	
						<?php echo form_label(lang('common_inactive').':', 'inactive',array('class'=>'wide col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'inactive',
							'id'=>'inactive',
							'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($giftcard_info->inactive ? 1 : 0)
						));?>
						<label for="inactive"><span></span></label>
						

						</div>
					</div>
					
					<?php if(!isset($is_clone)) { ?>
						
						<h5><?php echo lang('giftcards_log')?>:</h5>
						<div id="giftcard_log">
							<?php echo $giftcard_logs; ?>
						</div>
					<?php } ?>
						
					<?php echo form_hidden('redirect', $redirect); ?>
				
					<div class="form-actions pull-right">
						<?php echo form_submit(array(
						'name'=>'submit',
						'id'=>'submit',
						'value'=>lang('common_submit'),
						'class'=>'btn floating-button btn-primary')
						); ?>	
					</div>
			</div>
		</div>
			<?php echo form_close(); ?>
	</div>
</div>
</div>

<script type='text/javascript'>
				
<?php if (!$this->config->item('disable_giftcard_detection')) { ?>
	giftcard_swipe_field($('#giftcard_number'));
<?php
}
?>			
	//validation and submit handling
	$(document).ready(function()
	{
			$( "#choose_customer" ).autocomplete({
		 		source: '<?php echo site_url("giftcards/suggest_customer");?>',
				delay: 150,
		 		autoFocus: false,
		 		minLength: 0,
		 		select: function( event, ui ) 
		 		{
					event.preventDefault();
					$("#choose_customer").val(ui.item.label);
					$("#customer_id").val(ui.item.value);
		 		}
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
	     
	    setTimeout(function(){$(":input:visible:first","#giftcard_form").focus();},100);
		var submitting = false;
		$('#giftcard_form').validate({
			submitHandler:function(form)
			{
        if(!$("#choose_customer").val())
        {
						$("#customer_id").val("");
        }
				
				$('#grid-loader').show();
				if (submitting) return;
				submitting = true;
				$(form).ajaxSubmit({
				success:function(response)
				{
$('#grid-loader').hide();
					submitting = false;
					show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
					
					
					if(response.redirect==2 && response.success)
					{
						window.location.href = '<?php echo site_url('giftcards'); ?>';
					}
					else
					{
						$("html, body").animate({ scrollTop: 0 }, "slow");
						$(".form-group").removeClass('has-success has-error');
					}
				},
				<?php if(!$giftcard_info->giftcard_id) { ?>
				resetForm:true,
				<?php } ?>
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
				giftcard_number:
				{
					<?php if(!$giftcard_info->giftcard_id) { ?>
					remote: 
					    { 
						url: "<?php echo site_url('giftcards/giftcard_exists');?>", 
						type: "post"
		
					    }, 
					<?php } ?>
					required:true
	
				},
				value:
				{
					required:true,
					number:true
				}
	   		},
			messages:
			{
				giftcard_number:
				{
					<?php if(!$giftcard_info->giftcard_id) { ?>
					remote:<?php echo json_encode(lang('common_giftcards_exists')); ?>,
					<?php } ?>
					required:<?php echo json_encode(lang('common_giftcards_number_required')); ?>,

				},
				value:
				{
					required:<?php echo json_encode(lang('common_giftcards_value_required')); ?>,
					number:<?php echo json_encode(lang('common_giftcards_value')); ?>
				}
			}
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>