<?php $this->load->view("partial/header"); ?>

<?php if(isset($pagination) && $pagination) {  ?>
	<div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
		<?php echo $pagination;?>
	</div>
<?php }  ?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-piluku">
			<div class="panel-heading hidden-print">
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
			</div>
			<div class="panel-body">				
				<?php $counter = 0;?>
				<?php foreach($report_data as $data) {?>
					

					<div id="statement_header" class="store_account_address">
						<div id="company_name" class="form-heading"><?php echo $this->config->item('company'); ?></div>
						<?php if($this->config->item('company_logo')) {?>
						<div id="company_logo"><?php echo img(array('src' => $this->Appconfig->get_logo_image())); ?></div>
						<?php } ?>
						<div id="company_address"><?php echo nl2br($this->Location->get_info_for_key('address')); ?></div>
						<div id="company_phone"><?php echo $this->Location->get_info_for_key('phone'); ?></div>
						<?php if($this->config->item('website')) { ?>
							<div id="website"><?php echo $this->config->item('website'); ?></div>
						<?php } ?>
					</div>
					
					<?php
						
					if ($data['customer_info']->company_name)
					{
						$customer_title = $data['customer_info']->company_name;
					}
					else
					{
						$customer_title = $data['customer_info']->first_name .' '. $data['customer_info']->last_name;		
					}
					?>
					<div class="pull-right"><label class="label label-primary"><?php echo $subtitle;?></label></div>
					
					<div class="store_account_address">
						<span><strong><?php echo $customer_title.' '.($data['customer_info']->account_number ? $data['customer_info']->account_number : '') ;?></strong></span><br />
						<?php if($data['customer_info']->address_1) { ?>
								<span><?php echo $data['customer_info']->address_1 . ' '.$data['customer_info']->address_2; ?></span><br />
								<span><?php echo $data['customer_info']->city . ', '.$data['customer_info']->state . ' '.$data['customer_info']->zip; ?></span>
						<?php } ?>
					</div>

					<div class="table-responsive">
					<table class="table table-striped table-hover data-table tablesorter" id="sortable_table">
						<thead>
							<tr>
								<td><?php echo lang('reports_id');?></td>
								<td><?php echo lang('reports_date');?></td>
								<td><?php echo lang('reports_sale_id');?></td>
								<td><?php echo lang('reports_debit');?></td>
								<td><?php echo lang('reports_credit');?></td>
								<td><?php echo lang('reports_balance');?></td>
								<?php if (!$hide_items) { ?>
									<td><?php echo lang('reports_items');?></td>
								<?php } ?>
								<td><?php echo lang('common_comment');?></td>
							</tr>
						</thead>
						<tbody>
							<?php 
							$amount_due = false;
							foreach($data['store_account_transactions'] as $transaction) 
							{ 
							?>
							<tr>
								<td><?php echo $transaction['sno'];?></td>
								<td><?php echo date(get_date_format(). ' '.get_time_format(), strtotime($transaction[$date_column]));?></td>
								<td><?php echo $transaction['sale_id'] ? anchor('sales/receipt/'.$transaction['sale_id'], $this->config->item('sale_prefix').' '.$transaction['sale_id'], array('target' => '_blank')) : '-';?></td>
								<td><?php echo $transaction['transaction_amount'] > 0 ? to_currency($transaction['transaction_amount']) : to_currency(0); ?></td>
								<td><?php echo $transaction['transaction_amount'] < 0 ? to_currency($transaction['transaction_amount'] * -1) : to_currency(0); ?></td>
								<td><?php echo to_currency($transaction['balance']);?></td>
								<?php if (!$hide_items) { ?>
									<td><?php echo $transaction['items'];?></td>
								<?php } ?>
								<td><?php echo $transaction['comment'];?></td>
							</tr>
							<?php 
							$amount_due = $transaction['balance'];
							} ?>
						</tbody>
					</table>
				</div>					
					
				<div class="row">
					<div class="col-md-3 col-xs-12 col-sm-6 ">
                        <div class="info-seven primarybg-info">
                            <div class="logo-seven"><i class="ti-widget dark-info-primary"></i></div>
                            <?php echo to_currency($amount_due); ?>
                            <p><?php echo lang('common_amount_due'); ?></p>
                        </div>
                    </div>				
				</div>
				
				<?php
				if ($this->config->item('store_account_statement_message'))
				{
				?>
				<div class="row">
					<div class="col-md-12 col-xs-12 col-sm-12 ">
						<?php echo $this->config->item('store_account_statement_message');?>
					</div>
				</div>
				<?php
				}
				?>
				
				<?php if ($data['customer_info']->email) {?>
				
					<div class="row">
						<div class="col-md-3 col-xs-12 col-sm-6 ">
						<?php echo anchor("reports/store_account_statements_email_customer/".$data['customer_info']->person_id.'/'.$this->uri->segment(4).'/'.$this->uri->segment(5).'/'.$this->uri->segment(6).'/'.$this->uri->segment(7),
							"<div class='small_button'>".lang('reports_email_statement')."</div>",
							array('class'=>'btn btn-primary none email_statement hidden-print','title'=>lang('reports_email_statement')));
						?>									
                
						 </div>				
					</div>
				<?php } ?>
					
				<?php if ($counter != count($report_data) - 1) {?>
						<div class="page-break" style="page-break-before: always;"></div>
				<?php } ?>
				<?php $counter++;?>
				<?php } ?>
					
					<div class="text-center">
						<button class="btn btn-primary text-white hidden-print" id="print_button"  > <?php echo lang('common_print'); ?> </button>	
					</div>
					
			</div>
		</div>
	</div>
</div>

<?php if(isset($pagination) && $pagination) {  ?>
	<div class="pagination hidden-print alternate text-center fg-toolbar ui-toolbar" id="pagination_top" >
		<?php echo $pagination;?>
	</div>
<?php }  ?>

<script type="text/javascript">
$('.email_statement').click(function(e)
{
	e.preventDefault();
	$.get($(this).attr('href'));
	show_feedback('success', <?php echo json_encode(lang('reports_email_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
})

function print_report()
{
	window.print();
}

$(document).ready(function()
{
	$('#print_button').click(function(e){
		e.preventDefault();
		print_report();
	});
});

</script>
<?php $this->load->view("partial/footer"); ?>