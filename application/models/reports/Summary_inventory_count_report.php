<?php
require_once ("Report.php");
class Summary_inventory_count_report extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		
		$columns = array(
			array('data'=>lang('reports_count_date'), 'align'=>'left'), 
			array('data'=>lang('common_status'), 'align'=>'left'), 
			array('data'=>lang('common_employee'), 'align'=> 'left'),
			array('data'=>lang('reports_number_items_counted'), 'align'=>'left'), 
			array('data'=>lang('reports_amount_over_under_from_actual_on_hand'), 'align'=> 'left'),
			array('data'=>lang('reports_total_difference'), 'align'=> 'left'),
			array('data'=>lang('common_comments'), 'align'=>'left'));
				
		$location_count = count(self::get_selected_location_ids());
	
		if ($location_count > 1)
		{
			array_unshift($columns, array('data'=>lang('common_location'), 'align'=> 'left'));
		}
				
		return $columns;
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
				
		$this->db->select('locations.name as location_name, inventory_counts.comment, count_date, CONCAT(`first_name`, " ", `last_name`) as employee_name, status, SUM(count) - SUM(actual_quantity) as difference, COUNT(*) as items_counted,SUM(cost_price * count) - SUM(cost_price * actual_quantity) as cost_price_difference', false);
		$this->db->from('inventory_counts');
		$this->db->join('locations', 'inventory_counts.location_id = locations.location_id');
		$this->db->join('inventory_counts_items', 'inventory_counts.id = inventory_counts_items.inventory_counts_id');
		$this->db->join('items', 'inventory_counts_items.item_id = items.item_id');
		$this->db->join('employees', 'employees.person_id = inventory_counts.employee_id');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where('count_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']. ' 23:59:59').' and inventory_counts.location_id IN('.$location_ids_string.')');
		$this->db->group_by('inventory_counts_id');
		$this->db->order_by('count_date',($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('COUNT(*) as number_items_counted,SUM(cost_price * count) - SUM(cost_price * actual_quantity) as total_difference', false);
		$this->db->from('inventory_counts');
		$this->db->join('inventory_counts_items', 'inventory_counts.id = inventory_counts_items.inventory_counts_id');
		$this->db->join('items', 'inventory_counts_items.item_id = items.item_id');
		$this->db->where('count_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']. ' 23:59:59').' and inventory_counts.location_id IN('.$location_ids_string.')');
		$this->db->order_by('count_date',($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
		return $this->db->get()->row_array();
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
				
		$this->db->from('inventory_counts');
		$this->db->join('inventory_counts_items', 'inventory_counts.id = inventory_counts_items.inventory_counts_id');
		$this->db->join('employees', 'employees.person_id = inventory_counts.employee_id');
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where('count_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']. ' 23:59:59').' and location_id IN('.$location_ids_string.')');
		$this->db->group_by('inventory_counts_id');
				
		return $this->db->get()->num_rows();
	}
	
}
?>