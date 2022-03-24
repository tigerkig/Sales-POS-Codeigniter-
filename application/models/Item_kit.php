<?php
class Item_kit extends CI_Model
{
	/*
	Determines if a given item_id is an item kit
	*/
	function exists($item_kit_id)
	{
		$this->db->from('item_kits');
		$this->db->where('item_kit_id',$item_kit_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*
	Returns all the item kits
	*/
	function get_all($limit=10000, $offset=0,$col='name',$ord='asc')
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();

		$this->db->select('item_kits.*, categories.name as category,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
		$this->db->from('item_kits');
		$this->db->join('categories', 'categories.id = item_kits.category_id','left');
		$this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = '.$current_location, 'left');
		$this->db->where('item_kits.deleted',0);

		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $ord);
		}
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}

	function count_all()
	{
		$this->db->from('item_kits');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}

	/*
	Gets information about a particular item kit
	*/
	function get_info($item_kit_id, $can_cache = TRUE)
	{
		if ($can_cache)
		{
			static $cache  = array();
		}		
		else
		{
			$cache = array();
		}
		
		if (is_array($item_kit_id))
		{
			$item_kits = $this->get_multiple_info($item_kit_id)->result();
			
			foreach($item_kits as $item_kit)
			{
				$cache[$item_kit->item_kit_id] = $item_kit;
			}
			
			return $item_kits;
		}
		else
		{
			if (isset($cache[$item_kit_id]))
			{
				return $cache[$item_kit_id];
			}
		}
		
		//If we are NOT an int return empty item
		if (!is_numeric($item_kit_id))
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('item_kits');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}

		//KIT #
		$pieces = explode(' ',$item_kit_id);

		if (count($pieces) == 2)
		{
			$item_kit_id = (int)$pieces[1];
		}

