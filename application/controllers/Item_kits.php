<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");
class Item_kits extends Secure_area implements Idata_controller
{
	function __construct()
	{
		parent::__construct('item_kits');
		$this->lang->load('item_kits');
		$this->lang->load('module');
		$this->load->model('Item_kit');
		$this->load->model('Category');
		$this->load->model('Tag');
		
		
	}

	function index($offset=0)
	{
		$params = $this->session->userdata('item_kit_search_data') ? $this->session->userdata('item_kit_search_data') : array('offset' => 0, 'order_col' => 'item_kit_id', 'order_dir' => 'desc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		if ($offset!=$params['offset'])
		{
		   redirect('item_kits/index/'.$params['offset']);
		}
		$this->check_action_permission('search');
		$config['base_url'] = site_url('item_kits/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		$data['categories'][''] = lang('common_all');
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
		
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item_kit->search_count_all($data['search'],$data['category_id']);
			$table_data = $this->Item_kit->search($data['search'],$data['category_id'], $data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'],$data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item_kit->count_all();
			$table_data = $this->Item_kit->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];
		$data['manage_table']=get_item_kits_manage_table($table_data,$this);
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Item_kit->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_item_kit_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'], $this->Item_kit->get_displayable_columns());		
		
		$this->load->view('item_kits/manage',$data);
	}
	
	function sorting()
	{
		$this->check_action_permission('search');
		$params = $this->session->userdata('item_kit_search_data') ? $this->session->userdata('item_kit_search_data') : array('offset' => 0, 'order_col' => 'item_kit_id', 'order_dir' => 'desc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$category_id = $this->input->post('category_id');
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
		
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		
		$item_kit_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'category_id' => $category_id, 'fields' => $fields);
		
		
		$this->session->set_userdata("item_kit_search_data",$item_kit_search_data);
		if ($search)
		{
			$config['total_rows'] = $this->Item_kit->search_count_all($search,$category_id);
			$table_data = $this->Item_kit->search($search,$category_id, $per_page,$this->input->post('offset') ? $this->input->post('offset') : 0,$order_col, $order_dir, $fields);
		}
		else
		{
			$config['total_rows'] = $this->Item_kit->count_all();
			$table_data = $this->Item_kit->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0,$order_col, $order_dir);
		}
		$config['base_url'] = site_url('item_kits/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_Item_kits_manage_table_data_rows($table_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));	
	}
	

	/* added for excel expert */
	function excel_export() {
		
		set_time_limit(0);
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Item_kit_location');
		$this->load->model('Manufacturer');
		
		$params = $this->session->userdata('item_kit_search_data') ? $this->session->userdata('item_kit_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		
		$search = $params['search'] ? $params['search'] : "";
		$category_id = $params['category_id'] ? $params['category_id'] : "";
		
		//Filter based on search
		if ($search || $category_id)
		{
			$data = $this->Item_kit->search($search,$category_id,$this->Item_kit->search_count_all($search, $category_id,10000, $params['fields']),0,$params['order_col'],$params['order_dir'], $params['fields'])->result_object();
		}
		else
		{
			$data = $this->Item_kit->get_all()->result_object();
		}
		
		
		$this->load->helper('report');
		$rows = array();
		$row = array(lang('common_item_number'),lang('common_product_id'), lang('item_kits_name'),lang('common_category'),lang('common_manufacturer'),lang('common_cost_price'),lang('common_unit_price'),lang('item_kits_tax_1_name'),lang('item_kits_tax_1_percent'),lang('item_kits_tax_2_name'),lang('item_kits_tax_2_percent'),lang('item_kits_tax_2_cummulative'),lang('item_kits_description'));
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$row[] = lang('common_disable_loyalty');
		}
		
		$rows[] = $row;
		
		$categories = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id();
		$manufacturers = array();
		
	 foreach($this->Manufacturer->get_all() as $id => $row)
	 {
	 	 	$manufacturers[$id] = $row['name'];
	 
	 }
		foreach ($data as $r) {
			$taxdata = $this->Item_kit_taxes_finder->get_info($r->item_kit_id);
			if (sizeof($taxdata) >= 2) {
				$r->taxn = $taxdata[0]['name'];
				$r->taxp = $taxdata[0]['percent'];
				$r->taxn1 = $taxdata[1]['name'];
				$r->taxp1 = $taxdata[1]['percent'];
				$r->cumulative = $taxdata[1]['cumulative'] ? 'y' : '';
			} else if (sizeof($taxdata) == 1) {
				$r->taxn = $taxdata[0]['name'];
				$r->taxp = $taxdata[0]['percent'];
				$r->taxn1 = '';
				$r->taxp1 = '';
				$r->cumulative = '';
			} else {
				$r->taxn = '';
				$r->taxp = '';
				$r->taxn1 = '';
				$r->taxp1 = '';
				$r->cumulative = '';
			}
			
			$row = array(
				$r->item_kit_number,
				$r->product_id,
				$r->name,
				isset($categories[$r->category_id]) ? $categories[$r->category_id] : '',
				isset($manufacturers[$r->manufacturer_id]) ? $manufacturers[$r->manufacturer_id] : '',
				to_currency_no_money($r->cost_price),
				to_currency_no_money($r->unit_price),
				$r->taxn,
				$r->taxp,
				$r->taxn1,
				$r->taxp1,
				$r->cumulative,
				$r->description
			);
			
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
			{
				$row[] = $r->disable_loyalty ? 'y' : '';				
			}
			
			$rows[] = $row;		
		}
		
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($rows,'itemkits_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
		exit;
	}
	
	function item_search()
	{
		$this->load->model('Item');
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),'unit_price',100);
		echo json_encode($suggestions);
	}

	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';
		$category_id = $this->input->post('category_id');
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';

		$item_kit_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,'category_id' => $category_id, 'fields' => $fields);
		
