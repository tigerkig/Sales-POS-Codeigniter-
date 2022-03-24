<?php
require_once ("Report.php");
class Summary_items extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{		
		$columns = array();
		
		$columns[] = array('data'=>lang('common_item'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_item_number'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_product_id'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_current_cost_price'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_current_selling_price'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_quantity'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_quantity_purchased'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		
		return $columns;		
	}
	
	public function getData()
	{		
		if ($this->params['category_id'] != -1)
		{
			if ($this->config->item('include_child_categories_when_searching_or_reporting'))
			{	
				$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($this->params['category_id']);			
			}
			else
			{
				$category_ids = array($this->params['category_id']);
			}
		}		
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);

		$this->db->select('items.item_id,sales.location_id,items.cost_price as current_cost_price, items.unit_price as current_selling_price, items.name, items.item_number, items.product_id, categories.name as category , items.category_id, sum('.$this->db->dbprefix('sales_items').'.quantity_purchased) as quantity_purchased, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit', false);
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id', 'left');
		$this->db->join('categories', 'categories.id = items.category_id', 'left');
		
		$this->db->where('sales.deleted', 0);
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
			
		}
		
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if ($this->params['category_id'] != -1)
		{			
			$this->db->where_in('items.category_id', $category_ids);
		}
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('items.supplier_id', $this->params['supplier_id']);
		}	
		
		if (isset($this->params['compare_to_items']) && count($this->params['compare_to_items']) > 0)
		{
			$this->db->where_in('items.item_id', $this->params['compare_to_items']);
		}	
				
		$this->db->group_by('sales_items.item_id');
		$this->db->order_by('items.name');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		$items_sales_data = $this->db->get()->result_array();	
		$item_ids = array();
		
		foreach($items_sales_data as $index => $items_sales_data_row)
		{
			$item_ids[] = $items_sales_data_row['item_id'];
		}
		
		if ($this->params['show_all_items'])
		{
			$this->db->select('items.item_id,items.cost_price as current_cost_price,items.unit_price as current_selling_price, items.name, items.item_number, items.product_id, categories.name as category , items.category_id, 0 as quantity_purchased, 0 as subtotal, 0 as total, 0 as tax, 0 as profit', false);
			$this->db->from('items');			
			$this->db->join('categories','categories.id = items.category_id', 'left');
			
			if ($this->params['category_id'] != -1)
			{			
				$this->db->where_in('items.category_id', $category_ids);
			}
		
			if ($this->params['supplier_id'] != -1)
			{
				$this->db->where('items.supplier_id', $this->params['supplier_id']);
			}	
					
			if (!empty($item_ids))
			{
				$this->db->group_start();
				$item_ids_chunk = array_chunk($item_ids,25);
				foreach($item_ids_chunk as $item_ids)
				{
					$this->db->or_where_not_in('items.item_id',$item_ids);
				}	
				$this->db->group_end();
			}
			$items_sales_data_no_items = $this->db->get()->result_array();	
		}
		
		
		
		if ($this->params['show_all_items'])
		{
			$this->db->select('items.item_id,SUM(quantity) as quantity', FALSE);
			$this->db->from('items');
			
			$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN('.$location_ids_string.')', 'left');
			$this->db->group_by('items.item_id');
		}
		else
		{
			$this->db->select('items.item_id,SUM(quantity) as quantity', FALSE);
			$this->db->from('items');
			$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN('.$location_ids_string.')', 'left');
		
			if (count($item_ids))
			{
				$this->db->group_start();
				$item_ids_chunk = array_chunk($item_ids,25);
				foreach($item_ids_chunk as $item_ids)
				{
					$this->db->or_where_in('items.item_id',$item_ids);
				}
				$this->db->group_end();
			}
			else
			{
				$this->db->where('items.item_id',0);
			}
		
			$this->db->group_by('items.item_id');
		}
		
		$quantity_result = $this->db->get()->result_array();
		$quantities_indexed_by_id = array();
		
		foreach($quantity_result as $quan_row)
		{
			$quantities_indexed_by_id[$quan_row['item_id']] = $quan_row['quantity'];
		}
		
		for($k=0;$k<count($items_sales_data);$k++)
		{
			$items_sales_data[$k]['quantity'] = $quantities_indexed_by_id[$items_sales_data[$k]['item_id']];
		}
		
		if ($this->params['show_all_items'])
		{
			for($k=0;$k<count($items_sales_data_no_items);$k++)
			{
				$items_sales_data_no_items[$k]['quantity'] = $quantities_indexed_by_id[$items_sales_data_no_items[$k]['item_id']];
			}
			$items_sales_data = array_merge($items_sales_data,$items_sales_data_no_items);
			
		}
		
		return $items_sales_data;
			
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		if ($this->params['show_all_items'])
		{
			$this->db->from('items');			
			
			
			if ($this->params['category_id'] != -1)
			{			
				if ($this->config->item('include_child_categories_when_searching_or_reporting'))
				{	
					$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($this->params['category_id']);			
				}
				else
				{
					$category_ids = array($this->params['category_id']);
				}
				$this->db->where_in('items.category_id', $category_ids);
			}
		
			if ($this->params['supplier_id'] != -1)
			{
				$this->db->where('items.supplier_id', $this->params['supplier_id']);
			}	
			
			return $this->db->count_all_results();
		}
		else
		{
			if ($this->params['category_id'] != -1)
			{
				if ($this->config->item('include_child_categories_when_searching_or_reporting'))
				{	
					$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($this->params['category_id']);			
				}
				else
				{
					$category_ids = array($this->params['category_id']);
				}
			}		
		
		
			$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('sales_items').'.item_id)) as item_count');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->join('items', 'sales_items.item_id = items.item_id');

			$this->db->where('sales.deleted', 0);
			$this->sale_time_where();			
						

			if ($this->params['sale_type'] == 'sales')
			{
				$this->db->where('quantity_purchased > 0');
			}
			
			elseif ($this->params['sale_type'] == 'returns')
			{
				$this->db->where('quantity_purchased < 0');
			}
		
			if ($this->params['category_id'] != -1)
			{
				$this->db->where_in('items.category_id', $category_ids);
			}
		
			if ($this->params['supplier_id'] != -1)
			{
				$this->db->where('items.supplier_id', $this->params['supplier_id']);
			}
		
			if (isset($this->params['compare_to_items']) && count($this->params['compare_to_items']) > 0)
			{
				$this->db->where_in('items.item_id', $this->params['compare_to_items']);
			}	
		
			$ret = $this->db->get()->row_array();
			return $ret['item_count'];
		}
	}
	
	public function getSummaryData()
	{
		if ($this->params['category_id'] != -1)
		{
			if ($this->config->item('include_child_categories_when_searching_or_reporting'))
			{	
				$category_ids = $this->Category->get_category_id_and_children_category_ids_for_category_id($this->params['category_id']);			
			}
			else
			{
				$category_ids = array($this->params['category_id']);
			}
		}		
		
		$this->db->select('sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit', false);
		$this->db->from('sales');	
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id');

		$this->db->where('sales.deleted', 0);
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('items').'.deleted', 0);
		
		if ($this->params['category_id'] != -1)
		{
			$this->db->where_in('items.category_id', $category_ids);
		}
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('items.supplier_id', $this->params['supplier_id']);
		}
		
		
		$this->db->group_by('sales_items.sale_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
		}
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
}
?>