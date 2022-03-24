<?php
function is_sale_integrated_cc_processing()
{
	$CI =& get_instance();
	$cc_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_credit'));
	return $CI->Location->get_info_for_key('enable_credit_card_processing') && $cc_payment_amount != 0;
}

function is_sale_integrated_ebt_sale()
{
	$CI =& get_instance();
	return (is_ebt_sale() && $CI->Location->get_info_for_key('enable_credit_card_processing') && $CI->Location->get_info_for_key('ebt_integrated') && $CI->Location->get_info_for_key('emv_merchant_id') );
}

function is_ebt_sale()
{
	$CI =& get_instance();
	$ebt_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_ebt'));
	$ebt_cash_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_ebt_cash'));
	$ebt_wic_amount = $CI->sale_lib->get_payment_amount(lang('common_wic'));
	
	return  $CI->config->item('enable_ebt_payments') && ($ebt_payment_amount != 0 || $ebt_cash_payment_amount != 0 || $ebt_wic_amount != 0 );
}

function is_system_integrated_ebt()
{
	$CI =& get_instance();
	return $CI->Location->get_info_for_key('enable_credit_card_processing') && $CI->config->item('enable_ebt_payments');
}

function is_ebt_sale_not_ebt_cash()
{
	$CI =& get_instance();
	$ebt_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_ebt'));
	$wic_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_wic'));
	return $CI->config->item('enable_ebt_payments') && ($ebt_payment_amount != 0 || $wic_payment_amount != 0);
	
}

function is_credit_card_sale()
{
	$CI =& get_instance();
	$cc_payment_amount = $CI->sale_lib->get_payment_amount(lang('common_credit'));
	return $cc_payment_amount != 0;
}

function is_store_account_sale()
{
	$CI =& get_instance();
	$store_account_amount = $CI->sale_lib->get_payment_amount(lang('common_store_account'));
	return $store_account_amount != 0;
}


function sale_has_partial_credit_card_payment()
{
	$CI =& get_instance();
	$cc_partial_payment_amount = $CI->sale_lib->get_payment_amount(lang('sales_partial_credit'));
	return $cc_partial_payment_amount != 0;
}

function sale_has_partial_ebt_payment()
{
	$CI =& get_instance();
	$ebt_partial = $CI->sale_lib->get_payment_amount(lang('common_partial_ebt'));
	$ebt_cash_partial = $CI->sale_lib->get_payment_amount(lang('common_partial_ebt_cash'));
	$ebt_wic_partial= $CI->sale_lib->get_payment_amount(lang('common_wic'));

	return $ebt_partial != 0 || $ebt_cash_partial != 0 || $ebt_wic_partial !=0;
}

function sale_id_receipt_link_formatter($sale_id)
{
	$CI =& get_instance();
	return anchor('sales/receipt/'.$sale_id, ($CI->config->item('sale_prefix') ? $CI->config->item('sale_prefix') : 'POS') .' '.$sale_id, array('target' => '_blank'));
}

?>