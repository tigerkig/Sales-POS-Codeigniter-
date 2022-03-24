<?php
require_once ("Secure_area.php");
class Home extends Secure_area 
{
	function __construct()
	{
		parent::__construct();	
		$this->load->helper('report');
		$this->lang->load('module');
		$this->lang->load('home');
		$this->load->model('Item');
		$this->load->model('Item_kit');
		$this->load->model('Supplier');
		$this->load->model('Customer');
		$this->load->model('Employee');
		$this->load->model('Giftcard');
		$this->load->model('Sale');
		$this->load->helper('cloud');
	}
	
	function index($choose_location=0)
	{		
		
		if (!$choose_location && $this->config->item('timeclock') && !$this->Employee->is_clocked_in() && !$this->Employee->get_logged_in_employee_info()->not_required_to_clock_in)
		{
			redirect('timeclocks');
		}


		$data['choose_location'] = $choose_location;
		
		$data['total_items']=$this->Item->count_all();
		$data['total_item_kits']=$this->Item_kit->count_all();
		$data['total_suppliers']=$this->Supplier->count_all();
		$data['total_customers']=$this->Customer->count_all();
		$data['total_employees']=$this->Employee->count_all();
		$data['total_locations']=$this->Location->count_all();
		$data['total_giftcards']=$this->Giftcard->count_all();
		$data['total_sales']=$this->Sale->count_all();
		$current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
		$data['message']  = "";
		
		if ($this->Employee->has_module_action_permission('reports', 'view_dashboard_stats', $this->Employee->get_logged_in_employee_info()->person_id))
		{	
			$data['month_sale'] = $this->sales_widget();
		}
		$this->load->helper('demo');
		$data['can_show_mercury_activate'] = (!is_on_demo_host() && !$this->config->item('mercury_activate_seen')) && !$this->Location->get_info_for_key('enable_credit_card_processing');		
		
		if (is_on_phppos_host())
		{
			$this->lang->load('login');
			$site_db = $this->load->database('site', TRUE);
			
			if (is_subscription_cancelled($site_db) || is_subscription_failed($site_db) || is_in_trial($site_db))
			{
				$data['cloud_customer_info'] = get_cloud_customer_info($site_db);
				
				if (is_in_trial($site_db))
				{
						$data['trial_on']  = TRUE;
				}
				elseif (is_subscription_failed($site_db))
				{
					$data['subscription_payment_failed']  = TRUE;
				}
				elseif (is_subscription_cancelled_within_grace_period($site_db))
				{
					$data['subscription_cancelled_within_5_days']  = TRUE;
				}
			}
		}
		
		$this->load->view("home",$data);
	}

	function dismiss_mercury_message()
	{
		$this->Appconfig->mark_mercury_activate(true);
	}

	function logout()
	{
		$this->Employee->logout();
	}
	
	function set_employee_current_location_id()
	{
		$this->Employee->set_employee_current_location_id($this->input->post('employee_current_location_id'));
		
		//Clear out logged in register when we switch locations
		$this->Employee->set_employee_current_register_id(null);
	}

	function get_employee_current_location_id()
	{
		
		$current_location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());

