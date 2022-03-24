<?php
class Giftcard extends CI_Model
{
	function __construct()
	{
		parent::__construct('config');
		$this->lang->load('giftcards');		
	}
	
	/*
	Determines if a given giftcard_id is an giftcard
	*/
	function exists( $giftcard_id )
	{
		$this->db->from('giftcards');
		$this->db->where('giftcard_id',$giftcard_id);
		$this->db->where('deleted',0);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function is_inactive($giftcard_id)
	{
		$info = $this->get_info($giftcard_id);
		
		return $info->inactive;
	}
	/*
	Returns all the giftcards
	*/
	function get_all($limit=10000,$offset=0,$col='giftcard_number',$order='asc')
	{
        $this->db->select('giftcards.*,people.*,customers.account_number as account_number');
		$this->db->from('giftcards');
		$this->db->join('people','people.person_id = giftcards.customer_id', 'left');
        $this->db->join('customers','people.person_id = customers.person_id', 'left');

		$this->db->where('giftcards.deleted',0);
		
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}

	function get_customer_giftcards($customer_id)
	{
		$this->db->from('giftcards');
		$this->db->where('customer_id',$customer_id);
		$this->db->where('deleted',0);
		$this->db->where('inactive',0);
		$this->db->where('value > ',0);
		$this->db->order_by('value', 'desc');
		$this->db->limit(20);

		$query =  $this->db->get();
		return $query->result();
	}
	
	function count_all()
	{
		$this->db->from('giftcards');
		$this->db->where('deleted',0);
		return $this->db->count_all_results();
	}

	/*
	Gets information about a particular giftcard
	*/
	function get_info($giftcard_id)
	{
		$this->db->from('giftcards');
		$this->db->where('giftcard_id',$giftcard_id);
		$this->db->where('deleted',0);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $giftcard_id is NOT an giftcard
			$giftcard_obj=new stdClass();

			//Get all the fields from giftcards table
			$fields = $this->db->list_fields('giftcards');

			foreach ($fields as $field)
			{
				$giftcard_obj->$field='';
			}

			return $giftcard_obj;
		}
	}

