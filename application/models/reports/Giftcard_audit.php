<?php
require_once ("Report.php");
class Giftcard_audit extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_giftcard_number'), 'align'=> 'left');
		$columns[] = array('data'=>lang('common_description'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_comment'), 'align'=> 'left');

		return $columns;		
	}
	
	public function getData()
	{
		$data = array();
		
		$this->db->from('giftcards_log');
		$this->db->join('giftcards', 'giftcards.giftcard_id = giftcards_log.giftcard_id');
		
		if ($this->params['giftcard_number'] != -1)
		{
			$this->db->where('giftcards.giftcard_number', $this->params['giftcard_number']);
		}
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$this->db->where('log_date >=',$this->params['start_date']);
		$this->db->where('log_date <=',$this->params['end_date']);
		$this->db->order_by('log_date', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		return $this->db->get()->result_array();
	}
	
	public function getTotalRows()
	{
		$this->db->from('giftcards_log');
		$this->db->join('giftcards', 'giftcards.giftcard_id = giftcards_log.giftcard_id');
		
		if ($this->params['giftcard_number'] != -1)
		{
			$this->db->where('giftcards.giftcard_number', $this->params['giftcard_number']);
		}
		
		$this->db->where('log_date >=',$this->params['start_date']);
		$this->db->where('log_date <=',$this->params['end_date']);

		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		return array();
	}
}
?>