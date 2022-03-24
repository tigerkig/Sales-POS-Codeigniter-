<?php $this->load->view("partial/header"); ?>
<div class="row">

	<div class="text-center">
		<button class="btn btn-primary text-white hidden-print" id="print_button" onclick="window.print();"> <?php echo lang('common_print'); ?> </button>	
	</div>
	<br />
	
	
	<div class="col-md-12">			
		<?php
		if($register_log->shift_end=='0000-00-00 00:00:00')
		{
			$shift_end=lang('reports_register_log_open');	
		}
		else
		{
			$shift_end = date(get_date_format(). ' '.get_time_format(), strtotime($register_log->shift_end));
		}
		?>
		
		<div class="row" id="register_log_details">
			<div class="col-lg-4 col-md-12">
				<ul class="list-group">
					<li class="list-group-item"><?php echo lang('reports_register_log_id'). ': <strong class="pull-right">'. $register_log->register_log_id; ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_employee_open'). ': <strong class="pull-right">'. $register_log->open_first_name.' '.$register_log->open_last_name; ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_close_employee'). ': <strong class="pull-right">'.$register_log->close_first_name.' '.$register_log->close_last_name;  ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_shift_start'). ': <strong class="pull-right">'. date(get_date_format(). ' '.get_time_format(), strtotime($register_log->shift_start)); ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_shift_end'). ': <strong class="pull-right">'. $shift_end; ?></strong></li>
					<li class="list-group-item"><?php echo lang('common_open_amount'). ': <strong class="pull-right">'. to_currency($register_log->open_amount); ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_close_amount'). ': <strong class="pull-right">'. to_currency($register_log->close_amount); ?></strong></li>
					<li class="list-group-item"><?php echo lang('common_cash_sales'). ': <strong class="pull-right">'. to_currency($register_log->cash_sales_amount); ?></strong></li>
					<li class="list-group-item"><?php echo lang('common_total_cash_additions'). ': <strong class="pull-right">'. to_currency($register_log->total_cash_additions); ?></strong></li>
					<li class="list-group-item"><?php echo lang('common_total_cash_subtractions'). ': <strong class="pull-right">'. to_currency($register_log->total_cash_subtractions); ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_difference'). ': <strong class="pull-right">'. to_currency($register_log->difference); ?></strong></li>
					<li class="list-group-item"><?php echo lang('reports_notes'). ': <strong class="pull-right">'. $register_log->notes; ?></strong></li>
				</ul>
			</div>

			<div class="col-lg-8  col-md-12">
				<div class="panel panel-piluku">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?php echo lang('reports_adds_and_subs');?>
						</h3>
					</div>
					<div class="panel-body nopadding table_holder  table-responsive" >
						<table class="table  table-hover table-reports table-bordered">
							<thead>
								<tr>
									<th><?php echo lang('reports_date')?></th>
									<th><?php echo lang('reports_employee')?></th>
									<th><?php echo lang('common_amount')?></th>
									<th><?php echo lang('reports_notes')?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if ($register_log_details != FALSE)
								{
									foreach($register_log_details as $row) {?>
									<tr>
										<td><?php echo date(get_date_format(). ' '.get_time_format(), strtotime($row['date']));?></td>
										<td><?php echo $row['employee_name'];?></td>
										<td><?php echo to_currency($row['amount']);?></td>
										<td><?php echo $row['note'];?></td>
									</tr>
									<?php } 	
								}
								?>
							</tbody>
						</table>
					</div>		
				</div>
			</div>
			<!-- Col-md-6 -->

		</div> 
		<!-- row -->

	</div>
</div>	

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">

</script>