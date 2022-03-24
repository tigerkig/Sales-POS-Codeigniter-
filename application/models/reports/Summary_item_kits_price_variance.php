<?php
require_once ("Report.php");
class Summary_item_kits_price_variance extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{		
		$columns = array();
		
		$columns[] = array('data'=>lang('common_item'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_item_number'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_product_id'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_quantity_purchased'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_current_selling_price'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_variance'), 'align'=> 'left');
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
		$location_ids_string = implode(',',$location_ids);
				
		$this->db->select('item_kits.item_kit_id,item_kits.unit_price as current_selling_price, item_kits.name, item_kits.item_kit_number, item_kits.product_id, categories.name as category , item_kits.category_id, sum('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased) as quantity_purchased, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal)-sum(ROUND('.$this->db->dbprefix('sales_item_kits').'.regular_item_kit_unit_price_at_time_of_sale*'.$this->db->dbprefix('sales_item_kits').'.quantity_purchased,CASE WHEN '.$this->db->dbprefix('item_kits').'.tax_included =1 THEN 10 ELSE 2 END)) as variance_from_sale_price', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
		$this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
		$this->sale_time_where();
		$this->db->where('sales.deleted', 0);

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
						

		$this->db->group_by('item_kits.item_kit_id');
		$this->db->order_by('name');

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
		$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('sales_item_kits').'.item_kit_id)) as item_count');
		$this->db->from('sales_item_kits');		
		$this->db->join('item_kits', 'item_kits.item_kit_id = sales_item_kits.item_kit_id');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
			
		$ret = $this->db->get()->row_array();
		return $ret['item_count'];
	}
	
	public function getSummaryData()
	{		
		$this->db->select('sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit,sum('.$this->db->dbprefix('sales_item_kits').'.subtotal)-sum(ROUND('.$this->db->dbprefix('sales_item_kits').'.regular_item_kit_unit_price_at_time_of_sale*'.$this->db->dbprefix('sales_item_kits').'.quantity_purchased,CASE WHEN '.$this->db->dbprefix('item_kits').'.tax_included =1 THEN 10 ELSE 2 END)) as variance_from_sale_price', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
		$this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
				
		$this->sale_time_where();
		$this->db->where('sales.deleted', 0);

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_item_kits.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_item_kits.quantity_purchased < 0');
		}
				
		$this->db->group_by('sales_item_kits.sale_id');
		
		$return = array(
			'variance' => 0,
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['variance'] += to_currency_no_money($row['variance_from_sale_price'],2);
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