<?php
require_once ("Report.php");
class Specific_supplier_receiving extends Report
{
	function __construct()
	{		
		parent::__construct();
	}
	
	public function getDataColumns()
	{

		$return = array();
		
		$return['summary'] = array();
		$location_count = count(self::get_selected_location_ids());
		
		$return['summary'][] = array('data'=>lang('reports_receiving_id'), 'align'=> 'left');
		
		if ($location_count > 1)
		{
			$return['summary'][] = array('data'=>lang('common_location'), 'align'=> 'left');
		}
		
		
		$return['summary'][] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_items_ordered'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_qty_received'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('common_item_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_product_id'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_items_ordered'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('common_qty_received'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'right');
		
		return $return;		
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();

		$this->db->select('locations.name as location_name, receiving_id, receiving_time, date(receiving_time) as receiving_date, sum(total_quantity_purchased) as items_purchased, sum(total_quantity_received) as items_received, sum(total) as total, sum(subtotal) as subtotal, sum(tax) as tax, payment_type, comment', false);
		$this->db->from('receivings');
		$this->db->join('locations', 'locations.location_id = receivings.location_id');
		
		$this->db->where_in('receivings.location_id', $location_ids);
		$this->db->where('receiving_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and receivings.supplier_id='.$this->db->escape($this->params['supplier_id']));
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('recivings.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('receivings.total_quantity_purchased < 0');
		}

		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 0);
		
		$this->db->group_by('receivings.receiving_id');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
			return $this->db->get()->result_array();
			exit;
		}		
		
		foreach($this->db->get()->result_array() as $sale_summary_row)
		{
			$data['summary'][$sale_summary_row['receiving_id']] = $sale_summary_row; 
		}
		
		$receiving_ids = array();
		
		foreach($data['summary'] as $sale_row)
		{
			$receiving_ids[] = $sale_row['receiving_id'];
		}
		
			$result = $this->get_report_details($receiving_ids,1);
		
		foreach($result as $sale_item_row)
		{
			$data['details'][$sale_item_row['receiving_id']][] = $sale_item_row;
		}
		return $data;
		exit;
	}
	
	public function getTotalRows()
	{		
		$this->db->select("COUNT(receiving_id) as recv_count");
		$this->db->from('receivings');
		$this->db->where('receiving_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and receivings.supplier_id='.$this->db->escape($this->params['supplier_id']));
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 0);
		
		$ret = $this->db->get()->row_array();
		return $ret['recv_count'];
	}
	
	
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		$this->db->select('sum(total) as total,sum(subtotal) as subtotal, sum(tax) as tax', false);
		$this->db->from('receivings');
		$this->db->where('receiving_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and receivings.supplier_id='.$this->db->escape($this->params['supplier_id']));
		$this->db->where_in('receivings.location_id', $location_ids);
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('receivings.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('receivings.total_quantity_purchased < 0');
		}
		
		if ($this->config->item('hide_store_account_payments_from_report_totals'))
		{
			$this->db->where('receivings.store_account_payment', 0);
		}
		
		$this->db->where('receivings.deleted', 0);
		$this->db->where('receivings.suspended', 0);
		
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
	
	function get_report_details($ids, $export_excel=0)
	{
		$this->db->select('receivings_items.receiving_id, items.category_id, items.item_number, items.product_id, items.name, categories.name as category,quantity_purchased ,quantity_received , serialnumber, receivings_items.description, subtotal, total, tax, profit, discount_percent, items.size as size, items.unit_price as current_selling_price, suppliers.company_name as supplier_name, suppliers.person_id as supplier_id', false);
		$this->db->from('receivings_items');
		$this->db->join('items', 'receivings_items.item_id = items.item_id', 'left');
		$this->db->join('categories', 'categories.id = items.category_id', 'left');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left');
		
		if (!empty($ids))
		{
			$sale_ids_chunk = array_chunk($ids,25);
			$this->db->group_start();
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('receivings_items.receiving_id', $sale_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}		
		$qry=$this->db->get_compiled_select();
		

		$query = $this->db->query($qry);
		
		$res=$query->result_array();
		
		if($export_excel == 1)
		{
			return $res;
			exit;
		}
		$this->load->model('Category');
		$details_data = array();
		foreach($res as $key=>$drow)
			{	
				$details_data_row = array();
				$details_data_row[] = array('data'=>$drow['item_number'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
	
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_received']), 'align'=>'left');
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
				$details_data[$key][$drow['receiving_id']] = $details_data_row;
			}
		
		$data=array(
		"headers" => $this->getDataColumns(),
		"details_data" => $details_data
		);
		
		return $data;
	
	}
}
?>