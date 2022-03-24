<?php
function character_limiter($str, $n = 500, $end_char = '&#8230;')
{
	if (strlen($str) < $n)
	{
		return $str;
	}

	if (function_exists('mb_substr'))
	{
		return mb_substr($str,0, $n).$end_char;
	}
	
	return substr($str,0, $n).$end_char;
}

function replace_newline($string) 
{
	return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
}

function number_pad($number,$n) 
{
	return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}

function H($input)
{
	return htmlentities($input, ENT_QUOTES, 'UTF-8', false);
}

//From http://stackoverflow.com/a/26537463/627473
function escape_full_text_boolean_search($search)
{
	$return = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $search);
	if(trim($return) == "")
	{
		//If we have no search return a bar character is this prevents fatal error
		$return = '|';
	}
	return $return;
}

function does_contain_only_digits($string)
{
	return (preg_match('/^[0-9]+$/', $string));
}

function clean_string($string) 
{	
	$CI =& get_instance();
	return $CI->input->clean_string($string);
}

function boolean_as_string($val)
{
	if ($val)
	{
		return lang('common_yes');		
	}
	return lang('common_no');
}

function get_full_category_path($val)
{
	$CI =& get_instance();
	$CI->load->model('Category');
	return $CI->Category->get_full_path($val);
}

function item_name_formatter($val,$data)
{
	return '<a class="'.$data['low_inventory_class'].'" href="'.site_url('home/view_item_modal').'/'.$data['item_id'].'" data-toggle="modal" data-target="#myModal">'.H($val).'</a>';
}

function item_low_quantity_data_function($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_id'] = $item->item_id;
	$data['low_inventory_class']='';
	if($CI->config->item('highlight_low_inventory_items_in_items_module') && $item->quantity !== NULL && ($item->quantity<=0 || $item->quantity <= $item->reorder_level))
	{
		$data['low_inventory_class'] = "text-danger";
	}
	
	return $data;
}

function item_id_data_function($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_id'] = $item->item_id;
	return $data;
}

function item_inventory_data($item)
{
	$CI =& get_instance();
	$data = array();
	
	$data['item_id'] = $item->item_id;
	$data['is_service'] = $item->is_service;
	return $data;
}

function item_inventory_formatter($val,$data)
{
	if ($data['is_service'])
	{
		return '';
	}
	return '<a href="'.site_url('items/inventory').'/'.$data['item_id'].'">'.H($val).'</a>';
}

function item_quantity_format($val,$data)
{
	$val = to_quantity($val);
	
	return '<span class="'.$data['low_inventory_class'].'">'.$val.'</span>';
}

function to_percent($val)
{
	$val = to_quantity($val, false);
	
	if ($val!=='')
	{
		return $val."%";
	}
	
	return lang('common_not_set');
}

function commission_percent_type_formater($val)
{
	if ($val == 'selling_price')
	{
		return lang('common_unit_price');
	}
	elseif($val == 'profit')
	{
		return lang('common_profit');		
	}
	
	return lang('common_not_set');
}

function strsame($val)
{
	return $val;
}

function add_quotes_and_escape($str) 
{
		$CI =& get_instance();
		$return = $CI->db->escape($str);
		return $return;
}

?>