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
						<?php echo form_label($specific_input_name.':', 'specific_input_data', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-2 col-lg-2" id='controls'>								
							<?php echo form_input(array(
								'name'=>'specific_input_data',
								'id'=>'specific_input_data',
								'size'=>'10',
								'value'=>''));
								?>									
						</div>
					</div>

					<div class="form-group">
						<?php echo form_label(lang('common_giftcards_giftcard_number').':', 'giftcard_number_data', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10" id='giftcard_number'>								
							<?php echo form_input(array(
								'name'=>'giftcard_number_data',
								'id'=>'giftcard_number_data',
								'class'=>'form-control',
								'size'=>'20',
								'value'=>''));
							?>									
						</div>
					</div>
					

					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').':', '', array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?>
							&nbsp;<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?>
							&nbsp;<label for="export_excel_no"><span></span></label>
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
		<?php if (!$this->config->item('disable_giftcard_detection')) { ?>
			giftcard_swipe_field($('#giftcard_number_data'));
		<?php } ?>
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

		$("#generate_report").click(function()
		{
			var specific_id = $("#specific_input_data").val() ? $("#specific_input_data").val() : -1;
			var giftcard_number = $("#giftcard_number_data").val() ? $("#giftcard_number_data").val() : -1;
			var export_excel = 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}
			window.location = window.location+'/'+ specific_id + '/'+ giftcard_number + '/'+ export_excel;
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>