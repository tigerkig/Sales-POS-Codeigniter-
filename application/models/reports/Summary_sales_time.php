<?php
require_once ("Report.php");

class Summary_sales_time extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_time_range'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_number_of_transactions'), 'align'=> 'left');
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
		$this->db->select('sale_time, subtotal, total, tax, profit');
		$this->db->from('sales');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		$this->sale_time_where();
		$this->db->where('deleted', 0);
				
		$data = $this->db->get()->result_array();
		$time_ranges = $this->get_time_ranges();
		$return = array();
		
		foreach($data as $row)
		{
			$time = '';
			$sale_time_pieces = explode(' ',$row['sale_time']);
			$time_range_index = $this->get_time_range_index($sale_time_pieces[1]);

			if (!isset($return[$time_range_index]))
			{
				$return[$time_range_index] = array(
					'time_range' => $time_ranges[$time_range_index],
					'number_of_transactions' => 0,
					'subtotal' => 0,
					'total' => 0,
					'tax' => 0,
					'profit' => 0,
				);
			}
			
			$return[$time_range_index]['subtotal']+= $row['subtotal'];
			$return[$time_range_index]['total']+= $row['total'];
			$return[$time_range_index]['tax']+= $row['tax'];
			$return[$time_range_index]['profit']+= $row['profit'];
			
		}
		
		$this->db->select("time(sale_time) as time", FALSE);
		$this->db->from('sales');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->sale_time_where();
		$this->db->where('deleted', 0);
		$this->db->group_by('sale_id');
		
		$data = $this->db->get()->result_array();
		
		foreach($data as $row)
		{
			$time_range_index = $this->get_time_range_index($row['time']);
			$return[$time_range_index]['number_of_transactions']+=1;
		}
		
		ksort($return);
		return array_values($return);
	}
	
	function get_time_ranges()
	{
		$time_ranges = array();
		$time = mktime(0, 0, 0);
		
		$range_in_seconds = isset($this->params['interval']) && $this->params['interval'] && is_numeric($this->params['interval']) && $this->params['interval'] >=  1800 ? $this->params['interval'] : 7200;
		
		for ($i = 0; $i < 86400; $i += $range_in_seconds) 
		{
			$time_ranges[] = date(get_time_format(), $time + $i). ' - '.date(get_time_format(), $time + $i + $range_in_seconds);
		}
		
		$last_time = $time_ranges[count($time_ranges)-1];
		$last_time_end_range = substr($last_time, strpos($last_time,'- ') + 2);

		//Our last date should always be 11:59 pm midnight to prevnt losing dates
		$time_ranges[count($time_ranges)-1] = str_replace($last_time_end_range,date(get_time_format(),strtotime('midnight - 1 second')), $time_ranges[count($time_ranges)-1]);

		return $time_ranges;
	}
	
	function get_time_range_index($sale_time)
	{
		$time_ranges = $this->get_time_ranges();
		
		//This is a nice way to remove the seconds from a time that comes in...We don't want to use seconds for dates such as 11:59:xxx
		$sale_time = strtotime(date("H:i", strtotime($sale_time)));
		foreach($time_ranges as $index=>$range)
		{
			$times = explode(' - ', $range);
			$time_start = strtotime($times[0]);
			$time_end = strtotime($times[1]);
						
			if ($sale_time >=$time_start && $sale_time<=$time_end)
			{
				return $index;
			}
			
		}
		
		return -1;
	}
	
	
	function getTotalRows()
	{		
		return count($this->get_time_ranges());
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->sale_time_where();
		$this->db->where('deleted', 0);
		
		
		$this->db->group_by('sale_id');
		
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