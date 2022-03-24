<?php
require_once ("Report.php");
class Detailed_timeclock extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		
		$columns = array();
		if (!isset($this->params['is_view_only_self']) || $this->params['is_view_only_self'] == false)
		{
			$columns[] = array('data'=>lang('common_edit'), 'align'=> 'left');
			$columns[] = array('data'=>lang('common_delete'), 'align'=> 'left');
			$columns[] = array('data'=>lang('reports_employee'), 'align'=> 'left');
		}
		$columns[] = array('data'=>lang('common_clock_in'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_clock_out'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_hours'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_hourly_pay_rate'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_total'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_clock_in_comment'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_clock_out_comment'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_ip_address_clock_in'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_ip_address_clock_out'), 'align'=> 'left');
		return $columns;
	}
	
	public function getData()
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
				
		$this->db->select('employees_time_clock.*, people.first_name, people.last_name');
		$this->db->from('employees_time_clock');
		$this->db->where('clock_in >=', $this->params['start_date']);
		$this->db->where('clock_in <=', $this->params['end_date']. ' 23:59:59');
		$this->db->where('location_id', $current_location);
		$this->db->join('employees', 'employees.person_id = employees_time_clock.employee_id');
		$this->db->join('people', 'people.person_id = employees.person_id');
		if ($this->params['employee_id'] != -1)
		{
			$this->db->where('employee_id', $this->params['employee_id']);
		}
		if (!isset($this->params['is_view_only_self']) || $this->params['is_view_only_self'] == false)
		{
			$this->db->order_by('employees_time_clock.id');			
		}
		else
		{
			$this->db->order_by('employees_time_clock.id DESC');
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
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->from('employees_time_clock');
		$this->db->where('clock_in >=', $this->params['start_date']);
		$this->db->where('clock_in <=', $this->params['end_date']. ' 23:59:59');
		$this->db->where('location_id', $current_location);
		
		if ($this->params['employee_id'] != -1)
		{
			$this->db->where('employee_id', $this->params['employee_id']);
		}
		
		$this->db->order_by('id');
		
		return $this->db->count_all_results();
		
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>