		$this->session->set_userdata("item_kit_search_data",$item_kit_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Item_kit->search($search,$category_id,$per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc',$fields);
		$config['base_url'] = site_url('item_kits/search');
		$config['total_rows'] = $this->Item_kit->search_count_all($search,$category_id);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$data['manage_table']=get_Item_kits_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item_kit->get_manage_item_kits_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}
	
	function check_duplicate()
	{
		echo json_encode(array('duplicate'=>$this->Item_kit->check_duplicate($this->input->post('term'))));

	}
	
	function _get_item_kit_data($item_kit_id)
	{
		$this->load->model('Tax_class');
		
		$data = array();
		$data['tax_classes'] = array();
		$data['tax_classes'][''] = lang('common_none');
		
		foreach($this->Tax_class->get_all()->result_array() as $tax_class)
		{
			$data['tax_classes'][$tax_class['id']] = $tax_class['name'];
		}
		
		$data['controller_name']=strtolower(get_class());
		$data['item_kit_info']=$this->Item_kit->get_info($item_kit_id);
		$data['tags'] = implode(',',$this->Tag->get_tags_for_item_kit($item_kit_id));
		
		$data['categories'][''] = lang('common_select_category');
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$this->load->model('Manufacturer');
		$manufacturers = array('-1' => lang('common_none'));
		
		foreach($this->Manufacturer->get_all() as $id => $row)
		{
			$manufacturers[$id] = $row['name'];
		}
		
		$data['manufacturers'] = $manufacturers;
		$data['selected_manufacturer'] = $this->Item_kit->get_info($item_kit_id)->manufacturer_id;
		
		
		$data['item_kit_tax_info']=$this->Item_kit_taxes->get_info($item_kit_id);
		$data['tiers']=$this->Tier->get_all()->result();		
		
		$data['tier_prices'] = array();
		$data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'), 'cost_plus_percent' => lang('common_cost_plus_percent'),'cost_plus_fixed_amount' => lang('common_cost_plus_fixed_amount'));
		
		
		foreach($this->Location->get_all()->result() as $location)
		{
			if($this->Employee->is_location_authenticated($location->location_id))
			{				
				$data['locations'][] = $location;
				$data['location_item_kits'][$location->location_id] = $this->Item_kit_location->get_info($item_kit_id,$location->location_id);
				$data['location_taxes'][$location->location_id] = $this->Item_kit_location_taxes->get_info($item_kit_id, $location->location_id);
								
				foreach($data['tiers'] as $tier)
				{					
					$tier_prices = $this->Item_kit_location->get_tier_price_row($tier->id,$data['item_kit_info']->item_kit_id, $location->location_id);
					if (!empty($tier_prices))
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = $tier_prices;
					}
					else
					{
						$data['location_tier_prices'][$location->location_id][$tier->id] = FALSE;			
					}
				}
			}
			
		}
		
		foreach($data['tiers'] as $tier)
		{
			$tier_prices = $this->Item_kit->get_tier_price_row($tier->id,$data['item_kit_info']->item_kit_id);
			
			if (!empty($tier_prices))
			{
				$data['tier_prices'][$tier->id] = $tier_prices;
			}
			else
			{
				$data['tier_prices'][$tier->id] = FALSE;			
			}
		}
		$decimals = $this->Appconfig->get_raw_number_of_decimals();
		$decimals = $decimals !== NULL && $decimals!= '' ? $decimals : 2;
		$data['decimals'] = $decimals;
		
		return $data;
	}
	
