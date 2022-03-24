<?php
require_once ("Secure_area.php");
require_once ("interfaces/Idata_controller.php");
class Items extends Secure_area implements Idata_controller
{
	function __construct()
	{
		parent::__construct('items');
		$this->load->model('Inventory');
		$this->load->model('Additional_item_numbers');
		$this->lang->load('items');
		$this->lang->load('module');
		$this->load->model('Item');
		$this->load->model('Category');
		$this->load->model('Tag');		
	}

	function index($offset=0)
	{		
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'item_id', 'order_dir' => 'desc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		if ($offset!=$params['offset'])
		{
		   redirect('items/index/'.$params['offset']);
		}

		$this->check_action_permission('search');
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		
		$data['controller_name']=strtolower(get_class());
		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		$data['categories'][''] = lang('common_all');
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
		
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
		
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'],10000, $data['fields']);
			$table_data = $this->Item->search($data['search'],$data['category_id'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item->count_all();
			$table_data = $this->Item->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}

		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['order_col'] = $params['order_col'];
		$data['order_dir'] = $params['order_dir'];		
		$data['manage_table']=get_items_manage_table($table_data,$this);
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Item->get_default_columns();
		$data['selected_columns'] = $this->Employee->get_item_columns_to_display();
		$data['all_columns'] = array_merge($data['selected_columns'],$this->Item->get_displayable_columns());		
		$this->load->view('items/manage',$data);
		
	}
	
	function reload_table()
	{
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'item_id', 'order_dir' => 'desc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');

		$data['per_page'] = $config['per_page'];
		$data['search'] = $params['search'] ? $params['search'] : "";
		$data['category_id'] = $params['category_id'] ? $params['category_id'] : "";
		
		$data['fields'] = $params['fields'] ? $params['fields'] : "all";
		
		if ($data['search'] || $data['category_id'])
		{
			$config['total_rows'] = $this->Item->search_count_all($data['search'], $data['category_id'],10000, $data['fields']);
			$table_data = $this->Item->search($data['search'],$data['category_id'],$data['per_page'],$params['offset'],$params['order_col'],$params['order_dir'], $data['fields']);
		}
		else
		{
			$config['total_rows'] = $this->Item->count_all();
			$table_data = $this->Item->get_all($data['per_page'],$params['offset'],$params['order_col'],$params['order_dir']);
		}
		
		echo get_items_manage_table($table_data,$this);
	}
	
	function categories()
	{
		$this->check_action_permission('manage_categories');
		$categories = $this->Category->get_all_categories_and_sub_categories_as_tree();		
		$data = array('category_tree' => $this->_category_tree_list($categories));
		$this->load->view('items/categories',$data);		
	}
	
	function save_category($category_id = FALSE)
	{	
		$this->check_action_permission('manage_categories');

		$parent_id = $this->input->post('parent_id');
		$category_name = $this->input->post('category_name');
		$hide_from_grid = $this->input->post('hide_from_grid') ? 1 : 0;
		$category_color = $this->input->post('category_color');
		$delete_image = $this->input->post('del_image');
			
		//Save Image File
		$category_image_id = NULL;
		if(!empty($_FILES["category_image"]) && $_FILES["category_image"]["error"] == UPLOAD_ERR_OK)
		{			    
		  $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
			$extension = strtolower(pathinfo($_FILES["category_image"]["name"], PATHINFO_EXTENSION));
			$category_info = $this->Category->get_info($category_id);
			
		    if (in_array($extension, $allowed_extensions))
		    {
			    $config['image_library'] = 'gd2';
			    $config['source_image']	= $_FILES["category_image"]["tmp_name"];
			    $config['create_thumb'] = FALSE;
			    $config['maintain_ratio'] = TRUE;
			    $config['width']	 = 400;
			    $config['height']	= 300;
			    $this->load->library('image_lib', $config); 
			    $this->image_lib->resize();
		   	 	$this->load->model('Appfile');
		
			    $category_image_id = $this->Appfile->save($_FILES["category_image"]["name"], file_get_contents($_FILES["category_image"]["tmp_name"]), NULL, $category_info->image_id);
		    }
		} 
		elseif($delete_image && $category_id !== FALSE)
		{
			$this->Category->delete_category_image($category_id);
		}
		
		if (!$parent_id)
		{
			$parent_id = NULL;
		}
		
		if ($phppos_category_id = $this->Category->save($category_name, $hide_from_grid, $parent_id, $category_id, $category_color, $category_image_id))
		{
			if ($category_id !== FALSE)
			{
				if ($this->config->item("ecommerce_platform"))
				{
					require_once (APPPATH."models/interfaces/Ecom.php");
					$ecom_model = Ecom::get_ecom_model();
					$ecom_cat_id = $this->Category->get_ecommerce_category_id($phppos_category_id);
					$cat_info = $this->Category->get_info($phppos_category_id);
				
					if ($ecom_cat_id !== NULL)
					{
						if (strtolower(get_class($ecom_model)) == 'woo')
						{
							if ($cat_info->image_id !== NULL)
							{
								$phppos_cat_image_url = app_file_url($cat_info->image_id);
							}
							else
							{
								$phppos_cat_image_url = NULL;
							}
						
							$ecom_model->update_category_from_phppos_to_woocommerce($category_name,$phppos_cat_image_url,$phppos_category_id, $ecom_cat_id);
						}
					}
				}
			}
			echo json_encode(array('success'=>true,'message'=>lang('items_category_successful_adding').' '.$category_name));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_category_successful_error')));
		}
	}
		
	function delete_category()
	{
		$this->check_action_permission('manage_categories');		
		$category_id = $this->input->post('category_id');
		if($this->Category->delete($category_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
	}
	
	function get_category_tree_list()
	{
		$categories = $this->Category->get_all_categories_and_sub_categories_as_tree();
		echo $this->_category_tree_list($categories);
	}
	
	function manage_tags()
	{
		$this->check_action_permission('manage_tags');
		$tags = $this->Tag->get_all();
		$data = array('tags' => $tags, 'tag_list' => $this->_tag_list());
		$this->load->view('items/tags',$data);		
	}
	
	function save_tag($tag_id = FALSE)
	{		
		$this->check_action_permission('manage_tags');
		$tag_name = $this->input->post('tag_name');
		
		if ($this->Tag->save($tag_name, $tag_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_tag_successful_adding').' '.$tag_name));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_tag_successful_error')));
		}
	}
	
	function delete_tag()
	{
		$this->check_action_permission('manage_tags');		
		$tag_id = $this->input->post('tag_id');
		if($this->Tag->delete($tag_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
	}
	
	function tag_list()
	{
		echo $this->_tag_list();
	}
	
	function _tag_list()
	{
		$tags = $this->Tag->get_all();
     	$return = '<ul>';
		foreach($tags as $tag_id => $tag) 
		{
			$return .='<li>'.$tag['name'].
					'<a href="javascript:void(0);" class="edit_tag" data-name = "'.H($tag['name']).'" data-tag_id="'.$tag_id.'">['.lang('common_edit').']</a> '.
					'<a href="javascript:void(0);" class="delete_tag" data-tag_id="'.$tag_id.'">['.lang('common_delete').']</a> ';
			 $return .='</li>';
		}
     	$return .='</ul>';
		
		return $return;
	}
	
	function _category_tree_list($tree) 
	{
		$this->load->model('Appfile');
		$return = '';
    if(!is_null($tree) && count($tree) > 0) 
		{
        $return = '<ul>';
        foreach($tree as $node) 
				{
            $return .='<li>'.$node->name. ' <a href="javascript:void(0);" class="add_child_category" data-category_id="'.$node->id.'">['.lang('items_add_child_category').']</a> '.
						'<a href="javascript:void(0);" class="edit_category" data-color="'.H($node->color).'" data-image_id="'.H($node->image_id).'" data-image_timestamp="'.$this->Appfile->get_file_timestamp($node->image_id).'" data-name = "'.H($node->name).'" data-parent_id = "'.$node->parent_id.'" data-category_id="'.$node->id.'">['.lang('common_edit').']</a> '.
							'<a href="javascript:void(0);" class="delete_category" data-category_id="'.$node->id.'">['.lang('common_delete').']</a> '.
							'&nbsp;&nbsp;&nbsp;<label for="hide_from_grid_'.$node->id.'">'.lang('items_hide_from_item_grid').'</label> <input type="checkbox" '.($node->hide_from_grid ? 'checked="checked"' : '' ).' class="hide_from_grid" id="hide_from_grid_'.$node->id.'" value="1" name="hide_from_grid_'.$node->id.'" data-category_id="'.$node->id.'" /> <label for="hide_from_grid_'.$node->id.'"><span></span></label>';
            $return .= $this->_category_tree_list($node->children);
	          $return .='</li>';
        }
        $return .='</ul>';
    }
		
		return $return;
	}
	
	function manage_manufacturers()
	{
		$this->check_action_permission('manage_manufacturers');
		$this->load->model('Manufacturer');
		$manufacturers = $this->Manufacturer->get_all();
		$data = array('manufacturers' => $manufacturers, 'manufacturers_list' => $this->_manufacturers_list());
		$this->load->view('items/manufacturers',$data);		
	
	}
	
	function save_manufacturer($manufacturer_id = FALSE)
	{
		$this->check_action_permission('manage_manufacturers');
		$this->load->model('Manufacturer');
		$manufacturer_name = $this->input->post('manufacturer_name');
		
		if ($this->Manufacturer->save($manufacturer_name, $manufacturer_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_manufacturer_successful_adding').' '.$manufacturer_name));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_manufacturer_successful_error')));
		}
	
	}
	
