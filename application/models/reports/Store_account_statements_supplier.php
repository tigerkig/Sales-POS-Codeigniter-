<?php
require_once ("Report.php");
class Store_account_statements_supplier extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array();	
	}
	
	public function getData()
	{
		$return = array();
		
		$supplier_ids_for_report = array();
		$supplier_id = $this->params['supplier_id'];
		
		if ($supplier_id == -1)
		{
			$this->db->select('person_id');
			$this->db->from('suppliers');
			$this->db->where('balance !=', 0);
			$this->db->where('deleted',0);
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
			$result = $this->db->get()->result_array();
			
			foreach($result as $row)
			{
				$supplier_ids_for_report[] = $row['person_id'];
			}
		}
		else
		{
			$this->db->select('person_id');
			$this->db->from('suppliers');
			$this->db->where('person_id', $supplier_id);
			$this->db->where('deleted',0);
			
			$result = $this->db->get()->row_array();
			
			if (!empty($result))
			{
				$supplier_ids_for_report[] = $result['person_id'];
			}
		}
				
		foreach($supplier_ids_for_report as $supplier_id)
		{
			$this->db->select("supplier_store_accounts.*,receivings.receiving_time");
			$this->db->from('supplier_store_accounts');
			$this->db->where('supplier_store_accounts.supplier_id', $supplier_id);
			$this->db->join('receivings', 'receivings.receiving_id = supplier_store_accounts.receiving_id', 'left');
			
			if ($this->params['pull_payments_by'] == 'payment_date')
			{
				$this->db->where('date >=', $this->params['start_date']);
				$this->db->where('date <=', $this->params['end_date']. '23:59:59');				
				$this->db->order_by('date');
			}
			else
			{
				$this->db->where('receiving_time >=', $this->params['start_date']);
				$this->db->where('receiving_time <=', $this->params['end_date']. '23:59:59');
				$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
			}
			
			
			$result = $this->db->get()->result_array();
			
			//If we don't have results from this month, pull the last store account entry we have
			if (count($result) == 0)
			{
				$this->db->select("supplier_store_accounts.*,receivings.receiving_time");
				$this->db->from('supplier_store_accounts');
				$this->db->where('supplier_store_accounts.supplier_id', $supplier_id);
				$this->db->join('receivings', 'receivings.receiving_id = supplier_store_accounts.receiving_id', 'left');
				$this->db->limit(1);
				if ($this->params['pull_payments_by'] == 'payment_date')
				{
					$this->db->order_by('date', 'DESC');
				}
				else
				{
					$this->db->order_by('receiving_time', 'DESC');
				}
			
				$this->db->limit(1); 	
				$result = $this->db->get()->result_array();
				
			}
			
			for ($k=0;$k<count($result);$k++)
			{
				$item_names = array();
				$receiving_id = $result[$k]['receiving_id'];
				
				$this->db->select('name, receivings_items.description');
				$this->db->from('items');
				$this->db->join('receivings_items', 'receivings_items.item_id = items.item_id');
				$this->db->where('receiving_id', $receiving_id);
				
				foreach($this->db->get()->result_array() as $row)
				{
					$item_name_and_desc = $row['name'];
				
					if ($row['description'])
					{
						$item_name_and_desc .= ' - '.$row['description'];
					}
					
					$item_names[] = $item_name_and_desc;
				}
								
				$result[$k]['items'] = implode(', ', $item_names);
			}
			$return[]= array('supplier_info' => $this->Supplier->get_info($supplier_id),'store_account_transactions' => $result);
		}
		
		return $return;
	}
	
	public function getTotalRows()
	{
		$supplier_id = $this->params['supplier_id'];
		
		if ($supplier_id == -1)
		{
			$this->db->distinct();
			$this->db->select('supplier_store_accounts.supplier_id');
			$this->db->from('supplier_store_accounts');
			$this->db->join('receivings', 'receivings.receiving_id = supplier_store_accounts.receiving_id');
			$this->db->where('balance !=', 0);
		}
		else
		{
			$this->db->distinct();
			$this->db->select('supplier_store_accounts.supplier_id');
			$this->db->from('supplier_store_accounts');
			$this->db->join('receivings', 'receivings.receiving_id = supplier_store_accounts.receiving_id');
			$this->db->where('supplier_store_accounts.supplier_id', $supplier_id);
		}
		
		return $this->db->get()->num_rows();
	}
	
	
	public function getSummaryData()
	{
		return array();
	}
}
?>