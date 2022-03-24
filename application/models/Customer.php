<?php
class Customer extends Person
{	
	/*
	Determines if a given person_id is a customer
	*/
	function exists($person_id)
	{
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id',$person_id);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}

	function account_number_exists($account_number)
	{
		$this->db->from('customers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function customer_id_from_account_number($account_number)
	{
		$this->db->from('customers');	
		$this->db->where('account_number',$account_number);
		$query = $this->db->get();
		
		if ($query->num_rows()==1)
		{
			return $query->row()->person_id;
		}
		
		return false;
	}
	
	/*
	Returns all the customers
	*/
	function get_all($limit=10000, $offset=0,$col='last_name',$order='asc')
	{
		
		$order_by = '';
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by="ORDER BY ".$col." ". $order;
		}
		
		$people=$this->db->dbprefix('people');
		$customers=$this->db->dbprefix('customers');
		$data=$this->db->query("SELECT *,${people}.person_id as pid 
						FROM ".$people."
						STRAIGHT_JOIN ".$customers." ON 										                       
						".$people.".person_id = ".$customers.".person_id
						WHERE deleted =0 $order_by 
						LIMIT  ".$offset.",".$limit);		
						
		return $data;
	}
	
	function count_all()
	{
		$this->db->from('customers');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}
	
	/*
	Gets information about a particular customer
	*/
	function get_info($customer_id,$can_cache = FALSE)
	{
		if ($can_cache)
		{
			static $cache  = array();
		
			if (isset($cache[$customer_id]))
			{
				return $cache[$customer_id];
			}
		}
		else
		{
			$cache = array();
		}
		$this->db->from('customers');	
		$this->db->join('people', 'people.person_id = customers.person_id');
		$this->db->where('customers.person_id',$customer_id);
		$query = $this->db->get();
		
		if($query->num_rows()==1)
		{
			$cache[$customer_id] = $query->row();
			return $cache[$customer_id];
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT an customer
			$person_obj=parent::get_info(-1);
			
			//Get all the fields from customer table
			$fields = $this->db->list_fields('customers');
			
			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets information about multiple customers
	*/
	function get_multiple_info($customer_ids)
	{
		$this->db->from('customers');
		$this->db->join('people', 'people.person_id = customers.person_id');		
		$this->db->where_in('customers.person_id',$customer_ids);
		$this->db->order_by("last_name", "asc");
		return $this->db->get();		
	}
	
	/*
	Inserts or updates a customer
	*/
	function save_customer(&$person_data, &$customer_data,$customer_id=false)
	{
		$success=false;
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		if(parent::save($person_data,$customer_id))
		{
			if ($customer_id && $this->exists($customer_id))
			{
				$cust_info = $this->get_info($customer_id);
				
				$current_balance = $cust_info->balance;
				
				//Insert store balance transaction when manually editing
				if (isset($customer_data['balance']) && $customer_data['balance'] != $current_balance)
				{
		 			$store_account_transaction = array(
		   		'customer_id'=>$customer_id,
		   		'sale_id'=>NULL,
					'comment'=>lang('common_manual_edit_of_balance'),
		      'transaction_amount'=>$customer_data['balance'] - $current_balance,
					'balance'=>$customer_data['balance'],
					'date' => date('Y-m-d H:i:s')
					);
					
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			}
						
			if (!$customer_id or !$this->exists($customer_id))
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('customers',$customer_data);
				if(!$success)
				{
					unset($customer_data['person_id']);
					unset($person_data['person_id']);
				}		
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$success = $this->db->update('customers',$customer_data);
			}			
		}
		
		$this->db->trans_complete();
		return $success;
	}
	
	/*
	Deletes one customer
	*/
	function delete($customer_id)
	{
		$customer_info = $this->Customer->get_info($customer_id);
	
		if ($customer_info->image_id !== NULL)
		{
			$this->load->model('Appfile');
			$this->Person->update_image(NULL,$customer_id);
			$this->Appfile->delete($customer_info->image_id);			
		}			
		
		$this->db->where('person_id', $customer_id);
		return $this->db->update('customers', array('deleted' => 1));
	}
	
	/*
	Deletes a list of customers
	*/
	function delete_list($customer_ids)
	{
		foreach($customer_ids as $customer_id)
		{
			$customer_info = $this->Customer->get_info($customer_id);
		
			if ($customer_info->image_id !== NULL)
			{
				$this->Person->update_image(NULL,$customer_id);
				$this->load->model('Appfile');
				$this->Appfile->delete($customer_info->image_id);			
			}			
		}
		
		$this->db->where_in('person_id',$customer_ids);
		return $this->db->update('customers', array('deleted' => 1));
 	}
	
	function check_duplicate($name,$email,$phone_number)
	{
		if (!$email)
		{
			//Set to an email no one would have
			$email = 'no-reply@mg.phppointofsale.com';
		}
		
		if(!$phone_number)
		{
			//Set to phone number no one would have
			$phone_number = '555-555-5555';
		}
		
		$this->db->from('customers');
		$this->db->join('people','customers.person_id=people.person_id');	
		$this->db->where('deleted',0);		
		$this->db->where("full_name = ".$this->db->escape($name).' or email='.$this->db->escape($email).' or phone_number='.$this->db->escape($phone_number));
		$query=$this->db->get();
		if($query->num_rows()>0)
		{
			return true;
		}
		
		return false;
	}
	
	function get_customer_search_suggestions($search,$limit=25)
	{
		
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
		
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			full_name LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");			
		
			$this->db->limit($limit);	
			$by_name = $this->db->get();
			$temp_suggestions = array();
		
			foreach($by_name->result() as $row)
			{
				$name_label = $row->first_name.' '.$row->last_name.' ('.$row->person_id.')';
				
				if ($row->phone_number)
				{
					$name_label.=' ('.$row->phone_number.')';
				}
				
				$data = array(
					'name' => $name_label,
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
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("account_number",$search,'both');
			$this->db->limit($limit);
			$by_account_number = $this->db->get();
		
		
			$temp_suggestions = array();
		
			foreach($by_account_number->result() as $row)
			{
				$data = array(
						'name' => $row->account_number,
						'email' => $row->email,
						'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;

			}
			
			for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
			{
				if ($this->get_custom_field($k)) 
				{
					$this->load->helper('date');
					if ($this->get_custom_field($k,'type') != 'date')
					{
						$this->db->select('custom_field_'.$k.'_value as custom_field, email,image_id, customers.person_id', false);						
					}
					else
					{
						$this->db->select('FROM_UNIXTIME(custom_field_'.$k.'_value, "'.get_mysql_date_format().'") as custom_field, email,image_id, customers.person_id', false);
					}
					$this->db->from('customers');
					$this->db->join('people','customers.person_id=people.person_id');	
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
		
			
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("email",$search,'both');
			$this->db->limit($limit);
			$by_email = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_email->result() as $row)
			{
				$data = array(
						'name' => $row->first_name.'&nbsp;'.$row->last_name,
						'email' => $row->email,
						'avatar' => $row->image_id ?  app_file_url($row->image_id) : base_url()."assets/img/user.png" 
						);

				$temp_suggestions[$row->person_id] = $data;
			}
		
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['email'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
			
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("phone_number",$search,'both');
			$this->db->limit($limit);
			$by_phone_number = $this->db->get();
		
		
			$temp_suggestions = array();
		
			foreach($by_phone_number->result() as $row)
			{
				$data = array(
						'name' => $row->phone_number,
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
		
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			$this->db->where('deleted',0);		
			$this->db->like("company_name",$search,'both');
			$this->db->limit($limit);
			$by_company_name = $this->db->get();
		
			$temp_suggestions = array();
		
			foreach($by_company_name->result() as $row)
			{
				$data = array(
						'name' => $row->company_name,
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
		
		//Cleanup blank entries
		for($k=count($suggestions)-1;$k>=0;$k--)
		{
			if (!$suggestions[$k]['label'])
			{
				unset($suggestions[$k]);
			}
		}
		
		//Probably not needed; but doesn't hurt
		$suggestions = array_values($suggestions);
		
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
	/*
	Preform a search on customers
	*/
	function search($search, $limit=20,$offset=0,$column='last_name',$orderby='asc')
	{
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');	
			
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
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`last_name`,', ',`first_name`) LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`last_name`,', ',`first_name`, ' (',".$this->db->dbprefix('customers').".person_id,')') LIKE '%".$this->db->escape_like_str($search)."%'
					) and deleted=0");		
			}
			else
			{
				$this->db->where('deleted',0);
			}	
						
			if (!$this->config->item('speed_up_search_queries'))
			{
				$this->db->order_by($column,$orderby);				
			}
			$this->db->limit($limit);
			$this->db->offset($offset);
			return $this->db->get();
	}
	
	function search_count_all($search, $limit=10000)
	{
			$this->db->from('customers');
			$this->db->join('people','customers.person_id=people.person_id');		

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
					email LIKE '%".$this->db->escape_like_str($search)."%' or 
					phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					account_number LIKE '%".$this->db->escape_like_str($search)."%' or 
					company_name LIKE '%".$this->db->escape_like_str($search)."%' or 
					CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");		
			}
			else
			{
				$this->db->where('deleted',0);
			}

			$this->db->limit($limit);
			$result=$this->db->get();				
			return $result->num_rows();
	}
		
	function cleanup()
	{
		$customer_data = array('account_number' => null);
		$this->db->where('deleted', 1);
		return $this->db->update('customers',$customer_data);
	}
	
	function get_displayable_columns()
	{
		$columns = array(
			'person_id' => 											array('sort_column' => $this->db->dbprefix('people').'.person_id', 'label' => lang('common_person_id')),
			'full_name' => 											array('sort_column' => $this->db->dbprefix('people').'.full_name','label' => lang('common_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'first_name' => 										array('sort_column' => $this->db->dbprefix('people').'.first_name','label' => lang('common_first_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'last_name' => 											array('sort_column' => $this->db->dbprefix('people').'.last_name','label' => lang('common_last_name'),'data_function' => 'customer_name_data_function','format_function' => 'customer_name_formatter','html' => TRUE),
			'company_name' => 									array('sort_column' => $this->db->dbprefix('customers').'.company_name','label' => lang('common_company')),
			'account_number' => 								array('sort_column' => $this->db->dbprefix('customers').'.account_number','label' => lang('customers_account_number')),
			'email' => 													array('sort_column' => $this->db->dbprefix('people').'.email','label' => lang('common_email'),'format_function' => 'email_formatter','html' => TRUE),
			'phone_number' => 									array('sort_column' => $this->db->dbprefix('people').'.phone_number','label' => lang('common_phone_number')),
			'comments' => 											array('sort_column' => $this->db->dbprefix('people').'.comments','label' => lang('common_comments')),
			'balance' => 												array('sort_column' => $this->db->dbprefix('customers').'.balance','label' => lang('common_balance'),'data_function' => 'customer_balance_data','format_function' => 'customer_balance_formatter','html' => TRUE),
			'credit_limit' => 									array('sort_column' => $this->db->dbprefix('customers').'.credit_limit','label' => lang('common_credit_limit'),'format_function' => 'to_currency'),
			'disable_loyalty' => 								array('sort_column' => $this->db->dbprefix('customers').'.disable_loyalty','label' => lang('common_disable_loyalty'),'format_function' => 'boolean_as_string'),
			'points' => 												array('sort_column' => $this->db->dbprefix('customers').'.points','label' => lang('common_points'),'format_function' => 'to_quantity'),
			'current_spend_for_points' => 			array('sort_column' => $this->db->dbprefix('customers').'.current_spend_for_points','label' => lang('customers_amount_to_spend_for_next_point'),'format_function' => 'amount_to_spend_for_next_point_formatter', 'data_function' => 'amount_to_spend_for_next_point_data'),
			'current_sales_for_discount' => 		array('sort_column' => $this->db->dbprefix('customers').'.current_sales_for_discount','label' => lang('common_sales_until_discount'),'format_function' => 'sales_until_discount_formatter', 'data_function' => 'sales_until_discount_data'),
			'address_1' => 											array('sort_column' => $this->db->dbprefix('people').'.address_1','label' => lang('common_address_1')),
			'address_2' => 											array('sort_column' => $this->db->dbprefix('people').'.address_2','label' => lang('common_address_2')),
			'city' => 													array('sort_column' => $this->db->dbprefix('people').'.city','label' => lang('common_city')),
			'state' => 													array('sort_column' => $this->db->dbprefix('people').'.state','label' => lang('common_state')),
			'zip' => 														array('sort_column' => $this->db->dbprefix('people').'.zip','label' => lang('common_zip')),
			'country' => 												array('sort_column' => $this->db->dbprefix('people').'.country','label' => lang('common_country')),
			'override_default_tax' => 					array('sort_column' => $this->db->dbprefix('customers').'.override_default_tax','label' => lang('customers_override_default_tax_for_sale'),'format_function' => 'boolean_as_string'),			
			'taxable' => 												array('sort_column' => $this->db->dbprefix('customers').'.taxable','label' => lang('common_taxable'),'format_function' => 'boolean_as_string'),			
		);
		
		for($k=1;$k<=NUMBER_OF_PEOPLE_CUSTOM_FIELDS;$k++)
		{
			if($this->Customer->get_custom_field($k) !== false)
			{
				$field = array();
				$field['sort_column'] = $this->db->dbprefix('customers').".custom_field_${k}_value";
				$field['label']= $this->Customer->get_custom_field($k);
			
				if ($this->Customer->get_custom_field($k,'type') == 'checkbox')
				{
					$format_function = 'boolean_as_string';
				}
				elseif($this->Customer->get_custom_field($k,'type') == 'date')
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
	
	function get_custom_field($number,$key="name")
	{
		static $config_data;
		
		if (!$config_data)
		{
			$config_data = unserialize($this->config->item('customer_custom_field_prefs'));
		}
		
		return isset($config_data["custom_field_${number}_${key}"]) && $config_data["custom_field_${number}_${key}"] ? $config_data["custom_field_${number}_${key}"] : FALSE;
	}
	
}
?>