	function view($item_kit_id=-1,$redirect=0)
	{
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Tier');
		$this->load->model('Item');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_kit_taxes_finder');
		
		$this->check_action_permission('add_update');	
		$data = $this->_get_item_kit_data($item_kit_id);
		$data['redirect']=$redirect;
		
		$this->load->view("item_kits/form",$data);
	}
	
	function clone_item_kit($item_kit_id)
	{
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Tier');
		$this->load->model('Item');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_kit_taxes_finder');
		
		$this->check_action_permission('add_update');
		$data = $this->_get_item_kit_data($item_kit_id);
		$data['item_kit_info']->item_kit_number = '';
		$data['item_kit_info']->product_id = '';		
		$data['redirect']=2;
		$data['is_clone'] = TRUE;
		$this->load->view("item_kits/form",$data);
	}
		
	function save($item_kit_id=-1)
	{
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		
		$this->check_action_permission('add_update');
		
		if (!$this->Category->exists($this->input->post('category_id')))
		{
			if (!$category_id = $this->Category->get_category_id($this->input->post('category_id')))
			{
				$category_id = $this->Category->save($this->input->post('category_id'));
			}
		}	
		else
		{
			$category_id = $this->input->post('category_id');
		}
			
		$item_kit_data = array(
		'item_kit_number'=>$this->input->post('item_kit_number')=='' ? null:$this->input->post('item_kit_number'),
		'product_id'=>$this->input->post('product_id')=='' ? null:$this->input->post('product_id'),
		'name'=>$this->input->post('name'),
		'category_id'=>$category_id,
		'manufacturer_id'=>$this->input->post('manufacturer_id')== -1 || $this->input->post('manufacturer_id') == '' ? null:$this->input->post('manufacturer_id'),
		'tax_included'=>$this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
		'unit_price'=>$this->input->post('unit_price')=='' ? null:$this->input->post('unit_price'),
		'cost_price'=>$this->input->post('cost_price')=='' ? null:$this->input->post('cost_price'),
		'min_edit_price'=>$this->input->post('min_edit_price') !== '' ? $this->input->post('min_edit_price') : NULL,
		'max_edit_price'=>$this->input->post('max_edit_price') !== '' ? $this->input->post('max_edit_price') : NULL,
		'max_discount_percent'=>$this->input->post('max_discount_percent') !== '' ? $this->input->post('max_discount_percent') : NULL,
		'change_cost_price' => $this->input->post('change_cost_price') ? $this->input->post('change_cost_price') : 0,
		'description'=>$this->input->post('description'),
		'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
		'is_ebt_item'=> $this->input->post('is_ebt_item') ? $this->input->post('is_ebt_item') : 0,
		'tax_class_id'=> $this->input->post('tax_class') ? $this->input->post('tax_class') : NULL,
		);
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$item_kit_data['disable_loyalty'] = $this->input->post('disable_loyalty') ? $this->input->post('disable_loyalty') : 0;
		}
		
		
		if ($this->input->post('override_default_commission'))
		{
			if ($this->input->post('commission_type') == 'fixed')
			{
				$item_kit_data['commission_fixed'] = (float)$this->input->post('commission_value');
				$item_kit_data['commission_percent_type'] = '';
				$item_kit_data['commission_percent'] = NULL;
			}
			else
			{
				$item_kit_data['commission_percent'] = (float)$this->input->post('commission_value');
				$item_kit_data['commission_percent_type'] = $this->input->post('commission_percent_type');
				$item_kit_data['commission_fixed'] = NULL;
			}
		}
		else
		{
			$item_kit_data['commission_percent'] = NULL;
			$item_kit_data['commission_fixed'] = NULL;
			$item_kit_data['commission_percent_type'] = '';
		}
		
		$redirect=$this->input->post('redirect');
		
