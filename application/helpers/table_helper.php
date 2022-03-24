<?php
/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$CI->load->model('Employee');
	$controller_name=strtolower(get_class($CI));
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	
	
	
	if ($controller_name == 'customers')
	{
		$columns_to_display = $CI->Employee->get_customer_columns_to_display();
	}
	elseif($controller_name == 'suppliers')
	{
		$CI->load->model('Supplier');
		$columns_to_display = $CI->Employee->get_supplier_columns_to_display();		
	}
	elseif($controller_name == 'employees')
	{
		$CI->load->model('Employee');
		$columns_to_display = $CI->Employee->get_employee_columns_to_display();		
	}
		
	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_actions'), 'sort_column' => '');
	
	foreach(array_values($columns_to_display) as $value)
	{
		$headers[] = $value;
	}
	
	$headers[] = array('label' => '&nbsp;', 'sort_column' => '');
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	$controller_name=strtolower(get_class($CI));
	
	
	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}
	
	if($people->num_rows()==0 && $controller_name != 'employees')
	{
		$table_data_rows.="<tr><td colspan='10'><span class='col-md-12 text-center' ><span class='text-warning'>".lang('common_no_persons_to_display')."</span>&nbsp;&nbsp;<a class='btn btn-primary' href='". site_url($controller_name.'/excel_import') ."'>". lang($controller_name.'_import_'.$controller_name)."</a></span></tr>";
	}
	elseif($people->num_rows()==0 && $controller_name == 'employees')
	{
		$table_data_rows.="<tr><td colspan='10'><span class='col-md-12 text-center' ><span class='text-warning'>".lang('common_no_persons_to_display')."</span></span></tr>";		
	}
	
	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	$CI =& get_instance();
	$CI->load->helper('people');
	$controller_name=strtolower(get_class($CI));
	$avatar_url=$person->image_id ?  app_file_url($person->image_id) : base_url('assets/assets/images/avatar-default.jpg');

	$table_data_row='<tr>';
	
	if ($controller_name != 'employees')
	{
		$table_data_row.="<td><input type='checkbox' id='${controller_name}_$person->person_id' value='".$person->person_id."'/><label for='${controller_name}_$person->person_id'><span></span></label></td>";
		$table_data_row.='<td class="rightmost">'.anchor($controller_name."/view/$person->person_id/2	", lang('common_edit') ,array('class'=>' ','title'=>lang($controller_name.'_update'))).'</li>'.'</td>';	
	}
	else
	{
		$table_data_row.="<td><input type='checkbox' id='item_$person->person_id' value='".$person->person_id."'/><label for='item_$person->person_id'><span></span></label></td>";
		$table_data_row.='<td class="rightmost">'.
						'<div class="piluku-dropdown dropup btn-group table_buttons">
							<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="ion-edit"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
								<li>'.anchor($controller_name."/view/$person->person_id/2	", '<i class="ion-compose"></i> ' . lang('common_edit').' ' . lang('common_employee') ,array('class'=>' ','title'=>lang($controller_name.'_update'))).'</li>';
							
								$table_data_row.= '<li>'. anchor($controller_name."/clone_employee/$person->person_id/", '<i class="ion-ios-browsers-outline"></i> ' . lang('common_clone') .' ' . lang('common_employee'), array('class'=>' ','title'=>lang('common_clone'))).'</li>';
							
							$table_data_row.= '</ul>
						</div>'
			.'</td>';	
	}	
	
	if ($controller_name == 'customers')
	{
		$displayable_columns = $CI->Employee->get_customer_columns_to_display();
	}
	elseif($controller_name == 'suppliers')
	{
		$CI->load->model('Supplier');
		$displayable_columns = $CI->Employee->get_supplier_columns_to_display();		
	}
	elseif($controller_name == 'employees')
	{
		$CI->load->model('Employee');
		$displayable_columns = $CI->Employee->get_employee_columns_to_display();		
	}
		
		
		$CI->load->helper('text');
		$CI->load->helper('date');
		$CI->load->helper('currency');
		foreach($displayable_columns as $column_id => $column_values)
		{
			$val = $person->{$column_id};
			if (isset($column_values['format_function']))
			{
				if (isset($column_values['data_function']))
				{
					$data_function = $column_values['data_function'];
					$data = $data_function($person);
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
	if ($avatar_url)
	{	
		$table_data_row.="<td><a href='$avatar_url' class='rollover'><img src='".$avatar_url."' alt='".H($person->full_name)."' class='img-polaroid' width='45' /></a></td>";
	}
	
	$table_data_row.='</tr>';
	return $table_data_row;	
}


/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$CI->load->model('Employee');
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	
	$columns_to_display = $CI->Employee->get_item_columns_to_display();

	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_actions'), 'sort_column' => '');
	
	
	foreach(array_values($columns_to_display) as $value)
	{
		$headers[] = $value;
	}
	
	$headers[] = array('label' => '&nbsp;', 'sort_column' => '');
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($items->result() as $item)
	{
		$table_data_rows.=get_item_data_row($item,$controller);
	}
	
	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr>
			<td colspan='13'><span class='col-md-12 text-center' ><span class='text-warning'>".lang('items_no_items_to_display')."</span>&nbsp;&nbsp;<a class='btn btn-primary' href='". site_url('items/excel_import') ."'>". lang('items_import_items')."</a></span></td>
		</tr>";
	}
		
	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	$low_inventory_class = "";
	
	$reorder_level = $item->location_reorder_level ? $item->location_reorder_level : $item->reorder_level;

	$controller_name=strtolower(get_class($CI));
	$avatar_url=$item->image_id ?  app_file_url($item->image_id) : base_url('assets/assets/images/default.png');

	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='item_$item->item_id' value='".$item->item_id."'/><label for='item_$item->item_id'><span></span></label></td>";
	$table_data_row.='<td class="rightmost">'.
					'<div class="piluku-dropdown dropup btn-group table_buttons">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="ion-edit"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li>'.anchor($controller_name."/view/$item->item_id/2	", '<i class="ion-compose"></i> ' . lang('common_edit').' ' . lang('common_item') ,array('class'=>' ','title'=>lang($controller_name.'_update'))).'</li>';
							
						if (!$item->is_service)
						{
							$table_data_row.= '<li>'. anchor($controller_name."/inventory/$item->item_id/", '<i class="ion-android-clipboard"></i> ' .  lang('common_item') .' ' . lang('common_inv') , array('class'=>'','title'=>lang($controller_name.'_count'))).'</li>';
						}
							
							$table_data_row.= '<li>'. anchor($controller_name."/clone_item/$item->item_id/", '<i class="ion-ios-browsers-outline"></i> ' . lang('common_clone') .' ' . lang('common_item'), array('class'=>' ','title'=>lang('common_clone'))).'</li>';
							
						$table_data_row.= '</ul>
					</div>'
		.'</td>';	
		
		$displayable_columns = $CI->Employee->get_item_columns_to_display();
		$CI->load->helper('text');
		$CI->load->helper('date');
		$CI->load->helper('currency');
		foreach($displayable_columns as $column_id => $column_values)
		{
			$val = $item->{$column_id};
			if (isset($column_values['format_function']))
			{
				if (isset($column_values['data_function']))
				{
					$data_function = $column_values['data_function'];
					$data = $data_function($item);
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
	if ($avatar_url)
	{	
		$table_data_row.="<td><a href='$avatar_url' class='rollover'><img src='".$avatar_url."' alt='".H($item->name)."' class='img-polaroid' width='45' /></a></td>";
	}
	
	$table_data_row.='</tr>';
	return $table_data_row;
}


/*
Gets the html table to manage items.
*/
function get_locations_manage_table($locations,$controller)
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter table table-hover" id="sortable_table">';	
	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_edit'), 'sort_column' => '');
	$headers[] = array('label' => lang('locations_location_id'), 'sort_column' => 'location_id');
	$headers[] = array('label' => lang('locations_name'), 'sort_column' => 'name');
	$headers[] = array('label' => lang('locations_address'), 'sort_column' => 'address');
	$headers[] = array('label' => lang('locations_phone'), 'sort_column' => 'phone');
	$headers[] = array('label' => lang('locations_email'), 'sort_column' => 'email');
		
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	
	$table.='</tr></thead><tbody>';
	$table.=get_locations_manage_table_data_rows($locations,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_locations_manage_table_data_rows($locations,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($locations->result() as $location)
	{
		$table_data_rows.=get_location_data_row($location,$controller);
	}
	
	if($locations->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><span class='col-md-12 text-center text-warning' >".lang('locations_no_locations_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_location_data_row($location,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));

	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='location_$location->location_id' value='".$location->location_id."'/><label for='location_$location->location_id'><span></span></label></td>";
	$table_data_row.='<td>'.anchor($controller_name."/view/$location->location_id/2", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
	$table_data_row.='<td>'.$location->location_id.'</td>';
	$table_data_row.='<td>'.H($location->name).'</td>';
	$table_data_row.='<td>'.H($location->address).'</td>';
	$table_data_row.='<td>'.H($location->phone).'</td>';
	$table_data_row.='<td>'.H($location->email).'</td>';
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage giftcards.
*/
function get_giftcards_manage_table( $giftcards, $controller )
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter table table-hover" id="sortable_table">';	
	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_edit'), 'sort_column' => '');
	$headers[] = array('label' => lang('common_giftcards_giftcard_number'), 'sort_column' => 'giftcard_number');
	$headers[] = array('label' => lang('common_giftcards_card_value'), 'sort_column' => 'value');
	$headers[] = array('label' => lang('common_description'), 'sort_column' => 'description');
	$headers[] = array('label' => lang('common_customer_name'), 'sort_column' => 'last_name');
	$headers[] = array('label' => lang('common_active').'/'.lang('common_inactive'), 'sort_column' => 'inactive');
	$headers[] = array('label' => lang('common_clone'), 'sort_column' => '');
		
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	
	$table.='</tr></thead><tbody>';
	$table.=get_giftcards_manage_table_data_rows( $giftcards, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the giftcard.
*/
function get_giftcards_manage_table_data_rows( $giftcards, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($giftcards->result() as $giftcard)
	{
		$table_data_rows.=get_giftcard_data_row( $giftcard, $controller );
	}
	
	if($giftcards->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='10'><span class='col-md-12 text-center' ><span class='text-warning'>".lang('giftcards_no_giftcards_to_display')."</span>&nbsp;&nbsp;<a class='btn btn-primary' href='". site_url('giftcards/excel_import') ."'>". lang('giftcards_import_giftcards')."</a></span></tr>";
	}
	
	return $table_data_rows;
}

function get_giftcard_data_row($giftcard,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$link = site_url('reports/detailed_'.$controller_name.'/'.$giftcard->customer_id.'/0');
	$cust_info = $CI->Customer->get_info($giftcard->customer_id);
	
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='giftcard_$giftcard->giftcard_id' value='".$giftcard->giftcard_id."'/><label for='giftcard_$giftcard->giftcard_id'><span></span></label></td>";
	$table_data_row.='<td>'.anchor($controller_name."/view/$giftcard->giftcard_id/2	", lang('common_edit'),array('class'=>' ','title'=>lang($controller_name.'_update'))).'</td>';		
	$table_data_row.='<td>'.H($giftcard->giftcard_number).'</td>';
	$table_data_row.='<td>'.to_currency(H($giftcard->value), 10).'</td>';
	$table_data_row.='<td>'.H($giftcard->description).'</td>';
	$table_data_row.='<td><a class="underline" href="'.$link.'">'.H($cust_info->first_name). ' '.H($cust_info->last_name).'</a></td>';
	$table_data_row.='<td>'.($giftcard->inactive ? lang('common_inactive') : lang('common_active')).'</td>';
	$table_data_row.='<td class="rightmost">'.anchor($controller_name."/clone_giftcard/$giftcard->giftcard_id", lang('common_clone'),array('class'=>' ','title'=>lang('common_clone'))).'</td>';			
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage item kits.
*/
function get_item_kits_manage_table( $item_kits, $controller )
{
	$CI =& get_instance();
	$CI->load->model('Employee');
	$table='<table class="table tablesorter table-hover" id="sortable_table">';	
	$columns_to_display = $CI->Employee->get_item_kit_columns_to_display();

	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_actions'), 'sort_column' => '');
	
	
	foreach(array_values($columns_to_display) as $value)
	{
		$headers[] = $value;
	}
	
	$headers[] = array('label' => '&nbsp;', 'sort_column' => '');
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_item_kits_manage_table_data_rows($item_kits,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the item kits.
*/
function get_item_kits_manage_table_data_rows( $item_kits, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($item_kits->result() as $item_kit)
	{
		$table_data_rows.=get_item_kit_data_row( $item_kit, $controller );
	}
	
	if($item_kits->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='9'><span class='col-md-12 text-center text-warning' >".lang('item_kits_no_item_kits_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_item_kit_data_row($item_kit,$controller)
{

	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	
	$has_cost_price_permission = $CI->Employee->has_module_action_permission('item_kits','see_cost_price', $CI->Employee->get_logged_in_employee_info()->person_id);
		
	$table_data_row ='<tr>';
	$table_data_row.="<td><input type='checkbox' id='item_kit_$item_kit->item_kit_id' value='".$item_kit->item_kit_id."'/><label for='item_kit_$item_kit->item_kit_id'><span></span></label></td>";
		
	$table_data_row.='<td class="rightmost">'.
					'<div class="piluku-dropdown dropup btn-group table_buttons">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<span class="ion-edit"></span>
						</button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li>'.anchor($controller_name."/view/$item_kit->item_kit_id/2", '<i class="ion-compose"></i> ' . lang('common_edit').' ' . lang('common_item_kit') ,array('class'=>' ','title'=>lang($controller_name.'_update'))).'</li>';
							
							$table_data_row.= '<li>'. anchor($controller_name."/clone_item_kit/$item_kit->item_kit_id", '<i class="ion-ios-browsers-outline"></i> ' . lang('common_clone') .' ' . lang('common_item_kit'), array('class'=>' ','title'=>lang('common_clone'))).'</li>';
							
						$table_data_row.= '</ul>
					</div>'
		.'</td>';
		
		$displayable_columns = $CI->Employee->get_item_kit_columns_to_display();
		$CI->load->helper('text');
		$CI->load->helper('date');
		$CI->load->helper('currency');
		foreach($displayable_columns as $column_id => $column_values)
		{
			$val = $item_kit->{$column_id};
			if (isset($column_values['format_function']))
			{
				if (isset($column_values['data_function']))
				{
					$data_function = $column_values['data_function'];
					$data = $data_function($item_kit);
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
	$table_data_row.= '<td>&nbsp;</td>';
	$table_data_row.='</tr>';
	return $table_data_row;
}


function get_expenses_manage_table($expenses,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter table table-hover" id="sortable_table">';

	$headers[] = array('label' => '<input type="checkbox" id="select_all" /><label for="select_all"><span></span></label>', 'sort_column' => '');
	$headers[] = array('label' => lang('common_edit'), 'sort_column' => '');
	$headers[] = array('label' => lang('expenses_id'), 'sort_column' => 'id');
	$headers[] = array('label' => lang('expenses_type'), 'sort_column' => 'expense_type');
	$headers[] = array('label' => lang('expenses_description'), 'sort_column' => 'expense_description');
	$headers[] = array('label' => lang('common_category'), 'sort_column' => 'category');
	$headers[] = array('label' => lang('expenses_date'), 'sort_column' => 'expense_date');
	$headers[] = array('label' => lang('expenses_amount'), 'sort_column' => 'expense_amount');
	$headers[] = array('label' => lang('common_tax'), 'sort_column' => 'expense_tax');
	$headers[] = array('label' => lang('common_recipient_name'), 'sort_column' => 'employee_recv');
	$headers[] = array('label' => lang('common_approved_by'), 'sort_column' => 'employee_appr');
		
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
			$table.="<th data-sort-column='$sort_col' class='rightmost'>$label</th>";
		}
		else
		{
			$table.="<th data-sort-column='$sort_col'>$label</th>";		
		}
	}
	$table.='</tr></thead><tbody>';
	$table.=get_expenses_manage_table_data_rows($expenses,$controller);
	$table.='</tbody></table>';
	return $table;
}
/*
Gets the html data rows for the items.
*/
function get_expenses_manage_table_data_rows($expenses,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($expenses->result() as $expense)
	{
		$table_data_rows.=get_expenses_data_row($expense,$controller);
	}
	
	if($expenses->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><span class='col-md-12 text-center text-warning' >".lang('expenses_no_expenses_to_display')."</span></td></tr>";
	}
	
	return $table_data_rows;
}

function get_expenses_data_row($expense,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='expenses_$expense->id' value='".$expense->id."'/><label for='expenses_$expense->id'><span></span></label></td>";
	$table_data_row.='<td>'.anchor($controller_name."/view/$expense->id/2	", lang('common_edit'),array('class'=>'','title'=>lang($controller_name.'_update'))).'</td>';		
	$table_data_row.='<td>'.$expense->id.'</td>';
	$table_data_row.='<td>'.H($expense->expense_type).'</td>';
	$table_data_row.='<td>'.H($expense->expense_description).'</td>';
	$table_data_row.='<td>'.H($CI->Category->get_full_path($expense->category_id)).'</td>';
	$table_data_row.='<td>'.date(get_date_format(), strtotime($expense->expense_date)).'</td>';
	$table_data_row.='<td>'.to_currency($expense->expense_amount).'</td>';
	$table_data_row.='<td>'.to_currency($expense->expense_tax).'</td>';
	$table_data_row.='<td>'.H($expense->employee_recv).'</td>';
	$table_data_row.='<td>'.H($expense->employee_appr).'</td>';
	
	$table_data_row.='</tr>';
	return $table_data_row;
}
?>