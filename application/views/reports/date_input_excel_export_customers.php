<?php $this->load->view("partial/header"); ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('reports_date_range'); ?>
			</div>
			<div class="panel-body">
				<?php
				if(isset($error))
				{
					echo "<div class='error_message'>".$error."</div>";
				}
				?>
				<form  class="form-horizontal form-horizontal-mobiles">
					<div class="form-group">
						<?php echo form_label(lang('reports_fixed_range').':', 'simple_radio',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label   ')); ?>

						<div class="col-sm-9 col-md-2 col-lg-2">
							<input type="radio" name="report_type" id="simple_radio" value='simple' checked='checked'/>
							<label for="simple_radio"><span></span></label>
							<?php echo form_dropdown('report_date_range_simple',$report_date_range_simple, '', 'id="report_date_range_simple" class="form-control"'); ?>
						</div>
					</div>

					<div id='report_date_range_complex'>
						<div class="form-group">
							<?php echo form_label(lang('reports_custom_range').':', 'complex_radio',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>

							<div class="col-sm-9 col-md-6 col-lg-6">
								<input type="radio" name="report_type" id="complex_radio" value='complex' />
								<label for="complex_radio"><span></span></label>
								<div class="row">
									<div class="col-md-6">
										<div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           <?php echo lang('reports_from'); ?>
					                       	</span>
		                                    <input type="text" class="form-control start_date" name="start_date" id="start_date">
		                                </div>
									</div>
									<div class="col-md-6">
										<div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    <?php echo lang('reports_to'); ?>
			                                </span>
		                                    <input type="text" class="form-control end_date" name="end_date" id="end_date">
		                                </div>	
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_sale_type').':', 'sale_type', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php echo form_dropdown('sale_type',array('all' => lang('reports_all'), 'sales' => lang('reports_sales'), 'returns' => lang('reports_returns')), 'all', 'id="sale_type" class="form-control"'); ?>
						</div>
					</div>
					
					<div class="form-group">
						<?php echo form_label(lang('reports_total_spent').':', 'sale_type', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">							
							<?php echo form_dropdown('total_spent_condition',array('any' => lang('reports_any_amount'), 'greater_than' => lang('reports_sales_generator_selectCondition_7'), 'less_than' => lang('reports_sales_generator_selectCondition_8'), 'equal_to' => lang('reports_sales_generator_selectCondition_9')), 'any', 'id="total_spent_condition" class="form-control"'); ?>
						</div>
					</div>
					
					<div class="form-group" style="display: none;" id="total_spent_amount_container">
							<?php echo form_label(lang('common_amount').':', 'total_spent_amount', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
							<div class="col-sm-9 col-md-2 col-lg-2">							
								<input type="text" class="form-control total_spent_amount" name="total_spent_amount" id="total_spent_amount">
							</div>
					</div>					

					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?>  &nbsp;
							<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> &nbsp;
							<label for="export_excel_no"><span></span></label>
						</div>
					</div>
					
					<?php $this->load->view('partial/reports/locations_select');?>

					<div class="form-actions pull-right">
						<?php
						echo form_button(array(
							'name'=>'generate_report',
							'id'=>'generate_report',
							'content'=>lang('common_submit'),
							'class'=>'btn btn-primary submit_button btn-large')
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
		$("#total_spent_condition").change(function()
		{
			if ($(this).val() != 'any')
			{
				$("#total_spent_amount_container").show();
			}
			else
			{
				$("#total_spent_amount_container").hide();				
			}
		});
		
		$("#generate_report").click(function()
		{
			var sale_type = $("#sale_type").val();
			var export_excel = 0;
			var total_spent_condition = $("#total_spent_condition").val();
			var total_spent_amount = $("#total_spent_amount").val() ? parseFloat($("#total_spent_amount").val()) : 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}

			if ($("#simple_radio").prop('checked'))
			{
				window.location = window.location+'/'+$("#report_date_range_simple option:selected").val() + '/'+sale_type+'/'+total_spent_condition+'/'+total_spent_amount+'/'+export_excel;
			}
			else
			{
				var start_date = $("#start_date").val();
				var end_date = $("#end_date").val();

				window.location = window.location+'/'+start_date + '/'+ end_date + '/'+sale_type+'/'+total_spent_condition+'/'+total_spent_amount+'/'+export_excel;
			}
		});

		$("#start_date").click(function(){
			$("#complex_radio").prop('checked', true);
		}); 
		$("#end_date").click(function(){
			$("#complex_radio").prop('checked', true);
		});    
        
		$("#report_date_range_simple").change(function()
		{
			$("#simple_radio").prop('checked', true);
		});

     date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
     date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
	});
</script>
<?php $this->load->view("partial/footer"); ?>