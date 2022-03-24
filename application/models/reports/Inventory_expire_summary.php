<?php
require_once ("Report.php");
class Inventory_expire_summary extends Report
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
		
		$columns[] = array('data'=>lang('reports_item_name'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_expire_date'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_quantity_expiring'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_category'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_item_number'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_product_id'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_size'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_description'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_cost_price'), 'align'=> 'right');
		}

		$columns[] = array('data'=>lang('common_unit_price'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_count'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_reorder_level'), 'align'=> 'left');
		
		return $columns;
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('locations.name as location_name, items.name, SUM(quantity_purchased) as quantity_expiring,items.size,receivings_items.expire_date, categories.id as category_id,categories.name as category, company_name, item_number, product_id, 
		'.$this->db->dbprefix('receivings_items').'.item_unit_price as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price,
		SUM(quantity) as quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		items.description', FALSE);
		$this->db->from('items');
		$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('locations', 'locations.location_id = receivings.location_id');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_items.location_id IN ('.$location_ids_string.')', 'left');

		$this->db->where('items.deleted', 0);
		$this->db->where('items.system_item',0);
		
		$this->db->where_in('receivings.location_id', $location_ids);
			
		$this->db->where('receivings_items.expire_date >=', $this->params['start_date']);
		$this->db->where('receivings_items.expire_date <=', $this->params['end_date']);

		$this->db->group_by('receivings_items.receiving_id,receivings_items.item_id,receivings_items.line');
		$this->db->order_by('receivings_items.expire_date');
		
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
		
		$this->db->select('locations.name as location_name, items.name, SUM(quantity_purchased) as quantity_expiring,items.size,receivings_items.expire_date, categories.id as category_id,categories.name as category, company_name, item_number, product_id, 
		'.$this->db->dbprefix('receivings_items').'.item_unit_price as cost_price, 
		IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) as unit_price,
		SUM(quantity) as quantity, 
		IFNULL('.$this->db->dbprefix('location_items').'.reorder_level, '.$this->db->dbprefix('items').'.reorder_level) as reorder_level, 
		items.description', FALSE);
		$this->db->from('items');
		$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->join('locations', 'locations.location_id = receivings.location_id');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_items.location_id IN ('.$location_ids_string.')', 'left');

		$this->db->where('items.deleted', 0);
		$this->db->where('items.system_item',0);
		
		$this->db->where_in('receivings.location_id', $location_ids);
			
		$this->db->where('receivings_items.expire_date >=', $this->params['start_date']);
		$this->db->where('receivings_items.expire_date <=', $this->params['end_date']);

		$this->db->group_by('receivings_items.receiving_id,receivings_items.item_id,receivings_items.line');
		$this->db->order_by('receivings_items.expire_date');
		
		return $this->db->get()->num_rows();
	}
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);

		$this->db->select('sum(quantity_purchased) as total_items_in_inventory, sum(IFNULL('.$this->db->dbprefix('receivings_items').'.item_unit_price, '.$this->db->dbprefix('items').'.cost_price) * quantity_purchased) as inventory_total,
		sum(IFNULL('.$this->db->dbprefix('location_items').'.unit_price, '.$this->db->dbprefix('items').'.unit_price) * quantity_purchased) as inventory_sale_total', FALSE);
		$this->db->from('items');
		$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
		$this->db->join('receivings', 'receivings_items.receiving_id = receivings.receiving_id');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_items.location_id IN ('.$location_ids_string.')', 'left');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left outer');
		$this->db->where('items.deleted', 0);
		$this->db->where('items.system_item',0);

		$this->db->where('receivings_items.expire_date >=', $this->params['start_date']);
		$this->db->where('receivings_items.expire_date <=', $this->params['end_date']);
		$this->db->where_in('receivings.location_id', $location_ids);
		

		return $this->db->get()->row_array();
	}
}
?>