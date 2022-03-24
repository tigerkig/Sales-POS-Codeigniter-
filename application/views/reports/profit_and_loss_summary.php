<?php $this->load->view("partial/header"); ?>
<div class="panel panel-piluku">
	<div class="panel-heading">
		<?php echo lang('reports_reports'); ?> - <?php echo lang('reports_profit_and_loss') ?>
	</div>	
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="col-md-3 col-xs-12 col-sm-6 ">
			        <div class="info-seven redbg-info">
			            <div class="logo-seven"><i class="ti-widget dark-info-red"></i></div>
			            <?php echo to_currency($details_data['sales_total']); ?>
			            <p><?php echo lang('reports_sales'); ?></p>
			        </div>
			    </div>
			    <div class="col-md-3 col-xs-12 col-sm-6 ">
		            <div class="info-seven greenbg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-green"></i></div>
		                <?php echo to_currency($details_data['returns_total']); ?>
		                <p><?php echo lang('reports_returns'); ?></p>
		            </div>
		        </div>
				  
		        <div class="col-md-3 col-xs-12 col-sm-6 ">
		            <div class="info-seven orangebg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-orange"></i></div>
		                <?php echo to_currency($details_data['discount_total']); ?>
		                <p><?php echo lang('reports_discounts'); ?></p>
		            </div>
		        </div>
				  
				  <div class="col-md-3 col-xs-12 col-sm-6">
		            <div class="info-seven primarybg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-primary"></i></div>
		                <?php echo to_currency($details_data['taxes_total']); ?>
		                <p><?php echo lang('reports_taxes'); ?></p>
		            </div>
		        </div>
				  
				  
		        <div class="col-md-3 col-xs-12 col-sm-6 ">
		            <div class="info-seven redbg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-red"></i></div>
		                <?php echo to_currency($details_data['total']); ?>
		                <p><?php echo lang('reports_total_profit_and_loss'); ?></p>
		            </div>
		        </div>
		        
				  
		        <div class="col-md-3 col-xs-12 col-sm-6 ">
		            <div class="info-seven primarybg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-primary"></i></div>
		                <?php echo to_currency($details_data['receivings_total']); ?>
		                <p><?php echo lang('reports_receivings'); ?></p>
		            </div>
		        </div>
				  
		        <div class="col-md-3 col-xs-12 col-sm-6 ">
		            <div class="info-seven greenbg-info">
		                <div class="logo-seven"><i class="ti-widget dark-info-green"></i></div>
		                <?php echo to_currency($details_data['commission']); ?>
		                <p><?php echo lang('reports_commission'); ?></p>
		            </div>
					</div>				
				  
				  
		        <?php
				if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
				{
				?>
			        <div class="col-md-3 col-xs-12 col-sm-6 ">
			            <div class="info-seven greenbg-info">
			                <div class="logo-seven"><i class="ti-widget dark-info-green"></i></div>
			                <?php echo to_currency($details_data['profit']); ?>
			                <p><?php echo lang('common_profit'); ?></p>
			            </div>
			        </div>
			    <?php } ?>
				 
			 	<?php
			 	if($this->Employee->has_module_action_permission('reports','view_expenses',$this->Employee->get_logged_in_employee_info()->person_id))
			 	{
			 	?>

	        <div class="col-md-3 col-xs-12 col-sm-6 ">
	            <div class="info-seven greenbg-info">
	                <div class="logo-seven"><i class="ti-widget dark-info-green"></i></div>
	                <?php echo to_currency($details_data['expense_amount']); ?>
	                <p><?php echo lang('reports_expenses'); ?></p>
	            </div>
				</div>				
			 	<?php } ?>
				 
			</div>
		</div>

	</div>
</div>



<?php $this->load->view("partial/footer"); ?>