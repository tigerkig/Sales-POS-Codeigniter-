<?php
class Delivery extends CI_Model
{
	public function __construct()
	{
      parent::__construct();
			$this->load->model('Inventory');	
	}
	
	public function get_info($delivery_id)
	{
		$this->db->from('sales_deliveries');
		$this->db->where('id',$delivery_id);
		return $this->db->get();
	}
	
	function get_delivery_person_id($sale_id)
	{
		$this->db->from('sales_deliveries');
		$this->db->where('sale_id',$sale_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->shipping_address_person_id;
		}
		
		return FALSE;
	}
	
	function get_delivery_tax_group_id($sale_id)
	{
		$this->db->from('sales_deliveries');
		$this->db->where('sale_id', $sale_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->tax_class_id;
		}
		
		return FALSE;
	}
	
	function get_info_by_sale_id($sale_id)
	{
		$this->db->from('sales_deliveries');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}
	
	/*
	Perform a search on deliveries
	*/
	function search($search, $filters = array(), $limit=20, $offset=0, $column='estimated_shipping_date', $orderby='asc')
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('items').'.name,": ",FLOOR('.$this->db->dbprefix('sales_items').'.quantity_purchased)  SEPARATOR "<br /> ") as items, GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('item_kits').'.name,": ",FLOOR('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased)  SEPARATOR "<br /> ") as item_kits, sales.comment as sale_comment,sales.location_id as location_id,shipping_zones.name as shipping_zone_name, sales_deliveries.*,
		CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", zip, " ", country) as full_address,
		people.*,
		shipping_methods.name as `shipping_method_name`,
		shipping_providers.name as `shipping_provider_name`');		
		$this->db->from('sales_deliveries');
		$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id', 'left');
		$this->db->join('sales_item_kits', 'sales.sale_id = sales_item_kits.sale_id', 'left');
		$this->db->join('items', 'sales_items.item_id = items.item_id and system_item = 0','left');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id','left');
		$this->db->join('shipping_zones', 'shipping_zones.id = sales_deliveries.shipping_zone_id','left');
		$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
		$this->db->join('shipping_methods', 'sales_deliveries.shipping_method_id = shipping_methods.id','left');
		$this->db->join('shipping_providers', 'shipping_methods.shipping_provider_id = shipping_providers.id','left');
				
		if ($search)
		{
			$this->db->where("(
			tracking_number LIKE '".$this->db->escape_like_str($search)."%' or
			shipping_zones.name LIKE '".$this->db->escape_like_str($search)."%' or
			first_name LIKE '%".$this->db->escape_like_str($search)."%' or
			address_1 LIKE '%".$this->db->escape_like_str($search)."%' or
			address_2 LIKE '%".$this->db->escape_like_str($search)."%' or
			city LIKE '%".$this->db->escape_like_str($search)."%' or
			state LIKE '%".$this->db->escape_like_str($search)."%' or
			zip LIKE '%".$this->db->escape_like_str($search)."%' or
			CONCAT(address_1,', ',address_2,', ',city,', ',state,', ',zip,', ',country)  = ".$this->db->escape($search)." or
			sales_deliveries.sale_id  = ".$this->db->escape($search)." or
			email LIKE '%".$this->db->escape_like_str($search)."%' or 
			phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%')");		
		}
		
		if(isset($filters) && count($filters) > 0)
		{
			$this->db->group_start();
		
			if (isset($filters['is_pickup']))
			{
				$this->db->where_in('is_pickup', $filters['is_pickup']);
			}
		
			if (isset($filters['status']))
			{
				$this->db->where_in('status', $filters['status']);
			}
			
			if(isset($filters['shipping_start']))
			{
				$this->db->where('estimated_shipping_date >=', date('Y-m-d H:i:s',strtotime($filters['shipping_start'])));
			}
			
			if(isset($filters['shipping_end']))
			{
				$this->db->where('estimated_shipping_date <=', date('Y-m-d H:i:s',strtotime($filters['shipping_end'])));
			}
			
			if(isset($filters['delivery_start']))
			{
				$this->db->where('estimated_delivery_or_pickup_date >=',  date('Y-m-d H:i:s',strtotime($filters['delivery_start'])));
			}
			
			if(isset($filters['delivery_end']))
			{
				$this->db->where('estimated_delivery_or_pickup_date <=', date('Y-m-d H:i:s',strtotime($filters['delivery_end'])));
			}
		
			$this->db->group_end();
		}
		
		$this->db->where('location_id',$location_id);
		$this->db->where('sales.deleted',0);
		$this->db->group_by('sales.sale_id');
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($column, $orderby);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		
	 return $this->db->get();
		 
	}
	
	function search_count_all($search, $filters = array(),$limit=10000)
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('items').'.name,": ",FLOOR('.$this->db->dbprefix('sales_items').'.quantity_purchased)  SEPARATOR "<br /> ") as items, GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('item_kits').'.name,": ",FLOOR('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased)  SEPARATOR "<br /> ") as item_kits, sales.comment as sale_comment,sales.location_id as location_id,shipping_zones.name as shipping_zone_name, sales_deliveries.*,
		CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", zip, " ", country) as full_address,
		people.*,
		shipping_methods.name as `shipping_method_name`,
		shipping_providers.name as `shipping_provider_name`');		
		$this->db->from('sales_deliveries');
		$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id', 'left');
		$this->db->join('sales_item_kits', 'sales.sale_id = sales_item_kits.sale_id', 'left');
		$this->db->join('items', 'sales_items.item_id = items.item_id and system_item = 0','left');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id','left');
		$this->db->join('shipping_zones', 'shipping_zones.id = sales_deliveries.shipping_zone_id','left');
		$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
		$this->db->join('shipping_methods', 'sales_deliveries.shipping_method_id = shipping_methods.id','left');
		$this->db->join('shipping_providers', 'shipping_methods.shipping_provider_id = shipping_providers.id','left');
				
		if ($search)
		{
			$this->db->where("(
			tracking_number LIKE '".$this->db->escape_like_str($search)."%' or
			shipping_zones.name LIKE '".$this->db->escape_like_str($search)."%' or
			first_name LIKE '%".$this->db->escape_like_str($search)."%' or
			address_1 LIKE '%".$this->db->escape_like_str($search)."%' or
			address_2 LIKE '%".$this->db->escape_like_str($search)."%' or
			city LIKE '%".$this->db->escape_like_str($search)."%' or
			state LIKE '%".$this->db->escape_like_str($search)."%' or
			zip LIKE '%".$this->db->escape_like_str($search)."%' or
			CONCAT(address_1,', ',address_2,', ',city,', ',state,', ',zip,', ',country)  = ".$this->db->escape($search)." or
			sales_deliveries.sale_id  = ".$this->db->escape($search)." or
			email LIKE '%".$this->db->escape_like_str($search)."%' or 
			phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%')");		
		}
		
		if(isset($filters) && count($filters) > 0)
		{
			$this->db->group_start();
		
			if (isset($filters['is_pickup']))
			{
				$this->db->where_in('is_pickup', $filters['is_pickup']);
			}
		
			if (isset($filters['status']))
			{
				$this->db->where_in('status', $filters['status']);
			}
			
			if(isset($filters['shipping_start']))
			{
				$this->db->where('estimated_shipping_date >=', date('Y-m-d H:i:s',strtotime($filters['shipping_start'])));
			}
			
			if(isset($filters['shipping_end']))
			{
				$this->db->where('estimated_shipping_date <=', date('Y-m-d H:i:s',strtotime($filters['shipping_end'])));
			}
			
			if(isset($filters['delivery_start']))
			{
				$this->db->where('estimated_delivery_or_pickup_date >=',  date('Y-m-d H:i:s',strtotime($filters['delivery_start'])));
			}
			
			if(isset($filters['delivery_end']))
			{
				$this->db->where('estimated_delivery_or_pickup_date <=', date('Y-m-d H:i:s',strtotime($filters['delivery_end'])));
			}
		
			$this->db->group_end();
		}
		
		$this->db->where('location_id',$location_id);
		$this->db->where('sales.deleted',0);
		$this->db->group_by('sales.sale_id');
		
		$this->db->limit($limit);
		
		
		return $this->db->count_all_results();
	}
	
	/*
	Get search suggestions to find deliveries
	*/
	function get_search_suggestions($search,$limit=5)
	{
		
		if (!trim($search))
		{
			return array();
		}
		
			$suggestions = array();
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();

			$this->db->from('sales_deliveries');
			$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
			
			$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
			$this->db->where('sales.deleted',0);
					
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
		  last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%')");		
			$this->db->where('sales.location_id',$location_id);
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->first_name . ' ' .  $row->last_name,
					'subtitle' => $row->address_1 . ', ' . $row->address_2 . ', ' . $row->city . ', ' . $row->state . ', ' . $row->zip . ', ' . $row->country,
					'avatar' => base_url()."assets/img/giftcard.png",
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
			
			$this->db->from('sales_deliveries');
			$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
			
			$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
		
			$this->db->where('sales.deleted',0);
			
			$this->db->where("(address_1 LIKE '%".$this->db->escape_like_str($search)."%' or
			address_2 LIKE '%".$this->db->escape_like_str($search)."%' or 
		  city LIKE '%".$this->db->escape_like_str($search)."%' or 
		  state LIKE '%".$this->db->escape_like_str($search)."%' or 
			zip LIKE '%".$this->db->escape_like_str($search)."%')");		
			$this->db->where('sales.location_id',$location_id);
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->address_1 . ', ' . $row->address_2 . ', ' . $row->city . ', ' . $row->state . ', ' . $row->zip . ', ' . $row->country,
					'subtitle' => $row->first_name . ' ' .  $row->last_name,
					'avatar' => base_url()."assets/img/giftcard.png",
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
			
			
			
			$this->db->from('sales_deliveries');
			$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
			
			$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
			$this->db->where("phone_number LIKE '%".$this->db->escape_like_str($search)."%'");
			$this->db->where('sales.location_id',$location_id);
			$this->db->where('sales.deleted',0);
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->phone_number,
					'subtitle' => $row->first_name.' '.$row->last_name,
					'avatar' => base_url()."assets/img/giftcard.png",
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
			
			
			
			
			$this->db->from('sales_deliveries');
			$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
			
			$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
			$this->db->where("email LIKE '%".$this->db->escape_like_str($search)."%'");
			$this->db->where('sales.location_id',$location_id);
			$this->db->where('sales.deleted',0);
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->email,
					'subtitle' => $row->first_name.' '.$row->last_name,
					'avatar' => base_url()."assets/img/giftcard.png",
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
			
			
			$this->db->from('sales_deliveries');
			$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
			
			$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
			$this->db->where("tracking_number LIKE '".$this->db->escape_like_str($search)."%'");
			$this->db->where('sales.location_id',$location_id);
			$this->db->where('sales.deleted',0);
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			$temp_suggestions = array();
						
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->tracking_number,
					'subtitle' => $row->first_name.' '.$row->last_name,
					'avatar' => base_url()."assets/img/giftcard.png",
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle']);		
			}
		
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	
	}
	
	function get_all_for_range($start_date,$end_date,$col='estimated_delivery_or_pickup_date')
	{	
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		$this->db->select('sales.comment as sale_comment, sales.location_id as location_id,shipping_zones.name as shipping_zone_name, sales_deliveries.id as delivery_id,sales_deliveries.*,sales.sale_time,
		CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", zip, " ", country) as full_address,
		people.*,
		shipping_methods.name as `shipping_method_name`,
		shipping_providers.name as `shipping_provider_name`');
		$this->db->from('sales_deliveries');
		$this->db->join('shipping_zones', 'shipping_zones.id = sales_deliveries.shipping_zone_id','left');
		$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
		$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
		$this->db->join('shipping_methods', 'sales_deliveries.shipping_method_id = shipping_methods.id','left');
		$this->db->join('shipping_providers', 'shipping_methods.shipping_provider_id = shipping_providers.id','left');
		$this->db->where($col. ' >= ',$start_date);
		$this->db->where($col. ' <= ',$end_date.' 23:59:59');
		$this->db->where('location_id',$location_id);
		$this->db->where('sales.deleted',0);
		$this->db->order_by($col);
		return $this->db->get();
	}
	
	
	function get_all($limit=10000, $offset=0,$col='estimated_shipping_date',$order='asc',$filters = array())
	{	
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('items').'.name,": ",FLOOR('.$this->db->dbprefix('sales_items').'.quantity_purchased)  SEPARATOR "<br /> ") as items, GROUP_CONCAT(DISTINCT '.$this->db->dbprefix('item_kits').'.name,": ",FLOOR('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased)  SEPARATOR "<br /> ") as item_kits, sales.comment as sale_comment,sales.location_id as location_id,shipping_zones.name as shipping_zone_name, sales_deliveries.*,
		CONCAT(address_1, " ", address_2, " ", city, " ", state, " ", zip, " ", country) as full_address,
		people.*,
		shipping_methods.name as `shipping_method_name`,
		shipping_providers.name as `shipping_provider_name`');
		$this->db->from('sales_deliveries');
		$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id', 'left');
		$this->db->join('sales_item_kits', 'sales.sale_id = sales_item_kits.sale_id', 'left');
		$this->db->join('items', 'sales_items.item_id = items.item_id and system_item = 0','left');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id','left');
		$this->db->join('shipping_zones', 'shipping_zones.id = sales_deliveries.shipping_zone_id','left');
		$this->db->join('people', 'sales_deliveries.shipping_address_person_id = people.person_id');
		$this->db->join('shipping_methods', 'sales_deliveries.shipping_method_id = shipping_methods.id','left');
		$this->db->join('shipping_providers', 'shipping_methods.shipping_provider_id = shipping_providers.id','left');
		
				
		if(isset($filters) && count($filters) > 0)
		{
			
			$this->db->group_start();
		
			if (isset($filters['is_pickup']))
			{
				$this->db->where_in('is_pickup', $filters['is_pickup']);
			}
		
			if (isset($filters['status']))
			{
				$this->db->where_in('status', $filters['status']);
			}
			
			if(isset($filters['shipping_start']))
			{
				$this->db->where('estimated_shipping_date >=', date('Y-m-d H:i:s',strtotime($filters['shipping_start'])));
			}
			
			if(isset($filters['shipping_end']))
			{
				$this->db->where('estimated_shipping_date <=', date('Y-m-d H:i:s',strtotime($filters['shipping_end'])));
			}
			
			if(isset($filters['delivery_start']))
			{
				$this->db->where('estimated_delivery_or_pickup_date >=',  date('Y-m-d H:i:s',strtotime($filters['delivery_start'])));
			}
			
			if(isset($filters['delivery_end']))
			{
				$this->db->where('estimated_delivery_or_pickup_date <=', date('Y-m-d H:i:s',strtotime($filters['delivery_end'])));
			}
			
			$this->db->group_end();
		}
		$this->db->where('location_id',$location_id);
		$this->db->where('sales.deleted',0);
		$this->db->group_by('sales.sale_id');
		
		if(!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit, $offset);
 	 $return = $this->db->get();
 	 return $return;
	}
	
