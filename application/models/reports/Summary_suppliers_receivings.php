<?php
require_once ("Report.php");
class Summary_suppliers_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$columns = array();
		
		$columns[] = array('data'=>lang('reports_supplier'), 'align'=> 'left');
		$columns[] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$columns[] = array('data'=>lang('reports_total'), 'align'=> 'right');		
		$columns[] = array('data'=>lang('common_tax'), 'align'=> 'right');		
		return $columns;		
	}
	
	public function getData()
	{
		$this->db->select('CONCAT(company_name, " (",first_name, " ",last_name, ")") as supplier, sum('.$this->db->dbprefix('receivings').'.subtotal) as subtotal, sum('.$this->db->dbprefix('receivings').'.total) as total, sum('.$this->db->dbprefix('receivings').'.tax) as tax, sum('.$this->db->dbprefix('receivings').'.profit) as profit', false);
		$this->db->from('receivings');
		$this->db->join('suppliers', 'suppliers.person_id = receivings.supplier_id','left');
		$this->db->join('people', 'suppliers.person_id = people.person_id', 'left');
		
		$this->receiving_time_where();
		$this->db->where('receivings.deleted', 0);
		//$this->db->where('receivings.suspended', 0);
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('receivings.store_account_payment', 0);

		}

		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('receivings.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('receivings.total_quantity_purchased < 0');
		}
			
		$this->db->group_by('receivings.supplier_id');
		$this->db->order_by('people.last_name');
		
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
		$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('people').'.person_id)) as supplier_count');
		$this->db->from('receivings');
		$this->db->join('suppliers', 'suppliers.person_id = receivings.supplier_id');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		
		$this->receiving_time_where();
		$this->db->where('receivings.deleted', 0);
		//$this->db->where('receivings.suspended', 0);
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('receivings.store_account_payment', 0);

		}
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('receivings.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('receivings.total_quantity_purchased < 0');
		}
			
		$ret = $this->db->get()->row_array();
		return $ret['supplier_count'];
	}
	
	
	public function getSummaryData()
	{
		$this->db->select('receivings.subtotal, receivings.total, receivings.tax, receivings.profit', false);
		$this->db->from('receivings');
		$this->db->join('suppliers', 'suppliers.person_id = receivings.supplier_id');
		$this->db->join('people', 'suppliers.person_id = people.person_id');
		
		$this->receiving_time_where();
		$this->db->where('receivings.deleted', 0);
		//$this->db->where('receivings.suspended', 0);
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('receivings.store_account_payment', 0);

		}
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('receivings.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('receivings.total_quantity_purchased < 0');
		}		
				
		$this->db->group_by('receivings.receiving_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
		);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
		}
		return $return;
	}
}
?>