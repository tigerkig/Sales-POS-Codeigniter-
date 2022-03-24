<?php $this->load->view("partial/header");
	$controller_name="items";
 ?>
												

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
					<h3 class="panel-title hidden-print">
						 <?php echo lang('sales_list_of_suspended_sales'); ?>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					

						<table class="table table-bordered table-striped table-hover data-table" id="dTable">
				<thead>	<tr>
					<th><?php echo lang('sales_suspended_sale_id'); ?></th>
					<th><?php echo lang('common_date'); ?></th>
					<th><?php echo lang('common_type'); ?></th>
					<th><?php echo lang('sales_customer'); ?></th>
					<th><?php echo lang('reports_items'); ?></th>
					<th><?php echo lang('common_total'); ?></th>
					<th><?php echo lang('common_amount_paid'); ?></th>
					<th><?php echo lang('common_last_payment_date'); ?></th>
					<th><?php echo lang('common_amount_due'); ?></th>
					<th><?php echo lang('common_comments'); ?></th>
					<th><?php echo lang('common_unsuspend'); ?></th>
					<th><?php echo lang('sales_receipt'); ?></th>
					<th><?php echo lang('common_email_receipt'); ?></th>
					<?php if ($this->Employee->has_module_action_permission('sales', 'delete_suspended_sale', $this->Employee->get_logged_in_employee_info()->person_id)){ ?>
					<th><?php echo lang('common_delete'); ?></th>
					<?php } ?>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($suspended_sales as $suspended_sale)
				{
				?>
					<tr>
						<td><?php echo ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS' ). ' '.$suspended_sale['sale_id'];?></td>
						<td><?php echo date(get_date_format(). ' @ '.get_time_format(),strtotime($suspended_sale['sale_time']));?></td>
						<td><?php echo $suspended_sale['suspended']== 1  ? ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway')) : lang('common_estimate');?></td>
						<td>
							<?php
							if (isset($suspended_sale['customer_id'])) 
							{
								$customer = $this->Customer->get_info($suspended_sale['customer_id']);
								$company_name = $customer->company_name;
								if($company_name) {
								echo $customer->first_name. ' '. $customer->last_name.' ('.$customer->company_name.')';
								}
								else {
									echo $customer->first_name. ' '. $customer->last_name;
								}
							}
							else
							{
							?>
								&nbsp;
							<?php
							}
							?>
						</td>
						<td><?php echo $suspended_sale['items'];?></td>
						<td><?php echo to_currency($suspended_sale['sale_total'])?>
						<td><?php echo to_currency($suspended_sale['amount_paid']);?></td>
						<td><?php echo $suspended_sale['last_payment_date'];?></td>
						<td><?php echo to_currency($suspended_sale['amount_due']);?></td>
						<td><?php echo $suspended_sale['comment'];?></td>
						<td>
							<?php 
							echo form_open('sales/unsuspend');
							echo form_hidden('suspended_sale_id', $suspended_sale['sale_id']);
							?>
							<input type="submit" name="submit" value="<?php echo lang('common_unsuspend'); ?>" id="submit_unsuspend" class="btn btn-primary" />
							<?php echo form_close(); ?>
						</td>
						<td>
							<?php 
							echo form_open('sales/receipt/'.$suspended_sale['sale_id'], array('method'=>'get', 'class' => 'form_receipt_suspended_sale'));
							?>
							<input type="submit" name="submit" value="<?php echo lang('common_recp'); ?>" id="submit_receipt" class="btn btn-primary" />
							<?php echo form_close(); ?>
						</td>
						<td>
						<?php
						if ($suspended_sale['email']) 
						{
							echo form_open('sales/email_receipt/'.$suspended_sale['sale_id'], array('method'=>'get', 'class' => 'form_email_receipt_suspended_sale'));
							?>
								<input type="submit" name="submit" value="<?php echo lang('common_email'); ?>" id="submit_receipt" class="btn btn-primary" />
							<?php echo form_close(); ?>
						<?php } ?>
						
						</td>
						<?php 
						if ($this->Employee->has_module_action_permission('sales', 'delete_suspended_sale', $this->Employee->get_logged_in_employee_info()->person_id)){
						?>
						<td>
						<?php
						 	echo form_open('sales/delete_suspended_sale', array('class' => 'form_delete_suspended_sale'));
							echo form_hidden('suspended_sale_id', $suspended_sale['sale_id']);
							?>
							<input type="submit" name="submitf" value="<?php echo lang('common_delete'); ?>" id="submit_delete" class="btn btn-danger">
							<?php echo form_close(); ?>
						</td>
						<?php } ?>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>


				
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>




<script type="text/javascript">
$(".form_delete_suspended_sale").submit(function()
{
	var formDelete = this;
	bootbox.confirm(<?php echo json_encode(lang("sales_delete_confirmation")); ?>, function(result)
	{
		if (result)
		{
			formDelete.submit();
		}		
	});
	
	return false;
	
});

$(".form_email_receipt_suspended_sale").ajaxForm({success: function()
{
	bootbox.alert("<?php echo lang('common_receipt_sent'); ?>");
}});	

$('#dTable').dataTable({
	"sPaginationType": "bootstrap",
	"bSort" : false
});

</script>