<?php $this->load->view("partial/header"); ?>
<div class="modal fade skip-labels" id="skip-labels" role="dialog" aria-labelledby="skipLabels" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
      	<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="skipLabels"><?php echo lang('common_skip_labels') ?></h4>
	        </div>
	        <div class="modal-body">
				
	          	<?php echo form_open("items/generate_barcodes", array('id'=>'generate_barcodes_form','autocomplete'=> 'off')); ?>				
				<input type="text" class="form-control text-center" name="num_labels_skip" id="num_labels_skip" placeholder="<?php echo lang('common_skip_labels') ?>">
					<?php echo form_submit('generate_barcodes_form',lang("common_submit"),'class="btn btn-block btn-primary"'); ?>
				<?php echo form_close(); ?>
				
	        </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php echo form_open('items/save_inventory/'.$item_info->item_id,array('id'=>'item_form','class'=>'form-horizontal')); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-piluku">
				<div class="panel-heading"><?php echo lang("items_basic_information"); ?></div>
				<div class="panel-body">
					
					<div class="form-group">
						<?php echo form_label(lang('common_item_number_expanded').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 form-text">
							<?php echo $item_info->item_number; ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_item_name').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 form-text">
							<?php echo $item_info->name ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_category').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 form-text">
							<?php echo $this->Item->get_category($item_info->category_id); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('items_current_quantity').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10 form-text">
							<?php echo to_quantity($item_location_info->quantity) ?>
						</div>
					</div>
					
					<?php if ($this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
						<div class="form-group hidden-print">
							<?php echo form_label(lang('items_add_minus').':', 'newquantity',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
								'name'=>'newquantity',
								'id'=>'newquantity',
								'class'=>'form-control'
									)
								);?>
							</div>
						</div>

						<div class="form-group hidden-print">
							<?php echo form_label(lang('common_items_inventory_comments').':', 'trans_comment',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_textarea(array(
								'name'=>'trans_comment',
								'id'=>'trans_comment',
								'class'=>'form-control text-area',
								'rows'=>'3',
								'cols'=>'17')		
								);?>
							</div>
						</div>

						<?php
							echo form_submit(array(
							'name'=>'submit',
							'id'=>'submit',
							'value'=>lang('common_submit'),
							'class'=>'btn btn-primary hidden-print pull-right')
							);
						?>
					<?php } ?>
				</div>
				
		<div class="row">
			<div class="col-md-12 text-center">
			   <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#barcodes_panel"><?php echo lang('common_generate_barcodes')?></button>
				<br />
				<br />
			</div>
		</div>
				<div id="barcodes_panel" class="collapse">
					<div class="form-group">
						<?php echo form_label(lang('items_number_of_barcodes').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php 
							$numbers = array();
							foreach(range(1, 50) as $number) 
							{ 
								$numbers[$number] = $number;
							
							}
							?> 
						
	 						<?php echo form_dropdown('items_number_of_barcodes', $numbers,
	 						1 , 'class="form-control" id="items_number_of_barcodes"');
							?>
						</div>
					</div>
				
				
				
					<div class="form-group">
						<?php echo form_label(lang('common_barcode_labels').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo 
								anchor("items/generate_barcode_labels",
								'<span class="">'.lang("common_barcode_labels").'</span>',
								array('id'=>'generate_barcode_labels', 
									'data-item-id' => $item_info->item_id,
									'class' => 'btn btn-primary btn-lg generate_barcodes',
									'target' => '_blank',
									'title'=>lang('common_barcode_labels'))); 
							?>
						</div>
					</div>
				
				
					<div class="form-group">
						<?php echo form_label(lang('common_barcode_sheet').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo 
								anchor("items/generate_barcodes",
								'<span class="">'.lang("common_barcode_sheet").'</span>',
								array('id'=>'generate_barcodes', 
									'class' => 'btn btn-primary btn-lg generate_barcodes',
									'data-item-id' => $item_info->item_id,
									'target' => '_blank',
									'title'=>lang('common_barcode_sheet'))); 
							?>
						
						</div>
					</div>
				</div>
				
			</div>
			<?php if ($pagination) { ?>
				<div class="pagination hidden-print alternate text-center" id="pagination_top" >
					<?php echo $pagination;?>
				</div>
			<?php } ?>
			<div class="panel">
				<div class="panel-body">
					<table class="table table-striped table-hover custom-table">
						<thead>
							<tr>
								<th><?php echo lang("items_inventory_tracking"); ?></th>
								<th><?php echo lang("common_employee"); ?></th>
								<th><?php echo lang("common_items_in_out_qty"); ?></th>
								<th><?php echo lang("items_remarks"); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($inventory_data as $row) { ?>
								<tr>
									<td><?php echo date(get_date_format(). ' '.get_time_format(), strtotime($row['trans_date']))?></td>
									<td>
										<?php
											$person_id = $row['trans_user'];
											$employee = $this->Employee->get_info($person_id);
											echo $employee->first_name." ".$employee->last_name;
										?>
									</td>
									<td><?php echo to_quantity($row['trans_inventory']);?></td>
									
									<?php
									$row['trans_comment'] = preg_replace('/'.$this->config->item('sale_prefix').' ([0-9]+)/', anchor('sales/receipt/$1', $row['trans_comment']), $row['trans_comment']);
																					$row['trans_comment'] = preg_replace('/RECV ([0-9]+)/', anchor('receivings/receipt/$1', $row['trans_comment']), $row['trans_comment']);
									?>
									<td><?php echo $row['trans_comment'];?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="text-center">
						<button class="btn btn-primary text-white hidden-print" id="print_button" > <?php echo lang('common_print'); ?> </button>	
					</div>
				</div>
			</div>
			<?php if ($pagination) { ?>
				<div class="pagination hidden-print alternate text-center" id="pagination_bottom" >
					<?php echo $pagination;?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php  echo form_close(); ?>
			
<script type='text/javascript'>
function print_inventory()
 {
 	window.print();
 }
//validation and submit handling
$(document).ready(function()
{	
	$("#generate_barcode_labels").click(function()
	{
		var barcodes = [];
		var number_of_barcodes = $("#items_number_of_barcodes").val();
		
		if (number_of_barcodes <= 50)
		{
			for(var k=0;k<number_of_barcodes;k++)
			{
				barcodes.push($(this).data('item-id'));
			}
		
			window.open($(this).attr('href')+"/"+barcodes.join("~"),'_blank');
		}
		
		return false;
	});
	
	
	$("#generate_barcodes").click(function()
	{
		$("#skip-labels").modal('show');
		return false;
	});
	
	$("#generate_barcodes_form").submit(function()
	{
		var barcodes = [];
		var number_of_barcodes = $("#items_number_of_barcodes").val();
		var num_labels_skip = $("#num_labels_skip").val() ? $("#num_labels_skip").val() : 0;
		
		if (number_of_barcodes <= 50)
		{
			for(var k=0;k<number_of_barcodes;k++)
			{
				barcodes.push($("#generate_barcodes").data('item-id'));
			}
		
			window.open($("#generate_barcodes").attr('href')+"/"+barcodes.join("~")+'/'+num_labels_skip,'_blank');
		}
		
		return false;		
	});
	
	
	$('#print_button').click(function(e){
		e.preventDefault();
		$('.content').addClass('no-margin');
		print_inventory();
		$('.content').removeClass('no-margin');
	});

	var submitting = false;
	$('#item_form').validate({
		submitHandler:function(form)
		{
			if (submitting) return;
			submitting = true;
			$(form).ajaxSubmit({
			success:function(response)
			{
					if(!response.success)
						{ 
							show_feedback('error', response.message, <?php echo json_encode(lang('common_error')); ?>);
						}
						else
						{
							show_feedback('success', response.message, <?php echo json_encode(lang('common_success')); ?>);
							setTimeout(function()
							{
								window.location.reload(true);								
							}, 1200);
						}
					submitting = false;
			},
			dataType:'json'
		});

		},
			errorClass: "help-inline",
			errorElement: "span",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').addClass('text-danger');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('text-danger');
				$(element).parents('.form-group').addClass('text-success');
			},
		rules: 
		{
			newquantity:
			{
				required:true,
				number:true
			}
   		},
		messages: 
		{
			
			newquantity:
			{
				required:<?php echo json_encode(lang('items_quantity_required')); ?>,
				number:<?php echo json_encode(lang('items_quantity_number')); ?>
			}
		}
	});
});
</script>
<?php $this->load->view('partial/footer'); ?>
