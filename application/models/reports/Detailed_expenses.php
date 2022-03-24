<?php
require_once ("Report.php");
class Detailed_expenses extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
			
		$columns =  array(
		array('data'=>lang('reports_id'), 'align'=>'left')
		, array('data'=>lang('common_type'), 'align'=> 'left')
		, array('data'=>lang('common_description'), 'align'=> 'left')
		, array('data'=>lang('common_category'), 'align'=> 'left')	
		, array('data'=>lang('common_reason'), 'align'=> 'left')
		, array('data'=>lang('common_date'), 'align'=> 'left')
		, array('data'=>lang('common_amount'), 'align'=> 'left')
		, array('data'=>lang('common_tax'), 'align'=> 'left')
		, array('data'=>lang('common_recipient_name'), 'align'=> 'left')
		, array('data'=>lang('common_approved_by'), 'align'=> 'left')
		, array('data'=>lang('common_expenses_note'), 'align'=> 'left')
		);
	
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
		$this->db->select('locations.name as location_name, categories.id as category_id,categories.name as category, expenses.*, CONCAT(recv.last_name, ", ", recv.first_name) as employee_recv, CONCAT(appr.last_name, ", ", appr.first_name) as employee_appr', false);
		$this->db->from('expenses');
		$this->db->join('people as recv', 'recv.person_id = expenses.employee_id','left');
		$this->db->join('people as appr', 'appr.person_id = expenses.approved_employee_id','left');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->join('locations', 'locations.location_id = expenses.location_id');
		$this->db->where_in('expenses.location_id', $location_ids);
		$this->db->where('expenses.deleted', 0);
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
 		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}
		$this->db->order_by('expenses.id');
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
		$this->db->select('SUM(expense_amount) as total_expenses,SUM(expense_tax) as total_taxes', false);
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
 		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}
		$this->db->where_in('expenses.location_id', $location_ids);
		return $this->db->get()->row_array();		
	}
	
	function getTotalRows()
	{
		$this->db->from('expenses');
		$this->db->where('deleted', 0);
		if (isset($this->params['start_date']) && isset($this->params['end_date']))
		{
		  $this->db->where($this->db->dbprefix('expenses').'.expense_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		}
		$this->db->join('people', 'expenses.employee_id = people.person_id', 'left');
		$this->db->order_by('id');
		return $this->db->count_all_results();
	}
	
}
?>