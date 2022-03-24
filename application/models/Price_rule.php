<?php
class Price_rule extends CI_Model
{
	function search_count_all($search, $limit=10000)
	{
		$this->db->from('price_rules');
		
		if ($search)
		{			
			$this->db->like('name', $search, 'both');
			$this->db->or_like('type', $search, 'both');
			$this->db->where('deleted',0);
		}
				
		$this->db->limit($limit);
		$result=$this->db->get();
				
		return $result->num_rows();
	}	

	/*
	Perform a search on price_rules
	*/
	function search($search, $limit=20,$offset=0,$column='name',$orderby='asc')
	{
		$this->db->from('price_rules');
						
		if ($search)
		{			
			$this->db->like('name', $search);
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
	
	/*
	Returns all price rules
	*/
	function get_all($limit=10000, $offset=0,$col='name',$order='asc')
	{	
		
		$this->db->from('price_rules');
		$this->db->where('deleted', 0);
		if(!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit, $offset);
		
		return $this->db->get(); 		
	}
	
	function count_all()
	{
		$this->db->from('price_rules');
		$this->db->where('deleted',0);
		
		return $this->db->count_all_results();
	}
	
	function get_price_rule_for_spending($params, $sub_total)
	{
		if (!isset($params['coupons']))
		{
			$params['coupons'] = array();
		}
		
		$coupon_rule_ids = array_map(array($this, 'coupons_to_rule_id'), $params['coupons']);
				
		$rule=array();
		$this->db->select('price_rules.*, price_rules.id as rule_id');
		$this->db->from('price_rules');
		$this->db->where('deleted', 0);
		$this->db->where('active', 1);
		$this->db->where('type', 'spend_x_get_discount');
		$this->db->where('spend_amount <=', $sub_total);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('id',$coupon_rule_ids);			
		}
		$this->db->group_end();
		
		$this->db->order_by('spend_amount', 'DESC');
		$this->db->limit(1);
		
		$query=$this->db->get();
		
		if($query->num_rows() == 1)
		{
			$rule=$query->row_array();
			$rule['rule_spending'] = true;
			
			return $rule;
		} 
	}
	
	function coupons_to_rule_id($coupon)
	{
		return $coupon['value'];
	}
	
	/*function to get item & item_kit rules*/
	function get_price_rule_for_item($params = array())
	{
			if (!isset($params['coupons']))
			{
				$params['coupons'] = array();
			}
			
			$coupon_rule_ids = array_map(array($this, 'coupons_to_rule_id'), $params['coupons']);
			
			if(isset($params['item_kit_id']) and $params['item_kit_id'] > 0)
			{
				$rule=$this->get_rule_for_item_kit($params['item_kit_id'], $params['quantity'], $coupon_rule_ids);

				if($rule['rule_item_kit'] === true)
				{
					return $rule;
				}
				
				$rule=$this->get_rule_for_itemkit_category($params['item_kit_id'], $params['quantity'], $coupon_rule_ids);
				if($rule['rule_item_kit_cat'] === true)
				{
					return $rule;
				}

				$rule=$this->get_rule_for_itemkit_tags($params['item_kit_id'], $params['quantity'], $coupon_rule_ids);
				if($rule['rule_item_kit_tags'] === true)
				{
					return $rule;
				}
				
			}
			else
			{					
				$rule=$this->get_rule_for_item($params['item_id'], $params['quantity'], $coupon_rule_ids);
				
				if($rule['rule_item'] === true)
				{
					return $rule;
				}

				$rule=$this->get_rule_for_category($params['item_id'], $params['quantity'], $coupon_rule_ids);
				if($rule['rule_item_cat'] === true)
				{
					return $rule;
				}

				$rule=$this->get_rule_for_tags($params['item_id'], $params['quantity'], $coupon_rule_ids);
				if($rule['rule_item_tags'] === true)
				{
					return $rule;
				}
			}
	}
	
	function get_rule_for_item_kit($item_kit_id=-1, $quantity=-1, $coupon_rule_ids)
	{
		$rule=array();
		
		$this->db->select('price_rules_item_kits.rule_id, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_item_kits', 'price_rules_item_kits.rule_id = price_rules.id', 'inner');
			
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('price_rules_item_kits.item_kit_id', $item_kit_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		
		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_item_kits.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();
	
		$this->db->order_by('price_rules_item_kits.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();
					
		if($query->num_rows() == 1)
		{
			$rule = $query->row_array();
			$rule['rule_item_kit']=true;
			$rule['item_kit_id']=$item_kit_id;
		}
		else
		{
			$rule['rule_item_kit']=false;
		}
		
		return $rule;
	}
	
	function get_rule_for_itemkit_category($item_kit_id=-1, $quantity=-1, $coupon_rule_ids)
	{		
		$rule=array();
		$this->db->select('price_rules_categories.rule_id, price_rules_categories.category_id, categories.name, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_categories', 'price_rules_categories.rule_id = price_rules.id', 'inner');
		$this->db->join('categories', 'categories.id = price_rules_categories.category_id', 'inner');
		$this->db->join('item_kits', 'item_kits.category_id = price_rules_categories.category_id', 'left');
				
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('item_kits.item_kit_id', $item_kit_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);

		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_categories.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();
		
		$this->db->order_by('price_rules_categories.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);
		
		$query=$this->db->get();
		
		if($query->num_rows() == 1)
		{
			$rule=$query->row_array();
			$rule['rule_item_kit_cat']=true; //why?
		}
		else
		{
			$rule['rule_item_kit_cat']=false; //why?
		}
		
		return $rule;
	}
	

	function get_rule_for_itemkit_tags($item_kit_id=-1, $quantity=-1, $coupon_rule_ids)
	{
		
		$rule=array();
		
		$item_kits_tags = $this->db->dbprefix('item_kits_tags');
		
		$this->db->select('price_rules_tags.rule_id, price_rules_tags.tag_id, tags.name, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_tags', 'price_rules_tags.rule_id = price_rules.id', 'inner');
		$this->db->join('tags','tags.id = price_rules_tags.tag_id','inner');
		$this->db->join('item_kits_tags', 'item_kits_tags.tag_id = price_rules_tags.tag_id', 'inner');
		$this->db->join('item_kits',"item_kits.item_kit_id IN (SELECT item_kit_id FROM $item_kits_tags)",'left');
				
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('item_kits_tags.item_kit_id', $item_kit_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);

		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_tags.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();

		$this->db->order_by('price_rules_tags.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);

		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			$rule = $query->row_array();
			$rule['rule_item_kit_tags']=true;
			$rule['item_kit_id']=$item_kit_id;
		}
		else
		{
			$rule['rule_item_kit_tags']=false;
		}
		
		return $rule;
		
	}
	
	function get_rule_for_item($item_id=-1,$quantity=-1, $coupon_rule_ids)
	{		
		$rule=array();
		
		$this->db->select('price_rules_items.rule_id, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_items', 'price_rules_items.rule_id = price_rules.id', 'inner');
				
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('price_rules_items.item_id', $item_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		
		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();

		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_items.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();
		
		$this->db->order_by('price_rules_items.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);
		
		$query=$this->db->get();	
		if($query->num_rows() == 1)
		{
			$rule=$query->row_array();
			$rule['rule_item']=true;
		}
		else
		{
			$rule['rule_item']=false;
		}
		
		return $rule;
	}

	function get_rule_for_category($item_id=-1, $quantity=-1, $coupon_rule_ids)
	{	//done needs testing
		$rule=array();
		$this->db->select('price_rules_categories.rule_id, price_rules_categories.category_id, categories.name, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_categories', 'price_rules_categories.rule_id = price_rules.id', 'inner');
		$this->db->join('categories', 'categories.id = price_rules_categories.category_id', 'inner');
		$this->db->join('items', 'items.category_id = price_rules_categories.category_id', 'left');
		
		
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('items.item_id', $item_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		
		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_categories.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();
		
		$this->db->order_by('price_rules_categories.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);
		
		$query=$this->db->get();
		
		if($query->num_rows() == 1)
		{
			$rule=$query->row_array();
			$rule['rule_item_cat']=true;
		}
		else
		{
			$rule['rule_item_cat']=false;
		}
		
		return $rule;
	}

	function get_rule_for_tags($item_id=-1, $quantity=-1, $coupon_rule_ids)
	{
		$rule=array();
				
		$items_tags = $this->db->dbprefix('items_tags');
			
		$this->db->select('price_rules_tags.rule_id, price_rules_tags.tag_id, tags.name, price_rules_price_breaks.item_qty_to_buy, price_rules_price_breaks.discount_per_unit_fixed, price_rules_price_breaks.discount_per_unit_percent, price_rules.*');
		$this->db->from('price_rules');
		$this->db->join('price_rules_price_breaks', 'price_rules_price_breaks.rule_id = price_rules.id', 'left');
		$this->db->join('price_rules_tags', 'price_rules_tags.rule_id = price_rules.id', 'inner');
		$this->db->join('tags', 'tags.id = price_rules_tags.tag_id', 'inner');
		$this->db->join('items_tags', 'items_tags.tag_id = price_rules_tags.tag_id', 'inner');
		$this->db->join('items', "items.item_id IN (SELECT item_id FROM $items_tags)");
				
		$this->db->where('price_rules.active', 1);
		$this->db->where('price_rules.deleted', 0);
		$this->db->where('items_tags.item_id', $item_id);
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);

		$this->db->group_start();
		$this->db->where('price_rules_price_breaks.item_qty_to_buy <=', $quantity);
		$this->db->or_where('price_rules.items_to_buy <=', $quantity);
		$this->db->group_end();
		
		$this->db->group_start();
		$this->db->where('coupon_code', NULL);
		if (!empty($coupon_rule_ids))
		{
			$this->db->or_where_in('price_rules_tags.rule_id',$coupon_rule_ids);			
		}
		$this->db->group_end();
		
		$this->db->order_by('price_rules_tags.rule_id, price_rules_price_breaks.item_qty_to_buy', 'DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();

		if($query->num_rows() == 1)
		{
			$rule=$query->row_array();
			$rule['rule_item_tags']=true; //why?
		}
		else
		{
			$rule['rule_item_tags']=false; //why?
		}
		
		return $rule;
	}
	
	/*Get all tags*/
	function get_all_tags($limit=10000, $offset=0,$col='name',$order='asc')
	{
		$this->db->from('tags');
		$this->db->where('deleted',0);
		if (!$this->config->item('speed_up_search_queries'))
		{
			$this->db->order_by($col, $order);
		}
		
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}

	function save_price_rule($rule_id=-1, $rule_data, $items= array(), $item_kits = array(), $categories = array(), $tags = array(), $price_breaks = array())
	{	
		
		if($rule_data['type'] == 'simple_discount')
		{
			$rule_data['items_to_buy'] = 0;
			$rule_data['items_to_get'] = 1;
		}
		
		
		if (!$this->exists($rule_id))
		{
			if(!empty($rule_data['type']))
			{
				$this->db->insert('price_rules',$rule_data);
				$rule_id = $this->db->insert_id();	
			}
			else
			{
				return false;
			}
		}
		else
		{
			$this->db->where('id', $rule_id);
			$this->db->update('price_rules',$rule_data);
		}
		
		if($rule_data['type'] !== 'spend_x_get_discount')
		{
			$this->save_items_for_price_rule($rule_id, $items);
			$this->save_item_kits_for_price_rule($rule_id, $item_kits);
			$this->save_categories_for_price_rule($rule_id, $categories);
			$this->save_tags_for_price_rule($rule_id, $tags);
		}
		
		if(($rule_data['type'] == 'advanced_discount'))
		{
			$this->save_price_breaks($rule_id, $price_breaks);
		}
		else
		{
			$this->clear_price_breaks($rule_id);			
		}
		
		return true;
			
	}
	
	function save_items_for_price_rule($rule_id, $items)
	{		
		if(!$this->exists($rule_id)) return;
		//Remove current items for price rule
			
		$this->db->delete('price_rules_items', array('rule_id' => $rule_id));
		
		if(!empty($items))
		{
			foreach($items as $item)
			{
				if ($item != '')
				{
					$item = trim($item);
					
					if (is_numeric($item)) //Numeric Item ID
					{
						$this->db->insert('price_rules_items', array('rule_id' => $rule_id, 'item_id' => $item));
					}
				}
			}
		}
		return TRUE;
	}
	
	function save_item_kits_for_price_rule($rule_id, $item_kits)
	{
		if(!$this->exists($rule_id)) return;
		//Remove current item_kits for price rule
		$this->db->delete('price_rules_item_kits', array('rule_id' => $rule_id));
		if(!empty($item_kits))
		{
			foreach($item_kits as $kit)
			{
				if ($kit != '')
				{
					$kit = trim($kit);
					
					if (is_numeric($kit)) //Numeric Item Kit ID
					{
						$this->db->insert('price_rules_item_kits', array('rule_id' => $rule_id, 'item_kit_id' => $kit));
					}
				}
			}
		}
		
		return TRUE;
	}
	
	function save_categories_for_price_rule($rule_id, $cats)
	{
		if(!$this->exists($rule_id)) return;
		//Remove current categories for price rule
		$this->db->delete('price_rules_categories', array('rule_id' => $rule_id));

		if(!empty($cats))
		{
			foreach($cats as $cat)
			{
				if ($cat != '')
				{
					$cat = trim($cat);
					
					if (is_numeric($cat)) //Numeric Category ID
					{
						$this->db->insert('price_rules_categories', array('rule_id' => $rule_id, 'category_id' => $cat));
					}
				}
			}
		}
		
		return TRUE;
	}
	
	function save_tags_for_price_rule($rule_id, $tags)
	{
		if(!$this->exists($rule_id)) return;
		//Remove current tags for price rule
		$this->db->delete('price_rules_tags', array('rule_id' => $rule_id));
		if(!empty($tags))
		{
			foreach($tags as $tag)
			{
				if ($tag != '')
				{
					$tag = trim($tag);
					
					if (is_numeric($tag)) //Numeric Tag ID
					{
						$this->db->insert('price_rules_tags', array('rule_id' => $rule_id, 'tag_id' => $tag));
					}
				}
			}
		}
		
		return TRUE;
	}
	
	function save_price_breaks($rule_id, $price_breaks)
	{
		if(!$this->exists($rule_id)) return;
		//Remove current items for price rule
		$this->db->delete('price_rules_price_breaks', array('rule_id' => $rule_id));
		
		if(!empty($price_breaks) && count($price_breaks) > 0)
		{
			foreach($price_breaks as $price_break)
			{
				if (!empty($price_break))
				{					
					//Set rule id based on rule_id passed in
					$price_break['rule_id'] = $rule_id;
					$this->db->insert('price_rules_price_breaks', $price_break);
				}
			}
		}
		
		return TRUE;
	}
	
	function clear_price_breaks($rule_id)
	{
		$this->db->where('rule_id',$rule_id);
		return $this->db->delete('price_rules_price_breaks');
	}
	
	/* Determines if a given rule_id is a rule */
	function exists($rule_id)
	{
		$this->db->from('price_rules');
		$this->db->where('id',$rule_id);
		$query = $this->db->get();

		return ($query->num_rows() == 1);
		
	}	
	
	function get_rule_info($rule_id)
	{	
		$this->db->select('price_rules.*, price_rules.id as rule_id');
		$this->db->from('price_rules');
		$this->db->where('price_rules.id',$rule_id);		
		$query=$this->db->get();
		return $query->row_array();
	}
	
	function get_rule_items($rule_id)
	{
		$this->db->select('price_rules_items.*, items.name');
		$this->db->from('price_rules_items');
		$this->db->join('items','items.item_id = price_rules_items.item_id');
		$this->db->where(array('price_rules_items.rule_id' => $rule_id,'items.deleted' => 0));		
		
		$query=$this->db->get();
		return $query->result_array();
	}
	
	function get_rule_item_kits($rule_id)
	{
		$this->db->select('prkits.*,kits.name,kits.item_kit_number,kits.product_id');
		$this->db->from('price_rules_item_kits as prkits');
		$this->db->join('item_kits as kits','kits.item_kit_id=prkits.item_kit_id');
		$this->db->where(array('prkits.rule_id' => $rule_id, 'kits.deleted' => 0));		
		$query=$this->db->get();

		return $query->result_array();
	}
	
	function get_rule_categories($rule_id)
	{
		$this->db->select('prcats.*,c.name');
		$this->db->from('price_rules_categories as prcats');
		$this->db->join('categories as c','c.id=prcats.category_id');
		$this->db->where(array('prcats.rule_id'=>$rule_id,'c.deleted'=>0));		
		$query=$this->db->get();

		return $query->result_array();
	}
	
	function get_rule_tags($rule_id)
	{
		$this->db->select('prtags.*, t.name');
		$this->db->from('price_rules_tags as prtags');
		$this->db->join('tags as t','t.id=prtags.tag_id');
		$this->db->where(array('rule_id' => $rule_id,'t.deleted' => 0));		
		$query=$this->db->get();

		return $query->result_array();
	}
	
	function get_price_breaks($rule_id)
	{
		$this->db->select('*');
		$this->db->from('price_rules_price_breaks');
		$this->db->where(array('rule_id' => $rule_id));		
		$query=$this->db->get();

		return $query->result_array();
	}
	
	function get_coupons_for_receipt()
	{
		$this->db->from("price_rules");
		$this->db->where('coupon_code IS NOT NULL');
 		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where('active', 1);
		$this->db->where('show_on_receipt', 1);
		$this->db->where('deleted', 0);
		
		return $this->db->get()->result_array();
		
	}
	
	function has_coupons_for_today()
	{
		$this->db->select("COUNT(*) as count");
		$this->db->from("price_rules");
		$this->db->where('coupon_code IS NOT NULL');
		$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
		$this->db->where('active', 1);
		$this->db->where('deleted', 0);
		
		$row = $this->db->get()->row_array();
		
		if((int)$row['count'] >= 1)
		{
			return true;
		}
		
		return false;
	}
	
	
	/*
	Get search suggestions to for coupons field in sales interface
	*/
	function search_coupons($search, $limit=5)
	{
		if (!trim($search))
		{
			return array();
		}
		
			$suggestions = array();

			$this->db->select('id, name, start_date, end_date, coupon_code, type');
			$this->db->from('price_rules');
			
			$this->db->group_start();
			$this->db->like('name', $search, 'after');
			$this->db->or_like('type', $search, 'after');
			$this->db->or_like('coupon_code', $search, 'after');
			$this->db->group_end();
			
			$this->db->where('active', 1);
			$this->db->where('deleted', 0);
			
			$this->db->where($this->db->dbprefix('price_rules').'.start_date <= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
			$this->db->where($this->db->dbprefix('price_rules').'.end_date >= '.$this->db->escape(date('Y-m-d H:i:s')), NULL, FALSE);
			
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();	
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'subtitle' => $row->type,
					'coupon_code' => $row->coupon_code,
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'subtitle'=>$value['subtitle'], 'coupon_code' =>$value['coupon_code']);		
			}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	}
	
	/*
	Get search suggestions to find price_rules
	*/
	function get_search_suggestions($search,$limit=5)
	{
		
		if (!trim($search))
		{
			return array();
		}
		
			$suggestions = array();

			$this->db->select('id, name, start_date, end_date, coupon_code, type');
			$this->db->from('price_rules');
			
			$this->db->group_start();
			$this->db->like('name', $search, 'both');
			$this->db->or_like('type', $search, 'both');
			$this->db->or_like('coupon_code', $search, 'both');
			$this->db->group_end();
			
			$this->db->where('active', 1);
			$this->db->where('deleted', 0);
			$this->db->limit($limit);
			
			$query=$this->db->get();
			
			$temp_suggestions = array();	
			foreach($query->result() as $row)
			{
				$data = array(
					'name' => $row->name,
					'subtitle' => $row->type,
					'avatar' => base_url()."assets/img/giftcard.png",
					'coupon_code' => $row->coupon_code,
					 );
				$temp_suggestions[$row->id] = $data;
			}
		
		
			$this->load->helper('array');
			uasort($temp_suggestions, 'sort_assoc_array_by_name');
			
			foreach($temp_suggestions as $key => $value)
			{
				$suggestions[]=array('value'=> $key, 'label' => $value['name'],'avatar'=>$value['avatar'],'subtitle'=>$value['subtitle'], 'coupon_code' =>$value['coupon_code']);		
			}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	
	}
	
	/*
	Deletes Price rule(s)
	*/
	function delete($rule_ids)
	{				
		foreach($rule_ids as $id) {
			$this->db->where('id', $id);
			$this->db->update('price_rules', array('deleted' => 1));
		}
		
		return true;
	}
	
	/*
	Set Price Rule(s) active/inactive
	*/
	
	function set_active($rule_ids, $active)
	{
		
		foreach($rule_ids as $id) {
			$this->db->where('id', $id);
			$this->db->update('price_rules', array('active' => $active));
		}
		
		return true;
	}
	
}
?>
