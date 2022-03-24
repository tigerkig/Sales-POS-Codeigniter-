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

							<div class="col-sm-9 col-md-9 col-lg-10">
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
					
					<div class="form-heading">
						<?php echo lang('reports_compare_to_date_range'); ?>&nbsp;&nbsp;
						<?php echo form_checkbox(array(
							'name'=>'compare_to',
							'id'=>'compare_to',
							'value'=>'compare_to',
							));?>
							<label for="compare_to"><span></span></label>
							<br /><br />
					</div>
					<div id="compare_to_holder" style='display: none;'>
						<div class="form-group">
							<?php echo form_label(lang('reports_fixed_range').':', 'simple_radio_compare',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label   ')); ?>

							<div class="col-sm-9 col-md-2 col-lg-2">
								<input type="radio" name="report_type_compare" id="simple_radio_compare" value='simple' checked='checked'/>
								<label for="simple_radio_compare"><span></span></label>
								<?php echo form_dropdown('report_date_range_simple_compare',$report_date_range_simple, '', 'id="report_date_range_simple_compare" class="form-control"'); ?>
							</div>
						</div>

						<div id='report_date_range_complex_compare'>
							<div class="form-group">
								<?php echo form_label(lang('reports_custom_range').':', 'complex_radio_compare',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>

								<div class="col-sm-9 col-md-9 col-lg-10">
									<input type="radio" name="report_type_compare" id="complex_radio_compare" value='complex' />
									<label for="complex_radio_compare"><span></span></label>
									<div class="row">
										<div class="col-md-6">
											<div class="input-group input-daterange" id="reportrange_compare">
			                                    <span class="input-group-addon bg">
						                           <?php echo lang('reports_from'); ?>
						                       	</span>
			                                    <input type="text" class="form-control start_date_compare" name="start_date_compare" id="start_date_compare">
			                                </div>
										</div>
										<div class="col-md-6">
											<div class="input-group input-daterange" id="reportrange1_compare">
			                                    <span class="input-group-addon bg">
				                                    <?php echo lang('reports_to'); ?>
				                                </span>
			                                    <input type="text" class="form-control end_date_compare" name="end_date_compare" id="end_date_compare">
			                                </div>	
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
						<?php echo form_label(lang('reports_time_interval').':', 'interval', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php echo form_dropdown('interval',$intervals, '7200', 'id="interval" class="form-control"'); ?>
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
		$("#generate_report").click(function()
		{
			
			
			
			
			var sale_type = $("#sale_type").val();
			var interval = $("#interval").val();
			
			var export_excel = 0;
			
			var do_compare = $("#compare_to").prop('checked') ? 1 : 0;
			var date_range_regular = '';
			var date_range_compare = '';
			
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}

			if ($("#simple_radio").prop('checked'))
			{
				date_range_regular = $("#report_date_range_simple option:selected").val();
			}
			else
			{
				var start_date = $("#start_date").val();
				var end_date = $("#end_date").val();
				date_range_regular = start_date + '/'+ end_date;				
			}
			
			if ($("#simple_radio_compare").prop('checked'))
			{
				date_range_compare = $("#report_date_range_simple_compare option:selected").val();
			}
			else
			{
				var start_date = $("#start_date_compare").val();
				var end_date = $("#end_date_compare").val();
				date_range_compare = start_date + '/'+ end_date;				
			}			
						
			window.location = window.location+'/'+date_range_regular + '/' + do_compare + '/' + date_range_compare + '/'+sale_type+'/' + interval + '/' + export_excel;
	
		});

		$("#start_date").click(function(){
			$("#complex_radio").prop('checked', true);
		}); 
		$("#end_date").click(function(){
			$("#complex_radio").prop('checked', true);
		});    
		
		$("#start_date_compare").click(function(){
			$("#complex_radio_compare").prop('checked', true);
		}); 
		$("#end_date_compare").click(function(){
			$("#complex_radio_compare").prop('checked', true);
		});  
		
		$("#compare_to").click(function()
		{
			if ($(this).prop('checked'))
			{
				$('#compare_to_holder').show();
			}
			else
			{
				$('#compare_to_holder').hide();				
			}
		});
		
        
		$("#report_date_range_simple").change(function()
		{
			$("#simple_radio").prop('checked', true);
		});
		
		$("#report_date_range_simple_compare").change(function()
		{
			$("#simple_radio_compare").prop('checked', true);
		});
		

     date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
     date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
     date_time_picker_field_report($('#start_date_compare'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
     date_time_picker_field_report($('#end_date_compare'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
	});
</script>
<?php $this->load->view("partial/footer"); ?>