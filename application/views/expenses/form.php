<?php $this->load->view("partial/header"); ?>

<div class="row" id="form">
	
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
		 <?php echo form_open('expenses/save/'.$expense_info->id,array('id'=>'expenses_form','class'=>'form-horizontal')); ?>
		<div class="panel panel-piluku">
			<div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ion-edit"></i> <?php if(!$expense_info->id) { echo lang('expenses_new'); } else { echo lang('expenses_update'); } ?>
								<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
						 
            </div>
			<div class="panel-body">
			<h5><?php echo lang("expenses_basic_information"); ?></h5>
				
				<div class="form-group p-lr-15">
					<?php echo form_label(lang('expenses_date').':', 'expenses_date_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				  	<div class="input-group date">
				    	<span class="input-group-addon"><i class="ion-calendar"></i></span>
				    	<?php echo form_input(array(
				      		'name'=>'expenses_date',
							'id'=>'expenses_date_input',
							'class'=>'form-control form-inps datepicker',
							'value'=>$expense_info->expense_date ? date(get_date_format(), strtotime($expense_info->expense_date)) : date(get_date_format()))
				    	);?> 
				    </div>  
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('expenses_amount').':', 'expenses_amount_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'expenses_amount',
							'id'=>'expenses_amount_input',
							'value'=>$expense_info->expense_amount? to_currency_no_money($expense_info->expense_amount) : '')
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_tax').':', 'expenses_tax_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'expenses_tax',
							'id'=>'expenses_tax_input',
							'value'=>$expense_info->expense_tax? to_currency_no_money($expense_info->expense_tax) : to_currency_no_money(0))
						);?>
					</div>
				</div>
				
				
				<div class="form-group">
				<?php echo form_label(lang('expenses_description').':', 'expenses_description_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
				<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
					<?php echo form_input(array(
						'class'=>'form-control form-inps',
						'name'=>'expenses_description',
						'id'=>'expenses_description_input',
						'value'=>$expense_info->expense_description)
					);?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_type').':', 'expenses_type_input', array('class'=>'required col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'expenses_type',
							'id'=>'expenses_type_input',
							'value'=>$expense_info->expense_type)
						);?>
					</div>
				</div>
				
				
				<div class="form-group">
					<?php echo form_label(lang('common_reason').':', 'expenses_reason_input', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'expense_reason',
							'id'=>'expenses_reason_input',
							'value'=>$expense_info->expense_reason)
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_category').':', 'category_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('category_id', $categories,$expense_info->category_id, 'class="form-control form-inps" id ="category_id"');?>
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
							<div>
								<?php echo anchor("items/categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
							</div>
							<?php } ?>		
					</div>
				</div>
			
				<div class="form-group">
					<?php echo form_label(lang('expenses_recipient_name').':', 'employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_dropdown('employee_id',$employees, $expense_info->employee_id ? $expense_info->employee_id : $logged_in_employee_id , 'id="employee_id" class=""'); ?>
					</div>
				</div>


				<div class="form-group">
					<?php echo form_label(lang('common_approved_by').':', 'approved_employee_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_dropdown('approved_employee_id',$employees, $expense_info->approved_employee_id ? $expense_info->approved_employee_id : $logged_in_employee_id , 'id="approved_employee_id" class=""'); ?>
					</div>
				</div>
    
				<div class="form-group">
					<?php echo form_label(lang('common_expenses_note').':', 'expenses_note_input', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
						<?php echo form_textarea(array(
							'class'=>'form-control text-area',
							'name'=>'expenses_note',
							'id'=>'expenses_note_input',
							'rows'=>'5',
							'cols'=>'17',
							'value'=>$expense_info->expense_note)
						);?>
					</div>
				</div>
				
				<?php
				//Only allow removal from register for NEW expenses
				if ($this->config->item('track_cash') && !$expense_info->id)
				{
				?>	
					<div class="row">
					<div class="form-group">
					<?php echo form_label(lang('common_remove_cash_from_register').':', 'cash_register_id', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10 cmp-inps">
							<?php echo form_dropdown('cash_register_id', $registers, '' , 'id="cash_register_id" class=""'); ?>
						</div>
					</div>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo anchor(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
					</div>
					
					</div>
				<?php } ?>


				<?php echo form_hidden('redirect', $redirect_code); ?>

<div class="form-actions pull-right">
<?php
echo form_submit(array(
	'name'=>'submitf',
	'id'=>'submitf',
	'value'=>lang('common_submit'),
	'class'=>'btn btn-primary submit_button floating-button btn-large')
	);
	?>

	
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
</div>

<script type='text/javascript'>
var submitting = false;
//validation and submit handling
$(document).ready(function()
{
	$('#category_id').selectize({
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		}
	});
	        	
        $('#expenses_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
$('#grid-loader').show();
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			success:function(response)
			{
$('#grid-loader').hide();
				submitting = false;
				
				show_feedback(response.success ? 'success' : 'error',response.message, response.success ? <?php echo json_encode(lang('common_success')); ?>  : <?php echo json_encode(lang('common_error')); ?>);
				
				if(response.redirect==1 && response.success)
				{ 
					$.post('<?php echo site_url("expenses");?>', {expense: response.id}, function()
					{
						window.location.href = '<?php echo site_url('expenses'); ?>'
					});					
				}
				if(response.redirect==2 && response.success)
				{ 
					window.location.href = '<?php echo site_url('expenses'); ?>'
				}

			},
			
			<?php if(!$expense_info->id) { ?>
			resetForm: true,
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
        expenses_type: "required",
        expenses_description: "required",
        expenses_date: "required",
		expenses_amount: {
			required:true,
			number:true				
		},
		expenses_tax:"number",
        expenses_recipient_name: "required",
        category_id: "required"
		},
		messages: 
		{
     		expenses_type: <?php echo json_encode(lang('expenses_type_required')); ?>,
     		expenses_description: <?php echo json_encode(lang('expenses_description_required')); ?>,
     		expenses_date: <?php echo json_encode(lang('expenses_date_required')); ?>,
     		expenses_amount: 
			{
				required: <?php echo json_encode(lang('expenses_amount_required')); ?>,
				number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
			},
			expenses_tax: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>,
     		expenses_recipient_name: <?php echo json_encode(lang('expenses_recipient_name_required')); ?>,
     		category_id: <?php echo json_encode(lang('common_category_required')); ?>
		}
	});
});

date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);

$("#employee_id").select2();
$("#approved_employee_id").select2();
$("#cash_register_id").select2();
</script>
<?php $this->load->view('partial/footer')?>
