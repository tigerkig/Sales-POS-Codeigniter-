<?php
require_once ("Report.php");
class Summary_timeclock extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_employee'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_hours'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_total'), 'align'=> 'left');

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
		$this->db->order_by('people.first_name');
				
		$data = $this->db->get()->result_array();
		
		$return = array();
		foreach($data as $row)
		{
			if (!isset($return[$row['employee_id']]))
			{
				$return[$row['employee_id']] = array('first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'hours' => 0, 'total' => 0);
			}
			
			if ($row['clock_out'] != '0000-00-00 00:00:00')
			{
				$data_row[] = array('data'=>date(get_date_format().' '.get_time_format(), strtotime($row['clock_out'])), 'align' => 'left');
				$t1 = strtotime ($row['clock_out']);
				$t2 = strtotime ($row['clock_in']);
				$diff = $t1 - $t2;
				$hours = $diff / ( 60 * 60 );
				
				//Not really the purpose of this function; but it rounds to 2 decimals
				$hours = to_currency_no_money($hours,2);
				$return[$row['employee_id']]['hours']+=$hours;
				$return[$row['employee_id']]['total']+=$hours * $row['hourly_pay_rate'];
			}			 
		}
		return array_values($return);
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
		$this->db->group_by('employee_id');
		return($this->db->get()->num_rows());
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>