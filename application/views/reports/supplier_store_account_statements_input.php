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
				<form class="form-horizontal form-horizontal-mobiles">

					<div class="form-group">
						<?php echo form_label(lang('common_supplier').':', 'supplier_input', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label   ')); ?> 
						<div class="col-sm-9 col-md-2 col-lg-2">
							<?php echo form_input(array(
								'name'=>'supplier_input',
								'id'=>'supplier_input',
								'size'=>'10',
								'value'=>''));
								?>									
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('reports_date_range').':', 'simple_radio', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label   ')); ?>
						<div class="col-sm-9 col-md-2 col-lg-2">
							<input type="radio" name="report_type" id="simple_radio" value='simple' checked='checked'/>
							<label for="simple_radio"><span></span></label>
							<?php echo form_dropdown('report_date_range_simple',$report_date_range_simple, '', 'id="report_date_range_simple" class="form-control"'); ?>
						</div>
					</div>

					<div id='report_date_range_complex'>
						<div class="form-group">
							<?php echo form_label(lang('reports_custom_range').':', 'complex_radio',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label   ')); ?>
							
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



						<div class="form-group">	
							<?php echo form_label(lang('reports_hide_items').':', 'hide_items',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_checkbox(array(
									'name'=>'hide_items',
									'id'=>'hide_items',
									'value'=>'hide_items',
									));?>
								<label for="hide_items"><span></span></label>
							</div>
						</div>

						<div class="form-group">	
							<?php echo form_label(lang('reports_pull_payments_by').':', 'pull_payments_by',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
							<div class="col-sm-9 col-md-2 col-lg-2">
								<?php echo form_dropdown('pull_payments_by',array('payment_date' => lang('reports_payment_date'), 'receiving_date' => lang('reports_receiving_date')), '', 'id="pull_payments_by" class="form-control"'); ?>
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
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

	
		

<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		$("#supplier_input").select2(
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
					data.unshift({label:<?php echo json_encode('--'.lang('common_all').'--'); ?>, value: -1});
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

		$("#generate_report").click(function()
		{
			var supplier_id = $("#supplier_input").val() ? $("#supplier_input").val() : -1;
			var hide_items = $("#hide_items").prop('checked') ? 1 : 0;

			var start_date = $("#start_date").val();
			var end_date = $("#end_date").val();
			var pull_payments_by = $("#pull_payments_by").val();

			if ($("#simple_radio").prop('checked'))
			{
				window.location = window.location+'/'+supplier_id+'/'+$("#report_date_range_simple option:selected").val()+ '/'+hide_items + '/'+pull_payments_by;
			}
			else
			{
				var start_date = $("#start_date").val();
				var end_date = $("#end_date").val();

				window.location = window.location+'/'+supplier_id+'/'+start_date+'/'+end_date+'/'+hide_items + '/'+pull_payments_by;
			}

		});

		$("#start_date").click(function(){
			$("#complex_radio").prop('checked', true);
		}); 
		$("#end_date").click(function(){
			$("#complex_radio").prop('checked', true);
		});    
        

        date_time_picker_field_report($('#start_date'), JS_DATE_FORMAT);
        date_time_picker_field_report($('#end_date'), JS_DATE_FORMAT);

		$("#report_date_range_simple").change(function()
		{
			$("#simple_radio").prop('checked', true);
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>