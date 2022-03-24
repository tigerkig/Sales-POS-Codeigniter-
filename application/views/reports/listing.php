<?php $this->load->view("partial/header"); ?>

<div class="row report-listing">
	<div class="col-md-6  ">
		<div class="panel">
			<div class="panel-body">
				<div class="list-group parent-list">
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_categories', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="categories"><i class="icon ti-layout-grid3"></i>	<?php echo lang('reports_categories'); ?></a>
					<?php } ?>
					

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_closeout', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="closeout"><i class="icon ti-close"></i>	<?php echo lang('reports_closeout'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_sales_generator', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="custom-report">
							<i class="icon ti-search"></i>	<?php echo lang('reports_sales_generator'); ?>
						</a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_commissions', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="commissions"><i class="icon ti-money"></i>	<?php echo lang('reports_commission'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_customers', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="customers"><i class="icon ti-user"></i>	<?php echo lang('reports_customers'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_deleted_sales', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>	
						<a href="#" class="list-group-item" id="deleted-sales"><i class="icon ti-trash"></i>	<?php echo lang('reports_deleted_sales'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_discounts', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="discounts"><i class="icon ti-wand"></i>	<?php echo lang('reports_discounts'); ?></a>
					<?php } ?>

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_employees', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="employees"><i class="icon ti-id-badge"></i>	<?php echo lang('reports_employees'); ?></a>
					<?php } ?>
					
               <?php
					if ($this->Employee->has_module_action_permission('reports', 'view_expenses', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="expenses"><i class="icon ti-money"></i>	<?php echo lang('reports_expenses'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_giftcards', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="giftcards"><i class="icon ti-credit-card"></i>	<?php echo lang('reports_giftcards'); ?></a>
					<?php } ?>

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_inventory_reports', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>					
						<a href="#" class="list-group-item" id="inventory"><i class="icon ti-bar-chart"></i>	<?php echo lang('reports_inventory_reports'); ?></a>
					<?php } ?>

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_item_kits', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>					
						<a href="#" class="list-group-item" id="item-kits"><i class="icon ti-harddrives"></i>	<?php echo lang('module_item_kits'); ?></a>
					<?php } ?>


					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_items', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>					
						<a href="#" class="list-group-item" id="items"><i class="icon ti-harddrive"></i>	<?php echo lang('reports_items'); ?></a>
					<?php } ?>

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_manufacturers', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="manufacturers"><i class="icon ti-layout-grid3"></i>	<?php echo lang('reports_manufacturers'); ?></a>
					<?php } ?>


					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_payments', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>					
						<a href="#" class="list-group-item" id="payments"><i class="icon ti-money"></i>	<?php echo lang('common_payments'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_profit_and_loss', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="profit-and-loss"><i class="icon ti-shopping-cart-full"></i>	<?php echo lang('reports_profit_and_loss'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_receivings', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="receivings"><i class="icon ti-cloud-down"></i>	<?php echo lang('reports_receivings'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_register_log', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<?php if ($this->config->item('track_cash')) { ?>
							<a href="#" class="list-group-item" id="register-log"><i class="icon ti-search"></i>	<?php echo lang('reports_register_log_title'); ?></a>
						<?php } ?>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_sales', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="sales"><i class="icon ti-shopping-cart"></i>	<?php echo lang('reports_sales'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_store_account', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<?php if($this->config->item('customers_store_accounts') || $this->config->item('suppliers_store_accounts')) { ?>
							<a href="#" class="list-group-item" id="store-accounts"><i class="icon ti-credit-card"></i>	<?php echo lang('reports_store_account'); ?></a>
						<?php } ?>
					<?php } ?>

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_suppliers', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="suppliers"><i class="icon ti-download"></i>	<?php echo lang('reports_suppliers'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_suspended_sales', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="suspended_sales"><i class="icon ti-download"></i>	<?php echo lang('reports_suspended_sales'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_tags', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="tags"><i class="icon ti-layout-grid3"></i>	<?php echo lang('common_tags'); ?></a>
					<?php } ?>
					
					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_taxes', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="taxes"><i class="icon ti-agenda"></i>	<?php echo lang('reports_taxes'); ?></a>
					<?php } ?>
					

					<?php
					if ($this->Employee->has_module_action_permission('reports', 'view_tiers', $this->Employee->get_logged_in_employee_info()->person_id))
					{
					?>
						<a href="#" class="list-group-item" id="tiers"><i class="icon ti-stats-up"></i>	<?php echo lang('reports_tiers'); ?></a>
					<?php } ?>

					<?php
					if ($this->config->item('timeclock'))
					{
						if ($this->Employee->has_module_action_permission('reports', 'view_timeclock', $this->Employee->get_logged_in_employee_info()->person_id))
						{
							?>
							<a href="#" class="list-group-item" id="timeclock"><i class="icon ti-bell"></i>	<?php echo lang('employees_timeclock'); ?></a>
							<?php } ?>
					
					<?php } ?> 
					
				</div>
			</div>
		</div> <!-- /panel -->
	</div>
	<div class="col-md-6" id="report_selection">
		<div class="panel">
			<div class="panel-body child-list">
			<h3 id="right_heading" class="page-header text-info"><i class="icon ti-angle-double-left"></i><?php echo lang('reports_make_a_selection')?></h3>
				<div class="list-group custom-report hidden">
					<a href="<?php echo site_url('reports/sales_generator');?>" class="list-group-item ">
						<i class="icon ti-search report-icon"></i>  <?php echo lang('reports_sales_search'); ?>
					</a>
				</div>
				<div class="list-group customers hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a class="list-group-item" href="<?php echo site_url('reports/graphical_summary_customers');?>" ><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_customers');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/specific_customer');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>

				<div class="list-group commissions hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a class="list-group-item" href="<?php echo site_url('reports/graphical_summary_commissions');?>" ><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					
					<a class="list-group-item" href="<?php echo site_url('reports/summary_commissions');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_commissions');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				
				<div class="list-group employees hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a class="list-group-item" href="<?php echo site_url('reports/graphical_summary_employees');?>" ><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_employees');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/specific_employee');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>

				<div class="list-group sales hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a class="list-group-item" href="<?php echo site_url('reports/graphical_summary_sales');?>" ><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_sales');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_sales_time');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_sales_time_reports'); ?></a>
					<?php if (can_display_graphical_report() ){ ?>
						<a class="list-group-item" href="<?php echo site_url('reports/graphical_summary_sales_time');?>" ><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_summary_sales_graphical_time_reports'); ?></a>
					<?php } ?>
				</div>
				<div class="list-group deleted-sales hidden">
					<a href="<?php echo site_url('reports/deleted_sales');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				<div class="list-group register-log hidden">
					<a href="<?php echo site_url('reports/detailed_register_log');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				<div class="list-group categories hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_categories');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_categories');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				<div class="list-group discounts hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_discounts');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_discounts');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				<div class="list-group items hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_items');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_items');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_items_variance');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_price_variance_report'); ?></a>
				</div>
				
				<div class="list-group manufacturers hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_manufacturers');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_manufacturers');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				
				
				<div class="list-group item-kits hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_item_kits');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_item_kits');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/summary_item_kits_variance');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_price_variance_report'); ?></a>
				
				</div>
				<div class="list-group payments hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_payments');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_payments');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a href="<?php echo site_url('reports/detailed_payments');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				<div class="list-group suppliers hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_suppliers');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_suppliers');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a href="<?php echo site_url('reports/specific_supplier');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_suppliers_receivings');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_receiving_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_suppliers_receivings');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_receiving_reports'); ?></a>
					<a href="<?php echo site_url('reports/specific_supplier_receivings');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_receiving_reports'); ?></a>
					
				</div>
				
				<div class="list-group suspended_sales hidden">
					<a href="<?php echo site_url('reports/detailed_suspended_sales');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				
				<div class="list-group taxes hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_taxes');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_taxes');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				
				<div class="list-group timeclock hidden">
					<a href="<?php echo site_url('reports/summary_timeclock');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>			
					<a href="<?php echo site_url('reports/detailed_timeclock');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				
				
				<div class="list-group tiers hidden">
					<a href="<?php echo site_url('reports/summary_tiers');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>			
				</div>
				
				<div class="list-group receivings hidden">
					<a href="<?php echo site_url('reports/detailed_receivings');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					<a href="<?php echo site_url('reports/detailed_suspended_receivings');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('common_suspended_receivings'); ?></a>
					<a href="<?php echo site_url('reports/deleted_recevings');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_deleted_recv_reports'); ?></a>
					<a href="<?php echo site_url('reports/summary_taxes_receivings');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_taxes_reports'); ?></a>
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_taxes_receivings');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_summary_taxes_reports'); ?></a>
					<?php } ?>
					<br>
					<h4 class="text-info"><?php echo lang('reports_payments')?></h4>
										
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/receivings_graphical_summary_payments');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/receivings_summary_payments');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a href="<?php echo site_url('reports/receivings_detailed_payments');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					
				</div>
				<div class="list-group inventory hidden">
					<a href="<?php echo site_url('reports/inventory_low');?>" class="list-group-item"><i class="icon ti-stats-down"></i> <?php echo lang('reports_low_inventory'); ?></a>
					<a href="<?php echo site_url('reports/inventory_summary');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_inventory_summary'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_inventory');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					<a href="<?php echo site_url('reports/summary_count_report');?>" class="list-group-item"><i class="icon ti-stats-down"></i> <?php echo lang('reports_summary_count_report'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_count_report');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_count_report'); ?></a>
					<a href="<?php echo site_url('reports/expiring_inventory');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_expiring_items_report'); ?></a>
				
				</div>
				<div class="list-group giftcards hidden">
					<a href="<?php echo site_url('reports/summary_giftcards');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>			
					<a href="<?php echo site_url('reports/detailed_giftcards');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
					<a href="<?php echo site_url('reports/giftcard_audit');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_audit_report'); ?></a>
					<a href="<?php echo site_url('reports/summary_giftcard_sales');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_gift_card_sales_reports'); ?></a>			
					
				</div>
				<div class="list-group store-accounts hidden">
					
					<?php if ($this->config->item('customers_store_accounts') && $this->Employee->has_module_action_permission('reports', 'view_store_account', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>					
						<h4 class="text-info"><?php echo lang('reports_customers')?></h4>
						<a href="<?php echo site_url('reports/store_account_statements');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_store_account_statements'); ?></a>
						<a href="<?php echo site_url('reports/summary_store_accounts');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
						<a href="<?php echo site_url('reports/specific_customer_store_account');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
						<a href="<?php echo site_url('reports/store_account_activity');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_activity'); ?></a>
						<a href="<?php echo site_url('reports/store_account_outstanding');?>" class="list-group-item"><i class="icon ti-stats-down"></i> <?php echo lang('reports_outstanding_sales'); ?></a>
					<?php } ?>
					<br>
					<?php if ($this->config->item('suppliers_store_accounts') && $this->Employee->has_module_action_permission('reports', 'view_store_account_suppliers', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
						<h4 class="text-info"><?php echo lang('reports_suppliers')?></h4>
						<a href="<?php echo site_url('reports/supplier_store_account_statements');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_store_account_statements'); ?></a>
						<a href="<?php echo site_url('reports/supplier_summary_store_accounts');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
						<a href="<?php echo site_url('reports/supplier_specific_store_account');?>" class="list-group-item"><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
						<a href="<?php echo site_url('reports/supplier_store_account_activity');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_activity'); ?></a>
						<a href="<?php echo site_url('reports/supplier_store_account_outstanding');?>" class="list-group-item"><i class="icon ti-stats-down"></i> <?php echo lang('reports_outstanding_recv'); ?></a>
					<?php } ?>
				</div>
				<div class="list-group profit-and-loss hidden">
					<a class="list-group-item" href="<?php echo site_url('reports/summary_profit_and_loss');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_profit_and_loss');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				<div class="list-group expenses hidden">
					<a class="list-group-item" href="<?php echo site_url('reports/summary_expenses');?>" ><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_expenses');?>" ><i class="icon ti-calendar"></i> <?php echo lang('reports_detailed_reports'); ?></a>
				</div>
				
				<div class="list-group closeout hidden">
					<a href="<?php echo site_url('reports/closeout');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				
				<div class="list-group tags hidden">
					<?php if (can_display_graphical_report() ){ ?>
						<a href="<?php echo site_url('reports/graphical_summary_tags');?>" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> <?php echo lang('reports_graphical_reports'); ?></a>
					<?php } ?>
					<a href="<?php echo site_url('reports/summary_tags');?>" class="list-group-item"><i class="icon ti-receipt"></i> <?php echo lang('reports_summary_reports'); ?></a>
				</div>
				
				
			</div>
		</div> <!-- /panel -->
	</div>
</div>

<script type="text/javascript">
 $('.parent-list a').click(function(e){
 	e.preventDefault();
 	$('.parent-list a').removeClass('active');
 	$(this).addClass('active');
 	var currentClass='.child-list .'+ $(this).attr("id");
 	$('.child-list .page-header').html($(this).html());
 	$('.child-list .list-group').addClass('hidden');
 	$(currentClass).removeClass('hidden');
	$('#right_heading').addClass('active');
	$('html, body').animate({
	    scrollTop: $("#report_selection").offset().top
	 }, 500);
 });
 </script>


<?php $this->load->view("partial/footer"); ?>