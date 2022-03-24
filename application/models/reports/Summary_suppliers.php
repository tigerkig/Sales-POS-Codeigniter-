<?php
require_once ("Report.php");
class Summary_suppliers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_supplier'), 'align'=> 'left');
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
		$location_ids = self::get_selected_location_ids();		
		$this->db->select('CONCAT(company_name, " (",first_name, " ",last_name, ")") as supplier, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax,sum('.$this->db->dbprefix('sales_items').'.profit) as profit', false);
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id','left');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		
		$this->sale_time_where();
		$this->db->where_in('sales.location_id', $location_ids);
		
		$this->db->where('sales.deleted', 0);
		
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_items.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_items.quantity_purchased < 0');
		}
		
		
		$this->db->group_by('suppliers.id');
		$this->db->order_by('people.last_name');
		
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
		
		$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('people').'.person_id)) as supplier_count');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id','left');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		
		$this->db->where_in('sales.location_id', $location_ids);
		$this->sale_time_where();
		
		$this->db->where('sales.deleted', 0);
				

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_items.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_items.quantity_purchased < 0');
		}
						
		$ret = $this->db->get()->row_array();
		return $ret['supplier_count'];
	}
	
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$this->db->select('sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit', false);
		
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id','left');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		$this->sale_time_where();
		
		$this->db->where_in('sales.location_id', $location_ids);
		$this->db->where('sales.deleted', 0);
		
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_items.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_items.quantity_purchased < 0');
		}
		
			
		$this->db->group_by('sales.sale_id');
		
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