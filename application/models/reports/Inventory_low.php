<?php
require_once ("Report.php");
class Inventory_low extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$location_count = count(self::get_selected_location_ids());
		
		if ($location_count > 1)
		{
			$columns[] = array('data'=>lang('common_location'), 'align'=> 'left');
		}
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
		
		$this->db->select('locations.name as location_name, items.item_id, items.name, categories.id as category_id,categories.name as category, company_name, location, size, item_number, product_id,'.$this->db->dbprefix('items').'.replenish_level,
		IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price, 
		quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		description', FALSE);
		$this->db->from('items');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN ('.$location_ids_string.')', 'left');
		$this->db->join('locations', 'locations.location_id = location_items.location_id');
		if ($this->params['reorder_only'])
		{
			$this->db->where('((quantity <= IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level))) and '.$this->db->dbprefix('items').'.deleted=0');			
		}
		else
		{
			$this->db->where('(quantity <= 0 or (quantity <= IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level))) and '.$this->db->dbprefix('items').'.deleted=0');
		}
		
		$this->db->where('is_service !=', 1);
		$this->db->order_by('items.name');
		
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
		
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		return $this->db->get()->result_array();
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
		
		$this->db->select('items.name, categories.id as category_id,categories.name as category, company_name, item_number, product_id,
		IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price, 
		quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		description', FALSE);
		$this->db->from('items');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id IN ('.$location_ids_string.')', 'left');
		if ($this->params['reorder_only'])
		{
			$this->db->where('((quantity <= IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level))) and '.$this->db->dbprefix('items').'.deleted=0');			
		}
		else
		{
			$this->db->where('(quantity <= 0 or (quantity <= IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level))) and '.$this->db->dbprefix('items').'.deleted=0');
		}
		$this->db->where('is_service !=', 1);
		$this->db->order_by('items.name');
		
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
		
		
		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>