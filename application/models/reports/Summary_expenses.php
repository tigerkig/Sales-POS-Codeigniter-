<?php
require_once ("Report.php");
class Summary_expenses extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
			
		return array(
		array('data'=>lang('common_category'), 'align'=> 'left')	
			, array('data'=>lang('common_tax'), 'align'=> 'left')
			, array('data'=>lang('common_amount'), 'align'=> 'left')
		);
	}
	
	public function getData()
	{		
		$location_ids = self::get_selected_location_ids();
		
		$this->db->select('categories.id as category_id,categories.name as category, SUM(expense_amount) as expense_amount,SUM(expense_tax) as expense_tax', false);
		$this->db->from('expenses');
		$this->db->join('categories', 'categories.id = expenses.category_id','left');
		$this->db->where_in('expenses.location_id', $location_ids);
		$this->db->where('expenses.deleted', 0);
		$this->db->group_by('categories.id');
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