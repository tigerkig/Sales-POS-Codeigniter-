<?php
class Sale_lib
{
	var $CI;
	
	//This is used when we need to change the sale state and restore it before changing it (The case of showing a receipt in the middle of a sale)
	var $sale_state;
  	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Register_cart');
		$this->CI->load->model('Price_rule');
		$this->sale_state = array();
		$this->view_data  = NULL;
	}
	
	function set_view_data(&$view_data)
	{
		$this->view_data = &$view_data;
		
	}

	function get_cart()
	{
		if($this->CI->session->userdata('cart') === NULL)
			$this->set_cart(array(), false);

		return $this->CI->session->userdata('cart');
	}

	function set_cart($cart_data,$update_register_cart_data = TRUE)
	{
		$this->CI->session->set_userdata('cart',$cart_data);
		if ($update_register_cart_data)
		{
			$this->update_register_cart_data();
		}
	}
	
	function update_register_cart_data()
	{
		$data = array();
		$data['cart'] = $this->get_cart();
		$data['subtotal'] = $this->get_subtotal();
		$data['tax'] = $this->get_tax_total_amount();
		$data['amount_due'] = $this->get_amount_due();
		$data['exchange_rate'] = $this->get_exchange_rate();
		$data['exchange_name'] = $this->get_exchange_name();
		$data['exchange_symbol'] = $this->get_exchange_currency_symbol();
		$data['exchange_symbol_location'] = $this->get_exchange_currency_symbol_location();
		$data['exchange_number_of_decimals'] = $this->get_exchange_currency_number_of_decimals();
		$data['exchange_thousands_separator'] = $this->get_exchange_currency_thousands_separator();
		$data['exchange_decimal_point'] = $this->get_exchange_currency_decimal_point();
		
		$customer_id = $this->get_customer();
		if($customer_id!=-1)
		{
			$info=$this->CI->Customer->get_info($customer_id);
			$data['customer']=$info->first_name.' '.$info->last_name.($info->company_name==''  ? '':' ('.$info->company_name.')');
			$data['customer_email']=$info->email;
			$data['customer_balance'] = $info->balance;
			$data['avatar']=$info->image_id ?  app_file_url($info->image_id) : base_url()."assets/img/user.png"; //can be changed to  base_url()."img/avatar.png" if it is required
		}
		else
		{
			$data['customer']= NULL;
		}

		$data['payments'] = $this->get_payments();
		$data['total'] = $this->get_total();
		$this->CI->Register_cart->set_data($data,$this->CI->Employee->get_logged_in_employee_current_register_id());		
		
	}
	//Alain Multiple Payments
	function get_payments()
	{
		if($this->CI->session->userdata('payments') === NULL)
			$this->set_payments(array());

		return $this->CI->session->userdata('payments');
	}

	//Alain Multiple Payments
	function set_payments($payments_data)
	{
		$this->CI->session->set_userdata('payments',$payments_data);
		$this->update_register_cart_data();
	}
	
	function get_selected_payment()
	{
		if($this->CI->session->userdata('sale_selected_payment') === NULL)
			$this->set_selected_payment('');

		return $this->CI->session->userdata('sale_selected_payment');
	}
	
	function set_selected_payment($payment)
	{
		$this->CI->session->set_userdata('sale_selected_payment',$payment);
	}
	
	function clear_selected_payment() 	
	{
		$this->CI->session->unset_userdata('sale_selected_payment');
	}
	
	function change_credit_card_payments_to_partial()
	{
		$payments=$this->get_payments();
		
		foreach($payments as $payment_id=>$payment)
		{
			//If we have a credit payment, change it to partial credit card so we can process again
			if ($payment['payment_type'] == lang('common_credit'))
			{
				$payments[$payment_id]['payment_type'] =  lang('sales_partial_credit');
			}
		}
		
		$this->set_payments($payments);
	}
	
	function get_change_sale_date() 
	{
		return $this->CI->session->userdata('change_sale_date') ? $this->CI->session->userdata('change_sale_date') : '';
	}
	function clear_change_sale_date() 	
	{
		$this->CI->session->unset_userdata('change_sale_date');
		
	}
	function clear_change_sale_date_enable() 	
	{
		$this->CI->session->unset_userdata('change_sale_date_enable');
	}
	function set_change_sale_date_enable($change_sale_date_enable)
	{
		$this->CI->session->set_userdata('change_sale_date_enable',$change_sale_date_enable);
	}
	
	function get_change_sale_date_enable() 
	{
		return $this->CI->session->userdata('change_sale_date_enable') ? $this->CI->session->userdata('change_sale_date_enable') : '';
	}
	
	function set_change_sale_date($change_sale_date)
	{
		$this->CI->session->set_userdata('change_sale_date',$change_sale_date);
	}
	
	function get_comment() 
	{
		return $this->CI->session->userdata('comment') ? $this->CI->session->userdata('comment') : '';
	}

	function get_comment_on_receipt() 
	{
		return $this->CI->session->userdata('show_comment_on_receipt') ? $this->CI->session->userdata('show_comment_on_receipt') : '';
	}

	function set_comment($comment) 
	{
		$this->CI->session->set_userdata('comment', $comment);
	}
		
	function get_selected_tier_id() 
	{
		return $this->CI->session->userdata('selected_tier_id') ? $this->CI->session->userdata('selected_tier_id') : FALSE;
	}

	function get_previous_tier_id() 
	{
		return $this->CI->session->userdata('previous_tier_id') ? $this->CI->session->userdata('previous_tier_id') : FALSE;
	}

	function set_selected_tier_id($tier_id, $change_price = true) 
	{
		$this->CI->session->set_userdata('previous_tier_id', $this->get_selected_tier_id());
		$this->CI->session->set_userdata('selected_tier_id', $tier_id);
		
		if ($change_price == true)
		{
			$this->change_price();
		}
	}
	
	function clear_selected_tier_id()	
	{
		$this->CI->session->unset_userdata('previous_tier_id');
		$this->CI->session->unset_userdata('selected_tier_id');
	}
	
	
	function set_comment_on_receipt($comment_on_receipt) 
	{
		$this->CI->session->set_userdata('show_comment_on_receipt', $comment_on_receipt);
	}

	function clear_comment() 	
	{
		$this->CI->session->unset_userdata('comment');
		
	}
	
	function clear_show_comment_on_receipt() 	
	{
		$this->CI->session->unset_userdata('show_comment_on_receipt');
	}
	
	function get_delivery()
	{
		return $this->CI->session->userdata('delivery');
	}

	function set_delivery($delivery)
	{
		if (!$delivery)
		{
			$this->clear_delivery();
		}
		$this->CI->session->set_userdata('delivery', $delivery);
	}
	
	function get_delivery_person_info()
	{
		return $this->CI->session->userdata('delivery_person_info') ? $this->CI->session->userdata('delivery_person_info') : array();
	}
	
	function set_delivery_person_info($delivery_person_info)
	{
		$this->CI->session->set_userdata('delivery_person_info', $delivery_person_info);
	}
	
	function get_delivery_info()
	{
		return $this->CI->session->userdata('delivery_info') ? $this->CI->session->userdata('delivery_info') : array();
	}
	
	function set_delivery_info($delivery_info)
	{
		$this->CI->session->set_userdata('delivery_info', $delivery_info);
	}
	
	function get_delivery_tax_group_id()
	{
		return $this->CI->session->userdata('delivery_tax_group_id');
	}
	
	function set_delivery_tax_group_id($delivery_tax_group_id)
	{
		$this->CI->session->set_userdata('delivery_tax_group_id', $delivery_tax_group_id);
	}
	
	function get_email_receipt() 
	{
		return $this->CI->session->userdata('email_receipt');
	}

	function set_email_receipt($email_receipt) 
	{
		$this->CI->session->set_userdata('email_receipt', $email_receipt);
	}

	function clear_email_receipt() 	
	{
		$this->CI->session->unset_userdata('email_receipt');
	}
		
	function get_deleted_taxes() 
	{
		$deleted_taxes = $this->CI->session->userdata('deleted_taxes') ? $this->CI->session->userdata('deleted_taxes') : array();
		return $deleted_taxes;
	}

	function add_deleted_tax($name) 
	{
		$deleted_taxes = $this->CI->session->userdata('deleted_taxes') ? $this->CI->session->userdata('deleted_taxes') : array();
		
		if (!in_array($name, $deleted_taxes))
		{
			$deleted_taxes[] = $name;
		}
		$this->CI->session->set_userdata('deleted_taxes', $deleted_taxes);
	}
	
	function set_deleted_taxes($deleted_taxes)
	{
		$this->CI->session->set_userdata('deleted_taxes', $deleted_taxes);		
	}

	function clear_deleted_taxes() 	
	{
		$this->CI->session->unset_userdata('deleted_taxes');
	}	
	
	function get_save_credit_card_info() 
	{
		return $this->CI->session->userdata('save_credit_card_info');
	}

	function set_save_credit_card_info($save_credit_card_info) 
	{
		$this->CI->session->set_userdata('save_credit_card_info', $save_credit_card_info);
	}

	function clear_save_credit_card_info() 	
	{
		$this->CI->session->unset_userdata('save_credit_card_info');
	}
	
	function get_use_saved_cc_info() 
	{
		return $this->CI->session->userdata('use_saved_cc_info');
	}

	function set_use_saved_cc_info($use_saved_cc_info) 
	{
		$this->CI->session->set_userdata('use_saved_cc_info', $use_saved_cc_info);
	}

	function clear_use_saved_cc_info() 	
	{
		$this->CI->session->unset_userdata('use_saved_cc_info');
	}
	
	function clear_prompt_for_card() 	
	{
		$this->CI->session->unset_userdata('prompt_for_card');
	}
	
	function set_prompt_for_card($prompt_for_card)
	{
		$this->CI->session->set_userdata('prompt_for_card',$prompt_for_card);
	}
	
	function get_prompt_for_card() 
	{
		return $this->CI->session->userdata('prompt_for_card') ? $this->CI->session->userdata('prompt_for_card') : '';
	}
	
	function get_partial_transactions()
	{
		return $this->CI->session->userdata('partial_transactions');
	}
	
	function set_partial_transactions($partial_transactions)
	{
		$this->CI->session->set_userdata('partial_transactions', $partial_transactions);
	}
	
	function add_partial_transaction($partial_transaction)
	{
		$partial_transactions = $this->CI->session->userdata('partial_transactions');
		$partial_transactions[] = $partial_transaction;
		$this->CI->session->set_userdata('partial_transactions', $partial_transactions);
	}
	
	function delete_partial_transactions()
	{
		$this->CI->session->unset_userdata('partial_transactions');
	}
	
	
	function get_sold_by_employee_id() 
	{
		if ($this->CI->config->item('default_sales_person') != 'not_set' && !$this->CI->session->userdata('sold_by_employee_id'))
		{
			$employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;
			return $employee_id;
		}
		return $this->CI->session->userdata('sold_by_employee_id') ? $this->CI->session->userdata('sold_by_employee_id') : NULL;
	}

	function set_sold_by_employee_id($sold_by_employee_id) 
	{
		$this->CI->session->set_userdata('sold_by_employee_id', $sold_by_employee_id);
	}

	function clear_sold_by_employee_id() 	
	{
		$this->CI->session->unset_userdata('sold_by_employee_id');
	}
	
	function get_invoice_no() 
	{
		return $this->CI->session->userdata('invoice_no');
	}

	function set_invoice_no($invoice_no) 
	{
		$this->CI->session->set_userdata('invoice_no', $invoice_no);
	}

	function clear_invoice_no() 	
	{
		$this->CI->session->unset_userdata('invoice_no');
	}
	
	function add_paid_store_account_sale($sale_id)
	{
		$paid_store_account_sales = $this->get_paid_store_account_sales();
		$paid_store_account_sales[$sale_id] = TRUE;
		$this->CI->session->set_userdata('paid_store_account_sales',$paid_store_account_sales);
	}
	
	function get_paid_store_account_sales()
	{
		if($this->CI->session->userdata('paid_store_account_sales') === NULL)
		{
			return array();
		}
		
		return $this->CI->session->userdata('paid_store_account_sales');
	}
	
	function remove_paid_store_account_sale($sale_id)
	{
		$paid_store_account_sales = $this->get_paid_store_account_sales();
		
		if (isset($paid_store_account_sales[$sale_id]))
		{
			unset($paid_store_account_sales[$sale_id]);
			$this->CI->session->set_userdata('paid_store_account_sales',$paid_store_account_sales);
			return true;
		}
		
		return false;
	}
	
	function clear_all_paid_store_account_sales()
	{
		$this->CI->session->unset_userdata('paid_store_account_sales');		
	}
		
	function validate_payment($payment_type,$payment_amount,$payment_date = false)
	{
		$payment_date = $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s');
		
		foreach($this->get_payments() as $payment)
		{
			if ($payment_type == $payment['payment_type'] && $payment['payment_amount'] == $payment_amount)
			{
				//Do a check based on timestamp to be a little more relaxed
				
				//If payment amount is within 5 seconds deny it
				$seconds_diff = strtotime($payment_date) - strtotime($payment['payment_date']);
				if ($seconds_diff < 5)
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
	
	function add_payment($payment_type,$payment_amount,$payment_date = false, $truncated_card = '', $card_issuer = '', $auth_code = '', $ref_no = '', $cc_token='', $acq_ref_data = '', $process_data = '', $entry_method='', $aid= '',$tvr='',$iad='', $tsi='',$arc='',$cvm='',$tran_type='',$application_label = '',$ebt_voucher_no='',$ebt_auth_code='')
	{
			$payments=$this->get_payments();
			$payment = array(
				'payment_type'=>$payment_type,
				'payment_amount'=>$payment_amount,
				'payment_date' => $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s'),
				'truncated_card' => $truncated_card,
				'card_issuer' => $card_issuer,
				'auth_code' => $auth_code,
				'ref_no' => $ref_no,
				'cc_token' => $cc_token,
				'acq_ref_data' => $acq_ref_data,
				'process_data' => $process_data,
				'entry_method' => $entry_method,
				'aid' => $aid,
				'tvr' => $tvr,
				'iad' => $iad,
				'tsi' => $tsi,
				'arc' => $arc,
				'cvm' => $cvm,
				'tran_type' => $tran_type,
				'application_label' => $application_label,
				'ebt_voucher_no' => $ebt_voucher_no,	
				'ebt_auth_code' => $ebt_auth_code,
			);
			
			
			$payments[]=$payment;
			$this->set_payments($payments);
			return true;
	}
	
	function edit_payment($payment_id, $payment_type, $payment_amount,$payment_date = false, $truncated_card = '', $card_issuer = '', $auth_code = '', $ref_no = '', $cc_token='', $acq_ref_data = '', $process_data = '', $entry_method='', $aid= '',$tvr='',$iad='', $tsi='',$arc='',$cvm='',$tran_type='',$application_label = '',$ebt_voucher_no='',$ebt_auth_code='')
	{
		$payments=$this->get_payments();
		$payment = array(
			'payment_type'=>$payment_type,
			'payment_amount'=>$payment_amount,
			'payment_date' => $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s'),
			'truncated_card' => $truncated_card,
			'card_issuer' => $card_issuer,
			'auth_code' => $auth_code,
			'ref_no' => $ref_no,
			'cc_token' => $cc_token,
			'acq_ref_data' => $acq_ref_data,
			'process_data' => $process_data,		
			'entry_method' => $entry_method,
			'aid' => $aid,
			'tvr' => $tvr,
			'iad' => $iad,
			'tsi' => $tsi,
			'arc' => $arc,
			'cvm' => $cvm,
			'tran_type' => $tran_type,
			'application_label' => $application_label,
			'ebt_voucher_no' => $ebt_voucher_no,	
			'ebt_auth_code' => $ebt_auth_code,
		);
		
		$payments[$payment_id]=$payment;
		$this->set_payments($payments);
		return true;
	}
	
	public function get_payment_ids($payment_type)
	{
		$payment_ids = array();
		
		$payments=$this->get_payments();
		
		for($k=0;$k<count($payments);$k++)
		{
			if ($payments[$k]['payment_type'] == $payment_type)
			{
				$payment_ids[] = $k;
			}
		}
		
		return $payment_ids;
	}
	
	public function get_payment_amount($payment_type)
	{
		$payment_amount = 0;
		if (($payment_ids = $this->get_payment_ids($payment_type)) !== FALSE)
		{
			$payments=$this->get_payments();
			
			foreach($payment_ids as $payment_id)
			{
				$payment_amount += $payments[$payment_id]['payment_amount'];
			}
		}
		
		return $payment_amount;
	}
	
	//Alain Multiple Payments
	function delete_payment($payment_ids)
	{
		$payments=$this->get_payments();
		if (is_array($payment_ids))
		{
			foreach($payment_ids as $payment_id)
			{
				unset($payments[$payment_id]);
			}
		}
		else
		{
			unset($payments[$payment_ids]);			
		}
		$this->set_payments(array_values($payments));
	}
	
	function get_price_for_item($item_id, $tier_id = FALSE)
	{
		if ($tier_id === FALSE)
		{
			$tier_id = $this->get_selected_tier_id();
		}
		
		$item_info = $this->CI->Item->get_info($item_id);
		$item_location_info = $this->CI->Item_location->get_info($item_id);
		
		$item_tier_row = $this->CI->Item->get_tier_price_row($tier_id, $item_id);
		$item_location_tier_row = $this->CI->Item_location->get_tier_price_row($tier_id, $item_id, $this->CI->Employee->get_logged_in_employee_current_location_id());

		$tier_info = $this->CI->Tier->get_info($tier_id);
				
		if (!empty($item_location_tier_row) && $item_location_tier_row->unit_price)
		{
			return to_currency_no_money($item_location_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_location_tier_row) && $item_location_tier_row->percent_off)
		{
			$item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
			return to_currency_no_money($item_unit_price *(1-($item_location_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_location_tier_row) && $item_location_tier_row->cost_plus_percent)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price *(1+($item_location_tier_row->cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_location_tier_row) && $item_location_tier_row->cost_plus_fixed_amount)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price + $item_location_tier_row->cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_tier_row) && $item_tier_row->unit_price)
		{
			return to_currency_no_money($item_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_tier_row) && $item_tier_row->percent_off)
		{
			$item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
			return to_currency_no_money($item_unit_price *(1-($item_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_tier_row) && $item_tier_row->cost_plus_percent)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price *(1+($item_tier_row->cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_tier_row) && $item_tier_row->cost_plus_fixed_amount)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price + $item_tier_row->cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_percent_off)
		{
			$item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
			return to_currency_no_money($item_unit_price *(1-($tier_info->default_percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_cost_plus_percent)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price *(1+($tier_info->default_cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_cost_plus_fixed_amount)
		{
			$item_cost_price = $item_location_info->cost_price ? $item_location_info->cost_price : $item_info->cost_price;
			return to_currency_no_money($item_cost_price + $tier_info->default_cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		else
		{
			$today =  strtotime(date('Y-m-d'));
			$is_item_location_promo = ($item_location_info->start_date !== NULL && $item_location_info->end_date !== NULL) && (strtotime($item_location_info->start_date) <= $today && strtotime($item_location_info->end_date) >= $today);
			$is_item_promo = ($item_info->start_date !== NULL && $item_info->end_date !== NULL) && (strtotime($item_info->start_date) <= $today && strtotime($item_info->end_date) >= $today);
			
			if ($is_item_location_promo && $item_location_info->promo_price)
			{
				return to_currency_no_money($item_location_info->promo_price, 10);
			}
			elseif ($is_item_promo && $item_info->promo_price)
			{
				return to_currency_no_money($item_info->promo_price, 10);
			}
			else
			{
				$item_unit_price = $item_location_info->unit_price ? $item_location_info->unit_price : $item_info->unit_price;
				return to_currency_no_money($item_unit_price, 10);
			}
		}			
			
	}
	
	function get_price_for_item_kit($item_kit_id, $tier_id = FALSE)
	{
		if ($tier_id === FALSE)
		{
			$tier_id = $this->get_selected_tier_id();
		}
		
		$item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
		$item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);
		
		$item_kit_tier_row = $this->CI->Item_kit->get_tier_price_row($tier_id, $item_kit_id);
		$item_kit_location_tier_row = $this->CI->Item_kit_location->get_tier_price_row($tier_id, $item_kit_id, $this->CI->Employee->get_logged_in_employee_current_location_id());
		
		$tier_info = $this->CI->Tier->get_info($tier_id);
		
		if (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->unit_price)
		{
			return to_currency_no_money($item_kit_location_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->percent_off)
		{
			$item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
			return to_currency_no_money($item_kit_unit_price *(1-($item_kit_location_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->cost_plus_percent)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price *(1+($item_kit_location_tier_row->cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_location_tier_row) && $item_kit_location_tier_row->cost_plus_fixed_amount)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price + $item_kit_location_tier_row->cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->unit_price)
		{
			return to_currency_no_money($item_kit_tier_row->unit_price, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->percent_off)
		{
			$item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
			return to_currency_no_money($item_kit_unit_price *(1-($item_kit_tier_row->percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->cost_plus_percent)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price *(1+($item_kit_tier_row->cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif (!empty($item_kit_tier_row) && $item_kit_tier_row->cost_plus_fixed_amount)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price  + $item_kit_tier_row->cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_percent_off)
		{
			$item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
			return to_currency_no_money($item_kit_unit_price *(1-($tier_info->default_percent_off/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_cost_plus_percent)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price *(1+($tier_info->default_cost_plus_percent/100)), $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		elseif($tier_info->default_cost_plus_fixed_amount)
		{
			$item_kit_cost_price = $item_kit_location_info->cost_price ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;
			return to_currency_no_money($item_kit_cost_price + $tier_info->default_cost_plus_fixed_amount, $this->CI->config->item('round_tier_prices_to_2_decimals') ? 2 : 10);
		}
		else
		{
			$item_kit_unit_price = $item_kit_location_info->unit_price ? $item_kit_location_info->unit_price : $item_kit_info->unit_price;
			return to_currency_no_money($item_kit_unit_price, 10);
		}		
	}	
	
	function empty_payments()
	{
		$this->CI->session->unset_userdata('payments');
	}

	//Alain Multiple Payments
	function get_payments_totals_excluding_store_account()
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
		    if($payments['payment_type'] != lang('common_store_account'))
			{
		    	$subtotal+=$payments['payment_amount'];
			}	
		}
		return to_currency_no_money($subtotal);
	}

	function get_payments_totals()
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
			$subtotal+=$payments['payment_amount'];
		}

		return to_currency_no_money($subtotal);
	}

	//Alain Multiple Payments
	function get_amount_due($sale_id = false)
	{
		$amount_due=0;
		$payment_total = $this->get_payments_totals();
		$sales_total=$this->get_total($sale_id);
		$amount_due=to_currency_no_money($sales_total - $payment_total);
		return $amount_due;
	}

	function get_amount_due_round($sale_id = false)
	{
		$amount_due=0;
		$payment_total = $this->get_payments_totals();
		$sales_total= $this->CI->config->item('round_cash_on_sales') ?  round_to_nearest_05($this->get_total($sale_id)) : $this->get_total($sale_id);
		$amount_due=to_currency_no_money($sales_total - $payment_total);
		return $amount_due;
	}

	function get_customer()
	{
		if(!$this->CI->session->userdata('customer'))
			$this->set_customer(-1, false);

		return $this->CI->session->userdata('customer');
	}

	function set_customer($customer_id, $change_price = true)
	{
		if (is_numeric($customer_id))
		{
			$this->CI->session->set_userdata('customer',$customer_id);

			if ($change_price == true)
			{
				$this->change_price();
			}
		}
	}

	function get_mode()
	{
		if(!$this->CI->session->userdata('sale_mode'))
			$this->set_mode('sale');

		return $this->CI->session->userdata('sale_mode');
	}

	function set_mode($mode)
	{
		$this->CI->session->set_userdata('sale_mode',$mode);
	}
	
	/*
	* This function is called when a customer added or tier changed
	* It scans item and item kits to see if there price is at a default value
	* If a price is at a default value, it is changed to match the tier
	*/
	function change_price()
	{
		$items = $this->get_cart();
		foreach ($items as $item )
		{
			if (isset($item['item_id']))
			{
				$line=$item['line'];
				$price=$item['price'];
				$item_id=$item['item_id'];
				$item_info = $this->CI->Item->get_info($item_id);
				$item_location_info = $this->CI->Item_location->get_info($item_id);
				$previous_price = FALSE;
			
				if ($previous_tier_id = $this->get_previous_tier_id())
				{
					$previous_price = $this->get_price_for_item($item_id, $previous_tier_id);
				}
				$previous_price = to_currency_no_money($previous_price, 10);
				$price = to_currency_no_money($price, 10);
				
				if($price==$item_info->unit_price || $price == $item_location_info->unit_price || (($price == $previous_price) && ($price !=0 && $previous_price!=0)))
				{	
					$items[$line]['price']= $this->get_price_for_item($item_id);		
				}
			}
			elseif(isset($item['item_kit_id']))
			{
				$line=$item['line'];
				$price=$item['price'];
				$item_kit_id=$item['item_kit_id'];
				$item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
				$item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);
				$previous_price = FALSE;
			
				if ($previous_tier_id = $this->get_previous_tier_id())
				{
					$previous_price = $this->get_price_for_item_kit($item_kit_id, $previous_tier_id);
				}
				
				$previous_price = to_currency_no_money($previous_price, 10);
				$price = to_currency_no_money($price, 10);
						
				if($price==$item_kit_info->unit_price || $price == $item_kit_location_info->unit_price || (($price == $previous_price) && ($price !=0 && $previous_price!=0)))
				{
					$items[$line]['price']= $this->get_price_for_item_kit($item_kit_id);		
				}
			}
		}
		$this->set_cart($items);
	}
	
	function do_price_rules($params=array())
	{
		if($this->get_mode() == 'store_account_payment')
		{
			return;
		}
		
		$cart = $this->get_cart();
		
		if (isset($params['line']))
		{
			$cart_row = $cart[$params['line']];
			if (isset($cart_row['item_id']))
			{
				$params['item_id'] = $cart_row['item_id'];
			}
			else
			{
				$params['item_kit_id'] = $cart_row['item_kit_id'];
			}
		}
		
		if(isset($params['item_id']))
		{
			$params['quantity'] = $this->get_quantity_already_added($params['item_id'], false);
		}
		
		if(isset($params['item_kit_id']))
		{
			$params['quantity'] = $this->get_kit_quantity_already_added($params['item_kit_id']);
		}
		
		$params['coupons'] =  $this->get_coupons();
	

		$rule = $this->do_price_rule_for_items_and_item_kits($params);
		
		switch($rule['type'])
		{
			case 'simple_discount':
			 $this->apply_buy_x_get_y($rule, $params);
			break;
			
			case 'buy_x_get_y_free':
				$this->apply_buy_x_get_y($rule,$params);
			break;

			case 'buy_x_get_discount': 
				$this->apply_buy_x_get_y($rule,$params);
			break;

			case 'advanced_discount': 
				$this->apply_advanced_discount($rule,$params);
			break;
			
			default:
				$this->cleanup_price_rule_items($params);
			break;
		}
				
		$spending_rule = $this->do_spending_price_rule($params);
		
		switch($spending_rule['type'])
		{
			case 'spend_x_get_discount':
				$this->apply_spend_x_get_discount($spending_rule);
			break;
			
			default:
				$this->cleanup_price_rule_discounts();
			break;
		}	
	}
	
	function cleanup_price_rule_items($params)
	{
		$items = $this->get_cart();
		
		foreach($items as $line => $item)
		{
			if($this->is_price_rule_discount_item_line($line))
			{
				//if buyxgety but no rule returned remove it from cart	
				if((isset($item['item_kit_id']) && isset($params['item_kit_id'])) && $item['item_kit_id'] == $params['item_kit_id'])
				{
					if(isset($params['apply_coupons_only']) && $params['apply_coupons_only'])
					{
						$id = $item['item_kit_id'];
						$kit = true;
						$reg_line = $this->get_price_rule_non_discount_item_line($id, $kit);
						$this->edit_item($reg_line, NULL, NULL,$this->get_kit_quantity_already_added($id), NULL, NULL, NULL, FALSE);
							
					}
					$this->delete_item($line);
				}
				if((isset($item['item_id']) && isset($params['item_id'])) && $item['item_id'] == $params['item_id'])
				{
					
					if(isset($params['apply_coupons_only']) && $params['apply_coupons_only'])
					{
						$id = $item['item_id'];
						$kit = false;
						$reg_line = $this->get_price_rule_non_discount_item_line($id, $kit);
						$this->edit_item($reg_line, NULL, NULL,$this->get_quantity_already_added($id), NULL, NULL, NULL, FALSE);
							
					}
					$this->delete_item($line);
				}
				if($item['quantity'] == 0)
				{
					$this->delete_item($line);
				}
			}
			if($this->is_price_rule_advanced_discount_item_line($line))
			{
				//if customrule but no rule returned remove rule 
				if((isset($item['item_kit_id']) && isset($params['item_kit_id'])) && $item['item_kit_id'] == $params['item_kit_id'])
				{
					$items[$line]['rule'] = array();
					$items[$line]['price'] = $items[$line]['regular_price'];
					$items[$line]['discount'] = 0;
				}
				if((isset($item['item_id']) && isset($params['item_id'])) && $item['item_id'] == $params['item_id'])
				{
					$items[$line]['rule'] = array();
					$items[$line]['price'] = $items[$line]['regular_price'];
					$items[$line]['discount'] = 0;
				}
				
				$this->set_cart($items);
			}
		}		
	}
	
	function cleanup_price_rule_discounts()
	{
		$items = $this->get_cart();
		
		foreach($items as $line => $item)
		{
			if($this->is_price_rule_discount_line($line))
			{				
					$this->delete_item($line);
			}
		}
	}
	
	private function get_number_of_free_or_discount_items($rule,$params)
	{
		$is_edit = (isset($params['line']) && !isset($params['apply_coupons_only']));
		
		if(isset($params['item_kit_id']))
		{
			$id = $params['item_kit_id'];
			$kit = true;
		} 
		else
		{
			$id = $params['item_id'];
			$kit = false;
		}
		
		$is_bogo_rule = $rule['type'] === 'buy_x_get_y_free';
		
		$items_to_buy =  $rule['items_to_buy'];		
		$items_to_get =  $is_bogo_rule ? $rule['items_to_get'] : 1;
		
		if((string)(int)$items_to_buy != $items_to_buy || (string)(int)$items_to_get != $items_to_get)
		{
			return false;
		}
		
		$max = $rule['num_times_to_apply'] * $items_to_get;
		
		if($kit)
		{
			$quantity_of_item_in_cart = $this->get_kit_quantity_already_added($id);
		} 
		else 
		{
			$quantity_of_item_in_cart = $this->get_quantity_already_added($id, false);
		}
		
		if($is_edit)
		{	
			//discount item line
			if($this->is_price_rule_discount_item_line($params['line']))
			{
				$line_item = $this->get_line_item($params['line']);
				$discount_item_quantity = (int)$line_item['quantity'];
			}
			//regular item line
			else 
			{
				$line_item = $this->get_line_item($params['line']);
				$reg_item_quantity = (int)$line_item['quantity'];
				
				//we want to subtract remander to get number of times to discount applies
				
				if($items_to_buy > 0)
				{
					$r = $reg_item_quantity % $items_to_buy;

					$number_of_times_discount_applies = ($reg_item_quantity - $r) / $items_to_buy;
					
					$discount_item_quantity = $number_of_times_discount_applies*$items_to_get;
					
				}
				else
				{
					$r = ($quantity_of_item_in_cart % ($items_to_buy + $items_to_get));
		
					$number_of_times_discount_applies = ($quantity_of_item_in_cart - $r) / ($items_to_buy + $items_to_get);
			
					$discount_item_quantity = $number_of_times_discount_applies*$items_to_get;
				}
				
			}
		}
		else
		{
			
			//$remainder = $a % $b;
			$r = ($quantity_of_item_in_cart % ($items_to_buy + $items_to_get));
		
			$number_of_times_discount_applies = ($quantity_of_item_in_cart - $r) / ($items_to_buy + $items_to_get);
			
			$discount_item_quantity = $number_of_times_discount_applies*$items_to_get;
			
		}
		
		if($max && $discount_item_quantity > $max)
		{
				$discount_item_quantity = $max;
		}
		
		if(!$is_edit && $r == $items_to_buy && ($max == 0 || ($discount_item_quantity != $max)))
		{
			if(!$this->CI->config->item('disable_price_rules_dialog'))
			{
				if ($rule['type'] != 'simple_discount')
				{
					if (isset($params['item_id']))
					{
						$this->view_data['item_to_add'] = $id.'|FORCE_ITEM_ID|';
					}
					else
					{
						$this->view_data['item_to_add'] = 'KIT '.$id;
					}
				
					$this->view_data['number_to_add'] = $is_bogo_rule ? $rule['items_to_get'] : 1;
				}
			}
		}
		
		return $discount_item_quantity;
	}
	 
	private function get_number_of_discount_items_in_cart($id, $kit = false)
	{
		$discounted_item_qty = 0;
		$items = $this->get_cart();
		
		foreach ($items as $line=>$item )
		{
			if (($kit && isset($item['item_kit_id']) && $item['item_kit_id'] == $id) || (!$kit && isset($item['item_id']) && $item['item_id'] == $id))
			{
				if(isset($item['rule']['type']))
				{
					if($item['rule']['type'] == 'buy_x_get_y_free')
					{
						$discounted_item_qty += $item['quantity'];
					}
					
					if($item['rule']['type'] == 'buy_x_get_discount')
					{
						$discounted_item_qty += $item['quantity'];
					}
					
					if($item['rule']['type'] == 'simple_discount')
					{
						$discounted_item_qty += $item['quantity'];
					}
				}
			}
		}
			
		return $discounted_item_qty;
	}
	
	function get_line_item($line)
	{
		$items = $this->get_cart();
		
		if (isset($items[$line]))
		{
			return $items[$line];
		}
		
		return false;
	}
	
	function is_price_rule_discount_line($line)
	{
		$item = $this->get_line_item($line);
		
		if(isset($item['rule']['type']) && $item['rule']['type'] == 'spend_x_get_discount')
		{
			return true;
		}
		
		return false;
	}
	
	function is_price_rule_discount_item_line($line)
	{
		$item = $this->get_line_item($line);
		
		if(isset($item['rule']['type']) && $item['rule']['type'] == 'buy_x_get_y_free')
		{
			return true;
		}
		
		elseif(isset($item['rule']['type']) && $item['rule']['type'] == 'buy_x_get_discount')
		{
			return true;
		}
		
		elseif(isset($item['rule']['type']) && $item['rule']['type'] == 'simple_discount')
		{
			return true;
		}
		
		return false;
		
	}
	
	function is_price_rule_advanced_discount_item_line($line)
	{
		$item = $this->get_line_item($line);
		
		if(isset($item['rule']['type']) && $item['rule']['type'] == 'advanced_discount')
		{
			return true;
		}
		
		return false;
		
	}
		
	function get_price_rule_non_discount_item_line($id, $kit = false)
	{
		$items = $this->get_cart();

		foreach (array_reverse($items, TRUE) as $line=>$item )
		{
			if (($kit && isset($item['item_kit_id']) && $item['item_kit_id'] == $id) || (!$kit && isset($item['item_id']) && $item['item_id'] == $id))
			{
				if(empty($item['rule']) || isset($item['rule']['type']) && ($item['rule']['type'] != 'buy_x_get_y_free' && $item['rule']['type'] != 'buy_x_get_discount' && $item['rule']['type'] != 'simple_discount'))
				{
					return $line;
				}
			}
		}
		
		return FALSE;
	}
	
	function get_all_lines_for_item($id, $kit = false)
	{
		$items = $this->get_cart();
		
		$return = array();
		
		foreach ($items as $line=> $item)
		{
			if (($kit && isset($item['item_kit_id']) && $item['item_kit_id'] == $id) || (!$kit && isset($item['item_id']) && $item['item_id'] == $id))
			{
					$return[] = $line;
			}
		}
		
		return $return;
	}
	
	function get_price_rule_discount_item_line($id, $kit = false)
	{
		$cart = $this->get_cart();
		
		if(count($cart) > 0)
		{
			$max = max(array_keys($cart));
		}
		else
		{
			$max = 1;
		}

		foreach($cart as $key => $line)
		{
			if(($kit && isset($line['item_kit_id']) && $line['item_kit_id'] == $id) || (!$kit && isset($line['item_id']) && $line['item_id'] == $id))
			{
				if(isset($line['rule']['type']) && $line['rule']['type'] == 'buy_x_get_y_free')
				{
					return $key;
				}
				elseif(isset($line['rule']['type']) && $line['rule']['type'] == 'buy_x_get_discount')
				{
					return $key;
				}
				elseif(isset($line['rule']['type']) && $line['rule']['type'] == 'simple_discount')
				{
					return $key;
				}
			}
		
			if ($key === $max) return $key+1;			
		}
		
		return FALSE;
	}
	
	function apply_buy_x_get_y($rule,$params)
	{	
		$is_bogo_rule = $rule['type'] === 'buy_x_get_y_free';
		$is_edit = (isset($params['line']) && !isset($params['apply_coupons_only']));
		
		
		if(isset($params['item_kit_id']))
		{
			$id = $params['item_kit_id'];
			$kit = true;
		}
		else
		{
			$id = $params['item_id'];
			$kit = false;
		}
		
		if($is_edit)
		{
			$item = $this->get_line_item($params['line']);
			
			if($this->CI->config->item('do_not_group_same_items') || (isset($item['is_serialized']) && $item['is_serialized']))
			{
				return;
			}
		}
		
		//check number of free items we qualify for
		$number_of_discounted_items = $this->get_number_of_free_or_discount_items($rule,$params);
		
		if($number_of_discounted_items > 0)
		{
			$discounted_item_line = $this->get_price_rule_discount_item_line($id, $kit);
			$line_to_edit = $this->get_price_rule_non_discount_item_line($id, $kit);
			
			$line_to_edit_info = $this->get_line_item($line_to_edit);
						
			if($is_edit)
			{
				
				$items_to_buy =  $rule['items_to_buy'];		
				$items_to_get =  $is_bogo_rule ? $rule['items_to_get'] : 1;
				
				if($this->is_price_rule_discount_item_line($params['line']))
				{
					
					$reg_quantity = $number_of_discounted_items*($items_to_buy/$items_to_get);
					
				} else {
					$line_item = $this->get_line_item($params['line']);
					$reg_quantity = $line_item['quantity'];
				}
				
			} else {
				//subtract items to get free from total qty in cart
				$reg_quantity = $params['quantity']-$number_of_discounted_items;
			}	
			
			$current_quantity_of_discounted_item = $this->get_number_of_discount_items_in_cart($id, $kit);
			
			if ($line_to_edit_info['quantity'] > 0)
			{
				if($this->CI->config->item('do_not_group_same_items') || (isset($line_to_edit_info['is_serialized']) && $line_to_edit_info['is_serialized']))
				{
					if($current_quantity_of_discounted_item < $number_of_discounted_items)
					{
						$this->delete_item($line_to_edit);
						$discounted_item_line = FALSE;
					}	
				}
				else 
				{
					if($reg_quantity > 0)
					{
						$this->edit_item($line_to_edit, NULL, NULL,$reg_quantity, NULL, NULL, NULL, FALSE);
					}
					else
					{
						$this->delete_item($line_to_edit);
						$discounted_item_line = $this->get_price_rule_discount_item_line($id, $kit);						
					}
				}
			}
			else
			{				
				$this->delete_item($line_to_edit);
				$discounted_item_line = FALSE;
			}
			
			//do some math here		
			$discounted_item_quantity_to_be_added =  $number_of_discounted_items - $current_quantity_of_discounted_item;
			
			if($discounted_item_quantity_to_be_added !== 0)
			{
				
				//get per item discount price
				if(!$is_bogo_rule)
				{
					
					$price_to_use = $kit ? $this->get_price_for_item_kit($id) : $this->get_price_for_item($id);
					$flat_discount = isset($rule['fixed_off']) ? $rule['fixed_off'] : 0;
					
					$discount = isset($rule['percent_off']) ? $rule['percent_off'] : 0;
					$price = $price_to_use - $flat_discount;
					
				} else {
					$discount = 0;
					$price = 0;
				}
				
				//add/subtract from discounted items
				if($kit)
				{
					$this->add_item_kit('KIT '. $id, $discounted_item_quantity_to_be_added,$discount,$price,null, null, null, TRUE, $discounted_item_line , TRUE, FALSE, $rule);
				} else {
										
					$this->add_item($id.'|FORCE_ITEM_ID|',$discounted_item_quantity_to_be_added,$discount,$price,null, null, null,null, TRUE, $discounted_item_line, TRUE,FALSE, $rule);
				}
			} 
			elseif ($number_of_discounted_items <= 0)
			{
				$this->delete_item($discounted_item_line);
			}
		}	else {
			$this->cleanup_price_rule_items($params);
		}
	}
	
	function apply_spend_x_get_discount($rule)
	{
		$this->CI->load->model('Item');
		
		if ($rule['percent_off'])
		{
			$discount_amount = to_currency_no_money($rule['spend_amount']*($rule['percent_off']/100));
		}
		else
		{
			$discount_amount = to_currency_no_money($rule['fixed_off']);
		}
		
		$rule_spend_amount = $rule['spend_amount'];
		$sub_total = $this->get_subtotal(FALSE, FALSE);
		
		$max = $rule['num_times_to_apply'];
		
		$r = fmod($sub_total, $rule_spend_amount);
		$number_of_times_discount_applies = ($sub_total - $r) / $rule_spend_amount;
		
		if($max > 0 && $number_of_times_discount_applies > $max)
		{
			$number_of_times_discount_applies = $max;
		}
		
		$this->delete_item($this->get_line_for_flat_discount_item());
		$item_id = $this->CI->Item->create_or_update_flat_discount_item();
		
		$neg_apply = $number_of_times_discount_applies*-1;
		
		//discount item
		$this->add_item($item_id.'|FORCE_ITEM_ID|',$neg_apply,0,to_currency_no_money($discount_amount),to_currency_no_money($discount_amount),0,null, null, false, false, true, false, $rule);
	}
	
	function apply_advanced_discount($rule, $params)
	{
		
		if(isset($params['item_id']))
		{
			$id = $params['item_id'];
			$kit = false;
		}
		elseif (isset($params['item_kit_id']))
		{
			$id = $params['item_kit_id'];
			$kit = true;
		}
		
		$discount_percent = NULL;
		$discount_flat = NULL;
		
		if(isset($rule['discount_per_unit_fixed']))
		{
			$discount_flat = $rule['discount_per_unit_fixed'];
		}
		
		elseif(isset($rule['discount_per_unit_percent']))
		{
			$discount_percent = $rule['discount_per_unit_percent'];
		}
		
		if($kit)
		{
			$price_to_use =$this->get_price_for_item_kit($id);
		}
		else
		{
			$price_to_use = $this->get_price_for_item($id);
		}
		
		if($discount_flat)
		{
			$price_to_use = $price_to_use -$discount_flat;
		}
		
		$item_lines_to_apply_discount = $this->get_all_lines_for_item($id, $kit);
		
		foreach($item_lines_to_apply_discount as $line)
		{			
			$this->edit_item($line, NULL, NULL,NULL, $discount_percent, $price_to_use, NULL, FALSE, $rule);
		}
	}
	
	function do_price_rule_for_items_and_item_kits($params)
	{
		$rule = $this->CI->Price_rule->get_price_rule_for_item($params);
					
		return $rule;
	}
	
	function do_spending_price_rule($params)
	{
		$sub_total = $this->get_subtotal(false, false);
		
		$rule = $this->CI->Price_rule->get_price_rule_for_spending($params, $sub_total);
		
		return $rule;
	}
		
	function add_item($item_id,$quantity=1,$discount=0,$price=null,$regular_price = null, $cost_price = null, $description=null,$serialnumber=null, $force_add_or_update = FALSE, $line = FALSE, $update_register_cart_data = TRUE,$apply_price_rules = TRUE,$rule = array())
	{	
		$store_account_item_id = $this->CI->Item->get_store_account_item_id();
		
		//Do NOT allow item to get added unless in store_account_payment mode
		if (!$force_add_or_update && $this->get_mode() !=='store_account_payment' && $store_account_item_id == $item_id)
		{
			return FALSE;
		}
			
		//We are forcing to use item_id
		if (strpos($item_id,'|FORCE_ITEM_ID|') !== FALSE)
		{
			$item_id = str_replace('|FORCE_ITEM_ID|','',$item_id);
			//Lookup item based on just store config; ignore all other fields
			$item_id = $this->CI->Item->lookup_item_id($item_id,array('item_number','product_id','additional_item_numbers','serial_numbers'));
		}
		else
		{
			//Lookup item based on lookup order defined in store config
			$item_id = $this->CI->Item->lookup_item_id($item_id);			
		}
		
		if ($item_id === FALSE)
		{
			return false;
		}		
		
		if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
		{
			if (!$force_add_or_update && $this->will_be_out_of_stock($item_id,$quantity))
			{
				return FALSE;
			}
		}
		
		$item_info = $this->CI->Item->get_info($item_id);
		$item_location_info = $this->CI->Item_location->get_info($item_id);
		
		//Alain Serialization and Description

		//Get all items in the cart so far...
		$items = $this->get_cart();
        //We need to loop through all items in the cart.
        //If the item is already there, get it's key($updatekey).
        //We also need to get the next key that we are going to use in case we need to add the
        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

    $maxkey=0;                       //Highest key so far
    $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)
		$existing_qty=0;

		foreach ($items as $item)
		{
			//We primed the loop so maxkey is 0 the first time.
      //Also, we have stored the key in the element itself so we can compare.

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if(isset($item['item_id']) && $item['item_id']==$item_id && 
					(empty($item['rule']) || ($force_add_or_update || isset($item['rule']['type']) && (!in_array($item['rule']['type'], array('buy_x_get_discount','buy_x_get_y_free', 'simple_discount')))))) //We skip items with buy_x_get_y_free as we don't want to update these normally unless $force_add_or_update
			{
			
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
				
				if($item_info->description==$items[$updatekey]['description'] && $item_info->name==lang('common_giftcard'))
				{
					return false;
				}
			}	
		}
		
		$insertkey=$maxkey+1;

		$today =  strtotime(date('Y-m-d'));
		$price_to_use= $this->get_price_for_item($item_id);
		
  	$item_info = $this->CI->Item->get_info($item_id);		
		$item_location_info = $this->CI->Item_location->get_info($item_id);
		$regular_price = $regular_price !== NULL ? $regular_price : (($item_location_info && $item_location_info->unit_price) ? $item_location_info->unit_price : $item_info->unit_price);
				 
		$cost_price_to_use = ($item_location_info && $item_location_info->cost_price) ? $item_location_info->cost_price : $item_info->cost_price;
				 
		$tax_info = $this->CI->Item_taxes_finder->get_info($item_id);
				 
		//array/cart records are identified by $insertkey and item_id is just another field.
		$item = array(($line === FALSE ? $insertkey : $line)=> 
		array(
			'item_id'=>$item_id,
			'line'=>$line === FALSE ? $insertkey : $line,
			'name'=>$item_info->name,
			'change_cost_price' =>$item_info->change_cost_price,
			'cost_price' => $cost_price!=null ? $cost_price : $cost_price_to_use,
			'size' => $item_info->size,
			'item_number'=>$item_info->item_number,
			'product_id' => $item_info->product_id,
			'description'=>$description!=null ? $description: $item_info->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$item_info->allow_alt_description,
			'is_serialized'=>$item_info->is_serialized,
			'quantity'=>$quantity,
      'discount'=>$discount,
			'price'=>$price !== null ? $price : $price_to_use,
			'regular_price' => $regular_price,
			'tax_included'=> $item_info->tax_included,
			'disable_loyalty' => $item_info->disable_loyalty,
			'is_ebt_item' => $item_info->is_ebt_item,
			'min_edit_price' => $item_info->min_edit_price,
			'max_edit_price' => $item_info->max_edit_price,
			'max_discount_percent' => $item_info->max_discount_percent,
			'rule' => $rule,
			'taxable' => !empty($tax_info),
			)
		);
				
		//Item already exists and is not serialized, add to quantity
		if($itemalreadyinsale && ($item_info->is_serialized ==0) && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
		{
			$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
			$item_key = $line === FALSE ? $updatekey : $line;
		}
		else
		{
			//add to existing array
			$items+=$item;
			$item_key = $line === FALSE ? $insertkey : $line;
		}
		
		
		//this could be moved to do_price_rules		
		if (isset($rule['type']) && $rule['type'] == 'buy_x_get_y_free')
		{
			$quantity_total = $items[$item_key]['quantity'];
			
			$items[$item_key]['rule']['rule_discount'] = $quantity_total * $regular_price;
		}
		elseif (isset($rule['type']) && ($rule['type'] == 'buy_x_get_discount' || $rule['type'] == 'simple_discount'))
		{
			$quantity_total = $items[$item_key]['quantity'];
			$price = $items[$item_key]['price'];
			
			if(isset($items[$item_key]['rule']['percent_off']))
			{
				$items[$item_key]['rule']['rule_discount'] = $quantity_total * $price * ($items[$item_key]['rule']['percent_off']/100);
			} 
			elseif(isset($items[$item_key]['rule']['fixed_off']))
			{
				$items[$item_key]['rule']['rule_discount'] = $quantity_total * $items[$item_key]['rule']['fixed_off'];
			}
		}
		elseif(isset($rule['type']) && $rule['type'] == 'spend_x_get_discount')
		{
			$quantity_total = $items[$item_key]['quantity'];
			$price = $items[$item_key]['price'];
			
			$items[$item_key]['rule']['rule_discount'] = abs($quantity_total) * $regular_price;
		}
		
		$this->set_cart($items,$update_register_cart_data);
		
		if($apply_price_rules)
		{
			$params = array(
				'item_id' => $item_id,
			);
			$this->do_price_rules($params);
		}
		
		return true;
	}
	
	function add_scale_item($scan)
	{
		$data = parse_scale_data($scan);		
		return $this->add_item($data['item_id'].'|FORCE_ITEM_ID|',to_quantity($data['sell_quantity']),0,$data['sell_price']);
	}		
		
	function add_item_kit($external_item_kit_id_or_item_number,$quantity=1,$discount=0,$price=null,$regular_price = null, $cost_price = null,$description=null, $force_add_or_update = FALSE, $line=FALSE,$update_register_cart_data = TRUE, $apply_price_rules = TRUE, $rule = array())
	{		
		if (strpos(strtolower($external_item_kit_id_or_item_number), 'kit') !== FALSE)
		{
			//KIT #
			$pieces = explode(' ',$external_item_kit_id_or_item_number);
			$item_kit_id = (int)$pieces[1];	
		}
		else
		{
			//Lookup item based on lookup order defined in store config
			$item_kit_id = $this->CI->Item_kit->lookup_item_kit_id($external_item_kit_id_or_item_number);
		}
		
		if ($item_kit_id === FALSE)
		{
			return false;
		}	
		
		if ($this->CI->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
		{
			if (!$force_add_or_update && $this->will_be_out_of_stock_kit($item_kit_id,$quantity))
			{
				return FALSE;
			}
		}
	
		//make sure item exists
		if(!$this->CI->Item_kit->exists($item_kit_id))	
		{
			return false;
		}

		$item_kit_info = $this->CI->Item_kit->get_info($item_kit_id);
		$item_kit_location_info = $this->CI->Item_kit_location->get_info($item_kit_id);
		$regular_price = $regular_price !== NULL ? $regular_price : (($item_kit_location_info && $item_kit_location_info->unit_price) ? $item_kit_info->unit_price : $item_kit_info->unit_price);
		
		if ( $item_kit_info->unit_price == null)
		{
			foreach ($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
			{
				//If integer add them one by one; We do this for do NOT group items that are the same
				if ((string)(int)$item_kit_item->quantity == $item_kit_item->quantity)
				{
					for($k=0;$k<$item_kit_item->quantity;$k++)
					{					
						$this->add_item($item_kit_item->item_id.'|FORCE_ITEM_ID|', $quantity,0,null,null,null, null,null,$force_add_or_update, FALSE);
					}
				}
				else
				{
					$this->add_item($item_kit_item->item_id.'|FORCE_ITEM_ID|', $item_kit_item->quantity,0,null,null,null, null,null,$force_add_or_update, FALSE);
				}
			}
			
			return true;
		}
		else
		{
			$items = $this->get_cart();

	        //We need to loop through all items in the cart.
	        //If the item is already there, get it's key($updatekey).
	        //We also need to get the next key that we are going to use in case we need to add the
	        //item to the cart. Since items can be deleted, we can't use a count. we use the highest key + 1.

      $maxkey=0;                       //Highest key so far
      $itemalreadyinsale=FALSE;        //We did not find the item yet.
			$insertkey=0;                    //Key to use for new entry.
			$updatekey=0;                    //Key to use to update(quantity)

			foreach ($items as $item)
			{
        //We primed the loop so maxkey is 0 the first time.
        //Also, we have stored the key in the element itself so we can compare.

				if($maxkey <= $item['line'])
				{
					$maxkey = $item['line'];
				}
				
				if(isset($item['item_kit_id']) && $item['item_kit_id']==$item_kit_id && 
					(empty($item['rule']) || ($force_add_or_update || isset($item['rule']['type']) && (!in_array($item['rule']['type'], array('buy_x_get_discount','buy_x_get_y_free', 'simple_discount')))))) //We skip items with price rules discount as we don't want to update these normally unless $force_add_or_update is true
				{
					$itemalreadyinsale=TRUE;
					$updatekey=$item['line'];
				}
			}
				
			$insertkey=$maxkey+1;
			
			$price_to_use=$this->get_price_for_item_kit($item_kit_id);

			$cost_price_to_use = ($item_kit_location_info && $item_kit_location_info->cost_price) ? $item_kit_location_info->cost_price : $item_kit_info->cost_price;

			$tax_info = $this->CI->Item_kit_taxes_finder->get_info($item_kit_id);

			//array/cart records are identified by $insertkey and item_id is just another field.
			$item = array(($line === FALSE ? $insertkey : $line)=>
			array(
				'item_kit_id'=>$item_kit_id,
				'line'=>$line === FALSE ? $insertkey : $line,
				'item_kit_number'=>$item_kit_info->item_kit_number,
				'product_id'=>$item_kit_info->product_id,
				'name'=>$item_kit_info->name,
				'change_cost_price' =>$item_kit_info->change_cost_price,
				'cost_price' => $cost_price!=null ? $cost_price : $cost_price_to_use,
				'size' => '',
				'description'=>$description!=null ? $description: $item_kit_info->description,
				'quantity'=>$quantity,
	      'discount'=>$discount,
				'price'=>$price !== null ? $price : $price_to_use,
				'regular_price' => $regular_price,
				'tax_included'=> $item_kit_info->tax_included,
				'disable_loyalty' => $item_kit_info->disable_loyalty,
				'is_ebt_item' => $item_kit_info->is_ebt_item,
				'min_edit_price' => $item_kit_info->min_edit_price,
				'max_edit_price' => $item_kit_info->max_edit_price,
				'max_discount_percent' => $item_kit_info->max_discount_percent,
				'rule' => $rule,
				'taxable' => !empty($tax_info),
				)
			);



			//Item already exists and is not serialized, add to quantity
			if($itemalreadyinsale && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
			{
				$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
				$item_kit_key = $line === FALSE ? $updatekey : $line;
				
			}
			else
			{
				//add to existing array
				$items+=$item;
				$item_kit_key = $line === FALSE ? $insertkey : $line;
			}
			
			//needed here				
			if (isset($rule['type']) && $rule['type'] == 'buy_x_get_y_free')
			{
				$quantity_total = $items[$item_kit_key]['quantity'];
				$items[$item_kit_key]['rule']['rule_discount'] = $quantity_total * $regular_price;
			}
			
			if (isset($rule['type']) && ($rule['type'] == 'buy_x_get_discount' || $rule['type'] == 'simple_discount'))
			{
				$quantity_total = $items[$item_kit_key]['quantity'];
				$price = $items[$item_kit_key]['price'];
			
				if(isset($items[$item_kit_key]['rule']['percent_off']))
				{
					$items[$item_kit_key]['rule']['rule_discount'] = $quantity_total * $price * ($items[$item_kit_key]['rule']['percent_off']/100);
				} 
				elseif(isset($items[$item_kit_key]['rule']['fixed_off']))
				{
					$items[$item_kit_key]['rule']['rule_discount'] = $quantity_total * $items[$item_kit_key]['rule']['fixed_off'];
				}
			
			}
			
			$this->set_cart($items,$update_register_cart_data);
			
			if($apply_price_rules)
			{
				$params = array('item_kit_id' => $item_kit_id);
				$this->do_price_rules($params);
			}
			
			return true;
		}
	}
	
	function get_min_edit_price_for_item($line)
	{
		$item = $this->get_line_item($line);
			
		if($item)
		{
			return $item['min_edit_price'];
		}
		
		return false;
	}
	
	
	function get_max_edit_price_for_item($line)
	{
		$item = $this->get_line_item($line);
			
		if($item)
		{
			return $item['max_edit_price'];
		}
		
		return false;
	}
	
	
	function get_max_discount_for_item($line)
	{
		$item = $this->get_line_item($line);
			
		if($item)
		{
			return $item['max_discount_percent'];
		}
		
		return false;
	}
	
	function discount_all($percent_discount)
	{
		$items = $this->get_cart();
		
		foreach(array_keys($items) as $key)
		{
			if ((isset($items[$key]['item_id']) && $items[$key]['item_id'] != $this->CI->Item->get_item_id_for_flat_discount_item()) || isset($items[$key]['item_kit_id']))
			{
				$max_discount = $this->get_max_discount_for_item($key);
				if($max_discount !== NULL && floatval($percent_discount) > floatval($max_discount))
				{
					return false;
				}
			}
		}
		
		foreach(array_keys($items) as $key)
		{
			if ((isset($items[$key]['item_id']) && $items[$key]['item_id'] != $this->CI->Item->get_item_id_for_flat_discount_item()) || isset($items[$key]['item_kit_id']))
			{
				$this->edit_item($key,NULL, NULL, NULL,$percent_discount,NULL, NULL);
			}
		}
		return true;
	}
	
	function out_of_stock($item_id)
	{				
		//make sure item exists
		if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? $item_id : -1))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}
		
		$suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
		$quantity_in_sale = 0;
		
		if ($suspended_change_sale_id)
		{
			$suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;
			
			//Not an estiamte
			if ($suspended_type != 2)
			{
				$quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_in_sale($suspended_change_sale_id, $item_id);			
			}
		}
		
		$item_location_quantity = $this->CI->Item_location->get_location_quantity($item_id);
		$quanity_added = $this->get_quantity_already_added($item_id);
		
		//If $item_location_quantity is NULL we don't track quantity
		if ($item_location_quantity !== NULL && $item_location_quantity - $quanity_added  + $quantity_in_sale < 0)
		{
			return true;
		}
		
		return false;
	}
		
	function will_be_out_of_stock($item_id, $additional_quantity)
	{
		$suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
		
		if ($suspended_change_sale_id)
		{
			$suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;
			
			//Not an estiamte
			if ($suspended_type != 2)
			{
				$quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_in_sale($suspended_change_sale_id, $item_id);
			
				$additional_quantity -= $quantity_in_sale;
			}
		}
		
		//make sure item exists
		if(!$this->CI->Item->exists(does_contain_only_digits($item_id) ? $item_id : -1))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}
		
		$item_location_quantity = $this->CI->Item_location->get_location_quantity($item_id);
		$quanity_added = $this->get_quantity_already_added($item_id) + $additional_quantity;
		
		//If $item_location_quantity is NULL we don't track quantity
		if ($item_location_quantity !== NULL && $item_location_quantity - $quanity_added < 0)
		{
			return true;
		}
		
		return false;
	}
	
	function out_of_stock_kit($kit_id)
	{
	    //Make sure Item kit exist
	    if(!$this->CI->Item_kit->exists($kit_id)) return FALSE;

 		$suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
 		$quantity_in_sale = 0;
		
 		if ($suspended_change_sale_id)
 		{
			$suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;
			
			//Not an estiamte
			if ($suspended_type != 2)
			{
 				$quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_kit_in_sale($suspended_change_sale_id, $kit_id);			
			}
		}
		 
	    //Get All Items for Kit
	    $kit_items = $this->CI->Item_kit_items->get_info($kit_id);

	    //Check each item
	    foreach ($kit_items as $item)
	    {
			$item_location_quantity = $this->CI->Item_location->get_location_quantity($item->item_id);
			$item_already_added = $this->get_quantity_already_added($item->item_id);

			if ($item_location_quantity !== NULL && $item_location_quantity - $item_already_added + $this->get_quantity_to_be_added_from_kit($kit_id, $item->item_id, $quantity_in_sale) < 0)
			{
		    	return true;
			}	
	    }
	    return false;
	}
	
	function will_be_out_of_stock_kit($kit_id, $additional_quantity)
	{
		$suspended_change_sale_id=$this->get_suspended_sale_id() ? $this->get_suspended_sale_id() : $this->get_change_sale_id() ;
		
		if ($suspended_change_sale_id)
		{
			$suspended_type = $this->CI->Sale->get_info($suspended_change_sale_id)->row()->suspended;
			
			//Not an estiamte
			if ($suspended_type != 2)
			{
				$quantity_in_sale = $this->CI->Sale->get_quantity_sold_for_item_kit_in_sale($suspended_change_sale_id, $kit_id);
			
				$additional_quantity -= $quantity_in_sale;
			}
		}
		
	    //Make sure Item kit exist
	    if(!$this->CI->Item_kit->exists($kit_id)) return FALSE;

	    //Get All Items for Kit
	    $kit_items = $this->CI->Item_kit_items->get_info($kit_id);

	    //Check each item
	    foreach ($kit_items as $item)
	    {
			$item_location_quantity = $this->CI->Item_location->get_location_quantity($item->item_id);
			$item_already_added = $this->get_quantity_already_added($item->item_id) + $this->get_quantity_to_be_added_from_kit($kit_id, $item->item_id, $additional_quantity);

			if ($item_location_quantity !== NULL && $item_location_quantity - $item_already_added < 0)
			{
		    	return true;
			}	
	    }
	    return false;
	}

	/*Mike*/
	function get_spending_price_rule_id()
	{
		$cart = $this->get_cart();
		
		foreach($cart as $line=>$item)
		{
			
			if(isset($item['rule']['type']) && $item['rule']['type'] == 'spend_x_get_discount')
			{
				
				return $item['rule']['id'];
			}
		}
		
		return null;
	}
	
	/*Mike*/
	function get_spending_price_rule_discount()
	{
		$cart = $this->get_cart();
		
		foreach($cart as $line=>$item)
		{
			
			if(isset($item['rule']['type']) && $item['rule']['type'] == 'spend_x_get_discount')
			{
				
				return $item['price'];
			}
		}
		
		return null;
	}
	
	
	function below_cost_price_item($line, $price = NULL, $discount = NULL, $cost_price = NULL)
	{
		$cart = $this->get_cart();
		
		if (isset($cart[$line]))
		{
			$line_item = $cart[$line];
			
			if ($line_item['name'] == lang('common_store_account_payment'))
			{
				return false;
			}
			
			if ($price === NULL)
			{
				$price = $line_item['price'];				
			}
			
			if ($discount === NULL)
			{
				$discount = $line_item['discount'];	
			}
			
			if ($cost_price === NULL)
			{
				$cost_price = $line_item['cost_price'];
			}
			
			$total_for_one = $price-$price*$discount/100;
			
			//only report if not price rule discount item
			if(empty($line_item['rule']))
			{
				return $total_for_one < $cost_price;
			}
		}
		
		return FALSE;
	}
		
	function get_quantity_already_added($item_id, $look_in_kits = true)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach ($items as $item)
		{
			if(isset($item['item_id']) && $item['item_id']==$item_id)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		if($look_in_kits)
		{
			//Check Item Kist for this item
			$all_kits = $this->CI->Item_kit_items->get_kits_have_item($item_id);

			foreach($all_kits as $kits)
			{
			    $kit_quantity = $this->get_kit_quantity_already_added($kits['item_kit_id']);
			    if($kit_quantity > 0)
			    {
					$quanity_already_added += ($kit_quantity * $kits['quantity']);
			    }
			}
		}
		
		return $quanity_already_added;
	}
	
	function get_kit_quantity_already_added($kit_id)
	{
	    $items = $this->get_cart();
	    $quanity_already_added = 0;
	    foreach ($items as $item)
	    {
		    if(isset($item['item_kit_id']) && $item['item_kit_id']==$kit_id)
		    {
				$quanity_already_added+=$item['quantity'];
		    }
	    }

	    return $quanity_already_added;
	}
	
	function get_quantity_to_be_added_from_kit($kit_id, $item_id,$quantity)
	{
		$item_kit_items = $this->CI->Item_kit_items->get_info($kit_id);
		
		foreach ($item_kit_items as $item_kit_item)
		{
			if ($item_id == $item_kit_item->item_id)
			{
				return $quantity * $item_kit_item->quantity;
			}
		}
		
		return 0;
	}

	function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach ($items as $line=>$item)
		{
			if($line==$line_to_get)
			{
				return isset($item['item_id']) ? $item['item_id'] : -1;
			}
		}
		
		return -1;
	}
	
	function get_last_item_added_price()
	{
		$items = $this->get_cart();
		
		if (!empty($items))
		{
			//Get last element then reset pointer so nothing gets messed
			$last_item = end($items);
			reset($items);
			return $last_item['price'];
		}		
	
		return FALSE;
	}
	
	function get_last_item_line()
	{
		$items = $this->get_cart();
		
		if (!empty($items))
		{
			//Get last element then reset pointer so nothing gets messed
			$last_item = end($items);
			reset($items);
			return $last_item['line'];
		}		
	
		return FALSE;
		
	}
	
	function get_quantity_at_line($line_to_get)
	{
    $items = $this->get_cart();

    foreach ($items as $line=>$item)
    {
		if($line==$line_to_get)
		{
		    return isset($item['quantity']) ? $item['quantity'] : 0;
		}
    }
    return 0;
	}
	

	function get_kit_id($line_to_get)
	{
	    $items = $this->get_cart();

	    foreach ($items as $line=>$item)
	    {
			if($line==$line_to_get)
			{
			    return isset($item['item_kit_id']) ? $item['item_kit_id'] : -1;
			}
	    }
	    return -1;
	}

	function is_kit_or_item($line_to_get)
	{
	    $items = $this->get_cart();
	    foreach ($items as $line=>$item)
	    {
				if($line==$line_to_get)
				{
				    if(isset($item['item_id']))
				    {
					return 'item';
				    }
				    elseif ($item['item_kit_id'])
				    {
					return 'kit';
				    }
				}
	    }
	    return -1;
	}

	function edit_item($line,$description = NULL,$serialnumber = NULL,$quantity = NULL,$discount = NULL,$price = NULL, $cost_price = NULL,$apply_price_rules = TRUE, $rule = NULL)
	{		
		$items = $this->get_cart();
		
		if(isset($items[$line]))
		{
			if ($description !== NULL ) {
				$items[$line]['description'] = $description;
			}
			if ($discount !== NULL ) {
				$items[$line]['discount'] = $discount;
			}
			if ($price !== NULL ) {
				$items[$line]['price'] = $price;
			}
			if ($cost_price !== NULL ) {
				$items[$line]['cost_price'] = $cost_price;
			}
			if ($quantity !== NULL ) {
							$items[$line]['quantity'] = $quantity;
			}
			
			if ($serialnumber !== NULL ) 
			{
				$items[$line]['serialnumber'] = $serialnumber;
				$this->CI->load->model('Item_serial_number');
				$serial_number_price = $this->CI->Item_serial_number->get_price_for_serial($serialnumber);
				if ($serial_number_price !== FALSE)
				{
					$items[$line]['price'] = $serial_number_price;
				}
			}
			
			if ($rule !== NULL)
			{
				$items[$line]['rule'] = $rule;
			}
		
			if (isset($rule['type']) && ($rule['type'] == 'advanced_discount'))
			{
					$quantity_total = $items[$line]['quantity'];
					$price = $items[$line]['price'];
				
					if(isset($items[$line]['rule']['discount_per_unit_percent']))
					{
						$items[$line]['rule']['rule_discount'] = $quantity_total * $price * ($items[$line]['rule']['discount_per_unit_percent']/100);
					}
					elseif(isset($items[$line]['rule']['discount_per_unit_fixed']))
					{
						$items[$line]['rule']['rule_discount'] = $quantity_total * $items[$line]['rule']['discount_per_unit_fixed'];
					}
			}
			
			$this->set_cart($items);
			
			if ($apply_price_rules)
			{
				$params = array('line' => $line);
				$this->do_price_rules($params);
			}
			return true;
		}
		
		return false;
	}

	function is_valid_receipt($receipt_sale_id)
	{		
		//Valid receipt syntax
		if(strpos(strtolower($receipt_sale_id), strtolower($this->CI->config->item('sale_prefix')).' ') !== FALSE)
		{
			//Extract the id
			$sale_id = substr(strtolower($receipt_sale_id), strpos(strtolower($receipt_sale_id),$this->CI->config->item('sale_prefix').' ') + strlen(strtolower($this->CI->config->item('sale_prefix')).' '));
			return $this->CI->Sale->exists($sale_id);
		}

		return false;
	}
	
	function is_valid_item_kit($item_kit_id)
	{
		//KIT #
		$pieces = explode(' ',$item_kit_id);

		if(count($pieces)==2 && strtolower($pieces[0]) == 'kit')
		{
			return $this->CI->Item_kit->exists($pieces[1]);
		}
		else
		{
			return $this->CI->Item_kit->get_item_kit_id($item_kit_id) !== FALSE;
		}
	}

	function get_valid_item_kit_id($item_kit_id)
	{
		//KIT #
		$pieces = explode(' ',$item_kit_id);

		if(count($pieces)==2 && strtolower($pieces[0]) == 'kit')
		{
			return $pieces[1];
		}
		else
		{
			return $this->CI->Item_kit->get_item_kit_id($item_kit_id);
		}
	}

	function return_entire_sale($receipt_sale_id)
	{
		//POS #
		$sale_id = substr(strtolower($receipt_sale_id), strpos(strtolower($receipt_sale_id),$this->CI->config->item('sale_prefix').' ') + strlen(strtolower($this->CI->config->item('sale_prefix')).' '));

		$this->empty_cart();
		$this->delete_customer(false);
		$sale_taxes = $this->get_taxes($sale_id);
		
		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$item_info = $this->CI->Item->get_info($row->item_id);
			$price_to_use = $row->item_unit_price;			
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_info->tax_included && empty($sale_taxes))
			{
				$this->CI->load->helper('items');
				$price_to_use = get_price_for_item_including_taxes($row->item_id, $row->item_unit_price);
			}
			elseif($item_info->tax_included)
			{
				$this->CI->load->helper('items');
				
				$price_to_use = get_price_for_item_including_taxes($row->line, $row->item_unit_price,$sale_id);				
			}
			
			$this->add_item($row->item_id.'|FORCE_ITEM_ID|',-$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->regular_item_unit_price_at_time_of_sale,$row->item_cost_price,$row->description,$row->serialnumber, TRUE, $row->line, FALSE, FALSE);
		}
		foreach($this->CI->Sale->get_sale_item_kits($sale_id)->result() as $row)
		{
			$item_kit_info = $this->CI->Item_kit->get_info($row->item_kit_id);
			$price_to_use = $row->item_kit_unit_price;
						
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_kit_info->tax_included && empty($sale_taxes))
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row->item_kit_id, $row->item_kit_unit_price);
			}
			elseif ($item_kit_info->tax_included)
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row->line, $row->item_kit_unit_price,$sale_id);
			}
			
			$this->add_item_kit('KIT '.$row->item_kit_id,-$row->quantity_purchased,$row->discount_percent,$price_to_use,$row->regular_item_kit_unit_price_at_time_of_sale,$row->item_kit_cost_price,$row->description, TRUE, $row->line, FALSE, FALSE);
		}
		
		$sale_coupons = array();
		foreach($this->CI->Sale->get_sale_coupons($sale_id)->result_array() as $row)
		{
			$sale_coupons[] = array('value' => $row['rule_id'], 'label' => $row['name'].' - '.$row['coupon_code']);
		}
		$this->set_coupons($sale_coupons);
		
		$this->update_register_cart_data();
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id, false);		
		$this->set_discount_reason($this->CI->Sale->get_discount_reason($sale_id));
		$this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));
		$this->set_exchange_details($this->CI->Sale->get_exchange_details($sale_id));
		$this->set_register_cart_if_needed($sale_id);
	}
	
	function copy_entire_sale($sale_id, $is_receipt = false)
	{
		$this->empty_cart();
		$this->delete_customer(false);
		$sale_taxes = $this->get_taxes($sale_id);		
		
		foreach($this->CI->Sale->get_sale_items($sale_id)->result_array() as $row)
		{
			$item_info = $this->CI->Item->get_info($row['item_id']);
			$price_to_use = $row['item_unit_price'];
			
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_info->tax_included && empty($sale_taxes) && !$is_receipt)
			{
				$this->CI->load->helper('items');	
				$price_to_use = get_price_for_item_including_taxes($row['item_id'], $row['item_unit_price']);
			}
			elseif($item_info->tax_included)
			{
				$this->CI->load->helper('items');
				$price_to_use = get_price_for_item_including_taxes($row['line'], $row['item_unit_price'],$sale_id);				

			}
			$rule = array();
		
			if ($row['rule_id'])
			{
				$this->CI->load->model('Price_rule');
				$rule = $this->CI->Price_rule->get_rule_info($row['rule_id']);
			}
			$this->add_item($row['item_id'].'|FORCE_ITEM_ID|',$row['quantity_purchased'],$row['discount_percent'],$price_to_use,$row['regular_item_unit_price_at_time_of_sale'], $row['item_cost_price'], $row['description'],$row['serialnumber'], TRUE, $row['line'], FALSE, FALSE,$rule);
			
		}
		
		foreach($this->CI->Sale->get_sale_item_kits($sale_id)->result_array() as $row)
		{
			$item_kit_info = $this->CI->Item_kit->get_info($row['item_kit_id']);
			$price_to_use = $row['item_kit_unit_price'];
			
			//If we have tax included, but we don't have any taxes for sale, pretend that we do have taxes so the right price shows up
			if ($item_kit_info->tax_included && empty($sale_taxes) && !$is_receipt)
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row['item_kit_id'], $row['item_kit_unit_price']);
			}
			elseif ($item_kit_info->tax_included)
			{
				$this->CI->load->helper('item_kits');
				$price_to_use = get_price_for_item_kit_including_taxes($row['line'], $row['item_kit_unit_price'],$sale_id);
			}
			

			$rule = array();
			
			if ($row['rule_id'])
			{
				$this->CI->load->model('Price_rule');
				$rule = $this->CI->Price_rule->get_rule_info($row['rule_id']);
			}
			
			$this->add_item_kit('KIT '.$row['item_kit_id'],$row['quantity_purchased'],$row['discount_percent'],$price_to_use,$row['regular_item_kit_unit_price_at_time_of_sale'],$row['item_kit_cost_price'],$row['description'], TRUE, $row['line'], FALSE, FALSE, $rule);	
			
		}

		
		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount, $row->payment_date, $row->truncated_card, $row->card_issuer, $row->auth_code, $row->ref_no, $row->cc_token, $row->acq_ref_data, $row->process_data, $row->entry_method, $row->aid, $row->tvr, $row->iad, $row->tsi, $row->arc, $row->cvm, $row->tran_type, $row->application_label,$row->ebt_voucher_no,$row->ebt_auth_code);
			
		}
		
		$this->update_register_cart_data();
		
		$customer_info = $this->CI->Sale->get_customer($sale_id);
		$this->set_customer($customer_info->person_id, false);			
		$this->set_comment($this->CI->Sale->get_comment($sale_id));
		$this->set_discount_reason($this->CI->Sale->get_discount_reason($sale_id));
		$this->set_comment_on_receipt($this->CI->Sale->get_comment_on_receipt($sale_id));

		$this->set_sold_by_employee_id($this->CI->Sale->get_sold_by_employee_id($sale_id));
		$this->set_deleted_taxes($this->CI->Sale->get_deleted_taxes($sale_id));
		$this->set_exchange_details($this->CI->Sale->get_exchange_details($sale_id));
		$this->set_register_cart_if_needed($sale_id);
		
		//Add delivery fields
		$this->CI->load->model('Delivery');
		$this->CI->load->model('Person');
		
		
		$delivery = $this->CI->Delivery->get_info_by_sale_id($sale_id);
		
		if($delivery->num_rows()==1)
		{
			$this->set_delivery(1);
			$delivery = $delivery->row_array();
		
			$this->set_delivery_info($delivery);
			
			$delivery_person = (array)$this->CI->Person->get_info($this->CI->Delivery->get_delivery_person_id($sale_id));
			$this->set_delivery_person_info($delivery_person);
			
			$this->set_delivery_tax_group_id($this->CI->Delivery->get_delivery_tax_group_id($sale_id));
		}
	  
		
	}
	
	function set_register_cart_if_needed($sale_id)
	{
			$sale_info = $this->CI->Sale->get_info($sale_id)->row();		
			$register_id = $this->CI->Employee->get_logged_in_employee_current_register_id();
		
			if (!$register_id)
			{
				if ($sale_info->register_id)
				{
					$this->CI->Employee->set_employee_current_register_id($sale_info->register_id);
				}
				else
				{
					$location_id=$this->CI->Employee->get_logged_in_employee_current_location_id();
					$register_count = $this->CI->Register->count_all($location_id);
		
					if ($register_count > 0)
					{
							$registers = $this->CI->Register->get_all($location_id);
							$register = $registers->row_array();
			
							if (isset($register['register_id']))
							{
								$this->CI->Employee->set_employee_current_register_id($register['register_id']);
							}
					}
			}
		}
	}

	function get_suspended_sale_id()
	{
		return $this->CI->session->userdata('suspended_sale_id');
	}
	
	function set_suspended_sale_id($suspended_sale_id)
	{
		$this->CI->session->set_userdata('suspended_sale_id',$suspended_sale_id);
	}
	
	function delete_suspended_sale_id()
	{
		$this->CI->session->unset_userdata('suspended_sale_id');
	}
	
	function get_change_sale_id()
	{
		return $this->CI->session->userdata('change_sale_id');
	}
	
	function set_change_sale_id($change_sale_id)
	{
		$this->CI->session->set_userdata('change_sale_id',$change_sale_id);
	}
	
	function delete_change_sale_id()
	{
		$this->CI->session->unset_userdata('change_sale_id');
	}
	
	function delete_item($line, $apply_price_rules = false)
	{
		$item = $this->get_line_item($line);     
			
		if($apply_price_rules && !$this->is_price_rule_discount_line($line))
		{
			if(!$this->CI->config->item('do_not_group_same_items') && (isset($item['is_serialized']) && !$item['is_serialized']))
			{
				if(isset($item['item_kit_id']))
				{
					$params = array('item_kit_id' => $item['item_kit_id']);
				} else {
					$params = array('item_id' => $item['item_id']);
				}
			}
		}
		
		$items=$this->get_cart();
		$item_id=$this->get_item_id($line);
		
		unset($items[$line]);
		$this->set_cart($items);
		
		if($apply_price_rules && isset($params))
		{
			$this->do_price_rules($params);
		}
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cart');
		$this->CI->Register_cart->remove_data('cart',$this->CI->Employee->get_logged_in_employee_current_register_id());
	}

	function delete_customer($change_price = true)
	{
		$this->CI->session->unset_userdata('customer');
			
		if ($change_price == true)
		{
			$this->change_price();
		}
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('sale_mode');
	}
	
	function clear_redeem()
	{
		$this->CI->session->unset_userdata('redeem');
	}
	
	function set_redeem($redeem)
	{
		$this->CI->session->set_userdata('redeem',$redeem);
	}
	
	function get_redeem()
	{
		return $this->CI->session->userdata('redeem');
	}
	
	
	function clear_cc_info()
	{
		$this->CI->session->unset_userdata('ref_no');
		$this->CI->session->unset_userdata('auth_code');
		$this->CI->session->unset_userdata('masked_account');
		$this->CI->session->unset_userdata('cc_token');
		$this->CI->session->unset_userdata('acq_ref_data');
		$this->CI->session->unset_userdata('process_data');
		$this->CI->session->unset_userdata('card_issuer');
		$this->CI->session->unset_userdata('entry_method');
		$this->CI->session->unset_userdata('aid');
		$this->CI->session->unset_userdata('tvr');
		$this->CI->session->unset_userdata('iad');
		$this->CI->session->unset_userdata('tsi');
		$this->CI->session->unset_userdata('arc');
		$this->CI->session->unset_userdata('cvm');
		$this->CI->session->unset_userdata('tran_type');
		$this->CI->session->unset_userdata('application_label');
		$this->CI->session->unset_userdata('ebt_balance');
		$this->CI->session->unset_userdata('text_response');
		$this->CI->session->unset_userdata('CC_SUCCESS');		
	}
		
	
	function get_ebt_auth_code()
	{
		return $this->CI->session->userdata('ebt_auth_code');
	}
	
	function set_ebt_auth_code($ebt_auth_code)
	{
		$this->CI->session->set_userdata('ebt_auth_code',$ebt_auth_code);
	}
	
	function get_ebt_voucher_no()
	{
		return $this->CI->session->userdata('ebt_voucher_no');		
	}
	
	function set_ebt_voucher_no($ebt_voucher_no)
	{
		$this->CI->session->set_userdata('ebt_voucher_no',$ebt_voucher_no);
	}
	
	function set_ebt_voucher($voucher)
	{
		$this->CI->session->set_userdata('ebt_voucher',($voucher));		
	}
	
	function get_ebt_voucher()
	{
		return $this->CI->session->userdata('ebt_voucher');		
	}
	
	
	function clear_ebt_data()
	{
		$this->CI->session->unset_userdata('ebt_auth_code');
		$this->CI->session->unset_userdata('ebt_voucher_no');
		$this->CI->session->unset_userdata('ebt_voucher');
	}

	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->clear_comment();
		$this->clear_show_comment_on_receipt();
		$this->clear_change_sale_date();
		$this->clear_change_sale_date_enable();
		$this->clear_email_receipt();
		$this->empty_payments();
		$this->delete_customer(false);
		$this->delete_suspended_sale_id();
		$this->delete_change_sale_id();
		$this->delete_partial_transactions();
		$this->clear_save_credit_card_info();
		$this->clear_use_saved_cc_info();
		$this->clear_prompt_for_card();
		$this->clear_selected_tier_id();
		$this->clear_deleted_taxes();
		$this->clear_cc_info();
		$this->clear_sold_by_employee_id();
		$this->clear_selected_payment();
		$this->clear_invoice_no();
		$this->clear_redeem();
		$this->clear_all_paid_store_account_sales();
		$this->clear_discount_reason();
		$this->clear_ebt_data();
		$this->clear_coupons();
		$this->clear_delivery();
		$this->clear_exchange_details();
	}
	
	function clear_delivery()
	{
		$this->CI->session->unset_userdata('delivery');
		$this->CI->session->unset_userdata('delivery_info');
		$this->CI->session->unset_userdata('delivery_person_info');
		$this->CI->session->unset_userdata('delivery_tax_group_id');		
	}
	
	function clear_coupons()
	{
		$this->CI->session->unset_userdata('coupons');
	}
	
	function set_coupons($coupons)
	{
		$this->CI->session->set_userdata('coupons',$coupons);
		
  	foreach($this->get_cart() as $line=>$item)
	  {
			if($line !== $this->get_line_for_flat_discount_item())
			{
				//if the item still exists
				if($this->get_line_item($line))
				{
					$params = array('line' => $line, 'apply_coupons_only' => true);
					$this->do_price_rules($params);
				}
			}			
		}
	}
	
	function get_coupons()
	{
		return $this->CI->session->userdata('coupons');
	}
	
	function save_current_sale_state()
	{
		$this->sale_state = $this->CI->session->all_userdata();
	}
	
	function restore_current_sale_state()
	{
		if (isset($this->sale_state))
		{
			$this->CI->session->set_userdata($this->sale_state);
		}
	}
	
	function get_tax_total_amount($sale_id = false)
	{
		$taxes = $this->get_taxes($sale_id);
		$total_tax = 0;
		foreach($taxes as $name=>$value) 
		{
			$total_tax+=$value;
	 	}
		
		return to_currency_no_money($total_tax);
	}

	function get_taxes($sale_id = false)
	{
		$taxes = array();
		
		if ($sale_id)
		{
			$taxes_from_sale = array_merge($this->CI->Sale->get_sale_items_taxes($sale_id), $this->CI->Sale->get_sale_item_kits_taxes($sale_id));
			foreach($taxes_from_sale as $key=>$tax_item)
			{
				$name = $tax_item['percent'].'% ' . $tax_item['name'];
			
				if ($tax_item['cumulative'])
				{
					$prev_tax = ($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100)*(($taxes_from_sale[$key-1]['percent'])/100);
					$tax_amount=(($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100) + $prev_tax)*(($tax_item['percent'])/100);					
				}
				else
				{
					$tax_amount=($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100)*(($tax_item['percent'])/100);
				}

				if (!isset($taxes[$name]))
				{
					$taxes[$name] = 0;
				}
				$taxes[$name] += $tax_amount;
			}
		}
		else
		{
			$customer_id = $this->get_customer();
			$customer = $this->CI->Customer->get_info($customer_id);

			//Do not charge sales tax if we have a customer that is not taxable
			if (!$customer->taxable and $customer_id!=-1)
			{
			   return array();
			}

			foreach($this->get_cart() as $line=>$item)
			{
				$price_to_use = $this->_get_price_for_item_in_cart($item);		
				
				$tax_info = isset($item['item_id']) ? $this->CI->Item_taxes_finder->get_info($item['item_id']) : $this->CI->Item_kit_taxes_finder->get_info($item['item_kit_id']);
				foreach($tax_info as $key=>$tax)
				{
					$name = $tax['percent'].'% ' . $tax['name'];
				
					if ($tax['cumulative'])
					{
						$prev_tax = ($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax_info[$key-1]['percent'])/100);
						$tax_amount=(($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100) + $prev_tax)*(($tax['percent'])/100);					
					}
					else
					{
						$tax_amount=($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax['percent'])/100);
					}

					if (!in_array($name, $this->get_deleted_taxes()))
					{
						if (!isset($taxes[$name]))
						{
							$taxes[$name] = 0;
						}
					
						$taxes[$name] += $tax_amount;
					}
				}
			}
		}		
		return $taxes;
	}
	
	function get_items_in_cart()
	{
		$items_in_cart = 0;
		foreach($this->get_cart() as $item)
		{
		    $items_in_cart+=$item['quantity'];
		}
		
		return $items_in_cart;
	}
	
	function get_subtotal($sale_id = FALSE, $include_discount = TRUE)
	{
		$exchange_rate = $this->get_exchange_rate() ? $this->get_exchange_rate() : 1;
		
		$subtotal = 0;
		$discount_item_line = $this->get_line_for_flat_discount_item();
		
		
		foreach($this->get_cart() as $line => $item)
		{
			if(!$include_discount)
			{
				if($line == $discount_item_line)
				{
					continue;
				}	
			}
			
			$price_to_use = $this->_get_price_for_item_in_cart($item, $sale_id);
			if (isset($item['tax_included']) && $item['tax_included'])
			{
		    	$subtotal+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
				
			}
			else
			{
		    	$subtotal+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
				
			}
		}
		
		return to_currency_no_money($subtotal*$exchange_rate);
	}
	
	function _get_price_for_item_in_cart($item, $sale_id = FALSE)
	{
		
		$price_to_use = $item['price'];
		
		if (isset($item['item_id']))
		{
			$item_info = $this->CI->Item->get_info($item['item_id']);
			if($item_info->tax_included)
			{
				if ($sale_id)
				{
					$this->CI->load->helper('items');
					$price_to_use = get_price_for_item_excluding_taxes($item['line'], $item['price'], $sale_id);
				}
				else
				{
					$this->CI->load->helper('items');
					$price_to_use = get_price_for_item_excluding_taxes($item['item_id'], $item['price']);
				}
			}
		}
		elseif (isset($item['item_kit_id']))
		{
			$item_kit_info = $this->CI->Item_kit->get_info($item['item_kit_id']);
			if($item_kit_info->tax_included)
			{
				if ($sale_id)
				{
					$this->CI->load->helper('item_kits');
					$price_to_use = get_price_for_item_kit_excluding_taxes($item['line'], $item['price'], $sale_id);
				}
				else
				{
					$this->CI->load->helper('item_kits');
					$price_to_use = get_price_for_item_kit_excluding_taxes($item['item_kit_id'], $item['price']);
				}
			}
		}
		
		return $price_to_use;
	}

	function get_total_quantity()
	{
		$discount_item_line = $this->get_line_for_flat_discount_item();
		$cart_count = 0;
	  foreach($this->get_cart() as $line=>$item) 
	  { 
			if ($discount_item_line != $line)
			{
	 	 		$cart_count = $cart_count + $item['quantity'];
			}
		}
	 
	 return $cart_count;
	}


	function get_total($sale_id = false)
	{
		$exchange_rate = $this->get_exchange_rate() ? $this->get_exchange_rate() : 1;
		
		$total = 0;
		foreach($this->get_cart() as $item)
		{
			$price_to_use = $this->_get_price_for_item_in_cart($item, $sale_id);
			if (isset($item['tax_included']) && $item['tax_included'])
			{
		    	$total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
				
			}
			else
			{
		    	$total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
				
			}
		}
		
		foreach($this->get_taxes($sale_id) as $tax)
		{
			$total+=$tax;
		}
		$total = $this->CI->config->item('round_cash_on_sales') && $this->is_sale_cash_payment() ?  round_to_nearest_05($total) : $total;
		return to_currency_no_money($total*$exchange_rate);
	}	
		function get_item_subtotal($line)
		{
			$cart = $this->get_cart();
			$item = $cart[$line];
			$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
			
			if (isset($item['tax_included']) && $item['tax_included'])
			{
			  $subtotal=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
			}
			else
			{
			  $subtotal=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
			}
						
	 		return to_currency_no_money($subtotal);
		}
		
		
		function get_item_total($line)
		{
			$cart = $this->get_cart();
			$item = $cart[$line];
			
			$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
			if (isset($item['tax_included']) && $item['tax_included'])
			{
			  $total=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
			}
			else
			{
			  $total=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
			}
			
			$tax_info = isset($item['item_id']) ? $this->CI->Item_taxes_finder->get_info($item['item_id']) : $this->CI->Item_kit_taxes_finder->get_info($item['item_kit_id']);
			foreach($tax_info as $key=>$tax)
			{		
				if ($tax['cumulative'])
				{
					$prev_tax = ($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax_info[$key-1]['percent'])/100);
					$tax_amount=(($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100) + $prev_tax)*(($tax['percent'])/100);					
				}
				else
				{
					$tax_amount=($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100)*(($tax['percent'])/100);
				}
				
				$name = $tax['percent'].'% ' . $tax['name'];
			
				if (!in_array($name, $this->get_deleted_taxes()))
				{
					$total+=$tax_amount;
				}				
			}
			
	 		return to_currency_no_money($total);
		}
		
		function get_item_profit($line,$item_cost_price )
		{
			$cart = $this->get_cart();
			$item = $cart[$line];
			$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
		  $profit=to_currency_no_money(($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100) - ($item_cost_price*$item['quantity']));
			
	 		return to_currency_no_money($profit);
		}
	
	
	function is_sale_cash_payment()
	{
		foreach($this->get_payments() as $payment)
		{
			if($payment['payment_type'] ==  lang('common_cash'))
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	
	function does_customer_have_address()
	{
		$customer_id=$this->get_customer();
		if($customer_id != -1)
		{
			$cust_info=$this->CI->Customer->get_info($customer_id);
			$required_address_fields = array($cust_info->address_1, $cust_info->city, $cust_info->state, $cust_info->zip);
			
			foreach ($required_address_fields as $field) {
				if(!isset($field) || empty($field))
				{
					return false;
				}
			}
			
			return true;
		}
		
		return FALSE;
	}
	
	function is_over_credit_limit()
	{
		$customer_id=$this->get_customer();
		if($customer_id!=-1)
		{
			$cust_info=$this->CI->Customer->get_info($customer_id);
			$current_sale_store_account_balance = $this->get_payment_amount(lang('common_store_account'));
			return $cust_info->credit_limit !== NULL && $cust_info->balance + $current_sale_store_account_balance > $cust_info->credit_limit;
		}
		
		return FALSE;
	}
	
	function get_line_for_delivery_item()
	{
		$item_id_for_delivery_item = $this->CI->Item->get_item_id_for_delivery_item();
		
		$items = $this->get_cart();
		foreach ($items as $line=>$item )
		{
			if (isset($item['item_id']) && $item['item_id'] == $item_id_for_delivery_item)
			{
				return $line;
			}
		}
		
		return FALSE;
	}
	
	function get_delivery_item_price_in_cart()
	{
		$line = $this->get_line_for_delivery_item();
		$item = $this->get_line_item($line);
		if($item)
		{
			return $item['price'];
		}
		
		return 0;
	}
	
	
	
	function get_line_for_flat_discount_item()
	{
		$item_id_for_flat_discount_item = $this->CI->Item->get_item_id_for_flat_discount_item();
		
		$items = $this->get_cart();
		foreach ($items as $line=>$item )
		{
			if (isset($item['item_id']) && $item['item_id'] == $item_id_for_flat_discount_item)
			{
				return $line;
			}
		}
		
		return FALSE;
		
	}
	
	function get_discount_all_percent()
	{
		$percent_discount = NULL;
		$first_item = NULL;
		
		$line_for_fixed_discount = $this->get_line_for_flat_discount_item();
		$items = $this->get_cart();
		
		if (count($items) > 0)
		{
			foreach ($items as $line=>$item )
			{
				if ($line != $line_for_fixed_discount)
				{
					$first_item = $items[$line];
					break;
				}
			}
			$percent_discount = $first_item['discount'];
			
			foreach ($items as $line=>$item )
			{
				if ($line != $line_for_fixed_discount)
				{
					if ($item['discount'] == $percent_discount)
					{
						$percent_discount = $item['discount'];
					}
					else
					{
						$percent_discount = NULL;
						break;
					}
				}
			}
		}
		return $percent_discount;
	}
	
	function get_discount_all_fixed()
	{
		
		$line_for_fixed_discount = $this->get_line_for_flat_discount_item();
		
		if ($line_for_fixed_discount)
		{
			$cart = $this->get_cart();
			$item = $cart[$line_for_fixed_discount];
			
			return to_currency_no_money($item['price'] * -$item['quantity']);
		}
		
		return NULL;
	}
	
	function has_discount()
	{
		$items = $this->get_cart();
		$line_for_fixed_discount = $this->get_line_for_flat_discount_item();
		
		if ($line_for_fixed_discount && $items[$line_for_fixed_discount]['price']!=0)
		{
			return true;
		}
		
		if (count($items) > 0)
		{
			
			foreach ($items as $line=>$item )
			{
				if ($item['discount'] != 0)
				{
					return true;
				}
			}
		}
	
		return false;
			
	}
	
	function set_discount_reason($discount_reason)
	{
		$this->CI->session->set_userdata('sale_discount_reason', $discount_reason);
	}
	
	function clear_discount_reason()
	{		
		$this->CI->session->unset_userdata('sale_discount_reason');
	}
	
	function get_discount_reason()
	{
		return $this->CI->session->userdata('sale_discount_reason') ? $this->CI->session->userdata('sale_discount_reason') : '';
	}

	function get_is_ebt_sale()
	{
		$this->CI->load->helper('sale');
		return is_ebt_sale_not_ebt_cash();
	}
	
	function get_ebt_total_amount_to_charge()
	{
		$total = 0;
		
		foreach($this->get_cart() as $line=>$item)
		{
			if ($item['is_ebt_item'])
			{
				$price_to_use= $this->_get_price_for_item_in_cart($item);
	    	$total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100,10);
			}
		}
		
		if ($total >= 0)
		{
			return min($total,$this->get_amount_due());
		}
		else
		{
			return max($total,$this->get_amount_due());			
		}
	}	

	function can_convert_cart_from_sale_to_return()
	{
		$cart = $this->get_cart();
		
		if (!$cart || count($cart) == 0)
		{
			return FALSE;
		}
		
		foreach($cart as $cart_item)
		{	
			if ($cart_item["quantity"] < 0)
			{
				return false;
			}
		}
		unset($cart_item);
		
		return TRUE;
	}
	
	function do_convert_cart_from_sale_to_return()
	{
		$cart = $this->get_cart();
		
		foreach($cart as &$cart_item)
		{
			$cart_item["quantity"] = -1 * abs($cart_item["quantity"]);
		}
		//clean up referance as cart is modified directly with &
		unset($cart_item);
		
		$this->set_cart($cart);
	}
	
	function can_convert_cart_from_return_to_sale()
	{
		$cart = $this->get_cart();
		
		if (!$cart || count($cart) == 0)
		{
			return FALSE;
		}
		
		foreach($cart as $cart_item)
		{	
			if ($cart_item["quantity"] > 0)
			{
				return false;
			}
		}
		unset($cart_item);
		
		return TRUE;
	}
	
	function do_convert_cart_from_return_to_sale()
	{
		$cart = $this->get_cart();
		
		foreach($cart as &$cart_item)
		{
			$cart_item["quantity"] = 1 * abs($cart_item["quantity"]);
		}
		//clean up referance as cart is modified directly with &
		unset($cart_item);
		
		$this->set_cart($cart);
	}
	
	
	function get_exchange_rate()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $rate ? $rate : 1;
	}
	
	function get_exchange_name()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $name;
	}

	function get_exchange_currency_symbol()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $currency_symbol ? $currency_symbol : '$';
	}
	
	function get_exchange_currency_symbol_location()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $currency_symbol_location ? $currency_symbol_location : 'before';
		
	}
		
	function get_exchange_currency_number_of_decimals()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $number_of_decimals !=='' ? $number_of_decimals : '';
		
	}
		
	function get_exchange_currency_thousands_separator()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $thousands_separator ? $thousands_separator : ',';
		
	}
		
	function get_exchange_currency_decimal_point()
	{
		$details = $this->CI->session->userdata('sale_exchange_details');
  	@list($rate, $name,$currency_symbol,$currency_symbol_location,$number_of_decimals,$thousands_separator,$decimal_point) = explode("|",$details);
		
		return $decimal_point ? $decimal_point : '.';
	}
		
	function get_exchange_details()
	{
		return $this->CI->session->userdata('sale_exchange_details');
	}
	
	function set_exchange_details($rate_det)
	{
		$this->CI->session->set_userdata('sale_exchange_details',$rate_det);
		$this->update_register_cart_data();
	}
	
	function clear_exchange_details() 	
	{
		$this->CI->session->unset_userdata('sale_exchange_details');
	}	
}
?>