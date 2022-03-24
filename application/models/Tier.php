<?php
class Tier extends CI_Model
{
	/*
	Gets information about a particular tier
	*/
	function get_info($tier_id)
	{
		$this->db->from('price_tiers');	
		$this->db->where('id',$tier_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			$tier_obj = new stdClass;
			
			//Get all the fields from price_tiers table
			$fields = $this->db->list_fields('price_tiers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$tier_obj->$field='';
			}
			
			return $tier_obj;
		}
	}
	
	/*
	Determines if a given tier_id is a tier
	*/
	function exists($tier_id)
	{
		$this->db->from('price_tiers');	
		$this->db->where('id',$tier_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all()
	{
		$this->db->from('price_tiers');
		$this->db->order_by('order');
		return $this->db->get();
	}

	function count_all()
	{
		$this->db->from('price_tiers');
		return $this->db->count_all_results();
	}
	
	/*
	Inserts or updates a tier
	*/
	function save(&$tier_data,$tier_id=false)
	{
		if (!$tier_id or !$this->exists($tier_id))
		{
			if($this->db->insert('price_tiers',$tier_data))
			{
				$tier_data['id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('id', $tier_id);
		return $this->db->update('price_tiers',$tier_data);
	}
	
	function delete($tier_id)
	{
		//Make sure customers don't belong to tier anymore
		$this->db->where('tier_id', $tier_id);
		$this->db->update('customers', array('tier_id' => NULL));
		
		//Make sure sales doesn't have a tier anymore
		$this->db->where('tier_id', $tier_id);
		$this->db->update('sales', array('tier_id' => NULL));
		
		$this->db->where('id', $tier_id);
		return $this->db->delete('price_tiers'); 
	}

}
?>
