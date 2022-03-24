<?php
require_once ("Report.php");
class Summary_giftcards_sales extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{	
		$location_count = count(self::get_selected_location_ids());
		
		$return = array(
			array('data'=>lang('common_sale_date'), 'align'=>'left'), 
			array('data'=>lang('common_giftcards_giftcard_number'), 'align'=>'left'), 
			array('data'=>lang('reports_sales_generator_selectField_1'), 'align'=> 'left'),
			array('data'=>lang('reports_giftcard_sale_amount'), 'align'=> 'left'));
			
			$location_count = count(self::get_selected_location_ids());
		
			if ($location_count > 1)
			{
				array_unshift($return, array('data'=>lang('common_location'), 'align'=> 'left'));
			}
			
			return $return;
			
	}
	
	public function getData()
	{
		$this->db->select('customer_data.account_number as account_number, locations.name as location_name, sales.sale_time as sale_time, sales_items.item_unit_price as gift_card_sale_price, sales_items.description as giftcard_number, CONCAT(first_name," ",last_name) as customer_name', false);
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('locations', 'locations.location_id = sales.location_id');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'people.person_id = customers.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales.customer_id = customer_data.person_id', 'left');
		$this->db->where('items.name', lang('common_giftcard'));
		$this->db->where('sale_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		$this->db->order_by('sale_time', ($this->config->item('report_sort_order')) ? $this->config->item('report_sort_order') : 'asc');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', '0');

		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{
			$this->db->limit($this->report_limit);
			$this->db->offset($this->params['offset']);
		}

		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData()
	{
		$this->db->select('SUM(item_unit_price) as total', false);
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->where('items.name', lang('common_giftcard'));
		$this->db->where('sale_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		return $this->db->get()->row_array();
	}
	
	function getTotalRows()
	{
		$this->db->select('sales.sale_time as sale_time, sales_items.item_unit_price as gift_card_sale_price, sales_items.description as giftcard_number, CONCAT(first_name," ",last_name) as customer_name', false);
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'people.person_id = customers.person_id', 'left');
		$this->db->where('items.name', lang('common_giftcard'));
		$this->db->where('sale_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']));
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		return $this->db->count_all_results();
	}
	
}
?>