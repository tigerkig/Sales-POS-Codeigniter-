<?php
require_once ("Report.php");
class Summary_store_accounts extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_customer'), 'align'=> 'left'), array('data'=>lang('common_credit_limit'), 'align'=> 'right'), array('data'=>lang('common_balance'), 'align'=> 'right'), array('data'=>lang('common_pay'), 'align'=> 'right'));
	}
	
	public function getData()
	{
		$this->db->select('CONCAT(first_name, " ",last_name) as customer, balance, credit_limit, customers.person_id', false);
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);
		
		if ($this->params['show_accounts_over_credit_limit'])
		{
			$this->db->where('balance > credit_limit');
		}
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		return $this->db->get()->result_array();		
	}
	
	
	public function getTotalRows()
	{
		$this->db->select('CONCAT(first_name, " ",last_name) as customer, balance, customers.person_id', false);
		$this->db->from('customers');
		$this->db->join('people', 'customers.person_id = people.person_id');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);
		
		if ($this->params['show_accounts_over_credit_limit'])
		{
			$this->db->where('balance > credit_limit');
		}
		
		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		$this->db->select('SUM(balance) as total', false);
		$this->db->from('customers');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);
		
		if ($this->params['show_accounts_over_credit_limit'])
		{
			$this->db->where('balance > credit_limit');
		}
		
		
		return $this->db->get()->row_array();		
	}
}
?>