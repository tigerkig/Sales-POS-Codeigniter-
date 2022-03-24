<?php

function create_breadcrumb()
{
	$ci = &get_instance();
	$return = '';
	$dashboard_link = '<a  tabindex="-1"  href="'.site_url('home').'">'.lang('common_dashboard').'</a>';

	$return.=$dashboard_link;
		
	if ($ci->uri->segment(1) == 'customers')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$customers_home_link =create_current_page_url(lang('module_customers'));
		}
		else
		{
			$customers_home_link = '<a tabindex = "-1" href="'.site_url('customers').'">'.lang('module_customers').'</a>';
		}
		
		$return.=$customers_home_link;
		
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('customers_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('common_update'));
			}
		}
		elseif($ci->uri->segment(2) == 'excel_import')
		{
			$return.=create_current_page_url(lang('customers_import_customers_from_excel'));
		}
		elseif($ci->uri->segment(2) == 'custom_fields')
		{
			$return.=create_current_page_url(lang('common_custom_field_config'));
		}
		elseif($ci->uri->segment(2) == 'generate_barcode_labels')
		{
			$return.=create_current_page_url(lang('common_generate_barcodes'));
		}
  }
	elseif($ci->uri->segment(1) == 'items')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$items_home_link =create_current_page_url(lang('module_items'));
		}
		else
		{
			$items_home_link = '<a tabindex = "-1" href="'.site_url('items').'">'.lang('module_items').'</a>';
		}
				
		$return.=$items_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('items_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('items_update'));
			}
		}
		elseif($ci->uri->segment(2) == 'excel_import')
		{
			$return.=create_current_page_url(lang('common_excel_import'));
		}
		elseif($ci->uri->segment(2) == 'count')
		{
			$return.=create_current_page_url(lang('items_count_inventory'));
		}
		elseif($ci->uri->segment(2) == 'do_count')
		{
			$return.= '<a tabindex = "-1" href="'.site_url('items/count').'">'.lang('items_count_inventory').'</a>';
			
			$return.=create_current_page_url(lang('items_do_count'));
		}
		elseif($ci->uri->segment(2) == 'excel_import_count')
		{
			$count_id = $ci->session->userdata('current_count_id');	
			$return.= '<a tabindex = "-1" href="'.site_url("items/count").'">'.lang('items_count_inventory').'</a>';
			$return.= '<a tabindex = "-1" href="'.site_url("items/do_count/$count_id").'">'.lang('items_do_count').'</a>';
			$return.=create_current_page_url(lang('common_excel_import'));
		}
		elseif($ci->uri->segment(2) == 'categories')
		{
			$return.=create_current_page_url(lang('items_manage_categories'));
		}
		elseif($ci->uri->segment(2) == 'manage_tags')
		{
			$return.=create_current_page_url(lang('items_manage_tags'));
		}
		elseif($ci->uri->segment(2) == 'manage_manufacturers')
		{
			$return.=create_current_page_url(lang('items_manage_manufacturers'));
		}
		elseif($ci->uri->segment(2) == 'price_rules')
		{
			$return.=create_current_page_url(lang('items_manage_price_rules'));
		}
		elseif($ci->uri->segment(2) == 'generate_barcode_labels' || $ci->uri->segment(2) == 'generate_barcodes' || $ci->uri->segment(2) == 'generate_barcodes_labels_from_recv' || $ci->uri->segment(2) =='generate_barcodes_from_recv')
		{
			$return.=create_current_page_url(lang('common_generate_barcodes'));
		}
	}
	elseif($ci->uri->segment(1) == 'price_rules')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$price_rules_home_link =create_current_page_url(lang('module_price_rules'));
		}
		else
		{
			$price_rules_home_link = '<a tabindex = "-1" href="'.site_url('price_rules').'">'.lang('module_price_rules').'</a>';
		}
		
		$return.=$price_rules_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('price_rules_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('price_rules_update'));
			}
		}
		
	}
	elseif($ci->uri->segment(1) == 'item_kits')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$item_kits_home_link =create_current_page_url(lang('module_item_kits'));
		}
		else
		{
			$item_kits_home_link = '<a tabindex = "-1" href="'.site_url('item_kits').'">'.lang('module_item_kits').'</a>';
		}
				
		$return.=$item_kits_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('item_kits_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('item_kits_update'));
			}
		}
		elseif($ci->uri->segment(2) == 'generate_barcode_labels' || $ci->uri->segment(2) == 'generate_barcodes')
		{
			$return.=create_current_page_url(lang('common_generate_barcodes'));
		}
		
	}
	elseif($ci->uri->segment(1) == 'suppliers')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$suppliers_home_link =create_current_page_url(lang('module_suppliers'));
		}
		else
		{
			$suppliers_home_link = '<a tabindex = "-1" href="'.site_url('suppliers').'">'.lang('module_suppliers').'</a>';
		}
				
		$return.=$suppliers_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('suppliers_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('common_update'));
			}
		}
		elseif($ci->uri->segment(2) == 'excel_import')
		{
			$return.=create_current_page_url(lang('suppliers_import_suppliers_from_excel'));
		}
		elseif($ci->uri->segment(2) == 'custom_fields')
		{
			$return.=create_current_page_url(lang('common_custom_field_config'));
		}
	}
	elseif($ci->uri->segment(1) == 'reports')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$reports_home_link =create_current_page_url(lang('module_reports'));
		}
		else
		{
			$reports_home_link = '<a tabindex = "-1" href="'.site_url('reports').'">'.lang('module_reports').'</a>';
		}
		
		$return.=$reports_home_link;
		
		if($ci->uri->segment(2) == 'graphical_summary_categories' || $ci->uri->segment(2) == 'summary_categories')
		{
			$return.=create_report_breadcrumb(lang('reports_categories_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'sales_generator')
		{
			$return.=create_current_page_url(lang('reports_sales_generator'));
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_customers' || $ci->uri->segment(2) == 'summary_customers')
		{
			$return.=create_report_breadcrumb(lang('reports_customers_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'specific_customer')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_customers_report'));
		}		
		elseif($ci->uri->segment(2) == 'deleted_sales')
		{
			$return.=create_report_breadcrumb(lang('reports_deleted_sales_report'));				
		}		
		if($ci->uri->segment(2) == 'graphical_summary_discounts' || $ci->uri->segment(2) == 'summary_discounts')
		{
			$return.=create_report_breadcrumb(lang('reports_discounts_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_employees' || $ci->uri->segment(2) == 'summary_employees')
		{
			$return.=create_report_breadcrumb(lang('reports_employees_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'specific_employee')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_employees_report'));
		}		
		elseif($ci->uri->segment(2) == 'summary_giftcards')
		{
			$return.=create_report_breadcrumb(lang('reports_giftcard_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'detailed_giftcards')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_giftcards_report'));
		}		
		elseif($ci->uri->segment(2) == 'inventory_low')
		{
			$return.=create_report_breadcrumb(lang('reports_low_inventory_report'));	
		}		
		elseif($ci->uri->segment(2) == 'inventory_summary')
		{
			$return.=create_report_breadcrumb(lang('reports_inventory_summary'));		
		}		
		elseif($ci->uri->segment(2) == 'summary_count_report')
		{
			$return.=create_report_breadcrumb(lang('reports_summary_count_report'));		
		}		
		elseif($ci->uri->segment(2) == 'detailed_count_report')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_count_report'));		
		}
		elseif($ci->uri->segment(2) == 'expiring_inventory')
		{
			$return.=create_report_breadcrumb(lang('reports_expired_inventory_report'));		
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_item_kits' || $ci->uri->segment(2) == 'summary_item_kits')
		{
			$return.=create_report_breadcrumb(lang('reports_item_kits_summary_report'));	
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_items' || $ci->uri->segment(2) == 'summary_items')
		{
			$return.=create_report_breadcrumb(lang('reports_items_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_payments' || $ci->uri->segment(2) == 'summary_payments')
		{
			$return.=create_report_breadcrumb(lang('reports_payments_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'summary_profit_and_loss')
		{
			$return.=create_report_breadcrumb(lang('reports_profit_and_loss'));
		}		
		elseif($ci->uri->segment(2) == 'detailed_profit_and_loss')
		{
			$return.=create_report_breadcrumb(lang('reports_profit_and_loss'));				
		}		
		elseif($ci->uri->segment(2) == 'detailed_receivings')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_receivings_report'));						
		}
		elseif($ci->uri->segment(2) == 'detailed_register_log')
		{
			$return.=create_report_breadcrumb(lang('reports_register_log_title'));						
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_sales' || $ci->uri->segment(2) == 'summary_sales')
		{
			$return.=create_report_breadcrumb(lang('reports_sales_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'detailed_sales')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_sales_report'));
		}		
		elseif($ci->uri->segment(2) == 'store_account_statements')
		{			
			$return.=create_report_breadcrumb(lang('reports_store_account_statements'));
		}		
		elseif($ci->uri->segment(2) == 'summary_store_accounts')
		{
			$return.=create_report_breadcrumb(lang('reports_store_account_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'specific_customer_store_account')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_store_accounts_report'));
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_suppliers' || $ci->uri->segment(2) == 'summary_suppliers')
		{
			$return.=create_report_breadcrumb(lang('reports_suppliers_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_suppliers_receivings' || $ci->uri->segment(2) == 'summary_suppliers_receivings')
		{
			$return.=create_report_breadcrumb(lang('reports_suppliers_receivings_summary_report'));
		}		
		elseif($ci->uri->segment(2) == 'specific_supplier')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_suppliers_report'));	
		}
		elseif($ci->uri->segment(2) == 'specific_supplier_receivings')
		{
			$return.=create_report_breadcrumb(lang('reports_recevings_report'));	
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_taxes' || $ci->uri->segment(2) == 'summary_taxes')
		{
			$return.=create_report_breadcrumb(lang('reports_taxes_summary_report'));					
		}
		elseif($ci->uri->segment(2) == 'detailed_suspended_sales')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_suspended_sales_report'));					
		
		}
		elseif ($ci->uri->segment(2) == 'detailed_payments')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_payments_report'));	
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_commissions' || $ci->uri->segment(2) == 'summary_commissions')
		{
			$return.=create_report_breadcrumb(lang('reports_summary_commission_report'));
		}
		elseif($ci->uri->segment(2) == 'detailed_commissions')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_commission_report'));
		}
		elseif($ci->uri->segment(2) == 'detailed_inventory')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_inventory_report'));
		}
		elseif($ci->uri->segment(2) == 'summary_timeclock')
		{
			$return.=create_report_breadcrumb(lang('reports_summary_timeclock_report'));
		}
		elseif($ci->uri->segment(2) == 'detailed_timeclock')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_timeclock_report'));
		}
		elseif($ci->uri->segment(2) == 'summary_tiers')
		{
			$return.=create_report_breadcrumb(lang('reports_tiers_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'closeout')
		{
			$return.=create_report_breadcrumb(lang('reports_closeout'));
		}
		elseif($ci->uri->segment(2) == 'summary_expenses')
		{
			$return.=create_report_breadcrumb(lang('reports_expenses_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'detailed_expenses')
		{
			$return.=create_report_breadcrumb(lang('reports_expenses_detailed_report'));
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_tags' || $ci->uri->segment(2) == 'summary_tags')
		{
			$return.=create_report_breadcrumb(lang('reports_tags_summary_report'));
		}
		elseif($ci->uri->segment(2) == 'register_log_details')
		{
			$return.=create_current_page_url(lang('reports_register_log_details_report'));
		}
		elseif($ci->uri->segment(2) == 'deleted_recevings')
		{
			$return.=create_current_page_url(lang('reports_deleted_recv_reports'));
		}
		elseif($ci->uri->segment(2) == 'graphical_summary_taxes_receivings' || $ci->uri->segment(2) == 'summary_taxes_receivings')
		{
			$return.=create_report_breadcrumb(lang('reports_summary_taxes_receivings_report'));					
		}
		elseif($ci->uri->segment(2) == 'giftcard_audit')
		{
			$return.=create_report_breadcrumb(lang('reports_giftcards_audit_report'));					
		}
		elseif($ci->uri->segment(2) == 'summary_sales_time' || $ci->uri->segment(2) == 'graphical_summary_sales_time')
		{
			$return.=create_report_breadcrumb(lang('reports_summary_sales_time_reports'));					
		}
		elseif($ci->uri->segment(2) == 'detailed_suspended_receivings')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_suspended_receivings_report'));					
		}
		elseif($ci->uri->segment(2) == 'store_account_outstanding')
		{
			$return.=create_report_breadcrumb(lang('reports_outstanding_sales'));								
		}
		elseif($ci->uri->segment(2) == 'supplier_store_account_statements')
		{
			$return.=create_report_breadcrumb(lang('reports_store_account_statements'));								
		}
		elseif($ci->uri->segment(2) == 'supplier_summary_store_accounts')
		{
			$return.=create_report_breadcrumb(lang('reports_store_account_summary_report'));								
		}
		elseif($ci->uri->segment(2) == 'supplier_specific_store_account')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_store_accounts_report'));								
		}
		elseif($ci->uri->segment(2) == 'supplier_store_account_activity')
		{
			$return.=create_report_breadcrumb(lang('reports_store_account_activity_report'));								
		}
		elseif($ci->uri->segment(2) == 'store_account_activity')
		{
			$return.=create_report_breadcrumb(lang('reports_store_account_activity_report'));								
		}
		elseif($ci->uri->segment(2) == 'supplier_store_account_outstanding')
		{
			$return.=create_report_breadcrumb(lang('reports_outstanding_receivings_report'));								
		}
		elseif($ci->uri->segment(2) == 'receivings_summary_payments')
		{
			$return.=create_report_breadcrumb(lang('reports_payments_summary_report'));								
		}
		elseif($ci->uri->segment(2) == 'receivings_detailed_payments')
		{
			$return.=create_report_breadcrumb(lang('reports_detailed_payments_report'));								
		}
		elseif($ci->uri->segment(2) == 'receivings_graphical_summary_payments')
		{
			$return.=create_report_breadcrumb(lang('reports_payments_summary_report'));								
		}
		elseif($ci->uri->segment(2) == 'summary_items_variance')
		{
			$return.=create_report_breadcrumb(lang('reports_price_variance_report'));								
		}
		elseif($ci->uri->segment(2) == 'summary_item_kits_variance')
		{
			$return.=create_report_breadcrumb(lang('reports_price_variance_report'));								
		}		
		elseif($ci->uri->segment(2) == 'graphical_summary_manufacturers')
		{
			$return.=create_report_breadcrumb(lang('reports_manufacturers_report'));								
		}		
		elseif($ci->uri->segment(2) == 'summary_manufacturers')
		{
			$return.=create_report_breadcrumb(lang('reports_manufacturers_report'));								
		}		
	}
	
	elseif ($ci->uri->segment(1) == 'employees')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$employees_home_link =create_current_page_url(lang('module_employees'));
		}
		else
		{
			$employees_home_link = '<a tabindex = "-1" href="'.site_url('employees').'">'.lang('module_employees').'</a>';
		}
		
		$return.=$employees_home_link;
		
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('employees_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('employees_update'));
			}
		}
		elseif($ci->uri->segment(2) == 'custom_fields')
		{
			$return.=create_current_page_url(lang('common_custom_field_config'));
		}
		
  }
	elseif ($ci->uri->segment(1) == 'giftcards')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$giftcards_home_link =create_current_page_url(lang('module_giftcards'));
		}
		else
		{
			$giftcards_home_link = '<a tabindex = "-1" href="'.site_url('giftcards').'">'.lang('module_giftcards').'</a>';
		}
		
		
		$return.=$giftcards_home_link;
		
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('giftcards_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('giftcards_update'));
			}
		}
		
		if($ci->uri->segment(2) == 'excel_import')
		{
  			$return.=create_current_page_url(lang('common_excel_import'));
		}
		
		if($ci->uri->segment(2) == 'generate_barcode_labels' || $ci->uri->segment(2) == 'generate_barcodes')
		{
			$return.=create_current_page_url(lang('common_generate_barcodes'));
		}
		
  	}
	elseif($ci->uri->segment(1) == 'config')
	{

		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$config_home_link =create_current_page_url(lang('module_config'));
		}
		
		else if($ci->uri->segment(2) == 'cron_documentation')
		{
  			$config_home_link=create_current_page_url(lang('title_cron_documentation'));
		}
		
		else
		{
			$config_home_link = '<a tabindex = "-1" href="'.site_url('config').'">'.lang('module_config').'</a>';
		}
		
		$return.=$config_home_link;
		
		
		if($ci->uri->segment(2) == 'backup')
		{
  			$return.=create_current_page_url(lang('config_backup_overview'));
		}
	}
	elseif ($ci->uri->segment(1) == 'locations')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$locations_home_link =create_current_page_url(lang('module_locations'));
		}
		else
		{
			$locations_home_link = '<a tabindex = "-1" href="'.site_url('locations').'">'.lang('module_locations').'</a>';
		}
		
		$return.=$locations_home_link;
		
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('locations_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('locations_update'));
			}
		}
  }
	elseif ($ci->uri->segment(1) == 'sales')
	{
		if ($ci->uri->segment(2) == NULL || $ci->uri->segment(2) == 'index' || $ci->uri->segment(2) == 'unsuspend') //Main page
		{
			$sales_home_link = '<a class="current" tabindex = "-1" href="'.site_url('sales').'">'.lang('module_sales').'</a>';
			
			if ($ci->Register->count_all() > 1)
			{
				$register_name = $ci->Register->get_info($ci->Employee->get_logged_in_employee_current_register_id())->name;
				
				if ($register_name)
				{
					$sales_home_link.='<a tabindex = "-1" class="current" href="'.site_url('sales/clear_register').'">'.$register_name.' ['.lang('sales_change_register').']</a>';
				}
			}
		}
		else
		{
			$sales_home_link = '<a tabindex = "-1" href="'.site_url('sales').'">'.lang('module_sales').'</a>';
		}
		
		$return.=$sales_home_link;
		if($ci->uri->segment(2) == 'suspended')
		{
			$return.=create_current_page_url(lang('sales_suspended_sales'));
		}
		
		if($ci->uri->segment(2) == 'batch_sale')
		{
			$return.=create_current_page_url(lang('batch_sale'));
		}
		
		if($ci->uri->segment(2) == 'register_add_subtract' && $ci->uri->segment(3) == 'add')
		{
			$return.=create_current_page_url(lang('sales_add_cash_to_register'));
		}

		if($ci->uri->segment(2) == 'register_add_subtract' && $ci->uri->segment(3) == 'subtract')
		{
			$return.=create_current_page_url(lang('common_remove_cash_from_register'));
		}
		
		if($ci->uri->segment(2) == 'closeregister')
		{
			$return.=create_current_page_url(lang('sales_close_register'));
		}
		
		if($ci->uri->segment(2) == 'new_giftcard')
		{
			$return.=create_current_page_url(lang('sales_new_giftcard'));
		}
		
		if($ci->uri->segment(2) == 'receipt' || $ci->uri->segment(2) == 'complete' || $ci->uri->segment(2) == 'declined')
		{
			$return.=create_current_page_url(lang('sales_receipt'));
		}
		
		if ($ci->uri->segment(2) == 'open_drawer')
		{
			$return.=create_current_page_url(lang('common_pop_open_cash_drawer'));
		}
		if($ci->uri->segment(2) == 'customer_display')
		{
			$return.=create_current_page_url(lang('sales_customer_facing_display'));
		}
		
		if($ci->uri->segment(2) == 'fulfillment')
		{
			$return.=create_current_page_url(lang('sales_fulfillment_sheet'));
		}
		
		if($ci->uri->segment(2) == 'edit')
		{
			$return.=create_current_page_url(lang('sales_edit_sale'));
		}

		if($ci->uri->segment(2) == 'delete')
		{
			$return.=create_current_page_url(lang('module_action_delete_sale'));
		}

		if($ci->uri->segment(2) == 'undelete')
		{
			$return.=create_current_page_url(lang('sales_undelete_entire_sale'));
		}
		
		if ($ci->uri->segment(2)=='start_cc_processing')
		{
			$return.=create_current_page_url(lang('sales_process_credit_card'));
		}
		
		if ($ci->uri->segment(2)=='finish_cc_processing')
		{
			$return.=create_current_page_url(lang('sales_process_credit_card'));
		}

	}
	elseif ($ci->uri->segment(1) == 'receivings')
	{
		
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$receivings_home_link = '<a class="current" tabindex = "-1" href="'.site_url('receivings').'">'.lang('module_receivings').'</a>';
		}
		else
		{
			$receivings_home_link = '<a tabindex = "-1" href="'.site_url('receivings').'">'.lang('module_receivings').'</a>';
		}
		
		$return.=$receivings_home_link;
		if($ci->uri->segment(2) == 'suspended')
		{
			$return.=create_current_page_url(lang('common_suspended_receivings'));
		}
		
		if($ci->uri->segment(2) == 'batch_receiving')
		{
			$return.=create_current_page_url(lang('batch_receivings'));
		}
		
		if($ci->uri->segment(2) == 'receipt' || $ci->uri->segment(2) == 'complete')
		{
			$return.=create_current_page_url(lang('receivings_receipt'));
		}
		
		if($ci->uri->segment(2) == 'po')
		{
			$return.=create_current_page_url(lang('receivings_create_purchase_order'));
		}
	}
	elseif($ci->uri->segment(1) == 'expenses')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$suppliers_home_link =create_current_page_url(lang('module_expenses'));
		}
		else
		{
			$suppliers_home_link = '<a tabindex = "-1" href="'.site_url('expenses').'">'.lang('module_expenses').'</a>';
		}
				
		$return.=$suppliers_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			if ($ci->uri->segment(3) == -1)
			{
  				$return.=create_current_page_url(lang('expenses_new'));
			}
			else
			{
  				$return.=create_current_page_url(lang('expenses_update'));
			}
		}
        }
	elseif ($ci->uri->segment(1) == 'timeclocks')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$timeclock_home_link =create_current_page_url(lang('employees_timeclock'));
		}
		else
		{
			$timeclock_home_link = '<a tabindex = "-1" href="'.site_url('timeclocks').'">'.lang('employees_timeclock').'</a>';
		}
		
		$return.=$timeclock_home_link;
		
		if($ci->uri->segment(2) == 'view')
		{
			$return.=create_current_page_url(lang('common_edit'));
		}
		
		if($ci->uri->segment(2) == 'punches')
		{
			$return.=create_current_page_url(lang('timeclocks_my_punches'));
		}
		
		
	}
	elseif($ci->uri->segment(1) == 'messages')
	{

		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$messages_home_link =create_current_page_url(lang('module_messages'));
		}
		else
		{
			$messages_home_link = '<a tabindex = "-1" href="'.site_url('messages').'">'.lang('module_messages').'</a>';
		}
		
		$return.=$messages_home_link;
		
		
		if($ci->uri->segment(2) == 'send_message')
		{
  			$return.=create_current_page_url(lang('employees_send_message'));
		}
		
		if($ci->uri->segment(2) == 'sent_messages')
		{
  			$return.=create_current_page_url(lang('messages_sent_messages'));
		}
		
	}
	elseif($ci->uri->segment(1) == 'deliveries')
	{
		if ($ci->uri->segment(2) == NULL) //Main page
		{
			$return.=create_current_page_url(lang('deliveries_orders'));
		}

		if ($ci->uri->segment(2)=='view')
		{
			$return.= '<a tabindex = "-1" href="'.site_url('deliveries').'">'.lang('deliveries_orders').'</a>';
			$return.=create_current_page_url(lang('common_edit'));
		}
	
	}
  	return $return;
}
function create_current_page_url($link_text)
{
	return '<a tabindex = "-1" class="current" href="'.current_url().'">'.$link_text.'</a>';
}

function create_report_breadcrumb($report_name)
{
	$ci = &get_instance();

	$return = '';
	if ($ci->uri->segment(3) === NULL) // Input page
	{
		$return.=create_current_page_url(lang('reports_report_input').': '.$report_name);
	}
	else
	{
		$return.= '<a tabindex = "-1" href="'.site_url('reports/'.$ci->uri->segment(2)).'">'.lang('reports_report_input').': '.$report_name.'</a>';	
		$return.= create_current_page_url($report_name);
	}
	
	return $return;
}

?>