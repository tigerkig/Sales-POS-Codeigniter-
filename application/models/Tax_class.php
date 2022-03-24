<?php
class Tax_class extends CI_Model
{
	function get_first_tax_class_id()
	{
		$this->db->from('tax_classes');	
		$this->db->where('deleted',0);
		$this->db->order_by('id');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row()->id;
		}
		
		return FALSE;
	}
	
	/*
	Gets information about a particular tax
	*/
	function get_info($id)
	{
		$this->db->from('tax_classes');	
		$this->db->where('id',$id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			$tax_obj = new stdClass;
			
			//Get all the fields from tax_classes table
			$fields = $this->db->list_fields('tax_classes');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$tax_obj->$field='';
			}
			
			return $tax_obj;
		}
	}
	
	/*
	Determines if a given id is a tax
	*/
	function exists($id)
	{
		$this->db->from('tax_classes');	
		$this->db->where('id',$id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function find_tax_class_id($search)
	{
		if ($search)
		{
			$this->db->from('tax_classes');	
			$this->db->where('name',$search);
			$query = $this->db->get();			
			if ($query->num_rows() > 0)
			{
				return $query->row()->id;
			}
		}
		
		return null;
	}
	
	function get_tax_classes_indexed_by_id()
	{
		$return = array();
		foreach($this->get_all()->result_array() as $row)
		{
			$return[$row['id']] = $row['name'];
		}
	
		return $return;
	}
	
	function get_all($location_id = FALSE)
	{
		$this->db->from('tax_classes');
		$this->db->where('deleted',0);
		
		if ($location_id !== FALSE)
		{
			$this->db->where('location_id',$location_id);
		}
		$this->db->order_by('order');
		return $this->db->get();
	}
	
	function get_taxes($tax_class_id)
	{
		$this->db->from('tax_classes_taxes');
		$this->db->where('tax_class_id',$tax_class_id);
		$this->db->order_by('order');
		return $this->db->get();
	}
	
	function count_all()
	{
		$this->db->from('tax_classes');
		return $this->db->count_all_results();
	}
	
	/*
	Inserts or updates a tax
	*/
	function save(&$tax_data,$id=false)
	{
		if (!$id or !$this->exists($id))
		{
			if($this->db->insert('tax_classes',$tax_data))
			{
				$tax_data['id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('id', $id);
		return $this->db->update('tax_classes',$tax_data);
	}
	
	function save_tax(&$tax_class_tax_data, $tax_class_tax_id = false)
	{
		
		//Don't save 0 tax percent
		if (isset($tax_class_tax_data['percent']) && $tax_class_tax_data['percent'] == 0)
		{
			return true;
		}
		
		if (!$tax_class_tax_id)
		{
			if($this->db->insert('tax_classes_taxes',$tax_class_tax_data))
			{
				$tax_class_tax_data['id'] = $this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('id', $tax_class_tax_id);
		return $this->db->update('tax_classes_taxes', $tax_class_tax_data);	
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		return $this->db->update('tax_classes', array('deleted' => 1));
	}
	
	function delete_tax($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('tax_classes_taxes'); 
	}

}
?>