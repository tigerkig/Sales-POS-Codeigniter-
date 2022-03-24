<?php
require_once ("Report.php");
class Summary_payments_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_payment_type'), 'align'=> 'left'), array('data'=>lang('reports_total'), 'align'=> 'right'));
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		$recv_ids_for_payments = $this->get_receiving_ids_for_payments();

		$receivings_totals = array();
		
		$this->db->select('receiving_id, SUM(total) as total', false);
		$this->db->from('receivings');
		if (count($recv_ids_for_payments))
		{
			$this->db->group_start();
			$recv_ids_chunk = array_chunk($recv_ids_for_payments,25);
			foreach($recv_ids_chunk as $recv_ids)
			{
				$this->db->or_where_in('receiving_id',$recv_ids);
			}
			$this->db->group_end();
		}

		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_id');
		foreach($this->db->get()->result_array() as $receiving_total_row)
		{
			$receivings_totals[$receiving_total_row['receiving_id']] = to_currency_no_money($receiving_total_row['total'], 2);
		}
		$this->db->select('receivings_payments.receiving_id, receivings_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('receivings_payments');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_payments.receiving_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		if ($this->params['receiving_type'] == 'sales')
		{
			$this->db->where('payment_amount > 0');
		}
		elseif ($this->params['receiving_type'] == 'returns')
		{
			$this->db->where('payment_amount < 0');
		}
		
		$this->db->where($this->db->dbprefix('receivings').'.deleted', 0);
		$this->db->order_by('receiving_id, payment_date, payment_type');
				
		$receivings_payments = $this->db->get()->result_array();
		
		$payments_by_receiving = array();
		foreach($receivings_payments as $row)
		{
        	$payments_by_receiving[$row['receiving_id']][] = $row;
		}
		
		$payment_data = $this->Receiving->get_payment_data($payments_by_receiving,$receivings_totals);
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$payment_data = array_slice($payment_data, $this->params['offset'], $this->report_limit);
		}
		
		return $payment_data;
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('COUNT(DISTINCT('.$this->db->dbprefix('receivings_payments').'.payment_type)) as payment_count');
		$this->db->from('receivings_payments');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_payments.receiving_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		if ($this->params['receiving_type'] == 'sales')
		{
			$this->db->where('payment_amount > 0');
		}
		elseif ($this->params['receiving_type'] == 'returns')
		{
			$this->db->where('payment_amount < 0');
		}
		
		$this->db->where($this->db->dbprefix('receivings').'.deleted', 0);
		
		$ret = $this->db->get()->row_array();
		return $ret['payment_count'];
	}
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		$recv_ids_for_payments = $this->get_receiving_ids_for_payments();
		
		$receivings_totals = array();
		
		$this->db->select('receiving_id, SUM(total) as total', false);
		$this->db->from('receivings');
		if (count($recv_ids_for_payments))
		{
			$this->db->group_start();
			$recv_ids_chunk = array_chunk($recv_ids_for_payments,25);
			foreach($recv_ids_chunk as $recv_ids)
			{
				$this->db->or_where_in('receiving_id',$recv_ids);
			}
			$this->db->group_end();
		}
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_id');
		foreach($this->db->get()->result_array() as $receiving_total_row)
		{
			$receivings_totals[$receiving_total_row['receiving_id']] = to_currency_no_money($receiving_total_row['total'],2);
		}
		$this->db->select('receivings_payments.receiving_id, receivings_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('receivings_payments');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_payments.receiving_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		if ($this->params['receiving_type'] == 'sales')
		{
			$this->db->where('payment_amount > 0');
		}
		elseif ($this->params['receiving_type'] == 'returns')
		{
			$this->db->where('payment_amount < 0');
		}
		
		$this->db->where($this->db->dbprefix('receivings').'.deleted', 0);
		$this->db->order_by('receiving_id, payment_date, payment_type');
				
		$receivings_payments = $this->db->get()->result_array();
		
		$payments_by_receiving = array();
		foreach($receivings_payments as $row)
		{
        	$payments_by_receiving[$row['receiving_id']][] = $row;
		}
		
		$payment_data = $this->Receiving->get_payment_data($payments_by_receiving,$receivings_totals);		
		
		$return = array('total' => 0);
		foreach($payment_data as $payment)
		{
			$return['total']+=$payment['payment_amount'];
		}
				
		return $return;
	}
	
	function get_receiving_ids_for_payments()
	{
		$receiving_ids = array();
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('receivings_payments.receiving_id');
		$this->db->distinct();
		$this->db->from('receivings_payments');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_payments.receiving_id');
		$this->db->where('payment_date BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
	
		foreach($this->db->get()->result_array() as $receiving_row)
		{
			 $receiving_ids[] = $receiving_row['receiving_id'];
		}
		
		return $receiving_ids;
	}
}
?>