<?php
require_once ("Report.php");
class Detailed_inventory extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{				
		$columns = array(array('data'=>lang('common_item_id'), 'align'=> 'left'), array('data'=>lang('reports_date'), 'align' => 'left'), array('data'=>lang('reports_item_name'), 'align' => 'left'), array('data'=>lang('common_category'), 'align'=>'left'), array('data'=>lang('common_item_number'), 'align' => 'left'), array('data'=>lang('common_product_id'), 'align' => 'left'),array('data'=>lang('common_size'), 'align'=> 'right'), array('data'=>lang('common_items_in_out_qty'), 'align' => 'left'),array('data'=>lang('common_items_inventory_comments'), 'align' => 'left'));
		
		$location_count = count(self::get_selected_location_ids());
		
		if ($location_count > 1)
		{
			array_unshift($columns, array('data'=>lang('common_location'), 'align'=> 'left'));
			
		}
		
		return $columns;
		
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);

		$this->db->select('locations.name as location_name, inventory.*, items.*, categories.id as category_id,categories.name as category');
		$this->db->from('inventory');
		$this->db->join('items', 'items.item_id = inventory.trans_items');
		$this->db->join('locations', 'inventory.location_id = locations.location_id');
		$this->db->join('categories', 'items.category_id = categories.id', 'left outer');
		$this->db->where('trans_date BETWEEN '.$this->db->escape($this->params['start_date']).' and '.$this->db->escape($this->params['end_date']));
		$this->db->where('items.deleted', 0);
		$this->db->where('items.system_item',0);
		$this->db->where('trans_inventory !=', 0);
		$this->db->where_in('inventory.location_id', $location_ids);
		$this->db->order_by('trans_date', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
				
		//Hide POS XXX and RECV XXX
		if ($this->params['show_manual_adjustments_only'])
		{
			$sale_prefix = $this->config->item('sale_prefix');
			$recv_prefix = 'RECV';
			
			$this->db->not_like('trans_comment', $sale_prefix, 'both');
			$this->db->not_like('trans_comment', $recv_prefix, 'both');
			
		}
		
		if ($this->params['item_id'] != -1)
		{
			$this->db->where('trans_items', $this->params['item_id']);
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
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);

		$this->db->from('inventory');
		$this->db->where('trans_date BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"');
		$this->db->where_in('inventory.location_id', $location_ids);
		$this->db->where('trans_inventory !=', 0);
		
		//Hide POS XXX and RECV XXX
		if ($this->params['show_manual_adjustments_only'])
		{
			$sale_prefix = $this->config->item('sale_prefix');
			$recv_prefix = 'RECV';
			
			$this->db->not_like('trans_comment', $sale_prefix, 'both');
			$this->db->not_like('trans_comment', $recv_prefix, 'both');
			
		}
		
		if ($this->params['item_id'] != -1)
		{
			$this->db->where('trans_items', $this->params['item_id']);
		}
		
		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>