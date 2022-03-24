<?php
require_once ("Person_controller.php");
class Customers extends Person_controller
{
	function __construct()
	{
		parent::__construct('customers');
		$this->lang->load('customers');
		$this->lang->load('module');
		$this->load->model('Customer');
	}
	
	
	function index($offset=0)
	{
		$params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		if ($offset!=$params['offset'])
		{
		   redirect('customers/index/'.$params['offset']);
		}
		$this->check_action_permission('search');
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Customer->search_count_all($data['search']);
			$table_data = $this->Customer->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Customer->count_all();
			$table_data = $this->Customer->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		
		$data['manage_table']=get_people_manage_table($table_data,$this);
		$data['total_rows'] = $config['total_rows'];
		$data['default_columns'] = $this->Customer->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_customer_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'], $this->Customer->get_displayable_columns());		
		
		$this->load->view('people/manage',$data);
	}

	function sorting()
	{
		$params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('order_col' => 'last_name', 'order_dir' => 'asc');
		
		$this->check_action_permission('search');
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];

		$customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("customers_search_data",$customers_search_data);
		
		
		if ($search)
		{
			$config['total_rows'] = $this->Customer->search_count_all($search);
			$table_data = $this->Customer->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir);
		}
		else
		{
			$config['total_rows'] = $this->Customer->count_all();
			$table_data = $this->Customer->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir);
		}
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_people_manage_table_data_rows($table_data,$this);
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));	
		
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$customers_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("customers_search_data",$customers_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Customer->search($search,$per_page,$offset, $order_col ,$order_dir);
		$config['base_url'] = site_url('customers/search');
		$config['total_rows'] = $this->Customer->search_count_all($search);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['total_rows'] = $this->Customer->search_count_all($search);
		$data['manage_table']=get_people_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	function mailing_label_from_summary_customers_report($start_date, $end_date, $sale_type, $total_spent_condition = 'any', $total_spent_amount = 0)
	{
		$start_date=rawurldecode($start_date);
		$end_date=rawurldecode($end_date);

		$this->load->model('Sale');
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type, 'offset' => 0, 'export_excel' => 1, 'total_spent_condition' => $total_spent_condition, 'total_spent_amount' => $total_spent_amount));		
		$report_data = $model->getData();
		
		$customer_ids = array();
		foreach($report_data as $row)
		{
			$customer_ids[] = $row['customer_id'];
		}
		
		foreach($customer_ids as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			$label['address_1'] = $customer_info->address_1;
			$label['address_2'] = $customer_info->address_2;
			$label['city'] = $customer_info->city;
			$label['state'] = $customer_info->state;
			$label['zip'] = $customer_info->zip;
			$label['country'] = $customer_info->country;
			
			$data['mailing_labels'][] = $label;
			
		}
		
		$data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
		
		$this->load->view("mailing_labels", $data);	
		
	}
	
	function mailing_labels($customer_ids)
	{
		$data['mailing_labels'] = array();
		
		foreach(explode('~', $customer_ids) as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			$label['address_1'] = $customer_info->address_1;
			$label['address_2'] = $customer_info->address_2;
			$label['city'] = $customer_info->city;
			$label['state'] = $customer_info->state;
			$label['zip'] = $customer_info->zip;
			$label['country'] = $customer_info->country;
			
			$data['mailing_labels'][] = $label;
			
		}
		$data['type'] = $this->config->item('mailing_labels_type') == 'excel' ? 'excel' : 'pdf';
		$this->load->view("mailing_labels", $data);	
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	/*
	Loads the customer edit form
	*/
	function view($customer_id=-1,$redirect_code=0)
	{
		$this->check_action_permission('add_update');
		$this->load->model('Tier');
		$tiers = array();
		$tiers_result = $this->Tier->get_all()->result_array();
		
		if (count($tiers_result) > 0)
		{
			$tiers[0] = lang('common_none');
			foreach($tiers_result as $tier)
			{
				$tiers[$tier['id']]=$tier['name'];
			}	
		}
		
		$this->load->model('Tax_class');
		
		$data = array();
		$data['tax_classes'] = array();
		$data['tax_classes'][''] = lang('common_none');
		
		foreach($this->Tax_class->get_all()->result_array() as $tax_class)
		{
			$data['tax_classes'][$tax_class['id']] = $tax_class['name'];
		}
		
		$data['controller_name']=strtolower(get_class());
		$data['tiers']=$tiers;
		$data['person_info']=$this->Customer->get_info($customer_id);
		$this->load->model('Customer_taxes');
		$data['customer_tax_info']=$this->Customer_taxes->get_info($customer_id);
		
		$data['redirect_code']=$redirect_code;
		$this->load->view("customers/form",$data);
	}
	
	function account_number_exists()
	{
		if($this->Customer->account_number_exists($this->input->post('account_number')))
		echo 'false';
		else
		echo 'true';
		
	}

	function clear_state()
	{
		$this->session->unset_userdata('customers_search_data');
		redirect('customers');
	}
	/*
	Inserts/updates a customer
	*/
	function save($customer_id=-1)
	{
		$this->check_action_permission('add_update');
		$person_data = array(
		'first_name'=>$this->input->post('first_name'),
		'last_name'=>$this->input->post('last_name'),
		'email'=>$this->input->post('email'),
		'phone_number'=>$this->input->post('phone_number'),
		'address_1'=>$this->input->post('address_1'),
		'address_2'=>$this->input->post('address_2'),
		'city'=>$this->input->post('city'),
		'state'=>$this->input->post('state'),
		'zip'=>$this->input->post('zip'),
		'country'=>$this->input->post('country'),
		'comments'=>$this->input->post('comments')
		);
		
		
		$customer_data=array(
			'company_name' => $this->input->post('company_name'),
			'tier_id' => $this->input->post('tier_id') ? $this->input->post('tier_id') : NULL,
			'account_number'=>$this->input->post('account_number')=='' ? null:$this->input->post('account_number'),
			'taxable'=>$this->input->post('taxable')=='' ? 0:1,
			'tax_certificate' => $this->input->post('tax_certificate'),
			'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
			'tax_class_id'=> $this->input->post('tax_class') ? $this->input->post('tax_class') : NULL,
		);
		
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Customer->get_custom_field($k) !== FALSE)
			{
				$customer_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
			
				if ($this->Customer->get_custom_field($k,'type') == 'checkbox')
				{
					$customer_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
				elseif($this->Customer->get_custom_field($k,'type') == 'date')
				{
					$customer_data["custom_field_{$k}_value"] = strtotime($this->input->post("custom_field_{$k}_value"));
				}
				else
				{
					$customer_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
			}
		}

		if ($this->config->item('enable_customer_loyalty_system'))
		{
			$customer_data['disable_loyalty'] = $this->input->post('disable_loyalty') ? 1 : 0;
		}
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' &&  count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2)
		{
      	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
			$customer_data['current_spend_for_points'] = $spend_amount_for_points - $this->input->post('amount_to_spend_for_next_point');
		}
		elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			$number_of_sales_for_discount = $this->config->item('number_of_sales_for_discount'); 
			$customer_data['current_sales_for_discount'] = $number_of_sales_for_discount - (float)$this->input->post('sales_until_discount');			
		}
		
		if ($this->input->post('balance')!== NULL && is_numeric($this->input->post('balance')))
		{
			$customer_data['balance'] = $this->input->post('balance');
		}

		if ($this->input->post('credit_limit')!== NULL && is_numeric($this->input->post('credit_limit')))
		{
			$customer_data['credit_limit'] = $this->input->post('credit_limit');
		}
		elseif($this->input->post('credit_limit') === '')
		{
			$customer_data['credit_limit'] = NULL;
		}
		
		if ($this->input->post('points')!== NULL && is_numeric($this->input->post('points')))
		{
			$customer_data['points'] = $this->input->post('points');
		}
		
		$redirect_code=$this->input->post('redirect_code');
		if ($this->input->post('delete_cc_info'))
		{
			$customer_data['cc_token'] = NULL;
			$customer_data['cc_ref_no'] = NULL;
			$customer_data['cc_preview'] = NULL;
			$customer_data['card_issuer'] = NULL;			
		}
		
		if($this->Customer->save_customer($person_data,$customer_data,$customer_id))
		{
			if ($this->Location->get_info_for_key('mailchimp_api_key'))
			{
				$this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
			}
	

			$success_message = '';
			
			//New customer
			if($customer_id==-1)
			{
				$success_message = lang('customers_successful_adding').' '.$person_data['first_name'].' '.$person_data['last_name'];
				echo json_encode(array('success'=>true,'message'=> $success_message,'person_id'=>$customer_data['person_id'],'redirect_code'=>$redirect_code));
				$customer_id = $customer_data['person_id'];
				
			}
			else //previous customer
			{
				$success_message = lang('customers_successful_updating').' '.$person_data['first_name'].' '.$person_data['last_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$customer_id,'redirect_code'=>$redirect_code));
			}
			
			$customers_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$customers_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->load->model('Customer_taxes');
			$this->Customer_taxes->save($customers_taxes_data, $customer_id);
			
				$customer_info = $this->Customer->get_info($customer_id);
				
				//Delete Image
				if($this->input->post('del_image') && $customer_id != -1)
				{	
				    if($customer_info->image_id != null)
				    {
						$this->Person->update_image(NULL,$customer_id);
						$this->load->model('Appfile');
						$this->Appfile->delete($customer_info->image_id);
				    }
				}

				//Save Image File
				if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
				{			    

				    $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
					$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));
				    if (in_array($extension, $allowed_extensions))
				    {
					    $config['image_library'] = 'gd2';
					    $config['source_image']	= $_FILES["image_id"]["tmp_name"];
					    $config['create_thumb'] = FALSE;
					    $config['maintain_ratio'] = TRUE;
					    $config['width']	 = 400;
					    $config['height']	= 300;
					    $this->load->library('image_lib', $config); 
					    $this->image_lib->resize();
						$this->load->model('Appfile');
					    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]), NULL , $customer_info->image_id);
				    }
					
					if($customer_id==-1)
					{
		    			$this->Person->update_image($image_file_id,$customer_data['person_id']);
					}
					else
					{
						$this->Person->update_image($image_file_id,$customer_id);
	    			
					}
				}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('customers_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	/*
	This deletes customers from the customers table
	*/
	function delete()
	{
		$this->check_action_permission('delete');
		$customers_to_delete=$this->input->post('ids');
		
		if($this->Customer->delete_list($customers_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('customers_successful_deleted').' '.
			count($customers_to_delete).' '.lang('customers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('customers_cannot_be_deleted')));
		}
	}
	
	function _excel_get_header_row()
	{		
		$return = array(lang('common_first_name'),lang('common_last_name'),lang('common_email'),lang('common_phone_number'),lang('common_address_1'),lang('common_address_2'),lang('common_city'),	lang('common_state'),lang('common_zip'),lang('common_country'),lang('common_comments'),lang('customers_account_number'),lang('common_taxable'),lang('customers_tax_certificate'), lang('customers_company_name'),lang('common_tier_name'));
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Customer->get_custom_field($k) !== FALSE)
			{
				$return[] = $this->Customer->get_custom_field($k);
			}
		}
	
		if ($this->config->item('customers_store_accounts'))
		{
			$return[] = lang('common_balance');
			$return[] = lang('common_credit_limit');
		}
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$return[] = lang('common_points');
			$return[] = lang('customers_amount_to_spend_for_next_point');
			$return[] = lang('common_disable_loyalty');
		}
		
		
		return $return;
	}
		
	function excel()
	{
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		
		$this->load->helper('spreadsheet');
		array_to_spreadsheet(array($header_row),'import_customers.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}
	
	function excel_import()
	{
		$this->check_action_permission('add_update');
		$this->load->view("customers/excel_import", null);
	}
	
	function check_duplicate()
	{
		echo json_encode(array('duplicate'=>$this->Customer->check_duplicate($this->input->post('name'),$this->input->post('email'),$this->input->post('phone_number'))));
	}
	/* added for excel expert */
	function excel_export() {
		
		$this->load->helper('download');
		set_time_limit(0);
		
		$params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		
		$search = $params['search'] ? $params['search'] : "";
		
		//Filter based on search
		if ($search)
		{
			$data = $this->Customer->search($search,$this->Customer->search_count_all($search),0,$params['order_col'],$params['order_dir'])->result_object();
		}
		else
		{
			$data = $this->Customer->get_all($this->Customer->count_all())->result_object();
		}
		
		$tiers = array();
		$this->load->model('Tier');
		foreach($this->Tier->get_all()->result_array() as $tier)
		{
			$tiers[$tier['id']] = $tier['name'];
		}
		
		$this->load->helper('report');
		$rows = array();
		
		$header_row = $this->_excel_get_header_row();
		$header_row[] = lang('customers_customer_id');
		$rows[] = $header_row;
		
		foreach ($data as $r) {
			$row = array(
				$r->first_name,
				$r->last_name,
				$r->email,
				$r->phone_number,
				$r->address_1,
				$r->address_2,
				$r->city,
				$r->state,
				$r->zip,
				$r->country,
				$r->comments,
				$r->account_number,
				$r->taxable ? 'y' : 'n',
				$r->tax_certificate,
				$r->company_name,
				isset($tiers[$r->tier_id]) ?  $tiers[$r->tier_id] : '',
			);
			
			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
			{
				$type = $this->Customer->get_custom_field($k,'type');
				$name = $this->Customer->get_custom_field($k,'name');
				
				if ($name !== FALSE)
				{
					if ($type == 'date')
					{
						$row[] = date(get_date_format(),$r->{"custom_field_{$k}_value"});
					}
					elseif($type=='checkbox')
					{
						$row[] = $r->{"custom_field_{$k}_value"} ? '1' : '0';					
					}
					else
					{
						$row[] = $r->{"custom_field_{$k}_value"};				
					}
				}
			}
			
			if ($this->config->item('customers_store_accounts'))
			{
				$row[] = $r->balance ? to_currency_no_money($r->balance) : '';
				$row[] = $r->credit_limit ? to_currency_no_money($r->credit_limit) : '';
			}
			
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
			{
		      	list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
				
				$row[] = $r->points ? to_quantity($r->points) : '';		
				$row[] = $spend_amount_for_points - $r->current_spend_for_points;	
				$row[] = $r->disable_loyalty ? 'y' : '';				
									
			}
			
			
			$row[] = $r->person_id;

			$rows[] = $row;
		}
		
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($rows,'customers_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}
	
	function do_excel_upload()
	{
		$this->load->helper('demo');
				
		//Write to app files
 	 	$this->load->model('Appfile');
		$cur_timezone = date_default_timezone_get();
		//We are doing this to make sure same timezone is used for expiration date
		date_default_timezone_set('America/New_York');
    $app_file_file_id = $this->Appfile->save($_FILES["file"]["name"], file_get_contents($_FILES["file"]["tmp_name"]),'+3 hours');
		date_default_timezone_set($cur_timezone);
		//Store file_id from app files in session so we can reference later
		$this->session->set_userdata("excel_import_file_id",$app_file_file_id);
		
		$file_info = pathinfo($_FILES["file"]["name"]);		
		$file = $this->Appfile->get($this->session->userdata('excel_import_file_id'));
		$tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'cexcel');

		file_put_contents($tmpFilename,$file->file_data);
		$this->load->helper('spreadsheet');
		
		$first_row = get_spreadsheet_first_row($tmpFilename,$file_info['extension']);
		unlink($tmpFilename);
		
		$fields = $this->_get_database_fields_for_import_as_array();
		
		$k=0;
		foreach($first_row as $col_name)
		{
			$column =  array('Spreadsheet Column' => $col_name, 'Index' => $k);
			
			if($column['Spreadsheet Column'] == '')
			{
				echo json_encode(array('success'=>false,'message'=> lang('common_spreadsheet_columns_must_have_labels')));
				return;
			}
			
			$cols = array_column($fields, 'Name');
			$cols = array_map('strtolower', $cols);
			$search = strtolower($column['Spreadsheet Column']);
			$matchIndex = array_search($search, $cols);

			if (is_numeric($matchIndex))
			{
				$column['Database Field'] = $fields[$matchIndex]['Id'];
			}
			
			$columns[] = $column;
			$k++;
		}
		
		$this->session->set_userdata("customers_excel_import_column_map", $columns);
		echo json_encode(array('success'=>true,'message'=>lang('common_import_successful')));
	}
	
	function do_excel_import_map()
	{
		$this->load->helper('text');
 	 	$this->load->model('Appfile');
		
		$file = $this->Appfile->get($this->session->userdata('excel_import_file_id'));

		$tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'cexcel');

		file_put_contents($tmpFilename,$file->file_data);
		$this->load->helper('spreadsheet');

		$file_info = pathinfo($file->file_name);
		$sheet = file_to_spreadsheet($tmpFilename,$file_info['extension']);
		unlink($tmpFilename);

		$this->sheet_data = array();

		$columns = array();
		$k=0;

		$fields = $this->_get_database_fields_for_import_as_array();
		$numRows = $sheet->getNumberOfRows();

		while($col_name = $sheet->getCellByColumnAndRow($k,1))
		{
			$column =  array('Spreadsheet Column' => $col_name, 'Index' => $k);

			$cols = array_column($fields, 'Name');
			$cols = array_map('strtolower', $cols);
			$search = strtolower($column['Spreadsheet Column']);
			$matchIndex = array_search($search, $cols);

			if (is_numeric($matchIndex))
			{
				$column['Database Field'] = $fields[$matchIndex]['Id'];
			}

	    $col_data = array();
			for ($i = 2; $i <= $numRows; $i++) 
			{
	  		$col_data[] = trim(clean_string($sheet->getCellByColumnAndRow($k,$i)));
			}

			$column["data"] = $col_data;

			$columns[] = $column;
			$k++;
		}
		
		$this->session->set_userdata("customers_excel_import_num_rows", $numRows);
		$this->session->set_userdata("customers_excel_import_column_map", $columns);
	}
	
	function get_database_fields_for_import()
	{
		$fields = $this->_get_database_fields_for_import_as_array();
		array_unshift($fields , array('Name' => '', 'Id' => -1));
		echo json_encode($fields);
	}
	
	private function _get_database_fields_for_import_as_array()
	{		
		$this->load->model('Tier');
		$fields = array();

		$fields[] = array('Name' => lang('common_first_name'), 'key' => 'first_name');
		$fields[] = array('Name' => lang('common_last_name'), 'key' => 'last_name');
		$fields[] = array('Name' => lang('common_email'), 'key' => 'email');
		$fields[] = array('Name' => lang('common_phone_number'), 'key' => 'phone_number');
		$fields[] = array('Name' => lang('common_address_1'), 'key' => 'address_1');
		$fields[] = array('Name' => lang('common_address_2'), 'key' => 'address_2');
		$fields[] = array('Name' => lang('common_city'), 'key' => 'city');
		$fields[] = array('Name' => lang('common_state'), 'key' => 'state');
		$fields[] = array('Name' => lang('common_zip'), 'key' => 'zip');
		$fields[] = array('Name' => lang('common_country'), 'key' => 'country');
		$fields[] = array('Name' => lang('common_comments'), 'key' => 'comments');
		$fields[] = array('Name' => lang('customers_account_number'), 'key' => 'account_number');
		$fields[] = array('Name' => lang('common_taxable'), 'key' => 'taxable');
		$fields[] = array('Name' => lang('customers_tax_certificate'), 'key' => 'tax_certificate');
		$fields[] = array('Name' => lang('customers_company_name'), 'key' => 'company_name');
		$fields[] = array('Name' => lang('common_tier_name'), 'key' => 'tier_id');
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Customer->get_custom_field($k) !== FALSE)
			{
				$fields[] = array('Name' => $this->Customer->get_custom_field($k), 'key' => 'custom_field_'.$k.'_value');			
			}	
		}
		
		if ($this->config->item('customers_store_accounts'))
		{
			$fields[] = array('Name' => lang('common_balance'), 'key' => 'balance');
			$fields[] = array('Name' => lang('common_credit_limit'), 'key' => 'credit_limit');
		}
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$fields[] = array('Name' => lang('common_points'), 'key' => 'points');
			$fields[] = array('Name' => lang('customers_amount_to_spend_for_next_point'), 'key' => 'current_spend_for_points');
			$fields[] = array('Name' => lang('common_disable_loyalty'), 'key' => 'disable_loyalty');
		}
				
		$fields[] = array('Name' => lang('customers_customer_id'), 'key' => 'person_id');
		
		$id = 0;
		foreach($fields as &$field)
		{
			$field['Id'] = $id;
			$id++;
		}
		unset($field);
		
		return $fields;
	}
	
	function get_uploaded_excel_columns()
	{
		$data = $this->session->userdata("customers_excel_import_column_map");
		
		foreach($data as &$col)
		{
			unset($col["data"]);
		}
		
		echo json_encode($data);
	}
	
	public function set_excel_columns_map()
	{	
		$data = $this->session->userdata("customers_excel_import_column_map");
		
		$mapKeys = json_decode($this->input->post('mapKeys'), true);
		
		foreach($mapKeys as $mapKey)
		{
			foreach ($data as $key => $col) 
			{
	       if ($col['Index'] == $mapKey["Index"])
				 {
					 $data[$key]["Database Field"] = $mapKey["Database Field"];
	       }
			}
		}	
		
		$this->session->set_userdata("customers_excel_import_column_map", $data);
	}
	
	private function _indexColumnArray($n)
	{
		if (isset($n['Database Field']))
		{
			return $n['Database Field'];
		}
		
		return 'N/A';
	}
	
	//dedup
	function dedup_excel_import_data()
	{
		$this->session->set_userdata('excel_import_error_log', NULL);
		$columns_with_data = $this->session->userdata("customers_excel_import_column_map");
		
		$fieldId_to_colIndex = array_flip(array_map(array($this, '_indexColumnArray'), $columns_with_data));
		unset($fieldId_to_colIndex['N/A']);
		unset($fieldId_to_colIndex[-1]);
		
		$account_num_dups = array();
		if (isset($fieldId_to_colIndex[11]))
		{
			$account_num_index = $fieldId_to_colIndex[11];
			$account_nums = $columns_with_data[$account_num_index]['data'] ? $columns_with_data[$account_num_index]['data'] : array();
			$account_num_dups = $this->_get_keys_for_duplicate_values($account_nums);
		
			foreach($account_num_dups as $key => $val)
			{
				foreach($val as $v)
				{
					$row = $v+2;
					$message = 'Duplicate Account Number "'. $key .'" in Spreadsheet';
					$this->_log_validation_error($row, $message, 'Error');
				}
			}
		}
		
		if(count($account_num_dups) > 0)
		{
			echo json_encode(array('type'=> 'error','message'=> lang('customers_duplicate_account_numbers'), 'title' =>  lang('common_error')));
		} else {
			echo json_encode(array('type'=> 'success','message'=> lang('customers_no_duplicate_account_numbers'), 'title' =>  lang('common_success')));
		}
	}
	
	private function _get_keys_for_duplicate_values($my_arr) 
	{
    $dups = array();;
		$new_arr = array();
		
    foreach ($my_arr as $key => $val) {
			if(!$val)
			{
				continue;
			}
			
      if (!isset($new_arr[$val])) {
         $new_arr[$val] = $key;
      } else {
        if (isset($dups[$val])) {
           $dups[$val][] = $key;
        } else {
           // include the initial key in the dups array.
           $dups[$val] = array($new_arr[$val], $key);
        }
      }
    }
    return $dups;
	}
	
	//new function
	function complete_excel_import()
	{
		set_time_limit(0);
		$this->check_action_permission('add_update');
		
		$this->session->set_userdata('excel_import_error_log', NULL);
		
		$numRows = $this->session->userdata("customers_excel_import_num_rows");
		$columns_with_data = $this->session->userdata("customers_excel_import_column_map");
				
		$this->load->model('Tier');
		$this->_tiers = array();
		foreach($this->Tier->get_all()->result_array() as $tier)
		{
			$this->_tiers[$tier['name']] = $tier['id'];
		}
		
		$fields = $this->_get_database_fields_for_import_as_array();
		
		$fieldId_to_colIndex = array_flip(array_map(array($this, '_indexColumnArray'), $columns_with_data));
		unset($fieldId_to_colIndex['N/A']);
		
		$can_commit = TRUE;
		$this->db->trans_begin();
		
		for ($i = 0; $i < $numRows -1; $i++)
		{
			$person_id = FALSE;
			$customer_data = array();
			$person_data = array();
			
			$person_data_keys = array("first_name", "last_name", "email", "phone_number", "address_1", "address_2", "city", "state", "zip", "country", "comments");
			$customer_data_keys = array("account_number", "taxable", "tax_certificate", "company_name", "balance", "credit_limit", "tier_id", "points", "current_spend_for_points","disable_loyalty","custom_field_1_value","custom_field_2_value","custom_field_3_value","custom_field_4_value","custom_field_5_value");
			
			foreach($fields as $field)
			{
				
				if(array_key_exists($field['Id'], $fieldId_to_colIndex))
				{
					$key = $fieldId_to_colIndex[$field['Id']];
				}
				else
				{//if its not mapped skip
					continue;
				}
				
				if($field['key'] !== "")
				{
					if(in_array($field['key'], $person_data_keys))
					{
						$person_data[$field['key']] =  $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i+2);
					}
					elseif(in_array($field['key'], $customer_data_keys))
					{
						$customer_data[$field['key']] =  $this->_clean($field['key'], $columns_with_data[$key]['data'][$i], $i+2);
					}
					elseif($field['key'] == "person_id")
					{
						$person_id = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i]);
					}
				}
			}//end field foreach
			
			//Customer must have a first name to save
			if($person_data['first_name'] && !$this->Customer->save_customer($person_data,$customer_data, $person_id ? $person_id : FALSE))
			{	
				if($person_id === FALSE)
				{
					if(!isset($customer_data['account_number']) || !$person_id = $this->Customer->customer_id_from_account_number($customer_data['account_number']))
					{		
							//couldnt find person id to make second attempt
							$this->_logDbError($i+2);
							$can_commit = FALSE;
							continue;
					}
					
					$customer_data['deleted'] = 0;
					//second attempt
					if($this->Customer->save_customer($person_data,$customer_data, $person_id))
					{
						//second attempt Succeeded
						$this->_log_validation_error($i+2, lang('customers_customer_existed_warning'));
					}
					else
					{
						//second attempt failed
						$this->_logDbError($i+2);
						$can_commit = FALSE;
						continue;
					}
					
				}
				else
				{ //first attempt failed even with customer id
					$this->_logDbError($i+2);
					$can_commit = FALSE;
					continue;
				}	
			}
			
		} //loop done for customers
		
		if ($can_commit)
		{
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}
		
		//if there were any errors or warnings
		if ($this->db->trans_status() === FALSE && !$can_commit)
		{
			echo json_encode(array('type'=> 'error','message'=> lang('common_errors_occured_durring_import'), 'title' =>  lang('common_error')));
		}
		elseif ($this->db->trans_status() === FALSE && $can_commit)
		{
			echo json_encode(array('type'=> 'warning','message'=> lang('common_warnings_occured_durring_import'), 'title' =>  lang('common_warning')));
		}
		else
		{
			//Clear out session data used for import
			$this->session->unset_userdata('excel_import_file_id');
			$this->session->unset_userdata('customers_excel_import_column_map');
			$this->session->unset_userdata('excel_import_num_rows');
			echo json_encode(array('type'=> 'success','message'=>lang('common_import_successful'), 'title' =>  lang('common_success')));			
		}
	}
	
	private function _clean($key, $value, $row = NULL)
	{	
		if ($key == 'first_name'){
			if(!$value)
			{
				 return '';
			}
			return $value;
			
		}
		if ($key == 'last_name'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'email'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'phone_number'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'address_1'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'address_2'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'city'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'state'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'zip'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'country'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'comments'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'account_number'){
			if(!$value)
			{
				 return NULL;
			}
			return $value;
		}
		if ($key == 'taxable'){
			$true_values = array("","true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}

			return 0;
		}
		if ($key == 'tax_certificate'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'company_name'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'tier_id'){
			if(!$value)
			{
				 return NULL;
			}
			else
			{
				$result = isset($this->_tiers[$value]) ? $this->_tiers[$value] : NULL;
				
				if($result === NULL)
				{
					$this->_log_validation_error($row, lang('common_tier' ) . ' "' . $value . "' " . lang('customers_tier_could_not_be_matched'));
				}
			}
			
			return $result;
		}
		if ($key == 'balance'){
			return make_currency_no_money($value);
		}
		if ($key == 'credit_limit'){
			return make_currency_no_money($value);
		}
		if ($key == 'points'){
			if(!$value)
			{
				 return 0;
			}
			return $value;
		}
		if ($key == 'current_spend_for_points'){
			
			list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
		
			if(!$value)
			{
				 return make_currency_no_money($spend_amount_for_points);
			}
			
			$value = $spend_amount_for_points - $value;
			return make_currency_no_money($value);
		}
		if ($key == 'disable_loyalty'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
		
			return 0;
		}
		if ($key == 'person_id'){
			if(!$value)
			{
				 return FALSE;
			}
			return $value;
		}
		
		$custom_fields = array();
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Customer->get_custom_field($k) !== FALSE)
			{
				$custom_fields[] = "custom_field_${k}_value";
			}
		}
		
		if (in_array($key, $custom_fields))
		{
			if(!$value)
			{
				 return '';
			}
			
			$k = substr($key, strlen('custom_field_'),1);
			$type = $this->Customer->get_custom_field($k,'type');
			
			if ($type == 'date')
			{
				$value = strtotime($value);
			}
			
			return $value;
		}
	}
	
	private function _logDbError($index)
	{
		$error = $this->db->error();
		$matches = array();
		preg_match('/for key \'(.+)\'/', $error['message'], $matches);

		if (isset($matches[1]))
		{
			$col_name = $matches[1];
			$data = $this->_get_database_fields_for_import_as_array();
			$cols = array_column($data, 'key');
			$match_index = array_search($col_name, $cols);

			if ($match_index !== FALSE)
			{
				$column_human_name = $data[$match_index]['Name'];
				$error['message'] = str_replace($col_name,$column_human_name,$error['message']);
			}

		}
		$this->_log_validation_error($index, $error['message'], "Error");
	}
	
	private function _log_validation_error($row, $message, $type = "Warning")
	{
		//log errors and warnings for import
		if(!$log = $this->session->userdata('excel_import_error_log'))
		{
			$log = array();
		}
		
		$log[] = array("row" => $row, "message" => $message, "type" => $type);
		
		$this->session->set_userdata('excel_import_error_log', $log);
	}
	
	public function get_import_errors()
	{
		echo json_encode($this->session->userdata('excel_import_error_log'));
	}
		
	function cleanup()
	{
		$this->Customer->cleanup();
		echo json_encode(array('success'=>true,'message'=>lang('customers_cleanup_sucessful')));
	}
		
	function pay_now($customer_id)
	{
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Giftcard');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->library('sale_lib');
    	$this->sale_lib->clear_all();
		$this->sale_lib->set_customer($customer_id);
		$this->sale_lib->set_mode('store_account_payment');
		$store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
		$this->sale_lib->add_item($store_account_payment_item_id.'|FORCE_ITEM_ID|',1);
		redirect('sales');
	}
	
	function reload_table()
	{
		$params = $this->session->userdata('customers_search_data') ? $this->session->userdata('customers_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Customer->search_count_all($data['search']);
			$table_data = $this->Customer->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Customer->count_all();
			$table_data = $this->Customer->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		
		echo get_people_manage_table($table_data,$this);
	}
	
	function save_column_prefs()
	{
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save('customer_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('customer_column_prefs');			
		}
	}
	
	function custom_fields()
	{
		$this->lang->load('config');
		$fields_prefs = $this->config->item('customer_custom_field_prefs') ? unserialize($this->config->item('customer_custom_field_prefs')) : array();
		$data = array_merge(array('controller_name' => strtolower(get_class())),$fields_prefs);
		$this->load->view('custom_fields',$data);
	}
	
	function save_custom_fields()
	{
		$this->load->model('Appconfig');
		$this->Appconfig->save('customer_custom_field_prefs',serialize($this->input->post()));
	}
	
	function generate_barcodes($customer_ids, $skip=0)
	{		
		$customer_ids = explode('~',$customer_ids);
		foreach($customer_ids as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			
			if ($customer_info->company_name)
			{
				$label['name'] .=' ('.$customer_info->company_name.')';
			}
			
			if ($customer_info->account_number)
			{
				$label['id'] = $customer_info->account_number;				
			}
			else
			{
				$label['id'] = number_pad($customer_info->person_id,10);
			}
			
			$data['items'][] = $label;
			
		}
		$data['scale'] = 1;
		$data['skip'] = $skip;
		
		$this->load->view("barcode_sheet", $data);
	}

	function generate_barcode_labels($customer_ids)
	{		
		$customer_ids = explode('~',$customer_ids);
		foreach($customer_ids as $customer_id)
		{			
			$customer_info = $this->Customer->get_info($customer_id);
			
			$label = array();
			$label['name'] = $customer_info->first_name.' '.$customer_info->last_name;
			
			if ($customer_info->company_name)
			{
				$label['name'] .=' ('.$customer_info->company_name.')';
			}
			
			if ($customer_info->account_number)
			{
				$label['id'] = $customer_info->account_number;				
			}
			else
			{
				$label['id'] = number_pad($customer_info->person_id,10);
			}
			
			$data['items'][] = $label;
			
		}
		$data['scale'] = 1;
		$this->load->view("barcode_labels", $data);
	}
	
}
?>