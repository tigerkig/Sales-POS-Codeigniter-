<?php
class Employee extends Person
{
	/*
	Determines if a given person_id is an employee
	*/
	function exists($person_id)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id',$person_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
		
	function employee_username_exists($username)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.username',$username);
		$query = $this->db->get();
		
		
		if($query->num_rows()==1)
		{
			return $query->row()->username;
		}
	}	
	
	/*
	Returns all the employees
	*/
	function get_all($limit=10000, $offset=0,$col='last_name',$order='asc',$show_inactive=false)
	{	
		$order_by = '';
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by = "ORDER BY ".$col." ". $order;
		}
		
		$inactive = '1=1';
		if (!$show_inactive)
		{
			$inactive = 'inactive=0';
		}
		
		$employees=$this->db->dbprefix('employees');
		$people=$this->db->dbprefix('people');
		$data=$this->db->query("SELECT *,${people}.person_id as pid 
						FROM ".$people."
						JOIN ".$employees." ON 										                       
						".$people.".person_id = ".$employees.".person_id
						WHERE deleted =0 and $inactive $order_by 
						LIMIT  ".$offset.",".$limit);		
						
		return $data;
	}
	
	function count_all()
	{
		$this->db->from('employees');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular employee
	*/
	function get_info($employee_id, $can_cache = TRUE)
	{
		if ($can_cache)
		{
			static $cache = array();
		
			if (isset($cache[$employee_id]))
			{
				return $cache[$employee_id];
			}
		}
		else
		{
			$cache = array();
		}
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('employees.person_id',$employee_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$employee_id] = $query->row();
			return $cache[$employee_id];
		}
		else
		{
			//Get empty base parent object, as $employee_id is NOT an employee
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from employee table
			$fields = $this->db->list_fields('employees');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets information about multiple employees
	*/
	function get_multiple_info($employee_ids)
	{
		$this->db->from('employees');
		$this->db->join('people', 'people.person_id = employees.person_id');		
		$this->db->where_in('employees.person_id',$employee_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}

	
	/*
	Gets information about multiple employees from multiple locations
	*/
	function get_multiple_locations_employees($location_ids)
	{
		$this->db->select('employee_id');
		$this->db->from('employees_locations');
		$this->db->where_in('location_id',$location_ids);
		$this->db->distinct();
		return $this->db->get();		
	}
	
	function save_profile(&$person_data, &$employee_data, $employee_id)
	{
		$success=false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		if(parent::save($person_data,$employee_id))
		{
			if (!$employee_id or !$this->exists($employee_id))
			{
				$employee_data['person_id'] = $employee_id = $person_data['person_id'];
				$success = $this->db->insert('employees',$employee_data);
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees',$employee_data);		
			}	
		}		
		$this->db->trans_complete();		
		return $success;	
	}
	/*
	Inserts or updates an employee
	*/
	function save_employee(&$person_data, &$employee_data,&$permission_data, &$permission_action_data, &$location_data, $employee_id=false)
	{
		$success=false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		if(parent::save($person_data,$employee_id))
		{
			if (!$employee_id or !$this->exists($employee_id))
			{
				$employee_data['person_id'] = $employee_id = $person_data['person_id'];
				$success = $this->db->insert('employees',$employee_data);
			}
			else
			{
				$this->db->where('person_id', $employee_id);
				$success = $this->db->update('employees',$employee_data);		
			}
			
			//We have either inserted or updated a new employee, now lets set permissions. 
			if($success)
			{
				//First lets clear out any permissions the employee currently has.
				$success=$this->db->delete('permissions', array('person_id' => $employee_id));
				
				//Now insert the new permissions
				if($success)
				{
					foreach($permission_data as $allowed_module)
					{
						$success = $this->db->insert('permissions',
						array(
						'module_id'=>$allowed_module,
						'person_id'=>$employee_id));
					}
				}
				
				//First lets clear out any permissions actions the employee currently has.
				$success=$this->db->delete('permissions_actions', array('person_id' => $employee_id));
				
				//Now insert the new permissions actions
				if($success)
				{
					foreach($permission_action_data as $permission_action)
					{
						list($module, $action) = explode('|', $permission_action);
						$success = $this->db->insert('permissions_actions',
						array(
						'module_id'=>$module,
						'action_id'=>$action,
						'person_id'=>$employee_id));
					}
				}
				
				$success=$this->db->delete('employees_locations', array('employee_id' => $employee_id));
				
				//Now insert the new employee locations
				if($success)
				{
					if ($location_data !== FALSE)
					{
						foreach($location_data as $location_id)
						{
							$success = $this->db->insert('employees_locations',
							array(
							'employee_id'=>$employee_id,
							'location_id'=>$location_id
							));
						}
				
					}
				}
				
			}
			
		}
		
		$this->db->trans_complete();		
		return $success;
	}
	
	function set_language($language_id,$employee_id)
	{

		$this->db->where('person_id', $employee_id);
		return $this->db->update('employees', array('language' => $language_id));
	}
	
	function set_language_session($language_id)
	{
		$this->session->set_userdata('language', $language_id);
	}
	/*
	Deletes one employee
	*/
	function delete($employee_id)
	{
		$success=false;
		
		//Don't let employee delete their self
		if($employee_id==$this->get_logged_in_employee_info()->person_id)
			return false;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		$employee_info = $this->Employee->get_info($employee_id);
	
		if ($employee_info->image_id !== NULL)
		{
			$this->load->model('Appfile');
			$this->Person->update_image(NULL,$employee_id);
			$this->Appfile->delete($employee_info->image_id);			
		}			
		
		//Delete permissions
		if($this->db->delete('permissions', array('person_id' => $employee_id)) && $this->db->delete('permissions_actions', array('person_id' => $employee_id)))
		{	
			$this->db->where('person_id', $employee_id);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();		
		return $success;
	}
	
	/*
	Deletes a list of employees
	*/
	function delete_list($employee_ids)
	{
		$success=false;
		
		//Don't let employee delete their self
		if(in_array($this->get_logged_in_employee_info()->person_id,$employee_ids))
			return false;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		foreach($employee_ids as $employee_id)
		{
			$employee_info = $this->Employee->get_info($employee_id);
		
			if ($employee_info->image_id !== NULL)
			{
				$this->load->model('Appfile');
				$this->Person->update_image(NULL,$employee_id);
				$this->Appfile->delete($employee_info->image_id);			
			}			
		}
		
		$this->db->where_in('person_id',$employee_ids);
		//Delete permissions
		if ($this->db->delete('permissions'))
		{
			//delete from employee table
			$this->db->where_in('person_id',$employee_ids);
			$success = $this->db->update('employees', array('deleted' => 1));
		}
		$this->db->trans_complete();		
		return $success;
 	}
	
		
	function check_duplicate($term)
	{
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');	
		$this->db->where('deleted',0);		
		$query = $this->db->where("full_name = ".$this->db->escape($term));
		$query=$this->db->get();
		
		if($query->num_rows()>0)
		{
			return true;
		}	
	}
	
	/*
	Get search suggestions to find employees
	*/
	function get_search_suggestions($search,$limit=5)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
			$this->db->select("first_name, last_name, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
		
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			full_name LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");			
		
			$this->db->limit($limit);	

			$by_name = $this->db->get();
			$temp_suggestions = array();
			foreach($by_name->result() as $row)
			{
				$data = array(
					'name' => $row->first_name.' '.$row->last_name,
					'email' => $row->email,
					'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
					 );
				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->select("first_name, last_name, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('email', $search,'both');			
			$this->db->limit($limit);
		
			$by_email = $this->db->get();
			$temp_suggestions = array();
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.' '.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
			$this->db->select("username, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('username', $search,'both');			
			$this->db->limit($limit);
		
			$by_username = $this->db->get();
			$temp_suggestions = array();
			foreach($by_username->result() as $row)
			{
				$data = array(
						'name' => $row->username,
						'email' => $row->email,
						'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;	
			}

			uasort($temp_suggestions, 'sort_assoc_array_by_name');
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}


			$this->db->select("phone_number, email,image_id,employees.person_id", FALSE);
			$this->db->from('employees');
			$this->db->join('people','employees.person_id=people.person_id');	
			$this->db->where('deleted', 0);
			$this->db->like('phone_number', $search,'both');
			$this->db->limit($limit);
		
			$by_phone = $this->db->get();
			$temp_suggestions = array();
			foreach($by_phone->result() as $row)
			{
				$data = array(
						'name' => $row->phone_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
		
		
		
			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
			{
				if ($this->get_custom_field($k)) 
				{
					$this->load->helper('date');
					if ($this->get_custom_field($k,'type') != 'date')
					{
						$this->db->select('custom_field_'.$k.'_value as custom_field, email,image_id, employees.person_id', false);						
					}
					else
					{
						$this->db->select('FROM_UNIXTIME(custom_field_'.$k.'_value, "'.get_mysql_date_format().'") as custom_field, email,image_id, employees.person_id', false);
					}
					$this->db->from('employees');
					$this->db->join('people','employees.person_id=people.person_id');	
					$this->db->where('deleted',0);
				
					if ($this->get_custom_field($k,'type') != 'date')
					{
						$this->db->like("custom_field_${k}_value",$search,'both');
					}
					else
					{
						$this->db->where("FROM_UNIXTIME(custom_field_${k}_value, '%Y-%m-%d') = ".$this->db->escape(date('Y-m-d', strtotime($search))), NULL, false);					
					}
					$this->db->limit($limit);
					$by_custom_field = $this->db->get();
		
					$temp_suggestions = array();
		
					foreach($by_custom_field->result() as $row)
					{
						$data = array(
								'name' => $row->custom_field,
								'email' => $row->email,
								'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
								);

						$temp_suggestions[$row->person_id] = $data;

					}
			
					uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
					foreach($temp_suggestions as $key => $value)
					{
						$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
					}
				}			
			}
		
		
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);		
			}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	
	}
	
	
	function search($search, $limit=20,$offset=0,$column='last_name',$orderby='asc')
	{		
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');		
		if ($search)
		{
			$custom_fields = array();
			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
			{					
				if ($this->get_custom_field($k) !== FALSE)
				{
					if ($this->get_custom_field($k,'type') != 'date')
					{
						$custom_fields[$k]="custom_field_${k}_value LIKE '%".$this->db->escape_like_str($search)."%'";
					}
					else
					{							
						$custom_fields[$k]= "FROM_UNIXTIME(custom_field_${k}_value, '%Y-%m-%d') = ".$this->db->escape(date('Y-m-d', strtotime($search)));					
					}
					
				}	
			}
			
			if (!empty($custom_fields))
			{				
				$custom_fields = implode(' or ',$custom_fields);
			}
			else
			{
				$custom_fields='1=2';
			}
			
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or $custom_fields or
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				username LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
		}	
		else
		{
			$this->db->where('deleted',0);
		}
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($column, $orderby);
		}
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function search_count_all($search, $limit=10000)
	{
		$this->db->from('employees');
		$this->db->join('people','employees.person_id=people.person_id');		
		if ($search)
		{
			$custom_fields = array();
			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
			{					
				if ($this->get_custom_field($k) !== FALSE)
				{
					if ($this->get_custom_field($k,'type') != 'date')
					{
						$custom_fields[$k]="custom_field_${k}_value LIKE '%".$this->db->escape_like_str($search)."%'";
					}
					else
					{							
						$custom_fields[$k]= "FROM_UNIXTIME(custom_field_${k}_value, '%Y-%m-%d') = ".$this->db->escape(date('Y-m-d', strtotime($search)));					
					}
					
				}	
			}
			
			if (!empty($custom_fields))
			{				
				$custom_fields = implode(' or ',$custom_fields);
			}
			else
			{
				$custom_fields='1=2';
			}
			
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or $custom_fields or
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				username LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
				CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
		}	
		else
		{
			$this->db->where('deleted',0);
		}
		$this->db->limit($limit);
		$result=$this->db->get();				
		return $result->num_rows();
	}
	/*
	Attempts to login employee and set session. Returns boolean based on outcome.
	*/
	function login($username, $password)
	{
		//Username Query
		$query = $this->db->get_where('employees', array('username' => $username,'password'=>md5($password), 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		//Employee Number Query
		$query = $this->db->get_where('employees', array('employee_number' => $username,'password'=>md5($password), 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		return false;
	}
	
	function login_no_password($username)
	{
		//Username Query
		$query = $this->db->get_where('employees', array('username' => $username, 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		//Employee Number Query
		$query = $this->db->get_where('employees', array('employee_number' => $username, 'deleted'=> 0 ,'inactive' => 0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			$this->session->set_userdata('person_id', $row->person_id);
			return true;
		}
		
		return false;
	}
	
	/*
	Logs out a user by destorying all session data and redirect to login
	*/
	function logout($redirect_to_login = TRUE)
	{
		$this->session->sess_destroy();
		
		if ($redirect_to_login)
		{
			redirect('login');
		}
	}
	
	/*
	Determins if a employee is logged in
	*/
	function is_logged_in()
	{
		return $this->session->userdata('person_id')!=false;
	}
	
	/*
	Gets information about the currently logged in employee.
	*/
	function get_logged_in_employee_info()
	{
		if($this->is_logged_in())
		{
			$ret = $this->get_info($this->session->userdata('person_id'));
			if ($this->session->userdata('language'))
			{
				$ret->language = $this->session->userdata('language');
			}
			return $ret;
		}
		
		return false;
	}
	
	/*
	Gets the current employee's location. If they have more than 1, then a user can change during session
	*/
	function get_logged_in_employee_current_location_id()
	{
		if($this->is_logged_in())
		{
			//If we have a location in the session
			if ($this->session->userdata('employee_current_location_id')!==NULL)
			{
				return $this->session->userdata('employee_current_location_id');
			}
			
			//Return the first location user is authenticated for
			return current($this->get_authenticated_location_ids($this->session->userdata('person_id')));
		}
		
		return FALSE;
	}
	
	function get_current_location_info()
	{
		return $this->Location->get_info($this->get_logged_in_employee_current_location_id());
	}
		
	function set_employee_current_location_id($location_id)
	{
		if ($this->is_location_authenticated($location_id))
		{
			$this->session->set_userdata('employee_current_location_id', $location_id);
		}
	}
	
	/*
	Gets the current employee's register id (if set)
	*/
	function get_logged_in_employee_current_register_id()
	{
		if($this->is_logged_in())
		{
			//If we have a register in the session
			if ($this->session->userdata('employee_current_register_id')!==NULL)
			{
				return $this->session->userdata('employee_current_register_id');
			}
			
			return NULL;
		}
		
		return NULL;
	}
	
	function set_employee_current_register_id($register_id)
	{
		$this->session->set_userdata('employee_current_register_id', $register_id);
	}
	
	
	/*
	Determins whether the employee specified employee has access the specific module.
	*/
	function has_module_permission($module_id,$person_id)
	{
		//if no module_id is null, allow access
		if($module_id==null)
		{
			return true;
		}
		
		static $cache;
		
		if (isset($cache[$module_id.'|'.$person_id]))
		{
			return $cache[$module_id.'|'.$person_id];
		}
		
		$query = $this->db->get_where('permissions', array('person_id' => $person_id,'module_id'=>$module_id), 1);
		$cache[$module_id.'|'.$person_id] = $query->num_rows() == 1;
		return $cache[$module_id.'|'.$person_id];
	}
	
	function has_module_action_permission($module_id, $action_id, $person_id)
	{
		//if no module_id is null, allow access
		if($module_id==null)
		{
			return true;
		}
		
		static $cache;
		
		if (isset($cache[$module_id.'|'.$action_id.'|'.$person_id]))
		{
			return $cache[$module_id.'|'.$action_id.'|'.$person_id];
		}
		
		
		$query = $this->db->get_where('permissions_actions', array('person_id' => $person_id,'module_id'=>$module_id,'action_id'=>$action_id), 1);
		$cache[$module_id.'|'.$action_id.'|'.$person_id] =  $query->num_rows() == 1;
		return $cache[$module_id.'|'.$action_id.'|'.$person_id];
	}
	
	function get_employee_by_username_or_email($username_or_email)
	{
		$this->db->from('employees');	
		$this->db->join('people', 'people.person_id = employees.person_id');
		$this->db->where('username',$username_or_email);
		$this->db->or_where('email',$username_or_email);
		$query = $this->db->get();
		
		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		
		return false;
	}
	
	function update_employee_password($employee_id, $password, $force_password_change = 0)
	{
		$employee_data = array('password' => $password, 'force_password_change' => $force_password_change);
		$this->db->where('person_id', $employee_id);
		$success = $this->db->update('employees',$employee_data);
		
		return $success;
	}
		
	function cleanup()
	{
		$employee_data = array('username' => null);
		$this->db->where('deleted', 1);
		return $this->db->update('employees',$employee_data);
	}
		
	function get_employee_id($username)
	{
		$query = $this->db->get_where('employees', array('username' => $username, 'deleted'=>0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			return $row->person_id;
		}
		
		$query = $this->db->get_where('employees', array('employee_number' => $username, 'deleted'=>0), 1);
		if ($query->num_rows() ==1)
		{
			$row=$query->row();
			return $row->person_id;
		}
		
		return false;
	}
	
	function get_authenticated_location_ids($employee_id)
	{
		static $cache;
		
		if (isset($cache[$employee_id]))
		{
			return $cache[$employee_id];
		}
		
		$this->db->select('employees_locations.location_id');
		$this->db->from('employees_locations');
		$this->db->join('locations', 'locations.location_id = employees_locations.location_id');
		$this->db->where('employee_id', $employee_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('location_id', 'asc');
		
		$location_ids = array();
		
		foreach($this->db->get()->result_array() as $location)
		{
			$location_ids[] = $location['location_id'];
		}
		$cache[$employee_id] = $location_ids;
		return $location_ids;
	}
	
	function is_location_authenticated($location_id)
	{
		if ($employee = $this->get_logged_in_employee_info())
		{
			$this->db->select('location_id');
			$this->db->from('employees_locations');
			$this->db->where('employee_id', $employee->person_id);
			$this->db->where('location_id', $location_id);
			$result = $this->db->get();

			return $result->num_rows() == 1;
		}
		
		return FALSE;
	}
	
	function is_employee_authenticated($employee_id, $location_id)
	{
		static $authed_employees;
		
		if (!$authed_employees)
		{
			$this->db->select('employee_id');
			$this->db->from('employees_locations');
			$this->db->where('location_id', $location_id);
			$result = $this->db->get();
			$authed_employees = array();
			
			foreach($result->result_array() as $employee)
			{
				$authed_employees[$employee['employee_id']] = TRUE;
			}	
		}
		return isset($authed_employees[$employee_id]) && $authed_employees[$employee_id]; 
	}
	
	function clock_in($comment, $employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		return $this->db->insert('employees_time_clock', array(
			'employee_id' => $employee_id,
			'location_id' => $location_id,
			'clock_in' => date('Y-m-d H:i:s'),
			'clock_in_comment' => $comment,
			'clock_out_comment' => '',
			'ip_address_clock_in' => $this->input->ip_address(),
		));
		
	}
	
	function clock_out($comment, $employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		$cur_emp_info = $this->get_info($employee_id);
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		if ($this->is_clocked_in($employee_id, $location_id))
		{
			$this->db->limit(1);
			$this->db->where('clock_in !=','0000-00-00 00:00:00');
			$this->db->where('clock_out','0000-00-00 00:00:00');
			$this->db->where('employee_id',$employee_id);
			$this->db->where('location_id',$location_id);
			return $this->db->update('employees_time_clock', array('clock_out' => date('Y-m-d H:i:s'), 'clock_out_comment' => $comment, 'hourly_pay_rate' => $cur_emp_info->hourly_pay_rate,'ip_address_clock_out' => $this->input->ip_address()));
		}
		
		return FALSE;
	}
	
	function is_clocked_in($employee_id = false, $location_id = false)
	{
		if ($employee_id === FALSE)
		{
			$employee_id = $this->get_logged_in_employee_info()->person_id;
		}
		
		if ($location_id === FALSE)
		{
			$location_id = $this->get_logged_in_employee_current_location_id();
		}
		
		$this->db->from('employees_time_clock');
		$this->db->where('clock_in !=','0000-00-00 00:00:00');
		$this->db->where('clock_out','0000-00-00 00:00:00');
		$this->db->where('employee_id',$employee_id);
		$this->db->where('location_id',$location_id);
		
		$query = $this->db->get();
		if($query->num_rows())
		return true	;
		else
		return false;
	
	 }
	 
	 function delete_timeclock($id)
	 {
		 return $this->db->delete('employees_time_clock', array('id' => $id));
	 }
	 
	 function get_timeclock($id)
	 {
 		$this->db->from('employees_time_clock');	
		$this->db->where('id', $id);
 		$query = $this->db->get();
		
 		if($query->num_rows()==1)
 		{
 			return $query->row();
 		}
		else
		{
			//Get empty object
			$timeclock_obj=new stdClass();
			
			//Get all the fields from employee table
			$fields = $this->db->list_fields('employees_time_clock');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$timeclock_obj->$field='';
			}
			
			return $timeclock_obj;
		}
		
		
		return false;
	 }
	 
	function save_timeclock($data)
	{
		$save_data = array();
		
		$clock_in_time = strtotime($data['clock_in']);
		$clock_out_time = strtotime($data['clock_out']);
		
		if ($clock_in_time !== FALSE)
		{
			$save_data['clock_in'] = date('Y-m-d H:i:s', $clock_in_time);
		}
		
		if ($clock_out_time !== FALSE)
		{
			$save_data['clock_out'] = date('Y-m-d H:i:s', $clock_out_time);
		}
		
		$save_data['employee_id'] = $data['employee_id'];
		$save_data['location_id'] = $data['location_id'];
		$save_data['clock_in_comment'] = $data['clock_in_comment'];
		$save_data['clock_out_comment'] = $data['clock_out_comment'];
		$save_data['hourly_pay_rate'] = $data['hourly_pay_rate'];
		if ($this->exists($save_data['employee_id']))
		{
			if ($data['id'] == -1)
			{
				return $this->db->insert('employees_time_clock', $save_data);
			}
			else
			{
				$this->db->where('id', $data['id']);
				return $this->db->update('employees_time_clock', $save_data);
			}
		}	
		
		return FALSE;
	}

	function save_message($data)
	{
		$message_data = array(
		'message'=>$data['message'],
		'created_at' => date('Y-m-d H:i:s'),
		'sender_id'=>$this->get_logged_in_employee_info()->person_id,
		);
		

			if($this->db->insert('messages', $message_data))
			{
				$message_id = $this->db->insert_id();


				if($data['all_employees']=="all")
				{
					
					if($data["all_locations"]=="all")
					{
						$employee_ids = array();

						foreach ($this->Location->get_all()->result() as $location)
						{
							$location_ids[] = $location->location_id;
						}

						$employee_ids = $this->get_multiple_locations_employees($location_ids)->result_array();

					}
					else
					{
						$employee_ids = $this->get_multiple_locations_employees($data['locations'])->result_array();

					}

					//Prepare the employees ids format 
					$person_ids = array();
					foreach ($employee_ids as $value) {

						$message_receiver = array(
						'message_id'=>$message_id,
						'receiver_id'=>$value['employee_id'],
					);	
						
						$this->db->insert('message_receiver',$message_receiver);		

					}

					return true;

				}
				else
				{
					foreach ($data["employees"] as $employee_id) {
							$message_receiver = array(
								'message_id'=>$message_id,
								'receiver_id'=>$employee_id,
							);	
								
								$this->db->insert('message_receiver',$message_receiver);	
					}

					return true;
				}

				return false;

				
			}
		
		
	}

	function get_messages($limit=20, $offset=0)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;

		$this->db->from('messages');
		$this->db->join('message_receiver','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->limit($limit,$offset);		
		$this->db->where('messages.deleted',0);		
		$this->db->order_by("created_at", "desc");
		$this->db->limit($limit);
		$this->db->offset($offset);
		$query=$this->db->get();

		return $query->result_array();
	}

	function get_messages_count()
	{
		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		
		$this->db->from('messages');
		$this->db->join('message_receiver','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);
		
		return $this->db->count_all_results();
	}
	
	function get_sent_messages($limit=20, $offset=0)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->select('messages.*, GROUP_CONCAT('.$this->db->dbprefix('people').'.first_name, " ",'.$this->db->dbprefix('people').'.last_name SEPARATOR ", ") as sent_to', false);
		$this->db->from('messages');
		$this->db->join('message_receiver', 'message_receiver.message_id = messages.id');
		$this->db->join('people', 'people.person_id = message_receiver.receiver_id');
		$this->db->where('sender_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);		
		$this->db->order_by("created_at", "desc");
		$this->db->group_by('messages.id');
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		$query=$this->db->get();
		return $query->result_array();
	}
	
	function get_sent_messages_count()
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->from('messages');
		$this->db->where('sender_id',$logged_employee_id);		
		$this->db->where('messages.deleted',0);		
		
		return $this->db->count_all_results();
	}

	function get_unread_messages_count($limit=20, $offset=0)
	{
		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->from('message_receiver');
		$this->db->join('messages','messages.id=message_receiver.message_id');	
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('message_read',0);		
		$this->db->where('deleted',0);
		$this->db->limit($limit);
		$this->db->offset($offset);
		
		return $this->db->count_all_results();
	}	 

	function read_message($message_id)
	{

		$logged_employee_id = $this->get_logged_in_employee_info()->person_id;
		$this->db->where('receiver_id',$logged_employee_id);		
		$this->db->where('id', $message_id);
		return $this->db->update('message_receiver', array('message_read' => 1));		
	}

	function delete_message($message_id)
	{
		$this->db->where('id', $message_id);
		return $this->db->update('messages', array('deleted' => 1));		
	}
	
	function get_supplier_columns_to_display()
	{
		$all_columns = $this->Supplier->get_displayable_columns();
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('supplier_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->Supplier->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
	
		return $columns_to_display;
	}
	
	function get_customer_columns_to_display()
	{
		$all_columns = $this->Customer->get_displayable_columns();
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('customer_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->Customer->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
	
		return $columns_to_display;
		
	}
	
	function get_sale_order_columns_to_display()
	{
		$this->load->model('Delivery');
		$all_columns = $this->Delivery->get_displayable_columns();
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('sale_orders_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->Delivery->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
	
		return $columns_to_display;
		
	}
	
	function get_item_columns_to_display()
	{
		static $has_cost_price_permission;
		
		if (!$has_cost_price_permission)
		{
			$has_cost_price_permission = $this->has_module_action_permission('items','see_cost_price', $this->get_logged_in_employee_info()->person_id);
		}
		
		$this->load->model('Item');
		
		$all_columns = $this->Item->get_displayable_columns();
		
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('item_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->Item->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
		
		if (!$has_cost_price_permission)
		{
			if (isset($columns_to_display['cost_price']))
			{
				unset($columns_to_display['cost_price']);
			}
		}
		
		return $columns_to_display;
	}
	
	function get_item_kit_columns_to_display()
	{
		static $has_cost_price_permission;
		
		if (!$has_cost_price_permission)
		{
			$has_cost_price_permission = $this->has_module_action_permission('items','see_cost_price', $this->get_logged_in_employee_info()->person_id);
		}
		
		$this->load->model('Item_kit');
		
		$all_columns = $this->Item_kit->get_displayable_columns();
		
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('item_kit_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->Item_kit->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
		
		if (!$has_cost_price_permission)
		{
			if (isset($columns_to_display['cost_price']))
			{
				unset($columns_to_display['cost_price']);
			}
		}
		
		return $columns_to_display;
	}
	
	
	function get_employee_columns_to_display()
	{
		
		$all_columns = $this->get_displayable_columns();
		
		$columns_to_display = array();
		
		$this->load->model('Employee_appconfig');
		if ($choices = $this->Employee_appconfig->get('employee_column_prefs'))
		{
			$columns_to_display_keys = unserialize($choices);
		}
		else
		{
			$columns_to_display_keys = $this->get_default_columns();

		}
		
		foreach($columns_to_display_keys as $key)
		{
			$columns_to_display[$key] = $all_columns[$key];
		}
	
		return $columns_to_display;
	}
	
	function get_custom_field($number,$key="name")
	{
		static $config_data;
		
		if (!$config_data)
		{
			$config_data = unserialize($this->config->item('employee_custom_field_prefs'));
		}
		
		return isset($config_data["custom_field_${number}_${key}"]) && $config_data["custom_field_${number}_${key}"] ? $config_data["custom_field_${number}_${key}"] : FALSE;
	}
	
	
	function get_displayable_columns()
	{
		$columns = array(
			'person_id' => 											array('sort_column' => $this->db->dbprefix('people').'.person_id', 'label' => lang('common_person_id')),
			'full_name' => 											array('sort_column' => $this->db->dbprefix('people').'.full_name','label' => lang('common_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'first_name' => 										array('sort_column' => $this->db->dbprefix('people').'.first_name','label' => lang('common_first_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'last_name' => 											array('sort_column' => $this->db->dbprefix('people').'.last_name','label' => lang('common_last_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'email' => 													array('sort_column' => $this->db->dbprefix('people').'.email','label' => lang('common_email'),'format_function' => 'email_formatter','html' => TRUE),
			'username' => 											array('sort_column' => $this->db->dbprefix('employees').'.username','label' => lang('common_username')),
			'employee_number' => 								array('sort_column' => $this->db->dbprefix('employees').'.employee_number','label' => lang('common_employees_number')),
			'hire_date' => 											array('sort_column' => $this->db->dbprefix('employees').'.hire_date','label' => lang('employees_hire_date'),'format_function' => 'date_as_display_date'),
			'birthday' => 											array('sort_column' => $this->db->dbprefix('employees').'.birthday','label' => lang('employees_birthday'),'format_function' => 'date_as_display_date'),
			'phone_number' => 									array('sort_column' => $this->db->dbprefix('people').'.phone_number','label' => lang('common_phone_number')),
			'comments' => 											array('sort_column' => $this->db->dbprefix('people').'.comments','label' => lang('common_comments')),
			'address_1' => 											array('sort_column' => $this->db->dbprefix('people').'.address_1','label' => lang('common_address_1')),
			'address_2' => 											array('sort_column' => $this->db->dbprefix('people').'.address_2','label' => lang('common_address_2')),
			'city' => 													array('sort_column' => $this->db->dbprefix('people').'.city','label' => lang('common_city')),
			'state' => 													array('sort_column' => $this->db->dbprefix('people').'.state','label' => lang('common_state')),
			'zip' => 														array('sort_column' => $this->db->dbprefix('people').'.zip','label' => lang('common_zip')),
			'country' => 												array('sort_column' => $this->db->dbprefix('people').'.country','label' => lang('common_country')),
			'force_password_change' => 					array('sort_column' => $this->db->dbprefix('employees').'.force_password_change','label' => lang('employees_force_password_change_upon_login'),'format_function' => 'boolean_as_string'),
			'always_require_password' => 				array('sort_column' => $this->db->dbprefix('employees').'.always_require_password','label' => lang('employees_always_require_password'),'format_function' => 'boolean_as_string'),
			'inactive' => 											array('sort_column' => $this->db->dbprefix('employees').'.inactive','label' => lang('employees_inactive'),'format_function' => 'boolean_as_string'),
			'reason_inactive' => 											array('sort_column' => $this->db->dbprefix('employees').'.reason_inactive','label' => lang('employees_reason_inactive')),
			'language' => 											array('sort_column' => $this->db->dbprefix('employees').'.language','label' => lang('common_language'),'format_function' => 'ucwords'),
			'commission_percent' => 						array('sort_column' => $this->db->dbprefix('employees').'.commission_percent','label' => lang('common_commission_default_rate'),'format_function' => 'to_quantity'),
			'commission_percent_type' => 				array('sort_column' => $this->db->dbprefix('employees').'.commission_percent_type','label' => lang('common_commission_default_rate'),'format_function' => 'commission_percent_type_formater'),
			'hourly_pay_rate' => 								array('sort_column' => $this->db->dbprefix('employees').'.hourly_pay_rate','label' => lang('common_hourly_pay_rate'),'format_function' => 'to_currency'),			
			'not_required_to_clock_in' => 								array('sort_column' => $this->db->dbprefix('employees').'.not_required_to_clock_in','label' => lang('employees_not_required_to_clock_in'),'format_function' => 'boolean_as_string'),			
		);
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if($this->get_custom_field($k) !== false)
			{
				$field = array();
				$field['sort_column'] = $this->db->dbprefix('employees').".custom_field_${k}_value";
				$field['label']= $this->get_custom_field($k);
			
				if ($this->get_custom_field($k,'type') == 'checkbox')
				{
					$format_function = 'boolean_as_string';
				}
				elseif($this->get_custom_field($k,'type') == 'date')
				{
					$format_function = 'date_as_display_date';				
				}
				elseif($this->get_custom_field($k,'type') == 'email')
				{
					$this->load->helper('url');
					$format_function = 'mailto';					
					$field['html'] = TRUE;
				}
				elseif($this->get_custom_field($k,'type') == 'url')
				{
					$this->load->helper('url');
					$format_function = 'anchor_or_blank';					
					$field['html'] = TRUE;
				}
				elseif($this->get_custom_field($k,'type') == 'phone')
				{
					$this->load->helper('url');
					$format_function = 'tel';					
					$field['html'] = TRUE;
				}
				else
				{
					$format_function = 'strsame';
				}
				$field['format_function'] = $format_function;
				$columns["custom_field_${k}_value"] = $field;
			}
		}
		
		return $columns;
		
	}
	
	function get_default_columns()
	{
		return array('person_id','full_name','email','phone_number');
	}
	
}
?>