		if($this->Item_kit->save($item_kit_data,$item_kit_id))
		{
			
			$this->Tag->save_tags_for_item_kit(isset($item_kit_data['item_kit_id']) ? $item_kit_data['item_kit_id'] : $item_kit_id, $this->input->post('tags'));
			
			$tier_type = $this->input->post('tier_type');
			
			if ($this->input->post('item_kit_tier'))
			{
				foreach($this->input->post('item_kit_tier') as $tier_id => $price_or_percent)
				{
					if ($price_or_percent)
					{				
						$tier_data=array('tier_id'=>$tier_id);
						$tier_data['item_kit_id'] = isset($item_kit_data['item_kit_id']) ? $item_kit_data['item_kit_id'] : $item_kit_id;

						if ($tier_type[$tier_id] == 'unit_price')
						{
							$tier_data['unit_price'] = $price_or_percent;
							$tier_data['percent_off'] = NULL;
							$tier_data['cost_plus_percent'] = NULL;
							$tier_data['cost_plus_fixed_amount'] = NULL;
						}
						elseif($tier_type[$tier_id] == 'percent_off')
						{
							$tier_data['percent_off'] = (float)$price_or_percent;
							$tier_data['unit_price'] = NULL;
							$tier_data['cost_plus_percent'] = NULL;
							$tier_data['cost_plus_fixed_amount'] = NULL;
						}
						elseif($tier_type[$tier_id] == 'cost_plus_percent')
						{
							$tier_data['percent_off'] = NULL;
							$tier_data['unit_price'] = NULL;
							$tier_data['cost_plus_percent'] = (float)$price_or_percent;
							$tier_data['cost_plus_fixed_amount'] = NULL;
						}
						elseif($tier_type[$tier_id] == 'cost_plus_fixed_amount')
						{
							$tier_data['percent_off'] = NULL;
							$tier_data['unit_price'] = NULL;
							$tier_data['cost_plus_percent'] = NULL;
							$tier_data['cost_plus_fixed_amount'] = (float)$price_or_percent;
						}
						
					
						$this->Item_kit->save_item_tiers($tier_data,$item_kit_id);
					}
					else
					{
						$this->Item_kit->delete_tier_price($tier_id, $item_kit_id);
					}
				}
			}

			$success_message = '';
			//New item kit
			if($item_kit_id==-1)
			{
				$success_message = lang('item_kits_successful_adding').' '.$item_kit_data['name'];
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_kit_id'=>$item_kit_data['item_kit_id'],'redirect'=>$redirect));
				$item_kit_id = $item_kit_data['item_kit_id'];
			}
			else //previous item
			{
				$success_message = lang('item_kits_successful_updating').' '.$item_kit_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_kit_id'=>$item_kit_id,'redirect'=>$redirect));
			}
			
			
			if ($this->input->post('locations'))
			{
				foreach($this->input->post('locations') as $location_id => $item_kit_location_data)
				{		        
					$override_prices = isset($item_kit_location_data['override_prices']) && $item_kit_location_data['override_prices'];
				
					$data = array(
						'location_id' => $location_id,
						'item_kit_id' => $item_kit_id,
						'cost_price' => $override_prices && $item_kit_location_data['cost_price'] != '' ? $item_kit_location_data['cost_price'] : NULL,
						'unit_price' => $override_prices && $item_kit_location_data['unit_price'] != '' ? $item_kit_location_data['unit_price'] : NULL,
						'override_default_tax'=> isset($item_kit_location_data['override_default_tax'] ) && $item_kit_location_data['override_default_tax'] != '' ? $item_kit_location_data['override_default_tax'] : 0,
						'tax_class_id'=> isset($item_kit_location_data['tax_class']) && $item_kit_location_data['tax_class'] ? $item_kit_location_data['tax_class'] : NULL,
					);
					$this->Item_kit_location->save($data, $item_kit_id,$location_id);
					

					if (isset($item_kit_location_data['item_tier']))
					{
						$tier_type = $item_kit_location_data['tier_type'];

						foreach($item_kit_location_data['item_tier'] as $tier_id => $price_or_percent)
						{
							//If we are overriding prices and we have a price/percent, add..otherwise delete
							if ($override_prices && $price_or_percent)
							{				
								$tier_data=array('tier_id'=>$tier_id);
								$tier_data['item_kit_id'] = isset($item_data['item_kit_id']) ? $item_data['item_kit_id'] : $item_kit_id;
								$tier_data['location_id'] = $location_id;
							
								if ($tier_type[$tier_id] == 'unit_price')
								{
									$tier_data['unit_price'] = $price_or_percent;
									$tier_data['percent_off'] = NULL;
									$tier_data['cost_plus_percent'] = NULL;
									$tier_data['cost_plus_fixed_amount'] = NULL;
								}
								elseif($tier_type[$tier_id] == 'percent_off')
								{
									$tier_data['percent_off'] = (float)$price_or_percent;
									$tier_data['unit_price'] = NULL;
									$tier_data['cost_plus_percent'] = NULL;
									$tier_data['cost_plus_fixed_amount'] = NULL;
								}
								elseif($tier_type[$tier_id] == 'cost_plus_percent')
								{
									$tier_data['percent_off'] = NULL;
									$tier_data['unit_price'] = NULL;
									$tier_data['cost_plus_percent'] = (float)$price_or_percent;
									$tier_data['cost_plus_fixed_amount'] = NULL;
								}
								elseif($tier_type[$tier_id] == 'cost_plus_fixed_amount')
								{
									$tier_data['percent_off'] = NULL;
									$tier_data['unit_price'] = NULL;
									$tier_data['cost_plus_percent'] = NULL;
									$tier_data['cost_plus_fixed_amount'] = (float)$price_or_percent;
								}

								$this->Item_kit_location->save_item_tiers($tier_data,$item_kit_id, $location_id);
							}
							else
							{
								$this->Item_kit_location->delete_tier_price($tier_id, $item_kit_id, $location_id);
							}

						}
					}
									
					$location_items_taxes_data = array();
				
					$tax_names = $item_kit_location_data['tax_names'];
					$tax_percents = $item_kit_location_data['tax_percents'];
					$tax_cumulatives = $item_kit_location_data['tax_cumulatives'];
					for($k=0;$k<count($tax_percents);$k++)
					{
						if (is_numeric($tax_percents[$k]))
						{
							$location_items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
						}
					}
					$this->Item_kit_location_taxes->save($location_items_taxes_data, $item_kit_id, $location_id);
				}
			}
			
			if ($this->input->post('item_kit_item'))
			{
				$item_kit_items = array();
				foreach($this->input->post('item_kit_item') as $item_id => $quantity)
				{
					$item_kit_items[] = array(
						'item_id' => $item_id,
						'quantity' => $quantity
						);
				}
			
				$this->Item_kit_items->save($item_kit_items, $item_kit_id);
			}
			
			$item_kits_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$item_kits_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Item_kit_taxes->save($item_kits_taxes_data, $item_kit_id);
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('item_kits_error_adding_updating').' '.
			$item_kit_data['name'],'item_kit_id'=>-1));
		}

	}
	
	function delete()
	{
		$this->check_action_permission('delete');		
		$item_kits_to_delete=$this->input->post('ids');

		if($this->Item_kit->delete_list($item_kits_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>lang('item_kits_successful_deleted').' '.
			count($item_kits_to_delete).' '.lang('item_kits_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('item_kits_cannot_be_deleted')));
		}
	}

	function clear_state()
	{
		$this->session->unset_userdata('item_kit_search_data');
		redirect('item_kits');
	}

	function generate_barcodes($item_kit_ids, $skip=0)
	{
		$this->load->helper('item_kits');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit_taxes_finder');
			
		$data['items'] = get_item_kits_barcode_data($item_kit_ids);
		$data['scale'] = 1;
		$data['skip'] = $skip;
		$this->load->view("barcode_sheet", $data);
	}
	
	function generate_barcode_labels($item_kit_ids)
	{
		$this->load->helper('item_kits');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit_taxes_finder');
		$data['items'] = get_item_kits_barcode_data($item_kit_ids);
		$data['scale'] = 1;
		$this->load->view("barcode_labels", $data);
	}
	
	function tags()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Tag->get_tag_suggestions($this->input->get('term'),25);
		echo json_encode($suggestions);
	}
	
	function cleanup()
	{
		$this->Item_kit->cleanup();
		echo json_encode(array('success'=>true,'message'=>lang('item_kits_cleanup_sucessful')));
	}
	
	function get_item_info()
	{
		$this->load->model('Item');
		
		if(!$this->Item->exists(does_contain_only_digits($this->input->post('item_number')) ? (int)$this->input->post('item_number') : -1))	
		{
			$item_id = $this->Item->get_item_id($this->input->post('item_number'));
		}
		else
		{
			$item_id = (int)$this->input->post('item_number');
		}
		
		
		if ($item_id)
		{
			echo json_encode($this->Item->get_info($item_id));
		}
		else
		{
			echo json_encode("");
		}
	}
	
	function save_column_prefs()
	{
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save('item_kit_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('item_kit_column_prefs');			
		}
	}
	
	
	function reload_table()
	{
		$this->lang->load('items');
		$config['base_url'] = site_url('item_kits/sorting');
		$config['per_page'] = $this->config->item('number_of_item_kits_per_page') ? (int)$this->config->item('number_of_item_kits_per_page') : 20; 
		$params = $this->session->userdata('item_kit_search_data') ? $this->session->userdata('item_kit_search_data') : array('offset' => 0, 'order_col' => 'item_kit_id', 'order_dir' => 'desc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');

		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
		
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'],10000, $data['fields']);
			$table_data = $this->Item_kit->search($data['search'],$data['category_id'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item_kit->count_all();
			$table_data = $this->Item_kit->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		echo get_item_kits_manage_table($table_data,$this);
	}
}
?>
