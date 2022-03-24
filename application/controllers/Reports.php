<?php
require_once ("Secure_area.php");
class Reports extends Secure_area 
{	
	function __construct()
	{
		parent::__construct('reports');
		$this->load->helper('report');
		$this->has_profit_permission = $this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id);
		$this->has_cost_price_permission = $this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id);
		
		//Need to query database directly as load config hook doesn't happen until after constructor
		$this->decimals = $this->Appconfig->get_raw_number_of_decimals();
		$this->decimals = $this->decimals !== NULL && $this->decimals!= '' ? $this->decimals : 2;
		require_once (APPPATH.'models/reports/Report.php');
		$this->load->vars(array('reports_selected_location_ids' => Report::get_selected_location_ids()));
		$this->lang->load('reports');
		$this->lang->load('module');
	}
		
	function set_selected_location_ids()
	{
		$this->session->set_userdata('reports_selected_location_ids', $this->input->post('reports_selected_location_ids'));
	}
		
	//Initial report listing screen
	function index()
	{
		$this->load->view("reports/listing",array());	
	}

	// Sales Generator Reports 
	function sales_generator() 
	{			
		$this->load->model('Category');
		
		$this->check_action_permission('view_sales_generator');
		
		if ($this->input->get('act') == 'autocomplete') 
		{ // Must return a json string
			if ($this->input->get('w') != '') { // From where should we return data
				if ($this->input->get('term') != '') { // What exactly are we searchin
					
					//allow parallel searchs to improve performance.
					session_write_close();
					
					switch($this->input->get('w')) {
						case 'customers': 
						$this->load->model('Customer');
							$t = $this->Customer->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
							$tmp = array();
							foreach ($t as $k=>$v) { 
								$display_name = $v->last_name.", ".$v->first_name;
								
								if ($v->email)
								{
									$display_name.=" - ".$v->email;
								}

								if ($v->phone_number)
								{
									$display_name.=" - ".$v->phone_number;
								}
								
								$tmp[$k] = array('id'=>$v->person_id, 'name'=>$display_name); 
							}
							die(json_encode($tmp));
						break;
						case 'employees':
						case 'salesPerson':
							$t = $this->Employee->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
							die(json_encode($tmp));
						break;
						case 'itemsCategory':
							$this->load->model('Category');
							$t = $this->Category->get_search_suggestions($this->input->get('term'));
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v['id'], 'name'=>$v['label']); }
							die(json_encode($tmp));
						break;
						case 'itemsTag':
							$this->load->model('Tag');
							$t = $this->Tag->get_tag_suggestions($this->input->get('term'));
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v['id'], 'name'=>$v['label']); }
							die(json_encode($tmp));
						break;
						case 'manufacturer':
							$this->load->model('Manufacturer');
							$t = $this->Manufacturer->get_manufacturer_suggestions($this->input->get('term'));
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v['id'], 'name'=>$v['label']); }
							die(json_encode($tmp));
						break;
						case 'suppliers':
							$this->load->model('Supplier');
							$t = $this->Supplier->search($this->input->get('term'), 100, 0, 'last_name', 'asc')->result_object();
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->company_name." - ".$v->email); }
							die(json_encode($tmp));
						break;
						case 'itemsKitName':
							$this->load->model('Item_kit');
							$t = $this->Item_kit->search($this->input->get('term'), FALSE, 100, 0, 'name', 'asc')->result_object();
							$tmp = array();
							foreach ($t as $k=>$v) { $tmp[$k] = array('id'=>$v->item_kit_id, 'name'=>$v->name." / #".$v->item_kit_number); }
							die(json_encode($tmp));
						break;
						case 'itemsName':
						$this->load->model('Item');
						$t = $this->Item->get_item_search_suggestions($this->input->get('term'));
						$tmp = array();
						foreach ($t as $v) { $tmp[] = array('id'=>$v['value'], 'name'=>$v['label']); }
						die(json_encode($tmp));
						break;
						case 'paymentType':
							$t = array(lang('common_cash'),lang('common_check'), lang('common_giftcard'),lang('common_debit'),lang('common_credit'));
							
							if($this->config->item('customers_store_accounts')) 
							{
								$t[] =lang('common_store_account');
							}
							
							foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
							{
								$t[] = $additional_payment_type;
							}

							$tmp = array();
							foreach ($t as $k => $v) { $tmp[$k] = array('id'=>$v, 'name'=>$v); }
							die(json_encode($tmp));
						break;		
					}
				} else {
					die;	
				}
			} else {
				die(json_encode(array('value' => 'No such data found!')));
			}
		}		
		
		$data = $this->_get_common_report_data();
		$data["title"] = lang('reports_sales_generator');
		$data["subtitle"] = lang('reports_sales_report_generator');
		
		$setValues = array(	'report_type' => '', 'sreport_date_range_simple' => '', 
										'start_month' => date("m"), 'start_day' => date('d'), 'start_year' => date("Y"),
										'end_month' => date("m"), 'end_day' => date('d'), 'end_year' => date("Y"),
										'matchType' => '',
										'matched_items_only' => FALSE,
										'tax_exempt' => FALSE,
										);
		foreach ($setValues as $k => $v) { 
			if (empty($v) && !isset($data[$k])) { 
				$data[$k] = ''; 		
			} else {
				$data[$k] = $v;
			}
		}		
		if ($this->input->get('generate_report')) { // Generate Custom Raport
			$data['report_type'] = $this->input->get('report_type');
			$data['sreport_date_range_simple'] = $this->input->get('report_date_range_simple');
			
			
			if ($data['report_type'] == 'simple') {
				$q = explode("/", $data['sreport_date_range_simple']);
				list($data['start_year'], $data['start_month'], $data['start_day']) = explode("-", $q[0]);
				list($data['end_year'], $data['end_month'], $data['end_day']) = explode("-", $q[1]);
				
				
			
			}
			else
			{
				list($data['start_year'], $data['start_month'], $data['start_day']) = explode("-", $this->input->get('start_date'));
				list($data['end_year'], $data['end_month'], $data['end_day']) = explode("-", $this->input->get('end_date'));
				
				
			}
			$data['matchType'] = $this->input->get('matchType');
			$data['matched_items_only'] = $this->input->get('matched_items_only') ? TRUE : FALSE;
			$data['tax_exempt'] = $this->input->get('tax_exempt') ? TRUE : FALSE;

			$data['field'] = $this->input->get('field');
			$data['condition'] = $this->input->get('condition');
			$data['value'] = $this->input->get('value');
			
			$data['prepopulate'] = array();
			
			$field = $this->input->get('field');
			$condition = $this->input->get('condition');
			$value = $this->input->get('value');
			
			$tmpData = array();
			foreach ($field as $a => $b) {
				$uData = explode(",",$value[$a]);
				$tmp = $tmpID = array();
				switch ($b) {
					case '1': // Customer
						$this->load->model('Customer');
						$t = $this->Customer->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->person_id; $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;
					case '2': // Item Serial Number
						$tmpID[] = $value[$a];
					break;
					case '3': // Employees
						$t = $this->Employee->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;
					case '4': // Items Category
					$this->load->model('Category');
					$t = $this->Category->get_multiple_info($uData)->result_object();
					foreach ($t as $k=>$v) { $tmpID[] = $v->id;  $tmp[$k] = array('id'=>$v->id, 'name'=>$v->name); }
					break;
					case '5': // Suppliers 
						$this->load->model('Supplier');
						$t = $this->Supplier->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->company_name." - ".$v->email); }
					break;
					case  '6': // Sale Type
						$tmpID[] = $condition[$a];
					break;
					case '7': // Sale Amount
						$tmpID[] = $value[$a];
					break;
					case '8': // Item Kits
						$this->load->model('Item_kit');
						$t = $this->Item_kit->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->item_kit_id;  $tmp[$k] = array('id'=>$v->item_kit_id, 'name'=>$v->name." / #".$v->item_kit_number); }
					break;
					case '9': // Items Name
						$this->load->model('Item');
						$t = $this->Item->get_multiple_info($uData)->result_object();
						foreach ($t as $k => $v) { $tmpID[] = $v->item_id;  $tmp[$k] = array('id'=>$v->item_id, 'name'=>$v->name); }
					break;				
					case '10': // SaleID
						if(strpos(strtolower($value[$a]), strtolower($this->config->item('sale_prefix'))) !== FALSE)
						{							
							$value[$a] =(int)substr(strtolower($value[$a]), strpos(strtolower($value[$a]),$this->config->item('sale_prefix').' ') + strlen(strtolower($this->config->item('sale_prefix')).' '));	
						}
						$tmpID[] = $value[$a];
					break;
					case '11': // Payment type
						foreach ($uData as $k=>$v) { $tmpID[] = $v;  $tmp[$k] = array('id'=>$v, 'name'=>$v); }
					break;
					
					case '12': // Sale Item Description
						$tmpID[] = $value[$a];
					break;
					case '13': // Employees
						$t = $this->Employee->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->person_id;  $tmp[$k] = array('id'=>$v->person_id, 'name'=>$v->last_name.", ".$v->first_name." - ".$v->email); }
					break;
					case '14': // Items Tag
						$this->load->model('Tag');
						$t = $this->Tag->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->id;  $tmp[$k] = array('id'=>$v->id, 'name'=>$v->name); }
					break;				
					case '15': // Manufactor
						$this->load->model('Manufacturer');
						$t = $this->Manufacturer->get_multiple_info($uData)->result_object();
						foreach ($t as $k=>$v) { $tmpID[] = $v->id;  $tmp[$k] = array('id'=>$v->id, 'name'=>$v->name); }
						
					break;				
					
				}
				$data['prepopulate']['field'][$a][$b] = $tmp;			

				// Data for sql
				$tmpData[] = array('f' => $b, 'o' => $condition[$a], 'i' => $tmpID);
			}
			
			$params['matchType'] = $data['matchType'];
			$params['matched_items_only'] = $data['matched_items_only'];
			$params['tax_exempt'] = $data['tax_exempt'];
			$params['ops'] = array(
				1 => " = 'xx'", 
				2 => " != 'xx'", 
				5 => " IN ('xx')", 
				6 => " NOT IN ('xx')", 
				7 => " > xx", 
				8 => " < xx", 
				9 => " = xx",
				10 => '', // Sales
				11 => '', // Returns
				14 => " IN ('xx')", 
				15 => " IN ('xx')", 
			);

			$params['tables'] = array(
				1 => 'sales.customer_id', // Customers
				2 => 'sales_items.serialnumber', // Item Sale Serial number
				3 => 'sales.employee_id', // Employees
				4 => 'items.category_id', // Item Category
				5 => 'suppliers.person_id', // Suppliers
				6 => '', // Sale Type
				7 => '', // Sale Amount
				8 => 'item_kits.item_kit_id', // Item Kit Name
				9 => 'items.item_id', // Item Name
				10 => 'sales.sale_id', // Sale ID
				11 => 'sales.payment_type', // Payment Type
				12 => 'sales.description', // Item Sale Serial number
				13 => 'sales.sold_by_employee_id', // Item Sale Serial number
				14 => '', // TAGS
				15 => '', // Manufactor
				);			
			$params['values'] = $tmpData;
			$params['offset'] = $this->input->get('per_page')  ? $this->input->get('per_page') : 0;
			$offset=$params['offset'];
			$params['export_excel'] = $this->input->get('export_excel') ? 1 : 0;
			
			$export_excel=$params['export_excel'];
			$this->load->model('reports/Sales_generator');
			$model = $this->Sales_generator;
			$model->setParams($params);		
			
			// Sales Interval Reports
			$interval = 
			array(
				'start_date' => $data['start_year'].'-'.$data['start_month'].'-'.$data['start_day'], 
				'end_date' => $data['end_year'].'-'.$data['end_month'].'-'.$data['end_day']. ' 23:59:59'
				);							
							
			$this->load->model('Sale');
			$config = array();
			
			//Remove per_page from url so we don't have it duplicated
			$config['base_url'] = preg_replace('/&per_page=[0-9]*/','',current_url());
			$config['total_rows'] = $model->getTotalRows();
			$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
			$config['page_query_string'] = TRUE;
			$this->load->library('pagination');$this->pagination->initialize($config);
			
			$tabular_data = array();
			$report_data = $model->getData();
			
			$summary_data = array();
			$details_data = array();
			
			$location_count = count(Report::get_selected_location_ids());			
			
			foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
			{
				$summary_data_row = array();				
				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left', 'sale_id' => $row['sale_id']);				
				if ($location_count > 1)
				{
					$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
				}
				
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'center');
				$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
				$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
				
				if($this->has_profit_permission)
				{
					$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
				}
								
				$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
				$summary_data_row[] = array('data'=>$row['discount_reason'], 'align'=>'right');
			
				
				$summary_data[$key] = $summary_data_row;
				
				if($export_excel == 1)
				{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
				
					$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['item_product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['item_name'], 'align'=>'left');
				
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['current_selling_price']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					
					if($this->has_cost_price_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['cost_prices']), 'align'=>'right');
					}
									
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
					
				}
			}
			}
			$reportdata = array(
				"title" => lang('reports_sales_generator'),
				"subtitle" => lang('reports_sales_report_generator')." - ".date(get_date_format(), strtotime($interval['start_date'])) .'-'.date(get_date_format(), strtotime($interval['end_date']))." - ".$config['total_rows'].' '.lang('reports_sales_report_generator_results_found'),
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"overall_summary_data" => $model->getSummaryData(),
				'pagination' => $this->pagination->create_links(),
				'export_excel' =>$this->input->get('export_excel'),
				'report_model' =>"sales_generator",
				'params'=>$params
			);
			
			// Fetch & Output Data 
			
			if (!$this->input->get('export_excel'))
			{
				$data['results'] = $this->load->view("reports/sales_generator_tabular_details", $reportdata, true);	
			}
		}	
		
		if (!$this->input->get('export_excel'))
		{
			$this->load->view("reports/sales_generator",$data);
		}
		else //Excel export use regular tabular_details
		{
			$this->load->view("reports/tabular_details_lazy_load",$reportdata);
		}
	}	
	

	
	function _get_common_report_data($time=false)
	{
		$data = array();
		$data['report_date_range_simple'] = get_simple_date_ranges($time);
		$data['months'] = get_months();
		$data['days'] = get_days();
		$data['years'] = get_years();
		$data['hours'] = get_hours($this->config->item('time_format'));
		$data['minutes'] = get_minutes();
		$data['selected_month']=date('m');
		$data['selected_day']=date('d');
		$data['selected_year']=date('Y');
		$data['intervals'] = get_time_intervals();	
	
		return $data;
	}
	
	function _get_simple_date_ranges_expire()
	{	
		$data = array();
		$data['report_date_range_simple'] = get_simple_date_ranges_expire();
		$data['months'] = get_months();
		$data['days'] = get_days();
		$data['years'] = get_years();
		$data['hours'] = get_hours($this->config->item('time_format'));
		$data['minutes'] = get_minutes();
		$data['selected_month']=date('m');
		$data['selected_day']=date('d');
		$data['selected_year']=date('Y');
		$data['intervals'] = get_time_intervals();
		
		return $data;	

	}
	
	//Input for reports that require only a date range and an export to excel. (see routes.php to see that all summary reports route here)
	function date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export",$data);	
	}
	
	function date_input_excel_export_customers()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_customers",$data);			
	}
	
	function excel_export_customer_outstanding()
	{
		$data = array();
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view('reports/excel_export_customer_outstanding',$data);
	}
	
	function date_input_excel_export_compare()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_compare",$data);	
		
	}
	
	function date_input_excel_export_time()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_time",$data);	
	}
	
	function date_input_excel_export_store_account_activity()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_store_account_activity",$data);	
	}

	function date_input_no_sales_no_excel_with_time()
	{		
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_no_sales_no_excel_with_time",$data);	
	}
	
	function suspended_date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		
		$this->load->view("reports/suspended_date_input_excel_export",$data);	
	}
	
	function employees_date_input_excel_export()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/employees_date_input_excel_export",$data);	
		
	}
	
	/** added for register log */
	function date_input_no_sales()
	{
		$data = $this->_get_common_report_data();
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/date_input_no_sales",$data);	
	}
	
	function date_input_no_sales_expire()
	{
		$data = $this->_get_simple_date_ranges_expire();
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/date_input_no_sales",$data);	
	}
	
	/** also added for register log */
	
	function detailed_register_log($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$this->check_action_permission('view_register_log');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_register_log');
		$model = $this->Detailed_register_log;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_register_log/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		
		$summary_data = array();
			
		foreach($report_data as $row)
		{
			$details ='';
			if($row['shift_end']=='0000-00-00 00:00:00')
			{
				$shift_end='<span class="text-danger">'.lang('reports_register_log_open').'</span>';
				$delete=anchor('reports/delete_register_log/'.$row['register_log_id'].'/'.$start_date.'/'. $end_date, lang('common_delete'), 
				"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_register_log_delete')).", this)'");
			}
			else
			{
				$shift_end=date(get_date_format(), strtotime($row['shift_end'])) .' '.date(get_time_format(), strtotime($row['shift_end']));
				$delete=anchor('reports/delete_register_log/'.$row['register_log_id'].'/'.$start_date.'/'. $end_date, lang('common_delete'), 
				"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_register_log_delete')).", this)'");

			}

			$details = anchor('reports/edit_register_log/'.$row['register_log_id'], lang('common_edit')).', ';
			
			$details .= anchor('reports/register_log_details/'.$row['register_log_id'], lang('common_det')); 
			
			$summary_data[] = array(
				array('data'=>$delete, 'align'=>'left'), 
				array('data'=>$details, 'align'=>'left'), 
				array('data'=>$row['register_name'], 'align'=>'left'), 
				array('data'=>$row['open_first_name'] . ' ' . $row['open_last_name'], 'align'=>'left'), 
				array('data'=>$row['close_first_name'] . ' ' . $row['close_last_name'], 'align'=>'left'), 
				array('data'=>date(get_date_format(), strtotime($row['shift_start'])) .' '.date(get_time_format(), strtotime($row['shift_start'])), 'align'=>'left'), 
				array('data'=>$shift_end, 'align'=>'left'), 
				array('data'=>to_currency($row['open_amount']), 'align'=>'right'), 
				array('data'=>to_currency($row['close_amount']), 'align'=>'right'), 
				array('data'=>to_currency($row['cash_sales_amount']), 'align'=>'right'),
				array('data'=>to_currency($row['total_cash_additions']), 'align'=>'right'),
				array('data'=>to_currency($row['total_cash_subtractions']), 'align'=>'right'),
				array('data'=>to_currency($row['difference']), 'align'=>'right'),
				array('data'=>$row['notes'], 'align'=>'left')
			);			
		}

		$data = array(
			"title" =>lang('reports_register_log_title'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $summary_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular", $data);
	}
	
	function register_log_details($id)
	{
		$this->check_action_permission('view_register_log');
		
		$data = array(
			'register_log' => $this->Register->get_register_log($id),
			'register_log_details' => $this->Register->get_register_log_details($id)
		);
		
		$this->load->view('reports/register_log_details', $data);
	}
	
	function summary_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$this->check_action_permission('view_inventory_reports');
		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_inventory_count_report');
		$model = $this->Summary_inventory_count_report;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());

		$summary_data = array();
			
		foreach($report_data as $row)
		{
			$status = '';
			switch($row['status'])
			{
				case 'open':
					$status = lang('common_open');
				break;
	
				case 'closed':
					$status = lang('common_closed');
				break;
			}
			$tabular_data_row = array(
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'), 
				array('data'=>$status, 'align'=>'left'), 
				array('data'=>$row['employee_name'], 'align'=>'left'), 
				array('data'=>to_quantity($row['items_counted']), 'align'=>'left'), 
				array('data'=>to_quantity($row['difference']), 'align'=>'left'), 
				array('data'=>to_currency($row['cost_price_difference']), 'align'=>'left'), 
				array('data'=>$row['comment'], 'align'=>'left'), 
			);
			
		
			if ($location_count > 1)
			{
				array_unshift($tabular_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			
			$summary_data[] = $tabular_data_row;			
		}

		$data = array(
			"title" =>lang('reports_summary_count_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $summary_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular", $data);
	}
	
	function detailed_count_report($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('Category');
		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_inventory_count_report');
		$model = $this->Detailed_inventory_count_report;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_count_report/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data['summary'] as $key=>$row)
		{
			$status = '';
			switch($row['status'])
			{
				case 'open':
					$status = lang('common_open');
				break;
	
				case 'closed':
					$status = lang('common_closed');
				break;
			}
			
			$summary_data_row = array(
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['count_date'])), 'align'=>'left'), 
				array('data'=>$status, 'align'=>'left'), 
				array('data'=>$row['employee_name'], 'align'=>'left'), 
				array('data'=>to_quantity($row['items_counted']), 'align'=>'left'), 
				array('data'=>to_quantity($row['difference']), 'align'=>'left'), 
				array('data'=>to_currency($row['cost_price_difference']), 'align'=>'left'), 
				array('data'=>$row['comment'], 'align'=>'left'), 
			);	
		
			if ($location_count > 1)
			{
				array_unshift($summary_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			
			$summary_data[$key] = $summary_data_row;
			
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['count']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['actual_quantity']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['cost_price']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['cost_price_difference']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['comment'], 'align'=>'left');
				$details_data[$key][] = $details_data_row;
			}
			
		}

		$data = array(
			"title" =>lang('reports_detailed_count_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"details_data" => $details_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular_details", $data);
	}
	
	function delete_register_log($register_log_id,$start_date,$end_date)
	{
		$this->load->model('reports/Detailed_register_log');
		if($this->Detailed_register_log->delete_register_log($register_log_id))
		{
			redirect('reports/detailed_register_log/'.$start_date.'/'.$end_date);
		}
	}

	function edit_register_log($register_log_id)
	{
		redirect('sales/edit_register/'.$register_log_id);
	}
	
	function summary_sales_time($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $interval, $export_excel=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		
		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval,'export_excel' => $export_excel));
		
		$tabular_data = array();
		$report_data = $model->getData();
		
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_sales_time;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'interval' => $interval,'export_excel' => $export_excel));
						
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$time_range_to_compare_to = $row['time_range'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['time_range'] == $time_range_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$row['time_range'], 'align'=>'left');
			$data_row[] = array('data'=>$row['number_of_transactions'].($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['number_of_transactions'] >= $row['number_of_transactions'] ? ($row['number_of_transactions'] == $row_compare['number_of_transactions'] ?  '' : 'compare_better') : 'compare_worse').'">'.$row_compare['number_of_transactions'] .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		
		$data = array(
			"title" => lang('reports_sales_summary_by_time_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => '',
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function graphical_summary_sales_time($start_date, $end_date, $sale_type,$interval)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date).' 23:59:59';

		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval));

		$data = array(
			"title" => lang('reports_sales_summary_by_time_report'),
			"graph_file" => site_url("reports/graphical_summary_sales_time_graph/$start_date/$end_date/$sale_type/$interval"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
		
	}

	//The actual graph data
	function graphical_summary_sales_time_graph($start_date, $end_date, $sale_type,$interval)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_sales_time');
		$model = $this->Summary_sales_time;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'interval' => $interval));
		
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['time_range']] = to_quantity($row['number_of_transactions']);
		}
				

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>:  <%=value %>",
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	
	//Summary sales report
	function summary_sales($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$config = array();
		$config['base_url'] = site_url("reports/summary_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$tabular_data = array();

		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_sales;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		$index = 0;
		foreach($report_data as $row)
		{
			$data_row = array();
			if ($do_compare)
			{
				if (isset($report_data_compare[$index]))
				{
					$row_compare = $report_data_compare[$index];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row[] = array('data'=>date(get_date_format(), strtotime($row['sale_date'])).($do_compare && $row_compare ? ' / <span class="compare ">'.date(get_date_format(), strtotime($row_compare['sale_date'])).'</span>':''), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align'=>'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align'=>'right');
			}
			$tabular_data[] = $data_row;
			
			$index++;
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	//Summary tiers report
	function summary_tiers($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_tiers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_tiers');
		$model = $this->Summary_tiers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$config = array();
		$config['base_url'] = site_url("reports/summary_tiers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['tier_name'], 'align'=>'left');
			$data_row[] = array('data'=>$row['count'], 'align'=>'right');
			
			$tabular_data[] = $data_row;
		}
		$data = array(
			"title" => lang('reports_tiers_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary categories report
	function summary_categories($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		
		$this->check_action_permission('view_categories');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
 
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel'=>$export_excel, 'offset' => $offset));
		
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_categories/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_categories = array();
			
			foreach(array_keys($report_data) as $category_name)
			{
				$compare_to_category[] = $category_name;
			}

			$model_compare = $this->Summary_categories;		
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_categories' =>$compare_to_categories));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
			
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				
				if (isset($report_data_compare[$row['category']]))
				{
					$row_compare = $report_data_compare[$row['category']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$data_row[] = array('data'=>floatval($row['item_sold']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['item_sold'] >= $row['item_sold'] ? ($row['item_sold'] == $row_compare['item_sold'] ?  '' : 'compare_better') : 'compare_worse').'">'.floatval($row_compare['item_sold']) .'</span>':''), 'align' => 'right');
			$tabular_data[] = $data_row;				
		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}


	//Summary customers report
	function summary_customers($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_customers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_customers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$total_spent_condition/$total_spent_amount/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 9;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$no_customer = $model->getNoCustomerData();
		$report_data = array_merge($no_customer,$report_data);
		
		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['person_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['customer'], 'align' => 'left');
			$data_row[] = array('data'=>$row['phone_number'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] =  array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align' => 'right');
			}
			
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
			{
				$data_row[] = array('data'=>to_currency_no_money($row['points_used']), 'align' => 'right');
				$data_row[] = array('data'=>to_currency_no_money($row['points_gained']), 'align' => 'right');
			}
			elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
			{
			   $sales_until_discount = $this->config->item('number_of_sales_for_discount') - $row['current_sales_for_discount'];
				$data_row[] = array('data'=>to_quantity($sales_until_discount), 'align' => 'right');
			}
			
			$data_row[] = array('data'=>$row['count'], 'align' => 'left');
			
			$tabular_data[] = $data_row;				
		}

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary suppliers report
	function summary_suppliers($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_suppliers/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['supplier'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_suppliers_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	//Summary suppliers report
	function summary_suppliers_receivings($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_suppliers_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['supplier'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'left');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'left');
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	

	function summary_items_input()
	{
		$this->load->model('Category');
		$data = $this->_get_common_report_data(TRUE);
		$data['supplier_search_suggestion_url'] = site_url('reports/supplier_search');
		$data['hide_excel_export_and_compare'] = FALSE;
		
		$data['categories'] = array();
		$data['categories'][-1] =lang('common_all');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$this->load->view("reports/summary_items_input",$data);
	}
	
	
	function summary_items_input_graphical()
	{
		$this->load->model('Category');
		
		$data = $this->_get_common_report_data(TRUE);
		$data['supplier_search_suggestion_url'] = site_url('reports/supplier_search');
		$data['hide_excel_export_and_compare'] = TRUE;
		
		$data['categories'] = array();
		$data['categories'][-1] =lang('common_all');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$this->load->view("reports/summary_items_input",$data);
	}

	//Summary items report
	function summary_items($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all', $show_all_items = 0, $export_excel=0, $offset = 0)
	{
		$this->load->model('Category');
		$this->load->model('Sale');
		$this->check_action_permission('view_items');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel, 'show_all_items' => $show_all_items));

		$config = array();
		$config['base_url'] = site_url("reports/summary_items/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$supplier_id/$category_id/$sale_type/$show_all_items/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 13;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_items = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_items[] = $report_data[$k]['item_id'];
			}
			
			$model_compare = $this->Summary_items;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_items' => $compare_to_items, 'show_all_items' => $show_all_items));
						
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}


		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$item_id_to_compare_to = $row['item_id'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['item_id'] == $item_id_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align' => 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align' => 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['current_cost_price']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align' => 'right');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>':''), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;
		
		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary item kits report
	function summary_item_kits($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type = 'all', $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_item_kits');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel' =>$export_excel, 'offset' => $offset));

		$config = array();
		$config['base_url'] = site_url("reports/summary_item_kits/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_items = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_item_kits[] = $report_data[$k]['item_kit_id'];
			}
			
			$model_compare = $this->Summary_item_kits;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'export_excel' =>$export_excel, 'offset' => $offset, 'compare_to_item_kits' => $compare_to_item_kits));
						
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}


		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$item_kit_id_to_compare_to = $row['item_kit_id'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['item_kit_id'] == $item_kit_id_to_compare_to)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['quantity_purchased'] >= $row['quantity_purchased'] ? ($row['quantity_purchased'] == $row_compare['quantity_purchased'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_quantity($row_compare['quantity_purchased']) .'</span>':''), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$tabular_data[] = $data_row;
		
		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary employees report
	function summary_employees($start_date, $end_date, $sale_type, $employee_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_employees');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' =>$employee_type, 'export_excel' => $export_excel, 'offset' => $offset));

		$config = array();
		$config['base_url'] = site_url("reports/summary_employees/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['employee'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] =  array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align' => 'right');
			}
			
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	function summary_taxes_receivings($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');

		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_taxes_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_taxes_receivings;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['name']]))
				{
					$row_compare = $report_data_compare[$row['name']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			
			$tabular_data[] = array(array('data'=>$row['name'], 'align'=>'left'),array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) : ''), 'align'=>'left'),array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']).'</span>' : ''), 'align'=>'left'), array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align'=>'left'));
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
		
		
	}
	
	//Summary taxes report
	function summary_taxes($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date,  $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_taxes');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_taxes/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_taxes;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
						
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['name']]))
				{
					$row_compare = $report_data_compare[$row['name']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			
			$tabular_data[] = array(array('data'=>$row['name'], 'align'=>'left'),array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']).'</span>' : ''), 'align'=>'left'),array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']).'</span>' : ''), 'align'=>'left'), array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align'=>'left'));
		}
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	//Summary discounts report
	function summary_discounts($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_discounts');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel' => $export_excel, 'offset' => $offset));

		$config = array();
		$config['base_url'] = site_url("reports/summary_discounts/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['discount'], 'align'=>'left'),array('data'=>$row['summary'], 'align'=>'left'));
		}

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	function store_account_activity($start_date, $end_date, $export_excel=0, $offset=0)
	{
		$this->check_action_permission('view_store_account');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Store_account_activity');
		$model = $this->Store_account_activity;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset'=> $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/store_account_activity/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
									array('data'=>$row['first_name'].' '.$row['last_name'], 'align'=> 'left'),
									array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
									array('data'=>$row['sale_id'] ? anchor('sales/receipt/'.$row['sale_id'], $this->config->item('sale_prefix').' '.$row['sale_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
									array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
									array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
									array('data'=>to_currency($row['balance']), 'align'=> 'right'),
									array('data'=>$row['items'], 'align'=> 'left'),
									array('data'=>$row['comment'], 'align'=> 'left'));
									
		}

		$data = array(
			"title" => lang('reports_store_account_activity_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function summary_payments($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_payments');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_payments;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));

			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['payment_type']]))
				{
					$row_compare = $report_data_compare[$row['payment_type']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$tabular_data[] = array(array('data'=>$row['payment_type'], 'align'=>'left'),array('data'=>to_currency($row['payment_amount']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['payment_amount'] >= $row['payment_amount'] ? ($row['payment_amount'] == $row_compare['payment_amount'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['payment_amount']) .'</span>':''), 'align'=>'right'));
		}
		
		
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	function date_input()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input",$data);
	}
	
	function date_input_customers()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input_customers",$data);		
	}
	
	function date_input_time()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input_time",$data);
	}
	
	function timeclock_input()
	{
		$data = $this->_get_common_report_data();
		$data['specific_input_name'] = lang('reports_employee');

		$employees = array('' => lang('common_all'));
		foreach($this->Employee->get_all()->result() as $employee)
		{
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		$data['specific_input_data'] = $employees;
		
		$this->load->view("reports/timeclock_input",$data);
	}

	function employees_date_input()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/employees_date_input",$data);
	}

	//Graphical summary sales report
	function graphical_summary_sales($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_sales_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_sales_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[date(get_date_format(), strtotime($row['sale_date']))]= to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_sales_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/line",$data);

	}

	//Graphical summary items report
	function graphical_summary_items($start_date, $end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all')
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_items');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id,'show_all_items' => FALSE));

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_items_graph/$start_date/$end_date/$supplier_id/$category_id/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_items_graph($start_date, $end_date, $supplier_id = -1, $category_id = -1, $sale_type = 'all')
	{
		$this->load->model('Sale');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'category_id' => $category_id, 'supplier_id' => $supplier_id,'show_all_items' => FALSE));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_items_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"			
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary item kits report
	function graphical_summary_item_kits($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_item_kits');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_item_kits_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_item_kits_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_item_kits');
		$model = $this->Summary_item_kits;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		

		$data = array(
			"title" => lang('reports_item_kits_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary customers report
	function graphical_summary_categories($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_categories');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_categories_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_categories_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$this->Category->get_full_path($row['category_id'])] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_categories_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_suppliers($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_suppliers');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_suppliers_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_suppliers_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}
	
	function graphical_summary_suppliers_receivings($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_suppliers');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_suppliers_receivings_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}
	
	
	
	//The actual graph data
	function graphical_summary_suppliers_receivings_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers_receivings');
		$model = $this->Summary_suppliers_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['supplier']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_suppliers_receivings_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//The actual graph data
	function graphical_summary_suppliers_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['supplier']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_suppliers_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);
		

		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_employees($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_employees');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_employees_graph/$start_date/$end_date/$sale_type/$employee_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_employees_graph($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['employee']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_employees_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_taxes($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_taxes');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$model->getData();//To to do for caching purposes
		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_taxes_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_taxes_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['tax']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_taxes_receivings($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$model->getData();//To to do for caching purposes

		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_taxes_receivings_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_taxes_receivings_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Receiving');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_taxes_receivings');
		$model = $this->Summary_taxes_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = to_currency_no_money($row['tax']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_taxes_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}	

	//Graphical summary customers report
	function graphical_summary_customers($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_customers');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_customers_graph/$start_date/$end_date/$sale_type/$total_spent_condition/$total_spent_amount"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_customers_graph($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['customer']] = to_currency_no_money($row['total']);
		}
		
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_customers_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary discounts report
	function graphical_summary_discounts($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_discounts');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_discounts_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_discounts_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			if (isset($row['discount_count']))
			{
				$graph_data[$row['discount']] = $row['discount_count'];
			}
			else
			{
				$graph_data[$row['discount']] = $row['summary'];
			}
		}

		$data = array(
			"title" => lang('reports_discounts_summary_report'),
			"data" => $graph_data
		);

		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_payments($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_payments');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_payments_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_payments_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['payment_type']] = to_currency_no_money($row['payment_amount']);
		}
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
				

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	function specific_customer_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view("reports/specific_input",$data);
	}
	
	function specific_customer_store_account_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view("reports/specific_input",$data);
	}

	function specific_customer($start_date, $end_date, $customer_id, $sale_type, $export_excel=0, $offset=0)
	{
		
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Category');
		
		$this->check_action_permission('view_customers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel'=>$export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/specific_customer/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$customer_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', 
			array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', 
			array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], 
			array('target' => '_blank','class'=>'hidden-print')),'align'=>'left', 'detail_id' => $row['sale_id']);
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
		
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['discount_reason'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
					
					$details_data[$key][] = $details_data_row;
				}
			
			}

			
		}

		$customer_info = $this->Customer->get_info($customer_id);
		$data = array(
			"title" => $customer_info->first_name .' '. $customer_info->last_name.' '.lang('reports_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Specific_customer'
		);

		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}
	
	function specific_customer_store_account($start_date, $end_date, $customer_id, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->check_action_permission('view_store_account');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_customer_store_account');
		$model = $this->Specific_customer_store_account;		
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		$config = array();
		$config['base_url'] = site_url("reports/specific_customer_store_account/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$customer_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$tabular_data = array();
		
		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
									array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
									array('data'=>$row['sale_id'] ? anchor('sales/receipt/'.$row['sale_id'], $this->config->item('sale_prefix').' '.$row['sale_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
									array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
									array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
									array('data'=>to_currency($row['balance']), 'align'=> 'right'),
									array('data'=>$row['items'], 'align'=> 'left'),
									array('data'=>$row['comment'], 'align'=> 'left'));
									
		}


		if ($customer_id !=-1)
		{
			$customer_info = $this->Customer->get_info($customer_id);
		
			if ($customer_info->company_name)
			{
				$customer_title = $customer_info->company_name.' ('.$customer_info->first_name .' '. $customer_info->last_name.')';
			}
			else
			{
				$customer_title = $customer_info->first_name .' '. $customer_info->last_name;		
			}
		}
		else
		{
			$customer_title = lang('common_all');
		}
		$data = array(
			"title" => lang('reports_detailed_store_account_report').$customer_title,
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $headers,
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);

	}
	
	function store_account_statements_input()
	{
		$data = $this->_get_common_report_data();
		
		$data['search_suggestion_url'] = site_url('reports/customer_search');		
		$this->load->view('reports/store_account_statements_input', $data);

	}
	
	function store_account_statements($customer_id = -1, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->check_action_permission('view_store_account');
		$this->load->model('reports/Store_account_statements');
		$model = $this->Store_account_statements;
		$model->setParams(array('customer_id' =>$customer_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));
		$config = array();
		$config['base_url'] = site_url("reports/store_account_statements/$customer_id/$start_date/$end_date/$hide_items/$pull_payments_by");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$report_data = $model->getData();
		
		$data = array(
			"title" => lang('reports_store_account_statements'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			'report_data' => $report_data,
			'hide_items' => $hide_items,
			"pagination" => $this->pagination->create_links(),
			'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'sale_time',
		);
		
		$this->load->view("reports/store_account_statements",$data);
		
	}
	
	function store_account_statements_email_customer($customer_id, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');
		
		$this->check_action_permission('view_store_account');
		$this->load->model('reports/Store_account_statements');
		$model = $this->Store_account_statements;
		$model->setParams(array('customer_id' =>$customer_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));
		
		$report_data = $model->getData();
		
		$customer_info = $this->Customer->get_info($customer_id);
		$data = array(
			"title" => lang('reports_store_account_statement'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			'report_data' => $report_data,
			'hide_items' => $hide_items,
			'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'sale_time',
		);
		
		if (!empty($customer_info->email))
		{
			$this->load->library('email');
			$config = array();
			$config['mailtype'] = 'html';
					
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
			$this->email->to($customer_info->email); 

			$this->email->subject(lang('reports_store_account_statement'));
			$this->email->message($this->load->view("reports/store_account_statement_email",$data, true));	
			$this->email->send();
		}
	}

	function specific_employee_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_employee');

		$employees = array();

		$can_view_all_employee_commissions = false;
		if ($this->uri->segment(2) == 'detailed_commissions')
		{
			$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;

			if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
			{
				$can_view_all_employee_commissions = true;
			}	
		}
		
		if($this->uri->segment(2) == 'detailed_commissions' && $can_view_all_employee_commissions == false)
		{
			$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
			$employee = $this->Employee->get_info($employee_id);			
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		else
		{
			foreach($this->Employee->get_all()->result() as $employee)
			{
				$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
			}
		}
		$data['specific_input_data'] = $employees;
		$this->load->view("reports/specific_employee_input",$data);
	}

	function specific_employee($start_date, $end_date, $employee_id, $sale_type, $employee_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');		
		$this->load->model('Category');
		$data=array();
		
		$this->check_action_permission('view_employees');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_employee');
		$model = $this->Specific_employee;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'sale_type' => $sale_type, 'employee_type' => $employee_type, 'offset' => $offset, 'export_excel'=> $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/specific_employee/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 9;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());			

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();
		
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
		
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['discount_reason'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
					
					$details_data[$key][] = $details_data_row;
				}
			}
		}
		$employee_info = $this->Employee->get_info($employee_id);
		$data = array(
			"title" => $employee_info->first_name .' '. $employee_info->last_name.' '.lang('reports_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Specific_employee'
		);
			isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
			
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}

	function get_report_details()
	{
		$ids=$this->input->post('ids');
		$reportType=filter_var($this->input->post('key'), FILTER_SANITIZE_STRING);
		$result='';
		$model=$this->load->model('reports/'.$reportType);
		$model = $this->$reportType;
		
		$data=$model->get_report_details($ids);
		print_r(json_encode($data));
		exit;
	}

	function get_report_details_sales_generator()
	{	
		$params=json_decode($this->input->post('params'), TRUE);
		$ids=$this->input->post('ids');
		$reportType=filter_var($this->input->post('key'), FILTER_SANITIZE_STRING);
		$result='';
		$model=$this->load->model('reports/'.$reportType);
		$model = $this->$reportType;
		$model->setParams($params);
		
		$data=$model->get_report_details($ids,$params['export_excel']);
		print_r(json_encode($data));
		exit;
	}
	
	
	function detailed_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');			
		$this->load->model('Category');
		$data=array();
		$summary_data=array();
		$details_data=array();
		
		$this->check_action_permission('view_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_sales');
		$model = $this->Detailed_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		
		$report_data = $model->getData();
		
		$location_count = count(Report::get_selected_location_ids());
		
		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();

			$link = site_url('reports/specific_customer/'.$start_date.'/'.$end_date.'/'.$row['customer_id'].'/all/0');
			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', 
			array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', 
			array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], 
			array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>'<a href="'.$link.'" target="_blank">'.$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : '').'</a>', 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
			
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['discount_reason'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					
					$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['supplier_name']. ' ('.$drow['supplier_id'].')', 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['current_selling_price']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
				}
			
			}
		
		}
				
		$data = array(
			"title" =>lang('reports_detailed_sales_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_sales'
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
				
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}
  	
	function detailed_payments($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_payments');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_payments');
		$model = $this->Detailed_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();

		
			
		foreach($report_data['summary'] as $sale_id=>$row)
		{			
			foreach($row as $payment_type => $payment_data_row)
			{
				$summary_data_row = array();
				$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$payment_data_row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$payment_data_row['sale_id'].'</span>'.anchor('sales/edit/'.$payment_data_row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$payment_data_row['sale_id'], lang('common_edit').' '.$payment_data_row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left');
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['sale_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['payment_date'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$payment_data_row['payment_type'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($payment_data_row['payment_amount']), 'align'=>'right');

				$summary_data[$sale_id.'|'.$payment_type] = $summary_data_row;
			}
		}

		$temp_details_data = array();
		
		foreach($report_data['details']['sale_ids'] as $sale_id => $drows)
		{
			$payment_types = array();
			foreach ($drows as $drow)
			{
				$payment_types[$drow['payment_type']] = TRUE;
			}
			
			foreach(array_keys($payment_types) as $payment_type)
			{
				foreach ($drows as $drow)
				{
					$details_data_row = array();

					$details_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($drow['payment_date'])), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['payment_type'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['payment_amount']), 'align'=>'right');

					$details_data[$sale_id.'|'.$payment_type][] = $details_data_row;
				}
			}
		}
			
		$data = array(
			"title" =>lang('reports_detailed_payments_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => "detailed_payments"
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;

		$this->load->view("reports/tabular_details",$data);
	}
	
	function detailed_suspended_sales($start_date, $end_date, $customer_id, $sale_type, $export_excel=0, $offset = 0)
	{			
	
		$this->load->model('Sale');	
		$this->load->model('Category');
		
		$this->check_action_permission('view_suspended_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_suspended_sales');
		$model = $this->Detailed_suspended_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'customer_id' => $customer_id,'offset' => $offset, 'export_excel' => $export_excel, 'force_suspended' => true));
		
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_suspended_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$customer_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');
		$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();

		$location_count = count(Report::get_selected_location_ids());

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();

			$link = site_url('reports/specific_customer/'.$start_date.'/'.$end_date.'/'.$row['customer_id'].'/all/0');
			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>'<a href="'.$link.'" target="_blank">'.$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : '').'</a>', 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['amount_due']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['amount_paid']), 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['last_payment_date'], 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
			
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			
			
			if ($row['suspended'] == 1)
			{
				$summary_data_row[] = array('data'=> ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway')), 'align'=>'right');
			}
			elseif ($row['suspended'] == 2)
			{
				$summary_data_row[] = array('data'=> lang('common_estimate'), 'align'=>'right');
			}
			elseif ($row['was_layaway'] == 1)
			{
				$summary_data_row[] = array('data'=> lang('reports_completed_layaway'), 'align'=>'right');
			}
			elseif ($row['was_estimate'] == 1)
			{
				$summary_data_row[] = array('data'=> lang('reports_completed_estimate'), 'align'=>'right');
			}
			
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{

				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					
					$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_product_id'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['item_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
				}
				
			}
		}
		
		if ($customer_id != -1)
		{
			$this->load->model('Customer');
			$customer_info = $this->Customer->get_info($customer_id);
		}
		
		$data = array(
			"title" =>lang('reports_detailed_suspended_sales_report'),
			"subtitle" => ($customer_id != -1 ? ($customer_info->first_name .' '. $customer_info->last_name) : lang('common_all')).' '.date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_suspended_sales'
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}

	function specific_supplier_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_supplier');
		$data['search_suggestion_url'] = site_url('reports/supplier_search/1');
		$this->load->view("reports/specific_input",$data);
	}

	function specific_supplier($start_date, $end_date, $supplier_id, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Supplier');
		$this->load->model('Category');
		$data=array();
		
		$this->check_action_permission('view_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_supplier');
		$model = $this->Specific_supplier;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/specific_supplier/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{		
			$summary_data_row = array();
		
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left','detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
		
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			
			if($export_excel == 1)
			{
			foreach($report_data['details'][$key] as $drow)
			{$details_data_row = array();
				$details_data_row[] =  array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>isset($drow['item_product_id']) ? $drow['item_product_id'] : $drow['item_product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=> 'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=> 'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=> 'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=> 'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left');
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=> 'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=> 'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=> 'right');
				
				if($this->has_profit_permission)
				{
					$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
				}
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
				$details_data[$key][] = $details_data_row;
			}	
			
			}
		}

		$supplier_info = $this->Supplier->get_info($supplier_id);
		$data = array(
					"title" => $supplier_info->first_name .' '. $supplier_info->last_name.' '.lang('reports_report'),
					"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
					"headers" => $model->getDataColumns(),
					"summary_data" => $summary_data,
					"details_data" => $details_data,
					"overall_summary_data" => $model->getSummaryData(),
					"export_excel" => $export_excel,
					"pagination" => $this->pagination->create_links(),
					"report_model" => 'Specific_supplier'
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}

	function specific_supplier_receivings($start_date, $end_date, $supplier_id, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Receiving');
		$this->load->model('Supplier');
		$this->load->model('Category');
		
		$this->check_action_permission('view_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_supplier_receiving');
		$model = $this->Specific_supplier_receiving;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/specific_supplier_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{			
			$summary_data_row = array();
		
			$summary_data_row[] = array('data'=>anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), array('target' => '_blank')).']', 'align'=> 'left', 'detail_id' => $row['receiving_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['receiving_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_received']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
		
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
				
			{
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
	
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_received']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
				
				
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
				
				$details_data[$key][] = $details_data_row;
			}	
			
			}
		}

		$supplier_info = $this->Supplier->get_info($supplier_id);
		$data = array(
					"title" => $supplier_info->first_name .' '. $supplier_info->last_name.' '.lang('reports_recevings_report'),
					"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
					"headers" => $model->getDataColumns(),
					"summary_data" => $summary_data,
					"overall_summary_data" => $model->getSummaryData(),
					"export_excel" => $export_excel,
					"pagination" => $this->pagination->create_links(),
					"report_model" => 'Specific_supplier_receiving'
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}

	function deleted_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset = 0)
	{		
		$this->load->model('Sale');
		$this->load->model('Category');
		$data=array();
		
		$this->check_action_permission('view_deleted_sales');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
	
		$this->load->model('reports/Deleted_sales');
		$model = $this->Deleted_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));

		
		$config = array();
		$config['base_url'] = site_url("reports/deleted_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();

		$location_count = count(Report::get_selected_location_ids());			

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			
			$summary_data_row = array();

			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', 
			array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', 
			array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], 
			array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['deleted_by'], 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['employee_name'].($row['sold_by_employee'] && $row['sold_by_employee'] != $row['employee_name'] ? '/'. $row['sold_by_employee']: ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=>'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=>'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'left');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'left');
			
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					$details_data_row[] = array('data'=>$drow['item_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');

					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');
					}
					
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					
					$details_data[$key][] = $details_data_row;
				}
			
			}
			
		}

		$data = array(
			"title" =>lang('reports_deleted_sales_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			'pagination' => $this->pagination->create_links(),
			"report_model" => 'Deleted_sales'
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}	

	function detailed_suspended_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->load->model('Category');
		
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id, 'force_suspended' => true));
		
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		

		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		
		$summary_data = array();
		$details_data = array();

		$location_count = count(Report::get_selected_location_ids());
		
		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		
		{
			$summary_data[$key] = array(array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), 
			array('target' => '_blank')).']', 'align'=> 'left', 'detail_id' => $row['receiving_id']), 
			array('data'=>$row['location_name'], 'align'=> 'left'), 
			array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
			array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
			array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), 
			array('data'=>$row['employee_name'], 'align'=> 'left'), 
			array('data'=>$row['supplier_name'], 'align'=> 'left'), 
			array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
			array('data'=>to_currency($row['total']), 'align'=> 'right'),
			array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
			array('data'=>$row['payment_type'], 'align'=> 'left'), 
			array('data'=>$row['comment'], 'align'=> 'left'));
			
			if($export_excel == 1)
			{
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$key][] = array(array('data'=>$drow['name'], 'align'=> 'left'),array('data'=>$drow['product_id'], 'align'=> 'left'), array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=> 'left'), array('data'=>$drow['size'], 'align'=> 'left'), array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'), array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), array('data'=>to_currency($drow['total']), 'align'=> 'right'),array('data'=>to_currency($drow['tax']), 'align'=> 'right'), array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
			}
			}
		}

		$data = array(
			"title" =>lang('reports_detailed_suspended_receivings_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_receivings'
			
		);
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}


	function detailed_receivings($start_date, $end_date, $supplier_id,$sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->load->model('Category');
		$data=array();
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'supplier_id' => $supplier_id));

		$config = array();
		$config['base_url'] = site_url("reports/detailed_receivings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		
		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());
		
		
		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			
			$transfer_info = '';
			if ($row['transfer_to_location_id'])
			{
				$this->lang->load('receivings');
				$transfer_info=' <strong style="color: red;">'.lang('receivings_transfer').'</strong>';
			}
			
			$summary_data[$key] = array( array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank', 'class' => 'generate_barcodes_from_recv')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), 
		array('target' => '_blank')).']'.$transfer_info, 'align'=> 'left', 'detail_id' => $row['receiving_id'] ), 
			array('data'=>$row['location_name'], 'align'=> 'left'), 
			array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
			array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
			array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), 
			array('data'=>$row['employee_name'], 'align'=> 'left'), 
			array('data'=>$row['supplier_name'], 'align'=> 'left'), 
			array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
			array('data'=>to_currency($row['total']), 'align'=> 'right'),
			array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
			array('data'=>$row['payment_type'], 'align'=> 'left'), 
			array('data'=>$row['comment'], 'align'=> 'left'));
			
			
			if($export_excel == 1)				
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = array(
					array('data'=>$drow['name'], 'align'=> 'left'),
					array('data'=>$drow['product_id'], 'align'=> 'left'), 
					array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=> 'left'), 
					array('data'=>$drow['size'], 'align'=> 'left'), 
					array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
					array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'), 
					array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), 
					array('data'=>to_currency($drow['total']), 'align'=> 'right'),
					array('data'=>to_currency($drow['tax']), 'align'=> 'right'), 
					array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
				}
			}
		}

		$data = array(
			"title" =>lang('reports_detailed_receivings_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_receivings'
			
		);
				
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}
	
	function deleted_recevings($start_date, $end_date,$sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->load->model('Category');
		
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Deleted_receivings');
		$model = $this->Deleted_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/deleted_recevings/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
		

		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		
		$summary_data = array();
		$details_data = array();

		$location_count = count(Report::get_selected_location_ids());
		
		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data[$key] = array(array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$row['receiving_id'], lang('common_edit').' '.$row['receiving_id'], array('target' => '_blank')).' ['.anchor('items/generate_barcodes_from_recv/'.$row['receiving_id'], lang('common_barcode_sheet'), array('target' => '_blank')).' / '.anchor('items/generate_barcodes_labels_from_recv/'.$row['receiving_id'], lang('common_barcode_labels'), 
			array('target' => '_blank')).']', 'align'=> 'left','detail_id' => $row['receiving_id']), 
			array('data'=>$row['location_name'], 'align'=> 'left'),
			array('data'=>date(get_date_format(), strtotime($row['receiving_date'])), 'align'=> 'left'), 
			array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left'),
			array('data'=>to_quantity($row['items_received']), 'align'=> 'left'), 
			array('data'=>$row['employee_name'], 'align'=> 'left'), 
			array('data'=>$row['supplier_name'], 'align'=> 'left'), 
			array('data'=>to_currency($row['subtotal']), 'align'=> 'right'), 
			array('data'=>to_currency($row['total']), 'align'=> 'right'),
			array('data'=>to_currency($row['tax']), 'align'=> 'right'), 
			array('data'=>$row['payment_type'], 'align'=> 'left'), 
			array('data'=>$row['comment'], 'align'=> 'left'));
						
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = 
					
					array(array('data'=>$drow['name'], 'align'=> 'left'),
					array('data'=>$drow['product_id'], 'align'=> 'left'), 
					array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=> 'left'), 
					array('data'=>$drow['size'], 'align'=> 'left'), 
					array('data'=>to_quantity($drow['quantity_purchased']), 'align'=> 'left'),
					array('data'=>to_quantity($drow['quantity_received']), 'align'=> 'left'),
					array('data'=>to_currency($drow['subtotal']), 'align'=> 'right'), 
					array('data'=>to_currency($drow['total']), 'align'=> 'right'),
					array('data'=>to_currency($drow['tax']), 'align'=> 'right'), 
					array('data'=>$drow['discount_percent'].'%', 'align'=> 'left'));
				}
			}
		}
		
		$data = array(
			"title" =>lang('reports_deleted_recv_reports'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_receivings'
		);

		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}

	function excel_export()
	{
		$this->load->view("reports/excel_export",array());
	}

   function inventory_input()
	{
		$this->load->model('Category');
		$this->load->model('Supplier');
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_supplier');

		$suppliers = array();
		
		$suppliers[-1] = lang('common_all');
		foreach($this->Supplier->get_all()->result() as $supplier)
		{
			$suppliers[$supplier->person_id] = $supplier->company_name. ' ('.$supplier->first_name .' '.$supplier->last_name.')';
		}
		
		$data['categories'] = array();
		$data['categories'][-1] =lang('common_all');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$data['specific_input_data'] = $suppliers;
		$data['category_data'] = $categories;
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/inventory_input",$data);
	}

	function inventory_low($supplier = -1, $category_id = -1, $inventory = 'all', $reorder_only = 0, $export_excel=0, $offset=0)
	{
		$category_id = rawurldecode($category_id);
		$this->load->model('Category');
		
		
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('reports/Inventory_low');
		$model = $this->Inventory_low;
		$model->setParams(array('supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory, 'reorder_only' => $reorder_only));
		
		$config = array();
		$config['base_url'] = site_url("reports/inventory_low/$supplier/$category_id/$inventory/$reorder_only/export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data as $row)
		{
			$data_row = array();
			
		
			if ($location_count > 1)
			{
				$data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			$data_row[] = array('data'=>$row['item_id'], 'align' => 'left');
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=> 'left');
			$data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['location'], 'align'=> 'left');
			
			if($this->has_cost_price_permission)
			{
				$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
			}
			$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['replenish_level']), 'align'=> 'left');
			
			$tabular_data[] = $data_row;				
			
		}

		$data = array(
			"title" => lang('reports_low_inventory_report'),
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	function inventory_summary($inventory_date, $supplier = -1, $category_id = -1, $inventory = 'all', $show_only_pending = 0 ,$show_deleted_items = 0,$export_excel=0, $offset = 0)
	{
		$category_id = rawurldecode($category_id);
		$inventory_date=rawurldecode($inventory_date);
		
		$this->load->model('Category');
		
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('reports/Inventory_summary');
		$model = $this->Inventory_summary;
		$model->setParams(array('inventory_date' => $inventory_date.' 23:59:59','supplier'=>$supplier,'category_id' => $category_id, 'export_excel' => $export_excel, 'offset'=>$offset, 'inventory' => $inventory,'show_only_pending' => $show_only_pending,'show_deleted' => $show_deleted_items));
		
		$config = array();
		$config['base_url'] = site_url("reports/inventory_summary/$inventory_date/$supplier/$category_id/$inventory/$show_only_pending/$show_deleted_items/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{
			$data_row = array();

			$data_row[] = array('data'=>$row['item_id'], 'align' => 'left');			
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=> 'left');
			$data_row[] = array('data'=>$row['company_name'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['location'], 'align'=> 'left');
			if($this->has_cost_price_permission)
			{
				$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
			}
			$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['pending_inventory']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['replenish_level']), 'align'=> 'left');
			
			$tabular_data[] = $data_row;				
			
		}

		$data = array(
			"title" => lang('reports_inventory_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($inventory_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}

	function summary_giftcards($export_excel = 0, $offset = 0)
	{
		$this->check_action_permission('view_giftcards');
		$this->load->model('reports/Summary_giftcards');
		$model = $this->Summary_giftcards;
		$model->setParams(array('export_excel' => $export_excel, 'offset' => $offset));
		$config = array();
		$config['base_url'] = site_url("reports/summary_giftcards/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 4;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['giftcard_number'], 'align'=> 'left'), array('data'=>$row['description'], 'align'=> 'left'),array('data'=>to_currency($row['value']), 'align'=> 'left'), array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left'));
		}

		$data = array(
			"title" => lang('reports_giftcard_summary_report'),
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function summary_giftcard_sales($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->check_action_permission('view_giftcards');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_giftcards_sales');
		$model = $this->Summary_giftcards_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel));
	
		$config = array();
		$config['base_url'] = site_url("reports/summary_giftcard_sales/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data as $row)
		{
			$data_row = array();
			
			if ($location_count > 1)
			{
				$data_row[] = array('data'=>$row['location_name'], 'align'=> 'left');				
			}
			
			$data_row[] = array('data'=>date(get_date_format(), strtotime($row['sale_time'])), 'align'=>'left');
			$data_row[] = array('data'=>$row['giftcard_number'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
			$data_row[] = array('data'=>to_currency($row['gift_card_sale_price']), 'align'=>'left');	
					
			$tabular_data[] = $data_row;
		}
		$data = array(
			"title" => lang('reports_gift_card_sales_reports'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function excel_export_store_account_summary_input()
	{
		$this->load->view("reports/excel_export_store_account_summary_input",array());
	}
		
	function summary_store_accounts($show_accounts_over_credit_limit, $export_excel = 0, $offset=0)
	{
		$this->check_action_permission('view_store_account');
		$this->load->model('reports/Summary_store_accounts');
		$model = $this->Summary_store_accounts;
		$model->setParams(array('show_accounts_over_credit_limit' => $show_accounts_over_credit_limit, 'export_excel' => $export_excel, 'offset' => $offset));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_store_accounts/$show_accounts_over_credit_limit/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 5;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['customer'], 'align'=> 'left'), array('data'=>$row['credit_limit'] ? to_currency($row['credit_limit']) : lang('common_not_set'), 'align'=> 'right'), array('data'=>to_currency($row['balance']), 'align'=> 'right'), array('data'=>anchor("customers/pay_now/".$row['person_id'],lang('common_pay'),array('title'=>lang('common_update'),'class'=>'btn btn-info')), 'align'=> 'right'));
		}

		$data = array(
			"title" => lang('reports_store_account_summary_report'),
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			'pagination' => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);
	}

	function detailed_giftcards_input()
	{
		$data['specific_input_name'] = lang('reports_customer');
		$data['search_suggestion_url'] = site_url('reports/customer_search');
		$this->load->view("reports/detailed_giftcards_input",$data);
	}

	function detailed_giftcards($customer_id, $giftcard_number, $export_excel = 0, $offset=0)
	{
		
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Category');
		
		$this->check_action_permission('view_giftcards');
		$this->load->model('reports/Detailed_giftcards');
		$model = $this->Detailed_giftcards;
		$model->setParams(array('customer_id' =>$customer_id, 'giftcard_number' => $giftcard_number, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_giftcards/$customer_id/$giftcard_number/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();

		foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();
			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$row['sale_id'].'</span>'.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['register_name'], 'align'=>'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			if($export_excel == 1)
			{
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data_row = array();
					
					$details_data_row[] = array('data'=>isset($drow['item_number']) ? $drow['item_number'] : $drow['item_kit_number'], 'align'=>'left');
					$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
					$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');;
					$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
					$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
					
					if($this->has_profit_permission)
					{
						$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
					}
					
					$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=>'left');
					$details_data[$key][] = $details_data_row;
				}
			
			}
		
		}
		$customer_info = $this->Customer->get_info($customer_id);
		
		$data = array(
			"title" => $customer_info->first_name .' '. $customer_info->last_name.' '.lang('reports_giftcard'). ' '.lang('reports_report'),
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"details_data" => $details_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_giftcards'
			);
			
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;

		$this->load->view("reports/tabular_details_lazy_load",$data);
	}
	
	function date_input_profit_and_loss()
	{
		$data = $this->_get_common_report_data();
		$this->load->view("reports/date_input_profit_and_loss",$data);	
	}
	
	function detailed_profit_and_loss($start_date, $end_date)
	{
		$this->load->model('Category');
		$this->load->model('Sale');
		$this->load->model('Receiving');
		$this->check_action_permission('view_profit_and_loss');
		$this->load->model('reports/Detailed_profit_and_loss');
		$model = $this->Detailed_profit_and_loss;
		$end_date=date('Y-m-d 23:59:59', strtotime($end_date));

		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date));
	
		$data = array(
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"details_data" => $model->getData(),
			"overall_summary_data" => $model->getSummaryData(),
		);

		$this->load->view("reports/profit_and_loss_details",$data);
	}
	
	function summary_profit_and_loss($start_date, $end_date)
	{
		$this->load->model('Sale');
		$this->load->model('Receiving');
		
		$this->check_action_permission('view_profit_and_loss');
		$this->load->model('reports/Summary_profit_and_loss');
		$model = $this->Summary_profit_and_loss;
		$end_date=date('Y-m-d 23:59:59', strtotime($end_date));
		
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date));
	
		$data = array(
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"details_data" => $model->getData(),
			"overall_summary_data" => $model->getSummaryData(),
		);
		
		$this->load->view("reports/profit_and_loss_summary",$data);
	}
	
	function detailed_inventory_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$data['specific_input_name'] = lang('common_item');
		$data['search_suggestion_url'] = site_url('reports/item_search');
		
		$locations = array();
		foreach($this->Location->get_all()->result() as $location_row) 
		{
			$locations[$location_row->location_id] = $location_row->name;
		}
		$data['locations'] = $locations;
		
		$data['can_view_inventory_at_all_locations'] = $this->Employee->has_module_action_permission('reports','view_inventory_at_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);
		
		$this->load->view("reports/detailed_inventory_input",$data);	
	}
	
	function detailed_inventory($start_date, $end_date, $item_id, $show_manual_adjustments_only, $export_excel=0, $offset = 0)
	{
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('Category');
		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$this->load->model('reports/Detailed_inventory');
		$model = $this->Detailed_inventory;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date,'item_id' => $item_id,'show_manual_adjustments_only' => $show_manual_adjustments_only, 'offset' => $offset, 'export_excel' => $export_excel));
		
		$config = array();
		$config['base_url'] = site_url("reports/detailed_inventory/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$item_id/$show_manual_adjustments_only/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data as $row)
		{
			$row['trans_comment'] = preg_replace('/'.$this->config->item('sale_prefix').' ([0-9]+)/', anchor('sales/receipt/$1', $row['trans_comment']), $row['trans_comment']);
			
			$tabular_data_row = array(
				array('data'=>$row['item_id'], 'align'=>'left'),
				array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['trans_date'])), 'align'=>'left'),
				array('data'=>$row['name'], 'align'=>'left'),
				array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=>'left'),
				array('data'=>$row['item_number'], 'align'=>'left'),
				array('data'=>$row['product_id'], 'align'=>'left'),
				array('data'=>$row['size'], 'align'=>'left'),
				array('data'=>to_quantity($row['trans_inventory']), 'align'=>'left'),
				array('data'=>$row['trans_comment'], 'align'=>'left'),
			); 
			
		
			if ($location_count > 1)
			{
				array_unshift($tabular_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			
			$tabular_data[] = $tabular_data_row;
			
		}

		$data = array(
			"title" => lang('reports_detailed_inventory_report'),
			"subtitle" => lang('reports_detailed_inventory_report')." - ".date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date))." - ".$config['total_rows'].' '.lang('reports_sales_report_generator_results_found'),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	//Summary employees report
	function summary_commissions($start_date, $end_date, $sale_type, $employee_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_commissions');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' =>$employee_type, 'export_excel' => $export_excel, 'offset' => $offset));

		$config = array();
		$config['base_url'] = site_url("reports/summary_commissions/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;		
		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['employee'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align' => 'right');
			$data_row[] =  array('data'=>to_currency($row['total']), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align' => 'right');
			}
			$data_row[] = array('data'=>to_currency($row['commission']), 'align' => 'right');			
			$tabular_data[] = $data_row;			
		}

		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function graphical_summary_commissions($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_commissions');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_commissions_graph/$start_date/$end_date/$sale_type/$employee_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_commissions_graph($start_date, $end_date, $sale_type, $employee_type)
	{
		$this->load->model('Sale');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_commissions');
		$model = $this->Summary_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'employee_type' => $employee_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['employee']] = to_currency_no_money($row['commission']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
		
		$data = array(
			"title" => lang('reports_comissions_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	
	
	function detailed_commissions($start_date, $end_date, $employee_id, $sale_type, $employee_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		
		$this->check_action_permission('view_commissions');		
		$logged_in_employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		
		$can_view_all_employee_commissions = false;
		if (!$this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $logged_in_employee_id))
		{
			$employee_id = $logged_in_employee_id;
		}
		
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_commissions');
		$model = $this->Detailed_commissions;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'sale_type' => $sale_type, 'employee_type' => $employee_type, 'offset' => $offset, 'export_excel'=> $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/detailed_commissions/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$sale_type/$employee_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 9;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();
		$location_count = count(Report::get_selected_location_ids());			

	foreach(isset($export_excel) == 1 && isset($report_data['summary']) ? $report_data['summary']:$report_data as $key=>$row)
		{
			$summary_data_row = array();	
			
			$summary_data_row[] = array('data'=>anchor('sales/receipt/'.$row['sale_id'], '<i class="ion-printer"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('sales/edit/'.$row['sale_id'], lang('common_edit').' '.$row['sale_id'], array('target' => '_blank')), 'align'=>'left', 'detail_id' => $row['sale_id']);
			
			if ($location_count > 1)
			{
				$summary_data_row[] = array('data'=>$row['location_name'], 'align'=>'left');
			}
			
			$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_quantity($row['items_purchased']), 'align'=> 'left');
			
			$summary_data_row[] = array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left');
			$summary_data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['total']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			if($this->has_profit_permission)
			{
				$summary_data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			$summary_data_row[] = array('data'=>to_currency($row['commission']), 'align'=> 'right');
			$summary_data_row[] = array('data'=>$row['payment_type'], 'align'=>'right');
			$summary_data_row[] = array('data'=>$row['comment'], 'align'=>'right');
			$summary_data[$key] = $summary_data_row;
			
			
			if($export_excel == 1)
			{

			foreach($report_data['details'][$key] as $drow)
			{
				$details_data_row = array();
				$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
				if($this->has_profit_permission)
				{
					$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
				}
				$details_data_row[] = array('data'=>to_currency($drow['commission']), 'align'=>'right');
								
				
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
				$details_data[$key][] = $details_data_row;
			}
			}
		}
		$employee_info = $this->Employee->get_info($employee_id);
		$data = array(
			"title" => $employee_info->first_name .' '. $employee_info->last_name.' '.lang('reports_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_commissions'
		);
		
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
				
		$this->load->view("reports/tabular_details_lazy_load",$data);
	}
	


	function detailed_timeclock($start_date, $end_date, $employee_id, $export_excel=0, $offset=0)
	{
		$this->check_action_permission('view_timeclock');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Detailed_timeclock');
		$model = $this->Detailed_timeclock;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'offset' => $offset, 'export_excel'=> $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/detailed_timeclock/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$employee_id/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();

			$edit=anchor('timeclocks/view/'.$row['id'].'/'.$start_date.'/'. $end_date.'/'.$employee_id, lang('common_edit'));
			
			$delete=anchor('timeclocks/delete/'.$row['id'].'/'.$start_date.'/'. $end_date.'/'.$employee_id, lang('common_delete'), 
			"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_timeclock_delete')).", this)'");

			$data_row[] = array('data'=>$edit, 'align' => 'left');
			$data_row[] = array('data'=>$delete, 'align' => 'left');
			$data_row[] = array('data'=>$row['first_name'].' '.$row['last_name'], 'align' => 'left');
			$data_row[] = array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['clock_in'])), 'align' => 'left');
			
			if ($row['clock_out'] != '0000-00-00 00:00:00')
			{
				$data_row[] = array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['clock_out'])), 'align' => 'left');
				$t1 = strtotime ($row['clock_out']);
				$t2 = strtotime ($row['clock_in']);
				$diff = $t1 - $t2;
				$hours = $diff / ( 60 * 60 );
				
				//Not really the purpose of this function; but it rounds to 2 decimals
				$hours = to_currency_no_money($hours,2);	
			}
			else
			{
				$data_row[] = array('data'=>lang('reports_not_clocked_out'), 'align' => 'left');
				$hours = lang('reports_not_clocked_out');				
			}
			
			$data_row[] = array('data'=>$hours, 'align' => 'left');			
			$data_row[] = array('data'=>to_currency($row['hourly_pay_rate']), 'align' => 'left');			
			$data_row[] = array('data'=>to_currency($row['hourly_pay_rate'] * $hours), 'align' => 'left');			
			$data_row[] = array('data'=>$row['clock_in_comment'], 'align' => 'left');			
			$data_row[] = array('data'=>$row['clock_out_comment'], 'align' => 'left');
			$data_row[] = array('data'=>$row['ip_address_clock_in'], 'align' => 'left');
			$data_row[] = array('data'=>$row['ip_address_clock_out'], 'align' => 'left');
					
			$tabular_data[] = $data_row;			
		}

		$employee_info = $this->Employee->get_info($employee_id);

		$data = array(
			"title" => ($employee_id != -1 ? $employee_info->first_name . ' '.$employee_info->last_name . ' - ' : ' ').lang('reports_detailed_timeclock_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function summary_timeclock($start_date, $end_date, $employee_id, $export_excel=0, $offset=0)
	{
		$this->check_action_permission('view_timeclock');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Summary_timeclock');
		$model = $this->Summary_timeclock;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'offset' => $offset, 'export_excel'=> $export_excel));
	
		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['first_name'].' '.$row['last_name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['hours'], 'align' => 'left');			
			$data_row[] = array('data'=>to_currency($row['total']), 'align' => 'left');			
			$tabular_data[] = $data_row;			
		}

		$employee_info = $this->Employee->get_info($employee_id);

		$data = array(
			"title" => ($employee_id != -1 ? $employee_info->first_name . ' '.$employee_info->last_name . ' - ' : ' ').lang('reports_summary_timeclock_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => false,
		);

		$this->load->view("reports/tabular",$data);
	}
	
	
	function closeout($start_date, $end_date,$export_excel=0)
	{
		$this->load->model('Category');
		
		$this->load->model('Sale');
		$this->load->model('Receiving');
		
		$this->check_action_permission('view_closeout');		
		
		$open_time = $this->config->item('store_opening_time') ? $this->config->item('store_opening_time') : '00:00';
		$close_time = $this->config->item('store_closing_time') ? $this->config->item('store_closing_time') : '23:59';
		
		if(strtotime($open_time) > strtotime($close_time))
		{
			$start_date = date('Y-m-d H:i:00', strtotime(rawurldecode($start_date).' '.$open_time));
			$end_date = date('Y-m-d H:i:59', strtotime(rawurldecode($end_date).' '.$close_time.' + 1 Day'));
		} else {			
			$start_date = date('Y-m-d H:i:00', strtotime(rawurldecode($start_date).' '.$open_time));
			$end_date = date('Y-m-d H:i:59', strtotime(rawurldecode($end_date).' '.$close_time));
		}
				
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => 'all', 'offset'=> 0, 'export_excel' => 0));
		
		$this->db->select('sale_id, SUM(total) as total', false);
		$this->db->from($this->db->dbprefix('sales').' FORCE INDEX (sales_search)');
		$this->db->where('suspended!=2');
		$this->db->where('deleted', 0);
		$this->db->where('sale_time BETWEEN '. $this->db->escape($start_date). ' and '. $this->db->escape($end_date));
		$this->db->where_in('location_id',Report::get_selected_location_ids());
		$this->db->group_by('sale_id');
		
		$sales_totals_for_payments = array();
		foreach($this->db->get()->result_array() as $sale_total_row)
		{
			$sales_totals_for_payments[$sale_total_row['sale_id']] = to_currency_no_money($sale_total_row['total'], 2);
		}
				
		$this->load->model('reports/Closeout');
		$model = $this->Closeout;
		$model->setParams(array('start_date'=>$start_date, 'end_date' => $end_date, 'sales_total_for_payments' => $sales_totals_for_payments,'export_excel' => $export_excel));

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
		
			$data_row[] = array('data'=>$row[0], 'align'=> '');
			$data_row[] = array('data'=>$row[1], 'align'=> '');
		
			$tabular_data[] = $data_row;
		}
		$data = array(
			"title" => lang('reports_closeout'),
			"subtitle" => date(get_date_format().' '.get_time_format(), strtotime($start_date)) .'-'.date(get_date_format().' '.get_time_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
		);

		$this->load->view("reports/tabular",$data);
	}
	

	//Summary tags report
	function summary_tags($start_date, $end_date, $do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Tag');
		$this->check_action_permission('view_tags');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_tags');
		$model = $this->Summary_tags;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel'=>$export_excel, 'offset' => $offset));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_tags/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_tags = array();
			
			foreach(array_keys($report_data) as $tag_name)
			{
				$compare_to_tags[] = $tag_name;
			}
			
			$model_compare = $this->Summary_tags;			
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_tags' =>$compare_to_tags));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}


		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['tag']]))
				{
					$row_compare = $report_data_compare[$row['tag']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$row['tag'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$data_row[] = array('data'=>floatval($row['item_sold']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['item_sold'] >= $row['item_sold'] ? ($row['item_sold'] == $row_compare['item_sold'] ?  '' : 'compare_better') : 'compare_worse').'">'.floatval($row_compare['item_sold']) .'</span>':''), 'align' => 'right');
			$tabular_data[] = $data_row;				
		}		

		$data = array(
			"title" => lang('reports_tags_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	//Graphical summary customers report
	function graphical_summary_tags($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->load->model('Tag');
		
		$this->check_action_permission('view_tags');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_tags');
		$model = $this->Summary_tags;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$data = array(
			"title" => lang('reports_tags_summary_report'),
			"graph_file" => site_url("reports/graphical_summary_tags_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_tags_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->load->model('Tag');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_tags');
		$model = $this->Summary_tags;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));

		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['tag']] = to_currency_no_money($row['total']);
		}
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';


		$data = array(
			"title" => lang('reports_tags_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}
	
	function customer_search()
	{
		$this->load->model('Customer');
		
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function item_search()
	{
		$this->load->model('Item');
		
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),'unit_price',100);
		array_unshift($suggestions, array('value' => -1, 'label' => lang('common_all')));		
		echo json_encode($suggestions);
	}
	
	function supplier_search($hide_all = 0)
	{
		$this->load->model('Supplier');
		
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Supplier->get_supplier_search_suggestions($this->input->get('term'),100);
		
		if (!$hide_all)
		{
			array_unshift($suggestions, array('value' => -1, 'label' => lang('common_all')));		
		}
		
		echo json_encode($suggestions);
	}
		
	function expiring_inventory($start_date, $end_date, $export_excel=0, $offset = 0)
	{
		$this->load->model('Category');		
		$start_date = rawurldecode($start_date);
		$end_date = rawurldecode($end_date);
		
		$this->check_action_permission('view_inventory_reports');
		$this->load->model('reports/Inventory_expire_summary');
		$model = $this->Inventory_expire_summary;
		$model->setParams(array('start_date'=>$start_date,'end_date' => $end_date, 'export_excel' => $export_excel, 'offset'=>$offset));
		
		$config = array();
		$config['base_url'] = site_url("reports/expiring_inventory/$start_date/$end_date/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data as $row)
		{
			$data_row = array();
			
		
			if ($location_count > 1)
			{
				$data_row[] = array('data'=>$row['location_name'], 'align' => 'left');
			}
			
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>date(get_date_format(), strtotime($row['expire_date'])), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_expiring']), 'align'=> 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=> 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['size'], 'align'=> 'left');
			$data_row[] = array('data'=>$row['description'], 'align'=> 'left');
			if($this->has_cost_price_permission)
			{
				$data_row[] = array('data'=>to_currency($row['cost_price']), 'align'=> 'right');
			}
			$data_row[] = array('data'=>to_currency($row['unit_price']), 'align'=> 'right');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align'=> 'left');
			$data_row[] = array('data'=>to_quantity($row['reorder_level']), 'align'=> 'left');
			
			$tabular_data[] = $data_row;				
			
		}

		$data = array(
			"title" => lang('reports_expired_inventory_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
		
	//Detailed expenses report
	function detailed_expenses($start_date, $end_date, $export_excel=0, $offset = 0)        
	{
		$this->load->model('Category');
		
		$this->check_action_permission('view_expenses');
	   $start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
       
		$this->check_action_permission('view_expenses');
		$this->load->model('reports/Detailed_expenses');
		$model = $this->Detailed_expenses;
		$end_date .=' 23:59:59';
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date,'offset' => $offset, 'export_excel' => $export_excel));
		$config = array();		
		$config['base_url'] = site_url("reports/detailed_expenses/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$tabular_data = array();
		$report_data = $model->getData();
		$location_count = count(Report::get_selected_location_ids());
		
		foreach($report_data as $row)
		{
			$tabular_data_row = array(
			array('data'=>$row['id'], 'align'=> 'left'), 
			array('data'=>$row['expense_type'], 'align'=> 'left'), 
			array('data'=>$row['expense_description'], 'align'=> 'left'), 
			array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=> 'left'), 
			array('data'=>$row['expense_reason'], 'align'=> 'left'), 
			array('data'=>date(get_date_format(), strtotime($row['expense_date'])), 'align'=> 'left'), 
			array('data'=>  to_currency($row['expense_amount']), 'align'=> 'left'), 
			array('data'=>  to_currency($row['expense_tax']), 'align'=> 'left'), 
			array('data'=>$row['employee_recv'], 'align'=> 'left'), 
			array('data'=>$row['employee_appr'], 'align'=> 'left'), 
			array('data'=>$row['expense_note'], 'align'=> 'left'), 
			);
			

			if ($location_count > 1)
			{
				array_unshift($tabular_data_row, array('data'=>$row['location_name'], 'align'=>'left'));
			}
			$tabular_data[] = $tabular_data_row;
	
		}
		$data = array(
		"title" => lang('reports_expenses_detailed_report'),
		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
		"headers" => $model->getDataColumns(),
		"data" => $tabular_data,
		"summary_data" => $model->getSummaryData(),
		"export_excel" => $export_excel,
		"pagination" => $this->pagination->create_links(),
		);
       
		$this->load->view("reports/tabular",$data);
	}	
	
	
	//Summary expenses report
	function summary_expenses($start_date, $end_date, $export_excel=0, $offset = 0)        
	{
		$this->load->model('Category');
		
		$this->check_action_permission('view_expenses');
	   $start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$end_date .=' 23:59:59';
       
		$this->check_action_permission('view_expenses');
		$this->load->model('reports/Summary_expenses');
		$model = $this->Summary_expenses;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date,'offset' => $offset, 'export_excel' => $export_excel));
		$config = array();
		$config['base_url'] = site_url("reports/summary_expenses/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{
			$tabular_data[] = array(
			array('data'=>$this->Category->get_full_path($row['category_id']), 'align'=> 'left'), 
			array('data'=>  to_currency($row['expense_tax']), 'align'=> 'left'), 
			array('data'=>  to_currency($row['expense_amount']), 'align'=> 'left'), 
		);
		}
		$data = array(
		"title" => lang('reports_expenses_summary_report'),
		"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
		"headers" => $model->getDataColumns(),
		"data" => $tabular_data,
		"summary_data" => $model->getSummaryData(),
		"export_excel" => $export_excel,
		"pagination" => $this->pagination->create_links(),
		);
       
		$this->load->view("reports/tabular",$data);
	}
	
	function giftcards_audit_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$this->load->view("reports/giftcards_audit_input",$data);
	}
	
	function giftcard_audit($start_date, $end_date, $giftcard_number = -1, $export_excel = 0, $offset=0)
	{
		$this->check_action_permission('view_giftcards');
	   $start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Giftcard_audit');
		$model = $this->Giftcard_audit;
		$model->setParams(array('start_date' => $start_date, 'end_date' => $end_date, 'giftcard_number' => $giftcard_number, 'offset' => $offset, 'export_excel' => $export_excel));

		
		$config = array();
		$config['base_url'] = site_url("reports/giftcard_audit/$start_date/$end_date/$giftcard_number/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$headers = $model->getDataColumns();
		$report_data = $model->getData();
		$tabular_data = array();
		foreach($report_data as $row)
		{
			$row['log_message'] = strip_tags($row['log_message']);
			$row['log_message'] = preg_replace('/'.$this->config->item('sale_prefix').' ([0-9]+)/', anchor('sales/receipt/$1', $this->config->item('sale_prefix').' $1'), $row['log_message']);
			
			$tabular_data[] = array(
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['log_date'])), 'align'=> 'left'), 
				array('data'=>$row['giftcard_number'], 'align'=> 'left'), 
				array('data'=>$row['description'], 'align'=> 'left'), 
				array('data'=>$row['log_message'], 'align'=> 'left'), 
			);
		}
		
		
		$data = array(
			"title" => lang('reports_giftcard'). ' '.lang('reports_audit_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);

	}
	
	function store_account_outstanding($customer_id = -1, $show_paid = 0, $export_excel = 0, $offset = 0)
	{
		$this->check_action_permission('view_store_account');
		$this->load->model('reports/Store_account_outstanding');
		$model = $this->Store_account_outstanding;
		$model->setParams(array('customer_id' => $customer_id, 'show_paid' => $show_paid, 'export_excel' => $export_excel, 'offset' => $offset));
		$config = array();
		$config['base_url'] = site_url("reports/store_account_outstanding/$customer_id/$show_paid/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{			
			
			if ($row['paid'])
			{
				$mark_paid_unpaid=anchor('reports/store_account_outstanding_mark_as_unpaid/'.$row['sale_id'].'/'.$customer_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_as_unpaid'),"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_mark_as_unpaid')).", this)' class='btn btn-success'");
			}
			else
			{
				$mark_paid_unpaid=anchor('reports/store_account_outstanding_mark_as_paid/'.$row['sale_id'].'/'.$customer_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_as_paid'),"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_mark_as_paid')).", this)' class='btn btn-danger'");
			}
			
			
			$tabular_data[] = array(
				array('data'=>anchor('sales/receipt/'.$row['sale_id'], ($this->config->item('sale_prefix') ? $this->config->item('sale_prefix') : 'POS').' '.$row['sale_id'], array('target' => '_blank')), 'align'=> 'left'),
				array('data'=>$row['customer_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left'),
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['sale_time'])), 'align'=> 'left'),
				array('data'=>to_currency($row['payment_amount']), 'align'=> 'left'),
			 	array('data'=>$row['comment'], 'align'=> 'left'),
				array('data'=>$mark_paid_unpaid, 'align'=> 'center')
			);
		}

		$mark_all_paid=anchor('reports/store_account_outstanding_mark_all_as_paid/'.$customer_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_all_as_paid'), 
		"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_all_mark_as_paid')).", this)'");

		$data = array(
			"title" => lang('reports_outstanding_sales_report'),
			"subtitle" => $mark_all_paid,
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
		
	}
	
	function store_account_outstanding_mark_as_paid($sale_id,$customer_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account');
		$this->db->insert('store_accounts_paid_sales',array('sale_id' => $sale_id,'store_account_payment_sale_id' => NULL));
		redirect('reports/store_account_outstanding/'.$customer_id.'/'.$show_paid.'/0/'.$offset);
	
	}
	
	function store_account_outstanding_mark_as_unpaid($sale_id,$customer_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account');
		$this->db->delete('store_accounts_paid_sales',array('sale_id' => $sale_id));
		redirect('reports/store_account_outstanding/'.$customer_id.'/'.$show_paid.'/0/'.$offset);		
	}
	
	function store_account_outstanding_mark_all_as_paid($customer_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account');	
		$this->load->model('Sale');
		$this->Sale->mark_all_unpaid_sales_paid($customer_id);
		redirect('reports/store_account_outstanding/'.$customer_id.'/'.$show_paid.'/0/'.$offset);
	}
	
	
	function supplier_store_account_statements_input()
	{
		$data = $this->_get_common_report_data();
		
		$data['search_suggestion_url'] = site_url('reports/supplier_search');		
		$this->load->view('reports/supplier_store_account_statements_input', $data);
	}
	
	function supplier_excel_export_store_account_summary_input()
	{
		$this->load->view("reports/excel_export",array());
	}
	
	function supplier_specific_store_account_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		$data['specific_input_name'] = lang('reports_supplier');
		$data['search_suggestion_url'] = site_url('reports/supplier_search/1');
		$this->load->view("reports/specific_input",$data);
	}
	
	function supplier_store_account_activity_input()
	{
		$data = $this->_get_common_report_data(TRUE);
		
		$this->load->view("reports/date_input_excel_export_store_account_activity",$data);		
	}
	
	function supplier_store_account_outstanding_input()
	{
		$data = array();
		$data['specific_input_name'] = lang('reports_supplier');
		$data['search_suggestion_url'] = site_url('reports/supplier_search');
		$this->load->view('reports/excel_export_supplier_outstanding',$data);
	}
	
	function supplier_store_account_statements($supplier_id = -1, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Supplier');
		$this->check_action_permission('view_store_account_suppliers');
		$this->load->model('reports/Store_account_statements_supplier');
		$model = $this->Store_account_statements_supplier;
		$model->setParams(array('supplier_id' =>$supplier_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));
		$config = array();
		$config['base_url'] = site_url("reports/supplier_store_account_statements/$supplier_id/$start_date/$end_date/$hide_items/$pull_payments_by");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$report_data = $model->getData();
		
		$data = array(
			"title" => lang('reports_store_account_statements'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			'report_data' => $report_data,
			'hide_items' => $hide_items,
			"pagination" => $this->pagination->create_links(),
			'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'receiving_time',
		);
		
		$this->load->view("reports/supplier_store_account_statements",$data);
	}
	
	function store_account_statements_email_supplier($supplier_id, $start_date, $end_date, $hide_items = 0, $pull_payments_by = 'payment_date', $offset=0)
	{
		$this->load->model('Receiving');
		$this->load->model('Supplier');
		
		$this->check_action_permission('view_store_account_suppliers');
		$this->load->model('reports/Store_account_statements_supplier');
		$model = $this->Store_account_statements_supplier;
		$model->setParams(array('supplier_id' =>$supplier_id,'offset' => $offset, 'start_date' => $start_date, 'end_date'=>$end_date, 'pull_payments_by' => $pull_payments_by));
		
		$report_data = $model->getData();
		
		$supplier_info = $this->Supplier->get_info($supplier_id);
		$data = array(
			"title" => lang('reports_store_account_statement'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			'report_data' => $report_data,
			'hide_items' => $hide_items,
			'date_column' => $pull_payments_by == 'payment_date' ? 'date' : 'receiving_time',
		);
		
		if (!empty($supplier_info->email))
		{
			$this->load->library('email');
			$config = array();
			$config['mailtype'] = 'html';
					
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
			$this->email->to($supplier_info->email); 

			$this->email->subject(lang('reports_store_account_statement'));
			$this->email->message($this->load->view("reports/supplier_store_account_statement_email",$data, true));	
			$this->email->send();
		}
	}
	
	
	function supplier_summary_store_accounts($export_excel = 0, $offset=0)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$this->load->model('reports/Summary_store_accounts_supplier');
		$model = $this->Summary_store_accounts_supplier;
		$model->setParams(array('export_excel' => $export_excel, 'offset' => $offset));
		
		$config = array();
		$config['base_url'] = site_url("reports/supplier_summary_store_accounts/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 4;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['supplier'], 'align'=> 'left'), array('data'=>to_currency($row['balance']), 'align'=> 'right'), array('data'=>anchor("suppliers/pay_now/".$row['person_id'],lang('common_pay'),array('title'=>lang('common_update'),'class'=>'btn btn-info')), 'align'=> 'right'));
		}

		$data = array(
			"title" => lang('reports_store_account_summary_report'),
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			'pagination' => $this->pagination->create_links()
		);

		$this->load->view("reports/tabular",$data);	
	}
	
	function supplier_specific_store_account($start_date, $end_date, $supplier_id, $receiving_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Supplier');
		$this->check_action_permission('view_store_account_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Specific_supplier_store_account');
		$model = $this->Specific_supplier_store_account;		
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'supplier_id' =>$supplier_id, 'receiving_type' => $receiving_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		$config = array();
		$config['base_url'] = site_url("reports/supplier_specific_store_account/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$supplier_id/$receiving_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 8;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$tabular_data = array();
		
		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
									array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
									array('data'=>$row['receiving_id'] ? anchor('receivings/receipt/'.$row['receiving_id'], 'RECV '.$row['receiving_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
									array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
									array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
									array('data'=>to_currency($row['balance']), 'align'=> 'right'),
									array('data'=>$row['items'], 'align'=> 'left'),
									array('data'=>$row['comment'], 'align'=> 'left'));
									
		}

		
		
		if ($supplier_id !=-1)
		{
			$supplier_info = $this->Supplier->get_info($supplier_id);
			if ($supplier_info->company_name)
			{
				$supplier_title = $supplier_info->company_name.' ('.$supplier_info->first_name .' '. $supplier_info->last_name.')';
			}
			else
			{
				$supplier_title = $supplier_info->first_name .' '. $supplier_info->last_name;		
			}
		}
		else
		{
			$supplier_title = lang('common_all');
		}
		
		$data = array(
			"title" => lang('reports_detailed_store_account_report').$supplier_title,
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $headers,
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function supplier_store_account_activity($start_date, $end_date, $export_excel=0, $offset=0)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('reports/Store_account_activity_supplier');
		$model = $this->Store_account_activity_supplier;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'offset'=> $offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/supplier_store_account_activity/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;

		$this->load->library('pagination');$this->pagination->initialize($config);

		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$tabular_data[] = array(array('data'=>$row['sno'], 'align'=> 'left'),
									array('data'=>$row['company_name'].' ('.$row['first_name'].' '.$row['last_name'].')', 'align'=> 'left'),
									array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($row['date'])), 'align'=> 'left'),
									array('data'=>$row['receiving_id'] ? anchor('receivings/receipt/'.$row['receiving_id'], 'RECV '.$row['receiving_id'], array('target' => '_blank')) : '-', 'align'=> 'center'),
									array('data'=> $row['transaction_amount'] > 0 ? to_currency($row['transaction_amount']) : to_currency(0), 'align'=> 'right'),
									array('data'=>$row['transaction_amount'] < 0 ? to_currency($row['transaction_amount'] * -1)  : to_currency(0), 'align'=> 'right'),
									array('data'=>to_currency($row['balance']), 'align'=> 'right'),
									array('data'=>$row['items'], 'align'=> 'left'),
									array('data'=>$row['comment'], 'align'=> 'left'));
									
		}

		$data = array(
			"title" => lang('reports_store_account_activity_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	function supplier_store_account_outstanding($supplier_id = -1, $show_paid = 0, $export_excel = 0, $offset = 0)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$this->load->model('reports/Store_account_outstanding_supplier');
		$model = $this->Store_account_outstanding_supplier;
		$model->setParams(array('supplier_id' => $supplier_id, 'show_paid' => $show_paid, 'export_excel' => $export_excel, 'offset' => $offset));
		$config = array();
		$config['base_url'] = site_url("reports/supplier_store_account_outstanding/$supplier_id/$show_paid/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 6;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		foreach($report_data as $row)
		{			
			
			if ($row['paid'])
			{
				$mark_paid_unpaid=anchor('reports/supplier_store_account_outstanding_mark_as_unpaid/'.$row['receiving_id'].'/'.$supplier_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_as_unpaid'),"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_mark_as_unpaid')).", this)' class='btn btn-success'");
			}
			else
			{
				$mark_paid_unpaid=anchor('reports/supplier_store_account_outstanding_mark_as_paid/'.$row['receiving_id'].'/'.$supplier_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_as_paid'),"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_mark_as_paid')).", this)' class='btn btn-danger'");
			}
			
			
			$tabular_data[] = array(
				array('data'=>anchor('receivings/receipt/'.$row['receiving_id'], 'RECV '.$row['receiving_id'], array('target' => '_blank')), 'align'=> 'left'),
				array('data'=>$row['supplier_name'].(isset($row['account_number']) && $row['account_number'] ? ' ('.$row['account_number'].')' : ''), 'align'=> 'left'),
				array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['receiving_time'])), 'align'=> 'left'),
				array('data'=>to_currency($row['payment_amount']), 'align'=> 'left'),
			 	array('data'=>$row['comment'], 'align'=> 'left'),
				array('data'=>$mark_paid_unpaid, 'align'=> 'center')
			);
		}

		$mark_all_paid=anchor('reports/supplier_store_account_outstanding_mark_all_as_paid/'.$supplier_id.'/'.$show_paid.'/'.$offset, lang('reports_mark_all_as_paid'), 
		"onclick='return do_link_confirm(".json_encode(lang('reports_confirm_all_mark_as_paid')).", this)'");

		$data = array(
			"title" => lang('reports_outstanding_receivings_report'),
			"subtitle" => $mark_all_paid,
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
		
	}
	
	function supplier_store_account_outstanding_mark_as_paid($receiving_id,$supplier_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$this->db->insert('supplier_store_accounts_paid_receivings',array('receiving_id' => $receiving_id,'store_account_payment_receiving_id' => NULL));
		redirect('reports/supplier_store_account_outstanding/'.$supplier_id.'/'.$show_paid.'/0/'.$offset);
	
	}
	
	function supplier_store_account_outstanding_mark_as_unpaid($receiving_id,$supplier_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$this->db->delete('supplier_store_accounts_paid_receivings',array('receiving_id' => $receiving_id));
		redirect('reports/supplier_store_account_outstanding/'.$supplier_id.'/'.$show_paid.'/0/'.$offset);		
	}
	
	function supplier_store_account_outstanding_mark_all_as_paid($supplier_id,$show_paid,$offset)
	{
		$this->check_action_permission('view_store_account_suppliers');
		$this->load->model('Receiving');
		$this->Receiving->mark_all_unpaid_receivings_paid($supplier_id);
		redirect('reports/supplier_store_account_outstanding/'.$supplier_id.'/'.$show_paid.'/0/'.$offset);
	}	
	
	function receivings_summary_payments($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $receiving_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);
		

		$this->load->model('reports/Summary_payments_receivings');
		$model = $this->Summary_payments_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'receiving_type' => $receiving_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		$receiving_ids = $model->get_receiving_ids_for_payments();

		$config = array();
		$config['base_url'] = site_url("reports/receivings_summary_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$receiving_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');$this->pagination->initialize($config);
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$model_compare = $this->Summary_payments_receivings;
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'receiving_type' => $receiving_type, 'offset'=> $offset, 'export_excel' => $export_excel));
			$receiving_ids = $model_compare->get_receiving_ids_for_payments();

			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}
		
		
		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				if (isset($report_data_compare[$row['payment_type']]))
				{
					$row_compare = $report_data_compare[$row['payment_type']];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$tabular_data[] = array(array('data'=>$row['payment_type'], 'align'=>'left'),array('data'=>to_currency($row['payment_amount']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['payment_amount'] >= $row['payment_amount'] ? ($row['payment_amount'] == $row_compare['payment_amount'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['payment_amount']) .'</span>':''), 'align'=>'right'));
		}
				
		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}
		
		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
	
	function receivings_detailed_payments($start_date, $end_date, $receiving_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Detailed_payments_receivings');
		$model = $this->Detailed_payments_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'receiving_type' => $receiving_type, 'offset'=> $offset, 'export_excel' => $export_excel));
		$receiving_ids = $model->get_receiving_ids_for_payments();
		
		$config = array();
		$config['base_url'] = site_url("reports/receivings_detailed_payments/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$receiving_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;
		$this->load->library('pagination');$this->pagination->initialize($config);

		$headers = $model->getDataColumns();
		$report_data = $model->getData();

		$summary_data = array();
		$details_data = array();

		
		foreach($report_data['summary'] as $receiving_id=>$row)
		{		
			foreach($row as $payment_type => $payment_data_row)
			{
				$summary_data_row = array();
				
				
				$summary_data_row[] = array('data'=>anchor('receivings/receipt/'.$payment_data_row['receiving_id'], '<i class="ion-printer"></i>', array('target' => '_blank', 'class'=>'hidden-print')).'<span class="visible-print">'.$payment_data_row['receiving_id'].'</span>'.anchor('receivings/edit/'.$payment_data_row['receiving_id'], '<i class="ion-document-text"></i>', array('target' => '_blank')).' '.anchor('receivings/edit/'.$payment_data_row['receiving_id'], lang('common_edit').' '.$payment_data_row['receiving_id'], array('target' => '_blank','class'=>'hidden-print')), 'align'=>'left', 'detail_id' => $payment_data_row['receiving_id']);
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['receiving_time'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($payment_data_row['payment_date'])), 'align'=>'left');
				$summary_data_row[] = array('data'=>$payment_data_row['payment_type'], 'align'=>'left');
				$summary_data_row[] = array('data'=>to_currency($payment_data_row['payment_amount']), 'align'=>'right');

				$summary_data[$receiving_id.'|'.$payment_type] = $summary_data_row;
			}
		}

		$temp_details_data = array();
		
		foreach($report_data['details']['receiving_ids'] as $receiving_id => $drows)
		{
			$payment_types = array();
			foreach ($drows as $drow)
			{
				$payment_types[$drow['payment_type']] = TRUE;
			}
			
			foreach(array_keys($payment_types) as $payment_type)
			{
				foreach ($drows as $drow)
				{
					$details_data_row = array();
					$details_data_row[] = array('data'=>date(get_date_format().'-'.get_time_format(), strtotime($drow['payment_date'])), 'align'=>'left');
					$details_data_row[] = array('data'=>$drow['payment_type'], 'align'=>'left');
					$details_data_row[] = array('data'=>to_currency($drow['payment_amount']), 'align'=>'right');

					$details_data[$receiving_id.'|'.$payment_type][] = $details_data_row;
					
					
				}
			}
		}
	
		$data = array(
			"title" =>lang('reports_detailed_payments_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"overall_summary_data" => $model->getSummaryData(),
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
			"report_model" => 'Detailed_payments_receivings'
			
		);
		
			
		isset($details_data) && !empty($details_data) ? $data["details_data"]=$details_data: '' ;
		
		$this->load->view("reports/tabular_details",$data);
	}
	
	function receivings_graphical_summary_payments($start_date, $end_date, $receiving_type)
	{
		$this->load->model('Receiving');
		$this->check_action_permission('view_receivings');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments_receivings');
		$model = $this->Summary_payments_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'receiving_type' => $receiving_type));
		$receiving_ids = $model->get_receiving_ids_for_payments();

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"graph_file" => site_url("reports/receivings_graphical_summary_payments_graph/$start_date/$end_date/$receiving_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function receivings_graphical_summary_payments_graph($start_date, $end_date, $receiving_type)
	{
		$this->load->model('Receiving');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_payments_receivings');
		$model = $this->Summary_payments_receivings;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'receiving_type' => $receiving_type));

		$receiving_ids = $model->get_receiving_ids_for_payments();
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['payment_type']] = to_currency_no_money($row['payment_amount']);
		}
		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
				

		$data = array(
			"title" => lang('reports_payments_summary_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		);

		$this->load->view("reports/graphs/bar",$data);
	}
	
	function summary_items_variance($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		$this->check_action_permission('view_items');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_items_price_variance');
		$model = $this->Summary_items_price_variance;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_items_variance/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['item_number'], 'align' => 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align' => 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity']), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			
			$data_row[] = array('data'=>'<span class="compare '.($row['variance_from_sale_price'] >=0 ? ($row['variance_from_sale_price'] == 0 ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row['variance_from_sale_price']) .'</span>', 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			$tabular_data[] = $data_row;			
		}


		$summary_data = $model->getSummaryData();

		foreach($summary_data as $key=>$value)
		{
			if ($key == 'variance')
			{
				$summary_data[$key] = '<span class="compare '.($summary_data[$key] >= 0 ? (0 == $summary_data[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($value).'</span>';
			}
		}


		$data = array(
			"title" => lang('reports_price_variance_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
		
	}
	
	function summary_item_kits_variance($start_date, $end_date, $sale_type, $export_excel=0, $offset=0)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		$this->check_action_permission('view_item_kits');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		
		$this->load->model('reports/Summary_item_kits_price_variance');
		$model = $this->Summary_item_kits_price_variance;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset'=>$offset, 'export_excel' => $export_excel));

		$config = array();
		$config['base_url'] = site_url("reports/summary_item_kits_variance/".rawurlencode($start_date).'/'.rawurlencode($end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 7;		
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();

		foreach($report_data as $row)
		{
			$data_row = array();
			
			$data_row[] = array('data'=>$row['name'], 'align' => 'left');
			$data_row[] = array('data'=>$row['item_kit_number'], 'align' => 'left');
			$data_row[] = array('data'=>$row['product_id'], 'align' => 'left');
			$data_row[] = array('data'=>$this->Category->get_full_path($row['category_id']), 'align' => 'left');
			$data_row[] = array('data'=>to_quantity($row['quantity_purchased']), 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['current_selling_price']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['subtotal']), 'align'=>'right');
			
			$data_row[] = array('data'=>'<span class="compare '.($row['variance_from_sale_price'] >=0 ? ($row['variance_from_sale_price'] == 0 ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row['variance_from_sale_price']) .'</span>', 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['total']), 'align'=>'right');
			$data_row[] = array('data'=>to_currency($row['tax']), 'align'=> 'right');
			
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']), 'align'=>'right');
			}
			$tabular_data[] = $data_row;			
		}

		$summary_data = $model->getSummaryData();

		foreach($summary_data as $key=>$value)
		{
			if ($key == 'variance')
			{
				$summary_data[$key] = '<span class="compare '.($summary_data[$key] >= 0 ? (0 == $summary_data[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($value).'</span>';
			}
		}

		$data = array(
			"title" => lang('reports_price_variance_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
		
	}
	
	
	function graphical_summary_manufacturers($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->check_action_permission('view_manufacturers');
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_manufacturers');
		$model = $this->Summary_manufacturers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));


		$data = array(
			"title" => lang('reports_manufacturers_report'),
			"graph_file" => site_url("reports/graphical_summary_manufacturers_graph/$start_date/$end_date/$sale_type"),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)),
			"summary_data" => $model->getSummaryData()
		);

		$this->load->view("reports/graphical",$data);
	}

	//The actual graph data
	function graphical_summary_manufacturers_graph($start_date, $end_date, $sale_type)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		
		$start_date=rawurldecode($start_date);
		$end_date=date('Y-m-d 23:59:59', strtotime(rawurldecode($end_date)));
		
		$this->load->model('reports/Summary_manufacturers');
		$model = $this->Summary_manufacturers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$report_data = $model->getData();

		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['manufacturer']] = to_currency_no_money($row['total']);
		}

		$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';

		$data = array(
			"title" => lang('reports_manufacturers_report'),
			"data" => $graph_data,
			"tooltip_template" => "<%=label %>: ".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%= parseFloat(Math.round(value * 100) / 100).toFixed(".$this->decimals.") %>".($this->config->item('currency_symbol_location') =='after' ? $currency_symbol: ''),
		   "legend_template" => "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%> (".((!$this->config->item('currency_symbol_location') || $this->config->item('currency_symbol_location') =='before') ? $currency_symbol : '')."<%=parseFloat(Math.round(segments[i].value * 100) / 100).toFixed(".$this->decimals.")%>".($this->config->item('currency_symbol_location') =='after' ?  $currency_symbol : '').")<%}%></li><%}%></ul>"
		);

		$this->load->view("reports/graphs/pie",$data);
	}	
	
	function summary_manufacturers($start_date, $end_date,$do_compare, $compare_start_date, $compare_end_date, $sale_type, $export_excel=0, $offset = 0)
	{
		$this->load->model('Sale');
		$this->load->model('Category');
		
		$this->check_action_permission('view_manufacturers');
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);
		$compare_start_date=rawurldecode($compare_start_date);
		$compare_end_date=rawurldecode($compare_end_date);

		$this->load->model('reports/Summary_manufacturers');
		$model = $this->Summary_manufacturers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'export_excel'=>$export_excel, 'offset' => $offset));
		
		$config = array();
		$config['base_url'] = site_url("reports/summary_manufacturers/".rawurlencode($start_date).'/'.rawurlencode($end_date).'/'.$do_compare.'/'.rawurlencode($compare_start_date).'/'.rawurlencode($compare_end_date)."/$sale_type/$export_excel");
		$config['total_rows'] = $model->getTotalRows();
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['uri_segment'] = 10;

		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$tabular_data = array();
		$report_data = $model->getData();
		$summary_data = $model->getSummaryData();
		
		if ($do_compare)
		{
			$compare_to_categories = array();
			
			for($k=0;$k<count($report_data);$k++)
			{
				$compare_to_manufacturers[] = $report_data[$k]['manufacturer_id'];
			}
			
			$model_compare = $this->Summary_manufacturers;			
			$model_compare->setParams(array('start_date'=>$compare_start_date, 'end_date'=>$compare_end_date, 'sale_type' => $sale_type, 'offset' => $offset, 'export_excel' => $export_excel, 'compare_to_manufacturers' =>$compare_to_manufacturers));
			
			$report_data_compare = $model_compare->getData();
			$report_data_summary_compare = $model_compare->getSummaryData();
		}

		foreach($report_data as $row)
		{
			if ($do_compare)
			{
				$index_compare = -1;
				$compare_to_manufacturer = $row['manufacturer_id'];
				
				for($k=0;$k<count($report_data_compare);$k++)
				{
					if ($report_data_compare[$k]['manufacturer_id'] == $compare_to_manufacturer)
					{
						$index_compare = $k;
						break;
					}
				}
				
				if (isset($report_data_compare[$index_compare]))
				{
					$row_compare = $report_data_compare[$index_compare];
				}
				else
				{
					$row_compare = FALSE;
				}
			}
			
			$data_row = array();
			
			$data_row[] = array('data'=>$row['manufacturer'], 'align' => 'left');
			$data_row[] = array('data'=>to_currency($row['subtotal']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['subtotal'] >= $row['subtotal'] ? ($row['subtotal'] == $row_compare['subtotal'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['subtotal']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['total']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['total'] >= $row['total'] ? ($row['total'] == $row_compare['total'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['total']) .'</span>':''), 'align' => 'right');
			$data_row[] = array('data'=>to_currency($row['tax']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['tax'] >= $row['tax'] ? ($row['tax'] == $row_compare['tax'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['tax']) .'</span>':''), 'align' => 'right');
			if($this->has_profit_permission)
			{
				$data_row[] = array('data'=>to_currency($row['profit']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['profit'] >= $row['profit'] ? ($row['profit'] == $row_compare['profit'] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($row_compare['profit']) .'</span>':''), 'align' => 'right');
			}
			$data_row[] = array('data'=>floatval($row['item_sold']).($do_compare && $row_compare ? ' / <span class="compare '.($row_compare['item_sold'] >= $row['item_sold'] ? ($row['item_sold'] == $row_compare['item_sold'] ?  '' : 'compare_better') : 'compare_worse').'">'.floatval($row_compare['item_sold']) .'</span>':''), 'align' => 'right');
			$tabular_data[] = $data_row;				
		}

		if ($do_compare)
		{
			foreach($summary_data as $key=>$value)
			{
				$summary_data[$key] = to_currency($value) . ' / <span class="compare '.($report_data_summary_compare[$key] >= $value ? ($value == $report_data_summary_compare[$key] ?  '' : 'compare_better') : 'compare_worse').'">'.to_currency($report_data_summary_compare[$key]).'</span>';
			}
			
		}

		$data = array(
			"title" => lang('reports_manufacturers_report'),
			"subtitle" => date(get_date_format(), strtotime($start_date)) .'-'.date(get_date_format(), strtotime($end_date)).($do_compare  ? ' '. lang('reports_compare_to'). ' '. date(get_date_format(), strtotime($compare_start_date)) .'-'.date(get_date_format(), strtotime($compare_end_date)) : ''),
			"headers" => $model->getDataColumns(),
			"data" => $tabular_data,
			"summary_data" => $summary_data,
			"export_excel" => $export_excel,
			"pagination" => $this->pagination->create_links(),
		);

		$this->load->view("reports/tabular",$data);
	}
	
}

?>