		echo $current_location->current_announcement;

	}
	
	function keep_alive()
	{
		//Set keep alive session to prevent logging out
		$this->session->set_userdata("keep_alive",time());
		echo $this->session->userdata('keep_alive');
	}
	
	function set_fullscreen($on = 0)
	{
		$this->session->set_userdata("fullscreen",$on);		
	}
	
	function set_fullscreen_customer_display($on = 0)
	{
		$this->session->set_userdata("fullscreen_customer_display",$on);				
	}
	
	function view_item_modal($item_id)
	{
		$this->lang->load('items');
		$this->lang->load('receivings');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Manufacturer');
		$this->load->model('Tag');
		$this->load->model('Item_location');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_location_taxes');
		$this->load->model('Receiving');
		$this->load->model('Item_taxes');
		$this->load->model('Additional_item_numbers');
		
		$data['item_info']=$this->Item->get_info($item_id);
		
		$data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);
		
		
		$data['tier_prices'] = array();
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tier_id = $tier->id;
			$tier_price = $this->Item->get_tier_price_row($tier_id,$item_id);
			
			if ($tier_price)
			{
				$value = $tier_price->unit_price !== NULL ? to_currency($tier_price->unit_price) : $tier_price->percent_off.'%';			
				$data['tier_prices'][] = array('name' => $tier->name, 'value' => $value);
			}
		}
		
		$data['category'] = $this->Category->get_full_path($data['item_info']->category_id);
		$data['manufacturer'] = $this->Manufacturer->get_info($data['item_info']->manufacturer_id)->name;
		$data['item_location_info']=$this->Item_location->get_info($item_id);
		$data['item_tax_info']=$this->Item_taxes_finder->get_info($item_id);
		$data['reorder_level'] = ($data['item_location_info'] && $data['item_location_info']->reorder_level) ? $data['item_location_info']->reorder_level : $data['item_info']->reorder_level;
		
		if ($supplier_id = $this->Item->get_info($item_id)->supplier_id)
		{
			$supplier = $this->Supplier->get_info($supplier_id);
			$data['supplier'] = $supplier->company_name . ' ('.$supplier->first_name.' '.$supplier->last_name.')';
		}
		
		$data['suspended_receivings'] = $this->Receiving->get_suspended_receivings_for_item($item_id);		
		$this->load->view("items/items_modal",$data);
	}
	
	// Function to show the modal window when clicked on kit name
	function view_item_kit_modal($item_kit_id)
	{
		$this->lang->load('item_kits');
		$this->lang->load('items');
		$this->lang->load('receivings');
		$this->load->model('Item');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Manufacturer');
		$this->load->model('Tag');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Receiving');
		$this->load->model('Item_kit_taxes');
		
		// Fetching Kit information using kit_id
		$data['item_kit_info']=$this->Item_kit->get_info($item_kit_id);
		
		$tier_prices = $this->Item->get_all_tiers_prices();
		
		$data['tier_prices'] = array();
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tier_id = $tier->id;
			$tier_price = $this->Item_kit->get_tier_price_row($tier_id,$item_kit_id);
			
			if ($tier_price)
			{
				$value = $tier_price->unit_price !== NULL ? to_currency($tier_price->unit_price) : $tier_price->percent_off.'%';			
				$data['tier_prices'][] = array('name' => $tier->name, 'value' => $value);
			}
		}
		
		$data['manufacturer'] = $this->Manufacturer->get_info($data['item_kit_info']->manufacturer_id)->name;
		$data['category'] = $this->Category->get_full_path($data['item_kit_info']->category_id);
		
		$this->load->view("item_kits/items_modal",$data);
	}

	function sales_widget($type = 'monthly')
	{
		$day = array();
		$count = array();

		if($type == 'monthly')
		{
			$start_date = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y"))).' 00:00:00';
			$end_date = date('Y-m-d').' 23:59:59';
		}
		else
		{
			$current_week = strtotime("-0 week +1 day");
			$current_start_week = strtotime("last monday midnight",$current_week);
			$current_end_week = strtotime("next sunday",$current_start_week);

			$start_date = date("Y-m-d",$current_start_week).' 00:00:00';
			$end_date = date("Y-m-d",$current_end_week).' 23:59:59';
		}

		$return = $this->Sale->get_sales_amount_for_range($start_date, $end_date);	

		foreach ($return as $key => $value) {
			if($type == 'monthly')
			{
				$day[] = date('d',strtotime($value['sale_date']));	
			}
			else
			{
				$day[] = lang('common_'.strtolower(date('l',strtotime($value['sale_date']))));
			}
			$amount[] = $value['sale_amount'];
		}	

		
		if(empty($return))
		{
			$day = array(0);
			$amount = array(0);
			$data['message'] = lang('common_not_found');
		}
		$data['day'] = json_encode($day);
		$data['amount'] = json_encode($amount);
		
		if($this->input->is_ajax_request())
		{
			if(empty($return))
			{
				echo json_encode(array('message'=>lang('common_not_found')));
				die();
			}
		    echo json_encode(array('day'=>$day,'amount'=>$amount));
		    die();
		}
		return $data;
	}
	
	function enable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','1');
		}
		redirect('home');
	}
	
	function disable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','0');
		}
		redirect('home');	
	}
	
	function dismiss_test_mode()
	{
		$this->Appconfig->save('hide_test_mode_home','1');		
	}
	
	function edit_profile()
	{
		if (!$this->Employee->has_module_action_permission('employees', 'edit_profile', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/home');
		}
		
		$data = array();
		$employee_person_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$data['person_info']=$this->Employee->get_info($employee_person_id);
		$data['controller_name']=strtolower(get_class());
		
		$this->load->view('edit_profile', $data);
		
	}
	
	function do_edit_profile()
	{
		if (!$this->Employee->has_module_action_permission('employees', 'edit_profile', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/home');
		}
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		
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
		//Password has been changed OR first time password set
		if($this->input->post('password')!='')
		{
			$employee_data=array(
			'username'=>$this->input->post('username'),
			'password'=>md5($this->input->post('password'))
			);
		}
		else //Password not changed
		{
			$employee_data=array('username'=>$this->input->post('username'));
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
		elseif($this->Employee->save_profile($person_data,$employee_data, $employee_id))
		{
			$success_message = '';
			
			//New employee
			if($employee_id==-1)
			{
				$success_message = lang('common_employees_successful_adding').' '.$person_data['first_name'].' '.$person_data['last_name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_data['person_id']));
			}
			else //previous employee
			{
				$success_message = lang('common_employees_successful_updating').' '.$person_data['first_name'].' '.$person_data['last_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'person_id'=>$employee_id));
			}
			
			$employee_info = $this->Employee->get_info($employee_id);
			
			//Delete Image
			if($this->input->post('del_image') && $employee_id != -1)
			{
			    if($employee_info->image_id != null)
			    {
			 		$this->load->model('Appfile');
					$this->Person->update_image(NULL,$employee_id);
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
	
	function get_ecommerce_sync_progress()
	{	
		if ($this->config->item("ecommerce_platform"))
		{
			require_once (APPPATH."models/interfaces/Ecom.php");
			$ecom_model = Ecom::get_ecom_model();
			
			$progress = $ecom_model->get_sync_progress();
			echo json_encode(array('running' => $this->Appconfig->get_raw_ecommerce_cron_running() ? $this->Appconfig->get_raw_ecommerce_cron_running() : FALSE,'percent_complete' => $progress['percent_complete'],'message' => $progress['message']));
		}
		else
		{
			echo json_encode(array('running' => FALSE,'progress' =>0,'message' => ''));
		}

	}
}
?>