		$this->db->from('item_kits');
		$this->db->where('item_kit_id',$item_kit_id);

		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			$cache[$item_kit_id] = $query->row();
			return $cache[$item_kit_id];
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('item_kits');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}
	
	function check_duplicate($term)
	{
		$this->db->from('item_kits');
		$this->db->where('deleted',0);
		$query = $this->db->where("name = ".$this->db->escape($term));
		$query=$this->db->get();

		if($query->num_rows()>0)
		{
			return true;
		}

	}
	
	//returns an int or false
	public function lookup_item_kit_id($item_kit_identifer)
	{
		$result = false;
    $item_lookup_order = unserialize($this->config->item('item_lookup_order'));
		foreach($item_lookup_order as $item_lookup_number)
		{
			switch ($item_lookup_number) 
			{
		    case 'item_id':
						$result = $this->lookup_item_kit_by_item_kit_id($item_kit_identifer);
		        break;
		    case 'item_number':
		        $result = $this->lookup_item_kit_by_item_kit_number($item_kit_identifer);
		        break;
		    case 'product_id':
			      $result = $this->lookup_item_kit_by_product_id($item_kit_identifer);
		        break;
			}
			
			if ($result !== FALSE)
			{
				return $result;
			}
		}
		
		return FALSE;
	}

	private function lookup_item_kit_by_item_kit_id($item_kit_id)
	{
		if (does_contain_only_digits($item_kit_id))
		{
			if($this->exists($item_kit_id))
			{
				return (int)$item_kit_id;
			}	
	
		}	
		return false;
	}
	
	//return item_id
	private function lookup_item_kit_by_item_kit_number($item_kit_number)
	{
		$this->db->from('item_kits');
		$this->db->where('item_kit_number',$item_kit_number);

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->row()->item_kit_id;
		}
		
		return false;
	}
	
	private function lookup_item_kit_by_product_id($product_id)
	{
		$this->db->from('item_kits');
		$this->db->where('product_id', $product_id); 

		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->row()->item_kit_id;
		}
		
		return false;
	}
	
	/*
	Get an item_kit_id given an item kit number
	*/
	function get_item_kit_id($item_kit_number)
	{
		$this->db->from('item_kits');
		$this->db->where('item_kit_number',$item_kit_number);
		$this->db->or_where('product_id', $item_kit_number);
		$query = $this->db->get();

		if($query->num_rows() >= 1)
		{
			return $query->row()->item_kit_id;
		}

		return false;
	}
	
	/*
	Gets information about multiple item kits
	*/
	function get_multiple_info($item_kit_ids)
	{
		$this->db->from('item_kits');
		if (!empty($item_kit_ids))
		{
			$this->db->group_start();
			$item_kit_ids_chunk = array_chunk($item_kit_ids,25);
			foreach($item_kit_ids_chunk as $item_kit_ids)
			{
				$this->db->or_where_in('item_kit_id',$item_kit_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);
		}
		
		$this->db->order_by("name", "asc");
		return $this->db->get();
	}
	

	/*
	Inserts or updates an item kit
	*/
	function save(&$item_kit_data,$item_kit_id=false)
	{
		if (!$item_kit_id or !$this->exists($item_kit_id))
		{
			if($this->db->insert('item_kits',$item_kit_data))
			{
				$item_kit_data['item_kit_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('item_kit_id', $item_kit_id);
		return $this->db->update('item_kits',$item_kit_data);
	}

	/*
	Deletes one item kit
	*/
	function delete($item_kit_id)
	{
		$this->db->where('item_kit_id', $item_kit_id);
		return $this->db->update('item_kits', array('deleted' => 1));
	}

	/*
	Deletes a list of item kits
	*/
	function delete_list($item_kit_ids)
	{
		$this->db->where_in('item_kit_id',$item_kit_ids);
		return $this->db->update('item_kits', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find kits
	*/
	function get_manage_item_kits_search_suggestions($search,$limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
			$this->db->select('item_kits.*, categories.name as category');
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like('item_kits.name',$search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
			$by_name = $this->db->get();
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'subtitle' => $row->category,
					'avatar' => base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->item_kit_id] = $data;
			}
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle'] ? $value['subtitle'] : lang('common_none'));		
			}

			$this->db->select('item_kits.*, categories.name as category');
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like('item_kit_number',$search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
			$by_item_kit_number = $this->db->get();
			$temp_suggestions = array();
			foreach($by_item_kit_number->result() as $row)
			{
				$data = array(
					'name' => $row->item_kit_number,
					'subtitle' => $row->category,
					'avatar' => base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->item_kit_id] = $data;
			}

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle'] ? $value['subtitle'] : lang('common_none'));		
			}

			$this->db->select('item_kits.*, categories.name as category');
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like('product_id',$search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
			$by_product_id = $this->db->get();
			$temp_suggestions = array();
			foreach($by_product_id->result() as $row)
			{
				$data = array(
					'name' => $row->product_id,
					'subtitle' => $row->category,
					'avatar' => base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->item_kit_id] = $data;
			}

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle'] ? $value['subtitle'] : lang('common_none'));		

			}

			$this->db->from('item_kits_tags');
			$this->db->join('tags', 'item_kits_tags.tag_id=tags.id');
			$this->db->like('name',$search,'both');
			$this->db->where('deleted',0);
			$this->db->limit($limit);

			$by_tags = $this->db->get();
			$temp_suggestions = array();

			foreach($by_tags->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'subtitle' => '',
					'avatar' => base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->item_kit_id] = $data;
			}


			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle'] ? $value['subtitle'] : lang('common_none'));		

			}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
	
	function get_item_kit_search_suggestions_sales_recv($search,$price_field = 'unit_price', $limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
			$this->db->select("item_kits.*,categories.name as category", false);
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like($this->db->dbprefix('item_kits').'.name', $search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
			$by_name = $this->db->get();
		
			
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$data = array(
					'image' => base_url()."assets/img/item-kit.png" ,
					'category' => $row->category,
					'item_kit_number' => $row->item_kit_number,
				);

				if ($row->category)
				{
					$data['label'] = $row->name . ' ('.$row->category.') - '.to_currency($row->$price_field);
					$temp_suggestions['KIT '.$row->item_kit_id] =  $data;
				}
				else
				{
					$data['label'] = $row->name.' - '.to_currency($row->$price_field);
					$temp_suggestions['KIT '.$row->item_kit_id] = $data;
				}
			}
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'],  'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);		
			}
		
			$this->db->select("item_kits.*,categories.name as category", false);
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like($this->db->dbprefix('item_kits').'.item_kit_number', $search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
			$by_item_kit_number = $this->db->get();
			

			$temp_suggestions = array();
		
			foreach($by_item_kit_number->result() as $row)
			{
				$data = array(
						'label' => $row->item_kit_number.' - '.to_currency($row->$price_field),
						'image' => base_url()."assets/img/item-kit.png" ,
						'category' => $row->category,
						'item_kit_number' => $row->item_kit_number,
					);

				$temp_suggestions['KIT '.$row->item_kit_id] = $data;
			}
			
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'],  'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);		
			}

			$this->db->select("item_kits.*,categories.name as category", false);
			$this->db->from('item_kits');
			$this->db->join('categories', 'categories.id = item_kits.category_id','left');
			$this->db->like($this->db->dbprefix('item_kits').'.product_id', $search,'both');
			$this->db->where('item_kits.deleted',0);
			$this->db->limit($limit);
		
			$by_product_id = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_product_id->result() as $row)
			{
				$data = array(
						'label' => $row->product_id.' - '.to_currency($row->$price_field),
						'image' => base_url()."assets/img/item-kit.png" ,
						'category' => $row->category,
						'item_kit_number' => $row->item_kit_number,
					);

				$temp_suggestions['KIT '.$row->item_kit_id] = $data;
			}
			
			uasort($temp_suggestions, 'sort_assoc_array_by_label');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['label'], 'image' => $value['image'], 'category' => $value['category'], 'item_kit_number' => $value['item_kit_number']);		
			}
		
		for($k=count($suggestions)-1;$k>=0;$k--)
		{
			if (!$suggestions[$k]['label'])
			{
				unset($suggestions[$k]);
			}
		}
		
		$suggestions = array_values($suggestions);
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		
		return $suggestions;

	}
	
	
	
	function search($search, $category_id = false, $limit=20,$offset=0,$column='name',$orderby='asc', $fields = 'all')
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->distinct();
		}
		
		if ($category_id)
		{
			if ($this->config->item('include_child_categories_when_searching_or_reporting'))
			{	
				$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($category_id);			
			}
			else
			{
				$category_ids = array($category_id);
			}
		}
		
		$this->db->select('item_kits.*, categories.name as category,
		location_item_kits.unit_price as location_unit_price,
		location_item_kits.cost_price as location_cost_price');
		$this->db->from('item_kits');		
		$this->db->join('location_item_kits', 'location_item_kits.item_kit_id = item_kits.item_kit_id and location_id = '.$current_location, 'left');
		$this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');
		
		$this->db->join('categories', 'categories.id = item_kits.category_id','left');
		
		if ($fields == $this->db->dbprefix('manufacturers').'.name')
		{
			$this->db->join('manufacturers', 'item_kits.manufacturer_id = manufacturers.id', 'left');
		}		
		
		if ($fields == 'all')
		{
			if ($search)
			{
					$this->db->where("(".$this->db->dbprefix('item_kits').".name LIKE '%".$this->db->escape_like_str($search).
					"%' or item_kit_number LIKE '%".$this->db->escape_like_str($search)."%'".
					"or product_id LIKE '%".$this->db->escape_like_str($search)."%' or
					".$this->db->dbprefix('tags').".name LIKE '%".$this->db->escape_like_str($search)."%' or
					description LIKE '%".$this->db->escape_like_str($search)."%') and ".$this->db->dbprefix('item_kits').".deleted=0");
			}
		}
		else
		{			
			if ($search)
			{
				//Exact Match fields
				if ($fields == $this->db->dbprefix('item_kits').'.item_kit_id'|| $fields == $this->db->dbprefix('item_kits').'.cost_price' 
					|| $fields == $this->db->dbprefix('item_kits').'.unit_price' || $fields == $this->db->dbprefix('tags').'.name')
				{
					$this->db->where("$fields = ".$this->db->escape($search)." and ".$this->db->dbprefix('item_kits').".deleted=0");								
				}
				else
				{
						$this->db->like($fields,$search,'both');
						$this->db->where($this->db->dbprefix('item_kits').".deleted=0");																		
				}
			}
		}
				
		if(isset($category_ids) && !empty($category_ids)) 
		{
			$this->db->where_in('categories.id', $category_ids);
		}
		
			
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($column, $orderby);
		}
		
		if (!$search) //If we don't have a search make sure we filter out deleted items
		{
			$this->db->where('item_kits.deleted', 0);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	

	function search_count_all($search, $category_id = FALSE, $limit=10000)
	{
		
		if ($this->config->item('speed_up_search_queries'))
		{
			return $limit;
		}
		
		
		if ($category_id)
		{
			if ($this->config->item('include_child_categories_when_searching_or_reporting'))
			{	
				$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($category_id);			
			}
			else
			{
				$category_ids = array($category_id);
			}
		}
		
		
		$this->db->from('item_kits');
		$this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('tags', 'tags.id = item_kits_tags.tag_id', 'left');
		$this->db->join('categories', 'categories.id = item_kits.category_id','left');

		if ($search)
		{
				$this->db->where("(".$this->db->dbprefix('item_kits').".name LIKE '%".$this->db->escape_like_str($search).
				"%' or item_kit_number LIKE '%".$this->db->escape_like_str($search)."%' or
				description LIKE '%".$this->db->escape_like_str($search)."%') and ".$this->db->dbprefix('item_kits').".deleted=0");						
		}
		else
		{
			$this->db->where('item_kits.deleted',0);
		}


		if(isset($category_ids) && !empty($category_ids)) 
		{
			$this->db->where_in('categories.id', $category_ids);
		}

		$result=$this->db->get();
		return $result->num_rows();
	}

	function get_tier_price_row($tier_id,$item_kit_id)
	{
		$this->db->from('item_kits_tier_prices');
		$this->db->where('tier_id',$tier_id);
		$this->db->where('item_kit_id ',$item_kit_id);
		return $this->db->get()->row();
	}

	function delete_tier_price($tier_id, $item_kit_id)
	{

		$this->db->where('tier_id', $tier_id);
		$this->db->where('item_kit_id', $item_kit_id);
		$this->db->delete('item_kits_tier_prices');
	}

	function tier_exists($tier_id, $item_kit_id)
	{
		$this->db->from('item_kits_tier_prices');
		$this->db->where('tier_id',$tier_id);
		$this->db->where('item_kit_id',$item_kit_id);
		$query = $this->db->get();

		return ($query->num_rows()>=1);

	}

	function save_item_tiers($tier_data,$item_kit_id)
	{
		if($this->tier_exists($tier_data['tier_id'],$item_kit_id))
		{
			$this->db->where('tier_id', $tier_data['tier_id']);
			$this->db->where('item_kit_id', $item_kit_id);

			return $this->db->update('item_kits_tier_prices',$tier_data);

		}

		return $this->db->insert('item_kits_tier_prices',$tier_data);
	}
	
	function cleanup()
	{
		$item_kit_data = array('item_kit_number' => null, 'product_id' => null);
		$this->db->where('deleted', 1);
		return $this->db->update('item_kits',$item_kit_data);
	}
	
	function get_displayable_columns()
	{
		$this->lang->load('items');
		$this->load->helper('items');
		return array(
			'item_kit_id' => 										array('sort_column' => 'item_kits.item_kit_id', 'label' => lang('common_item_kit_id')),
			'item_kit_number' => 								array('sort_column' => 'item_kits.item_kit_number','label' => lang('common_item_number_expanded')),
			'product_id' => 										array('sort_column' => 'item_kits.product_id','label' => lang('common_product_id')),
			'name' => 													array('sort_column' => 'item_kits.name','label' => lang('common_name')),
			'category' => 											array('sort_column' => 'category','label' => lang('common_category')),
			'category_id' => 										array('sort_column' => 'category','label' => lang('common_category_full_path'),'format_function' => 'get_full_category_path'),
			'cost_price' => 										array('sort_column' => 'item_kits.cost_price','label' => lang('common_cost_price'),'format_function' => 'to_currency'),
			'location_cost_price' => 						array('sort_column' => 'location_cost_price','label' => lang('common_location_cost_price'),'format_function' => 'to_currency'),
			'unit_price' => 										array('sort_column' => 'item_kits.unit_price','label' => lang('common_unit_price'),'format_function' => 'to_currency'),
			'description' => 										array('sort_column' => 'item_kits.description','label' => lang('common_description')),
			'tax_included' => 									array('sort_column' => 'item_kits.tax_included','label' => lang('common_prices_include_tax'),'format_function' => 'boolean_as_string'),
			'override_default_tax'  => 					array('sort_column' => 'item_kits.override_default_tax','label' => lang('common_override_default_tax'),'format_function' => 'boolean_as_string'),		
			'is_ebt_item'  => 									array('sort_column' => 'item_kits.is_ebt_item','label' => lang('common_is_ebt_item'),'format_function' => 'boolean_as_string'),		
			'commission_percent'  => 						array('sort_column' => 'item_kits.commission_percent','label' => lang('items_commission_percent')),		
			'commission_percent_type'  => 			array('sort_column' => 'item_kits.commission_percent_type','label' => lang('items_commission_percent_type'),'format_function' => 'commission_percent_type_formater'),		
			'commission_fixed'  => 							array('sort_column' => 'item_kits.commission_fixed','label' => lang('items_commission_fixed')),		
			'change_cost_price'  => 						array('sort_column' => 'item_kits.change_cost_price','label' => lang('common_change_cost_price_during_sale'),'format_function' => 'boolean_as_string'),		
			'disable_loyalty'  => 							array('sort_column' => 'item_kits.disable_loyalty','label' => lang('common_disable_loyalty'),'format_function' => 'boolean_as_string'),				
			'max_discount_percent'  => 					array('sort_column' => 'item_kits.max_discount_percent','label' => lang('common_max_discount_percent'),'format_function' => 'to_percent'),
			'min_edit_price'  => 								array('sort_column' => 'item_kits.min_edit_price','label' => lang('common_min_edit_price'),'format_function' => 'to_currency'),
			'max_edit_price'  => 								array('sort_column' => 'item_kits.max_edit_price','label' => lang('common_max_edit_price'),'format_function' => 'to_currency'),
		);
	}
	
	function get_default_columns()
	{
		return array('item_kit_id','item_kit_number','name','category_id','cost_price','unit_price');
	}
}
?>
