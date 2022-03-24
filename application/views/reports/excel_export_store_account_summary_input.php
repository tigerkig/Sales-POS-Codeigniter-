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
						<?php echo form_label(lang('reports_show_accounts_over_credit_limit').':', null, array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							
							<?php echo form_checkbox(array(
						    'name'        => 'show_accounts_over_credit_limit',
						    'id'          => 'show_accounts_over_credit_limit',
						    'value'       => '1'
					    	)).'<label for="show_accounts_over_credit_limit"><span></span></label>'; ?>
							
						</div>
					</div>
					
					<div class="form-group">
						<?php echo form_label(lang('reports_export_to_excel').':', null, array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?> 
						<div class="col-sm-9 col-md-9 col-lg-10">
							<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> <?php echo lang('common_yes'); ?> &nbsp;
							<label for="export_excel_yes"><span></span></label>
							<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> <?php echo lang('common_no'); ?> &nbsp;
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


<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		$("#generate_report").click(function()
		{
			var export_excel = 0;
			if ($("#export_excel_yes").prop('checked'))
			{
				export_excel = 1;
			}
			
			var show_accounts_over_credit_limit = 0;
			if ($("#show_accounts_over_credit_limit").prop('checked'))
			{
				show_accounts_over_credit_limit = 1;
			}
			

			window.location = window.location+'/' + show_accounts_over_credit_limit + '/' + export_excel;
		});	
	});
</script>
<?php $this->load->view("partial/footer"); ?>