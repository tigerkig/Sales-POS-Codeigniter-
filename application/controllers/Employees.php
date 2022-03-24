<?php
require_once ("Person_controller.php");
class Employees extends Person_controller
{
	function __construct()
	{
		parent::__construct('employees');
		$this->lang->load('employees');
		$this->lang->load('module');		
		
	}
	
	function index($offset=0)
	{
		$params = $this->session->userdata('employee_search_data') ? $this->session->userdata('employee_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		if ($offset!=$params['offset'])
		{
		   redirect('employees/index/'.$params['offset']);
		}
		$this->check_action_permission('search');
		$config['base_url'] = site_url('employees/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Employee->search_count_all($data['search']);
			$table_data = $this->Employee->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Employee->count_all();
			$table_data = $this->Employee->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],TRUE);
		}
		
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['manage_table']=get_people_manage_table($table_data,$this);
		$data['default_columns'] = $this->Employee->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_employee_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'], $this->Employee->get_displayable_columns());		
		
		$this->load->view('people/manage',$data);
	}
	
	
	function sorting()
	{
		$this->check_action_permission('search');
		$params = $this->session->userdata('employee_search_data') ? $this->session->userdata('employee_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		

		$employee_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("employee_search_data",$employee_search_data);
		if ($search)
		{
			$config['total_rows'] = $this->Employee->search_count_all($search);
			$table_data = $this->Employee->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir);
		}
		else
		{
			$config['total_rows'] = $this->Employee->count_all();
			$table_data = $this->Employee->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir);
		}
		$config['base_url'] = site_url('employees/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_people_manage_table_data_rows($table_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	function clear_state()
	{
		$this->session->unset_userdata('employee_search_data');
		redirect('employees');
	}

	function check_duplicate()
	{
		echo json_encode(array('duplicate'=>$this->Employee->check_duplicate($this->input->post('term'))));

	}
	/* added for excel expert */
	function excel_export() {
		set_time_limit(0);
		
		$params = $this->session->userdata('employee_search_data') ? $this->session->userdata('employee_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		
		$search = $params['search'] ? $params['search'] : "";
		
		//Filter based on search
		if ($search)
		{
			$data = $this->Employee->search($search,$this->Employee->search_count_all($search),0,$params['order_col'],$params['order_dir'])->result_object();
		}
		else
		{
			$data = $this->Employee->get_all(10000, 0,'last_name','asc',TRUE)->result_object();
		}
		
		$this->load->helper('report');
		$rows = array();
		$row = array(lang('common_username'),lang('common_first_name'),lang('common_last_name'),lang('common_email'),lang('common_phone_number'),lang('common_address_1'),lang('common_address_2'),lang('common_city'),	lang('common_state'),lang('common_zip'),lang('common_country'),lang('common_comments'));
		$rows[] = $row;
		foreach ($data as $r) {
			$row = array(
				$r->username,
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
				$r->comments
			);
			$rows[] = $row;
		}
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($rows,'employees_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}
	
	
	
	
	
	
	/*
	Returns employee table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';

		$employee_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("employee_search_data",$employee_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Employee->search($search,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		$config['base_url'] = site_url('employees/search');
		$config['total_rows'] = $this->Employee->search_count_all($search);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_people_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
		
	}
	
	function mailing_labels($employee_ids)
	{
		$data['mailing_labels'] = array();
		
		foreach(explode('~', $employee_ids) as $employee_id)
		{			
			$employee_info = $this->Employee->get_info($employee_id);
			
			$label = array();
			$label['name'] = $employee_info->first_name.' '.$employee_info->last_name;
			$label['address_1'] = $employee_info->address_1;
			$label['address_2'] = $employee_info->address_2;
			$label['city'] = $employee_info->city;
			$label['state'] = $employee_info->state;
			$label['zip'] = $employee_info->zip;
			$label['country'] = $employee_info->country;
			
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
		$suggestions = $this->Employee->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	function _get_employee_data($employee_id)
	{
		$data = array();
		
		$data['person_info']=$this->Employee->get_info($employee_id);
		$data['logged_in_employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
		$data['all_modules']=$this->Module->get_all_modules();
		$data['controller_name']=strtolower(get_class());

		$locations_list=$this->Location->get_all()->result();
		$authenticated_locations = $this->Employee->get_authenticated_location_ids($employee_id);
		$logged_in_employee_authenticated_locations = $this->Employee->get_authenticated_location_ids($data['logged_in_employee_id']);
		$can_assign_all_locations = $this->Employee->has_module_action_permission('employees', 'assign_all_locations', $this->Employee->get_logged_in_employee_info()->person_id);		

		$locations = array();
		foreach($locations_list as $row)
		{
			$has_access = in_array($row->location_id, $authenticated_locations);
			$can_assign_access = $can_assign_all_locations || (in_array($row->location_id, $logged_in_employee_authenticated_locations));

			$locations[$row->location_id] = array('name' => $row->name, 'has_access' => $has_access, 'can_assign_access' => $can_assign_access);
		}
		
		$data['locations']=$locations;
		
		return $data;
	}
	
	/*
	Loads the employee edit form
	*/
	function view($employee_id=-1,$redirect_code=0)
	{
		$this->load->model('Module_action');
		$this->check_action_permission('add_update');
		$data = $this->_get_employee_data($employee_id);
		$data['redirect_code']=$redirect_code;
		$this->load->view("employees/form",$data);
	}
	
	function clone_employee($employee_id)
	{
		$this->load->model('Module_action');
		
		$this->check_action_permission('add_update');
		$data = $this->_get_employee_data($employee_id);
		
		//Unset unique identifiers
		$data['person_info']->first_name = '';
		$data['person_info']->last_name = '';
		$data['person_info']->email = '';
		$data['person_info']->phone_number = '';
		$data['person_info']->image_id = '';
		$data['person_info']->address_1 = '';
		$data['person_info']->address_2 = '';
		$data['person_info']->comments = '';
		$data['person_info']->username = '';		
		$data['person_info']->employee_number = '';		
		$data['person_info']->birthday = '';
		$data['person_info']->reason_inactive = '';
		$data['person_info']->inactive = '';
		$data['person_info']->termination_date = '';
			
		$data['redirect_code']=2;
		$data['is_clone'] = TRUE;
		$this->load->view("employees/form",$data);
	}
	
	
	function exmployee_exists()
	{
		if($this->Employee->employee_username_exists($this->input->post('username')))
		echo 'false';
		else
		echo 'true';
		
	}
	/*
	Inserts/updates an employee
	*/
	function save($employee_id=-1)
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
		$permission_data = $this->input->post("permissions")!=false ? $this->input->post("permissions"):array();
		$permission_action_data = $this->input->post("permissions_actions")!=false ? $this->input->post("permissions_actions"):array();
		$location_data = $this->input->post('locations');
		$redirect_code=$this->input->post('redirect_code');
		//Password has been changed OR first time password set
		if($this->input->post('password')!='')
		{
			$employee_data=array(
			'username'=>$this->input->post('username'),
			'password'=>md5($this->input->post('password')),
			'inactive'=>$this->input->post('inactive') && $employee_id != 1? 1 : 0,
			'reason_inactive'=>$this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
			'hire_date'=>$this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
			'employee_number'=>$this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
			'birthday'=>$this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
			'termination_date'=>$this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
			'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
			'always_require_password' => $this->input->post('always_require_password') ? 1 : 0,
			'not_required_to_clock_in' => $this->input->post('not_required_to_clock_in') ? 1 : 0,
			);
		}
		else //Password not changed
		{
			$employee_data=array(
				'username'=>$this->input->post('username'),
				'inactive'=>$this->input->post('inactive') && $employee_id != 1? 1 : 0,
				'reason_inactive'=>$this->input->post('reason_inactive') ? $this->input->post('reason_inactive') : NULL,
				'hire_date'=>$this->input->post('hire_date') ? date('Y-m-d', strtotime($this->input->post('hire_date'))) : NULL,
				'employee_number'=>$this->input->post('employee_number') ? $this->input->post('employee_number') : NULL,
				'birthday'=>$this->input->post('birthday') ? date('Y-m-d', strtotime($this->input->post('birthday'))) : NULL,
				'termination_date'=>$this->input->post('termination_date') ? date('Y-m-d', strtotime($this->input->post('termination_date'))) : NULL,
				'force_password_change' => $this->input->post('force_password_change') ? 1 : 0,
				'always_require_password' => $this->input->post('always_require_password') ? 1 : 0,
				'not_required_to_clock_in' => $this->input->post('not_required_to_clock_in') ? 1 : 0,
			);
		}
		
		//Commission
		$employee_data['commission_percent'] = (float)$this->input->post('commission_percent');
		$employee_data['commission_percent_type'] = $this->input->post('commission_percent_type');
		$employee_data['hourly_pay_rate'] = (float)$this->input->post('hourly_pay_rate');
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if ($this->Employee->get_custom_field($k) !== FALSE)
			{
				$employee_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");

				if ($this->Employee->get_custom_field($k,'type') == 'checkbox')
				{
					$employee_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
				elseif($this->Employee->get_custom_field($k,'type') == 'date')
				{
					$employee_data["custom_field_{$k}_value"] = strtotime($this->input->post("custom_field_{$k}_value"));
				}
				else
				{
					$employee_data["custom_field_{$k}_value"] = $this->input->post("custom_field_{$k}_value");
				}
			}
		}
		$this->load->helper('directory');
		
		$valid_languages = str_replace(DIRECTORY_SEPARATOR,'',directory_map(APPPATH.'language/', 1));
		$employee_data=array_merge($employee_data,array('language'=>in_array($this->input->post('language'), $valid_languages) ? $this->input->post('language') : 'english'));
		
		$this->load->helper('demo');
		if ( (is_on_demo_host()) && $employee_id == 1)
		{
			//failure
			echo json_encode(array('success'=>false,'message'=>lang('common_employees_error_updating_demo_admin'),'person_id'=>-1));
		}
		elseif((is_array($location_data) && count($location_data) > 0) && $this->Employee->save_employee($person_data,$employee_data,$permission_data, $permission_action_data, $location_data, $employee_id))
		{
			if ($this->Location->get_info_for_key('mailchimp_api_key'))
			{
				$this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
			}
			
			$success_message = '';
			
			//New employee
			if($employee_id==-1)
			{
				$success_message = lang('common_employees_successful_adding').' '.$person_data['first_name'].' '.$person_data['last_name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_data['person_id'],'redirect_code'=>$redirect_code));
			}
			else //previous employee
			{
				$success_message = lang('common_employees_successful_updating').' '.$person_data['first_name'].' '.$person_data['last_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_id,'redirect_code'=>$redirect_code));
			}
			
			$employee_info = $this->Employee->get_info($employee_id);
			
			//Delete Image
			if($this->input->post('del_image') && $employee_id != -1)
			{
			    if($employee_info->image_id != null)
			    {
					$this->Person->update_image(NULL,$employee_id);
					$this->load->model('Appfile');
					$this->Appfile->delete($employee_info->image_id);
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
				    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]), NULL, $employee_info->image_id);
			    }
						if($employee_id==-1)
						{
			    			$this->Person->update_image($image_file_id,$employee_data['person_id']);
						}
						else
						{
							$this->Person->update_image($image_file_id,$employee_id);
		    			
						}
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('common_employees_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	function set_language()
	{
		//Clear out cache used in sales ui for language matching
		$this->session->unset_userdata('foreign_language_to_cur_language_recv');
		$this->session->unset_userdata('foreign_language_to_cur_language_sales');
		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$this->load->helper('directory');

		$valid_languages = str_replace(DIRECTORY_SEPARATOR,'',directory_map(APPPATH.'language/', 1));
		$language_id=in_array($this->input->post('employee_language_id'), $valid_languages) ? $this->input->post('employee_language_id') : 'english';
		
		$this->load->helper('demo');
		if ( (is_on_demo_host()) && $employee_id == 1)
		{
			$this->Employee->set_language_session($language_id);			
		}
		else
		{
			$this->Employee->set_language($language_id,$employee_id);			
		}
	}

	/*
	This deletes employees from the employees table
	*/
	function delete()
	{
		$this->check_action_permission('delete');
		$employees_to_delete=$this->input->post('ids');
		
		if (in_array(1,$employees_to_delete))
		{
			//failure
			echo json_encode(array('success'=>false,'message'=>lang('employees_cannot_delete_default_user')));
		}
		elseif($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('employees_successful_deleted').' '.
			count($employees_to_delete).' '.lang('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('employees_cannot_be_deleted')));
		}
	}
		
	function cleanup()
	{
		$this->Employee->cleanup();
		echo json_encode(array('success'=>true,'message'=>lang('employees_cleanup_sucessful')));
	}
	
	function reload_table()
	{
		$params = $this->session->userdata('employee_search_data') ? $this->session->userdata('employee_search_data') : array('offset' => 0, 'order_col' => 'last_name', 'order_dir' => 'asc', 'search' => FALSE);
		$config['base_url'] = site_url('customers/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		if ($data['search'])
		{
			$config['total_rows'] = $this->Employee->search_count_all($data['search']);
			$table_data = $this->Employee->search($data['search'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Employee->count_all();
			$table_data = $this->Employee->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
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
			$this->Employee_appconfig->save('employee_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('employee_column_prefs');			
		}
	}
	
	function custom_fields()
	{
		$this->lang->load('config');
		$fields_prefs = $this->config->item('employee_custom_field_prefs') ? unserialize($this->config->item('employee_custom_field_prefs')) : array();
		$data = array_merge(array('controller_name' => strtolower(get_class())),$fields_prefs);
		$this->load->view('custom_fields',$data);
	}
	
	function save_custom_fields()
	{
		$this->load->model('Appconfig');
		$this->Appconfig->save('employee_custom_field_prefs',serialize($this->input->post()));
	}
}
?>