<?php

/*
Gets the html table to manage items.
*/
function get_orders_manage_table($orders,$controller)
{
	$CI =& get_instance();
	$CI->load->model('Employee');
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	
	$columns_to_display = $CI->Employee->get_sale_order_columns_to_display();

	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');

	$has_edit_permission = $CI->Employee->has_module_action_permission('deliveries','edit', $CI->Employee->get_logged_in_employee_info()->person_id);
	
	if ($has_edit_permission)
	{
		$headers[] = array('label' => lang('common_edit'), 'sort_column' => '');
	}
	
	foreach(array_values($columns_to_display) as $value)
	{
		$headers[] = $value;
	}
	
		
	$table.='<thead><tr>';
	$count = 0;
	foreach($headers as $header)
	{
		$count++;
		$label = $header['label'];
		$sort_col = $header['sort_column'];
		if ($count == 1)
		{
			$table.="<th data-sort-column='$sort_col' class='leftmost'>$label</th>";
		}
		elseif ($count == count($headers))
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_orders_manage_table_data_rows($orders,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_orders_manage_table_data_rows($orders,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($orders->result() as $order)
	{
		$table_data_rows.=get_order_data_row($order,$controller);
	}
	
	if($orders->num_rows()==0)
	{
		$table_data_rows.="<tr>
			<td colspan='13'><span class='col-md-12 text-center' ><span class='text-warning'>".lang('deliveries_no_deliveries')."</span></span></td>
		</tr>";
	}
		
	return $table_data_rows;
}

function delivery_status($status)
{
	return lang('deliveries_'.$status);
}

function get_order_data_row($order,$controller)
{
		$CI =& get_instance();	
		$controller_name=strtolower(get_class($CI));

		$table_data_row='<tr>';
		$table_data_row.="<td><input type='checkbox' id='order_$order->id' value='".$order->id."'/><label for='item_$order->id'><span></span></label></td>";		
		$displayable_columns = $CI->Employee->get_sale_order_columns_to_display();
		$CI->load->helper('text');
		$CI->load->helper('date');
		$CI->load->helper('currency');
		
		$has_edit_permission = $CI->Employee->has_module_action_permission('deliveries','edit', $CI->Employee->get_logged_in_employee_info()->person_id);
		
		if ($has_edit_permission)
		{
			$table_data_row.='<td class="">'.anchor($controller_name."/view/$order->id/2", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
		}	
		foreach($displayable_columns as $column_id => $column_values)
		{
			$val = $order->{$column_id};
			if (isset($column_values['format_function']))
			{
				if (isset($column_values['data_function']))
				{
					$data_function = $column_values['data_function'];
					$data = $data_function($order);
				}
				
				$format_function = $column_values['format_function'];
				
				if (isset($data))
				{
					$val = $format_function($val,$data);
				}
				else
				{
					$val = $format_function($val);					
				}
			}
			
			if (!isset($column_values['html']) || !$column_values['html'])
			{
				$val = H($val);
			}
			
			$table_data_row.='<td>'.$val.'</td>';
			//Unset for next round of the loop
			unset($data);
		}	
	
	$table_data_row.='</tr>';
	return $table_data_row;
}
?>