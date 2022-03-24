<?php
class Register extends CI_Model
{
	
	function get_default_register_info($location_id = FALSE)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('registers');	
		$this->db->where('location_id',$location_id);
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$row = $query->row();
			return $this->get_info($row->register_id);
			
		}
		
		return $this->get_info(1);
		
	}
	
	/*
	Gets information about a particular register
	*/
	function get_info($register_id)
	{
		$this->db->from('registers');	
		$this->db->where('register_id',$register_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			$register_obj = new stdClass;
			
			//Get all the fields from registers table
			$fields = $this->db->list_fields('registers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$register_obj->$field='';
			}
			
			return $register_obj;
		}
	}
	
	function get_register_name($register_id)
	{
		$info = $this->get_info($register_id);
		
		if ($info && $info->name)
		{
			return $info->name;
		}
		
		return false;
	}
	
	/*
	Determines if a given register_id is a register
	*/
	function exists($register_id)
	{
		$this->db->from('registers');	
		$this->db->where('register_id',$register_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all($location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('registers');
		$this->db->where('location_id', $location_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('register_id');
		return $this->db->get();
	}
	
	function get_all_open($location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->select('registers.*');
		$this->db->from('registers');
		$this->db->join('register_log', 'registers.register_id = register_log.register_id');
		$this->db->where('shift_end','0000-00-00 00:00:00');
		$this->db->where('registers.deleted', 0);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('register_id');
		return $this->db->get();
	}

	function count_all($location_id = false)
	{
		if (!$location_id)
		{
			$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('registers');
		$this->db->where('location_id', $location_id);
		$this->db->where('deleted', 0);
		return $this->db->count_all_results();
	}
	
	/*
	Inserts or updates a register
	*/
	function save(&$register_data,$register_id=false)
	{
		if (!$register_id or !$this->exists($register_id))
		{
			if($this->db->insert('registers',$register_data))
			{
				$register_data['register_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('register_id', $register_id);
		return $this->db->update('registers',$register_data);
	}
	
	function delete($register_id)
	{
		$this->db->where('register_id', $register_id);
		return $this->db->update('registers', array('deleted' => 1));
	}
	
	function get_register_currency_denominations()
	{
		$this->db->from('register_currency_denominations');
		$this->db->order_by('id');
		return $this->db->get();
	}
	
	function save_register_currency_denominations($names, $values)
	{
		$this->db->truncate('register_currency_denominations');
		
		for($k = 0; $k< count($names); $k++)
		{
			$name = $names[$k];
			$value = $values[$k];
			
			$this->db->insert('register_currency_denominations', array('name' => $name, 'value' => (float)$value));
		}
		
		return true;
	}
	
	/**
	 * added for cash register
	 * insert a log for track_cash_log
	 * @param array $data
	 */
	
	function update_register_log($data,$register_id = false) {
		
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		$this->db->where('shift_end','0000-00-00 00:00:00');
		$this->db->where('register_id', $register_id);
		return $this->db->update('register_log', $data) ? true : false;		
	}

	function get_existing_register_log($register_log_id) {

		$this->db->from('register_log');
		$this->db->where('register_log_id',$register_log_id);
		$this->db->where('deleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows())
		return $query->row();
		else
		return false;

	}

	function get_previous_closing_amount($register_id)
	{
		$this->db->from('register_log');
		$this->db->where('register_id',$register_id);
		$this->db->order_by('register_log_id DESC');
		$this->db->where('deleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows())
		return $query->row()->close_amount;
		else
		return 0;
		
	}

	function update_existing_register_log($data, $register_log_id) {
		$this->db->where('register_log_id', $register_log_id);
		return $this->db->update('register_log', $data) ? true : false;
	}


	function insert_register($data) {
		return $this->db->insert('register_log', $data) ? $this->db->insert_id() : false;		
	}
	
	function is_register_log_open()
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();

		$this->db->from('register_log');
		$this->db->where('shift_end','0000-00-00 00:00:00');
		$this->db->where('register_id',$register_id);
		$this->db->where('deleted', 0);
		$query = $this->db->get();
		if($query->num_rows())
		return true	;
		else
		return false;
	
	 }

	function get_current_register_log()
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();

		$this->db->from('register_log');
		$this->db->where('shift_end','0000-00-00 00:00:00');
		$this->db->where('register_id',$register_id);
		$this->db->where('deleted',0);
		
		$query = $this->db->get();
		if($query->num_rows())
		return $query->row();
		else
		return false;
	
	 }
	 
 	function get_register_log_by_id($id)
 	{
 		$register_id = $this->Employee->get_logged_in_employee_current_register_id();

 		$this->db->from('register_log');
 		$this->db->where('shift_end','0000-00-00 00:00:00');
 		$this->db->where('register_id',$id);
		$this->db->where('deleted',0);
		
 		$query = $this->db->get();
 		if($query->num_rows())
 		return $query->row();
 		else
 		return false;
	}
	 
	 
	 function get_register_log($id)
	 {
 		$this->db->select("registers.name as register_name, open_person.first_name as open_first_name, open_person.last_name as open_last_name, close_person.first_name as close_first_name, close_person.last_name as close_last_name, register_log.*, (register_log.close_amount - register_log.open_amount - register_log.cash_sales_amount - register_log.total_cash_additions + register_log.total_cash_subtractions) as difference");
 		$this->db->from('register_log as register_log');
 		$this->db->join('people as open_person', 'register_log.employee_id_open=open_person.person_id');
 		$this->db->join('people as close_person', 'register_log.employee_id_close=close_person.person_id', 'left');
 		$this->db->join('registers', 'registers.register_id = register_log.register_id');
 		$this->db->where('register_log_id', $id);
		
 		$register_log = $this->db->get()->row();
		
		return $register_log;
	 }
	 
	 function get_register_log_details($id)
	 {
		$this->db->select('register_log_audit.*, CONCAT(employee.first_name, " ",employee.last_name) as employee_name', false);
  		$this->db->from('register_log_audit');
 		$this->db->join('people as employee', 'register_log_audit.employee_id=employee.person_id');
  		$this->db->where('register_log_id',$id);
		$this->db->order_by('id');
  		$query = $this->db->get();
  		if($query->num_rows())
  		return $query->result_array();
  		else
  		return false;
	 }
	 
	function insert_audit_log($data)
	{
 	  return $this->db->insert('register_log_audit', $data) ? $this->db->insert_id() : false;		
	}

}
?>
