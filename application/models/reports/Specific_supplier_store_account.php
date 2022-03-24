<?php
require_once ("Report.php");
class Specific_supplier_store_account extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_id'), 'align'=>'left'),
		array('data'=>lang('reports_time'), 'align'=> 'left'),
		array('data'=>lang('reports_receiving_id'), 'align'=> 'left'),
		array('data'=>lang('reports_debit'), 'align'=> 'left'),
		array('data'=>lang('reports_credit'), 'align'=> 'left'),
		array('data'=>lang('reports_balance'), 'align'=> 'left'),
		array('data'=>lang('reports_items'), 'align'=> 'left'),		
		array('data'=>lang('reports_comment'), 'align'=> 'left'));
		
	}
	
	public function getData()
	{
		$this->db->from('supplier_store_accounts');
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_id',$this->params['supplier_id']);
		}
		
		$this->db->where('date BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"');
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}
		
		$result = $this->db->get()->result_array();
		
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
		return $result;
	}
	
	public function getTotalRows()
	{
		$this->db->from('supplier_store_accounts');
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_id',$this->params['supplier_id']);
		}
		$this->db->where('date BETWEEN "'.$this->params['start_date'].'" and "'.$this->params['end_date'].'"');
		return $this->db->count_all_results();
	}
	
	
	public function getSummaryData()
	{

		if ($this->params['supplier_id'] != -1)
		{
			$summary_data=array('balance'=>$this->Supplier->get_info($this->params['supplier_id'])->balance);
			return $summary_data;
		}
		return array();
	}
}
?>