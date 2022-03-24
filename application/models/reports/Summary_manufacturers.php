<?php
require_once ("Report.php");
class Summary_manufacturers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('common_manufacturer'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');

		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$columns[] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$columns[] = array('data'=>lang('common_items_sold'), 'align'=> 'right');
		return $columns;		
	}
	
	public function getData()
	{
		$this->db->select('items.manufacturer_id, manufacturers.name as manufacturer, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.quantity_purchased) as item_sold', false);
		
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('manufacturers', 'manufacturers.id = items.manufacturer_id', 'left');
		$this->sale_time_where();
		$this->db->group_start();
		$this->db->where('items.name !=', lang('common_discount'));
		$this->db->where('sales.deleted', 0);
		$this->db->or_where('items.name IS NULL');
		$this->db->group_end();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_manufacturers']) && count($this->params['compare_to_manufacturers']) > 0)
		{
			$this->db->where_in('items.manufacturer_id', $this->params['compare_to_manufacturers']);
		}	
		
		$this->db->group_by('manufacturers.id');
		$this->db->order_by('manufacturers.name');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		

		$items= $this->db->get()->result_array();	
		
		$this->db->select('item_kits.manufacturer_id, manufacturers.name as manufacturer, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased) as item_sold', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
		$this->db->join('manufacturers', 'manufacturers.id = item_kits.manufacturer_id', 'left');
		$this->sale_time_where();
		
		$this->db->group_start();
		$this->db->where('item_kits.name !=', lang('common_discount'));
		$this->db->where('sales.deleted', 0);		
		$this->db->or_where('item_kits.name IS NULL');
		$this->db->group_end();
				
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_manufacturers']) && count($this->params['compare_to_manufacturers']) > 0)
		{
			$this->db->where_in('item_kits.manufacturer_id', $this->params['compare_to_manufacturers']);
		}	

		$this->db->group_by('item_kits.manufacturer_id');
		$this->db->order_by('sales.sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$item_kits = $this->db->get()->result_array();		
		return $this->merge_item_and_item_kits($items, $item_kits);		
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('items.manufacturer_id, manufacturers.name as manufacturer, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.quantity_purchased) as item_sold', false);
		
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('manufacturers', 'manufacturers.id = items.manufacturer_id', 'left');
		$this->sale_time_where();
		
		$this->db->group_start();
		$this->db->where('items.name !=', lang('common_discount'));
		$this->db->where('sales.deleted', 0);		
		$this->db->or_where('items.name IS NULL');
		$this->db->group_end();
				
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_manufacturers']) && count($this->params['compare_to_manufacturers']) > 0)
		{
			$this->db->where_in('items.manufacturer_id', $this->params['compare_to_manufacturers']);
		}	
		
		$this->db->group_by('manufacturers.id');
		$this->db->order_by('manufacturers.name');

		$items= $this->db->get()->result_array();	
		
		$this->db->select('item_kits.manufacturer_id, manufacturers.name as manufacturer, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased) as item_sold', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
		$this->db->join('manufacturers', 'manufacturers.id = item_kits.manufacturer_id', 'left');
		
		$this->sale_time_where();
		$this->db->group_start();
		$this->db->where('item_kits.name !=', lang('common_discount'));
		$this->db->where('sales.deleted', 0);
		$this->db->or_where('item_kits.name IS NULL');
		$this->db->group_end();

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_manufacturers']) && count($this->params['compare_to_manufacturers']) > 0)
		{
			$this->db->where_in('item_kits.manufacturer_id', $this->params['compare_to_manufacturers']);
		}	
		
		
		$this->db->group_by('item_kits.manufacturer_id');
		$this->db->order_by('sales.sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		
		$item_kits = $this->db->get()->result_array();
		$result= $this->merge_item_and_item_kits($items, $item_kits);		

		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach($result as $row)
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
		$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('items').'.manufacturer_id)) as category_count');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('manufacturers', 'manufacturers.id = items.manufacturer_id');
		$this->sale_time_where();
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted',0);
		
		$ret = $this->db->get()->row_array();
		return $ret['category_count'];
	}
	
	private function merge_item_and_item_kits($items, $item_kits)
	{
		$new_items = array();
		$new_item_kits = array();
		
		foreach($items as $item)
		{
			$new_items[$item['manufacturer']] = $item;
		}
		
		foreach($item_kits as $item_kit)
		{
			$new_item_kits[$item_kit['manufacturer']] = $item_kit;
		}
		
		$merged = array();
		
		foreach($new_items as $manufacturer=>$row)
		{
			if (!isset($merged[$manufacturer]))
			{
				$merged[$manufacturer] = $row;
			}
			else
			{
				$merged[$category]['manufacturer']+= $row['subtotal'];
				$merged[$category]['manufacturer']+= $row['total'];
				$merged[$category]['manufacturer']+= $row['tax'];
				$merged[$category]['manufacturer']+= $row['profit'];
				$merged[$category]['manufacturer']+= $row['item_sold'];
				
			}
		}
		
		foreach($new_item_kits as $manufacturer=>$row)
		{
			if (!isset($merged[$manufacturer]))
			{
				$merged[$manufacturer] = $row;
			}
			else
			{
				$merged[$manufacturer]['subtotal']+= $row['subtotal'];
				$merged[$manufacturer]['total']+= $row['total'];
				$merged[$manufacturer]['tax']+= $row['tax'];
				$merged[$manufacturer]['profit']+= $row['profit'];
				$merged[$manufacturer]['item_sold']+= $row['item_sold'];
				
			}
		}
		
		
		return $merged;
	}
}
?>