	/*
	Get an giftcard id given an giftcard number
	*/
	function get_giftcard_id($giftcard_number,$deleted=false)
	{
		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$giftcard_number);
		if(!$deleted)
		{
			$this->db->where('deleted',0);
		}
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row()->giftcard_id;
		}

		return false;
	}

	/*
	Gets information about multiple giftcards
	*/
	function get_multiple_info($giftcard_ids)
	{
		$this->db->from('giftcards');
		$this->db->where_in('giftcard_id',$giftcard_ids);
		$this->db->where('deleted',0);
		$this->db->order_by("giftcard_number", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates a giftcard
	*/
	function save(&$giftcard_data,$giftcard_id=false)
	{
		if (!$giftcard_id or !$this->exists($giftcard_id))
		{
			if($this->db->insert('giftcards',$giftcard_data))
			{
				$giftcard_data['giftcard_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('giftcard_id', $giftcard_id);
		return $this->db->update('giftcards',$giftcard_data);
	}

	/*
	Updates multiple giftcards at once
	*/
	function update_multiple($giftcard_data,$giftcard_ids)
	{
		$this->db->where_in('giftcard_id',$giftcard_ids);
		return $this->db->update('giftcards',$giftcard_data);
	}

	/*
	Deletes one giftcard
	*/
	function delete($giftcard_id)
	{
		$this->db->where('giftcard_id', $giftcard_id);
		return $this->db->update('giftcards', array('deleted' => 1, 'giftcard_number' => NULL));
	}
	
	/*
	Deletes a list of giftcards
	*/
	function delete_list($giftcard_ids)
	{
		$this->db->where_in('giftcard_id',$giftcard_ids);
		return $this->db->update('giftcards', array('deleted' => 1, 'giftcard_number' => NULL));
 	}

	/*
	Get search suggestions to find giftcards
	*/
	function get_search_suggestions($search,$limit=25)
	{
		if (!trim($search))
		{
			return array();
		}
		
		$suggestions = array();
		
			$this->db->from('giftcards');
			$this->db->like('giftcard_number', $search,'both');
			$this->db->or_like('description', $search,'both');
			$this->db->where('deleted',0);
			$this->db->limit($limit);
			$by_number = $this->db->get();
		
			$temp_suggestions = array();
			foreach($by_number->result() as $row)
			{
				$data = array(
						'name' => H($row->giftcard_number),
						'email' => to_currency(H($row->value)),
						'avatar' => base_url()."assets/img/giftcard.png" 
						);

				$temp_suggestions[$row->giftcard_id] = $data;
			}
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');

			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['email']);
			}
		
			$this->db->from('giftcards');
			$this->db->join('people','giftcards.customer_id=people.person_id');	
		
			$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
			CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->db->escape_like_str($search)."%') and ".$this->db->dbprefix('giftcards').".deleted=0");
		
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

				$temp_suggestions[$row->giftcard_id] = $data;
			}
		
			uasort($temp_suggestions, 'sort_assoc_array_by_name');

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

	/*
	Preform a search on giftcards
	*/
	function search($search, $limit=20,$offset=0,$column="giftcard_number",$orderby='asc')
	{
	 //The queries are done as 2 unions to speed up searches to use indexes.
	 //When doing OR WHERE across 2 tables; performance is not good
    $this->db->select('giftcards.*,people.*,customers.account_number as account_number');
		$this->db->from('giftcards');
		$this->db->join('people','giftcards.customer_id=people.person_id', 'left');	
		$this->db->join('customers','people.person_id = customers.person_id', 'left');
		
		if ($search)
		{
				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
				email LIKE '%".$this->db->escape_like_str($search)."%' or 
				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				full_name LIKE '%".$this->db->escape_like_str($search)."%') and giftcards.deleted=0");		
		}
		else
		{
			$this->db->where('giftcards.deleted',0);
		}	
					
		$people_search = $this->db->get_compiled_select();

    $this->db->select('giftcards.*,people.*,customers.account_number as account_number');
 		$this->db->from('giftcards');
 		$this->db->join('people','giftcards.customer_id=people.person_id', 'left');	
 		$this->db->join('customers','people.person_id = customers.person_id', 'left');
		
		if ($search)
		{
				$this->db->where("(giftcard_number LIKE '%".$this->db->escape_like_str($search)."%' or 
				description LIKE '%".$this->db->escape_like_str($search)."%') and giftcards.deleted=0");		
		}
		else
		{
			$this->db->where('gifcards.deleted',0);
		}	
		
		$giftcard_search = $this->db->get_compiled_select();
		
		$order_by = '';
		if (!$this->config->item('speed_up_search_queries'))
		{
			$order_by = " ORDER BY $column $orderby ";
		}			
		
		return $this->db->query($people_search." UNION ".$giftcard_search." $order_by LIMIT $limit OFFSET $offset");	
	}
	
	function search_count_all($search, $limit=10000)
	{
 	 //The queries are done as 2 unions to speed up searches to use indexes.
 	 //When doing OR WHERE across 2 tables; performance is not good
     $this->db->select('giftcards.*,people.*,customers.account_number as account_number');
 		$this->db->from('giftcards');
 		$this->db->join('people','giftcards.customer_id=people.person_id', 'left');	
 		$this->db->join('customers','people.person_id = customers.person_id', 'left');
		
 		if ($search)
 		{
 				$this->db->where("(first_name LIKE '%".$this->db->escape_like_str($search)."%' or 
 				last_name LIKE '%".$this->db->escape_like_str($search)."%' or 
 				email LIKE '%".$this->db->escape_like_str($search)."%' or 
 				phone_number LIKE '%".$this->db->escape_like_str($search)."%' or 
 				full_name LIKE '%".$this->db->escape_like_str($search)."%') and giftcards.deleted=0");		
 		}
 		else
 		{
 			$this->db->where('giftcards.deleted',0);
 		}	
					
 		$people_search = $this->db->get_compiled_select();

     $this->db->select('giftcards.*,people.*,customers.account_number as account_number');
  		$this->db->from('giftcards');
  		$this->db->join('people','giftcards.customer_id=people.person_id', 'left');	
  		$this->db->join('customers','people.person_id = customers.person_id', 'left');
		
 		if ($search)
 		{
 				$this->db->where("(giftcard_number LIKE '%".$this->db->escape_like_str($search)."%' or 
 				description LIKE '%".$this->db->escape_like_str($search)."%') and giftcards.deleted=0");		
 		}
 		else
 		{
 			$this->db->where('giftcards.deleted',0);
 		}	
		
 		$giftcard_search = $this->db->get_compiled_select();
		
 		$result = $this->db->query($people_search." UNION ".$giftcard_search);	
		return $result->num_rows();
	}
	
	public function get_giftcard_value( $giftcard_number )
	{
		if ( !$this->exists( $this->get_giftcard_id($giftcard_number)))
			return 0;
		
		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$giftcard_number);
		return $this->db->get()->row()->value;
	}
	
	function update_giftcard_value( $giftcard_number, $value )
	{
		$this->db->where('giftcard_number', $giftcard_number);
		$this->db->update('giftcards', array('value' => $value));
	}
	
	function log_modification($data)
	{		
		$transaction_amount = $data['new_value'] - $data['old_value'];;
				
		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$data['number']);
		$row = $this->db->get()->row_array();
		
		if($data['type'] == "sale")
		{
			$spent = to_currency($transaction_amount);
			$new_value = to_currency($row['value']);
			$log_message = lang('common_sale_id'). ': '.$this->config->item('sale_prefix'). ' '.$data['sale_id'].' '.$data['person'].' '.lang('giftcards_spent').' '.$spent. " ".lang('giftcards_with_a_new_value_of')." ". $new_value;
		}
		elseif($data['type'] == 'sale_delete')
		{
			$spent = to_currency($transaction_amount);
			$new_value = to_currency($row['value']);
			$log_message = lang('common_sale_id'). ': '.$this->config->item('sale_prefix'). ' '.$data['sale_id'].' '.lang('sales_deleted_voided').' '.lang('giftcards_added').' '.$spent. " ".lang('giftcards_with_a_new_value_of')." ". $new_value;
		}
		elseif($data['type'] == 'sale_undelete')
		{
			$spent = to_currency($transaction_amount);
			$new_value = to_currency($row['value']);
			$log_message = lang('common_sale_id'). ': '.$this->config->item('sale_prefix'). ' '.$data['sale_id'].' '.lang('sales_undeleted_voided').' '.lang('giftcards_removed').' '.$spent. " ".lang('giftcards_with_a_new_value_of')." ". $new_value;
		}
		else if($data['type'] == "update")
		{
			$log_message = $data['person']." ". $data['keyword']." ".to_currency($transaction_amount)." ".lang('giftcards_to_giftcard_with_value_of')." ".to_currency($data['new_value']);
		}
		elseif ($data['type'] == 'create')
		{
			$transaction_amount = $data['new_value'];
			
			$sale_id_message = '';
			if (isset($data['sale_id']))
			{
				$sale_id_message = lang('common_sale_id'). ': '.anchor('sales/receipt/'.$data['sale_id'], $this->config->item('sale_prefix'). ' '.$data['sale_id'], array('target' => '_blank')).' ';
			}
			
			$log_message = $sale_id_message.$data['person']." ".lang('giftcards_created_giftcard_with_value')." ".to_currency($transaction_amount);
		}
		$this->db->insert('giftcards_log',array("giftcard_id" => $row['giftcard_id'], "log_message" => $log_message, "transaction_amount" => $transaction_amount, 'log_date' => date('Y-m-d H:i:s')));
	}
	
	function get_giftcard_log($giftcard_id)
	{
		$this->db->from('giftcards_log');
		$this->db->where('giftcard_id', $giftcard_id);
		$this->db->order_by("id", "desc");
		
		return $this->db->get()->result();
	}
}
?>
