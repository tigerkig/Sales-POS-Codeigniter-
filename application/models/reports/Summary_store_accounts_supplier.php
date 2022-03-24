<?php
require_once ("Report.php");
class Summary_store_accounts_supplier extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_supplier'), 'align'=> 'left'), array('data'=>lang('common_balance'), 'align'=> 'right'), array('data'=>lang('common_pay'), 'align'=> 'right'));
	}
	
	public function getData()
	{
		$this->db->select('CONCAT(company_name," (",first_name, " ",last_name, ")") as supplier, balance, suppliers.person_id', false);
		$this->db->from('suppliers');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);
			
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
		$this->db->select('CONCAT(first_name, " ",last_name) as supplier, balance, suppliers.person_id', false);
		$this->db->from('suppliers');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);
				
		return $this->db->count_all_results();
	}
	
	public function getSummaryData()
	{
		$this->db->select('SUM(balance) as total', false);
		$this->db->from('suppliers');
		$this->db->where('balance != 0');
		$this->db->where('deleted',0);		
		
		return $this->db->get()->row_array();		
	}
}
?>