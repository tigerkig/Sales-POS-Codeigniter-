<?php $this->load->view("partial/header"); ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('sales_closing_amount_desc'); ?>
			</div>
			<div class="panel-body">
				
								
					<div class="col-md-6">
						<div class="table-responsive">
							<table class="table table-striped text-center opening_bal">
							<tr>
								<th><?php echo lang('common_denomination');?></th>
								<th><?php echo lang('common_count');?></th>
							</tr>
							<?php foreach($denominations as $denomination) { ?>
								<tr>
									<td><?php echo $denomination['name']; ?></td>
									<td>
										<div class="form-group table-form-group">
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
						
						<ul class="text-error" id="error_message_box"></ul>
						
							<h3 class="text-right"><?php echo anchor("reports/register_log_details/$register_log_id", lang('common_det'), array('target' => '_blank')); ?></h3>


						<ul class="list-group close-amount">
						  <li class="list-group-item"><?php echo lang('common_open_amount'); ?>:  <span class="pull-right"><?php echo to_currency($open_amount); ?></span></li>
						  <li class="list-group-item"><?php echo lang('common_cash_sales'); ?>:  <span class="pull-right"><?php echo to_currency($cash_sales); ?></span></li>
						  <li class="list-group-item"><?php echo lang('common_total_cash_additions'); ?> [<?php echo anchor('sales/register_add_subtract/add/closeregister', lang('common_edit')); ?>]:  <span class="pull-right"><?php echo to_currency($total_cash_additions); ?> </span></li>
						  <li class="list-group-item"><?php echo lang('common_total_cash_subtractions'); ?> [<?php echo anchor('sales/register_add_subtract/subtract/closeregister', lang('common_edit')); ?>]:  <span class="pull-right"><?php echo to_currency($total_cash_subtractions); ?> </span></li>
						  <li class="list-group-item active"><?php echo sprintf(lang('sales_closing_amount_approx'), ''); ?> <span class="pull-right text-success total-amount"><?php echo to_currency($closeout); ?></span></li>
						
						</ul>
						

					
						<div class="col-md-12">
							
											<?php
											if(isset($update))
											{
											echo form_open('sales/edit_register/'.$register_log_id . $continue, array('id'=>'closing_amount_form','class'=>'form-horizontal'));
											}
											else
											{
												echo form_open('sales/closeregister' . $continue, array('id'=>'closing_amount_form','class'=>'form-horizontal'));	
											}
											?>
											
											<?php if(isset($open_amount_editable)) { ?>
												<div class="form-group controll-croups1">
												<?php echo form_label(lang('common_opening_amount').':', 'opening_amount',array('class'=>'control-label')); ?>
												<?php echo form_input(array(
													'name'=>'opening_amount',
													'id'=>'opening_amount',
													'class'=>'form-control',
													'value'=>$open_amount ? to_currency_no_money($open_amount): '')
													);?>
												</div>
										 <?php } ?>

											<div class="form-group controll-croups1">
											<?php echo form_label(lang('common_closing_amount').':', 'closing_amount',array('class'=>'control-label')); ?>
											<?php echo form_input(array(
												'name'=>'closing_amount',
												'id'=>'closing_amount',
												'class'=>'form-control',
												'value'=>$closeout ? to_currency_no_money($closeout): '')
												);?>
											</div>
											<div class="form-group controll-croups1">
											<?php echo form_label(lang('sales_notes').':', 'notes',array('class'=>'control-label')); ?>
											<?php echo form_textarea(array(
												'name'=>'notes',
												'id'=>'notes',
												'class'=>'form-control text-area',
												'value'=>$notes ? $notes: '')
												);?>
											</div>
											
											<div class="from-group text-right">
												<?php echo anchor(site_url('sales/open_drawer'), '<i class="ion-android-open"></i> '.lang('common_pop_open_cash_drawer'),array('class'=>'', 'target' => '_blank')); ?>
											</div>
											
											<br />
											
											<div class="form-group form-actions1">
												<input type="button" id="close_submit" class="btn btn-primary" value="<?php echo lang('common_submit'); ?>">
											</div>
											
											<?php if(!isset($update)) {  ?>
											<div style="text-align: center;">
												<h3><?php echo lang('common_or'); ?></h3>					
												<input type="button" id="logout_without_closing" class="btn btn-danger" value="<?php echo lang('sales_logout_without_closing_register'); ?>">
											</div>
											<?php }  ?>
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
$(document).ready(function(e)
{
	$("#closing_amount").focus();
	
	$("#closing_amount").keypress(function (e) {
	    if (e.keyCode == 13) {
	    	e.preventDefault();
	       	check_amount();
	    }
	 });

	$('#close_submit').click(function(){
		check_amount();
	});
	var submitting = false;

	$('#closing_amount_form').validate({
		rules:
		{
			closing_amount: {
				required: true,
				number: true
			}
		},
		messages:
		{
			closing_amount: {
				required: <?php echo json_encode(lang('sales_amount_required')); ?>,
				number: <?php echo json_encode(lang('sales_amount_number')); ?>
			}
		}
	});
	
	$("#logout_without_closing").click(function()
	{
		window.location = '<?php echo site_url('home/logout'); ?>';
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
		
		$("#closing_amount").val(parseFloat(Math.round(total * 100) / 100).toFixed(<?php echo $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2; ?>));
	}
	
	$(".denomination").change(calculate_total);
	$(".denomination").keyup(calculate_total);
});
function check_amount()
{

	if($('#closing_amount').val()=='<?php echo $closeout; ?>' || $('#closing_amount').val()=='<?php echo to_currency_no_money($closeout); ?>')
		{
			$('#closing_amount_form').submit();	
		}
		else
		{
			bootbox.confirm(<?php echo json_encode(lang('closing_amount_not_equal')); ?>,function(result)
			{
				if (result)
				{
					$('#closing_amount_form').submit();			
				}
			});
			
		}
}
</script>
<?php $this->load->view('partial/footer.php'); ?>