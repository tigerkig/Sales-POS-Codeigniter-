<?php $this->load->view("partial/header"); ?>
<style scoped>
	a
	{
		text-decoration: none !important;
	}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('sales_opening_amount_desc'); ?>
			</div>
			<div class="panel-body">
				
				<div class="col-md-6">
					<div class="table-responsive">
						<table class="table table-striped table-hover text-center opening_bal">
							<tr>
								<th><?php echo lang('common_denomination');?></th>
								<th><?php echo lang('common_count');?></th>
							</tr>

							<?php foreach($denominations as $denomination) { ?>
							<tr>
								<td><?php echo $denomination['name']; ?></td>
								<td>
									<div class="form-group">
										<?php echo form_input(array(
											'name'=>'denom_'.$denomination['id'],
											'id'=>'denom_'.$denomination['id'],
											'data-value' => $denomination['value'],
											'class'=> 'form-control denomination',
											)
										);?>
									</div>
								</td>
							</tr>
							<?php } ?>
						</table>
					</div>
				</div>

				<div class="col-md-6">
					<?php
					$reg_info = $this->Register->get_info($this->Employee->get_logged_in_employee_current_register_id());
					$reg_name =  '&nbsp;<span class="badge bg-primary">'.$reg_info->name.'&nbsp;(<small>'.lang('sales_change_register').'</small>)</span>';
					?>

					<?php echo form_open('sales', array('id'=>'opening_amount_form')); ?>

					<div class="form-group">
						
						<div class="from-group text-center">
							<?php echo lang('sales_previous_closing_amount');?>: <?php echo to_currency($previous_closing);?>
						</div>
						
                        <?php echo form_label(lang('common_opening_amount').':', 'opening_amount',array('class'=>'control-label col-sm-2')); ?>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <?php echo form_input(array(
								'name'=>'opening_amount',
								'id'=>'opening_amount',
								'class'=>'form-control',
								'value'=>'')
								);?>
								<span class="input-group-btn bg">
                                    <?php echo form_submit(array(
										'name'=>'submit',
										'id'=>'submit',
										'value'=>lang('common_submit'),
										'class'=>'btn btn-primary')
									);
									?>
                                </span>
                            </div>
                            <!-- /input-group -->
                        </div>
                    </div>


					<div class="from-group text-center">
						<h3><?php echo lang('common_or'); ?></h3>					
						<?php echo lang('common_register_name');?>: <?php echo anchor('sales/clear_register', $reg_name);?>
					</div>
					<br />
					<div class="from-group text-right">
						<?php echo anchor(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
					</div>
					<?php
					echo form_close();
					?>
				</div>
			</div>
		</div>
	</div>	
</div>

<script type='text/javascript'>

	//validation and submit handling
	$(document).ready(function()
	{
		$("#opening_amount").focus();
		
		var submitting = false;

		$('#opening_amount_form').validate({
			rules:
			{
				opening_amount: {
					required: true,
					number: true
				}
			},
			errorClass: "text-danger",
			errorElement: "span",
			highlight:function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
			},
			messages: {
				closing_amount: {
					required: <?php echo json_encode(lang('sales_amount_required')); ?>,
					number: <?php echo json_encode(lang('sales_amount_number')); ?>
				}
			}
		});
		
		function calculate_total()
		{
			var total = 0;
			
			$(".denomination").each(function( index ) 
			{
				if ($(this).val())
				{
					total+= $(this).data('value') * $(this).val();
				}
			});			
			
			$("#opening_amount").val(parseFloat(Math.round(total * 100) / 100).toFixed(<?php echo $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2; ?>));
		}
		
		$(".denomination").change(calculate_total);
		$(".denomination").keyup(calculate_total);

	});
</script>
<?php $this->load->view('partial/footer.php'); ?>