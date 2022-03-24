<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/


$route['default_controller'] = "login";
$route['404_override'] = '';
$route['no_access/(:any)'] = "no_access/index/$1";

//Summary reports inputs
$route['reports/summary_sales'] = "reports/date_input_excel_export_compare";
$route['reports/summary_categories'] = "reports/date_input_excel_export_compare";
$route['reports/summary_tags'] = "reports/date_input_excel_export_compare";
$route['reports/summary_customers'] = "reports/date_input_excel_export_customers";
$route['reports/summary_suppliers'] = "reports/date_input_excel_export";
$route['reports/summary_items'] = "reports/summary_items_input";
$route['reports/summary_item_kits'] = "reports/date_input_excel_export_compare";
$route['reports/summary_employees'] = "reports/employees_date_input_excel_export";
$route['reports/summary_taxes'] = "reports/date_input_excel_export_compare";
$route['reports/summary_taxes_receivings'] = "reports/date_input_excel_export_compare";
$route['reports/summary_sales_time'] = "reports/date_input_excel_export_time";


$route['reports/summary_discounts'] = "reports/date_input_excel_export";
$route['reports/summary_payments'] = "reports/date_input_excel_export_compare";
$route['reports/summary_giftcards'] = "reports/excel_export";
$route['reports/giftcard_audit'] = "reports/giftcards_audit_input";

$route['reports/summary_store_accounts'] = "reports/excel_export_store_account_summary_input";
$route['reports/store_account_outstanding'] = "reports/excel_export_customer_outstanding";
$route['reports/store_account_activity'] = "reports/date_input_excel_export_store_account_activity";
$route['reports/summary_profit_and_loss'] = "reports/date_input_profit_and_loss";
$route['reports/summary_commissions'] = "reports/employees_date_input_excel_export";
$route['reports/detailed_expenses'] = "reports/date_input_no_sales";
$route['reports/summary_expenses'] = "reports/date_input_no_sales";
$route['reports/summary_timeclock'] = "reports/timeclock_input";
$route['reports/summary_tiers'] = "reports/date_input_excel_export";
$route['reports/closeout'] = "reports/date_input_no_sales";
$route['reports/summary_suppliers_receivings'] = 'reports/date_input_excel_export';
$route['reports/summary_giftcard_sales'] = 'reports/date_input_excel_export';
$route['reports/summary_items_variance'] = 'reports/date_input_excel_export';
$route['reports/summary_item_kits_variance'] = 'reports/date_input_excel_export';
$route['reports/summary_manufacturers'] = 'reports/date_input_excel_export_compare';

//Graphical reports inputs
$route['reports/graphical_summary_sales'] = "reports/date_input";
$route['reports/graphical_summary_sales_time'] = "reports/date_input_time";
$route['reports/graphical_summary_items'] = "reports/summary_items_input_graphical";
$route['reports/graphical_summary_item_kits'] = "reports/date_input";
$route['reports/graphical_summary_categories'] = "reports/date_input";
$route['reports/graphical_summary_tags'] = "reports/date_input";
$route['reports/graphical_summary_suppliers'] = "reports/date_input";
$route['reports/graphical_summary_employees'] = "reports/employees_date_input";
$route['reports/graphical_summary_taxes'] = "reports/date_input";
$route['reports/graphical_summary_customers'] = "reports/date_input_customers";
$route['reports/graphical_summary_discounts'] = "reports/date_input";
$route['reports/graphical_summary_payments'] = "reports/date_input";
$route['reports/graphical_summary_commissions'] = "reports/employees_date_input";
$route['reports/graphical_summary_suppliers_receivings'] = "reports/date_input";
$route['reports/graphical_summary_taxes_receivings'] = "reports/date_input";
$route['reports/graphical_summary_manufacturers'] = 'reports/date_input';

//Inventory report inputs
$route['reports/inventory_low'] = "reports/inventory_input";
$route['reports/inventory_summary'] = "reports/inventory_input";
$route['reports/summary_count_report'] = "reports/date_input_no_sales";
$route['reports/detailed_count_report'] = "reports/date_input_no_sales";
$route['reports/expiring_inventory'] = 'reports/date_input_no_sales_expire';

//Detailed report inputs
$route['reports/detailed_register_log'] = 'reports/date_input_no_sales';
$route['reports/detailed_sales'] = "reports/date_input_excel_export";
$route['reports/detailed_receivings'] = "reports/specific_supplier_input";
$route['reports/detailed_suspended_receivings'] = "reports/specific_supplier_input";
$route['reports/deleted_recevings'] = "reports/date_input_excel_export";
$route['reports/detailed_giftcards'] = "reports/detailed_giftcards_input";
$route['reports/specific_customer'] = "reports/specific_customer_input";
$route['reports/specific_customer_store_account'] = "reports/specific_customer_store_account_input";
$route['reports/store_account_statements'] = "reports/store_account_statements_input";
$route['reports/specific_employee'] = "reports/specific_employee_input";
$route['reports/specific_supplier'] = "reports/specific_supplier_input";
$route['reports/deleted_sales'] = "reports/date_input_excel_export";
$route['reports/detailed_profit_and_loss'] = "reports/date_input_profit_and_loss";
$route['reports/detailed_inventory'] = "reports/detailed_inventory_input";
$route['reports/detailed_suspended_sales'] = "reports/suspended_date_input_excel_export";
$route['reports/detailed_payments'] = "reports/date_input_excel_export";
$route['reports/detailed_commissions'] = "reports/specific_employee_input";
$route['reports/detailed_timeclock'] = "reports/timeclock_input";
$route['reports/specific_supplier_receivings'] = 'reports/specific_supplier_input';

//Store accounts suppliers
$route['reports/supplier_store_account_statements'] = "reports/supplier_store_account_statements_input";
$route['reports/supplier_summary_store_accounts'] = "reports/supplier_excel_export_store_account_summary_input";
$route['reports/supplier_specific_store_account'] = "reports/supplier_specific_store_account_input";
$route['reports/supplier_store_account_activity'] = "reports/supplier_store_account_activity_input";
$route['reports/supplier_store_account_outstanding'] = "reports/supplier_store_account_outstanding_input";

//Recv payments
$route['reports/receivings_summary_payments'] = "reports/date_input_excel_export_compare";
$route['reports/receivings_detailed_payments'] = "reports/date_input_excel_export";
$route['reports/receivings_graphical_summary_payments'] = "reports/date_input";

/* End of file routes.php */
/* Location: ./application/config/routes.php */