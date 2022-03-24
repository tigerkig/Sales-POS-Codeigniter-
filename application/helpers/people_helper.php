<?php
function customer_name_data_function($person)
{
	$CI =& get_instance();
	$data = array();
	
	$avatar_url=$person->image_id ?  app_file_url($person->image_id) : base_url('assets/assets/images/avatar-default.jpg');
	$six_months_ago = date('Y-m-d', strtotime('-6 months'));
	$today = date('Y-m-d').'%2023:59:59';
	$data['link'] = site_url('reports/specific_customer/'.$six_months_ago.'/'.$today.'/'.$person->person_id.'/all/0');
	
	return $data;
}

function customer_name_formatter($person,$data)
{
	$CI =& get_instance();
	$link = $data['link'];
	return '<a href="'.$link.'" class="underline">'.H($person).'</a>';
}


function customer_balance_data($person)
{
	$data = array();
	$data['person_id'] = $person->person_id;
	
	return $data;
}

function customer_balance_formatter($balance,$data)
{
	$person_id = $data['person_id'];
	$output = to_currency($balance);
	$output.= anchor("customers/pay_now/$person_id",lang('common_pay'),array('title'=>lang('common_pay'),'class'=>'btn btn-primary btn-pay'));
	
	return $output;	
}

function supplier_balance_data($person)
{
	$data = array();
	$data['person_id'] = $person->person_id;
	
	return $data;
}

function supplier_balance_formatter($balance,$data)
{
	$person_id = $data['person_id'];
	$output = to_currency($balance);
	$output.= anchor("suppliers/pay_now/$person_id",lang('common_pay'),array('title'=>lang('common_pay'),'class'=>'btn btn-primary btn-pay'));
	
	return $output;	
}


function email_formatter($email)
{
	$link = "mailto:$email";
	return '<a href="'.$link.'" class="underline">'.H($email).'</a>';	
}

function amount_to_spend_for_next_point_formatter($points,$data)
{
	if (!isset($data['spend_amount_for_points']))
	{
		return lang('common_not_set');
	}
	
	return to_currency($data['spend_amount_for_points'] - $points);
}

function amount_to_spend_for_next_point_data($person)
{
	$CI =& get_instance();
	$data = array();
	if ($CI->config->item('spend_to_point_ratio'))
	{
		list($spend_amount_for_points, $points_to_earn) = explode(":",$CI->config->item('spend_to_point_ratio'),2);
		$data['spend_amount_for_points'] = $spend_amount_for_points;
	}
	return $data;
}

function sales_until_discount_data($person)
{
	$CI =& get_instance();
	$data = array();
	$data['number_of_sales_for_discount'] = $CI->config->item('number_of_sales_for_discount');
	return $data;
}

function sales_until_discount_formatter($current_sales_for_discount, $data)
{
	return to_quantity($data['number_of_sales_for_discount']-$current_sales_for_discount);
}

?>