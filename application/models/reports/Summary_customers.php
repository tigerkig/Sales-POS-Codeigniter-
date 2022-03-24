<?php
require_once ("Report.php");
class Summary_customers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('common_person_id'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_customer'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_phone_number'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$columns[] = array('data'=>lang('reports_points_used'), 'align'=> 'left');
			$columns[] = array('data'=>lang('reports_points_earned'), 'align'=> 'left');
		}
		elseif ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			$columns[] = array('data'=>lang('common_sales_until_discount'), 'align'=> 'left');
		}
		$columns[] = array('data'=>lang('common_count'), 'align'=> 'right');
		
		return $columns;		
	}
	
	public function getData()
	{
		$this->db->select('COUNT(*) as count,customers.current_sales_for_discount, customer_id, CONCAT(first_name, " ",last_name) as customer, customers.person_id as person_id, people.phone_number, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales');
		$this->db->join('customers', 'customers.person_id = sales.customer_id');
		$this->db->join('people', 'customers.person_id = people.person_id');
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->sale_time_where();
		$this->db->where('sales.deleted', 0);
	
		$this->db->group_by('customer_id');
		
		if ($this->params['total_spent_condition'] != 'any' && is_numeric($this->params['total_spent_amount']))
		{
			$condition = '=';
			switch($this->params['total_spent_condition'])
			{
				case $this->params['total_spent_condition'] == 'greater_than':
					$condition = '>';
				break;

				case $this->params['total_spent_condition'] == 'less_than':
					$condition = '<';
				break;

				case $this->params['total_spent_condition'] == 'equal_to':
					$condition = '=';
				break;
				
			}
					
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
				
			$this->db->having('ROUND(sum(total),'.$decimals.') '.$condition.' '.$this->params['total_spent_amount']);
		}
		
		$this->db->order_by('last_name');
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$ret = $this->db->get()->result_array();
		
		if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		{
			$customer_ids = array(-1);
			
			for($k=0;$k<count($ret);$k++)
			{
				$customer_ids[] = $ret[$k]['customer_id'];
			}
			
			$this->db->select('customer_id, points_used, points_gained');
			$this->db->from('sales');
			$this->sale_time_where();
			
			$this->db->where_in('customer_id', $customer_ids);
			$this->db->group_by('sale_id');
			$cust_ret = $this->db->get()->result_array();
			
			$customers_points = array();
			
			for($j=0;$j<count($cust_ret);$j++)
			{
				$cust_row = $cust_ret[$j];
				
				if (!isset($customers_points[$cust_row['customer_id']]))
				{
					$customers_points[$cust_row['customer_id']] = array(
						'points_used' => $cust_row['points_used'],
						'points_gained' => $cust_row['points_gained'],
					);
				}
				else
				{
					$customers_points[$cust_row['customer_id']]['points_used']+=$cust_row['points_used'];
					$customers_points[$cust_row['customer_id']]['points_gained']+=$cust_row['points_gained'];
				}
			}
				
			for($p=0;$p<count($ret);$p++)
			{
				$ret[$p]['points_used'] = isset($customers_points[$ret[$p]['customer_id']]['points_used']) ? $customers_points[$ret[$p]['customer_id']]['points_used'] : 0;
				$ret[$p]['points_gained'] = isset($customers_points[$ret[$p]['customer_id']]['points_gained']) ? $customers_points[$ret[$p]['customer_id']]['points_gained'] : 0;
			}
		}
		
		return $ret;
	}
	
	public function getNoCustomerData()
	{
		$this->db->select('COUNT(*) as count,'.$this->db->escape(lang('reports_no_customer')).' as customer, "-" as person_id, "-" as phone_number, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
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
		$this->db->where('sales.deleted', 0);
		
		$this->db->where('customer_id',NULL);
		$this->db->group_by('customer_id');
		
		if ($this->params['total_spent_condition'] != 'any' && is_numeric($this->params['total_spent_amount']))
		{
			$condition = '=';
			switch($this->params['total_spent_condition'])
			{
				case $this->params['total_spent_condition'] == 'greater_than':
					$condition = '>';
				break;

				case $this->params['total_spent_condition'] == 'less_than':
					$condition = '<';
				break;

				case $this->params['total_spent_condition'] == 'equal_to':
					$condition = '=';
				break;
				
			}
					
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
				
			$this->db->having('ROUND(sum(total),'.$decimals.') '.$condition.' '.$this->params['total_spent_amount']);
		}

		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
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
		$this->db->where('sales.deleted', 0);
		
		
		$this->db->group_by('customer_id');
		
		if ($this->params['total_spent_condition'] != 'any' && is_numeric($this->params['total_spent_amount']))
		{
			$condition = '=';
			switch($this->params['total_spent_condition'])
			{
				case $this->params['total_spent_condition'] == 'greater_than':
					$condition = '>';
				break;

				case $this->params['total_spent_condition'] == 'less_than':
					$condition = '<';
				break;

				case $this->params['total_spent_condition'] == 'equal_to':
					$condition = '=';
				break;
				
			}
					
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
				
			$this->db->having('ROUND(sum(total),'.$decimals.') '.$condition.' '.$this->params['total_spent_amount']);
		}		
		
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
	
	function getTotalRows()
	{
		$this->db->select('sum(total) as total', false);
		$this->db->from('sales');
		$this->db->join('customers', 'customers.person_id = sales.customer_id');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->sale_time_where();
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		$this->db->group_by('customer_id');
		
		if ($this->params['total_spent_condition'] != 'any' && is_numeric($this->params['total_spent_amount']))
		{
			$condition = '=';
			switch($this->params['total_spent_condition'])
			{
				case $this->params['total_spent_condition'] == 'greater_than':
					$condition = '>';
				break;

				case $this->params['total_spent_condition'] == 'less_than':
					$condition = '<';
				break;

				case $this->params['total_spent_condition'] == 'equal_to':
					$condition = '=';
				break;
				
			}
					
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
				
			$this->db->having('ROUND(sum(total),'.$decimals.') '.$condition.' '.$this->params['total_spent_amount']);
		}
		
		$num_customers = $this->db->get()->num_rows();
		
		$this->db->select($this->db->escape(lang('reports_no_customer')).' as customer, "-" as person_id, "-" as phone_number, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
		$this->db->from('sales');
		$this->sale_time_where();
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		$this->db->where('customer_id',NULL);
		$this->db->group_by('customer_id');
		
		if ($this->params['total_spent_condition'] != 'any' && is_numeric($this->params['total_spent_amount']))
		{
			$condition = '=';
			switch($this->params['total_spent_condition'])
			{
				case $this->params['total_spent_condition'] == 'greater_than':
					$condition = '>';
				break;

				case $this->params['total_spent_condition'] == 'less_than':
					$condition = '<';
				break;

				case $this->params['total_spent_condition'] == 'equal_to':
					$condition = '=';
				break;
				
			}
					
			$decimals = $this->config->item('number_of_decimals') !== NULL && $this->config->item('number_of_decimals') != '' ? (int)$this->config->item('number_of_decimals') : 2;
				
			$this->db->having('ROUND(sum(total),'.$decimals.') '.$condition.' '.$this->params['total_spent_amount']);
		}
		
		
		$num_no_customers = $this->db->get()->num_rows();
		
		return $num_customers + $num_no_customers;
	}
	
}
?>