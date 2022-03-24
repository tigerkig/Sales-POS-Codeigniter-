<?php
require_once ("Report.php");
class Summary_giftcards extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('common_giftcards_giftcard_number'), 'align'=>'left'),array('data'=>lang('common_description'), 'align'=> 'left'), array('data'=>lang('common_giftcards_card_value'), 'align'=> 'left'), array('data'=>lang('reports_sales_generator_selectField_1'), 'align'=> 'left'));
	}
	
	public function getData()
	{
		$this->db->select('customer_data.account_number as account_number, giftcard_number, description, value, CONCAT(first_name, " ",last_name) as customer_name', false);
		$this->db->from('giftcards');
		$this->db->where('giftcards.deleted', 0);
		$this->db->join('people', 'giftcards.customer_id = people.person_id', 'left');
		$this->db->join('customers as customer_data', 'people.person_id = customer_data.person_id', 'left');
		$this->db->order_by('giftcard_number');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData()
	{
		$this->db->select('SUM(value) as total_liabilities', false);
		$this->db->from('giftcards');
		$this->db->where('deleted', 0);
		return $this->db->get()->row_array();		
	}
	
	function getTotalRows()
	{
		$this->db->from('giftcards');
		$this->db->where('deleted', 0);
		$this->db->join('people', 'giftcards.customer_id = people.person_id', 'left');
		$this->db->order_by('giftcard_number');
		
		return $this->db->count_all_results();
	}
	
}
?>