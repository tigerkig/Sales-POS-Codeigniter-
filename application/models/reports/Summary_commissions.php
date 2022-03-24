<?php
require_once ("Report.php");
class Summary_commissions extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_employee'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$columns[] = array('data'=>lang('reports_commission'), 'align'=> 'right');
		
		return $columns;		
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$employee_column = $this->params['employee_type'] == 'logged_in_employee' ? 'employee_id' : 'sold_by_employee_id';
		$this->db->select($employee_column.', CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.commission) as commission', false);
		
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->db->where_in('sales.location_id', $location_ids);
		$this->sale_time_where();		
		$this->db->group_by($employee_column);

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		
		$qry1=$this->db->get_compiled_select();
		
		$this->db->select($employee_column.', CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.commission) as commission', false);
		
		$this->db->from('sales');
		$this->db->join('sales_item_kits', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		
		$this->sale_time_where();
		$this->db->group_by($employee_column);
		$this->db->where_in('sales.location_id', $location_ids);
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
				
		$qry2=$this->db->get_compiled_select();		
			
			if (isset($this->params['export_excel']) && !$this->params['export_excel'])
			{	
				$limit=$this->report_limit;
				$offset=$this->params['offset'];
				
				$query = $this->db->query("select $employee_column, employee, sum(subtotal) as subtotal,sum(total) as total, sum(tax) as tax,sum(profit) as profit,sum(commission) as commission from (".$qry1." UNION ".$qry2. ") as alias where employee is not null group by $employee_column limit ".$offset.",".$limit);
		
				return $query->result_array();	
			}
			else
			{
				
				$query = $this->db->query("select $employee_column, employee, sum(subtotal) as subtotal,sum(total) as total, sum(tax) as tax,sum(profit) as profit,sum(commission) as commission from (".$qry1." UNION ".$qry2. ") as alias where employee is not null group by $employee_column ");
			
				return $query->result_array();
			}
				
		
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$employee_column = $this->params['employee_type'] == 'logged_in_employee' ? 'employee_id' : 'sold_by_employee_id';
		
		$this->db->select('COUNT(DISTINCT(person_id)) as employee_count');
		$this->db->from('sales');		
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		
		$this->sale_time_where();
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		
		$ret = $this->db->get()->row_array();
		return $ret['employee_count'];
	}
	
	
	public function getSummaryData()
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$employee_column = $this->params['employee_type'] == 'logged_in_employee' ? 'employee_id' : 'sold_by_employee_id';
		$this->db->select('CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.commission) as commission', false);
		
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		$items= $this->db->get()->result_array();	

		
		$this->db->select('CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.commission) as commission', false);

		$this->db->from('sales');
		$this->db->join('sales_item_kits', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');

		$this->sale_time_where();
		
		$location_ids = self::get_selected_location_ids();
		
		$this->db->where_in('sales.location_id', $location_ids);
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		$item_kits = $this->db->get()->result_array();
		
		$result= $this->merge_item_and_item_kits($items, $item_kits);		

		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
			'commission' => 0,
		);
		
		foreach($result as $row)
		{ 
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
			$return['commission'] += to_currency_no_money($row['commission'],2);
		}
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
	
	private function merge_item_and_item_kits($items, $item_kits)
	{
		$location_ids = self::get_selected_location_ids();
		$new_items = array();
		$new_item_kits = array();
		
		foreach($items as $item)
		{
			$new_items[$item['commission']] = $item;
		}
		
		foreach($item_kits as $item_kit)
		{
			$new_item_kits[$item_kit['commission']] = $item_kit;
		}
		
		$merged = array();
		
		foreach($new_items as $commission=>$row)
		{
			if (!isset($merged[$commission]))
			{
				$merged[$commission] = $row;
			}
			else
			{
				$merged[$category]['commission']+= $row['subtotal'];
				$merged[$category]['commission']+= $row['total'];
				$merged[$category]['commission']+= $row['tax'];
				$merged[$category]['commission']+= $row['profit'];
			}
		}
		
		foreach($new_item_kits as $commission=>$row)
		{
			if (!isset($merged[$commission]))
			{
				$merged[$commission] = $row;
			}
			else
			{
				$merged[$commission]['subtotal']+= $row['subtotal'];
				$merged[$commission]['total']+= $row['total'];
				$merged[$commission]['tax']+= $row['tax'];
				$merged[$commission]['profit']+= $row['profit'];
				
				
			}
		}
		
		
		return $merged;
	}
}

?>