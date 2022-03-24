<?php
require_once ("Report.php");
class Store_account_outstanding_supplier extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{		
		
		return array(
			array('data'=>lang('reports_receiving_id'), 'align'=>'left'), 
			array('data'=>lang('reports_supplier'), 'align'=>'left'),
			array('data'=>lang('common_date'), 'align'=> 'left'), 
			array('data'=>lang('common_total_charge_to_account'), 'align'=> 'left'), 
			array('data'=>lang('common_comment'), 'align'=> 'left'),
			array('data'=>lang('reports_mark_as_paid').'/'.lang('reports_mark_as_unpaid'), 'align'=> 'left'),
		);
	}
	
	public function getData()
	{
		$this->db->select('CONCAT('.$this->db->dbprefix('suppliers').'.company_name, " "," (",supplier_person_info.first_name," ",supplier_person_info.last_name,")") as supplier_name, suppliers.account_number, receivings.receiving_id, receiving_time,SUM(transaction_amount) as payment_amount,receivings.comment', false);
		$this->db->from('supplier_store_accounts');
		$this->db->join('receivings','receivings.receiving_id = supplier_store_accounts.receiving_id');
		$this->db->join('receivings_payments', 'receivings.receiving_id = receivings_payments.receiving_id');
		$this->db->join('people as supplier_person_info', 'receivings.supplier_id = supplier_person_info.person_id');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id');
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		$this->db->where_in('receivings_payments.payment_type', $store_account_in_all_languages);
		$this->db->group_by('receivings.receiving_id');
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_store_accounts.supplier_id',$this->params['supplier_id']);
		}
		if (!$this->params['show_paid'])
		{
			$this->db->where('supplier_store_accounts.receiving_id NOT IN (SELECT receiving_id FROM '.$this->db->dbprefix('supplier_store_accounts_paid_receivings').' WHERE receiving_id is NOT NULL)');
		}
		$this->db->order_by('date',($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');


		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$return = $this->db->get()->result_array();
		
		for($k=0;$k<count($return);$k++)
		{
			$this->db->from('supplier_store_accounts_paid_receivings');
			$this->db->where('receiving_id', $return[$k]['receiving_id']);
			$query = $this->db->get();
			$paid = ($query->num_rows()>=1);
			
			if ($paid)
			{
				$return[$k]['paid'] = TRUE;
			}
			else
			{
				$return[$k]['paid'] = FALSE;
			}
		}
		
		return $return;
	}
	
	public function getSummaryData()
	{
		$this->db->select('SUM(transaction_amount) as total');
		$this->db->from('supplier_store_accounts');
		$this->db->join('receivings','receivings.receiving_id = supplier_store_accounts.receiving_id');
		$this->db->join('receivings_payments', 'receivings.receiving_id = receivings_payments.receiving_id');
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		$this->db->where_in('receivings_payments.payment_type', $store_account_in_all_languages);
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_store_accounts.supplier_id',$this->params['supplier_id']);
		}
		
		if (!$this->params['show_paid'])
		{
			$this->db->where('supplier_store_accounts.receiving_id NOT IN (SELECT receiving_id FROM '.$this->db->dbprefix('supplier_store_accounts_paid_receivings').' WHERE receiving_id is NOT NULL)');
		}
		return $this->db->get()->row_array();		
	}
	
	function getTotalRows()
	{
		$this->db->select('CONCAT(supplier_person_info.first_name," ",supplier_person_info.last_name) as supplier_name, suppliers.account_number, receivings.receiving_id, receiving_time,SUM(transaction_amount) as payment_amount,receivings.comment', false);
		$this->db->from('supplier_store_accounts');
		$this->db->join('receivings','receivings.receiving_id = supplier_store_accounts.receiving_id');
		$this->db->join('receivings_payments', 'receivings.receiving_id = receivings_payments.receiving_id');
		$this->db->join('people as supplier_person_info', 'receivings.supplier_id = supplier_person_info.person_id');
		$this->db->join('suppliers', 'receivings.supplier_id = suppliers.person_id');
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		$this->db->where_in('receivings_payments.payment_type', $store_account_in_all_languages);
		$this->db->group_by('receivings.receiving_id');
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_store_accounts.supplier_id',$this->params['supplier_id']);
		}
		if (!$this->params['show_paid'])
		{
			$this->db->where('supplier_store_accounts.receiving_id NOT IN (SELECT receiving_id FROM '.$this->db->dbprefix('supplier_store_accounts_paid_receivings').' WHERE receiving_id is NOT NULL)');
		}
		return $this->db->count_all_results();
	}
	
}
?>