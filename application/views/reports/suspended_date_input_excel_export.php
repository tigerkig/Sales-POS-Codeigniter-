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

					<div class="form-group">
						<?php echo form_label($specific_input_name.':', 'specific_input_data', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							
							<?php if (isset($search_suggestion_url)) {?>
							<?php echo form_input(array(
								'name'=>'specific_input_data',
								'id'=>'specific_input_data',
								'size'=>'10',
								'value'=>''));
								?>									
								<?php } else { ?>
								<?php echo form_dropdown('specific_input_data',$specific_input_data, '', 'id="specific_input_data" class="input-medium"'); ?>
								<?php } ?>
							</div>
						</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_sale_type').':', 'sale_type', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php echo form_dropdown('sale_type',array('all' => lang('reports_all_open_layaways_and_estimates'), 'layaway' => ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway')), 'completed_layaway'  => lang('reports_completed_layaway'), 'estimate' => lang('common_estimate'),'completed_estimate'  => lang('reports_completed_estimate')), 'all', 'id="sale_type" class="form-control"'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?> 
							<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> 
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
		
		<?php
		if (isset($search_suggestion_url))
		{
			?>
			$("#specific_input_data").select2(
			{
				placeholder: <?php echo json_encode(lang('common_search')); ?>,
				id: function(suggestion){ return suggestion.value; },
				ajax: {
					url: <?php echo json_encode($search_suggestion_url); ?>,
					dataType: 'json',
					data: function(term, page) 
					{
						return {
							'term': term
						};
					},
					results: function(data, page) {
						return {results: data};
					}
				},
				formatSelection: function(suggestion) {
					return suggestion.label;
				},
				formatResult: function(suggestion) {
					return suggestion.label;
				}
			});
			<?php
		}
		else
		{
			?>
			$("#specific_input_data").select2();		
			<?php
		}
		?>
		
		$("#generate_report").click(function()
		{
			var sale_type = $("#sale_type").val();
			var export_excel = 0;
			var customer_id = $("#specific_input_data").val() ? $("#specific_input_data").val() : -1;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}

			if ($("#simple_radio").prop('checked'))
			{
				window.location = window.location+'/'+$("#report_date_range_simple option:selected").val() + '/'+customer_id+'/'+sale_type+'/'+export_excel;
			}
			else
			{
				var start_date = $("#start_date").val();
				var end_date = $("#end_date").val();

				window.location = window.location+'/'+start_date + '/'+ end_date + '/'+customer_id+'/'+sale_type+'/'+ export_excel;
			}
		});

		$("#start_date").click(function(){
			$("#complex_radio").prop('checked', true);
		}); 
		$("#end_date").click(function(){
			$("#complex_radio").prop('checked', true);
		});    
        

        date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);
        date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT+ " "+JS_TIME_FORMAT);

		$("#report_date_range_simple").change(function()
		{
			$("#simple_radio").prop('checked', true);
		});

	});
</script>
<?php $this->load->view("partial/footer"); ?>