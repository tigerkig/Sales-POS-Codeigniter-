<?php
require_once ("Report.php");
class Summary_tiers extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('common_tier_name'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_count'), 'align'=> 'right');

		return $columns;		
	}
	
	public function getData()
	{		
		$this->db->select('COUNT(tier_id) as count, price_tiers.name as tier_name');
		$this->db->from('sales'); 
		$this->db->join('price_tiers','sales.tier_id=price_tiers.id');
		$this->db->group_by('sales.tier_id');
		$this->db->where('sales.deleted', 0);
		$this->sale_time_where();
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
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
		$this->db->select('COUNT(id) as count');
		$this->db->from('price_tiers');
		
		$ret = $this->db->get()->row_array();
		return $ret['count'];
	}
	
	
	public function getSummaryData()
	{
		return array();
	}

}
?>