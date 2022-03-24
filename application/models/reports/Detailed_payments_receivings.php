<?php
require_once ("Report.php");
class Detailed_payments_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$return = array();
		
		$return['summary'] = array();
		$return['summary'][] = array('data'=>lang('reports_receiving_id'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_receiving_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_payment_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_payment_amount'), 'align'=> 'left');
				

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('reports_payment_date'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_payment_amount'), 'align'=> 'left');
		
		return $return;
	}
	
	
	public function getData()
	{
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();
		$data['details']['receiving_ids'] = array();
				
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
		$this->db->select('receivings.receiving_time, receivings_payments.receiving_id, receivings_payments.payment_type, payment_amount, payment_date, payment_id', false);
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
		
		$payment_data = $this->Receiving->get_payment_data_grouped_by_receiving($payments_by_receiving,$receivings_totals);
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$payment_data = array_slice($payment_data, $this->params['offset'], $this->report_limit, TRUE);
		}
		
		$data['summary'] = $payment_data;
		$receiving_ids = array();
		
		foreach(array_keys($data['summary']) as $receiving_id)
		{
			$receiving_ids[] = $receiving_id;
		}
		
		
		$receivings_totals = array();
		
		$this->db->select('receiving_id, SUM(total) as total', false);
		$this->db->from('receivings');
		$this->receiving_time_where();
		$this->db->where('deleted', 0);
		$this->db->group_by('receiving_id');
		foreach($this->db->get()->result_array() as $receiving_total_row)
		{
			$receivings_totals[$receiving_total_row['receiving_id']] = $receiving_total_row['total'];
		}
		
		$this->db->select('receivings.receiving_time, receivings_payments.receiving_id, receivings_payments.payment_type, payment_amount, payment_date, payment_id', false);
		$this->db->from('receivings_payments');
		$this->db->join('receivings', 'receivings.receiving_id=receivings_payments.receiving_id');

		if (!empty($receiving_ids))
		{
			$receiving_ids_chunk = array_chunk($receiving_ids,25);
			$this->db->group_start();
			foreach($receiving_ids_chunk as $receiving_ids)
			{
				$this->db->or_where_in('receivings.receiving_id', $receiving_ids);
			}			
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}
		
		$this->db->order_by('receiving_id, payment_date, payment_type');
		
		$receivings_payments = $this->db->get()->result_array();
		
		$payments_by_receiving = array();
		foreach($receivings_payments as $row)
		{
        	$payments_by_receiving[$row['receiving_id']][] = $row;
		}
		
		$payment_data = $this->Receiving->get_payment_data_grouped_by_receiving($payments_by_receiving,$receivings_totals);
		foreach($payment_data as $receiving_id => $payments_row)
		{
			foreach($payments_row as $payment_type => $receiving_payment_row)
			{
				$data['details'][$receiving_id.'|'.$payment_type][] = $receiving_payment_row;
				$data['details']['receiving_ids'][$receiving_id][] = $receiving_payment_row;
			}
		}
		return $data;
	}
	
	public function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select("COUNT(payment_date) as payment_row_count", false);
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
		return $ret['payment_row_count'];
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