<?php
class Inventory extends CI_Model 
{	
	function insert($inventory_data)
	{
		if(is_numeric($inventory_data['trans_inventory']))
		{
			return $this->db->insert('inventory',$inventory_data);
		}
		
		return TRUE;
	}
	
	function get_inventory_data_for_item($item_id, $limit, $offset, $location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		$this->db->from('inventory');
		$this->db->where('trans_items',$item_id);
		$this->db->where('location_id',$location_id);
		$this->db->order_by("trans_date", "desc");
		$this->db->order_by("trans_id", "desc");
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();		
	}
	
	function count_all($item_id,$location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('inventory');
		$this->db->where('trans_items',$item_id);
		$this->db->where('location_id',$location_id);
		
		return $this->db->count_all_results();
	}
	
	function get_count_by_status($status, $location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('inventory_counts');
		$this->db->where('status',$status);
		$this->db->where('location_id',$location_id);
		
		return $this->db->count_all_results();
	}
	
	function get_counts_by_status($status, $limit = 100, $offset = 0, $location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('inventory_counts');
		$this->db->where('status',$status);
		$this->db->where('location_id',$location_id);
		$this->db->order_by("count_date", "desc");
		$this->db->limit($limit);
		$this->db->offset($offset);

		return $this->db->get();		
	}
	
	function get_number_of_items_counted($inventory_counts_id)
	{
		$this->db->from('inventory_counts_items');
		$this->db->where('inventory_counts_id',$inventory_counts_id);
		return $this->db->count_all_results();
	}
	
	function create_count($date = false, $status = false, $comment = false, $employee_id = false, $location_id = false)
	{
		if (!$date)
		{
			$date = date('Y-m-d H:i:s');	
		}
		
		if (!$status)
		{
			$status = 'open';
		}
		
		if (!$comment)
		{
			$comment = '';
		}
		
		if (!$employee_id)
		{
			$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		}
		
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		
		$count_data = array(
		'count_date'=>$date,
		'employee_id' => $employee_id,
		'location_id'=> $location_id,
		'status' => $status,
		'comment' => $comment,
		);


		if($this->db->insert('inventory_counts', $count_data))
		{
			return $this->db->insert_id();
		}
		
		return FALSE;
	}
	
	function get_count_info($count_id)
	{
		$this->db->from('inventory_counts');
		$this->db->where('id',$count_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		
		return NULL;
	}
	
	function set_count($count_id, $status = false, $comment = false)
	{
		$data = array();
		
		if ($status !== FALSE)
		{
			$data['status'] = $status;
		}
		
		if ($comment !== FALSE)
		{
			$data['comment'] = $comment;
		}
		
		$this->db->where('id', $count_id);
		return $this->db->update('inventory_counts', $data);
		
	}
	
	function set_count_item($count_id, $item_id, $count = false, $actual_quantity = false, $comment = false)
	{
		$this->db->from('inventory_counts_items');
		$this->db->where('item_id',$item_id);
		$this->db->where('inventory_counts_id', $count_id);
		$query = $this->db->get();

		$exists = ($query->num_rows()==1);	
		
		$data = array(
			'inventory_counts_id' => $count_id,
			'item_id' => $item_id,
		);
		
		if ($count !== FALSE)
		{
			$data['count'] = $count;			
		}
		
		if ($actual_quantity !== FALSE)
		{
			$data['actual_quantity'] = $actual_quantity;			
			
		}
		
		if ($comment !== FALSE)
		{
			$data['comment'] = $comment;			
		
		}
		
		
		if ($exists)
		{
			$mode = $this->session->userdata('count_mode') ? $this->session->userdata('count_mode') : 'scan_and_set';
			
			if ($mode == 'scan_and_set')
			{
				$data['actual_quantity'] = $this->get_count_item_actual_quantity($count_id, $item_id);
				
				if ($comment === FALSE)
				{
					$data['comment'] = $this->get_count_item_actual_comment($count_id, $item_id);
				}
				
				//Remove previous item
				$this->db->where('item_id',$item_id);
				$this->db->where('inventory_counts_id', $count_id);
				$this->db->delete('inventory_counts_items');
				
				return $this->db->insert('inventory_counts_items', $data);					
			}
			
			$this->db->where('item_id',$item_id);
			$this->db->where('inventory_counts_id', $count_id);
			return $this->db->update('inventory_counts_items', $data);
		}
		
		return $this->db->insert('inventory_counts_items', $data);	
	}
	
	function delete_count_item($count_id, $item_id)
	{
		$this->db->delete('inventory_counts_items', array('inventory_counts_id' => $count_id, 'item_id' => $item_id));
	}
	
	function get_count_item_current_quantity($count_id, $item_id)
	{
		$this->db->from('inventory_counts_items');
		$this->db->where('item_id',$item_id);
		$this->db->where('inventory_counts_id', $count_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->count;
		}
		
		return 0;
	}
	
	function get_count_item_actual_quantity($count_id, $item_id)
	{
		$this->db->from('inventory_counts_items');
		$this->db->where('item_id',$item_id);
		$this->db->where('inventory_counts_id', $count_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->actual_quantity;
		}
		
		return NULL;
	}
	
	function get_count_item_actual_comment($count_id, $item_id)
	{
		$this->db->from('inventory_counts_items');
		$this->db->where('item_id',$item_id);
		$this->db->where('inventory_counts_id', $count_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->comment;
		}
		
		return NULL;
	}
	
	function get_items_counted($count_id,$limit = 100, $offset = 0)
	{
		$this->db->select('items.*, inventory_counts_items.*, inventory_counts.location_id, inventory_counts.employee_id, categories.name as category');
		$this->db->from('inventory_counts_items');
		$this->db->where('inventory_counts_id', $count_id);
		$this->db->join('inventory_counts', 'inventory_counts.id = inventory_counts_items.inventory_counts_id');
		$this->db->join('items', 'items.item_id = inventory_counts_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id','left');
		
		if ($limit !== NULL)
		{
			$this->db->limit($limit);
		}
		
		if ($offset !== NULL)
		{
			$this->db->offset($offset);
		}
		$this->db->order_by('inventory_counts_items.id', 'DESC');
		return $this->db->get()->result_array();
	}
	
	function delete_inventory_count($count_id)
	{
		$this->db->delete('inventory_counts_items', array('inventory_counts_id' => $count_id));
		$this->db->delete('inventory_counts', array('id' => $count_id));
		
		return TRUE;
	}
	
	function update_inventory_from_count($count_id)
	{
		$this->load->model('Item_location');
		foreach($this->get_items_counted($count_id, NULL,NULL) as $count_item)
		{
			$current_inventory_value = $count_item['actual_quantity'];
			$counted_inventory_value = $count_item['count'];
	
	
			if ($current_inventory_value != $counted_inventory_value)
			{
				$inv_data = array
					(
					'trans_date'=>date('Y-m-d H:i:s'),
					'trans_items'=>$count_item['item_id'],
					'trans_user'=>$count_item['employee_id'],
					'trans_comment'=>lang('items_inventory_count_update'),
					'trans_inventory'=>$counted_inventory_value - $current_inventory_value,
					'location_id' => $count_item['location_id'],
				);
				$this->insert($inv_data);
				
				$cur_quantity = $this->Item_location->get_location_quantity($count_item['item_id'],$count_item['location_id']);
				$this->Item_location->save_quantity($cur_quantity + ($counted_inventory_value - $current_inventory_value), $count_item['item_id'],$count_item['location_id']);
			}
		}
	}
}

?>