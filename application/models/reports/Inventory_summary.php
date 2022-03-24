<?php
require_once ("Report.php");
class Inventory_summary extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		
		$columns = array();
		
		$columns[] = array('data'=>lang('common_item_id'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_item_name'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_category'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_supplier'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_item_number'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_product_id'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_description'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_size'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_location'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_cost_price'), 'align'=> 'right');
		}

		$columns[] = array('data'=>lang('common_unit_price'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_count'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_pending_inventory'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_reorder_level'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_replenish_level'), 'align'=> 'left');
		
		return $columns;
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
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
		
		//Today
		if ($this->params['inventory_date'] == date('Y-m-d').' 23:59:59')
		{
			$sum_query = 'SUM(quantity)';
		}
		else
		{
			$sum_query = '(SELECT SUM(trans_inventory) FROM '.$this->db->dbprefix('inventory').' WHERE 
				'.$this->db->dbprefix('inventory').'.trans_date < "'.$this->params['inventory_date'].'" and '.$this->db->dbprefix('inventory').'.trans_items='.$this->db->dbprefix('items').'.item_id and '.$this->db->dbprefix('inventory').'.location_id =location_id
			)';
		}
		
		$this->db->select('location_id as location_id, items.item_id, items.name, categories.id as category_id,categories.name as category,location, company_name, item_number,size, product_id, '.$this->db->dbprefix('items').'.replenish_level,
		IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price,
		'.$sum_query.' as quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		description', FALSE);
		$this->db->from('items');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN('.$location_ids_string.')', 'left');
		if (!isset($this->params['show_deleted']) || !$this->params['show_deleted'])
		{
			$this->db->where('items.deleted', 0);
		}
		$this->db->where('items.system_item',0);
		$this->db->group_by('items.item_id');
		
		if ($this->params['supplier'] != -1)
		{
			$this->db->where('suppliers.person_id', $this->params['supplier']);
		}
		
		if ($this->params['category_id'] != -1)
		{
			$this->db->where_in('categories.id', $category_ids);
		}
		
		if ($this->params['inventory'] == 'in_stock')
		{
			$this->db->having('quantity > 0');
		}
		
		if ($this->params['inventory'] == 'out_of_stock')
		{
			$this->db->having('quantity <= 0');
		}
			
		$this->db->where('is_service !=', 1);
		$this->db->order_by('items.name');
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$inventory_result = $this->db->get()->result_array();
		
		$this->db->select('item_id, quantity_purchased - quantity_received as pending_inventory', false);
		$this->db->from('receivings_items');
		$this->db->join('receivings', 'receivings.receiving_id = receivings_items.receiving_id');
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 1);
		$this->db->where_in('location_id', $location_ids);
		
		$pending_inventory_result = $this->db->get()->result_array();
		
		for($k=0;$k<count($inventory_result);$k++)
		{
			$inventory_result[$k]['pending_inventory'] = 0;
		}
		
		for($k=0;$k<count($pending_inventory_result);$k++)
		{
			$item_id = $pending_inventory_result[$k]['item_id'];
			$pending_inventory = $pending_inventory_result[$k]['pending_inventory'];
			
			for($i=0;$i<count($inventory_result);$i++)
			{
				if ($inventory_result[$i]['item_id'] == $item_id)
				{
					$inventory_result[$i]['pending_inventory'] += $pending_inventory;
					break;
				}
			}
		}
		
		if ($this->params['show_only_pending'])
		{
			foreach($inventory_result as $key=>$value)
			{
				if($value['pending_inventory'] <= 0)
				{
					unset($inventory_result[$key]);
				}
			}
			
			//Fix any missing holes...not really needed but looks better
			$inventory_result = array_values($inventory_result);
		}
		
		return $inventory_result;
		
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
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
		
		//Today
		if ($this->params['inventory_date'] == date('Y-m-d').' 23:59:59')
		{
			$sum_query = 'SUM(quantity)';
		}
		else
		{
			$sum_query = '(SELECT SUM(trans_inventory) FROM '.$this->db->dbprefix('inventory').' WHERE 
				'.$this->db->dbprefix('inventory').'.trans_date < "'.$this->params['inventory_date'].'" and '.$this->db->dbprefix('inventory').'.trans_items='.$this->db->dbprefix('items').'.item_id and '.$this->db->dbprefix('inventory').'.location_id =location_id
			)';
		}
		
		$this->db->select('location_id as location_id, items.item_id, items.name, categories.id as category_id,categories.name as category,location, company_name, item_number,size, product_id, 
		IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price,
		'.$sum_query.' as quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		description', FALSE);
		$this->db->from('items');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN('.$location_ids_string.')', 'left');
		if (!isset($this->params['show_deleted']) || !$this->params['show_deleted'])
		{
			$this->db->where('items.deleted', 0);
		}
		$this->db->where('items.system_item',0);
		
		$this->db->group_by('items.item_id');
		
		if ($this->params['supplier'] != -1)
		{
			$this->db->where('suppliers.person_id', $this->params['supplier']);
		}
		
		if ($this->params['category_id'] != -1)
		{
			$this->db->where_in('categories.id', $category_ids);
		}
		
		if ($this->params['inventory'] == 'in_stock')
		{
			$this->db->where('quantity > 0');
		}
		
		if ($this->params['inventory'] == 'out_of_stock')
		{
			$this->db->where('quantity <= 0');
		}
		
		$this->db->where('is_service !=', 1);
		$this->db->order_by('items.name');
		
		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		if ($this->params['show_only_pending'])
		{
			return array();
		}
		
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
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
		
		//Today
		if ($this->params['inventory_date'] == date('Y-m-d').' 23:59:59')
		{
			$full_sum_query = 'SUM(quantity)';
			$sum_query = 'SUM(quantity)';
			$quantity_query = 'quantity';
		}
		else
		{
			$full_sum_query = '(SELECT SUM(trans_inventory) FROM '.$this->db->dbprefix('inventory').' WHERE 
				'.$this->db->dbprefix('inventory').'.trans_date < "'.$this->params['inventory_date'].'" and '.$this->db->dbprefix('inventory').'.location_id =location_id
			)';
			
			$sum_query = '(SELECT SUM(trans_inventory) FROM '.$this->db->dbprefix('inventory').' WHERE 
				'.$this->db->dbprefix('inventory').'.trans_date < "'.$this->params['inventory_date'].'" and '.$this->db->dbprefix('inventory').'.trans_items='.$this->db->dbprefix('items').'.item_id and '.$this->db->dbprefix('inventory').'.location_id =location_id
			)';
			$quantity_query = '(SELECT SUM(trans_inventory) FROM '.$this->db->dbprefix('inventory').' WHERE 
				'.$this->db->dbprefix('inventory').'.trans_date < "'.$this->params['inventory_date'].'" and '.$this->db->dbprefix('inventory').'.trans_items='.$this->db->dbprefix('items').'.item_id and '.$this->db->dbprefix('inventory').'.location_id =location_id
			)';
		}
		
		
		$this->db->select('location_id,'.$full_sum_query.' as total_items_in_inventory, sum(IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) * '.$quantity_query.') as inventory_total,
		sum(IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) * '.$quantity_query.') / '.$full_sum_query.' as weighted_cost,
		sum(IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) * '.$quantity_query.') as inventory_sale_total', FALSE);
		$this->db->from('items');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN('.$location_ids_string.')', 'left');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->where('is_service !=', 1);
		if (!isset($this->params['show_deleted']) || !$this->params['show_deleted'])
		{
				$this->db->where('items.deleted', 0);
		}
		$this->db->where('items.system_item',0);
			
		if ($this->params['supplier'] != -1)
		{
			$this->db->where('suppliers.person_id', $this->params['supplier']);
		}
		
		if ($this->params['category_id'] != -1)
		{
			$this->db->where_in('categories.id', $category_ids);
		}
		
		if ($this->params['inventory'] == 'in_stock')
		{
			$this->db->where('quantity > 0');
		}
		
		if ($this->params['inventory'] == 'out_of_stock')
		{
			$this->db->where('quantity <= 0');
		}
		
		$result = $this->db->get()->row_array();
		unset($result['location_id']);
		return $result;
	}
}
?>