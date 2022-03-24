<?php
require_once ("Report.php");
class Summary_taxes extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array(array('data'=>lang('reports_tax_percent'), 'align'=>'left'),array('data'=>lang('reports_subtotal'), 'align'=>'left'), array('data'=>lang('common_tax'), 'align'=>'left'),array('data'=>lang('reports_total'), 'align'=>'left'));
	}
	
	public function getData()
	{
		$location_ids = self::get_selected_location_ids();
		$this->taxes_data = array();
	
		//Just one tax
		if ($this->getTotalRows() == 2)
		{
			$location_ids = self::get_selected_location_ids();
			$this->db->select('sales_items.subtotal, sales_items.total, sales_items.tax, sales_items.profit');
			$this->db->from('sales_items', 'sales.sale_id = sales_items.sale_id');
			$this->db->join('sales', 'sales.sale_id=sales_items.sale_id');
			$this->sale_time_where();
			$this->db->where_in('sales.location_id', $location_ids);
			$this->db->where('sales.deleted', 0);
			$this->db->where('sales_items.tax != 0');
		
			if ($this->params['sale_type'] == 'sales')
			{
				$this->db->where('sales_items.quantity_purchased > 0');
			}
			elseif ($this->params['sale_type'] == 'returns')
			{
				$this->db->where('sales_items.quantity_purchased < 0');
			}
			elseif($this->params['sale_type'] == 'exchanges')
			{
				$this->db->where('sales_items.quantity_purchased = 0');				
			}
		
			$this->db->where('sales.store_account_payment', 0);
				
			$return = array(
				'subtotal' => 0,
				'total' => 0,
				'tax' => 0,
				'profit' => 0,
			);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
				$return['total'] += to_currency_no_money($row['total'],2);
				$return['tax'] += to_currency_no_money($row['tax'],2);
				$return['profit'] += to_currency_no_money($row['profit'],2);
			}
			
			$this->db->select('sales_item_kits.subtotal, sales_item_kits.total, sales_item_kits.tax, sales_item_kits.profit');
			$this->db->from('sales_item_kits', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->join('sales', 'sales.sale_id=sales_item_kits.sale_id');
			$this->sale_time_where();
			$this->db->where_in('sales.location_id', $location_ids);
			$this->db->where('sales.deleted', 0);
			$this->db->where('sales_item_kits.tax != 0');
		
			if ($this->params['sale_type'] == 'sales')
			{
				$this->db->where('sales_item_kits.quantity_purchased > 0');
			}
			elseif ($this->params['sale_type'] == 'returns')
			{
				$this->db->where('sales_item_kits.quantity_purchased < 0');
			}
			elseif($this->params['sale_type'] == 'exchanges')
			{
				$this->db->where('sales_item_kits.quantity_purchased = 0');				
			}
			
		
			$this->db->where('sales.store_account_payment', 0);
			
			
			foreach($this->db->get()->result_array() as $row)
			{
				$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
				$return['total'] += to_currency_no_money($row['total'],2);
				$return['tax'] += to_currency_no_money($row['tax'],2);
				$return['profit'] += to_currency_no_money($row['profit'],2);
			}
						
			$name = lang('reports_taxable_sales');
			$this->taxes_data[$name]['name'] = $name;
			$this->taxes_data[$name]['subtotal'] = $return['subtotal'];
			$this->taxes_data[$name]['tax'] = $return['tax'];
			$this->taxes_data[$name]['profit'] = $return['profit'];
			$this->taxes_data[$name]['total'] = ($return['subtotal'] + $return['tax']);		
		}
		else //Many Taxes
		{
			$this->db->select('sales.sale_id, item_id,  line');
			$this->db->from('sales');
			$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
			
			$this->sale_time_where();
			$this->db->where_in('sales.location_id', $location_ids);
			$this->db->where('sales.deleted', 0);
			$this->db->where('sales.store_account_payment', 0);
			

			if ($this->params['sale_type'] == 'sales')
			{
				$this->db->where('sales.total_quantity_purchased > 0');
			}
			elseif ($this->params['sale_type'] == 'returns')
			{
				$this->db->where('sales.total_quantity_purchased < 0');
			}
			elseif($this->params['sale_type'] == 'exchanges')
			{
				$this->db->where('sales.total_quantity_purchased = 0');				
			}
		
			
			$counter = 0;
			foreach($this->db->get()->result_array() as $row)
			{
				if ($row['item_id'])
				{
					$reset_cache = $counter == 0 ? TRUE : FALSE;
					$this->getTaxesForItems($row['sale_id'], $row['item_id'], $row['line'], $this->taxes_data,$reset_cache);
				}
				
				$counter++;
				
			}
			
			$this->db->select('sales.sale_id, item_kit_id, line');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales_item_kits.sale_id = sales.sale_id');
			
			$this->sale_time_where();
			$this->db->where_in('sales.location_id', $location_ids);
			$this->db->where('sales.deleted', 0);		
			$this->db->where('sales.store_account_payment', 0);
			
			if ($this->params['sale_type'] == 'sales')
			{
				$this->db->where('sales.total_quantity_purchased > 0');
			}
			elseif ($this->params['sale_type'] == 'returns')
			{
				$this->db->where('sales.total_quantity_purchased < 0');
			}
			elseif($this->params['sale_type'] == 'exchanges')
			{
				$this->db->where('sales.total_quantity_purchased = 0');				
			}
			
		
			$counter = 0;
		
			foreach($this->db->get()->result_array() as $row)
			{
				if ($row['item_kit_id'])
				{
					$reset_cache = $counter == 0 ? TRUE : FALSE;
					
					$this->getTaxesForItemKits($row['sale_id'], $row['item_kit_id'], $row['line'], $this->taxes_data);			
				}
				
				$counter++;
				
			}
			
		}
		
		$this->getNonTaxableTotalForItemsAndItemKits($this->taxes_data);		
		return $this->taxes_data;
	}
	
	function getTotalRows()
	{
		$location_ids = self::get_selected_location_ids();
		$location_ids_string = implode(',',$location_ids);
		
		$this->db->select('COUNT(DISTINCT(CONCAT('.$this->db->dbprefix('sales_items_taxes').'.name,'.$this->db->dbprefix('sales_items_taxes').'.percent))) as tax_count', false);
		$this->db->from('sales_items_taxes');
		$this->db->join('sales', 'sales.sale_id=sales_items_taxes.sale_id');
		$this->db->where('sale_time BETWEEN '. $this->db->escape($this->params['start_date']). ' and '. $this->db->escape($this->params['end_date']).' and location_id IN('.$location_ids_string.')');
		$this->db->where('sales.store_account_payment',0);
		$this->db->where_in('sales.location_id', $location_ids);
		$this->db->where('sales.deleted', 0);
		$this->sale_time_where();
		
		
		$ret = $this->db->get()->row_array();
		
		//add 1 for non taxable
		return $ret['tax_count'] + 1;
	}
	
	function getTaxesForItems($sale_id, $item_id, $line, &$taxes_data,$reset_cache = FALSE)
	{
		static $all_tax_data;
		
		if ($reset_cache)
		{
			$all_tax_data = FALSE;
		}
		
		
		if (!$all_tax_data)
		{
			$this->db->select('sales_items_taxes.sale_id,sales_items_taxes.item_id,sales_items_taxes.line,name, percent, cumulative, item_unit_price, item_cost_price, quantity_purchased, discount_percent');
			$this->db->from('sales_items_taxes');
			$this->db->join('sales', 'sales.sale_id = sales_items_taxes.sale_id');
			$this->db->join('sales_items', 'sales_items_taxes.sale_id = '.$this->db->dbprefix('sales_items').'.sale_id and sales_items_taxes.item_id = '.$this->db->dbprefix('sales_items').'.item_id and sales_items_taxes.line='.$this->db->dbprefix('sales_items').'.line');
			$this->db->where($this->db->dbprefix('sales').'.sale_time >=', $this->params['start_date']);
			$this->db->where($this->db->dbprefix('sales').'.sale_time <=', $this->params['end_date']);
			$this->db->where('sales.deleted', 0);
			$this->db->order_by('sales_items_taxes.sale_id, sales_items_taxes.item_id, sales_items_taxes.cumulative');
			$all_tax_data_result = $this->db->get()->result_array();
			$all_tax_data = array();
			foreach($all_tax_data_result as $row)
			{
				$all_tax_data[$row['sale_id'].'|'.$row['item_id'].'|'.$row['line']][] = $row;
			}
						
		}
		
		if (isset($all_tax_data["$sale_id|$item_id|$line"]))
		{
			$tax_result = $all_tax_data["$sale_id|$item_id|$line"];
		
			for($k=0;$k<count($tax_result);$k++)
			{
				$row = $tax_result[$k];
				if ($row['cumulative'])
				{
					$previous_tax = $tax;
					$subtotal = ($row['item_unit_price']*$row['quantity_purchased']-$row['item_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
					$tax = ($subtotal + $tax) * ($row['percent'] / 100);
				}
				else
				{
					$subtotal = ($row['item_unit_price']*$row['quantity_purchased']-$row['item_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
					$tax = $subtotal * ($row['percent'] / 100);
				}
			
				if (empty($taxes_data[$row['name'].' ('.$row['percent'] . '%)']))
				{
					$taxes_data[$row['name'].' ('.$row['percent'] . '%)'] = array('name' => $row['name'].' ('.$row['percent'] . '%)', 'tax' => 0, 'subtotal' => 0, 'total' => 0, 'profit' => 0);
				}
						
			  $profit = $subtotal - ($row['item_cost_price']*$row['quantity_purchased']);
				
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['subtotal'] += to_currency_no_money($subtotal);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['tax'] += ($tax);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['total'] += ($subtotal+ $tax);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['profit'] += to_currency_no_money($profit);
			
			}
		}
	}
	
	function getTaxesForItemKits($sale_id, $item_kit_id, $line, &$taxes_data,$reset_cache = FALSE)
	{
		static $all_tax_data;
		
		if ($reset_cache)
		{
			$all_tax_data = FALSE;
		}
		
		
		if (!isset($all_tax_data))
		{
			$this->db->select('sales_item_kits_taxes.sale_id,sales_item_kits_taxes.item_kit_id,sales_item_kits_taxes.line,name, percent, cumulative, item_kit_unit_price, item_kit_cost_price, quantity_purchased, discount_percent');
			$this->db->from('sales_item_kits_taxes');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits_taxes.sale_id');
			$this->db->join('sales_item_kits', 'sales_item_kits_taxes.sale_id = '.$this->db->dbprefix('sales_item_kits').'.sale_id and sales_item_kits_taxes.item_kit_id = '.$this->db->dbprefix('sales_item_kits').'.item_kit_id and sales_item_kits_taxes.line='.$this->db->dbprefix('sales_item_kits').'.line');
			$this->db->where($this->db->dbprefix('sales').'.sale_time >=', $this->params['start_date']);
			$this->db->where($this->db->dbprefix('sales').'.sale_time <=', $this->params['end_date']);
			$this->db->where('sales.deleted', 0);
			$this->db->where('sales.store_account_payment',0);
			$this->db->order_by('sales_item_kits_taxes.sale_id, sales_item_kits_taxes.item_kit_id, sales_item_kits_taxes.cumulative');
			$all_tax_data_result = $this->db->get()->result_array();
			$all_tax_data = array();
			foreach($all_tax_data_result as $row)
			{
				$all_tax_data[$row['sale_id'].'|'.$row['item_kit_id'].'|'.$row['line']][] = $row;
			}
						
		}

		if (isset($all_tax_data["$sale_id|$item_kit_id|$line"]))
		{
			$tax_result = $all_tax_data["$sale_id|$item_kit_id|$line"];
			for($k=0;$k<count($tax_result);$k++)
			{
				$row = $tax_result[$k];
				if ($row['cumulative'])
				{
					$previous_tax = $tax;
					$subtotal = ($row['item_kit_unit_price']*$row['quantity_purchased']-$row['item_kit_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
					$tax = ($subtotal + $tax) * ($row['percent'] / 100);
				}
				else
				{
					$subtotal = ($row['item_kit_unit_price']*$row['quantity_purchased']-$row['item_kit_unit_price']*$row['quantity_purchased']*$row['discount_percent']/100);
					$tax = $subtotal * ($row['percent'] / 100);
				}
			
				if (empty($taxes_data[$row['name'].' ('.$row['percent'] . '%)']))
				{
					$taxes_data[$row['name'].' ('.$row['percent'] . '%)'] = array('name' => $row['name'].' ('.$row['percent'] . '%)', 'tax' => 0, 'subtotal' => 0, 'total' => 0, 'profit' => 0);				
				}
			
			  $profit = $subtotal - ($row['item_kit_cost_price']*$row['quantity_purchased']);
			
			
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['subtotal'] += to_currency_no_money($subtotal);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['tax'] += ($tax);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['total'] += ($subtotal+ $tax);
				$taxes_data[$row['name'].' ('.$row['percent'] . '%)']['profit'] += to_currency_no_money($profit);
			}
		}
	}
	
	function getNonTaxableTotalForItemsAndItemKits(&$taxes_data)
	{
		$location_ids = self::get_selected_location_ids();
		$this->db->select('sum('.$this->db->dbprefix('sales_items').'.subtotal) as subtotal,sum('.$this->db->dbprefix('sales_items').'.profit) as profit', false);
		$this->db->from('sales_items');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->sale_time_where();
		$this->db->where_in('sales.location_id', $location_ids);
		$this->db->where('sales.deleted', 0);
		$this->db->where('sales.store_account_payment',0);
		
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		elseif ($this->params['sale_type'] == 'exchanges')
		{
			$this->db->where('sales.total_quantity_purchased = 0');
		}
		
		$this->db->where('sales_items.tax',0);
		
		$items_non_tax = $this->db->get_compiled_select();
		
		
		$this->db->select('sum('.$this->db->dbprefix('sales_item_kits').'.subtotal) as subtotal,sum('.$this->db->dbprefix('sales_item_kits').'.profit) as profit', false);
		$this->db->from('sales_item_kits');
		$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
		$this->sale_time_where();
		$this->db->where_in('sales.location_id', $location_ids);
		$this->db->where('sales.deleted', 0);
		
		
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('sales.total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('sales.total_quantity_purchased < 0');
		}
		elseif ($this->params['sale_type'] == 'exchanges')
		{
			$this->db->where('sales.total_quantity_purchased = 0');
		}
		
		$this->db->where('sales_item_kits.tax',0);
		
		$item_kits_non_tax = $this->db->get_compiled_select();
		
		
		$result = $this->db->query($items_non_tax." UNION ALL ".$item_kits_non_tax);			
				
		$non_tax = $result->result_array();
		
		$subtotal = 0;
		$profit = 0;
		
		foreach($non_tax as $non_tax_row)
		{
			$subtotal+=$non_tax_row['subtotal'];
			$profit+=$non_tax_row['profit'];
		}
		
		$taxes_data[lang('reports_non_taxable')] = array(
			'name' => lang('reports_non_taxable'),
			'subtotal' => $subtotal,
			'total' => $subtotal,
			'tax' => 0,
			'profit' => $profit,
		);
			
	}
	public function getSummaryData()
	{
		$location_ids = self::get_selected_location_ids();
		
		$this->db->select('sale_id, location_id, date(sale_time) as sale_date, sum(subtotal) as subtotal, sum(total) as total, sum(profit) as profit', false);
		$this->db->from('sales');
		if ($this->params['sale_type'] == 'sales')
		{
			$this->db->where('total_quantity_purchased > 0');
		}
		elseif ($this->params['sale_type'] == 'returns')
		{
			$this->db->where('total_quantity_purchased < 0');
		}
		elseif ($this->params['sale_type'] == 'exchanges')
		{
			$this->db->where('total_quantity_purchased = 0');
		}
		
		$this->db->where('sales.store_account_payment', 0);
		
		$this->sale_time_where();
		$this->db->where('sales.deleted', 0);
		
		$this->db->where_in('sales.location_id', $location_ids);
		$this->db->group_by('sale_id');
		
		$return = array(
			'subtotal' => 0,
			'total' => 0,
			'tax' => 0,
			'profit' => 0,
		);
		
		foreach(array_values($this->taxes_data) as $row)
		{
			$return['tax'] += to_currency_no_money($row['tax'],2);
		}
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return['subtotal'] += to_currency_no_money($row['subtotal'],2);
			$return['total'] += to_currency_no_money($row['total'],2);
			$return['profit'] += to_currency_no_money($row['profit'],2);
		}
		if(!$this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			unset($return['profit']);
		}
		return $return;
	}
}
?>