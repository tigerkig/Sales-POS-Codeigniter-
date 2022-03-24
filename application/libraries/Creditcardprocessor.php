<?php
/*
This abstact class is implemented by any credit card processor in the system
*/

abstract class Creditcardprocessor
{
	public abstract function start_cc_processing();
	public abstract function finish_cc_processing();
	public abstract function cancel_cc_processing();
	public abstract function void_partial_transactions();
	public abstract function void_sale($sale_id);
	public abstract function void_return($sale_id);
	
	protected $controller;
	
	function __construct($controller) 
	{
		$this->controller = $controller;
	}
	
	protected function _get_session_invoice_no()
	{
		if (!$this->controller->sale_lib->get_invoice_no())
		{
			$this->controller->sale_lib->set_invoice_no(substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16));
		}
		return $this->controller->sale_lib->get_invoice_no();
	}
	
	protected function _is_valid_zip($zip)
	{
		if (strlen($zip) == 5 || strlen($zip) == 9)
		{
			return is_numeric($zip);
		}
		elseif(strlen($zip) == 10)
		{
			$parts = explode('-', $zip);
			return (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1]));
		}
		return FALSE;
	}

	protected function _get_cc_payments_for_sale($sale_id)
	{
   	$this->controller->db->from('sales_payments');
		$this->controller->db->where('sale_id', $sale_id);
		$this->controller->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit'), lang('common_ebt')));
		
		return $this->controller->db->get()->result_array();
	}
}
?>