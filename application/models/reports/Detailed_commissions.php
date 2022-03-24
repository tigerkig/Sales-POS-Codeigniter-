<?php
require_once ("Report.php");
class Detailed_commissions extends Report
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
		$return['summary'][] = array('data'=>lang('reports_sale_id'), 'align'=> 'left');
		
		if ($location_count > 1)
		{
			$return['summary'][] = array('data'=>lang('common_location'), 'align'=> 'left');
		}
	
		$return['summary'][] = array('data'=>lang('reports_date'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('common_items_purchased'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_sold_to'), 'align'=> 'left');
		$return['summary'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
				
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['summary'][] = array('data'=>lang('common_profit'), 'align'=> 'right');
		}
		$return['summary'][] = array('data'=>lang('reports_commission'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_payment_type'), 'align'=> 'right');
		$return['summary'][] = array('data'=>lang('reports_comments'), 'align'=> 'right');

		$return['details'] = array();
		$return['details'][] = array('data'=>lang('reports_name'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_category'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_serial_number'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_description'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_quantity_purchased'), 'align'=> 'left');
		$return['details'][] = array('data'=>lang('reports_subtotal'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('reports_total'), 'align'=> 'right');
		$return['details'][] = array('data'=>lang('common_tax'), 'align'=> 'right');
		if($this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$return['details'][] = array('data'=>lang('common_profit'), 'align'=> 'right');			
		}
		$return['details'][] = array('data'=>lang('reports_commission'), 'align'=> 'right');			
		$return['details'][] = array('data'=>lang('common_discount'), 'align'=> 'right');			
		
		return $return;	
	}
	
	public function getData()
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$data = array();
		$data['summary'] = array();
		$data['details'] = array();
		
		$this->db->select('customer_data.account_number as account_number, locations.name as location_name, sales_items.sale_id, sale_time, date(sale_time) as sale_date, sum(quantity_purchased) as items_purchased, CONCAT(first_name," ",last_name) as customer_name, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.commission) as commission, payment_type, comment', false);
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->join('locations', 'sales.location_id = locations.location_id');
		$this->db->join('people', 'sales.customer_id = people.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales.customer_id = customer_data.person_id', 'left');
		
		$this->sale_time_where();
		
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('sales.employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sales.sold_by_employee_id', $employee_id);			
			}		
		}

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		$this->db->group_by('sales_items.sale_id');
		
		$qry1=$this->db->get_compiled_select();
		
		$this->db->select('customer_data.account_number as account_number, locations.name as location_name, sales_item_kits.sale_id, sale_time, date(sale_time) as sale_date, sum(quantity_purchased) as items_purchased, CONCAT(first_name," ",last_name) as customer_name, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.commission) as commission, payment_type, comment', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->db->join('locations', 'sales.location_id = locations.location_id');
		$this->db->join('people', 'sales.customer_id = people.person_id', 'left');
		$this->db->join('customers as customer_data', 'sales.customer_id = customer_data.person_id', 'left');

		$this->sale_time_where();
		
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('sales.employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sold_by_employee_id', $employee_id);			
			}		
		}

		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->where('sales.deleted', 0);
		
		$this->db->group_by('sales_item_kits.sale_id');
			
		$qry2=$this->db->get_compiled_select();		
		
		//If we are exporting NOT exporting to excel make sure to use offset and limit
		if (isset($this->params['export_excel']) && !$this->params['export_excel'])
		{	
			$limit=$this->report_limit;
			$offset=$this->params['offset'];
			
			$query = $this->db->query(" select account_number, location_name, sale_id, sale_time,  sale_date, sum(items_purchased) as items_purchased,  customer_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(commission) as commission, payment_type,comment from (".$qry1." UNION ".$qry2. ") as alias where sale_id is not null group by sale_id limit ".$offset.",".$limit);
	
			$res = $query->result_array();
			return $res;				
				
				
			exit;
		}		
		
		if (isset($this->params['export_excel']) && $this->params['export_excel'] == 1)
		{
			
			
			$query = $this->db->query(" select account_number, location_name, sale_id, sale_time,  sale_date, sum(items_purchased) as items_purchased,  customer_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, sum(commission) as commission, payment_type,comment from (".$qry1." UNION ".$qry2. ") as alias where sale_id is not null group by sale_id");
	
			$res = $query->result_array();
			
			$data=array();
			$data['summary']=array();
			$data['details']=array();
		
		foreach($res as $sale_summary_row)
		{
			$data['summary'][$sale_summary_row['sale_id']] = $sale_summary_row; 
		}
		
		$sale_ids = array();
		
		foreach($data['summary'] as $sale_row)
		{
			$sale_ids[] = $sale_row['sale_id'];
		}
		
		$result= $this->get_report_details($sale_ids,1);
		
		
		foreach($result as $sale_item_row)
		{
			$data['details'][$sale_item_row['sale_id']][] = $sale_item_row;
		}
		
		return $data;
		exit;
		
		}
	}
	
	public function getTotalRows()
	{		
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$this->db->from('sales');
		
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('sales.employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sales.sold_by_employee_id', $employee_id);			
			}		
		}
		$this->sale_time_where();
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		return $this->db->count_all_results();

	}
	
	public function getSummaryData()
	{
		$employee_id = $this->Employee->get_logged_in_employee_info()->person_id;
		$can_view_all_employee_commissions = false;
		if ($this->Employee->has_module_action_permission('reports','view_all_employee_commissions', $employee_id))
		{
			$can_view_all_employee_commissions = true;
		}
		
		$employee_column = $this->params['employee_type'] == 'logged_in_employee' ? 'employee_id' : 'sold_by_employee_id';
		
		$this->db->select('CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_items').'.total) as total, sum('.$this->db->dbprefix('sales_items').'.tax) as tax, sum('.$this->db->dbprefix('sales_items').'.profit) as profit, sum('.$this->db->dbprefix('sales_items').'.commission) as commission', false);
		
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales_items.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		
		$this->sale_time_where();
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('sales.employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sales.sold_by_employee_id', $employee_id);			
			}		
		}
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		$this->db->group_by($employee_column);
		
		$qry1=$this->db->get_compiled_select();
		
		$this->db->select('CONCAT(first_name, " ",last_name) as employee, sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal, sum('.$this->db->dbprefix('sales_item_kits').'.total) as total, sum('.$this->db->dbprefix('sales_item_kits').'.tax) as tax, sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit, sum('.$this->db->dbprefix('sales_item_kits').'.commission) as commission', false);
		
		$this->db->from('sales');
		$this->db->join('sales_item_kits', 'sales_item_kits.sale_id = sales.sale_id');
		$this->db->join('employees', 'employees.person_id = sales.'.$employee_column);
		$this->db->join('people', 'employees.person_id = people.person_id');
		
		$this->sale_time_where();
		if ($this->params['employee_type'] == 'logged_in_employee')
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.employee_id', $this->params['employee_id']);
			}
			else
			{
				$this->db->where('sales.employee_id', $employee_id);
			}
		}
		else
		{
			if ($can_view_all_employee_commissions)
			{
				$this->db->where('sales.sold_by_employee_id', $this->params['employee_id']);	
			}
			else
			{
				$this->db->where('sales.sold_by_employee_id', $employee_id);			
			}		
		}
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		
		$this->db->where('sales.deleted', 0);
		
		if (!$can_view_all_employee_commissions)
		{
			$this->db->where('employees.person_id',$employee_id);
		}
		
		$this->db->group_by($employee_column);

		//If we are exporting NOT exporting to excel make sure to use offset and limit

		$qry2=$this->db->get_compiled_select();
		
		$offset= $this->params['offset'];
		$limit=20;
		
		$query = $this->db->query($qry1." UNION ".$qry2."limit ".$offset.", ".$limit);
		
		$res=$query->result_array();
		

		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
			'commission' => 0,
		);
		
		foreach($res as $row)
		{ 
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['tax'] += to_currency_no_money($row['tax'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
			$return['commission'] += to_currency_no_money($row['commission'],2);
			
			
		}
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
	
	function get_report_details($ids, $export_excel=0)
	{
		$this->db->select('sales_items.sale_id, items.category_id, items.item_number, items.product_id as item_product_id, items.name as item_name, categories.name as category, quantity_purchased, serialnumber, sales_items.description, subtotal, total, tax, profit, discount_percent, items.size as size, items.unit_price as current_selling_price, suppliers.company_name as supplier_name, suppliers.person_id as supplier_id, commission', false);
		
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id', 'left');
		$this->db->join('categories', 'categories.id = items.category_id', 'left');
		$this->db->join('suppliers', 'items.supplier_id = suppliers.person_id', 'left');
		if (!empty($ids))
		{
			$sale_ids_chunk = array_chunk($ids,25);
			$this->db->group_start();
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sales_items.sale_id', $sale_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}		
		$qry1=$this->db->get_compiled_select();
		
		$this->db->select('sales_item_kits.sale_id,item_kits.category_id, item_kits.item_kit_number as item_number, item_kits.product_id as item_product_id, item_kits.name as item_name, categories.name as category, quantity_purchased, NULL as serialnumber, sales_item_kits.description, subtotal, total, tax, profit, discount_percent, NULL as size, item_kits.unit_price as current_selling_price, NULL as supplier_name, NULL as supplier_id,commission', false);
		
		$this->db->from('sales_item_kits');
		$this->db->join('item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id', 'left');
		$this->db->join('categories', 'categories.id = item_kits.category_id', 'left');
		if (!empty($ids))
		{
			$sale_ids_chunk = array_chunk($ids,25);
			$this->db->group_start();
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sales_item_kits.sale_id', $sale_ids);
			}
			$this->db->group_end();
		}
		else
		{
			$this->db->where('1', '2', FALSE);		
		}
		
		$qry2=$this->db->get_compiled_select();
		
		$query = $this->db->query($qry1." UNION ALL ".$qry2);
		//echo $this->db->last_query();exit;
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
			
				$details_data_row = array();
				$details_data_row[] = array('data'=>isset($drow['item_name']) ? $drow['item_name'] : $drow['item_kit_name'], 'align'=>'left');
				$details_data_row[] = array('data'=>$this->Category->get_full_path($drow['category_id']), 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['serialnumber'], 'align'=>'left');
				$details_data_row[] = array('data'=>$drow['description'], 'align'=>'left');
				$details_data_row[] = array('data'=>to_quantity($drow['quantity_purchased']), 'align'=>'left');
				
				$details_data_row[] = array('data'=>to_currency($drow['subtotal']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['total']), 'align'=>'right');
				$details_data_row[] = array('data'=>to_currency($drow['tax']), 'align'=>'right');
				
				if($this->has_profit_permission)
				{
					$details_data_row[] = array('data'=>to_currency($drow['profit']), 'align'=>'right');					
				}
				$details_data_row[] = array('data'=>to_currency($drow['commission']), 'align'=>'right');
								
				
				$details_data_row[] = array('data'=>$drow['discount_percent'].'%', 'align'=> 'left');
				
			
				
				$details_data[$key][$drow['sale_id']] = $details_data_row;
			}
		
		$data=array(
		"headers" => $this->getDataColumns(),
		"details_data" => $details_data
		);
		
		return $data;
	
	}
	private function merge_item_and_item_kits($items, $item_kits)
	{
		$new_items = array();
		$new_item_kits = array();
		
		foreach($items as $item)
		{
			$new_items[$item['commission']] = $item;
		}
		
		foreach($item_kits as $item_kit)
		{
			$new_item_kits[$item_kit['commission']] = $item_kit;
		}
		
		$merged = array();
		
		foreach($new_items as $commission=>$row)
		{
			if (!isset($merged[$commission]))
			{
				$merged[$commission] = $row;
			}
			else
			{
				$merged[$category]['commission']+= $row['subtotal'];
				$merged[$category]['commission']+= $row['total'];
				$merged[$category]['commission']+= $row['tax'];
				$merged[$category]['commission']+= $row['profit'];
				
				
			}
		}
		
		foreach($new_item_kits as $commission=>$row)
		{
			if (!isset($merged[$commission]))
			{
				$merged[$commission] = $row;
			}
			else
			{
				$merged[$commission]['subtotal']+= $row['subtotal'];
				$merged[$commission]['total']+= $row['total'];
				$merged[$commission]['tax']+= $row['tax'];
				$merged[$commission]['profit']+= $row['profit'];
				
				
			}
		}
		
		return $merged;
	}
}
?>