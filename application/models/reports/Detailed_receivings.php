<?php
require_once ("Report.php");
class Detailed_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$return = array('summary' => array(
		array('data'=>lang('reports_receiving_id'), 'align'=>'left'), 
		array('data'=>lang('common_location'), 'align'=> 'left'),
		array('data'=>lang('reports_date'), 'align'=>'left'), 
		array('data'=>lang('reports_items_ordered'), 'align'=>'left'),
		array('data'=>lang('common_qty_received'), 'align'=>'left'), 
		array('data'=>lang('reports_received_by'), 'align'=>'left'), 
		array('data'=>lang('reports_supplied_by'), 'align'=>'left'),  
		array('data'=>lang('reports_subtotal'), 'align'=>'right'), 
		array('data'=>lang('reports_total'), 'align'=>'right'),  
		array('data'=>lang('common_tax'), 'align'=>'right'), 
		array('data'=>lang('reports_payment_type'), 'align'=>'left'), 
		array('data'=>lang('reports_comments'), 'align'=>'left')),
		'details' => array(
		array('data'=>lang('reports_name'), 'align'=>'left'),
		array('data'=>lang('common_product_id'), 'align'=> 'left'), 
		array('data'=>lang('reports_category'), 'align'=>'left'),
		array('data'=>lang('common_size'), 'align'=>'left'), 
		array('data'=>lang('reports_items_ordered'), 'align'=>'left'), 
		array('data'=>lang('common_qty_received'),'align'=>'left'), 
		array('data'=>lang('reports_subtotal'), 'align'=>'right'), 
		array('data'=>lang('reports_total'), 'align'=>'right'),  
		array('data'=>lang('common_tax'), 'align'=>'right'), array('data'=>lang('common_discount'), 'align'=>'left'))
		);		
		
		return $return;
	}
	
	public function getData()
	{
		$this->db->select('receivings.transfer_to_location_id, locations.name as location_name, receiving_id, date(receiving_time) as receiving_date, total_quantity_purchased as items_purchased,total_quantity_received as items_received, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(supplier.company_name, " (",people.first_name," ",people.last_name, ")") as supplier_name, subtotal, total, tax, sum(profit) as profit, payment_type, comment', false);
		$this->db->from('receivings');
		$this->db->join('locations', 'locations.location_id = receivings.location_id');
		$this->db->join('people as employee', 'receivings.employee_id = employee.person_id');
		$this->db->join('suppliers as supplier', 'receivings.supplier_id = supplier.person_id', 'left');
		$this->db->join('people as people', 'people.person_id = supplier.person_id', 'left');
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_id', $this->params['supplier_id']);			
		}
		
		$this->receiving_time_where();
		$this->db->where('receivings.deleted', 0);
		$this->db->group_by('receiving_id');
		$this->db->order_by('receiving_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
			return $this->db->get()->result_array();
			exit;
			
		}		
		if (isset($this->params['export_excel']) && $this->params['export_excel'] == 1)
		{
			
			$data=array();
			$data['summary']=array();
			$data['details']=array();
			
		foreach($this->db->get()->result_array() as $receiving_summary_row)
		{
			$data['summary'][$receiving_summary_row['receiving_id']] = $receiving_summary_row; 
		}
		
		$receiving_ids = array();
		
		foreach($data['summary'] as $receiving_row)
		{
			$receiving_ids[] = $receiving_row['receiving_id'];
		}
		$result = $this->get_report_details($receiving_ids,1);
		
		foreach($result as $receiving_item_row)
		{
			
			$data['details'][$receiving_item_row['receiving_id']][] = $receiving_item_row;
		}

		return $data;
		exit;
		
		}
	}
	
	public function getTotalRows()
	{		
		$this->db->select("COUNT(receiving_id) as receiving_count");
		$this->db->from('receivings');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_id', $this->params['supplier_id']);			
		}
		
		$this->receiving_time_where();
		$this->db->where('receivings.deleted', 0);
		$ret = $this->db->get()->row_array();
		return $ret['receiving_count'];

	}
	
	public function getSummaryData()
	{
		$this->db->select('sum(tax) as tax, sum(total) as total', false);
		$this->db->from('receivings');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		
		if ($this->params['supplier_id'] != -1)
		{
			$this->db->where('supplier_id', $this->params['supplier_id']);			
		}
		
		$this->receiving_time_where();
		$this->db->where('deleted', 0);
		return $this->db->get()->row_array();
	}
	
	function get_report_details($ids, $export_excel=0)
	{
		$this->db->select('receiving_id, items.category_id, items.item_number, items.product_id, items.name, categories.name as category,quantity_purchased ,quantity_received , serialnumber, receivings_items.description, subtotal, total, tax, profit, discount_percent, items.size as size, items.unit_price as current_selling_price, suppliers.company_name as supplier_name, suppliers.person_id as supplier_id', false);
		$this->db->from('receivings_items');
		$this->db->join('items', 'receivings_items.item_id = items.item_id', 'left');
		$this->db->join('categories', 'categories.id = items.category_id', 'left');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left');
		if (!empty($ids))
		{
			$receiving_ids_chunk = array_chunk($ids,25);
			$this->db->group_start();
			foreach($receiving_ids_chunk as $receiving_ids)
			{
				$this->db->or_where_in('receivings_items.receiving_id', $receiving_ids);
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
				$details_data_row[] = array('data'=>$drow['name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['product_id'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['size'], 'align'=>'left');
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