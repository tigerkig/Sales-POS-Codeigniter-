<?php
require_once ("Creditcardprocessor.php");
class Stripeprocessor extends Creditcardprocessor
{
	function __construct($controller)
	{
		parent::__construct($controller);
		require_once('stripe/init.php');
	  	\Stripe\Stripe::setApiKey($this->controller->Location->get_info_for_key('stripe_private'));
	}	
	
	public function start_cc_processing()
	{
		$data = array();
		$cc_amount = $this->controller->sale_lib->get_payment_amount(lang('common_credit'));
		
		if ($cc_amount <=0)
		{
			$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
			$this->controller->_reload(array('error' => lang('sales_cannot_process_sales_less_than_0')), false);
			return;
		}
		if(!$this->controller->sale_lib->get_use_saved_cc_info())
		{
			$data['cc_amount'] = to_currency($cc_amount);
			$this->controller->load->view('sales/stripe_checkout', $data);			
		}
		else
		{
		  	try 
		  	{
				$charge_amount_in_cents = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit'))) * 100;
			
				$customer_id = $this->controller->sale_lib->get_customer();
				$customer_info=$this->controller->Customer->get_info($customer_id);
			
				$charge_parameters = array(
		  			"amount" => $charge_amount_in_cents,
		  	  		"currency" => $this->controller->Location->get_info_for_key('stripe_currency_code'),
					'customer' => $customer_info->cc_token,
				);
			
		  		$charge = \Stripe\Charge::create($charge_parameters);
				
				$charge_id = $charge->id;
				$masked_account = $charge->source->last4;
				$card_brand = $charge->source->brand;
				$this->controller->session->set_userdata('ref_no', $charge_id);
				$this->controller->session->set_userdata('masked_account', $masked_account);
				$this->controller->session->set_userdata('card_issuer', $card_brand);
			
				if ($this->controller->_payments_cover_total())
				{
					$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
					redirect(site_url('sales/complete'));
				}
				else //Change payment type to Partial Credit Card and show sales interface
				{
					$credit_card_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));

					$partial_transaction = array(
						'charge_id' => $charge_id,
					);
									
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
					$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $credit_card_amount, FALSE, $masked_account, $card_brand,'', $charge_id, '','','','', '', '', '', '', '', '', '', '');
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
					return;
				}
				
			}
		  	catch (Exception $e)
		  	{				
				//If we have failed, remove cc token and cc preview
				$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
				$customer_info = array('cc_token' => NULL, 'cc_preview' => NULL, 'card_issuer' => NULL);
				$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
				
				//Clear cc token for using saved cc info
				$this->controller->sale_lib->clear_use_saved_cc_info();
				
				
				$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
				$this->controller->_reload(array('error' => lang('sales_cc_decline')), false);
				return;
		  	}
		}		
	}
	public function finish_cc_processing()
	{
		$token = $this->controller->input->post('stripeToken');
		
		if (!$token)
		{
			$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
			$this->controller->_reload(array('error' => lang('sales_unknown_card_error')), false);
			return;
		}
		
		$charge_amount_in_cents = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit'))) * 100;
		
	  	// Get the credit card details submitted by the form
	  	// Create the charge on Stripe's servers - this will charge the user's card
	  	try 
	  	{
			$charge_parameters = array(
	  			"amount" => $charge_amount_in_cents,
	  	  		"currency" => $this->controller->Location->get_info_for_key('stripe_currency_code'),
			);
			
			//We want to save/update card when we have a customer AND they have chosen to save
			if (($this->controller->sale_lib->get_save_credit_card_info()) && $this->controller->sale_lib->get_customer() != -1)
			{
				$customer_info=$this->controller->Customer->get_info($this->controller->sale_lib->get_customer());
				
				$customer = \Stripe\Customer::create(
				  array(
					 'source' => $token,
   			    'email' => $customer_info->email,
					 'description' => $customer_info->first_name.' '.$customer_info->last_name,
					)
				);
				
				$cc_token = $customer->id;
				$masked_account = $customer->sources->data[0]->last4;
				$card_issuer = $customer->sources->data[0]->brand;
								
				$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
				$customer_info = array('cc_token' => $cc_token, 'cc_preview' => $masked_account, 'card_issuer' => $card_issuer);
				$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
				$charge_parameters['customer'] = $cc_token;
				
			}
			else
			{
				$charge_parameters['card'] = $token;
			}
			
	  		$charge = \Stripe\Charge::create($charge_parameters);
			
			$charge_id = $charge->id;
			$masked_account = $charge->source->last4;
			$card_brand = $charge->source->brand;
			$this->controller->session->set_userdata('ref_no', $charge_id);
			$this->controller->session->set_userdata('masked_account', $masked_account);
			$this->controller->session->set_userdata('card_issuer', $card_brand);
			
			if ($this->controller->_payments_cover_total())
			{
				$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
				redirect(site_url('sales/complete'));
			}
			else //Change payment type to Partial Credit Card and show sales interface
			{
				$credit_card_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));

				$partial_transaction = array(
					'charge_id' => $charge_id,
				);
									
				$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
				$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $credit_card_amount, FALSE, $masked_account, $card_brand,'', $charge_id, '','','','', '', '', '', '', '', '', '', '');
				$this->controller->sale_lib->add_partial_transaction($partial_transaction);
				$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
				return;
			}
			
			
	  	} 
	  	catch(Exception $e) 
	  	{
			if ($this->controller->sale_lib->get_customer() != -1)
			{
				//If we have failed, remove cc token and cc preview
				$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
				$customer_info = array('cc_token' => NULL, 'cc_preview' => NULL, 'card_issuer' => NULL);
				$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
		
				//Clear cc token for using saved cc info
				$this->controller->sale_lib->clear_use_saved_cc_info();
			}		
		
			$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
			$this->controller->_reload(array('error' => lang('sales_cc_decline')), false);
			return;		
	  	}
				
	}
	public function cancel_cc_processing()
	{
		$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
		$this->controller->_reload(array('error' => lang('sales_cc_processing_cancelled')), false);
		
	}
	public function void_partial_transactions()
	{
		$void_success = true;
				
		$partial_transactions = $this->controller->sale_lib->get_partial_transactions() ;
		
		if ($partial_transactions)
		{
			foreach($partial_transactions as $transaction)
			{
				$charge_id = $transaction['charge_id'];
			
				try
				{
					\Stripe\Refund::create(array(
						"charge" => $charge_id,
					));
				}
			  	catch (Exception $e)
				{
					$void_success = false;
				}
			}
		}
				
		return $void_success;
		
	}
	
	public function void_sale($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_sale($sale_id))
		{
			$void_success = true;
						
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			foreach($payments as $payment)
			{
				try
				{
					\Stripe\Refund::create(array(
						"charge" => $payment['ref_no'],
					));
				}
			  	catch (Exception $e)
				{
					$void_success = false;
				}
			}
			
			return $void_success;
		}
		
		return FALSE;
		
	}
	public function void_return($sale_id)
	{
		//Cannot do in stripe
		return FALSE;
	}
	
}