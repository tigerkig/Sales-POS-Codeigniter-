<?php
require_once ("Report.php");
class Summary_tags extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('common_tag'), 'align'=> 'left');
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
		$this->db->select('tags.name as tag, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.quantity_purchased) as item_sold', false);
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('items_tags', 'items_tags.item_id = items.item_id');
		$this->db->join('tags', 'tags.id = items_tags.tag_id');
		
		$this->db->group_start();
		$this->db->where('items.name !=', lang('common_discount'));
		$this->db->where('sales.deleted', 0);
		$this->db->or_where('items.name IS NULL');
		$this->db->group_end();
		
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_items.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_items.quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_tags']) && count($this->params['compare_to_tags']) > 0)
		{
			$this->db->where_in('tags.name', $this->params['compare_to_tags']);
		}				

		$this->db->group_by('tags.name');
		$this->db->order_by('tags.name');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		$items = $this->db->get()->result_array();	
		
		$this->db->select('tags.name as tag, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.quantity_purchased) as item_sold', false);
		$this->db->from('sales');
		$this->db->join('sales_item_kits', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
		$this->db->join('item_kits_tags', 'item_kits_tags.item_kit_id = item_kits.item_kit_id');
		$this->db->join('tags', 'tags.id = item_kits_tags.tag_id');
		$this->sale_time_where();
		
		$this->db->or_where('item_kits.name IS NULL');		
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales_item_kits.quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales_item_kits.quantity_purchased < 0');
		}
		
		if (isset($this->params['compare_to_tags']) && count($this->params['compare_to_tags']) > 0)
		{
			$this->db->where_in('tags.name', $this->params['compare_to_tags']);
		}	
		
		$this->db->where('sales.deleted', 0);

		$this->db->group_by('tags.name');
		$this->db->order_by('tags.name');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$item_kits = $this->db->get()->result_array();
		return $this->merge_item_and_item_kits($items, $item_kits);
	}
	
	private function merge_item_and_item_kits($items, $item_kits)
	{
		$new_items = array();
		$new_item_kits = array();
		
		foreach($items as $item)
		{
			$new_items[$item['tag']] = $item;
		}
		
		foreach($item_kits as $item_kit)
		{
			$new_item_kits[$item_kit['tag']] = $item_kit;
		}
		
		$merged = array();
		
		foreach($new_items as $tag=>$row)
		{
			if (!isset($merged[$tag]))
			{
				$merged[$tag] = $row;
			}
			else
			{
				$merged[$tag]['subtotal']+= $row['subtotal'];
				$merged[$tag]['total']+= $row['total'];
				$merged[$tag]['tax']+= $row['tax'];
				$merged[$tag]['profit']+= $row['profit'];
				$merged[$tag]['item_sold']+= $row['item_sold'];
			}
		}
		
		foreach($new_item_kits as $tag=>$row)
		{
			if (!isset($merged[$tag]))
			{
				$merged[$tag] = $row;
			}
			else
			{
				$merged[$tag]['subtotal']+= $row['subtotal'];
				$merged[$tag]['total']+= $row['total'];
				$merged[$tag]['tax']+= $row['tax'];
				$merged[$tag]['profit']+= $row['profit'];
				$merged[$tag]['item_sold']+= $row['item_sold'];
			}
		}
		
		
		return $merged;
	}
	
	public function getSummaryData()
	{
		return array();
	}
	
	function getTotalRows()
	{
		return $this->Tag->count_all();
	}
}
?>