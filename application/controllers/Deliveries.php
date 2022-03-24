<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");

class Deliveries extends Secure_area implements Idata_controller
{
	function __construct()
	{
		parent::__construct('deliveries');	
		$this->lang->load('deliveries');
		$this->load->model('Delivery');
		$this->load->model('Shipping_provider');
		$this->load->model('Shipping_method');
		
		$this->load->model('Person');
		$this->lang->load('deliveries');
		$this->load->helper('order');
		
		$this->lang->load('module');	
		
	}

	function index($offset=0)
	{
		$this->check_action_permission('search');
		$this->load->model('Delivery');
		$this->lang->load('deliveries');
		
		$params = $this->session->userdata('deliveries_orders_search_data') ? $this->session->userdata('deliveries_orders_search_data') : array('offset' => 0, 'order_col' => 'estimated_shipping_date', 'order_dir' => 'asc', 'search' => FALSE);
		
		if ($offset != $params['offset'])
		{
		   redirect('deliveries/index/'.$params['offset']);
		}
		
		$config['base_url'] = site_url('deliveries/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['filters'] = $this->session->userdata('orders_fitlers');
		
		$data['default_start_date'] = '';
		$data['default_end_date'] = '';
		
		if ($data['search'])
		{
			$config['total_rows'] = $this->Delivery->search_count_all($data['search'],$data['filters']);
			$table_data = $this->Delivery->search($data['search'],$data['filters'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{	
			$config['total_rows'] = $this->Delivery->count_all();
			$table_data = $this->Delivery->get_all($data['per_page'], $params['offset'],$params['order_col'],$params['order_dir'],$data['filters']);
		}
				
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['manage_table']= $dataset = get_orders_manage_table($table_data,$this);
		
		$data['default_columns'] = $this->Delivery->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_sale_order_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'],$this->Delivery->get_displayable_columns());
		//TODO: fix
		$data['date_fields'] = array(
			'estimated_delivery_or_pickup_date' => lang('deliveries_estimated_delivery_or_pickup_date'),
			'actual_shipping_date' => lang('deliveries_actual_shipping_date'),
			'actual_delivery_or_pickup_date' => lang('deliveries_actual_delivery_or_pickup_date'),
			'sale_time' => lang('common_sale_date'),
		);
				
		$this->load->view('deliveries/manage', $data);
	}
	
	function clear_state()
	{
		$this->session->unset_userdata('deliveries_orders_search_data');
		redirect("deliveries/");
	}
	
	
	function search()
	{
		$this->load->model('Delivery');
		$this->check_action_permission('search');
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'estimated_shipping_date';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';
		
		$deliveries_orders_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		$this->session->set_userdata("deliveries_orders_search_data",$deliveries_orders_search_data);
		$data['filters'] = $this->session->userdata('orders_fitlers');
		
		if ($search)
		{
			$config['total_rows'] = $this->Delivery->search_count_all($search,$data['filters']);
			$table_data = $this->Delivery->search($search,$data['filters'],$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc');
		}
		else
		{
			$config['total_rows'] = $this->Delivery->count_all();
			$table_data = $this->Delivery->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'last_name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc',$data['filters']);
		}
		
		$config['base_url'] = site_url('deliveries/sorting');
		
		$config['per_page'] = $per_page;
		
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_orders_manage_table_data_rows($table_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	function sorting()
	{
		$this->load->model('Delivery');
		$this->lang->load('deliveries');
		
		$this->check_action_permission('search');
		$params = $this->session->userdata('deliveries_orders_search_data') ? $this->session->userdata('deliveries_orders_search_data') : array('order_col' => 'estimated_shipping_date', 'order_dir' => 'asc');
		$search = $this->input->post('search') ? $this->input->post('search') : "";
		$category_id = $this->input->post('category_id');
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
		
		$per_page = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		
		$item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search);
		
		$this->session->set_userdata("deliveries_orders_search_data",$item_search_data);
		$data['filters'] = $this->session->userdata('orders_fitlers');
		
		if ($search)
		{
			$config['total_rows'] = $this->Delivery->search_count_all($search,$data['filters']);
			$table_data = $this->Delivery->search($search, $data['filters'],$per_page, $this->input->post('offset') ? $this->input->post('offset') : 0, $order_col, $order_dir);
		}
		else
		{
			$config['total_rows'] = $this->Delivery->count_all();
			$table_data = $this->Delivery->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col,$order_dir,$data['filters']);
		}
		
		$config['base_url'] = site_url('deliveries/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Delivery->get_default_columns();
		$data['manage_table'] = get_orders_manage_table_data_rows($table_data, $this);
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'], 'total_rows' => $config['total_rows']));
	}	

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$this->load->model('Delivery');
		$this->check_action_permission('search');
		
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Delivery->get_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	/*
	Loads the price rule edit form
	*/
	function view($delivery_id=-1,$redirect_code=0)
	{
		$this->load->model('Module_action');
		$this->check_action_permission('edit');
		
		$data = $this->_get_delivery_data($delivery_id);
		$data['redirect']= $redirect_code;
		$data['rule_id'] = $delivery_id;

		$data['redirect_code']=$redirect_code;
		
		$this->load->view('deliveries/form', $data);
	}
	
	private function _get_delivery_data($delivery_id)
	{
		$data = array();
		$data['delivery_info'] = $this->Delivery->get_info($delivery_id)->row_array();
		
		$shipping_address_person_id = $data['delivery_info']['shipping_address_person_id'];
		if($shipping_address_person_id)
		{
			$data['delivery_person_info'] = (array)$this->Person->get_info($shipping_address_person_id);
		}
		
		$delivery_providers = $this->Shipping_provider->get_all()->result_array();
		$delivery_methods = $this->Shipping_method->get_all()->result_array();
		
		$providers_with_methods = array();
		
		foreach($delivery_providers as $provider)
		{
			$providers_with_methods[$provider['id']] = $provider;
		}
		
		foreach($delivery_methods as $method)
		{
			$providers_with_methods[$method['shipping_provider_id']]['methods'][] = $method;			
		}
		
		$data['providers_with_methods'] = $providers_with_methods;
		
		return $data;
	}
	
	function save($delivery=-1)
	{
		$this->check_action_permission('edit');
		
		$delivery_person_data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'address_1' => $this->input->post('address_1'),
			'address_2' => $this->input->post('address_2'),
			'city' => $this->input->post('city'),
			'state' => $this->input->post('state'),
			'zip' => $this->input->post('zip'),
			'country' => $this->input->post('country'),
		);
		
		
		$delivery_data = array(
			'comment' => $this->input->post('comment'),
			'tracking_number' => $this->input->post('tracking_number'),
			'status' => $this->input->post('status'),
			'estimated_shipping_date' => $this->input->post('estimated_shipping_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('estimated_shipping_date'))) : NULL,
			'actual_shipping_date' => $this->input->post('actual_shipping_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('actual_shipping_date'))) : NULL,
			'estimated_delivery_or_pickup_date' => $this->input->post('estimated_delivery_or_pickup_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('estimated_delivery_or_pickup_date'))) : NULL,
			'actual_delivery_or_pickup_date' => $this->input->post('actual_delivery_or_pickup_date') ? date('Y-m-d H:i:s', strtotime($this->input->post('actual_delivery_or_pickup_date'))) : NULL,
		);
		
		if($this->Delivery->save($delivery_data, $delivery))
		{
			
			$shipping_address_person_id = $this->Delivery->get_info($delivery)->row()->shipping_address_person_id;
			
			$this->Person->save($delivery_person_data,$shipping_address_person_id);
			$success=lang('deliveries_success');
			$this->session->set_flashdata('success', $success);
			redirect('deliveries');
		}
		else
		{
			$error=lang('deliveries_error');
			$this->session->set_flashdata('error', $error);
			redirect('deliveries');
		}
	}
	
	function delete()
	{
		$this->check_action_permission('delete');
		$deliveries_to_delete=$this->input->post('ids');
		
		if($this->Delivery->delete_list($deliveries_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('deliveries_successful_deleted').' '.
			count($deliveries_to_delete).' '.lang('deliveries_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('deliveries_cannot_be_deleted')));
		}
	}
	
	function save_filters()
	{
		$this->session->set_userdata("orders_fitlers",$this->input->post());
		echo json_encode(array('success' => TRUE));
	}
	
	function save_column_prefs()
	{
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save('sale_orders_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('sale_orders_column_prefs');			
		}
	}
	
	function reload_delivery_table()
	{
		$this->load->model('Delivery');
		$config['base_url'] = site_url('deliveries/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$params = $this->session->userdata('deliveries_orders_search_data') ? $this->session->userdata('deliveries_orders_search_data') : array('offset' => 0, 'order_col' => 'estimated_shipping_date', 'order_dir' => 'asc', 'search' => FALSE);

		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";		
		$data['filters'] = $this->session->userdata('orders_fitlers');
		
		if ($data['search'])
		{
			$config['total_rows'] = $this->Delivery->search_count_all($data['search'],$data['filters']);
			$table_data = $this->Delivery->search($data['search'],$data['filters'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		else
		{
			$config['total_rows'] = $this->Delivery->count_all();
			$table_data = $this->Delivery->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],$data['filters']);
		}
		
		echo get_orders_manage_table($table_data,$this);
	}
	
	function calendar($date_field, $year = '', $month='',$week='',$day='')
	{
		$this->load->helper('date_helper');
		
		$controller_name = strtolower(get_class());
		
		if (!$year)
		{
			$year = date('Y');
		}
		
		if (!$month)
		{
			$month = date('m');
		}
		
		
		$url_day = $day ? $day : date('d');
				
		$url_week = getWeeks(date("Y-m-d", strtotime("$year-$month-$url_day")), "sunday");
				
		$date_fields = array(
			'estimated_delivery_or_pickup_date' => lang('deliveries_estimated_delivery_or_pickup_date'),
			'actual_shipping_date' => lang('deliveries_actual_shipping_date'),
			'actual_delivery_or_pickup_date' => lang('deliveries_actual_delivery_or_pickup_date'),
			'sale_time' => lang('common_sale_date'),
		);
		
		if(!isset($date_fields[$date_field]))
		{
			$date_field = 'estimated_delivery_or_pickup_date';
		}
		
		$day_url = site_url("deliveries/calendar/$date_field/$year/$month/-1/{day}");
				$prefs = array(
					'show_next_prev'  => TRUE,
					'next_prev_url'   => site_url("deliveries/calendar/$date_field"),
					'template'				=> 
		'
		        {table_open}<table class="calendar" border="1" cellpadding="0" cellspacing="0" width="100%" style="text-align:center;margin: 0 auto;">{/table_open}

		        {heading_row_start}<tr>{/heading_row_start}

		        {heading_previous_cell}<th class="heading_previous_cell" style="text-align:center;"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		        {heading_title_cell}<th class="heading_title_cell" colspan="{colspan}" style="text-align:center;">{heading}</th>{/heading_title_cell}
		        {heading_next_cell}<th class="heading_next_cell" style="text-align:center;"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

		        {heading_row_end}</tr>{/heading_row_end}

		        {week_row_start}<tr>{/week_row_start}
		        {week_day_cell}<td class="week_day_cell">{week_day}</td>{/week_day_cell}
		        {week_row_end}</tr>{/week_row_end}

		        {cal_row_start}<tr>{/cal_row_start}
		        {cal_cell_start}<td class="cal_cell_start" style="height:140px; vertical-align: top;">{/cal_cell_start}
		        {cal_cell_start_today}<td class="cal_cell_start_today" style=" height:140px; vertical-align: top;background-color:#ddd;">{/cal_cell_start_today}
		        {cal_cell_start_other}<td class="cal_cell_start_other" style="height:140px; vertical-align: top;" class="other-month">{/cal_cell_start_other}

		        {cal_cell_content}<a class="cal_cell_content" href="'.$day_url.'">{day}</a><br />{content}</a>{/cal_cell_content}
		        {cal_cell_content_today}<div class="cal_cell_content_today highlight"><a href="'.$day_url.'">{day}</a><br />{content}</div>{/cal_cell_content_today}

		        {cal_cell_no_content}<a "cal_cell_no_content" href="'.$day_url.'">{day}</a>{/cal_cell_no_content}
		        {cal_cell_no_content_today}<div class="cal_cell_no_content_today highlight"><a href="'.$day_url.'">{day}</a></div>{/cal_cell_no_content_today}

		        {cal_cell_blank}&nbsp;{/cal_cell_blank}

		        {cal_cell_other}<a class="cal_cell_other" href="'.$day_url.'">{day}</a>{/cal_cel_other}

		        {cal_cell_end}</td>{/cal_cell_end}
		        {cal_cell_end_today}</td>{/cal_cell_end_today}
		        {cal_cell_end_other}</td>{/cal_cell_end_other}
		        {cal_row_end}</tr>{/cal_row_end}

		        {table_close}</table>{/table_close}
						'
 					);
				
			 	$calendar_data = array();
				$calender_data_days = array();
				
				//If we are doing monthy calendar fall back to parent place
				if (!$week && !$day)
				{
					$start_date = date("$year-$month-01");
					$end_date = date("$year-$month-t");
				}
		
				//Weekly Calendar
				if ($week && !$day)
				{
					//pull in all events for month; frontend will only show that week
					$start_date = date("$year-$month-01");
					$end_date = date("$year-$month-t");
				}
				
				$selected_date = '';
				//Daily Calendar
				if ($day)
				{
					$selected_date = date(get_date_format(), strtotime("$year-$month-$day"));
					$start_date = date("$year-$month-$day");
					$end_date = date("$year-$month-$day 23:59:59");
				}
										
				foreach($this->Delivery->get_all_for_range($start_date,$end_date,$date_field)->result() as $row)
				{
					$cur_day = date('j',strtotime($row->{$date_field}));
					$time = date(get_time_format(),strtotime($row->{$date_field}));
					$calendar_data_days[$cur_day][] = array('delivery_id' => $row->delivery_id,'name' =>$row->first_name.' '.$row->last_name, 'time' => $time, 'status' => $row->status);
				}
		
				if (!empty($calendar_data_days))
				{
					foreach($calendar_data_days as $cur_day => $data)
					{
						$entry = '';
						
						
						foreach($data as $data_point)
						{
							if($day)
							{
								$url = site_url('deliveries/view/'.$data_point['delivery_id']);
								
								$entry .= '<a href="'.$url.'" class="list-group-item">';
								$entry .= '<h4 class="list-group-item-heading">'.$data_point['time'].'</h4>';
								$entry .= '<p class="list-group-item-text">'.$data_point['name'].'</p>';
								$entry .= '</a>';
							} 
							else 
							{
								
								$entry.= anchor('deliveries/view/'.$data_point['delivery_id'],$data_point['name'].' '.$data_point['time']).'<br />';
								
							}
						}	
			
						$calendar_data[$cur_day] = $entry;
					}
				}
				$this->load->library('calendar',$prefs);
				
				$daily_url = site_url("deliveries/calendar/$date_field/$year/$month/-1/$day");
				$weekly_url = site_url("deliveries/calendar/$date_field/");
				$monthly_url = '';
				
				$this->load->view('deliveries/calendar',array('monthly_url' =>site_url("deliveries/calendar/$date_field/$year/$month"), 'weekly_url' =>site_url("deliveries/calendar/$date_field/$year/$month/$url_week"), 'daily_url' => site_url("deliveries/calendar/$date_field/$year/$month/-1/$url_day"),'controller_name' => $controller_name, 'date_field' => $date_field,'month' => $month,'year'=>$year,'week' => $week,'day' => $day,'date_fields' => $date_fields,'calendar' => $this->calendar->generate($year,$month,$week,$day,$calendar_data), 'selected_date' => $selected_date));
				
		 }
}
?>