	function count_all()
	{
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->from('sales_deliveries');
		$this->db->join('sales', 'sales.sale_id = sales_deliveries.sale_id');
		$this->db->where('location_id',$location_id);
		$this->db->where('sales.deleted',0);
		return $this->db->count_all_results();
	}
	
	function exists($id)
	{
		$this->db->from('sales_deliveries');
		$this->db->where('id',$id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	/*
	Inserts or updates a delivery
	*/
	function save(&$delivery_data, $delivery_id = false)
	{		
		//If we are overwriting a delivery make sure sale is gone
		if (isset($delivery_data['sale_id']))
		{
			$this->delete_by_sale_id($delivery_data['sale_id']);
		}
		
		if (!$delivery_id or !$this->exists($delivery_id))
		{			
			if($this->db->insert('sales_deliveries',$delivery_data))
			{
				$delivery_data['id'] = $this->db->insert_id();
				return true;
			}
			
			return false;
		}

		$this->db->where('id', $delivery_id);
		return $this->db->update('sales_deliveries', $delivery_data);
	}
	
	function delete($id)
	{	
		$this->db->where('id', $id);
		return $this->db->delete('sales_deliveries'); 
	}
	
	function delete_list($delivery_ids)
	{
		foreach($delivery_ids as $delivery_id)
		{
			$result = $this->Delivery->delete($delivery_id);
			
			if(!$result)
			{
				return false;
			}
		}
		
		return true;
 	}
	
	function delete_by_sale_id($sale_id)
	{
		$this->db->where('sale_id', $sale_id);
		return $this->db->delete('sales_deliveries'); 
		
	}
	function get_displayable_columns()
	{
		$this->load->helper('people_helper');
		$this->lang->load('deliveries');
		$this->load->helper('sale');
		
		return array(
			'sale_id' =>                           array('sort_column' => 'sales_deliveries.sale_id', 'label' => lang('common_sale_id'),'format_function' => 'sale_id_receipt_link_formatter','html' => TRUE),
			'first_name' =>                        array('sort_column' => 'people.first_name', 'label' => lang('common_first_name')),
			'last_name' =>                         array('sort_column' => 'people.last_name', 'label' => lang('common_last_name')),
			'full_address' =>                      array('sort_column' => 'people.address_1', 'label' => lang('common_address'), 'format_function' => 'address', 'html' => TRUE),
			'address_1' =>                         array('sort_column' => 'people.address_1', 'label' => lang('common_address_1')),
			'address_2' =>                         array('sort_column' => 'people.address_2', 'label' => lang('common_address_2')),
			'city' =>                              array('sort_column' => 'people.city', 'label' => lang('common_city')),
			'state' =>                             array('sort_column' => 'people.state', 'label' => lang('common_state')),
			'zip' =>                               array('sort_column' => 'people.zip', 'label' => lang('common_zip')),
			'country' =>                           array('sort_column' => 'people.country', 'label' => lang('common_country')),
			'email' =>                             array('sort_column' => 'people.email', 'label' => lang('common_email'), 'format_function' => 'email_formatter', 'html' => TRUE),
			'phone_number' =>                      array('sort_column' => 'people.phone_number', 'label' => lang('common_phone_number'), 'format_function' => 'tel', 'html' => TRUE),
			'estimated_shipping_date' =>           array('sort_column' => 'sales_deliveries.estimated_shipping_date', 'label' => lang('deliveries_estimated_shipping_date'), 'format_function' => 'datetime_as_display_date', 'html' => FALSE),
			'actual_shipping_date' =>              array('sort_column' => 'sales_deliveries.actual_shipping_date', 'label' => lang('deliveries_actual_shipping_date'), 'format_function' => 'datetime_as_display_date', 'html' => FALSE),
			'estimated_delivery_or_pickup_date' => array('sort_column' => 'sales_deliveries.estimated_delivery_or_pickup_date', 'label' => lang('deliveries_estimated_delivery_or_pickup_date'), 'format_function' => 'datetime_as_display_date', 'html' => FALSE),
			'is_pickup' =>                         array('sort_column' => 'sales_deliveries.is_pickup', 'label' => lang('deliveries_is_pickup'), 'format_function' => 'boolean_as_string', 'html' => FALSE),
			'shipping_method_name' =>              array('sort_column' => 'shipping_methods.name', 'label' => lang('deliveries_shipping_method')),
			'shipping_provider_name' =>            array('sort_column' => 'shipping_providers.name', 'label' => lang('deliveries_shipping_provider')),
			'shipping_zone_name' =>            		 array('sort_column' => 'shipping_zone_name', 'label' => lang('delivery_shipping_zone')),
			'tracking_number' =>                   array('sort_column' => 'sales_deliveries.tracking_number', 'label' => lang('deliveries_tracking_number')),
			'status' =>                            array('sort_column' => 'sales_deliveries.status', 'label' => lang('common_status'), 'format_function' => 'delivery_status'),
			'comment' =>                           array('sort_column' => 'sales_deliveries.comment', 'label' => lang('common_comment')),
			'sale_comment' =>                      array('sort_column' => 'sales.comment', 'label' => lang('deliveries_sale_comment')),
			'items' =>                     				 array('sort_column' => '', 'label' => lang('reports_items'), 'html' => TRUE),
			'item_kits' =>                     		 array('sort_column' => '', 'label' => lang('module_item_kits'), 'html' => TRUE),
		);
	}
	
	function get_default_columns()
	{
		return array('sale_id','status','first_name','last_name', 'full_address');
	}
}
