<?php
require_once ("Report.php");
class Summary_discounts extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('common_discount'), 'align'=> 'left'),array('data'=>lang('common_count').'/'.lang('reports_total'), 'align'=> 'left'));
	}
	
	public function getData()
	{
		$return = array();
		$this->db->select('CONCAT(discount_percent, "%") as discount, count(*) as summary', false);
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->sale_time_where();
		$this->db->where('discount_percent > 0');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		$this->db->group_by('sales_items.discount_percent');
				
		$qry1=$this->db->get_compiled_select();
				
		$this->db->select('CONCAT(discount_percent, "%") as discount, count(*) as summary', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->sale_time_where();
		$this->db->where('discount_percent > 0');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}

		$this->db->where('sales.deleted', 0);
		
		$this->db->group_by('sales_item_kits.discount_percent');
		
		$qry2=$this->db->get_compiled_select();
		
		$query = $this->db->query($qry1." UNION ".$qry2. "order by discount desc");
		$res=$query->result_array();
				
		$percent_discounts = $res;
		$return = $percent_discounts;
		
		$this->db->select('COUNT(*) as discount_count');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->sale_time_where();
		$this->db->where('items.name', lang('common_discount'));
		$this->db->where('sales.deleted', 0);
		
		$discount_count = $this->db->get()->row()->discount_count;
				
		$this->db->select('SUM(item_unit_price * quantity_purchased) as discount_total');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->sale_time_where();
		$this->db->where('items.name', lang('common_discount'));
		$this->db->where('sales.deleted', 0);
		

		$result=$this->db->get();				
		if ($result->num_rows() > 0)
		{
			$query_result = $result->result();
			$discount = $query_result[0]->discount_total;
			
			$return[] = array('discount_count' =>$discount_count, 'discount' => lang('common_discount'), 'summary' => to_currency(abs($discount)));
		}
		
		return $return;
	}
	
	function getTotalRows()
	{	
		$this->db->select('COUNT(DISTINCT(discount_percent)) as discount_count');
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->sale_time_where();
		$this->db->where('discount_percent > 0');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);

		$this->db->group_by('sales_items.discount_percent');
		
				
		$qry1=$this->db->get_compiled_select();
				
		$this->db->select('COUNT(DISTINCT(discount_percent)) as discount_count');
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->sale_time_where();
		$this->db->where('discount_percent > 0');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		
		$this->db->group_by('sales_item_kits.discount_percent');
		
		$qry2=$this->db->get_compiled_select();
		
		$query = $this->db->query($qry1." UNION ALL ".$qry2);
		$ret=$query->row_array();

		return $ret['discount_count'] + 1; // + 1 for flat discount
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit', false);
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
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		
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