	function delete_manufacturer()
	{
		$this->check_action_permission('manage_manufacturers');
		$this->load->model('Manufacturer');
		$manufacturer_id = $this->input->post('manufacturer_id');
		if($this->Manufacturer->delete($manufacturer_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
		
	}
	
	function manufacturers_list()
	{
		echo $this->_manufacturers_list();
	}
	
	function _manufacturers_list()
	{
		$this->load->model('Manufacturer');
		$manufacturers = $this->Manufacturer->get_all();
     	$return = '<ul>';
		foreach($manufacturers as $manufacturers_id => $manufacturers) 
		{
			$return .='<li>'.$manufacturers['name'].
					'<a href="javascript:void(0);" class="edit_manufacturer" data-name = "'.H($manufacturers['name']).'" data-manufacturer_id="'.$manufacturers_id.'">['.lang('common_edit').']</a> '.
					'<a href="javascript:void(0);" class="delete_manufacturer" data-manufacturer_id="'.$manufacturers_id.'">['.lang('common_delete').']</a> ';
			 $return .='</li>';
		}
     	$return .='</ul>';
		
		return $return;
	}		
	
	function sorting()
	{		
		$this->check_action_permission('search');
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('order_col' => 'name', 'order_dir' => 'asc');
		$search=$this->input->post('search') ? $this->input->post('search') : "";
		$category_id = $this->input->post('category_id');
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
		
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : $params['order_col'];
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): $params['order_dir'];
		

		$item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search, 'category_id' => $category_id, 'fields' => $fields);
		
		$this->session->set_userdata("item_search_data",$item_search_data);
		if ($search || $category_id)
		{
			$config['total_rows'] = $this->Item->search_count_all($search, $category_id);
			$table_data = $this->Item->search($search,$category_id, $per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir, $fields);
		}
		else
		{
			$config['total_rows'] = $this->Item->count_all();
			$table_data = $this->Item->get_all($per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $order_col ,$order_dir);
		}
		$config['base_url'] = site_url('items/sorting');
		$config['per_page'] = $per_page; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Item->get_default_columns();
		$data['manage_table']=get_items_manage_table_data_rows($table_data,$this);
		
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));	
	}

	
	function find_item_info()
	{
		$item_number=$this->input->post('scan_item_number');
		echo json_encode($this->Item->find_item_info($item_number));
	}
		
	function item_number_exists()
	{
		if($this->Item->account_number_exists($this->input->post('item_number')))
		echo 'false';
		else
		echo 'true';
		
	}

	function product_id_exists()
	{
		if($this->Item->product_id_exists($this->input->post('product_id')))
		echo 'false';
		else
		echo 'true';
	}
	
	function check_duplicate()
	{
		echo json_encode(array('duplicate'=>$this->Item->check_duplicate($this->input->post('term'))));
	}
		
	function search()
	{
		$this->check_action_permission('search');
		$search=$this->input->post('search');
		$category_id = $this->input->post('category_id');
		$offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
		$order_col = $this->input->post('order_col') ? $this->input->post('order_col') : 'name';
		$order_dir = $this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc';
		$fields = $this->input->post('fields') ? $this->input->post('fields') : 'all';
		
		$item_search_data = array('offset' => $offset, 'order_col' => $order_col, 'order_dir' => $order_dir, 'search' => $search,  'category_id' => $category_id, 'fields' => $fields);
		$this->session->set_userdata("item_search_data",$item_search_data);
		$per_page=$this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$search_data=$this->Item->search($search, $category_id, $per_page,$this->input->post('offset') ? $this->input->post('offset') : 0, $this->input->post('order_col') ? $this->input->post('order_col') : 'name' ,$this->input->post('order_dir') ? $this->input->post('order_dir'): 'asc', $fields);
		$config['base_url'] = site_url('items/search');
		$config['total_rows'] = $this->Item->search_count_all($search, $category_id,10000, $fields);
		$config['per_page'] = $per_page ;
		
		$this->load->library('pagination');$this->pagination->initialize($config);				
		$data['pagination'] = $this->pagination->create_links();
		$this->load->model('Employee_appconfig');
		$data['default_columns'] = $this->Item->get_default_columns();
		$data['manage_table']=get_items_manage_table_data_rows($search_data,$this);
		echo json_encode(array('manage_table' => $data['manage_table'], 'pagination' => $data['pagination'],'total_rows' => $config['total_rows']));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_manage_items_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function item_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),'unit_price',100);
		echo json_encode($suggestions);
	}

	function get_info($item_id=-1)
	{
		echo json_encode($this->Item->get_info($item_id));
	}

	function _get_item_data($item_id)
	{
      $this->load->helper('report');
			$this->load->model('Item_serial_number');
			$this->load->model('Tax_class');
		
		$data = array();
		$data['controller_name']=strtolower(get_class());

		$data['item_info']=$this->Item->get_info($item_id);
		$data['tax_classes'] = array();
		$data['tax_classes'][''] = lang('common_none');
		
		foreach($this->Tax_class->get_all()->result_array() as $tax_class)
		{
			$data['tax_classes'][$tax_class['id']] = $tax_class['name'];
		}
		
		$data['item_images']=$this->Item->get_item_images($item_id);
		
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
		$data['selected_manufacturer'] = $this->Item->get_info($item_id)->manufacturer_id;
				
		$data['tags'] = implode(',',$this->Tag->get_tags_for_item($item_id));
		$data['item_tax_info']=$this->Item_taxes->get_info($item_id);
		$data['tiers']=$this->Tier->get_all()->result();
		$data['locations'] = array();
		$data['location_tier_prices'] = array();
		$data['additional_item_numbers'] = $this->Additional_item_numbers->get_item_numbers($item_id);
		$data['serial_numbers'] = $this->Item_serial_number->get_all($item_id);
		
		if ($item_id != -1)
		{
			$data['next_item_id'] = $this->Item->get_next_id($item_id);
			$data['prev_item_id'] = $this->Item->get_prev_id($item_id);;
		}
			
		foreach($this->Location->get_all()->result() as $location)
		{
			if($this->Employee->is_location_authenticated($location->location_id))
			{				
				$data['locations'][] = $location;
				$data['location_items'][$location->location_id] = $this->Item_location->get_info($item_id,$location->location_id);
				$data['location_taxes'][$location->location_id] = $this->Item_location_taxes->get_info($item_id, $location->location_id);
								
				foreach($data['tiers'] as $tier)
				{					
					$tier_prices = $this->Item_location->get_tier_price_row($tier->id,$data['item_info']->item_id, $location->location_id);
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
				
		
		if ($item_id == -1)
		{
			$suppliers = array(''=> lang('common_not_set'), '-1' => lang('common_none'));
		}
		else
		{
			$suppliers = array('-1' => lang('common_none'));
		}
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
		}
		
		$data['tier_prices'] = array();
		$data['tier_type_options'] = array('unit_price' => lang('common_fixed_price'), 'percent_off' => lang('common_percent_off'), 'cost_plus_percent' => lang('common_cost_plus_percent'),'cost_plus_fixed_amount' => lang('common_cost_plus_fixed_amount'));
		foreach($data['tiers'] as $tier)
		{
			$tier_prices = $this->Item->get_tier_price_row($tier->id,$data['item_info']->item_id);
			
			if (!empty($tier_prices))
			{
				$data['tier_prices'][$tier->id] = $tier_prices;
			}
			else
			{
				$data['tier_prices'][$tier->id] = FALSE;			
			}
		}

		$data['suppliers']=$suppliers;
		$data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;
		
		$decimals = $this->Appconfig->get_raw_number_of_decimals();
		$decimals = $decimals !== NULL && $decimals!= '' ? $decimals : 2;
		$data['decimals'] = $decimals;
		
		return $data;
	}
	function view($item_id=-1,$redirect=0, $sale_or_receiving = 'sale')
	{		
		$this->load->model('Item_taxes');
		$this->load->model('Tier');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_taxes_finder');
		$this->check_action_permission('add_update');
    $this->load->helper('report');
		
		$data = $this->_get_item_data($item_id);
		$data['redirect']=$redirect;
		$data['sale_or_receiving']=$sale_or_receiving;			
		$this->load->view("items/form",$data);
	}
	function clone_item($item_id)
	{		
		$this->load->model('Item_taxes');
		$this->load->model('Tier');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Supplier');
		$this->load->model('Item_taxes_finder');
		
		
		$this->check_action_permission('add_update');
     $this->load->helper('report');
		$data = $this->_get_item_data($item_id);
		$data['redirect']=2;
		//Unset unique identifiers
		$data['item_info']->item_number = '';
		$data['item_info']->ecommerce_product_id = '';
		$data['item_info']->product_id = '';	
		$data['item_images'] = array();
		$data['additional_item_numbers'] = array();	
		$data['serial_numbers'] = array();	
		$data['is_clone'] = TRUE;		
		$this->load->view("items/form",$data);
	}
	
	function inventory($item_id=-1,$offset=0)
	{
		$this->load->model('Item_location');
		
		$this->check_action_permission('edit_quantity');
		$data['item_info']=$this->Item->get_info($item_id);
		$data['item_location_info']=$this->Item_location->get_info($item_id);
		
		$config['base_url'] = site_url('items/inventory/'.$item_id);
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['total_rows'] = $this->Inventory->count_all($item_id);
		$config['uri_segment'] = 4;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['inventory_data'] = $this->Inventory->get_inventory_data_for_item($item_id, $config['per_page'],$offset)->result_array();
	
		$this->load->view("items/inventory",$data);
	}

	function generate_barcodes($item_ids, $skip=0)
	{		
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes_finder');
		
		$this->load->helper('items');
		$data['items'] = get_items_barcode_data($item_ids);
		$data['scale'] = 1;
		$data['skip'] = $skip;
		
		$this->load->view("barcode_sheet", $data);
	}

	function generate_barcode_labels($item_ids)
	{		
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes_finder');
		
		$this->load->helper('items');
		$data['items'] = get_items_barcode_data($item_ids);
		$data['scale'] = 1;
		$this->load->view("barcode_labels", $data);
	}
	
	function generate_barcodes_from_recv($recv_id, $skip=0)
	{
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Receiving');
		$item_ids = array();
		$items_expire = array();
		
		foreach($this->Receiving->get_receiving_items($recv_id)->result() as $item)
		{
			for($k = 0; $k< abs((int)$item->quantity_purchased);$k++)
			{
				$item_ids[] = $item->item_id;
				$key = $item->receiving_id.'|'.$item->item_id;
				$items_expire[$key] = $item->expire_date ? date(get_date_format(), strtotime($item->expire_date)) : FALSE;
			}
		}
	
		
		$data = array();
		$this->load->helper('items');
		$data['items'] = get_items_barcode_data(implode('~',$item_ids));
		$data['items_expire'] = $items_expire;
		$data['scale'] = 1;
		$data['from_recv'] = $recv_id;
		$data['skip'] = $skip;
		
		$this->load->view("barcode_sheet", $data);
	}
	
	
	function generate_barcodes_labels_from_recv($recv_id)
	{
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Receiving');
		
		$item_ids = array();
		$items_expire = array();
		
		foreach($this->Receiving->get_receiving_items($recv_id)->result() as $item)
		{
			for($k = 0; $k< abs((int)$item->quantity_purchased);$k++)
			{
				$item_ids[] = $item->item_id;
				$key = $item->receiving_id.'|'.$item->item_id;
				$items_expire[$key] = $item->expire_date ? date(get_date_format(), strtotime($item->expire_date)) : FALSE;
			}
		}
		
		$data = array();
		$this->load->helper('items');
		$data['items'] = get_items_barcode_data(implode('~',$item_ids));
		$data['items_expire'] = $items_expire;
		$data['scale'] = 1;
		$data['from_recv'] = $recv_id;
		$this->load->view("barcode_labels", $data);
	}
	

	function bulk_edit()
	{
		$this->load->model('Supplier');
		$this->load->model('Tier');
		$this->load->model('Tax_class');
		$this->check_action_permission('add_update');		
		$this->load->helper('report');
        $data = array();
		
		$data['tax_classes'] = array();
		$data['tax_classes'][''] = lang('common_do_nothing');
		
		foreach($this->Tax_class->get_all()->result_array() as $tax_class)
		{
			$data['tax_classes'][$tax_class['id']] = $tax_class['name'];
		}
		
		
		$suppliers = array('' => lang('common_do_nothing'), '-1' => lang('common_none'));
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name']. ' ('.$row['first_name'] .' '. $row['last_name'].')';
		}
		$data['suppliers'] = $suppliers;
		$data['categories'][''] = lang('common_do_nothing');
		$data['manufacturers'][''] = lang('common_do_nothing');

		$this->load->model('Manufacturer');
		$manufacturers = array('' => lang('common_do_nothing'), '-1' => lang('common_none'));
		foreach($this->Manufacturer->get_all() as $id => $row)
		{
			$manufacturers[$id] = $row['name'];
		}
		$data['manufacturers'] = $manufacturers;
		
		
		$categories = $this->Category->sort_categories_and_sub_categories($this->Category->get_all_categories_and_sub_categories());
		foreach($categories as $key=>$value)
		{
			$name = str_repeat('&nbsp;&nbsp;', $value['depth']).$value['name'];
			$data['categories'][$key] = $name;
		}
				
		$data['item_cost_price_choices'] = array(
			''=>lang('common_do_nothing'), 
			'fixed'=>lang('common_fixed_price'), 
			'percent'=>lang('items_increase_decrease_percent'),			
		);
		
		
		$data['disable_loyalty_choices'] = array(			
			''=>lang('common_do_nothing'), 
			'0' => lang('common_no'), 
			'1' => lang('common_yes')
		);
		
		
		$data['change_cost_price_during_sale_choices'] = array(
			''=>lang('common_do_nothing'), 
			'0' => lang('common_no'), 
			'1' => lang('common_yes'));
	
		$data['change_is_ebt_item_during_sale_choices'] = array(
			''=>lang('common_do_nothing'), 
			'0' => lang('common_no'), 
			'1' => lang('common_yes'));	

		$data['item_unit_price_choices'] = array(
			''=>lang('common_do_nothing'), 
			'fixed'=>lang('common_fixed_price'), 
			'percent'=>lang('items_increase_decrease_percent'),			
		);
		
		
		$data['item_promo_price_choices'] = array(
			''=>lang('common_do_nothing'), 
			'fixed'=>lang('common_fixed_price'), 
			'percent'=>lang('items_increase_decrease_percent'),			
		);
				
		$data['override_default_commission_choices'] = array(			
			''=>lang('common_do_nothing'), 
			'0' => lang('common_no'), 
			'1' => lang('common_yes'));
		
		$data['override_default_tax_choices'] = array(
			''=>lang('common_do_nothing'), 
			'0' => lang('common_no'), 
			'1' => lang('common_yes'));
			
		$data['allow_alt_desciption_choices'] = array(
			''=>lang('common_do_nothing'),
			1 =>lang('items_change_all_to_allow_alt_desc'),
			0 =>lang('items_change_all_to_not_allow_allow_desc'));
	 
       
		$data['serialization_choices'] = array(
			''=>lang('common_do_nothing'),
			1 =>lang('items_change_all_to_serialized'),
			0 =>lang('items_change_all_to_unserialized'));

		$data['tax_included_choices'] = array(
				''=>lang('common_do_nothing'),
				'0' => lang('common_no'), 
				'1' => lang('common_yes'));
			
		$data['is_ecommerce_choices'] = array(
				''=>lang('common_do_nothing'),
				'0' => lang('common_no'), 
				'1' => lang('common_yes'));
		
		$data['is_service_choices'] = array(
			''=>lang('common_do_nothing'),
			'0' => lang('common_no'), 
			'1' => lang('common_yes'));
			
			
		$this->load->view("items/form_bulk", $data);
	}

	function save($item_id=-1)
	{
		if ($this->config->item("ecommerce_platform"))
		{
			require_once (APPPATH."models/interfaces/Ecom.php");
			$ecom_model = Ecom::get_ecom_model();
			$e_new_quantity = 0;
		}
		
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Item_location_taxes');
		
		$is_ecom_configured = false;
		if ($this->config->item("ecommerce_platform"))
		{
			$is_ecom_configured=$ecom_model->is_configured();
			$was_ecommerce_item = FALSE;
		}
		
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
				
		$item_data = array(
		'name'=>$this->input->post('name'),
		'description'=>$this->input->post('description'),
		'tax_included'=>$this->input->post('tax_included') ? $this->input->post('tax_included') : 0,
		'category_id'=>$category_id,
		'size'=>$this->input->post('size'),
		'expire_days'=>$this->input->post('expire_days') ?  $this->input->post('expire_days') : NULL,
		'supplier_id'=>$this->input->post('supplier_id')== -1 || $this->input->post('supplier_id') == '' ? null:$this->input->post('supplier_id'),
		'manufacturer_id'=>$this->input->post('manufacturer_id')== -1 || $this->input->post('manufacturer_id') == '' ? null:$this->input->post('manufacturer_id'),
		'item_number'=>$this->input->post('item_number')=='' ? null:$this->input->post('item_number'),
		'product_id'=>$this->input->post('product_id')=='' ? null:$this->input->post('product_id'),
		'ecommerce_product_id'=>$this->input->post('ecommerce_product_id') ? $this->input->post('ecommerce_product_id') : NULL,
		'cost_price'=>$this->input->post('cost_price'),
		'change_cost_price' => $this->input->post('change_cost_price') ? $this->input->post('change_cost_price') : 0,
 		'unit_price'=>$this->input->post('unit_price'),
		'promo_price'=>$this->input->post('promo_price') ? $this->input->post('promo_price') : NULL,
		'start_date'=>$this->input->post('start_date') ? date('Y-m-d', strtotime($this->input->post('start_date'))) : NULL,
		'end_date'=>$this->input->post('end_date') ?date('Y-m-d', strtotime($this->input->post('end_date'))) : NULL,
		'min_edit_price'=>$this->input->post('min_edit_price') !== '' ? $this->input->post('min_edit_price') : NULL,
		'max_edit_price'=>$this->input->post('max_edit_price') !== '' ? $this->input->post('max_edit_price') : NULL,
		'max_discount_percent'=>$this->input->post('max_discount_percent') !== '' ? $this->input->post('max_discount_percent') : NULL,
		
		'reorder_level'=>$this->input->post('reorder_level')!='' ? $this->input->post('reorder_level') : NULL,
		'replenish_level'=>$this->input->post('replenish_level')!='' ? $this->input->post('replenish_level') : NULL,
		'is_service'=>$this->input->post('is_service') ? $this->input->post('is_service') : 0 ,
		'allow_alt_description'=>$this->input->post('allow_alt_description') ? $this->input->post('allow_alt_description') : 0 ,
		'is_serialized'=>$this->input->post('is_serialized') ? $this->input->post('is_serialized') : 0,
		'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
		'tax_class_id'=> $this->input->post('tax_class') ? $this->input->post('tax_class') : NULL,
		'is_ebt_item'=> $this->input->post('is_ebt_item') ? $this->input->post('is_ebt_item') : 0,
		'is_ecommerce'=> $this->input->post('is_ecommerce') ? $this->input->post('is_ecommerce') : 0,
		);
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$item_data['disable_loyalty'] = $this->input->post('disable_loyalty') ? $this->input->post('disable_loyalty') : 0;
		}
		
		if ($this->input->post('override_default_commission'))
		{
			if ($this->input->post('commission_type') == 'fixed')
			{
				$item_data['commission_fixed'] = (float)$this->input->post('commission_value');
				$item_data['commission_percent_type'] = '';
				$item_data['commission_percent'] = NULL;
			}
			else
			{
				$item_data['commission_percent'] = (float)$this->input->post('commission_value');
				$item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
				$item_data['commission_fixed'] = NULL;
			}
		}
		else
		{
			$item_data['commission_percent'] = NULL;
			$item_data['commission_fixed'] = NULL;
			$item_data['commission_percent_type'] = '';
		}
		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
		
		if ($cur_item_info->is_ecommerce)
		{
			$was_ecommerce_item = TRUE;
		}
		$redirect=$this->input->post('redirect');
		$sale_or_receiving=$this->input->post('sale_or_receiving');
		
		if($this->Item->save($item_data,$item_id))
		{			
			$this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $this->input->post('tags'));
			$tier_type = $this->input->post('tier_type');
			
			if ($this->input->post('item_tier'))
			{
				foreach($this->input->post('item_tier') as $tier_id => $price_or_percent)
				{
					if ($price_or_percent)
					{				
						$tier_data=array('tier_id'=>$tier_id);
						$tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;

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
					
						$this->Item->save_item_tiers($tier_data,$item_id);
					}
					else
					{
						$this->Item->delete_tier_price($tier_id, $item_id);
					}
				
				}
			}
			
			
			$success_message = '';
			
			//New item
			if($item_id==-1)
			{	
				$success_message = lang('common_successful_adding').' '.$item_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_id'=>$item_data['item_id'],'redirect' => $redirect, 'sale_or_receiving'=>$sale_or_receiving));
				$item_id = $item_data['item_id'];
			}
			else //previous item
			{
				$success_message = lang('common_items_successful_updating').' '.$item_data['name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'message'=>$success_message,'item_id'=>$item_id,'redirect' => $redirect, 'sale_or_receiving'=>$sale_or_receiving));
			}
			
			if ($this->input->post('additional_item_numbers') && is_array($this->input->post('additional_item_numbers')))
			{
				$this->Additional_item_numbers->save($item_id, $this->input->post('additional_item_numbers'));
			}
			else
			{
				$this->Additional_item_numbers->delete($item_id);
			}
			
			$this->load->model('Item_serial_number');
			if ($this->input->post('serial_numbers') && is_array($this->input->post('serial_numbers')))
			{
				$this->Item_serial_number->save($item_id, $this->input->post('serial_numbers'), $this->input->post('serial_number_prices'));
			}
			else
			{
				$this->Item_serial_number->delete($item_id);
			}
			
			
			if ($this->input->post('locations'))
			{
				foreach($this->input->post('locations') as $location_id => $item_location_data)
				{		        
					$override_prices = isset($item_location_data['override_prices']) && $item_location_data['override_prices'];
					$quantity_add_minus = isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] ? $item_location_data['quantity_add_minus'] : 0;
					$quantity = isset($item_location_data['quantity']) && $item_location_data['quantity'] ? $item_location_data['quantity'] : FALSE;
					$item_location_before_save = $this->Item_location->get_info($item_id,$location_id);
					
					if ($quantity === FALSE)
					{
						$new_quantity = ($item_location_before_save->quantity ? $item_location_before_save->quantity : 0) + $quantity_add_minus;						
					}
					else
					{
						$new_quantity = $quantity;
					}
					$data = array(
						'location_id' => $location_id,
						'item_id' => $item_id,
						'location' => $item_location_data['location'],
						'cost_price' => $override_prices && $item_location_data['cost_price'] != '' ? $item_location_data['cost_price'] : NULL,
						'unit_price' => $override_prices && $item_location_data['unit_price'] != '' ? $item_location_data['unit_price'] : NULL,
						'promo_price' => $override_prices && $item_location_data['promo_price'] != '' ? $item_location_data['promo_price'] : NULL,
						'start_date' => $override_prices && $item_location_data['promo_price']!='' && $item_location_data['start_date'] != '' ? date('Y-m-d', strtotime($item_location_data['start_date'])) : NULL,
						'end_date' => $override_prices && $item_location_data['promo_price'] != '' && $item_location_data['end_date'] != '' ? date('Y-m-d', strtotime($item_location_data['end_date'])) : NULL,
						'quantity' => !$this->input->post('is_service')  ? $new_quantity : NULL,
						'reorder_level' => isset($item_location_data['reorder_level']) && $item_location_data['reorder_level'] != '' ? $item_location_data['reorder_level'] : NULL,
						'override_default_tax'=> isset($item_location_data['override_default_tax'] ) && $item_location_data['override_default_tax'] != '' ? $item_location_data['override_default_tax'] : 0,
						'tax_class_id'=> isset($item_location_data['tax_class']) && $item_location_data['tax_class'] ? $item_location_data['tax_class'] : NULL,
						
					);
					
					if($is_ecom_configured == true && $location_id == $ecom_model->ecommerce_store_location) 
					{
						$e_new_quantity = $new_quantity;
					}
					
					$this->Item_location->save($data, $item_id,$location_id);
					

					if (isset($item_location_data['item_tier']))
					{
						$tier_type = $item_location_data['tier_type'];

						foreach($item_location_data['item_tier'] as $tier_id => $price_or_percent)
						{
							//If we are overriding prices and we have a price/percent, add..otherwise delete
							if ($override_prices && $price_or_percent)
							{				
								$tier_data=array('tier_id'=>$tier_id);
								$tier_data['item_id'] = isset($item_data['item_id']) ? $item_data['item_id'] : $item_id;
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
								

								$this->Item_location->save_item_tiers($tier_data,$item_id, $location_id);
							}
							else
							{
								$this->Item_location->delete_tier_price($tier_id, $item_id, $location_id);
							}

						}
					}
									
				
					if (isset($item_location_data['tax_names']))
					{
						$location_items_taxes_data = array();
						$tax_names = $item_location_data['tax_names'];
						$tax_percents = $item_location_data['tax_percents'];
						$tax_cumulatives = $item_location_data['tax_cumulatives'];
						for($k=0;$k<count($tax_percents);$k++)
						{
							if (is_numeric($tax_percents[$k]))
							{
								$location_items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
							}
						}
						$this->Item_location_taxes->save($location_items_taxes_data, $item_id, $location_id);
					}
					
					if (isset($item_location_data['quantity_add_minus']) && $item_location_data['quantity_add_minus'] && !$this->input->post('is_service'))
					{
						$inv_data = array
							(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>lang('items_manually_editing_of_quantity'),
							'trans_inventory'=>$item_location_data['quantity_add_minus'],
							'location_id' => $location_id,
						);
						$this->Inventory->insert($inv_data);
					}
					elseif(isset($item_location_data['quantity']) && $item_location_data['quantity'] && !$this->input->post('is_service'))
					{
						$inv_data = array
							(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>lang('items_manually_editing_of_quantity'),
							'trans_inventory'=>$item_location_data['quantity'] - $item_location_before_save->quantity,
							'location_id' => $location_id,
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}
			$items_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Item_taxes->save($items_taxes_data, $item_id);
			
			//Delete Image
			if($this->input->post('del_images') && $item_id != -1)
			{
				foreach(array_keys($this->input->post('del_images')) as $image_id)
				{
					$this->Item->delete_image($image_id);
				}
			}
			
			if (isset($_FILES['image_files']))
			{
				for($k=0; $k<count($_FILES['image_files']['name']); $k++)
				{
					$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
					$extension = strtolower(pathinfo($_FILES['image_files']['name'][$k], PATHINFO_EXTENSION));
			    if (in_array($extension, $allowed_extensions))
			    {
				    $config['image_library'] = 'gd2';
				    $config['source_image']	= $_FILES['image_files']['tmp_name'][$k];
				    $config['create_thumb'] = FALSE;
				    $config['maintain_ratio'] = TRUE;
				    $config['width']	 = 400;
				    $config['height']	= 300;
				    $this->load->library('image_lib', $config); 
				    $this->image_lib->resize();
			   	 	$this->load->model('Appfile');
				    $image_file_id = $this->Appfile->save($_FILES['image_files']['name'][$k], file_get_contents($_FILES['image_files']['tmp_name'][$k]));
			  		$this->Item->add_image(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id, $image_file_id);
					} 
				}
			}
			
			$titles = $this->input->post('titles');
			$alt_texts = $this->input->post('alt_texts');
			
			if ($titles)
			{
				foreach(array_keys($titles) as $image_id)
				{
					$title = $titles[$image_id];
					$alt_text = $alt_texts[$image_id];
				
	  			$this->Item->save_image_metadata($image_id, $title,$alt_text);
				}
			}
			
			//Eccommerce
			if($is_ecom_configured == true ) 
			{
				$ecom_item_data = $item_data;
				$ecom_item_data['quantity'] = $e_new_quantity;
				$ecom_item_data['tags'] = explode(',', $this->input->post('tags'));
				$ecom_item_data['images'] = array();
				
				if ($item_data['is_ecommerce'])
				{
					foreach($this->Item->get_item_images(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id) as $image)
					{
						$ecom_item_data['images'][] = array('image_id' => $image['image_id'], 'ecommerce_image_id' => $image['ecommerce_image_id'], 'src' => app_file_url($image['image_id']), 'name' => isset($titles[$image['image_id']]) ? $titles[$image['image_id']] : '' , 'alt' => isset($alt_texts[$image['image_id']]) ? $alt_texts[$image['image_id']] : '');
					}
						
					$ecom_model->save_item_from_phppos_to_ecommerce($ecom_item_data, isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
				}
				elseif($was_ecommerce_item)
				{
					$ecom_model->unlink_item(isset($item_data['item_id']) ? $item_data['item_id'] : $item_id);
				}
			}
		}
		else //failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_error_adding_updating').' '.
			$item_data['name'],'item_id'=>-1));
		}

	}

	function save_inventory($item_id=-1)
	{
		$this->load->model('Item_location');
		
		$this->check_action_permission('add_update');		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
		$cur_item_location_info = $this->Item_location->get_info($item_id);
		
		$inv_data = array
		(
			'trans_date'=>date('Y-m-d H:i:s'),
			'trans_items'=>$item_id,
			'trans_user'=>$employee_id,
			'trans_comment'=>$this->input->post('trans_comment'),
			'trans_inventory'=>$this->input->post('newquantity'),
			'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
		);
		$this->Inventory->insert($inv_data);

		//Update stock quantity
		if($this->Item_location->save_quantity($cur_item_location_info->quantity + $this->input->post('newquantity'),$item_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('common_items_successful_updating').' '.
			$cur_item_info->name,'item_id'=>$item_id));
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_error_adding_updating').' '.
			$cur_item_info->name,'item_id'=>-1));
		}

	}

	function clear_state()
	{
		$this->session->unset_userdata('item_search_data');
		redirect('items');
	}

	function bulk_update()
	{
		$this->load->model('Item_location');
		$this->load->model('Item_taxes');

		$cost_price_percent = FALSE;
		$unit_price_percent = FALSE;
		$promo_price_percent = FALSE;
		$promo_price_use_selling_price = FALSE;
		
		$this->db->trans_start();
		
		$this->check_action_permission('add_update');
		$items_to_update=$this->input->post('item_ids');
		$select_inventory=$this->get_select_inventory();

		//clears the total inventory selection
		$this->clear_select_inventory();

		$item_data = array();

		
		foreach($_POST as $key=>$value)
		{
			if ($key == 'submit' || $key == 'tags' || $key == 'tier_types' || $key == 'tier_values' )
			{
				continue;
			}

			//This field is nullable, so treat it differently
			if ($key == 'supplier_id' || $key =='manufacturer_id')
			{
				if ($value!='')
				{
					$item_data["$key"]=$value == '-1' ? null : $value;
				}
			}
			elseif($value != '' && ($key == 'start_date' || $key == 'end_date'))
			{
				$item_data["$key"]=date('Y-m-d', strtotime($value));
			}
			elseif(($value != '' || !empty($_POST['is_service'])) && $key == 'quantity')
			{				
				$this->Item_location->update_multiple(array('quantity' => empty($_POST['is_service']) ? $value : NULL), $items_to_update,$select_inventory);		
			}
			elseif($value!='' && $key =='item_cost_price_method' && $this->input->post('cost_price'))
			{
				if ($value == 'fixed')
				{
					$item_data["cost_price"]=$this->input->post('cost_price');
				}
				elseif($value == 'percent')
				{
					$cost_price_percent = (float)$this->input->post('cost_price');
				}
			}
			elseif($value!='' && $key =='item_unit_price_method' && $this->input->post('unit_price'))
			{
				if ($value == 'fixed')
				{
					$item_data["unit_price"]=$this->input->post('unit_price');
				}
				elseif($value == 'percent')
				{
					$unit_price_percent = (float)$this->input->post('unit_price');
				}
			}
			elseif($value!='' && $key =='item_promo_price_method' && $this->input->post('promo_price'))
			{
				if ($value == 'fixed')
				{
					$item_data["promo_price"]=$this->input->post('promo_price');
				}
				elseif($value == 'percent')
				{
					$promo_price_percent = (float)$this->input->post('promo_price');
					$promo_price_use_selling_price = $this->input->post('use_selling_price');
				}
			}
			elseif($value!='' and !(in_array($key, array('cost_price', 'unit_price','promo_price','item_cost_price_method','item_unit_price_method','item_promo_price_method','item_ids', 'tax_names', 'tax_percents', 'tax_cumulatives', 'select_inventory', 'commission_value', 'commission_type', 'commission_percent_type', 'override_default_commission','use_selling_price'))))
			{
				$item_data["$key"]=$value;
			}
		}
		
		//If we have any of the percents to update then we will update them (one or more)
		if ($cost_price_percent || $unit_price_percent || $promo_price_percent)
		{			
			$this->Item->update_multiple_percent($items_to_update,$select_inventory,$cost_price_percent, $unit_price_percent, $promo_price_percent, $promo_price_use_selling_price);
		}
		
		$this->Item->update_tiers($items_to_update,$select_inventory, $this->input->post('tier_types'), $this->input->post('tier_values'));
		
		if ($this->input->post('override_default_commission')!= '')
		{
			if ($this->input->post('override_default_commission') == 1)
			{
				if ($this->input->post('commission_type') == 'fixed')
				{
					$item_data['commission_fixed'] = (float)$this->input->post('commission_value');
					$item_data['commission_percent_type'] = '';
					$item_data['commission_percent'] = NULL;
				}
				else
				{
					$item_data['commission_percent'] = (float)$this->input->post('commission_value');
					$item_data['commission_percent_type'] = $this->input->post('commission_percent_type');
					$item_data['commission_fixed'] = NULL;
				}
			}
			else
			{
				$item_data['commission_percent'] = NULL;
				$item_data['commission_fixed'] = NULL;
				$item_data['commission_percent_type'] = '';
				
			}
		}
	
		//Item data could be empty if tax information is being updated
		if(empty($item_data) || $this->Item->update_multiple($item_data,$items_to_update,$select_inventory))
		{
			//Only update tax data of we are override taxes
			if (isset($item_data['override_default_tax']) && $item_data['override_default_tax'])
			{
				$items_taxes_data = array();
				$tax_names = $this->input->post('tax_names');
				$tax_percents = $this->input->post('tax_percents');
				$tax_cumulatives = $this->input->post('tax_cumulatives');

				for($k=0;$k<count($tax_percents);$k++)
				{
					if (is_numeric($tax_percents[$k]))
					{
						$items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
					}
				}

				if (!empty($items_taxes_data))
				{
					$this->Item_taxes->save_multiple($items_taxes_data, $items_to_update,$select_inventory);
				}
			}
						
			//Update all items with tags
			if ($this->input->post('tags'))
			{
				if ($select_inventory == 0)
				{
					foreach($items_to_update as $item_id)
					{
						$this->Tag->save_tags_for_item($item_id, $this->input->post('tags'));
					}
				}
				else
				{
					$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
					$total_items = $this->Item->count_all();
					$result = $this->Item->search(isset($params['search']) ? $params['search'] : '',isset($params['category_id']) ? $params['category_id'] : '',$total_items,0,'name','asc', isset($params['fields']) ? $params['fields']: 'all');
					
					foreach($result->result() as $item)
					{
						$this->Tag->save_tags_for_item($item->item_id, $this->input->post('tags'));
					}
				}
			}
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_bulk_edit')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_error_updating_multiple')));
		}
		
		$this->db->trans_complete();
	}

	function delete()
	{
		$this->check_action_permission('delete');		
		$items_to_delete=$this->input->post('ids');
		$select_inventory=$this->get_select_inventory();
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		$total_rows= $select_inventory ? $this->Item->search_count_all(isset($params['search']) ? $params['search'] : '',isset($params['category_id']) ? $params['category_id'] : '',$this->Item->count_all(),isset($params['fields']) ? $params['fields']: 'all') : count($items_to_delete);
		//clears the total inventory selection
		$this->clear_select_inventory();
		if($this->Item->delete_list($items_to_delete,$select_inventory))
		{
			$new_count = $this->Item->search_count_all(isset($params['search']) ? $params['search'] : '', isset($params['category_id']) ? $params['category_id'] : '',$this->Item->count_all(), isset($params['fields']));
			
			echo json_encode(array('success'=>true,'message'=>lang('items_successful_deleted').' '.
			$total_rows.' '.lang('items_one_or_multiple'), 'total_rows'=> $new_count));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('items_cannot_be_deleted')));
		}
	}

	function _excel_get_header_row()
	{
		$this->load->model('Tier');
		
		$header_row = array();
	
		$header_row[] = lang('common_item_number');
		$header_row[] = lang('common_product_id');
		$header_row[] = lang('common_item_name');
		$header_row[] = lang('common_category');
		$header_row[] = lang('common_supplier_id');
		$header_row[] = lang('common_cost_price');
		$header_row[] = lang('common_unit_price');
		
		if ($this->config->item('limit_manual_price_adj'))
		{
			$header_row[] = lang('common_min_edit_price');
			$header_row[] = lang('common_max_edit_price');
			$header_row[] = lang('common_max_discount_percent');
		}
		
		$header_row[] = lang('items_promo_price');
		$header_row[] = lang('items_promo_start_date');
		$header_row[] = lang('items_promo_end_date');
		
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$header_row[] =$tier->name;
		}
	
		$header_row[] = lang('items_price_includes_tax');
		$header_row[] = lang('items_is_service');
		$header_row[] = lang('items_quantity');
		$header_row[] = lang('items_reorder_level');
		$header_row[] = lang('common_replenish_level');
		$header_row[] = lang('common_description');
		$header_row[] = lang('items_allow_alt_desciption');
		$header_row[] = lang('items_is_serialized');
		$header_row[] = lang('common_size');
		$header_row[] = lang('reports_commission');
		$header_row[] = lang('items_commission_percent_based_on_profit');
		$header_row[] = lang('common_tax_class');
		$header_row[] = lang('common_tags');
		$header_row[] = lang('items_days_to_expiration');
		$header_row[] = lang('common_change_cost_price_during_sale');
		$header_row[] = lang('common_manufacturer');
		$header_row[] = lang('items_location_at_store');
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$header_row[] = lang('common_disable_loyalty');
		}
		
		if ($this->config->item('enable_ebt_payments'))
		{
			$header_row[] = lang('common_ebt');			
		}
		
		if($this->config->item("ecommerce_platform"))
		{
			$header_row[] = lang('items_is_ecommerce');
		}
		
		return $header_row;
	}
	
	function excel()
	{
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		$this->load->helper('spreadsheet');
		array_to_spreadsheet(array($header_row),'items_import.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}

	function excel_export() 
	{			
		$this->load->model('Tier');
		$this->load->model('Manufacturer');
		$this->load->model('Tax_class');
		$this->load->model('Additional_item_numbers');
		
		set_time_limit(0);
			
		$params = $this->session->userdata('item_search_data') ? $this->session->userdata('item_search_data') : array('offset' => 0, 'order_col' => 'name', 'order_dir' => 'asc', 'search' => FALSE, 'category_id' => FALSE, 'fields' => 'all');
		
		$search = $params['search'] ? $params['search'] : "";
		$category_id = $params['category_id'] ? $params['category_id'] : "";
		
		//Filter based on search
		if ($search || $category_id)
		{
			$data = $this->Item->search($search,$category_id,$this->Item->search_count_all($search, $category_id,10000, $params['fields']),0,$params['order_col'],$params['order_dir'], $params['fields'])->result_object();
		}
		else
		{
			$data = $this->Item->get_all($this->Item->count_all())->result_object();
		}
		
		$this->load->model('Tax_class');
		
		$tax_classes_indexed_by_id = $this->Tax_class->get_tax_classes_indexed_by_id();
		$tier_prices = $this->Item->get_all_tiers_prices();
		$this->load->helper('report');
		
		$header_row = $this->_excel_get_header_row();
		$header_row[] = lang('common_item_id');
		$rows[] = $header_row;
		
		$tiers = $this->Tier->get_all()->result();
		$categories = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_category_id();
		
		$manufacturers = array();
		
		foreach($this->Manufacturer->get_all() as $id => $row)
		{
		 	$manufacturers[$id] = $row['name'];

		}
		
		$additional_item_numbers = $this->Additional_item_numbers->get_all();
		
		foreach ($data as $r) 
		{
			if(isset($additional_item_numbers[$r->item_id]) && count($additional_item_numbers[$r->item_id]) > 0)
			{
				foreach($additional_item_numbers[$r->item_id] as $item_num)
				{
					if($r->item_number)
					{
						$r->item_number .= "|";
					}
					$r->item_number .= $item_num;
				}
				
			}
			
			$row = array();
			$row[] = $r->item_number;
			$row[] = $r->product_id;
			$row[] = $r->name;
			$row[] = isset($categories[$r->category_id]) ? $categories[$r->category_id] : '';
			$row[] = $r->supplier_id;
			$row[] = to_currency_no_money($r->cost_price, 10);
			$row[] = to_currency_no_money($r->unit_price);
			
			if ($this->config->item('limit_manual_price_adj'))
			{
				$row[] = $r->min_edit_price !== NULL ? to_currency_no_money($r->min_edit_price) : '';
				$row[] = $r->max_edit_price !== NULL ? to_currency_no_money($r->max_edit_price) : '';
				$row[] = $r->max_discount_percent !== NULL ? to_quantity($r->max_discount_percent,FALSE) : '';
			}
			
			$row[] = $r->promo_price!=0 ? to_currency_no_money($r->promo_price) : '';
			$row[] = $r->start_date ? date(get_date_format(), strtotime($r->start_date)) : '';
			$row[] = $r->end_date ? date(get_date_format(), strtotime($r->end_date)) : '';
			
			foreach($tiers as $tier)
			{
				$tier_id = $tier->id;
				$value = '';
				
				if (isset($tier_prices[$r->item_id][$tier->id]))
				{
					$percent_value = '';
					if ($this->config->item('default_tier_percent_type_for_excel_import') == 'cost_plus_percent')
					{
						if ( $tier_prices[$r->item_id][$tier->id]['cost_plus_percent'])
						{
							$percent_value = $tier_prices[$r->item_id][$tier->id]['cost_plus_percent'].'%';
						}
					}
					else
					{
						if ($tier_prices[$r->item_id][$tier->id]['percent_off'])
						{
							$percent_value = $tier_prices[$r->item_id][$tier->id]['percent_off'].'%';						
						}
					}
					
					$fixed_value ='';
					if ($this->config->item('default_tier_fixed_type_for_excel_import') == 'cost_plus_fixed_amount')
					{
						if ( $tier_prices[$r->item_id][$tier->id]['cost_plus_fixed_amount'])
						{
							$fixed_value = to_currency_no_money($tier_prices[$r->item_id][$tier->id]['cost_plus_fixed_amount']);
						}
					}
					else
					{
						if ( $tier_prices[$r->item_id][$tier->id]['unit_price'])
						{
							$fixed_value = to_currency_no_money($tier_prices[$r->item_id][$tier->id]['unit_price']);
						}
					}
					$value = $fixed_value !== '' ? $fixed_value : $percent_value;
				}

				$row[] = $value;
			}
			
			
			$row[] = $r->tax_included ? 'y' : '';
			$row[] = $r->is_service ? 'y' : '';
			$row[] = to_quantity($r->quantity, FALSE);
			$row[] = to_quantity($r->reorder_level, fALSE);
			$row[] = to_quantity($r->replenish_level, fALSE);
			$row[] = $r->description;
			$row[] = $r->allow_alt_description ? 'y' : '';
			$row[] = $r->is_serialized ? 'y' : '';
			$row[] = $r->size;
			$commission = '';
			
			if ($r->commission_fixed)
			{
				$commission = to_currency_no_money($r->commission_fixed);
			}
			elseif($r->commission_percent)
			{
				$commission = to_currency_no_money($r->commission_percent).'%';
			}
			
			$row[] = $commission;
			$row[] = $r->commission_percent_type == 'profit' ? 'y':'';
			$row[] = isset($tax_classes_indexed_by_id[$r->tax_class_id]) ? $tax_classes_indexed_by_id[$r->tax_class_id] : '';
			$row[] = $r->tags;
			$row[] = $r->expire_days ? $r->expire_days : '';
			$row[] = $r->change_cost_price ? 'y' : '';
			$row[] = isset($manufacturers[$r->manufacturer_id]) ? $manufacturers[$r->manufacturer_id] : '';
			$row[] = $r->location;
			if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
			{
				$row[] = $r->disable_loyalty ? 'y' : '';				
			}
			
			if ($this->config->item('enable_ebt_payments'))
			{
				$row[] = $r->is_ebt_item ? 'y' : '';				
			}
			
			if($this->config->item("ecommerce_platform"))
			{
				$row[] = $r->is_ecommerce ? 'y' : '';
			}
			
			$row[] = $r->item_id;
					
			$rows[] = $row;
		}
		$this->load->helper('spreadsheet');
		array_to_spreadsheet($rows,'items_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}

	function excel_import()
	{
		$this->check_action_permission('add_update');
		$this->load->view("items/excel_import", null);
	}
	
	function do_excel_upload()
	{
		$this->load->helper('demo');
		
		//Write to app files
 	 	$this->load->model('Appfile');
    $app_file_file_id = $this->Appfile->save($_FILES["file"]["name"], file_get_contents($_FILES["file"]["tmp_name"]),'+3 hours');
		//Store file_id from app files in session so we can reference later
		$this->session->set_userdata("excel_import_file_id",$app_file_file_id);
		
		$file_info = pathinfo($_FILES["file"]["name"]);		
		$file = $this->Appfile->get($this->session->userdata('excel_import_file_id'));
		$tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'iexcel');
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
				echo json_encode(array('success'=>false,'message'=>lang('common_all_spreadsheet_columns_must_have_labels')));
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
		
		$this->session->set_userdata("items_excel_import_column_map", $columns);
		echo json_encode(array('success'=>true,'message'=>lang('common_import_successful')));
	}
	
	function do_excel_import_map()
	{
		$this->load->helper('text');
 	 	$this->load->model('Appfile');
		
		$file = $this->Appfile->get($this->session->userdata('excel_import_file_id'));

		$tmpFilename = tempnam(ini_get('upload_tmp_dir'), 'iexcel');
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
	  		$col_data[] = clean_string(trim($sheet->getCellByColumnAndRow($k,$i)));
			}

			$column["data"] = $col_data;

			$columns[] = $column;
			$k++;
		}
		
		$this->session->set_userdata("items_excel_import_num_rows", $numRows);
		$this->session->set_userdata("items_excel_import_column_map", $columns);
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

		$fields[] = array('Name' => lang('common_item_number'), 'key' => 'item_number');
		$fields[] = array('Name' => lang('common_product_id'), 'key' => 'product_id');
		$fields[] = array('Name' => lang('common_item_name'), 'key' => 'name');
		$fields[] = array('Name' => lang('common_category'), 'key' => 'category_id');
		$fields[] = array('Name' => lang('common_supplier_id'), 'key' => 'supplier_id');
		$fields[] = array('Name' => lang('common_cost_price'), 'key' => 'cost_price');
		$fields[] = array('Name' => lang('common_unit_price'), 'key' => 'unit_price');
		$fields[] = array('Name' => lang('common_min_edit_price'), 'key' => 'min_edit_price');
		$fields[] = array('Name' => lang('common_max_edit_price'), 'key' => 'max_edit_price');
		$fields[] = array('Name' => lang('common_max_discount_percent'), 'key' => 'max_discount_percent');
		$fields[] = array('Name' => lang('items_promo_price'), 'key' => 'promo_price');
		$fields[] = array('Name' => lang('items_promo_start_date'), 'key' => 'start_date');
		$fields[] = array('Name' => lang('items_promo_end_date'), 'key' => 'end_date');
		
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$fields[] = array('Name' => $tier->name, 'key' => 'tier');
		}
		
		$fields[] = array('Name' => lang('items_price_includes_tax'), 'key' => 'tax_included');
		$fields[] = array('Name' => lang('items_is_service'), 'key' => 'is_service');
		$fields[] = array('Name' => lang('items_quantity'), 'key' => 'quantity');
		$fields[] = array('Name' => lang('items_reorder_level'), 'key' => 'reorder_level');
		$fields[] = array('Name' => lang('common_replenish_level'), 'key' => 'replenish_level');
		$fields[] = array('Name' => lang('common_description'), 'key' => 'description');
		$fields[] = array('Name' => lang('items_allow_alt_desciption'), 'key' => 'allow_alt_description');
		$fields[] = array('Name' => lang('items_is_serialized'), 'key' => 'is_serialized');
		$fields[] = array('Name' => lang('common_size'), 'key' => 'size');
		$fields[] = array('Name' => lang('reports_commission'), 'key' => 'commission');
		$fields[] = array('Name' => lang('items_commission_percent_based_on_profit'), 'key' => 'commission_percent_type');
		$fields[] = array('Name' => lang('common_tax_class'), 'key' => 'tax_class_id');
		$fields[] = array('Name' => lang('common_tags'), 'key' => 'tags');
		$fields[] = array('Name' => lang('items_days_to_expiration'), 'key' => 'expire_days');
		$fields[] = array('Name' => lang('common_change_cost_price_during_sale'), 'key' => 'change_cost_price');
		$fields[] = array('Name' => lang('common_manufacturer'), 'key' => 'manufacturer_id');
		$fields[] = array('Name' => lang('items_location_at_store'), 'key' => 'location');
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$fields[] = array('Name' => lang('common_disable_loyalty'), 'key' => 'disable_loyalty');
		}
		
		if ($this->config->item('enable_ebt_payments'))
		{
			$fields[] = array('Name' => lang('common_ebt'), 'key' => 'is_ebt_item');
		}
		
		if($this->config->item("ecommerce_platform"))
		{
			$fields[] = array('Name' => lang('items_is_ecommerce'), 'key' => 'is_ecommerce');
		}
		
		$fields[] = array('Name' => lang('common_item_id'), 'key' => 'item_id');
		
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
		$data = $this->session->userdata("items_excel_import_column_map");
		
		foreach($data as &$col)
		{
			unset($col["data"]);
		}
		
		echo json_encode($data);
	}
	
	public function set_excel_columns_map()
	{	
		$data = $this->session->userdata("items_excel_import_column_map");
		
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
		
		$this->session->set_userdata("items_excel_import_column_map", $data);
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
		$this->session->set_userdata('items_excel_import_error_log', NULL);
		$columns_with_data = $this->session->userdata("items_excel_import_column_map");
		
		$fieldId_to_colIndex = array_flip(array_map(array($this, '_indexColumnArray'), $columns_with_data));
		unset($fieldId_to_colIndex['N/A']);
		unset($fieldId_to_colIndex[-1]);
		
		$item_number_dups = array();
		if (isset($fieldId_to_colIndex[0]))
		{
			$item_number_index = $fieldId_to_colIndex[0];
			$item_numbers = $columns_with_data[$item_number_index]['data'] ? $columns_with_data[$item_number_index]['data'] : array();
			
			$all_item_numbers = array();
			
			foreach($item_numbers as $item_number)
			{
				$all_item_numbers = array_merge($all_item_numbers, explode('|', $item_number));
			}
			
			$item_number_dups = $this->_get_keys_for_duplicate_values($all_item_numbers);

			foreach($item_number_dups as $key => $val)
			{
				foreach($val as $v)
				{
					$row = $v+2;
					$message = lang('items_duplicate_item_number').' "'. $key .'" ' .lang('items_in_spreadsheet');
					$this->_log_validation_error($row, $message, 'Error');
				}
			}
		}
		
		$product_id_dups = array();
		
		if (isset($fieldId_to_colIndex[1]))
		{
			$product_id_index = $fieldId_to_colIndex[1];
			$product_ids = $columns_with_data[$product_id_index]['data'] ? $columns_with_data[$product_id_index]['data'] : array();
			$product_id_dups = $this->_get_keys_for_duplicate_values($product_ids);
				
		
			foreach($product_id_dups as $key => $val)
			{
				foreach($val as $v)
				{
					$row = $v+2;
					$message = lang('items_duplicate_product_id').' "'. $key .'" ' .lang('items_in_spreadsheet');
					$this->_log_validation_error($row, $message, 'Error');
				}
			}
		}		
		if(count($item_number_dups) > 0 || count($product_id_dups) > 0)
		{
			echo json_encode(array('type'=> 'error','message'=>lang('items_duplicate_item_numbers_product_ids'), 'title' =>  lang('common_error')));
		} else {
			echo json_encode(array('type'=> 'success','message'=>lang('items_no_duplicate_item_numbers_product_ids'), 'title' =>  lang('common_success')));
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
		
		$this->session->set_userdata('items_excel_import_error_log', NULL);
		
		$numRows = $this->session->userdata("items_excel_import_num_rows");
		$columns_with_data = $this->session->userdata("items_excel_import_column_map");
		$current_location_id= $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->load->model('Tier');
		$this->load->model('Item_taxes');
		$this->load->model('Item_location');
		$this->load->model('Supplier');
		$this->load->model('Manufacturer');
		
		$fields = $this->_get_database_fields_for_import_as_array();
		
		$tiers = $this->Tier->get_all()->result_array();
		
		$this->categories_indexed_by_name = $this->Category->get_all_categories_and_sub_categories_as_indexed_by_name_key();
		
		$this->manufacturers_map = array();
		
		foreach($this->Manufacturer->get_all() as $id => $row)
		{
		 	$this->manufacturers_map[strtoupper($row['name'])] = $id;
		}
				
		$fieldId_to_colIndex = array_flip(array_map(array($this, '_indexColumnArray'), $columns_with_data));
		unset($fieldId_to_colIndex['N/A']);
		
		$can_commit = TRUE;
		$this->db->trans_begin();
		
		for ($i = 0; $i < $numRows -1; $i++)
		{
			$item_id = NULL;
			$item_data = array();
			$tier_datas = array();
			
			foreach($fields as $field)
			{
				if(array_key_exists($field['Id'], $fieldId_to_colIndex))
				{
					$key = $fieldId_to_colIndex[$field['Id']];
				}
				else
				{
					continue;
				}
				
				if($field['key'] !== "")
				{	
					if($field['key'] == 'commission')
					{
						
						if (strpos($columns_with_data[$key]['data'][$i], '%') === FALSE)
						{
							$field['key'] = 'commission_fixed';
						}
						else
						{
							$field['key'] = 'commission_percent';
						}
						
						//hotfix for data
						if($columns_with_data[$key]['data'][$i] === '')
						{
							$columns_with_data[$key]['data'][$i] = NULL;
						}
						
						
					}
					
					if($field['key'] == 'item_id')
					{
						$item_id = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i]);
					} 
					elseif($field['key'] == 'quantity')
					{
						$quantity = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i]);
					}
					elseif($field['key'] == 'location')
					{
						$location_at_store = $columns_with_data[$key]['data'][$i];
					}
					elseif($field['key'] == 'tier')
					{
						$tier_data = array();
						$cols = array_column($tiers, 'name');
						$tier_data['tier_id'] = $tiers[array_search($field['Name'], $cols)]['id'];
						
						$tier_value = $columns_with_data[$key]['data'][$i];
						
						if ($tier_value)
						{
							if (strpos($tier_value, '%') === FALSE)
							{
								
									
								if ($this->config->item('default_tier_fixed_type_for_excel_import') == 'cost_plus_fixed_amount')
								{
									$tier_data['unit_price'] = NULL;									
									$tier_data['cost_plus_fixed_amount'] =  $this->_clean('cost_plus_fixed_amount',$tier_value);
								}
								else
								{
									$tier_data['unit_price'] = $this->_clean('unit_price',$tier_value);									
									$tier_data['cost_plus_fixed_amount'] = NULL;
								}
									
								$tier_data['percent_off'] = NULL;
								$tier_data['cost_plus_percent'] = NULL;
								
							}
							else
							{
								$tier_data['unit_price'] = NULL;
								$tier_data['cost_plus_fixed_amount'] = NULL;
								
								if ($this->config->item('default_tier_percent_type_for_excel_import') == 'cost_plus_percent')
								{
									$tier_data['cost_plus_percent'] = $this->_clean('cost_plus_percent', $tier_value);
									$tier_data['percent_off'] = NULL;
								}
								else
								{
									$tier_data['percent_off'] =  $this->_clean('percent_off', $tier_value);
									$tier_data['cost_plus_percent'] = NULL;
								}
							}
						}
						$tier_datas[] = $tier_data;
					}
					elseif($field['key'] == 'tags')
					{
						$tags = $this->_clean($field['key'], $columns_with_data[$key]['data'][$i]);
					}
					elseif($field['key'] == 'item_number')
					{
						$item_numbers = explode('|', $columns_with_data[$key]['data'][$i], 2);
						
						$item_data[$field['key']]  = $this->_clean($field['key'], $item_numbers[0]);
							
						if(isset($item_numbers[1]))
						{
							$additional_item_numbers = explode('|', $item_numbers[1]);
						}
						
					}
					else 
					{
						$item_data[$field['key']] =  $this->_clean($field['key'], $columns_with_data[$key]['data'][$i]);
					}
				}
			}
			
			$item_data['deleted'] = 0;
			
			
			if(!isset($item_data['commission_fixed']) && !isset($item_data['commission_percent']))
			{
				$item_data['commission_fixed'] = NULL;
				$item_data['commission_percent'] = NULL;
			}
			
			if(isset($item_data['commission_fixed']))
			{
				$item_data['commission_percent'] = NULL;
			}
			
			if(isset($item_data['commission_percent']))
			{
				$item_data['commission_fixed'] = NULL;
			}

			if (isset($item_data['is_service']) && $item_data['is_service'])
			{
				$quantity = NULL;
			}
			
			if (isset($item_data['tax_class_id']) && $item_data['tax_class_id'])
			{
				$item_data['override_default_tax'] = 1;
			}
			else
			{
				$item_data['override_default_tax'] = 0;				
			}
			
			
			//Item must have a name to save
			if($item_data['name'] && !$this->Item->save($item_data, $item_id))
			{
				if($item_id === NULL)
				{					
					if(!isset($item_data['item_number']) || !$item_id = $this->Item->get_item_id($item_data['item_number']))
					{			
						if(!isset($item_data['product_id']) || !$item_id = $this->Item->get_item_id($item_data['product_id']))
						{
							//couldnt find Item id to make second attempt
							$this->_logDbError($i+2);
							$can_commit = FALSE;
							continue;
						}
					}
										
					$item_data['deleted'] = 0;
					//second attempt
					if($this->Item->save($item_data, $item_id))
					{
						//second attempt Succeeded
						$this->_log_validation_error($i+2, lang('items_item_existed_warning'));
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
				{ //first attempt failed even with item id
					$this->_logDbError($i+2);
					$can_commit = FALSE;
					continue;
				}	
				
			}
		
			$item_id = isset($item_data['item_id']) ? $item_data['item_id'] :  $item_id;
			
			if(isset($tags))
			{
				if(!$this->Tag->save_tags_for_item(isset($item_data['item_id']) ? $item_data['item_id'] :  $item_id, $tags))
				{
					$this->_logDbError($i+2);
				}
			}
			
			foreach($tier_datas as $tier_data)
			{
				$tier_data['item_id'] = $item_id;
				
				if(array_key_exists("unit_price", $tier_data) || array_key_exists("cost_plus_percent", $tier_data) || array_key_exists("percent_off", $tier_data))
				{
					if(!$this->Item->save_item_tiers($tier_data, $tier_data['item_id']))
					{
						$this->_logDbError($i+2);
					}
				}
				else 
				{
					if(!$this->Item->delete_tier_price($tier_data['tier_id'], $tier_data['item_id']))
					{
						$this->_logDbError($i+2);
					}
				}
			}
			
			if($item_id && isset($additional_item_numbers))
			{
				if(!$this->Additional_item_numbers->save($item_id, $additional_item_numbers))
				{
					$this->_logDbError($i+2);
				}
			}
			
			if (isset($location_at_store))
			{
				$this->Item_location->save(array('location' => $location_at_store), $item_id,$current_location_id);
			}
			
			$item_location_before_save = $this->Item_location->get_info($item_id,$this->Employee->get_logged_in_employee_current_location_id());
			
			if ($item_id && (isset($quantity) && !empty($quantity) && ($quantity !== '0')) || (isset($item_data['is_service']) && $item_data['is_service']))
			{
				if (!$this->Item_location->save_quantity($quantity, $item_id))
				{
					$this->_logDbError($i+2);
					$can_commit = FALSE;
					continue;
				}
				
				$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
				$emp_info=$this->Employee->get_info($employee_id);
				$comment =lang('items_csv_import');
			
				//Only log inventory if quantity changes
				if (!$item_data['is_service'] && $quantity != (float)$item_location_before_save->quantity)
				{
					$inv_data = array
					(
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>isset($item_data['item_id']) ? $item_data['item_id'] :  $item_id,
						'trans_user'=>$employee_id,
						'trans_comment'=>$comment,
						'trans_inventory'=>$quantity - (float)$item_location_before_save->quantity,
						'location_id'=>$this->Employee->get_logged_in_employee_current_location_id()
					);
					if(!$this->Inventory->insert($inv_data))
					{
						//ERROR updating quantity
						$this->_logDbError($i+2);
						$can_commit = FALSE;
						continue;
					}
				}
			}
			
			$tax_class_id = NULL;
			if(array_key_exists('tax_class_id', $item_data))
			{
				$tax_class_id = $item_data['tax_class_id'];
			}
			
			if ($tax_class_id)
			{
				if(!$this->Item_taxes->delete($item_id))
				{
					$this->_logDbError($i+2);
					$can_commit = FALSE;
					continue;
				}
			}
		} //loop done for items
		
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
			echo json_encode(array('type'=> 'error','message'=> lang('common_errors_occured_durring_import'), 'title' => lang('common_error')));
		}
		elseif ($this->db->trans_status() === FALSE && $can_commit)
		{	
			echo json_encode(array('type'=> 'warning','message'=> lang('common_warnings_occured_durring_import'), 'title' => lang('common_warning')));
		}
		else
		{
			//Clear out session data used for import
			$this->session->unset_userdata('excel_import_file_id');
			$this->session->unset_userdata('items_excel_import_column_map');
			$this->session->unset_userdata('items_excel_import_num_rows');
			
			echo json_encode(array('type'=> 'success','message'=>lang('common_import_successful'), 'title' =>  lang('common_success')));			
		}
	}
	
	private function _clean($key, $value, $row = NULL)
	{	//$row added for logging warnings if we decide to
		
		if ($key == 'item_number'){
			if($value === '')
			{
				 return NULL;
			}
			return $value;
		}
		if ($key == 'product_id'){
			if($value === '')
			{
				 return NULL;
			}
			return $value;
		}
		if ($key == 'name')
		{
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'category_id')
		{
			if($value)
			{	
				
				//remove false or empty values
				$category_list = explode('|', $value);
				$category_list = array_values(array_filter($category_list, function($value) { return $value !== ''; }));
				$value = implode("|", $category_list);
				
				if (!isset($this->categories_indexed_by_name[strtoupper($value)]))
				{
					$this->Category->create_categories_as_needed($value, $this->categories_indexed_by_name);
				}
			
				return $this->categories_indexed_by_name[strtoupper($value)];
			}
			
			return NULL;
		}
		if ($key == 'supplier_id'){
			if ($value)
			{
				$supplier_name_before_searching = $value;
				$value = $this->Supplier->exists($value) ? $value : $this->Supplier->find_supplier_id($value);

				if (!$value)
				{
					$person_data = array('first_name' => '', 'last_name' => '');
					$supplier_data = array('company_name' => $supplier_name_before_searching);
					$this->Supplier->save_supplier($person_data, $supplier_data);
					$value = $supplier_data['person_id'];
				}
				return $value;
				
			}
			
			return NULL;
		}
		if ($key == 'cost_price'){
			return make_currency_no_money($value);
		}
		if ($key == 'unit_price'){
			return make_currency_no_money($value);
		}
		if ($key == 'min_edit_price') {
			
			if ($value !== "")
			{
				return make_currency_no_money($value);
			}
			return NULL;
		}
		if ($key == 'max_edit_price') {
			
			if ($value !== "")
			{
				return make_currency_no_money($value);
			}
			return NULL;
		}
		if ($key == 'max_discount_percent') {
			
			if ($value !== "")
			{
				return floatval($value);
			}
			return NULL;
		}
		if ($key == 'promo_price'){
			
			if ($value!=='')
			{
				return make_currency_no_money($value);
			}
			return NULL;
		}
		if ($key == 'start_date'){
			if($value)
			{
				return date('Y-m-d',strtotime($value));
			}
			return NULL;
		}
		if ($key == 'end_date'){
			if($value)
			{
				return date('Y-m-d',strtotime($value));
			}
			return NULL;
		}
		if ($key == 'tax_included') {
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
		
			return 0;
		}
		if ($key == 'is_service') {
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
		}
		if ($key == 'reorder_level' || $key == 'replenish_level'){
			if(is_numeric($value))
			{
				return $value;
			}
			return NULL;
		}
		if ($key == 'description'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'allow_alt_description'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
		}
		if ($key == 'is_serialized'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
		}
		if ($key == 'size'){
			if(!$value)
			{
				 return '';
			}
			return $value;
		}
		if ($key == 'commission_fixed'){
			
			if($value === '' || $value === NULL)
			{
				return NULL;  
			}
				
			return make_currency_no_money($value);
		}
		if ($key == 'commission_percent'){
			
			if($value === '' || $value === NULL)
			{
				return NULL;  
			}
			
			return strval((float) $value);
		
		}
		if ($key == 'commission_percent_type')
		{
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 'profit';
			}
			
			return 'selling_price';
		}
		if ($key == 'tax_class_id'){
			if ($value)
			{
				$this->load->model('Tax_class');
				
				$value = $this->Tax_class->exists($value) ? $value : $this->Tax_class->find_tax_class_id($value);
				return $value;
			}
			
			return NULL;
			
		}
		if ($key == 'expire_days'){
			
			if($value !='' && $value == (int) $value)
			{
				return (int)$value;
			}
			
			return null;
		}
		if ($key == 'change_cost_price'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
		}
		if ($key == 'manufacturer_id')
		{
			if ($value)
			{
				$manufacturer_id = NULL;
				
				if (isset($this->manufacturers_map[strtoupper($value)]))
				{
					$manufacturer_id = $this->manufacturers_map[strtoupper($value)];
				}	
				else
				{
					$manufacturer_id = $this->Manufacturer->save($value);
					$this->manufacturers_map[strtoupper($value)] = $manufacturer_id;
				}
				return $manufacturer_id;
			}	
		}
		if ($key == 'disable_loyalty'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
			
		}
		if ($key == 'is_ebt_item'){
			$true_values = array("true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;

		}
		if ($key == 'item_id'){
			if($value == NULL)
			{
				return NULL;
			}
			if($value == (int) $value)
			{
				return strval((int) $value);
			}
			return NULL;
		}
		if ($key == 'quantity'){
			if(is_numeric($value))
			{
				return $value;
			}
			return '';
		}
		if ($key == 'unit_price'){
			return make_currency_no_money($value);
		}
		
		if ($key == 'cost_plus_fixed_amount')
		{
			return make_currency_no_money($value);			
		}
		
		if ($key == 'cost_plus_percent'){
			if(is_numeric($value))
			{
				return strval((float) $value);
			}
			return NULL;
		}
		if ($key == 'percent_off'){
			if($value == (float) $value)
			{
				return strval((float) $value);
			}
			return NULL;
		}
		if ($key == 'tags'){
			if($value)
			{
				return $value;
			}
			return '';
		}
		if($key == 'quantity')
		{
			if($value == (float) $value)
			{
				return strval((float) $value);
			}
			return 0;
		}
		if($key == 'is_ecommerce')
		{
			$true_values = array("","true", "yes", "y", "1");
			if (in_array(strtolower($value), $true_values)) {
			    return 1;
			}
			
			return 0;
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
		if(!$log = $this->session->userdata('items_excel_import_error_log'))
		{
			$log = array();
		}
		
		$log[] = array("row" => $row, "message" => $message, "type" => $type);
		
		$this->session->set_userdata('items_excel_import_error_log', $log);
	}
	
	public function get_import_errors()
	{
		echo json_encode($this->session->userdata('items_excel_import_error_log'));
	}
	
	function cleanup()
	{
		$this->Item->cleanup();
		echo json_encode(array('success'=>true,'message'=>lang('items_cleanup_sucessful')));
	}
	
	
	function select_inventory() 
	{
		$this->session->set_userdata('select_inventory', 1);
	}
	
	function get_select_inventory() 
	{
		return $this->session->userdata('select_inventory') ? $this->session->userdata('select_inventory') : 0;
	}

	function clear_select_inventory() 	
	{
		$this->session->unset_userdata('select_inventory');
		
	}
	
	function tags()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Tag->get_tag_suggestions($this->input->get('term'),25);
		echo json_encode($suggestions);
	}
	
	function count($status = 'open', $offset = 0)
	{
		$this->check_action_permission('count_inventory');
		$data = array();
		$config = array();
		$config['base_url'] = site_url("items/count/$status");
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['total_rows'] = $this->Inventory->get_count_by_status($status);
		$config['uri_segment'] = 4;
		$data['per_page'] = $config['per_page'];
	

		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
	
		$counts = $this->Inventory->get_counts_by_status($status,$config['per_page'], $offset)->result_array();
		
		$data['counts'] = $counts;
		$data['status'] = $status;
		$this->load->view('items/count', $data);
	}
	
	function new_count()
	{
		$this->check_action_permission('count_inventory');
		$count_id = $this->Inventory->create_count();
	   redirect('items/do_count/'.$count_id);
	}
	
	function do_count($count_id, $offset = 0)
	{
		$this->check_action_permission('count_inventory');		
		$this->session->set_userdata('current_count_id',$count_id);
		
		$data = array();
		$config = array();
		$config['base_url'] = site_url("items/do_count/$count_id");
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20;
		$config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
		$config['uri_segment'] = 4;
		$data['per_page'] = $config['per_page'];
	

		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$data['count_info'] = $this->Inventory->get_count_info($count_id);
		
		$data['items_counted'] = $this->Inventory->get_items_counted($count_id,$config['per_page'], $offset);
		$data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
		$data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add') ); 
			
		$this->load->view('items/do_count', $data);
	}
	
	function add_item_to_inventory_count()
	{
		$this->check_action_permission('count_inventory');
		$this->load->model('Item_location');
		
		$item = $this->input->post('item');
		$count_id = $this->session->userdata('current_count_id');	
		$mode = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
		
		$data = array();
		
		if ($item && $count_id)
		{
			$count_info = $this->Inventory->get_count_info($count_id);
			if(!$this->Item->exists(does_contain_only_digits($item) ? (int)$item : -1))	
			{
				//try to get item id given an item_number
				$item = $this->Item->get_item_id($item);
			}
			
			if ($item)
			{
					$current_count = $this->Inventory->get_count_item_current_quantity($count_id, $item);
					$actual_quantity = $this->Inventory->get_count_item_actual_quantity($count_id, $item);
				
					if ($actual_quantity !== NULL)
					{
						$current_inventory_value = $actual_quantity;
					}
					else
					{
						$current_inventory_value = $this->Item_location->get_location_quantity($item,$count_info->location_id);
					}
				
					if ($mode == 'scan_and_add')
					{	
						$this->Inventory->set_count_item($count_id, $item, $current_count + 1, $current_inventory_value);
					}
					else
					{
						$this->Inventory->set_count_item($count_id, $item, $current_count, $current_inventory_value);
					}
			} else {
				$data['error'] = true;
			} 
		}
		
		$this->_reload_inventory_counts($data);
	}
	
	function edit_count()
	{
		$this->check_action_permission('count_inventory');
		$name = $this->input->post('name');
		$count_id = $this->input->post('pk');
		$$name = $this->input->post('value');
		
		$this->Inventory->set_count($count_id, isset($status) ? $status : FALSE, isset($comment) ? $comment : FALSE);
	}
	
	function excel_import_count()
	{		
		$this->check_action_permission('count_inventory');
		$this->load->view("items/excel_import_count", null);	
	}
	
	function _excel_get_header_row_count()
	{
		return array(lang('common_item_id').'/'.lang('common_item_number').'/'.lang('common_product_id'),lang('items_count'));
	}
	
	function excel_count()
	{
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row_count();
		$this->load->helper('spreadsheet');
		array_to_spreadsheet(array($header_row),'items_count.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}
	
	
	function do_excel_import_count()
	{
		$this->check_action_permission('count_inventory');
		$this->load->model('Item_location');
		 
		$count_id = $this->session->userdata('current_count_id');	
		$this->load->helper('demo');

		$file_info = pathinfo($_FILES['file_path']['name']);
		if($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv')
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
			return;
		}
		
		
		set_time_limit(0);
		$this->db->trans_start();
		$msg = 'do_excel_import';
		
		//$category_map = array();
		//$failCodes = array();
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = lang('common_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{	
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				$this->load->helper('spreadsheet');
				$file_info = pathinfo($_FILES['file_path']['name']);
				$sheet = file_to_spreadsheet($_FILES['file_path']['tmp_name'],$file_info['extension']);
				$num_rows = $sheet->getNumberOfRows();
				
				//Loop through rows, skip header row
				for($k = 2;$k<=$num_rows; $k++)
				{
					$item_id = $sheet->getCellByColumnAndRow(0, $k);
					if (!$item_id)
					{
						continue;
					}
									
					$quantity = $sheet->getCellByColumnAndRow(1, $k);
					if (!$quantity)
					{
						continue;
					}
				
					if ($item_id && $quantity)
					{
						if(!$this->Item->exists(does_contain_only_digits($item_id) ? (int)$item_id : -1))	
						{
							//try to get item id given an item_number
							$item_id = $this->Item->get_item_id($item_id);
						}
			
						if ($item_id)
						{
							$count_info = $this->Inventory->get_count_info($count_id);
							
							$current_inventory_value = $this->Item_location->get_location_quantity($item_id,$count_info->location_id);
							$this->Inventory->set_count_item($count_id, $item_id, $quantity, $current_inventory_value);
						}
					}
					
				}
				
				$this->db->trans_complete();
				echo json_encode(array('success'=>true,'message'=>lang('common_import_successful')));
				
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
				return;
			}
		}
	}
	
	function count_import_success()
	{
		$count_id = $this->session->userdata('current_count_id');	
		redirect('items/do_count/'.$count_id);
	}
	
	function finish_count($update_inventory = 0)
	{
		$this->check_action_permission('count_inventory');
		
		$count_id = $this->session->userdata('current_count_id');	
		
		if ($update_inventory && $this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id))
		{	
			$this->Inventory->update_inventory_from_count($count_id);
		}
		
		$this->Inventory->set_count($count_id, 'closed');
	   redirect('items/count');
	}
	
	function edit_count_item()
	{
		$this->check_action_permission('count_inventory');
		$this->load->model('Item_location');
		$name = $this->input->post('name');
		$item_id = $this->input->post('pk');
		$$name = $this->input->post('value');
		$count_id = $this->session->userdata('current_count_id');
		
		$current_count = $this->Inventory->get_count_item_current_quantity($count_id, $item_id);
		$actual_quantity = $this->Inventory->get_count_item_actual_quantity($count_id, $item_id);
	
		if ($actual_quantity !== NULL)
		{
			$current_inventory_value = $actual_quantity;
		}
		else
		{
			$count_info = $this->Inventory->get_count_info($count_id);
			$current_inventory_value = $this->Item_location->get_location_quantity($item_id,$count_info->location_id);
		}
	
		$this->Inventory->set_count_item($count_id, $item_id, isset($quantity) ? $quantity : $current_count, $current_inventory_value,isset($comment) ? $comment : FALSE);		
		$this->_reload_inventory_counts();
	}
	
	function delete_inventory_count_item($item_id)
	{
		$this->check_action_permission('count_inventory');
		
		$count_id = $this->session->userdata('current_count_id');
		$this->Inventory->delete_count_item($count_id, $item_id);
	   redirect('items/do_count/'.$count_id);		
	}
	
	function delete_inventory_count($count_id, $go_back_to_status = 'open')
	{
		$this->check_action_permission('count_inventory');
		
		$this->Inventory->delete_inventory_count($count_id);
	   redirect("items/count/$go_back_to_status");		
	}
		
	function reload_inventory_counts()
	{	
		$this->check_action_permission('count_inventory');
			
		$this->_reload_inventory_counts();
	}
	
	function change_count_mode()
	{
		$this->check_action_permission('count_inventory');
		
		$this->session->set_userdata('count_mode', $this->input->post('mode'));
			
		$this->_reload_inventory_counts();
	}
	
	function _reload_inventory_counts($data = array())
	{
		$this->check_action_permission('count_inventory');
		
		$count_id = $this->session->userdata('current_count_id');
		$config = array();
		
		$config['base_url'] = site_url("items/do_count/$count_id");
		$config['per_page'] = $this->config->item('number_of_items_per_page') ? (int)$this->config->item('number_of_items_per_page') : 20; 
		$config['total_rows'] = $this->Inventory->get_number_of_items_counted($count_id);
		$config['uri_segment'] = 4;
		$data['per_page'] = $config['per_page'];		
		$data['count_info'] = $this->Inventory->get_count_info($count_id);

		$data['total_rows'] = $config['total_rows'];
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		$data['items_counted'] = $this->Inventory->get_items_counted($count_id,	$config['per_page']);
		
		$data['mode'] = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
		$data['modes'] = array('scan_and_set' => lang('items_scan_and_set'), 'scan_and_add' => lang('items_scan_and_add') ); 
		
		$this->load->view("items/do_count_data",$data);
	}
	
	function save_column_prefs()
	{
		$this->load->model('Employee_appconfig');
		
		if ($this->input->post('columns'))
		{
			$this->Employee_appconfig->save('item_column_prefs',serialize($this->input->post('columns')));
		}
		else
		{
			$this->Employee_appconfig->delete('item_column_prefs');			
		}
	}
}
?>
