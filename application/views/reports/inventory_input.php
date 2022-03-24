<?php $this->load->view("partial/header"); ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('reports_report_input'); ?>
			</div>
			<div class="panel-body">
				<?php
				if(isset($error))
				{
					echo "<div class='error_message'>".$error."</div>";
				}
				?>
				<form class="form-horizontal form-horizontal-mobiles">
					<div class="form-group">
						<?php echo form_label($specific_input_name.':', 'specific_input_data', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php echo form_dropdown('specific_input_data',$specific_input_data, '', 'id="specific_input_data" class="input-medium"'); ?>
						</div>
					</div>
					
					<div class="form-group">
						<?php echo form_label(lang('common_category').':', 'category_data', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
						<?php echo form_dropdown('category_data', $categories,'', 'class="form-control form-inps" id ="category_data"');?>
						</div>
					</div>
					
					<div class="form-group">
						<?php echo form_label(lang('common_inventory').':', 'inventory', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
						<?php echo form_dropdown('inventory_data', array(
						'all' => lang('common_all'),
						'in_stock' => lang('reports_in_stock'),
						'out_of_stock' => lang('reports_out_of_stock'),
						),'', 'class="form-control form-inps" id ="inventory"');?>
						</div>
					</div>
					
					<?php
					if ($this->uri->segment(2) == 'inventory_low')
					{						
						?>
						<div class="form-group">	
							<?php echo form_label(lang('reports_show_only_reorder').':', 'reports_show_only_reorder',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'reports_show_only_reorder',
									'id'=>'reports_show_only_reorder',
									'value'=>'1',
									));?>
								<label for="reports_show_only_reorder"><span></span></label>
							</div>
						</div>
					<?php
					}
					?>
					
					<?php
					if ($this->uri->segment(2) == 'inventory_summary')
					{						
						?>
						
						<div id='report_date_range_complex'>
							<div class="form-group">
								<?php echo form_label(lang('reports_day').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
								<div class="col-md-3 col-sm-3">
									<div class="input-group input-daterange" id="reportrange">
	                                    <span class="input-group-addon bg">
				                           <?php echo lang('reports_from'); ?>
				                       	</span>
	                                    <input type="text" class="form-control start_date" name="start_date" id="start_date">
	                                </div>
								</div>
							</div>
						
						<div class="form-group">	
							<?php echo form_label(lang('reports_show_pending_only').':', 'reports_show_pending_only',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'reports_show_pending_only',
									'id'=>'reports_show_pending_only',
									'value'=>'1',
									));?>
								<label for="reports_show_pending_only"><span></span></label>
							</div>
						</div>
						
						<div class="form-group">	
							<?php echo form_label(lang('reports_show_deleted_items').':', 'reports_show_deleted_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'reports_show_deleted_items',
									'id'=>'reports_show_deleted_items',
									'value'=>'1',
									));?>
								<label for="reports_show_deleted_items"><span></span></label>
							</div>
						</div>
						
					<?php
					}
					
					$this->load->view('partial/reports/locations_select');
					?>
					
					
					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' />  <?php echo lang('common_yes'); ?> 
							<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> 
							<label for="export_excel_no"><span></span></label>
						</div>
					</div>
										
					<div class="form-actions pull-right">
						<?php
						echo form_button(array(
							'name'=>'generate_report',
							'id'=>'generate_report',
							'content'=>lang('common_submit'),
							'class'=>'btn btn-primary submit_button')
						);
						?>
					</div>
				</form>
			</div>	
		</div>
	</div>
</div>
</div>			

<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
		
		$("#specific_input_data").selectize();
		$("#generate_report").click(function()
		{
			var export_excel = 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}
			
			var category_id = encodeURIComponent($('#category_data').val());
			var inventory = encodeURIComponent($('#inventory').val());
			
			<?php
			if ($this->uri->segment(2) == 'inventory_low')
			{
			?>
				var show_only_reorder = encodeURIComponent($('#reports_show_only_reorder').prop('checked') ? '1' : '0');
				window.location = window.location+'/'+$('#specific_input_data').val() + '/' + category_id + '/' + inventory +'/' + show_only_reorder + '/' + export_excel;
			
			<?php
			}
			elseif($this->uri->segment(2) == 'inventory_summary')
			{
			?>
				var start_date = $("#start_date").val();			
				var show_only_pending = encodeURIComponent($('#reports_show_pending_only').prop('checked') ? '1' : '0');
				var reports_show_deleted_items = encodeURIComponent($('#reports_show_deleted_items').prop('checked') ? '1' : '0');
				window.location = window.location+'/'+start_date + '/' + $('#specific_input_data').val() + '/' + category_id + '/' + inventory + '/' + show_only_pending + '/' + reports_show_deleted_items+ '/' + export_excel;				
			<?php
			}
			?>
		});	
	});
</script>
<?php $this->load->view("partial/footer"); ?>