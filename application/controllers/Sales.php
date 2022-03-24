<?php
require_once ("Secure_area.php");
class Sales extends Secure_area
{
	function __construct()
	{
		parent::__construct('sales');
		
		$this->load->library('sale_lib');
		$this->lang->load('sales');
		$this->lang->load('module');
		$this->load->helper('order');
		$this->load->helper('items');
		$this->load->helper('sale');
		$this->load->model('Sale');
		$this->load->model('Customer');
		$this->load->model('Tier');
		$this->load->model('Category');
		$this->load->model('Giftcard');
		$this->load->model('Tag');
		$this->load->model('Item');
		$this->load->model('Item_location');
		$this->load->model('Item_kit_location');
		$this->load->model('Item_kit_location_taxes');
		$this->load->model('Item_kit');
		$this->load->model('Item_kit_items');
		$this->load->model('Item_kit_taxes');
		$this->load->model('Item_location_taxes');
		$this->load->model('Item_taxes');
		$this->load->model('Item_taxes_finder');
		$this->load->model('Item_kit_taxes_finder');
		$this->load->model('Appfile');
		$this->load->model('Item_serial_number');
		$this->load->model('Price_rule');
		$this->load->model('Shipping_provider');
		$this->load->model('Shipping_method');
		$this->lang->load('deliveries');
		
		cache_item_and_item_kit_cart_info($this->sale_lib->get_cart());
		
	}	
	
	function index($dont_switch_employee = 0)
	{
		if (count($this->sale_lib->get_cart()) > 0)
		{
			$dont_switch_employee = 1;
		}
		if($this->config->item('automatically_show_comments_on_receipt'))
		{
			$this->sale_lib->set_comment_on_receipt(1);
		}
		
		$location_id=$this->Employee->get_logged_in_employee_current_location_id();
		
		$register_count = $this->Register->count_all($location_id);
		
		if ($register_count > 0)
		{
			if ($register_count == 1)
			{
				$registers = $this->Register->get_all($location_id);
				$register = $registers->row_array();
			
				if (isset($register['register_id']))
				{
					$this->Employee->set_employee_current_register_id($register['register_id']);
				}
			}
		
			if (!$this->Employee->get_logged_in_employee_current_register_id())
			{
				$this->load->view('sales/choose_register');		
				return;
			}
		}
		
		if ($this->config->item('track_cash')) 
		{
			if ($this->input->post('opening_amount') != '' && !$this->Register->is_register_log_open())  
			{
				$now = date('Y-m-d H:i:s');

				$cash_register = new stdClass();
				$cash_register->register_id = $this->Employee->get_logged_in_employee_current_register_id();
				$cash_register->employee_id_open = $this->session->userdata('person_id');
				$cash_register->shift_start = $now;
				$cash_register->open_amount = $this->input->post('opening_amount');
				$cash_register->close_amount = 0;
				$cash_register->cash_sales_amount = 0;
				$this->Register->insert_register($cash_register);

				redirect(site_url('sales'));
			}
			else if ($this->Register->is_register_log_open()) 
			{
				$this->_reload(array('dont_switch_employee' => $dont_switch_employee), false);
			} 
			else 
			{
				$this->load->view('sales/opening_amount', array('previous_closing' => $this->Register->get_previous_closing_amount($this->Employee->get_logged_in_employee_current_register_id()),'denominations' => $this->Register->get_register_currency_denominations()->result_array()));
			}
		} 
		else 
		{			
			$this->_reload(array('dont_switch_employee' => $dont_switch_employee), false);
		}		
	}
	
	function choose_register($register_id)
	{
		if ($this->Register->exists($register_id))
		{
			$this->Employee->set_employee_current_register_id($register_id);
		}
		
		redirect(site_url('sales'));
		return;		
	}
	
	function clear_register()
	{
		//Clear out logged in register when we switch locations
		$this->Employee->set_employee_current_register_id(null);
		
		redirect(site_url('sales'));
		return;		
	}
	
	function register_add_subtract($mode,$return = 'sales')
	{
		$data = array();
		$data['mode'] = $mode;
		$data['return'] = $return;
		$cash_register = $this->Register->get_current_register_log();
		
		if (!$this->Register->is_register_log_open()) 
		{
			redirect(site_url('home'));
			return;
		}
		
		if ($this->input->post('amount') != '') 
		{
			$message = '';
			$amount = to_currency_no_money($this->input->post('amount'));
		
			if ($mode == 'add')
			{
				$cash_register->total_cash_additions+=$amount;
				$message = lang('sales_cash_successfully_added_to_drawer');
			}
			else
			{
				$cash_register->total_cash_subtractions+=$amount;
				$message = lang('sales_cash_successfully_removed_from_drawer');
			}
			$this->Register->update_register_log($cash_register);
						
						
			$employee_id_audit = $this->Employee->get_logged_in_employee_info()->person_id;
			$register_audit_log_data = array(
				'register_log_id'=> $cash_register->register_log_id,
				'employee_id'=> $employee_id_audit,
				'date' => date('Y-m-d H:i:s'),
				'amount' => $mode == 'add' ? $amount : -$amount,
				'note' => $this->input->post('note'),
			);
			
			$this->Register->insert_audit_log($register_audit_log_data);
			
			$this->session->set_flashdata('cash_drawer_add_subtract_message', $message);
			
			if ($return == 'sales')
			{
				redirect('sales');	
			}
			elseif ($return == 'closeregister')
			{
				redirect('sales/closeregister?continue=home');
			}
		} 
		else
		{
			
			if ($mode == 'add')
			{
				$data['amount'] = to_currency($cash_register->total_cash_additions);
			}
			else
			{
				$data['amount'] = to_currency($cash_register->total_cash_subtractions);
				
			}
			
			$this->load->view('sales/register_add_subtract', $data);
		}
		
	}
	
	function closeregister() 
	{
		if (!$this->Register->is_register_log_open()) 
		{
			redirect(site_url('home'));
			return;
		}
		$cash_register = $this->Register->get_current_register_log();
		$register_log_id = $cash_register->register_log_id;
		
		$continueUrl = $this->input->get('continue');
		if ($this->input->post('closing_amount') != '') {
			$now = date('Y-m-d H:i:s');
			$cash_register->register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$cash_register->employee_id_close = $this->session->userdata('person_id');
			$cash_register->shift_end = $now;
			$cash_register->close_amount = $this->input->post('closing_amount');
			$cash_register->cash_sales_amount = $this->Sale->get_cash_sales_total_for_shift($cash_register->shift_start, $cash_register->shift_end);			
			unset($cash_register->register_log_id);
			$cash_register->notes = $this->input->post('notes');
			$this->Register->update_register_log($cash_register);
			if ($continueUrl == 'logout') 
			{
				redirect(site_url('home/logout'));
			} 
			elseif($continueUrl == 'timeclocks')
			{
				redirect(site_url('timeclocks'));				
			}
			elseif($continueUrl == 'closeoutreceipt')
			{
				redirect(site_url("reports/register_log_details/$register_log_id"));
				
			}
			else
			{
				redirect(site_url('home'));
			}
		} 
		else
		{
			$cash_sales = $this->Sale->get_cash_sales_total_for_shift($cash_register->shift_start, date("Y-m-d H:i:s"));
			
			$this->load->view('sales/closing_amount', array(
				'continue'=>$continueUrl ? "?continue=$continueUrl" : '',
				'open_amount' => $cash_register->open_amount,
				'notes'=>'',
				'closeout'=>$cash_register->open_amount + $cash_sales + $cash_register->total_cash_additions - $cash_register->total_cash_subtractions,
				'cash_sales' => $cash_sales,
				'total_cash_additions' => $cash_register->total_cash_additions,
				'total_cash_subtractions' => $cash_register->total_cash_subtractions,
				'denominations' => $this->Register->get_register_currency_denominations()->result_array(),
				'register_log_id' => $register_log_id,
			));
		}
	}
	

	function edit_register($register_log_id) 
	{
		
		$cash_register = $this->Register->get_existing_register_log($register_log_id);

		$continueUrl = $this->input->get('continue');
		if ($this->input->post('closing_amount') != '') {

			$cash_register->close_amount = $this->input->post('closing_amount');
			$cash_register->open_amount = $this->input->post('opening_amount');

			unset($cash_register->register_id);
			$cash_register->notes = $this->input->post('notes');
			$this->Register->update_existing_register_log($cash_register,$register_log_id);

			redirect(site_url("reports/register_log_details/$register_log_id"));
				
		} 
		else
		{
	
			$this->load->view('sales/closing_amount', array(
				'continue'=>$continueUrl ? "?continue=$continueUrl" : '',
				'open_amount' => $cash_register->open_amount,
				'open_amount_editable' => true,
				'closeout'=>$cash_register->close_amount,
				'notes'=>$cash_register->notes,
				'cash_sales' => $cash_register->cash_sales_amount,
				'total_cash_additions' => $cash_register->total_cash_additions,
				'total_cash_subtractions' => $cash_register->total_cash_subtractions,
				'denominations' => $this->Register->get_register_currency_denominations()->result_array(),
				'register_log_id' => $register_log_id,
				'update' => true,
			));
		}
	}
	
	function item_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Item->get_item_search_suggestions($this->input->get('term'),'unit_price',100);
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions_sales_recv($this->input->get('term'),'unit_price', 100));
		echo json_encode($suggestions);
	}

	function customer_search()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->get('term'),100);
		echo json_encode($suggestions);
	}

	function select_customer()
	{
		$data = array();
		$customer_id = $this->input->post("customer");
			
		if ($this->Customer->account_number_exists($customer_id))
		{
			$customer_id = $this->Customer->customer_id_from_account_number($customer_id);
		}
		
		if ($this->Customer->exists($customer_id))
		{
			$customer_info=$this->Customer->get_info($customer_id);
		
			if ($customer_info->tier_id)
			{
				$this->sale_lib->set_selected_tier_id($customer_info->tier_id);
			}
			
			$this->sale_lib->set_customer($customer_id);
			if($this->config->item('automatically_email_receipt'))
			{
				$this->sale_lib->set_email_receipt(1);
			}
			
			if ($this->config->item('point_value'))
			{
				$max_points = min(floor($this->sale_lib->get_amount_due() / $this->config->item('point_value')), (int)$customer_info->points);
			
				if ($max_points > 0)
				{
					$data['number_of_points_to_use'] = $max_points;
				}
			}
		}
		else
		{
			$data['error']=lang('sales_unable_to_add_customer');
		}
		$this->sale_lib->clear_all_paid_store_account_sales();
		$this->_reload($data);
	}

	function change_mode($mode = false, $redirect = false)
	{
		$previous_mode = $this->sale_lib->get_mode();
		
		$mode = $mode === FALSE ? $this->input->post("mode") : $mode;
		
		if ($previous_mode == 'store_account_payment' && ($mode == 'sale' || $mode == 'return'))
		{
			$this->sale_lib->empty_cart();
		}
		
		$this->sale_lib->set_mode($mode);
		
		if ($mode == 'store_account_payment')
		{
			$store_account_payment_item_id = $this->Item->create_or_update_store_account_item();
			$this->sale_lib->empty_cart();
			$this->sale_lib->add_item($store_account_payment_item_id.'|FORCE_ITEM_ID|',1);
		}
		if ($redirect)
		{
			redirect('sales');
		}
		else
		{
			$data = array();
			if ($previous_mode == 'sale' && $mode =='return')
			{
				if ($this->sale_lib->can_convert_cart_from_sale_to_return())
				{
					$data  = array('prompt_convert_sale_to_return' => TRUE);
				}
				else
				{
					$data  = array('prompt_convert_sale_to_return' => FALSE);					
				}
			}
			elseif($previous_mode == 'return' && $mode =='sale')
			{
				if ($this->sale_lib->can_convert_cart_from_return_to_sale())
				{
					$data  = array('prompt_convert_return_to_sale' => TRUE);
				}
				else
				{
					$data  = array('prompt_convert_return_to_sale' => FALSE);					
				}				
			}
			$this->_reload($data);
		}
	}
	
	function convert_sale_to_return()
	{
		//do logic for making a sale a return
		$this->sale_lib->do_convert_cart_from_sale_to_return();
		
		$this->_reload();
	}
	
	function convert_return_to_sale()
	{
		//do logic for making a sale a return
		$this->sale_lib->do_convert_cart_from_return_to_sale();
		
		$this->_reload();		
	}
	
	function set_comment() 
	{
 	  $this->sale_lib->set_comment($this->input->post('comment'));
	}

	function set_selected_payment()
	{
		$this->sale_lib->set_selected_payment($this->input->post('payment'));
	}
	
	function set_change_sale_date() 
	{
 	  $this->sale_lib->set_change_sale_date($this->input->post('change_sale_date'));
	}
	
	function set_change_sale_date_enable() 
	{
 	  $this->sale_lib->set_change_sale_date_enable($this->input->post('change_sale_date_enable'));
	  if (!$this->sale_lib->get_change_sale_date())
	  {
	 	  $this->sale_lib->set_change_sale_date(date(get_date_format()));
	  }
	}
	
	function set_comment_on_receipt() 
	{
 	  $this->sale_lib->set_comment_on_receipt($this->input->post('show_comment_on_receipt'));
	}
	
	function set_email_receipt()
	{
 	  $this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	function set_save_credit_card_info() 
	{
 	  $this->sale_lib->set_save_credit_card_info($this->input->post('save_credit_card_info'));
	}
	
	function set_use_saved_cc_info()
	{
 	  $this->sale_lib->set_use_saved_cc_info($this->input->post('use_saved_cc_info'));
	}
	
	function set_prompt_for_card()
	{
 	  $this->sale_lib->set_prompt_for_card($this->input->post('prompt_for_card'));
	}
	
	function set_tier_id() 
	{
	  $data = array();
		
 	  $this->sale_lib->set_selected_tier_id($this->input->post('tier_id'));
	  
		$items = $this->sale_lib->get_cart();
	  foreach(array_keys($items) as $line)
	  {
	  	  if ($this->sale_lib->below_cost_price_item($line))
	  	  { 
				  if ($this->config->item('do_not_allow_below_cost'))
				  {
					  $this->sale_lib->set_selected_tier_id(0);
					  $data['error'] = lang('sales_selling_item_below_cost');
				  }
				  else
				  {
					  $data['warning'] = lang('sales_selling_item_below_cost');
				  }
					
			  	$this->_reload($data);
			  	return;
		  	}
				
				//Mike
				$this->sale_lib->edit_item($line);
				
	  }
		
	  
	  $this->_reload($data);
	  
	}

	function set_sold_by_employee_id() 
	{
 	  $this->sale_lib->set_sold_by_employee_id($this->input->post('sold_by_employee_id') ? $this->input->post('sold_by_employee_id') : NULL);
	}

	function payment_check($amount)
	{
		return $amount != '0' || $this->sale_lib->get_total() == 0;
	}
	
	function search_coupons()
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$suggestions = $this->Price_rule->search_coupons($this->input->get('term'));
		$result = array();
				
		foreach ($suggestions as $k=>$v) 
		{
			if(!empty($v['coupon_code']))
			{
				$result[$k] = array('value' => $v['value'], 'label'=> $v['label'] . ' - ' . $v['coupon_code']); 
			}
		}		
		
		echo json_encode($result);
	}
	
	function set_coupons()
	{
		$data = array();
		
		$this->sale_lib->set_coupons($this->input->post('coupons'));
				
		$this->_reload($data);
	}

	//Alain Multiple Payments
	function add_payment()
	{		
		$data=array();
		$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'required|callback_payment_check');
		
		if ($this->form_validation->run() == FALSE)
		{
			if ( $this->input->post('payment_type') == lang('common_giftcard') )
			{
				$data['error']=lang('sales_must_enter_numeric_giftcard');				
			}
			elseif($this->input->post('amount_tendered') == '0' && $this->sale_lib->get_total() != 0)
			{
				$data['error']=lang('common_cannot_add_zero_payment');				
			}
			else
			{
				$data['error']=lang('common_must_enter_numeric');				
			}
				
 			$this->_reload($data);
 			return;
		}
		
		if (($this->input->post('payment_type') == lang('common_store_account') && $this->sale_lib->get_customer() == -1) ||
			($this->sale_lib->get_mode() == 'store_account_payment' && $this->sale_lib->get_customer() == -1)
			) 
		{
				$data['error']=lang('sales_customer_required_store_account');
				$this->_reload($data);
				return;
		}
		
		$store_account_payment_amount = $this->sale_lib->get_total();

		if ($this->sale_lib->get_mode() == 'store_account_payment'  && $store_account_payment_amount == 0) 
		{
          $data['error']=lang('common_store_account_payment_item_must_not_be_0');
          $this->_reload($data);
          return;
		}
			
		$this->load->helper('sale');
		if((is_sale_integrated_cc_processing() && $this->input->post('payment_type') ==lang('common_credit')) || is_sale_integrated_ebt_sale())
		{
			$data['error']=lang('sales_process_card_first');
			$this->_reload($data);
			return;
		}
		
		if((is_sale_integrated_ebt_sale() && ($this->input->post('payment_type') == lang('common_ebt') ||  $this->input->post('payment_type') == lang('common_ebt_cash'))) || is_sale_integrated_cc_processing())
		{
			$data['error']=lang('sales_process_card_first');
			$this->_reload($data);
			return;
		}
		
		if(($this->input->post('payment_type') == lang('common_ebt') && ($this->input->post('amount_tendered') + $this->sale_lib->get_payment_amount(lang('common_wic'))+ $this->sale_lib->get_payment_amount(lang('common_ebt'))) > $this->sale_lib->get_ebt_total_amount_to_charge()) || ($this->input->post('payment_type') == lang('common_wic') && ($this->input->post('amount_tendered') +  $this->sale_lib->get_payment_amount(lang('common_ebt')) + $this->sale_lib->get_payment_amount(lang('common_wic'))) > $this->sale_lib->get_ebt_total_amount_to_charge()))
		{
			$data['error']=lang('sales_ebt_too_high');
			$this->_reload($data);
			return;
		}
		
		if ($this->config->item('select_sales_person_during_sale') && !$this->sale_lib->get_sold_by_employee_id())
		{
			$data['error']=lang('sales_must_select_sales_person');
			$this->_reload($data);
			return;			
		}
		
				
		$payment_type=$this->input->post('payment_type');

		if ( $payment_type == lang('common_points') )
		{
			$customer_info = $this->Customer->get_info($this->sale_lib->get_customer());
			if ($this->input->post('amount_tendered') > to_currency_no_money($customer_info->points) || $this->input->post('amount_tendered') <=0 || $this->sale_lib->get_amount_due() <= 0)
			{
				$data['error']=lang('sales_points_to_much');
				$this->_reload($data);
				return;
				
			}
			
			$max_points = floor($this->sale_lib->get_amount_due() / $this->config->item('point_value'));
			$payment_amount = min($max_points * $this->config->item('point_value'), to_currency_no_money($this->input->post('amount_tendered') * $this->config->item('point_value')), $this->sale_lib->get_amount_due());
		}
		elseif ( $payment_type == lang('common_giftcard') )
		{
			if(!$this->Giftcard->exists($this->Giftcard->get_giftcard_id($this->input->post('amount_tendered'))) || $this->Giftcard->is_inactive($this->Giftcard->get_giftcard_id($this->input->post('amount_tendered'))))
			{
				$data['error']=lang('sales_giftcard_does_not_exist');
				$this->_reload($data);
				return;
			}
			
			$payment_type=$this->input->post('payment_type').':'.$this->input->post('amount_tendered');
			$current_payments_with_giftcard = $this->sale_lib->get_payment_amount($payment_type);
			$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $this->input->post('amount_tendered') ) - $current_payments_with_giftcard;
			if ( $cur_giftcard_value <= 0 && $this->sale_lib->get_total() > 0)
			{
				$data['error']=lang('sales_giftcard_balance_is').' '.to_currency( $this->Giftcard->get_giftcard_value( $this->input->post('amount_tendered') ) ).' !';
				$this->_reload($data);
				return;
			}
			elseif ( ( $this->Giftcard->get_giftcard_value( $this->input->post('amount_tendered') ) - $this->sale_lib->get_total() ) > 0 )
			{
				$data['warning']=lang('sales_giftcard_balance_is').' '.to_currency( $this->Giftcard->get_giftcard_value( $this->input->post('amount_tendered') ) - $this->sale_lib->get_total() ).' !';
			}
			$payment_amount=min( $this->sale_lib->get_amount_due(), $this->Giftcard->get_giftcard_value( $this->input->post('amount_tendered') ) );
		}
		else
		{
			$payment_amount=$this->input->post('amount_tendered');
		}
		
		if (!$this->sale_lib->validate_payment($payment_type,$payment_amount))
		{
			$data['error']=lang('common_unable_to_add_payment');
  		$this->_reload($data);
  		return;
		}	
		
		if( !$this->sale_lib->add_payment( $payment_type, $payment_amount))
		{
			$data['error']=lang('common_unable_to_add_payment');
		}
		
		$this->_reload($data);
	}

	//Alain Multiple Payments
	function delete_payment($payment_id)
	{
		$this->sale_lib->delete_payment($payment_id);
		$this->_reload();
	}

	function add()
	{		
		$data=array();
		$this->sale_lib->set_view_data($data);
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post("item");
		
		if ($this->input->post('quantity'))
		{
			$quantity = $this->input->post('quantity');
		}
		else
		{
			$quantity = $mode=="sale" ? 1:-1;
		}
		
		$this->load->model('Item_serial_number');
		
		$serialnumber = $this->Item_serial_number->get_item_id($item_id_or_number_or_item_kit_or_receipt)!== FALSE ? $item_id_or_number_or_item_kit_or_receipt : NULL;
		if ($serialnumber)
		{
			$serial_number_price = $this->Item_serial_number->get_price_for_serial($serialnumber);
		}
		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->sale_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			if($this->Item_kit->get_info(str_replace(array('kit ','KIT '),'',$item_id_or_number_or_item_kit_or_receipt))->deleted || $this->Item_kit->get_info($this->Item_kit->get_item_kit_id(str_replace(array('kit ','KIT '),'',$item_id_or_number_or_item_kit_or_receipt)))->deleted)
			{
				$data['error']=lang('sales_unable_to_add_item');			
			}
			else
			{
				if (!$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt, $quantity))
				{
					$out_of_stock_check = FALSE;
			
					if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
					{
						if (strpos(strtolower($item_id_or_number_or_item_kit_or_receipt), 'kit') !== FALSE)
						{
							//KIT #
							$pieces = explode(' ',$item_id_or_number_or_item_kit_or_receipt);
							$current_item_kit_id = (int)$pieces[1];	
						}
						else
						{
							$current_item_kit_id = $this->Item_kit->get_item_kit_id($item_id_or_number_or_item_kit_or_receipt);
						}
						
						if ($current_item_kit_id !== FALSE && $this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, $quantity))
						{
							$out_of_stock_check = TRUE;
						}
						
						
					}
			
					if ($out_of_stock_check)
					{
						$data['error']=lang('sales_unable_to_add_item_out_of_stock');
					}
					else
					{
						$data['error']=lang('sales_unable_to_add_item');
					}
				}
				else
				{
					//As surely a Kit item , do out of stock check
					$item_kit_id = $this->sale_lib->get_valid_item_kit_id($item_id_or_number_or_item_kit_or_receipt);

					if($this->sale_lib->out_of_stock_kit($item_kit_id))
					{
						$data['warning'] = lang('sales_quantity_less_than_zero');
					}
					else
					{
						$data['success']= TRUE;
						$data['success_no_message']= TRUE;	
					}
				}
				//Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
			}	
		}
		elseif($this->Item->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item->get_info($this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt))->deleted || !$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity,$discount=0,isset($serial_number_price) && $serial_number_price ? $serial_number_price: null ,null, null, null,$serialnumber))
		{
			$out_of_stock_check = FALSE;
			
			if (!($this->Item->get_info($item_id_or_number_or_item_kit_or_receipt)->deleted || $this->Item->get_info($this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt))->deleted) && $this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				//make sure item exists
				if(!$this->Item->exists(does_contain_only_digits($item_id_or_number_or_item_kit_or_receipt) ? (int)$item_id_or_number_or_item_kit_or_receipt : -1))	
				{
					//try to get item id given an item_number
					$current_item_id = $this->Item->get_item_id($item_id_or_number_or_item_kit_or_receipt);
				}
				else
				{
					$current_item_id = (int)$item_id_or_number_or_item_kit_or_receipt;
				}
				
				if ($current_item_id !== FALSE && $this->sale_lib->will_be_out_of_stock($current_item_id, $quantity))
				{
					$out_of_stock_check = TRUE;
				}				
			}
			
			if ($out_of_stock_check)
			{
				$data['error']=lang('sales_unable_to_add_item_out_of_stock');
			}
			else
			{
				if ($this->config->item('enable_scale') && $this->sale_lib->add_scale_item($item_id_or_number_or_item_kit_or_receipt))
				{
					$data['success']= TRUE;
					$data['success_no_message']= TRUE;
				}
				else
				{
					$data['error']=lang('sales_unable_to_add_item');
				}
			}
		}
		else
		{
			$data['success']= TRUE;
			$data['success_no_message']= TRUE;
		}
		if(!$this->config->item('do_not_allow_out_of_stock_items_to_be_sold') && $this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt))
		{
			$data['warning'] = lang('sales_quantity_less_than_zero');
		}		
		
		//Not doing check for price being less than cost price for performace reasons AND a user should know its below cost price
		
		if ($this->_is_tax_inclusive() && count($this->sale_lib->get_deleted_taxes()) > 0)
		{
			$data['warning'] = lang('sales_cannot_delete_taxes_if_using_tax_inclusive_items');
		}
				
		if ($this->config->item('edit_item_price_if_zero_after_adding'))
		{
			$last_item_price = $this->sale_lib->get_last_item_added_price();
			if ($last_item_price == 0 && $last_item_price !== FALSE)
			{
				$data['price_zero'] = TRUE;
			}
		}
		
		//We were able to add; now check if the last $line is below cost price
		if (isset($data['success']) && $data['success'])
		{
		  $line = $this->sale_lib->get_last_item_line();
			
 	  	  if ($this->sale_lib->below_cost_price_item($line))
 	  	  { 
 			  if ($this->config->item('do_not_allow_below_cost'))
 			  {
				  $this->sale_lib->delete_item($line);
 				  $data['error'] = lang('sales_selling_item_below_cost');
				  $data['success'] = FALSE;
 			  }
 			  else
 			  {
 				  $data['warning'] = lang('sales_selling_item_below_cost');
 			  }
 		  }
		}

		$this->_reload($data);
	}
	
	function _is_tax_inclusive()
	{
		$is_tax_inclusive = FALSE;
		foreach($this->sale_lib->get_cart() as $item)
		{
			if (isset($item['item_id']))
			{
				$cur_item_info = $this->Item->get_info($item['item_id']);
				if ($cur_item_info->tax_included)
				{
					$is_tax_inclusive = TRUE;
					break;
				}
			}
			else //item kit
			{
				$cur_item_kit_info = $this->Item_kit->get_info($item['item_kit_id']);
				
				if ($cur_item_kit_info->tax_included)
				{
					$is_tax_inclusive = TRUE;
					break;
				}
				
			}
		}
		
		return $is_tax_inclusive;		
	}

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('cost_price', 'lang:common_price', 'numeric');
		$this->form_validation->set_rules('quantity', 'lang:common_quantity', 'numeric');
		$this->form_validation->set_rules('discount', 'lang:common_discount_percent', 'numeric');
		
		if($this->input->post("name"))
		{
			$variable = $this->input->post("name");
			$$variable = $this->input->post("value");
		}
		
		if (isset($discount) && $discount !== NULL)
		{
			if($discount == '')
			{
				$discount = 0;
			}
			
			$max_discount = $this->sale_lib->get_max_discount_for_item($line);
			
			if($max_discount!==NULL && floatval($discount) > floatval($max_discount))
			{
				$discount = $max_discount;
				$data['warning'] = lang('sales_could_not_discount_item_above_max')." ".to_percent($max_discount);	
			}
				
		}

		$can_edit = TRUE;
	
		if ($this->form_validation->run() != FALSE)
		{			
			if ($this->config->item('do_not_allow_out_of_stock_items_to_be_sold'))
			{
				if (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'item')
				{
					$current_item_id = $this->sale_lib->get_item_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
					
					if ($this->sale_lib->will_be_out_of_stock($current_item_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
				elseif (isset($quantity) && $this->sale_lib->is_kit_or_item($line) == 'kit')
				{
					$current_item_kit_id = $this->sale_lib->get_kit_id($line);
					$before_quantity = $this->sale_lib->get_quantity_at_line($line);
					
					if ($this->sale_lib->will_be_out_of_stock_kit($current_item_kit_id, isset($quantity) ? $quantity - $before_quantity : 0))
					{
						$can_edit = FALSE;
					}
				}
				
				if (!$can_edit)
				{
					$data['error']=lang('sales_unable_to_add_item_out_of_stock');
				}
			}			
		}
		else
		{
			$can_edit = FALSE;
			$data['error']=lang('sales_error_editing_item');
		}
		
		if($can_edit && isset($price))
		{			
			$max = $this->sale_lib->get_max_edit_price_for_item($line);
			$min = $this->sale_lib->get_min_edit_price_for_item($line);
			
			if(isset($min) && floatval($price) < floatval($min))
			{
				$price = $min;
				$data['warning'] = lang('sales_could_not_set_item_price_bellow_min')." ".to_currency($min);
			}
			
			if(isset($max) && floatval($price) > floatval($max))
			{
				$price = $max;
				$data['warning'] = lang('sales_could_not_set_item_price_above_max')." ".to_currency($max);
			}
		}
			
		if($this->sale_lib->is_kit_or_item($line) == 'item')
		{
			if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
			{
				$data['warning'] = lang('sales_quantity_less_than_zero');
			}
			
			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
			}
		}
		elseif($this->sale_lib->is_kit_or_item($line) == 'kit')
		{
		    if($this->sale_lib->out_of_stock_kit($this->sale_lib->get_kit_id($line)))
		    {
			    $data['warning'] = lang('sales_quantity_less_than_zero');
		    }
			 
 			if ($this->sale_lib->below_cost_price_item($line, isset($price) ? $price : NULL, isset($discount) ? $discount : NULL, isset($cost_price)  ? $cost_price : NULL))
 			{
				if ($this->config->item('do_not_allow_below_cost'))
				{
					$can_edit = FALSE;
					$data['error'] = lang('sales_selling_item_below_cost');
				}
				else
				{
					$data['warning'] = lang('sales_selling_item_below_cost');
				}
 			}
		}
		
		if ($can_edit)
		{
			$this->sale_lib->edit_item($line,isset($description) ? $description : NULL,isset($serialnumber) ? $serialnumber : NULL, isset($quantity) ? $quantity : NULL,isset($discount) ? $discount : NULL,isset($price) ? $price: NULL,isset($cost_price) ? $cost_price: NULL);
		}
		
		$this->_reload($data);
	}

	function delete_item($item_line)
	{	
		if($this->sale_lib->get_line_for_delivery_item() == $item_line)
		{
			$this->sale_lib->clear_delivery();
		}
		
		$this->sale_lib->delete_item($item_line, true);
		
		if (count($this->sale_lib->get_cart()) == 0)
		{
			$this->sale_lib->clear_all();
		}
		
		
		$this->_reload();
	}

	function delete_customer()
	{
		$this->sale_lib->delete_customer();
   	$this->sale_lib->set_selected_tier_id(0);
		$this->sale_lib->clear_all_paid_store_account_sales();
		$this->_reload();
	}
	
	function _get_cc_processor()
	{
		if (!$this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			return false;
		}
										
		//If we have setup Mercury....or if it is not set then default to Mercury
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$registers = $this->Register->get_all();
			$register = $registers->row_array();
		
			if (!$this->Employee->get_logged_in_employee_current_register_id() && isset($register['register_id']))
			{
				$this->Employee->set_employee_current_register_id($register['register_id']);
			}
			
			$current_register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$register_info = $this->Register->get_info($current_register_id);
			
			//IP Tran; supports all platforms
			if($register_info->iptran_device_id)
			{
				require_once(APPPATH.'libraries/Mercuryemvtranscloudprocessor.php');
				$credit_card_processor = new Mercuryemvtranscloudprocessor($this);
				return $credit_card_processor;
			}
			
			//Mobile always uses hosted checkout as we do NOT have mobile support for EMV
			if ($this->agent->is_mobile())
			{
				require_once (APPPATH.'libraries/Mercuryhostedcheckoutprocessor.php');
				$credit_card_processor = new Mercuryhostedcheckoutprocessor($this);	
				return $credit_card_processor;
			}
		
			//EMV
			if ($this->Location->get_info_for_key('emv_merchant_id') && $this->Location->get_info_for_key('com_port') && $this->Location->get_info_for_key('listener_port'))
			{
				require_once (APPPATH.'libraries/Mercuryemvusbprocessor.php');
				$credit_card_processor = new Mercuryemvusbprocessor($this);
				return $credit_card_processor;
			}
			else //Default hosted checkout
			{
				require_once (APPPATH.'libraries/Mercuryhostedcheckoutprocessor.php');
				$credit_card_processor = new Mercuryhostedcheckoutprocessor($this);
				return $credit_card_processor;
			}
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'heartland')
		{
			$registers = $this->Register->get_all();
			$register = $registers->row_array();
		
			if (!$this->Employee->get_logged_in_employee_current_register_id() && isset($register['register_id']))
			{
				$this->Employee->set_employee_current_register_id($register['register_id']);
			}
			
			$current_register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$register_info = $this->Register->get_info($current_register_id);
			
			//IP Tran; supports all platforms
			if($register_info->iptran_device_id)
			{
				require_once(APPPATH.'libraries/Heartlandemvtranscloudprocessor.php');
				$credit_card_processor = new Heartlandemvtranscloudprocessor($this);
				return $credit_card_processor;
			}
			
			require_once (APPPATH.'libraries/Heartlandemvusbprocessor.php');
			$credit_card_processor = new Heartlandemvusbprocessor($this);
			return $credit_card_processor;			
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'evo')
		{
			$registers = $this->Register->get_all();
			$register = $registers->row_array();
		
			if (!$this->Employee->get_logged_in_employee_current_register_id() && isset($register['register_id']))
			{
				$this->Employee->set_employee_current_register_id($register['register_id']);
			}
			
			$current_register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$register_info = $this->Register->get_info($current_register_id);
			
			require_once (APPPATH.'libraries/Evoemvusbprocessor.php');
			$credit_card_processor = new Evoemvusbprocessor($this);
			return $credit_card_processor;			
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'worldpay')
		{
			$registers = $this->Register->get_all();
			$register = $registers->row_array();
		
			if (!$this->Employee->get_logged_in_employee_current_register_id() && isset($register['register_id']))
			{
				$this->Employee->set_employee_current_register_id($register['register_id']);
			}
			
			$current_register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$register_info = $this->Register->get_info($current_register_id);
			
			require_once (APPPATH.'libraries/Worldpayemvusbprocessor.php');
			$credit_card_processor = new Worldpayemvusbprocessor($this);
			return $credit_card_processor;			
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'firstdata')
		{
			$registers = $this->Register->get_all();
			$register = $registers->row_array();
		
			if (!$this->Employee->get_logged_in_employee_current_register_id() && isset($register['register_id']))
			{
				$this->Employee->set_employee_current_register_id($register['register_id']);
			}
			
			$current_register_id = $this->Employee->get_logged_in_employee_current_register_id();
			$register_info = $this->Register->get_info($current_register_id);
			
			require_once (APPPATH.'libraries/Firstdataemvusbprocessor.php');
			$credit_card_processor = new Firstdataemvusbprocessor($this);
			return $credit_card_processor;			
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			require_once (APPPATH.'libraries/Stripeprocessor.php');
			$credit_card_processor = new Stripeprocessor($this);
			return $credit_card_processor;
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			require_once (APPPATH.'libraries/Braintreeprocessor.php');
			$credit_card_processor = new Braintreeprocessor($this);
			return $credit_card_processor;
		}
		elseif ($this->Location->get_info_for_key('credit_card_processor') == 'other_usb')
		{
			require_once (APPPATH.'libraries/Otheremvusbprocessor.php');
			$credit_card_processor = new Otheremvusbprocessor($this);
			return $credit_card_processor;
		}
		return false;
	}
		
	function start_cc_processing()
	{
		if ($this->config->item('test_mode'))
		{
			$this->_reload(array('error' => lang('common_in_test_mode')), false);
			return;
		}
		
		$cc_amount = round($this->sale_lib->get_payment_amount(lang('common_credit')),2);
		$total = round($this->sale_lib->get_total(),2);		
		
		if ($total >=0 && $cc_amount > $total)
		{
			$this->_reload(array('error' => lang('sales_credit_card_payment_is_greater_than_total_cannot_complete')), false);
		}
		elseif ($total < 0 && $cc_amount < $total)
		{
			$this->_reload(array('error' => lang('sales_cannot_refund_more_than_sale_total')), false);
		}
		else
		{
			$credit_card_processor = $this->_get_cc_processor();
			
			if ($credit_card_processor)
			{
				$credit_card_processor->start_cc_processing();
			}
			else
			{
				$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
				return;
			}
		}		
	}
	
	function start_cc_processing_trans_cloud()
	{
		$credit_card_processor = $this->_get_cc_processor();
		
		if ($credit_card_processor)
		{
			$credit_card_processor->do_start_cc_processing();
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}
	
	function finish_cc_processing()
	{
		$credit_card_processor = $this->_get_cc_processor();
		if ($credit_card_processor)
		{
			$credit_card_processor->finish_cc_processing();			
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}
	
	function finish_cc_processing_saved_card()
	{
		$credit_card_processor = $this->_get_cc_processor();
	
		if ($credit_card_processor)
		{
			$credit_card_processor->finish_cc_processing_saved_card();
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}
	
	function get_emv_ebt_balance($type="EBT")
	{
		$credit_card_processor = $this->_get_cc_processor();
		if ($credit_card_processor && method_exists($credit_card_processor,'get_emv_ebt_balance'))
		{
			$credit_card_processor->get_emv_ebt_balance($type);
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
		
	}
	function reset_pin_pad()
	{
		$credit_card_processor = $this->_get_cc_processor();
	
		if ($credit_card_processor && method_exists($credit_card_processor,'pad_reset'))
		{
			$credit_card_processor->pad_reset();
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
		
	}
	
	function cancel_cc_processing()
	{
		$credit_card_processor = $this->_get_cc_processor();
		
		if ($credit_card_processor)
		{
			$credit_card_processor->cancel_cc_processing();
		}
		else
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
	}
	
	function set_sequence_no_emv()
	{
		if ($this->input->post('sequence_no'))
		{
			$this->session->set_userdata('sequence_no',$this->input->post('sequence_no'));
		}
	}
	
	function declined()
	{
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		
		$data['is_sale'] = FALSE;
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['transaction_time'] = date(get_date_format().' '.get_time_format());
		$data['payments']=$this->sale_lib->get_payments();
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $employee_id ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
		}
		
		$data['auth_code'] = $this->session->userdata('auth_code') ? $this->session->userdata('auth_code') : '';
		$data['ref_no'] = $this->session->userdata('ref_no') ? $this->session->userdata('ref_no') : '';
		$data['entry_method'] = $this->session->userdata('entry_method') ? $this->session->userdata('entry_method') : '';
		$data['aid'] = $this->session->userdata('aid') ? $this->session->userdata('aid') : '';
		$data['tvr'] = $this->session->userdata('tvr') ? $this->session->userdata('tvr') : '';
		$data['iad'] = $this->session->userdata('iad') ? $this->session->userdata('iad') : '';
		$data['tsi'] = $this->session->userdata('tsi') ? $this->session->userdata('tsi') : '';
		$data['arc'] = $this->session->userdata('arc') ? $this->session->userdata('arc') : '';
		$data['cvm'] = $this->session->userdata('cvm') ? $this->session->userdata('cvm') : '';
		$data['tran_type'] = $this->session->userdata('tran_type') ? $this->session->userdata('tran_type') : '';
		$data['application_label'] = $this->session->userdata('application_label') ? $this->session->userdata('application_label') : '';
		$data['masked_account'] = $this->session->userdata('masked_account') ? $this->session->userdata('masked_account') : '';
		$data['text_response'] = $this->session->userdata('text_response') ? $this->session->userdata('text_response') : '';
		$this->sale_lib->clear_cc_info();
		$this->load->view("sales/receipt_decline",$data);
	}
	
	function complete()
	{
		$this->load->helper('sale');
		$this->lang->load('deliveries');
		
		
		///Make sure we have actually processed a transaction before compelting sale
		if (is_sale_integrated_cc_processing() && !$this->session->userdata('CC_SUCCESS'))
		{
			$this->_reload(array('error' => lang('sales_credit_card_processing_is_down')), false);
			return;
		}
		
		$data['is_sale'] = TRUE;
		$data['cart']=$this->sale_lib->get_cart();
		
		if (empty($data['cart']))
		{
			redirect('sales');
		}
			
		if (!$this->_payments_cover_total())
		{
			$this->_reload(array('error' => lang('sales_cannot_complete_sale_as_payments_do_not_cover_total')), false);
			return;
		}
		
		$tier_id = $this->sale_lib->get_selected_tier_id();
		$tier_info = $this->Tier->get_info($tier_id);
		$exchange_rate = $this->sale_lib->get_exchange_rate() ? $this->sale_lib->get_exchange_rate() : 1;
		$data['exchange_rate'] = $exchange_rate;
		$data['exchange_name'] = $this->sale_lib->get_exchange_name();
		$data['exchange_symbol'] = $this->sale_lib->get_exchange_currency_symbol();
		$data['exchange_symbol_location'] = $this->sale_lib->get_exchange_currency_symbol_location();
		$data['exchange_number_of_decimals'] = $this->sale_lib->get_exchange_currency_number_of_decimals();
		$data['exchange_thousands_separator'] = $this->sale_lib->get_exchange_currency_thousands_separator();
		$data['exchange_decimal_point'] = $this->sale_lib->get_exchange_currency_decimal_point();
		
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($this->Employee->get_logged_in_employee_current_register_id());
		
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();		
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$data['comment'] = $this->sale_lib->get_comment();
		$data['discount_reason'] = $this->sale_lib->get_discount_reason();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		
		$data['balance'] = 0;
		//Add up balances for all languages
		foreach($store_account_in_all_languages as $store_account_lang)
		{
				//Thanks Mike for math help on how to convert exchange rate back to get correct balance
				$data['balance']+= $this->sale_lib->get_payment_amount($store_account_lang)*pow($exchange_rate,-1);
		}

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $employee_id ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = '';
		$data['auth_code'] = '';
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		
		$masked_account = $this->session->userdata('masked_account') ? $this->session->userdata('masked_account') : '';
		$card_issuer = $this->session->userdata('card_issuer') ? $this->session->userdata('card_issuer') : '';
		$auth_code = $this->session->userdata('auth_code') ? $this->session->userdata('auth_code') : '';
		$ref_no = $this->session->userdata('ref_no') ? $this->session->userdata('ref_no') : '';
		$cc_token = $this->session->userdata('cc_token') ? $this->session->userdata('cc_token') : '';
		$acq_ref_data = $this->session->userdata('acq_ref_data') ? $this->session->userdata('acq_ref_data') : '';
		$process_data = $this->session->userdata('process_data') ? $this->session->userdata('process_data') : '';
		$entry_method = $this->session->userdata('entry_method') ? $this->session->userdata('entry_method') : '';
		$aid = $this->session->userdata('aid') ? $this->session->userdata('aid') : '';
		$tvr = $this->session->userdata('tvr') ? $this->session->userdata('tvr') : '';
		$iad = $this->session->userdata('iad') ? $this->session->userdata('iad') : '';
		$tsi = $this->session->userdata('tsi') ? $this->session->userdata('tsi') : '';
		$arc = $this->session->userdata('arc') ? $this->session->userdata('arc') : '';
		$cvm = $this->session->userdata('cvm') ? $this->session->userdata('cvm') : '';
		$tran_type = $this->session->userdata('tran_type') ? $this->session->userdata('tran_type') : '';
		$application_label = $this->session->userdata('application_label') ? $this->session->userdata('application_label') : '';
		
		if ($ref_no)
		{
			if (count($this->sale_lib->get_payment_ids(lang('common_credit'))) || count($this->sale_lib->get_payment_ids(lang('common_ebt'))) || count($this->sale_lib->get_payment_ids(lang('common_ebt_cash'))))
			{
				$cc_payment_id = current($this->sale_lib->get_payment_ids(lang('common_credit')));
				if ($cc_payment_id !== FALSE)
				{
					$cc_payment = $data['payments'][$cc_payment_id];
					$this->sale_lib->edit_payment($cc_payment_id, $cc_payment['payment_type'], $cc_payment['payment_amount'],$cc_payment['payment_date'], $masked_account, $card_issuer,$auth_code, $ref_no, $cc_token, $acq_ref_data, $process_data, $entry_method, $aid,$tvr,$iad, $tsi,$arc,$cvm,$tran_type,$application_label);
				}
				
				$ebt_payment_id = current($this->sale_lib->get_payment_ids(lang('common_ebt')));
				if ($ebt_payment_id !== FALSE)
				{
					$ebt_payment = $data['payments'][$ebt_payment_id];
					
					$ebt_voucher_no = $this->sale_lib->get_ebt_voucher_no();
					$ebt_auth_code = $this->sale_lib->get_ebt_auth_code();
						
					$this->sale_lib->edit_payment($ebt_payment_id, $ebt_payment['payment_type'], $ebt_payment['payment_amount'],$ebt_payment['payment_date'], $masked_account, $card_issuer,$auth_code, $ref_no, $cc_token, $acq_ref_data, $process_data, $entry_method, $aid,$tvr,$iad, $tsi,$arc,$cvm,$tran_type,$application_label,$ebt_voucher_no,$ebt_auth_code);
					
					$data['ebt_balance'] = $this->session->userdata('ebt_balance');
					
				}
				
				$ebt_cash_payment_id = current($this->sale_lib->get_payment_ids(lang('common_ebt_cash')));
				if ($ebt_cash_payment_id !== FALSE)
				{
					$ebt_cash_payment = $data['payments'][$ebt_cash_payment_id];
					$this->sale_lib->edit_payment($ebt_cash_payment_id, $ebt_cash_payment['payment_type'], $ebt_cash_payment['payment_amount'],$ebt_cash_payment['payment_date'], $masked_account, $card_issuer,$auth_code, $ref_no, $cc_token, $acq_ref_data, $process_data, $entry_method, $aid,$tvr,$iad, $tsi,$arc,$cvm,$tran_type,$application_label);
					
					$data['ebt_balance'] = $this->session->userdata('ebt_balance');
					
				}
				
				//Make sure our payments has the latest change to masked_account
				$data['payments'] = $this->sale_lib->get_payments();
			}
		}
		
		
		$old_date = $this->sale_lib->get_change_sale_id()  ? $this->Sale->get_info($this->sale_lib->get_change_sale_id())->row_array() : false;
		$old_date=  $old_date ? date(get_date_format().' '.get_time_format(), strtotime($old_date['sale_time'])) : date(get_date_format().' '.get_time_format());
		$data['transaction_time']= $this->sale_lib->get_change_sale_date_enable() ?  date(get_date_format().' '.get_time_format(), strtotime($this->sale_lib->get_change_sale_date())) : $old_date;
	
		
		$suspended_change_sale_id=$this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : $this->sale_lib->get_change_sale_id() ;
				
		$data['store_account_payment'] = $this->sale_lib->get_mode() == 'store_account_payment' ? 1 : 0;
		//If we have a suspended sale, update the date for the sale
		
		$data['change_sale_date'] = FALSE;
		
		if ($this->sale_lib->get_suspended_sale_id() && $this->config->item('change_sale_date_when_completing_suspended_sale'))
		{
			$data['change_sale_date'] = date('Y-m-d H:i:s');
		}

		if ($this->sale_lib->get_change_sale_date_enable())
		{
			$data['change_sale_date'] = $this->sale_lib->get_change_sale_date();			
		}
				
		//SAVE sale to database
		$sale_id_raw = $this->Sale->save($data['cart'], $customer_id, $employee_id, $sold_by_employee_id, $data['comment'],$data['discount_reason'],$data['show_comment_on_receipt'],$this->sale_lib->get_coupons(),$data['payments'], $suspended_change_sale_id, 0, $data['change_sale_date'], $data['balance'], $data['store_account_payment']); 
		
		//Set exchange details in so receipt has correct info on it (Sale->save clears it out but we need for receipt)
		if ($data['exchange_name'])
		{
			$this->sale_lib->set_exchange_details($this->Sale->get_exchange_details($sale_id_raw));
		}
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id_raw;
		$data['sale_id_raw']=$sale_id_raw;
		
		$data['disable_loyalty'] = 0;
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;			
			$data['customer_points'] = $cust_info->points;			
		  $data['sales_until_discount'] = ($this->config->item('number_of_sales_for_discount') ? $this->config->item('number_of_sales_for_discount') : 0) - $cust_info->current_sales_for_discount;
 			$data['disable_loyalty'] = $cust_info->disable_loyalty;
		}
		
		$this->Register_cart->add_data(array('can_email' => !$this->sale_lib->get_email_receipt(), 'sale_id' => $sale_id_raw),$this->Employee->get_logged_in_employee_current_register_id());		
		
		if($customer_id != -1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}
		
		//If we don't have any taxes, run a check for items so we don't show the price including tax on receipt
		if (empty($data['taxes']))
		{
			foreach(array_keys($data['cart']) as $key)
			{
				if (isset($data['cart'][$key]['item_id']))
				{
					$item_info = $this->Item->get_info($data['cart'][$key]['item_id']);
					if($item_info->tax_included)
					{
						$this->load->helper('items');
						$price_to_use = get_price_for_item_excluding_taxes($data['cart'][$key]['item_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
				elseif (isset($data['cart'][$key]['item_kit_id']))
				{
					$item_info = $this->Item_kit->get_info($data['cart'][$key]['item_kit_id']);
					if($item_info->tax_included)
					{
						$price_to_use = get_price_for_item_kit_excluding_taxes($data['cart'][$key]['item_kit_id'], $data['cart'][$key]['price']);
						$data['cart'][$key]['price'] = $price_to_use;
					}					
				}
			}
		}
		
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$data['error_message'] = '';
			$this->load->helper('sale');
			if (is_sale_integrated_cc_processing())
			{
				$this->sale_lib->change_credit_card_payments_to_partial();
				$data['error_message'].='<span class="text-success">'.lang('sales_credit_card_transaction_completed_successfully').'. </span><br /<br />';
			}
			$data['error_message'] .= '<span class="text-danger">'.lang('sales_transaction_failed').'</span>';
			$data['error_message'] .= '<br /><br />'.anchor('sales','&laquo; '.lang('sales_register'));
			$data['error_message'] .= '<br /><br />'.anchor('sales/complete',lang('common_try_again'). ' &raquo;');
		}
		else
		{			
			if ($this->sale_lib->get_email_receipt() && !empty($cust_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
				$this->email->to($cust_info->email); 

				$this->email->subject(lang('sales_receipt'));
				$this->email->message($this->load->view("sales/receipt_email",$data, true));	
				$this->email->send();
			}
			
			if ($this->session->userdata('CC_SUCCESS'))
			{
				$credit_card_processor = $this->_get_cc_processor();
		
				if ($credit_card_processor)
				{
					$cc_processor_class_name = strtoupper(get_class($credit_card_processor));
					$cc_processor_parent_class_name = strtoupper(get_parent_class($credit_card_processor));
			
					if ($cc_processor_parent_class_name == 'DATACAPUSBPROCESSOR')
					{
						$data['reset_params'] = $credit_card_processor->get_emv_pad_reset_params();
					}
					
					if ($cc_processor_parent_class_name == 'DATACAPTRANSCLOUDPROCESSOR')
					{
						$data['trans_cloud_reset'] = TRUE;
					}
				}		
			}
		}
		
		if($this->sale_lib->get_delivery())
		{
			$data['delivery_person_info'] = $this->sale_lib->get_delivery_person_info();
						
			$data['delivery_info'] = $this->sale_lib->get_delivery_info();
		}
						
		$this->load->view("sales/receipt",$data);
		
		if ($data['sale_id'] != $this->config->item('sale_prefix').' -1')
		{
			$this->sale_lib->clear_all();
		}
	}
	
	function email_receipt($sale_id)
	{
		$this->lang->load('deliveries');
		
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
		$this->sale_lib->save_current_sale_state();
		
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id, true);
		$data['cart']=$this->sale_lib->get_cart();
		$data['deleted'] = $sale_info['deleted'];
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
		$data['taxes']=$this->sale_lib->get_taxes($sale_id);
		$data['total']=$this->sale_lib->get_total($sale_id);
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['comment'] = $sale_info['comment'];
		$data['show_comment_on_receipt'] = $sale_info['show_comment_on_receipt'];
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$data['override_location_id'] = $sale_info['location_id'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		$customer_id=$this->sale_lib->get_customer();
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$this->sale_lib->get_amount_due_round($sale_id) * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		
		$exchange_rate = $this->sale_lib->get_exchange_rate() ? $this->sale_lib->get_exchange_rate() : 1;
		$data['exchange_rate'] = $exchange_rate;
		$data['exchange_name'] = $this->sale_lib->get_exchange_name();
		$data['exchange_symbol'] = $this->sale_lib->get_exchange_currency_symbol();
		$data['exchange_symbol_location'] = $this->sale_lib->get_exchange_currency_symbol_location();
		$data['exchange_number_of_decimals'] = $this->sale_lib->get_exchange_currency_number_of_decimals();
		$data['exchange_thousands_separator'] = $this->sale_lib->get_exchange_currency_thousands_separator();
		$data['exchange_decimal_point'] = $this->sale_lib->get_exchange_currency_decimal_point();
		
		$data['disable_loyalty'] = 0;
		
		
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
			$data['customer_points'] = $cust_info->points;
		  $data['sales_until_discount'] = ($this->config->item('number_of_sales_for_discount') ? $this->config->item('number_of_sales_for_discount') : 0) - $cust_info->current_sales_for_discount;
 			$data['disable_loyalty'] = $cust_info->disable_loyalty;
						
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}
				
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['store_account_payment'] = FALSE;
		
		foreach($data['cart'] as $item)
		{
			if ($item['name'] == lang('common_store_account_payment'))
			{
				$data['store_account_payment'] = TRUE;
				break;
			}
		}
		
		if ($sale_info['suspended'] > 0)
		{
			if ($sale_info['suspended'] == 1)
			{
				$data['sale_type'] = ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway'));
			}
			elseif ($sale_info['suspended'] == 2)
			{
				$data['sale_type'] = lang('common_estimate');				
			}
		}
		
		if($this->sale_lib->get_delivery())
		{
			$data['delivery_person_info'] = $this->sale_lib->get_delivery_person_info();
						
			$data['delivery_info'] = $this->sale_lib->get_delivery_info();
		}
		
		if (!empty($cust_info->email))
		{
			$this->load->library('email');
			$config['mailtype'] = 'html';				
			$this->email->initialize($config);
			$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
			$this->email->to($cust_info->email); 

			$this->email->subject($sale_info['suspended'] == 2 ? lang('common_estimate') : lang('sales_receipt'));
			$this->email->message($this->load->view("sales/receipt_email",$data, true));	
			$this->email->send();
		}

		$this->sale_lib->clear_all();
		
		//Restore previous state saved above
		$this->sale_lib->restore_current_sale_state();
	}
	
	function receipt_validate()
	{
		if ($this->sale_lib->is_valid_receipt($this->input->post('sale_id')))
		{
			$sale_id = substr(strtolower($this->input->post('sale_id')), strpos(strtolower($this->input->post('sale_id')),$this->config->item('sale_prefix').' ') + strlen(strtolower($this->config->item('sale_prefix')).' '));
		}
		else
		{
			$sale_id = $this->input->post('sale_id');
		}
		
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		if(!$sale_info)
		{
			echo json_encode(array('success'=>false,'message'=>lang('sales_sale_id_not_found')));
			die();
		}
		else
		{
			echo json_encode(array('success'=>true,'sale_id' => $sale_id));
			die();
		}
	}
	
	function receipt($sale_id)
	{
		//Before changing the sale session data, we need to save our current state in case they were in the middle of a sale
		$this->sale_lib->save_current_sale_state();
		
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id, true);
		$data['cart']=$this->sale_lib->get_cart();
		$data['payments']=$this->sale_lib->get_payments();
		$data['is_sale_cash_payment'] = $this->sale_lib->is_sale_cash_payment();
		$data['show_payment_times'] = TRUE;
		$data['signature_file_id'] = $sale_info['signature_image_id'];
		
		$tier_id = $sale_info['tier_id'];
		$tier_info = $this->Tier->get_info($tier_id);
		$data['tier'] = $tier_info->name;
		$data['register_name'] = $this->Register->get_register_name($sale_info['register_id']);
		$data['override_location_id'] = $sale_info['location_id'];
		$data['deleted'] = $sale_info['deleted'];

		$data['subtotal']=$this->sale_lib->get_subtotal($sale_id);
		$data['taxes']=$this->sale_lib->get_taxes($sale_id);
		$data['total']=$this->sale_lib->get_total($sale_id);
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$sold_by_employee_id=$sale_info['sold_by_employee_id'];
		$sale_emp_info=$this->Employee->get_info($sold_by_employee_id);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=$this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.($sold_by_employee_id && $sold_by_employee_id != $sale_info['employee_id'] ? '/'. $sale_emp_info->first_name.' '.$sale_emp_info->last_name: '');
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$data['auth_code'] = $sale_info['auth_code'];
		$data['discount_exists'] = $this->_does_discount_exists($data['cart']);
		$data['disable_loyalty'] = 0;
		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
			$data['customer_points'] = $cust_info->points;
		  $data['sales_until_discount'] = ($this->config->item('number_of_sales_for_discount') ? $this->config->item('number_of_sales_for_discount') : 0) - $cust_info->current_sales_for_discount;
 			$data['disable_loyalty'] = $cust_info->disable_loyalty;
			
			if ($cust_info->balance !=0)
			{
				$data['customer_balance_for_sale'] = $cust_info->balance;
			}
		}		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['store_account_payment'] = FALSE;
		
		foreach($data['cart'] as $item)
		{
			if ($item['name'] == lang('common_store_account_payment'))
			{
				$data['store_account_payment'] = TRUE;
				break;
			}
		}
		
		if ($sale_info['suspended'] > 0)
		{
			if ($sale_info['suspended'] == 1)
			{
				$data['sale_type'] = ($this->config->item('user_configured_layaway_name') ? $this->config->item('user_configured_layaway_name') : lang('common_layaway'));
			}
			elseif ($sale_info['suspended'] == 2)
			{
				$data['sale_type'] = lang('common_estimate');				
			}
		}
		
		$exchange_rate = $this->sale_lib->get_exchange_rate() ? $this->sale_lib->get_exchange_rate() : 1;
		$data['exchange_rate'] = $exchange_rate;
		$data['exchange_name'] = $this->sale_lib->get_exchange_name();
		$data['exchange_symbol'] = $this->sale_lib->get_exchange_currency_symbol();
		$data['exchange_symbol_location'] = $this->sale_lib->get_exchange_currency_symbol_location();
		$data['exchange_number_of_decimals'] = $this->sale_lib->get_exchange_currency_number_of_decimals();
		$data['exchange_thousands_separator'] = $this->sale_lib->get_exchange_currency_thousands_separator();
		$data['exchange_decimal_point'] = $this->sale_lib->get_exchange_currency_decimal_point();
		
		if($this->sale_lib->get_delivery())
		{
			$data['delivery_person_info'] = $this->sale_lib->get_delivery_person_info();
						
			$data['delivery_info'] = $this->sale_lib->get_delivery_info();
		}
		
		$this->load->view("sales/receipt",$data);
		$this->sale_lib->clear_all();
		
		//Restore previous state saved above
		$this->sale_lib->restore_current_sale_state();
	}
	
	function fulfillment($sale_id)
	{
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$data['override_location_id'] = $sale_info['location_id'];
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time']= date(get_date_format().' '.get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id=$sale_info['customer_id'];
		
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
		
		if($customer_id)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
			$data['customer_address_1'] = $cust_info->address_1;
			$data['customer_address_2'] = $cust_info->address_2;
			$data['customer_city'] = $cust_info->city;
			$data['customer_state'] = $cust_info->state;
			$data['customer_zip'] = $cust_info->zip;
			$data['customer_country'] = $cust_info->country;
			$data['customer_phone'] = $cust_info->phone_number;
			$data['customer_email'] = $cust_info->email;
		}
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		$data['sale_id_raw']=$sale_id;
		$data['sales_items'] = $this->Sale->get_sale_items_ordered_by_category($sale_id)->result_array();
		$data['sales_item_kits'] = $this->Sale->get_sale_item_kits_ordered_by_category($sale_id)->result_array();
		$data['discount_exists'] = $this->_does_discount_exists($data['sales_items']) || $this->_does_discount_exists($data['sales_item_kits']);
				
		$this->load->model('Delivery');
		$this->load->model('Person');
		
		$delivery = $this->Delivery->get_info_by_sale_id($sale_id);
		
		if($delivery->num_rows()==1)
		{
			$data['delivery_info'] = $delivery->row_array();			
			$data['delivery_person_info'] = (array)$this->Person->get_info($this->Delivery->get_delivery_person_id($sale_id));
		}
		
		$this->load->view("sales/fulfillment",$data);
	}
	
	function _does_discount_exists($cart)
	{
		foreach($cart as $line=>$item)
		{
			if( (isset($item['discount']) && $item['discount']>0 ) || (isset($item['discount_percent']) && $item['discount_percent']>0 ) )
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	
	function edit($sale_id)
	{
		if(!$this->Employee->has_module_action_permission('sales', 'edit_sale', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();

		$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();
				
		if ($data['sale_info']['customer_id'])
		{
			$customer = $this->Customer->get_info($data['sale_info']['customer_id']);			
			$data['selected_customer_name'] = $customer->first_name . ' '. $customer->last_name;
			$data['selected_customer_email'] = $customer->email;
		}
		else
		{
			$data['selected_customer_name'] = lang('common_none');
		}
		
		$data['employees'] = array();
		foreach ($this->Employee->get_all()->result() as $employee)
		{
			$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
		}

		
		$data['store_account_payment'] = $data['sale_info']['store_account_payment'];
		$data['store_account_charge'] = $this->Sale->get_store_account_payment_total($sale_id) > 0 ? true : false;
		
		
		$this->load->view('sales/edit', $data);
	}
	
	function delete_sale_only($sale_id)
	{
		$this->check_action_permission('delete_sale');
		if ($this->Sale->delete($sale_id))
		{			
			echo json_encode(array('success'=>true,'message'=>lang('sales_successfully_deleted')));
		}
		else
		{
			echo json_encode(array('success'=>true,'message'=>lang('sales_unsuccessfully_deleted')));
		}
	}
	
	function delete($sale_id)
	{
		$this->check_action_permission('delete_sale');
		
		if (!$this->input->post('do_delete'))
		{
			$this->load->view('sales/delete', array('success' => false));
			return;
		}
		
		$data = array();
				
		$can_delete = TRUE;
		
		if ($this->input->post('sales_void_and_refund_credit_card') || $this->input->post('sales_void_and_cancel_return'))
		{					
			$credit_card_processor = $this->_get_cc_processor();
			if ($credit_card_processor)
			{
				$cc_processor_class_name = strtoupper(get_class($credit_card_processor));
				$cc_processor_parent_class_name = strtoupper(get_parent_class($credit_card_processor));
				if ($cc_processor_class_name == 'MERCURYHOSTEDCHECKOUTPROCESSOR' || $cc_processor_parent_class_name == 'DATACAPTRANSCLOUDPROCESSOR' || $cc_processor_class_name=='STRIPEPROCESSOR' || $cc_processor_class_name=='BRAINTREEPROCESSOR')
				{
					if ($this->input->post('sales_void_and_refund_credit_card'))
					{
						$can_delete = $credit_card_processor->void_sale($sale_id);
					}
					elseif($this->input->post('sales_void_and_cancel_return'))
					{
						$can_delete = $credit_card_processor->void_return($sale_id);
					}
					
					if ($can_delete && $this->Sale->delete($sale_id))
					{			
						$data['success'] = true;
						if ($this->input->post('clear_sale'))
						{
							$this->sale_lib->clear_all();
						}
						$data['sale_id'] = $sale_id;
					}
					else
					{
						$data['success'] = false;
					}
		
					$this->load->view('sales/delete', $data);
				}
				elseif ($cc_processor_parent_class_name =='DATACAPUSBPROCESSOR')
				{					
					if ($this->input->post('sales_void_and_refund_credit_card'))
					{
						$credit_card_processor->void_sale($sale_id);
					}
					elseif($this->input->post('sales_void_and_cancel_return'))
					{
						$credit_card_processor->void_return($sale_id);
					}	
				}
			}
		}
		else
		{
			if ($this->Sale->delete($sale_id))
			{			
				$data['success'] = true;
				if ($this->input->post('clear_sale'))
				{
					$this->sale_lib->clear_all();
				}
				$data['sale_id'] = $sale_id;
			}
			else
			{
				$data['success'] = false;
			}

			$this->load->view('sales/delete', $data);
		}
	}
	
	function undelete($sale_id)
	{
		if (!$this->input->post('do_undelete'))
		{
			$this->load->view('sales/undelete', array('success' => false));
			return;
		}
		$data = array();
		
		if ($this->Sale->undelete($sale_id))
		{
			$data['success'] = true;
		}
		else
		{
			$data['success'] = false;
		}
		
		$this->load->view('sales/undelete', $data);
		
	}
	
	function save($sale_id)
	{
		$sale_data = array(
			'sale_time' => date('Y-m-d H:i:s', strtotime($this->input->post('date'))),
			'customer_id' => $this->input->post('customer_id') ? $this->input->post('customer_id') : null,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment'),
			'show_comment_on_receipt' => $this->input->post('show_comment_on_receipt') ? 1 : 0
		);

		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		
		
		if ($this->Sale->update($sale_data, $sale_id))
		{
			echo json_encode(array('success'=>true,'message'=>lang('sales_successfully_updated')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>lang('sales_unsuccessfully_updated')));
		}
	}
	
	function _payments_cover_total()
	{
		$total_payments = 0;

		foreach($this->sale_lib->get_payments() as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}

		/* Changed the conditional to account for floating point rounding */
		if ( ( $this->sale_lib->get_mode() == 'sale' || $this->sale_lib->get_mode() == 'store_account_payment' ) && ( ( to_currency_no_money( $this->sale_lib->get_total() ) - $total_payments ) > 1e-6 ) )
		{
			return false;
		}
		
		return true;
	}
	
	function redeem_discount()
	{
		$customer_id = $this->sale_lib->get_customer();
		
		if ($customer_id != -1)
		{
			$cust_info = $this->Customer->get_info($customer_id);
		   $sales_until_discount = ($this->config->item('number_of_sales_for_discount') ? $this->config->item('number_of_sales_for_discount') : 0) - $cust_info->current_sales_for_discount;
			
			if ($sales_until_discount <= 0)
			{
				$discount_all_percent = $this->config->item('discount_percent_earned');
				$this->sale_lib->set_redeem('1');
				$this->sale_lib->discount_all($discount_all_percent);
				
	 	 	  foreach(array_keys($this->sale_lib->get_cart()) as $line)
	 	 	  {
	 	 	  	  if ($this->sale_lib->below_cost_price_item($line))
	 	 	  	  { 
	 	 			  if ($this->config->item('do_not_allow_below_cost'))
	 	 			  {
	 	 				  $this->sale_lib->discount_all(0);
		  				  $this->sale_lib->set_redeem('0');
	 	 				  $data['error'] = lang('sales_selling_item_below_cost');
	 	 			  }
	 	 			  else
	 	 			  {
	 	 				  $data['warning'] = lang('sales_selling_item_below_cost');
	 	 			  }
	 	 			  $this->_reload($data);
	 	 			  return;
	 	 		  }
	 	 	  }
				
			}
		}
		$this->_reload();
	}
	
	function unredeem_discount()
	{
		$this->sale_lib->set_redeem('0');
		$this->sale_lib->discount_all(0);
		$this->_reload();
	}
	
	function set_ebt_voucher_no()
	{
		$this->sale_lib->set_ebt_voucher_no($this->input->post('ebt_voucher_no'));
	}

	function set_ebt_voucher()
	{
		$this->sale_lib->set_ebt_voucher($this->input->post('ebt_voucher') ? 1 : 0);
	}
	
		
	function set_ebt_auth_code()
	{
		$this->sale_lib->set_ebt_auth_code($this->input->post('ebt_auth_code'));	
	}
	
	function reload()
	{
		$this->_reload();
	}
	
	function _reload($data=array(), $is_ajax = true)
	{	
		$data['is_tax_inclusive'] = $this->_is_tax_inclusive();
		
		$data['ebt_total'] = $this->sale_lib->get_ebt_total_amount_to_charge() - $this->sale_lib->get_payment_amount(lang('common_wic')) - $this->sale_lib->get_payment_amount(lang('common_ebt'));
		if ($data['is_tax_inclusive'] && count($this->sale_lib->get_deleted_taxes()) > 0)
		{
			$this->sale_lib->clear_deleted_taxes();
		}
		
		$person_info = $this->Employee->get_logged_in_employee_info();
		$modes = array('sale'=>lang('sales_sale'),'return'=>lang('sales_return'));
		
		if($this->config->item('customers_store_accounts')) 
		{
			$modes['store_account_payment'] = lang('common_store_account_payment');
		}
				
		$data['has_coupons_for_today'] = $this->Sale->has_coupons_for_today();
		$data['sale_id_of_edit_or_suspended_sale'] = $this->sale_lib->get_suspended_sale_id() ? $this->sale_lib->get_suspended_sale_id() : $this->sale_lib->get_change_sale_id();
		
		if (!$data['sale_id_of_edit_or_suspended_sale'])
		{
			$data['sale_id_of_edit_or_suspended_sale'] = '';
			$data['was_cc_return'] = 0;
			$data['was_cc_sale'] = 0;
		}
		else
		{
			$sale_info = $this->Sale->get_info($data['sale_id_of_edit_or_suspended_sale'])->row();	
			$data['was_cc_return'] = $this->Sale->can_void_cc_return($data['sale_id_of_edit_or_suspended_sale'])  ? 1 : 0;
			$data['was_cc_sale'] = $this->Sale->can_void_cc_sale($data['sale_id_of_edit_or_suspended_sale']) ? 1 : 0;
		}
		
		$data['cart']=$this->sale_lib->get_cart();
		$data['coupons'] = array();
		$data['has_discount'] = $this->sale_lib->has_discount();
		$data['modes']= $modes;
		$data['mode']=$this->sale_lib->get_mode();
		$data['items_in_cart'] = $this->sale_lib->get_items_in_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['line_for_flat_discount_item'] = $this->sale_lib->get_line_for_flat_discount_item();
		$data['discount_all_percent'] = $this->sale_lib->get_discount_all_percent();
		$data['discount_all_fixed'] = $this->sale_lib->get_discount_all_fixed();
		$data['items_module_allowed'] = $this->Employee->has_module_permission('items', $person_info->person_id);
		$data['comment'] = $this->sale_lib->get_comment();
		$data['show_comment_on_receipt'] = $this->sale_lib->get_comment_on_receipt();
		$data['email_receipt'] = $this->sale_lib->get_email_receipt();
		$data['delivery'] = $this->sale_lib->get_delivery();
		$data['payments_total']=$this->sale_lib->get_payments_totals_excluding_store_account();
		$data['selected_payment'] = $this->sale_lib->get_selected_payment();
		$data['discount_reason'] = $this->sale_lib->get_discount_reason();
		$data['coupon_codes'] = $this->sale_lib->get_coupons();
		$data['exchange_rate'] = $this->sale_lib->get_exchange_rate();
		$data['exchange_name'] = $this->sale_lib->get_exchange_name();
		$data['exchange_symbol'] = $this->sale_lib->get_exchange_currency_symbol();
		$data['exchange_symbol_location'] = $this->sale_lib->get_exchange_currency_symbol_location();
		$data['exchange_number_of_decimals'] = $this->sale_lib->get_exchange_currency_number_of_decimals();
		$data['exchange_thousands_separator'] = $this->sale_lib->get_exchange_currency_thousands_separator();
		$data['exchange_decimal_point'] = $this->sale_lib->get_exchange_currency_decimal_point();
		
		$data['exchange_details'] = $this->sale_lib->get_exchange_details();
		if (!$this->session->userdata('foreign_language_to_cur_language_sales'))
		{
			$this->load->helper('directory');
			$language_folder = directory_map(APPPATH.'language',1);

			$languages = array();

			foreach($language_folder as $language_folder)
			{
				$languages[] = substr($language_folder,0,strlen($language_folder)-1);
			}

			$cur_lang = array();
			foreach($this->Sale->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
			{
				$cur_lang[$lang_key] = $cur_lang_value;
			}


			foreach($languages as $language)
			{
				$this->lang->load('common', $language);

				foreach($this->Sale->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
				{
					if (strpos($lang_key,'common') !== FALSE)
					{
						$foreign_language_to_cur_language[lang($lang_key)] = $cur_lang[$lang_key];
					}
					else
					{
						$foreign_language_to_cur_language[$cur_lang_value] = $cur_lang_value;
					}
				}
			}
			
			$this->session->set_userdata('foreign_language_to_cur_language_sales', $foreign_language_to_cur_language);
			//Switch back
			$this->lang->switch_to($this->config->item('language'));
		}
		else
		{
			$foreign_language_to_cur_language = $this->session->userdata('foreign_language_to_cur_language_sales');
		}

		$default_payment_type_translated = false;
		if (isset($foreign_language_to_cur_language[$this->config->item('default_payment_type')]))
		{
			$default_payment_type_translated = $foreign_language_to_cur_language[$this->config->item('default_payment_type')];
		}
		else
		{
			$default_payment_type_translated = $this->config->item('default_payment_type');
		}
		
		$data['default_payment_type'] = $default_payment_type_translated ? $default_payment_type_translated : lang('common_cash');
		
		$data['amount_due']=$this->sale_lib->get_amount_due();
		$data['payments']=$this->sale_lib->get_payments();
		$data['change_sale_date_enable'] = $this->sale_lib->get_change_sale_date_enable();
		$data['change_sale_date'] = $this->sale_lib->get_change_sale_date();
		$data['selected_tier_id'] = $this->sale_lib->get_selected_tier_id();
		$data['is_over_credit_limit'] = false;
		$data['fullscreen'] = $this->session->userdata('fullscreen');
		$data['redeem'] = $this->sale_lib->get_redeem();
		
		$customer_id=$this->sale_lib->get_customer();
		
		if ($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);

			$customer_giftcards=$this->Giftcard->get_customer_giftcards($customer_id);

		}
		
		$data['prompt_for_card'] = $this->sale_lib->get_prompt_for_card();
		$data['cc_processor_class_name'] = $this->_get_cc_processor() ? strtoupper(get_class($this->_get_cc_processor())) : '';
		$data['cc_processor_parent_class_name'] = $this->_get_cc_processor() ? strtoupper(get_parent_class($this->_get_cc_processor())) : '';
		
		$data['ebt_voucher'] = $this->sale_lib->get_ebt_voucher();
		$data['ebt_voucher_no'] = $this->sale_lib->get_ebt_voucher_no();
		$data['ebt_auth_code'] = $this->sale_lib->get_ebt_auth_code();
		
		
		if ($this->config->item('select_sales_person_during_sale'))
		{
			$employees = array('' => lang('common_not_set'));
			
			foreach($this->Employee->get_all()->result() as $employee)
			{
				if ($this->Employee->is_employee_authenticated($employee->person_id, $this->Employee->get_logged_in_employee_current_location_id()))
				{
					$employees[$employee->person_id] = $employee->first_name.' '.$employee->last_name;
				}
			}
			$data['employees'] = $employees;
			$data['selected_sold_by_employee_id'] = $this->sale_lib->get_sold_by_employee_id();
		}
		
		$tiers = array();

		$tiers[0] = lang('common_none');
		foreach($this->Tier->get_all()->result() as $tier)
		{
			$tiers[$tier->id]=$tier->name;
		}
		
		$data['tiers'] = $tiers;
		
		$data['payment_options'] = $this->Sale->get_payment_options();
		if($customer_id!=-1)
		{
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->company_name==''  ? '':' ('.$cust_info->company_name.')');
			$data['customer_email']=$cust_info->email;
			$data['customer_has_address'] = $this->sale_lib->does_customer_have_address();
			$data['customer_balance'] = $cust_info->balance;
			$data['customer_credit_limit'] = $cust_info->credit_limit;
			$data['is_over_credit_limit'] = $this->sale_lib->is_over_credit_limit();
			$data['customer_id']=$customer_id;
			$data['customer_cc_token'] = $cust_info->cc_token;
			$data['customer_cc_preview'] = $cust_info->cc_preview;
			$data['save_credit_card_info'] = $this->sale_lib->get_save_credit_card_info();
			$data['use_saved_cc_info'] = $this->sale_lib->get_use_saved_cc_info();
			$data['avatar']=$cust_info->image_id ?  app_file_url($cust_info->image_id) : base_url()."assets/img/user.png"; //can be changed to  base_url()."img/avatar.png" if it is required
			if(count($customer_giftcards))
			{
				$data['customer_giftcards'] = $customer_giftcards;
	
			}
			$data['disable_loyalty'] = $cust_info->disable_loyalty;
			$data['points'] = to_currency_no_money($cust_info->points);
			$data['sales_until_discount'] = ($this->config->item('number_of_sales_for_discount')) ? (float)$this->config->item('number_of_sales_for_discount') - (float)$cust_info->current_sales_for_discount : 0;
		}
		$data['customer_required_check'] = (!$this->config->item('require_customer_for_sale') || ($this->config->item('require_customer_for_sale') && isset($customer_id) && $customer_id!=-1));
		$data['suspended_sale_customer_required_check'] = (!$this->config->item('require_customer_for_suspended_sale') || ($this->config->item('require_customer_for_suspended_sale') && isset($customer_id) && $customer_id!=-1));
		$data['payments_cover_total'] = $this->_payments_cover_total();
				
		if ($data['mode'] == 'store_account_payment' && $customer_id!=-1)
		{
			$sale_ids = $this->Sale->get_unpaid_store_account_sale_ids($customer_id);
			
			$paid_sales = $this->sale_lib->get_paid_store_account_sales();
									
			$this->db->select('sales.sale_id, sale_time,SUM(payment_amount) as payment_amount,sales.comment', false);
			$this->db->from('sales');
			$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
			
			
			if (!empty($sale_ids))
			{
				$this->db->where_in('sales.sale_id', $sale_ids);
			}
			else
			{
				$this->db->where_in('sales.sale_id', array(0));				
			}
			$this->db->order_by('sale_time');
			$this->db->group_by('sale_id');
			$data['unpaid_store_account_sales'] = $this->db->get()->result_array();
			
			
			for($k=0;$k<count($data['unpaid_store_account_sales']);$k++)
			{
				if (isset($paid_sales[$data['unpaid_store_account_sales'][$k]['sale_id']]))
				{
					$data['unpaid_store_account_sales'][$k]['paid'] = TRUE;
				}
			}
		}
		
		
			//fixing this for arabic
			if (is_rtl_lang())
			{
			  $data['discount_editable_placement'] = $this->agent->is_mobile() && !$this->agent->is_tablet() ? 'top' : 'right';
			}
			else
			{
				$data['discount_editable_placement'] = $this->agent->is_mobile() && !$this->agent->is_tablet() ? 'top' : 'left';
			}

	  	if ($is_ajax)
			{
				$this->load->view("sales/register",$data);
			}
			else
			{
				$this->load->view("sales/register_initial",$data);
			}
		
	}
	
	function pay_store_account_sale($sale_id, $amount)
	{
		$this->sale_lib->add_paid_store_account_sale($sale_id);
		$cart = $this->sale_lib->get_cart();
		foreach($cart as $item)
		{
			if ($item['name'] == lang('common_store_account_payment'))
			{
				$cart[$item['line']]['price'] += $amount; 
				$this->sale_lib->set_cart($cart);	
				break;
			}
		}
		$comment = lang('sales_pays_sales'). ' - '.implode(', ',array_keys($this->sale_lib->get_paid_store_account_sales()));
			
		$this->sale_lib->set_comment($comment);
		
     	$this->_reload();
	}
	
	function delete_store_account_sale($sale_id, $amount)
	{
		$this->sale_lib->remove_paid_store_account_sale($sale_id);
		$cart = $this->sale_lib->get_cart();
		foreach($cart as $item)
		{
			if ($item['name'] == lang('common_store_account_payment'))
			{
				$cart[$item['line']]['price'] -= $amount; 
				$this->sale_lib->set_cart($cart);	
				break;
			}
		}
		$comment = lang('sales_pays_sales'). ' - '.implode(', ',array_keys($this->sale_lib->get_paid_store_account_sales()));
			
		$this->sale_lib->set_comment($comment);
		
     	$this->_reload();
	}
	

	function customer_recent_sales($customer_id)
	{
		$data['customer'] = $this->Customer->get_info($customer_id)->first_name.' '.$this->Customer->get_info($customer_id)->last_name;
		$data['customer_comments'] = $this->Customer->get_info($customer_id)->comments;
		$data['recent_sales'] = $this->Sale->get_recent_sales_for_customer($customer_id);
		$this->load->view("sales/customer_recent_sales", $data);
	}


  function cancel_sale()
  {
	 if ($this->Location->get_info_for_key('enable_credit_card_processing'))
	 {
 		$credit_card_processor = $this->_get_cc_processor();
		 
		 if ($credit_card_processor && method_exists($credit_card_processor, 'void_partial_transactions'))
		 {
			 if (!$credit_card_processor->void_partial_transactions())
			 {
	     		 $this->sale_lib->clear_all();
				 $this->_reload(array('error' => lang('sales_attempted_to_reverse_transactions_failed_please_contact_support')), true);
				 return;
			 }
 			 }
	 }
	 
   	$this->sale_lib->clear_all();
   	$this->_reload();
	}
	
	function clear_sale()
	{
   	$this->sale_lib->clear_all();
   	$this->_reload();
	}
	
	function suspend($suspend_type = 1)
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']= $this->config->item('override_receipt_title') ? $this->config->item('override_receipt_title') : lang('sales_receipt');
		$data['transaction_time']= date(get_date_format().' '.get_time_format());
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sold_by_employee_id=$this->sale_lib->get_sold_by_employee_id();
		$comment = $this->sale_lib->get_comment();
		$discount_reason = $this->sale_lib->get_discount_reason();
		$exchange_rate = $this->sale_lib->get_exchange_rate() ? $this->sale_lib->get_exchange_rate() : 1;

		$show_comment_on_receipt = $this->sale_lib->get_comment_on_receipt();
		$emp_info=$this->Employee->get_info($employee_id);
		//Alain Multiple payments
		$data['payments']=$this->sale_lib->get_payments();
		$data['amount_change']=$this->sale_lib->get_amount_due() * -1;
		
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		
		$data['balance'] = 0;
		//Add up balances for all languages
		foreach($store_account_in_all_languages as $store_account_lang)
		{
			//Thanks Mike for math help on how to convert exchange rate back to get correct balance
			$data['balance']+= $this->sale_lib->get_payment_amount($store_account_lang)*pow($exchange_rate,-1);
		}
		
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
			$data['customer_company']= $cust_info->company_name;
		}

		$total_payments = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}
		
		$sale_id = $this->sale_lib->get_suspended_sale_id();
		
		$sale_date = $this->config->item('change_sale_date_when_suspending') ? date('Y-m-d H:i:s') : FALSE;
		
		if ($this->sale_lib->get_change_sale_date_enable())
		{
			$sale_date = $this->sale_lib->get_change_sale_date();			
		}
		
		//SAVE sale to database
		$sale_id = $this->Sale->save($data['cart'], $customer_id,$employee_id, $sold_by_employee_id, $comment,$discount_reason, $show_comment_on_receipt,$this->sale_lib->get_coupons(), $data['payments'], $sale_id, $suspend_type,$sale_date, $data['balance']);
		
		$data['sale_id']=$this->config->item('sale_prefix').' '.$sale_id;
		if ($data['sale_id'] == $this->config->item('sale_prefix').' -1')
		{
			$this->_reload(array('error' => lang('sales_transaction_failed')));
			return;
		}
		$this->sale_lib->clear_all();
		
		if ($this->config->item('show_receipt_after_suspending_sale'))
		{
			redirect('sales/receipt/'.$sale_id);
		}
		else
		{
			$this->_reload(array('success' => lang('sales_successfully_suspended_sale')));
		}
	}
	
	
	function batch_sale()
	{
		$this->load->view("sales/batch");
	}
	
	function _excel_get_header_row()
	{
		return array(lang('common_item_id').'/'.lang('common_item_number').'/'.lang('common_product_id'),lang('common_unit_price'),lang('common_quantity'),lang('common_discount_percent'),lang('common_description'));
	}
	
	function excel()
	{	
		$this->load->helper('report');
		$header_row = $this->_excel_get_header_row();
		$this->load->helper('spreadsheet');
		array_to_spreadsheet(array($header_row),'batch_sale_export.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'));
	}
	
	
	function do_excel_import()
	{
		$this->load->helper('demo');

		$file_info = pathinfo($_FILES['file_path']['name']);
		if($file_info['extension'] != 'xlsx' && $file_info['extension'] != 'csv')
		{
			echo json_encode(array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
			return;
		}
		
		set_time_limit(0);
		//$this->check_action_permission('add_update');
		$this->db->trans_start();
		
		$msg = 'do_excel_import';
		$failCodes = array();
		
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = lang('common_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				$this->load->helper('spreadsheet');
				$file_info = pathinfo($_FILES['file_path']['name']);
				
				$sheet = file_to_spreadsheet($_FILES['file_path']['tmp_name'],$file_info['extension']);
				$num_rows = $sheet->getNumberOfRows();
				
				//Loop through rows, skip header row
				for($k = 2;$k<=$num_rows; $k++)
				{
					
					$item_id = $sheet->getCellByColumnAndRow(0, $k);
					if (!$item_id)
					{
						continue;
					}
					
					$price = $sheet->getCellByColumnAndRow(1, $k);
					if (!$price)
					{
						$price = null;
					}
				
					$quantity = $sheet->getCellByColumnAndRow(2, $k);
					if (!$quantity)
					{
						continue;
					}

					$discount = $sheet->getCellByColumnAndRow(3, $k);
					if (!$discount)
					{
						$discount = 0;
					}

					$description = $sheet->getCellByColumnAndRow(4, $k);
					if (!$description)
					{
						$description = null;
					}
					
					if($this->sale_lib->is_valid_item_kit($item_id))
					{
						if(!$this->sale_lib->add_item_kit($item_id,$quantity,$discount,$price,null,null,$description))
						{
							$this->sale_lib->empty_cart();
							echo json_encode( array('success'=>false,'message'=>lang('batch_sales_error')));
							return;
						}
					}
					elseif(!$this->sale_lib->add_item($item_id,$quantity,$discount,$price,null,null,$description))
					{	
						$this->sale_lib->empty_cart();
						echo json_encode( array('success'=>false,'message'=>lang('batch_sales_error')));
						return;
					}
				}
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>lang('common_upload_file_not_supported_format')));
				return;
			}
		}
		$this->db->trans_complete();
		echo json_encode(array('success'=>true,'message'=>lang('sales_import_successfull')));
		
	}
	
	
	function new_giftcard()
	{
		if (!$this->Employee->has_module_action_permission('giftcards', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			redirect('no_access/'.$this->module_id);
		}
		
		$data = array();
		$data['item_id']=$this->Item->get_item_id(lang('common_giftcard'));
		$this->load->view("sales/giftcard_form",$data);
	}
	
	function suspended()
	{
		$data = array();
		$data['suspended_sales'] = $this->Sale->get_all_suspended();
		$this->load->view('sales/suspended', $data);
	}
	
	function change_sale($sale_id)
	{
		$this->check_action_permission('edit_sale');
		$sale_info = $this->Sale->get_info($sale_id)->row();
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->sale_lib->set_change_sale_id($sale_id);
		
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$this->sale_lib->change_credit_card_payments_to_partial();				
		}
		
		//Clear register if we switch locations
		if ($sale_info->location_id != $this->Employee->get_logged_in_employee_current_location_id())
		{
			$this->load->model('Register');
			$this->Employee->set_employee_current_register_id($this->Register->get_default_register_info()->register_id);
		}
		
    $this->_reload(array('orig_location' => $sale_info->location_id), false);
	}
		
	function unsuspend($sale_id = 0)
	{
		$sale_id = $this->input->post('suspended_sale_id') ? $this->input->post('suspended_sale_id') : $sale_id;
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_sale($sale_id);
		$this->sale_lib->set_suspended_sale_id($sale_id);
		
		
		if ($this->sale_lib->get_customer())
		{
			if($this->config->item('automatically_email_receipt'))
			{
				$this->sale_lib->set_email_receipt(1);
			}
			
			$customer_info=$this->Customer->get_info($this->sale_lib->get_customer());
			
			$sale_tier_id = $this->Sale->get_tier_id($sale_id);
			
			if ($sale_tier_id)
			{				
				$this->sale_lib->set_selected_tier_id($sale_tier_id,$this->config->item('lock_prices_suspended_sales') ? FALSE : TRUE);
			}
			elseif ($customer_info->tier_id)
			{
				$this->sale_lib->set_selected_tier_id($customer_info->tier_id);
			}
		}
		
    	$this->_reload(array(), false);
	}
	
	function delete_suspended_sale()
	{
		$this->check_action_permission('delete_suspended_sale');
		$suspended_sale_id = $this->input->post('suspended_sale_id');
		if ($suspended_sale_id)
		{
			$this->sale_lib->delete_suspended_sale_id();
			$this->Sale->delete($suspended_sale_id);
		}
    	redirect('sales/suspended');
	}
	
	function discount_all()
	{
		$discount_all_percent = (float)$this->input->post('discount_all_percent');

		if($this->input->post('name')=="discount_all_percent")
		{
			$discount_all_percent = (float)$this->input->post('value');
			$result = $this->sale_lib->discount_all($discount_all_percent);
			if(!$result)
			{
			  $data['error'] = lang('sales_could_not_discount_item_above_max').' '.lang('sales_the_items_in_the_cart');
 			  $this->_reload($data);
				return;
			}
			
	 	  foreach(array_keys($this->sale_lib->get_cart()) as $line)
	 	  {
	 	  	  if ($this->sale_lib->below_cost_price_item($line))
	 	  	  { 
	 			  if ($this->config->item('do_not_allow_below_cost'))
	 			  {
	 				  $this->sale_lib->discount_all(0);
	 				  $data['error'] = lang('sales_selling_item_below_cost');
	 			  }
	 			  else
	 			  {
	 				  $data['warning'] = lang('sales_selling_item_below_cost');
	 			  }
	 			  $this->_reload($data);
	 			  return;
	 		  }
	 	  }
			
		}
		elseif ($this->input->post('name') == 'discount_all_flat')
		{
			$discount_amount = strpos($this->input->post('value'), '%',0) !== FALSE ? ($this->sale_lib->get_subtotal(FALSE, FALSE) * (float)$this->input->post('value')/100) : (float)$this->input->post('value');
			$this->sale_lib->delete_item($this->sale_lib->get_line_for_flat_discount_item());
			$item_id = $this->Item->create_or_update_flat_discount_item();
			
			$description =  strpos($this->input->post('value'), '%',0) ? lang('sales_discount_percent').': '.$this->input->post('value') : '';			
			$this->sale_lib->add_item($item_id.'|FORCE_ITEM_ID|',-1,0,to_currency_no_money($discount_amount),to_currency_no_money($discount_amount),0,$description, null, false, false, true, false);
		}
		
		$this->_reload();
	}
	
	function discount_reason()
	{
		$discount_reason = $this->input->post('value');
		$this->sale_lib->set_discount_reason($discount_reason);
		$this->_reload();
	}
	
	function categories_and_items($category_id = NULL, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		//If a false value, make sure it is NULL
		if (!$category_id)
		{
			$category_id = NULL;
		}
		
		//Categories
		$categories = $this->Category->get_all($category_id);
		$categories_count = count($categories);		
		$config['base_url'] = site_url('sales/categories_and_items/'.($category_id ? $category_id : 0));
		$config['uri_segment'] = 4;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		
		$categories_and_items_response = array();
		$this->load->model('Appfile');
		foreach($categories as $id=>$value)
		{
			$categories_and_items_response[] = array('id' => $id, 'name' => $value['name'], 'color' => $value['color'], 'image_id' => $value['image_id'],'image_timestamp' => $this->Appfile->get_file_timestamp($value['image_id']),'type' => 'category');
		}
		
		//Items
		$items = array();
		
		$items_offset = ($offset - $categories_count > 0 ? $offset - $categories_count : 0);		
		$items_result = $this->Item->get_all_by_category($category_id, $this->config->item('hide_out_of_stock_grid') ? TRUE : FALSE, $items_offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14)->result();
		
		foreach($items_result as $item)
		{
			$img_src = "";
			if ($item->image_id != 'no_image' && trim($item->image_id) != '') {
				$img_src = app_file_url($item->image_id);
			}
			
			$size = $item->size ? ' - '.$item->size : '';
			
			if (strpos($item->item_id, 'KIT') === 0)
			{
				$price_to_use = $this->sale_lib->get_price_for_item_kit(str_replace('KIT','',$item->item_id));
			}
			else
			{
				$price_to_use = $this->sale_lib->get_price_for_item($item->item_id);
			}
			
			$categories_and_items_response[] = array(
				'id' => $item->item_id,
				'name' => character_limiter($item->name, 58).$size,				
				'image_src' => 	$img_src,
				'type' => 'item',		
				'price' => $price_to_use != '0.00' ? to_currency($price_to_use) : FALSE,
				'regular_price' => to_currency($item->unit_price),	
				'different_price' => $price_to_use != $item->unit_price,	
			);	
		}
	
		$items_count = $this->Item->count_all_by_category($category_id);		
		$categories_and_items_response = array_slice($categories_and_items_response, $offset > $categories_count ? $categories_count : $offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14);
		
		$data = array();
		$data['categories_and_items'] = $categories_and_items_response;
		$config['total_rows'] = $categories_count + $items_count;
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);
	}
	
	function categories($parent_id = NULL, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		//If a false value, make sure it is NULL
		if (!$parent_id)
		{
				$parent_id = NULL;
		}
		$categories = $this->Category->get_all($parent_id,FALSE, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14, $offset);
		
		$categories_count = $this->Category->count_all($parent_id);		
		$config['base_url'] = site_url('sales/categories/'.($parent_id ? $parent_id : 0));
		$config['uri_segment'] = 4;
		$config['total_rows'] = $categories_count;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$categories_response = array();
		$this->load->model('Appfile');
		foreach($categories as $id=>$value)
		{
				$categories_response[] = array('id' => $id, 'name' => $value['name'], 'color' => $value['color'], 'image_id' => $value['image_id'],'image_timestamp' => $this->Appfile->get_file_timestamp($value['image_id']));
		}
		

		$data = array();
		$data['categories'] = $categories_response;
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);	
	}
	
	function tags($offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$tags = $this->Tag->get_all($this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14, $offset);
		
		$tags_count = $this->Tag->count_all();		
		$config['base_url'] = site_url('sales/tags');
		$config['uri_segment'] = 3;
		$config['total_rows'] = $tags_count;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		$this->load->library('pagination');$this->pagination->initialize($config);
		
		$tags_response = array();
		
		foreach($tags as $id=>$value)
		{
				$tags_response[] = array('id' => $id, 'name' => $value['name']);
		}
		

		$data = array();
		$data['tags'] = $tags_response;
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);	
	}
	
	
	function tag_items($tag_id, $offset = 0)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$config['base_url'] = site_url('sales/tag_items/'.($tag_id ? $tag_id : 0));
		$config['uri_segment'] = 4;
		$config['per_page'] = $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14; 
		
				
		//Items
		$items = array();
		
		$items_result = $this->Item->get_all_by_tag($tag_id,$this->config->item('hide_out_of_stock_grid') ? TRUE : FALSE, $offset, $this->config->item('number_of_items_in_grid') ? $this->config->item('number_of_items_in_grid') : 14)->result();
		
		
		foreach($items_result as $item)
		{
			$img_src = "";
			if ($item->image_id != 'no_image' && trim($item->image_id) != '') {
				$img_src = app_file_url($item->image_id);
			}

			if (strpos($item->item_id, 'KIT') === 0)
			{
				$price_to_use = $this->sale_lib->get_price_for_item_kit(str_replace('KIT','',$item->item_id));
			}
			else
			{
				$price_to_use = $this->sale_lib->get_price_for_item($item->item_id);
			}

			$items[] = array(
				'id' => $item->item_id,
				'name' => character_limiter($item->name, 58),				
				'image_src' => 	$img_src,
				'type' => 'item',		
				'price' => $price_to_use != '0.00' ? to_currency($price_to_use) : FALSE,
				'regular_price' => to_currency($item->unit_price),	
				'different_price' => $price_to_use != $item->unit_price,	
			);	
		}
	
		$items_count = $this->Item->count_all_by_tag($tag_id);		
		
		$data = array();
		$data['items'] = $items;
		$config['total_rows'] = $items_count;
		$this->load->library('pagination');$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		echo json_encode($data);
	}
	
		
	function delete_tax($name)
	{
		$this->check_action_permission('delete_taxes');
		$name = rawurldecode($name);
		$this->sale_lib->add_deleted_tax($name);
		$this->sale_lib->update_register_cart_data();
		$this->_reload();
	}

	function view_receipt_modal()
	{
		$this->load->view('sales/lookup_modal');
	}
	
	function set_delivery()
	{
		$this->sale_lib->set_delivery($this->input->post('delivery'));
		
		if($this->input->post('delivery') == '0')
		{
			$this->sale_lib->delete_item($this->sale_lib->get_line_for_delivery_item());
		}
		
		$data = array();
		$this->_reload($data);
	}
	
	function set_delivery_info()
	{		
	  $data = array();
		$this->sale_lib->set_delivery(1);
		
 	  $this->sale_lib->set_delivery_person_info($this->input->post('delivery_person_info'));
 	  $this->sale_lib->set_delivery_info($this->input->post('delivery_info'));
		$this->sale_lib->set_delivery_tax_group_id($this->input->post('delivery_tax_group_id'));
		
		$delivery_item_id = $this->Item->create_or_update_delivery_item();
		
		$this->sale_lib->delete_item($this->sale_lib->get_line_for_delivery_item());
		
		$this->sale_lib->add_item($delivery_item_id.'|FORCE_ITEM_ID|',1,0,to_currency_no_money($this->input->post('delivery_fee')),to_currency_no_money($this->input->post('delivery_fee')),0,'', null, false, false, true, false);
		
  	$this->_reload($data);
	}
	
	function view_delivery_modal()
	{
		$this->lang->load('deliveries');
		$this->load->model('Tax_class');
		$this->load->model('Shipping_zone');
		$this->load->model('Zip');
		
		$tax_classes = array();
		$tax_classes[''] = lang('common_none');
		
		foreach($this->Tax_class->get_all()->result_array() as $tax_class)
		{
			$tax_classes[$tax_class['id']] = $tax_class['name'];
		}
		
		$delivery_info = $this->sale_lib->get_delivery_info();
				
		if (empty($delivery_info))
		{
			$delivery_info['comment'] = '';
			$delivery_info['tracking_number'] = '';
			$delivery_info['is_pickup'] = 0;
		}
		
		$delivery_person_info = $this->sale_lib->get_delivery_person_info();
		
		$customer_info = $this->Customer->get_info($this->sale_lib->get_customer());
		
		if (empty($delivery_person_info))
		{
			$delivery_person_info['first_name'] = $customer_info->first_name;
			$delivery_person_info['last_name'] = $customer_info->last_name;
			$delivery_person_info['phone_number'] = $customer_info->phone_number;
			$delivery_person_info['address_1'] = $customer_info->address_1;
			$delivery_person_info['address_2'] = $customer_info->address_2;
			$delivery_person_info['city'] = $customer_info->city;
			$delivery_person_info['state'] = $customer_info->state;
			$delivery_person_info['zip'] = $customer_info->zip;
			$delivery_person_info['country'] = $customer_info->country;
						
			$this->sale_lib->set_delivery_person_info($delivery_person_info);
		}
		
		$zip_lookup = $this->Zip->lookup($delivery_person_info['zip'])->row_array();
		
		$zip_zones = array();
		
		foreach($this->Zip->get_all()->result_array() as $zip)
		{
			$zip_zones[$zip['name']] = $zip['shipping_zone_id'];
		}
		
		$shipping_zones = array();
		$shipping_zones['0'] =lang('common_none');
		
		$shipping_zone_id = isset($delivery_info['shipping_zone_id']) ? $delivery_info['shipping_zone_id'] : $zip_lookup['shipping_zone_id'];
		
		foreach($this->Shipping_zone->get_all()->result_array() as $shipping_zone)
		{
			$shipping_zones[$shipping_zone['id']] = $shipping_zone['name'];
		}
		
		$shipping_zone_info = array();
		foreach($this->Shipping_zone->get_all()->result_array() as $shipping_zone)
		{
			$shipping_zone_info[$shipping_zone['id']] = array('name' => $shipping_zone['name'], 'fee' => $shipping_zone['fee'], 'tax_class_id' => $shipping_zone['tax_class_id']);
		}
		
		//session tax group
		$delivery_tax_group_id = $this->sale_lib->get_delivery_tax_group_id();
		if($delivery_tax_group_id === NULL)//haven't set group
		{
			//Customer tax group
			$delivery_tax_group_id = $customer_info->tax_class_id;
			
			
			//zone tax group
			if (!$delivery_tax_group_id)
			{
				$delivery_tax_group_id = isset($shipping_zone_info[$shipping_zone_id]['tax_class_id']) ? $shipping_zone_info[$shipping_zone_id]['tax_class_id'] : NULL;
			}
		}
		$delivery_providers = $this->Shipping_provider->get_all()->result_array();
		$delivery_methods = $this->Shipping_method->get_all()->result_array();
		
		$providers_with_methods = array();
		
		foreach($delivery_providers as $provider)
		{
			$providers_with_methods[$provider['id']] = $provider;
		}
		
		foreach($delivery_methods as $method)
		{
			$providers_with_methods[$method['shipping_provider_id']]['methods'][] = $method;			
		}
		
		$delivery_fee = $this->sale_lib->get_delivery_item_price_in_cart();
		
		$this->load->view('sales/delivery_modal', array('delivery_person_info' => $delivery_person_info, 'delivery_info' => $delivery_info, 'providers_with_methods' => $providers_with_methods, 'tax_classes' => $tax_classes, 'delivery_tax_group_id' => $delivery_tax_group_id, 'shipping_zone_id' => $shipping_zone_id, 'shipping_zone_info' => $shipping_zone_info, 'shipping_zones' => $shipping_zones, 'delivery_fee' => $delivery_fee, 'zip_zones' => $zip_zones));
	}
	
	function sig_save()
	{
		$this->load->model('Appfile');
		$sale_id = $this->input->post('sale_id');
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		
		//If we have a signature delete it
		if ($sale_info['signature_image_id'])
		{
			$this->Sale->update(array('signature_image_id' => NULL), $sale_id);
			$this->Appfile->delete($sale_info['signature_image_id']);
		}
		
		$image = base64_decode($this->input->post('image'));
    	$image_file_id = $this->Appfile->save('signature_'.$sale_id.'.png', $image);
		$this->Sale->update(array('signature_image_id' => $image_file_id), $sale_id);
		
		echo json_encode(array('file_id' => $image_file_id, 'file_timestamp' => $this->Appfile->get_file_timestamp($image_file_id)));
	}
	
	function customer_display($register_id = false)
	{
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		if ($this->Register->exists($register_id))
		{
			
			$this->load->view('sales/customer_display_initial', array('register_id' => $register_id,'fullscreen_customer_display'=> $this->session->userdata('fullscreen_customer_display')));
		}
	}
	
	function customer_display_update($register_id = false)
	{
		//allow parallel searchs to improve performance.
		session_write_close();
		
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		if ($this->Register->exists($register_id))
		{
			$data = $this->Register_cart->get_data($register_id);
			$data['mode'] = "sale";
			
			if (isset($data['sale_id']))
			{
				$sale_info = $this->Sale->get_info($data['sale_id'])->row_array();
				$customer_id = $sale_info['customer_id'];
				
				if($customer_id)
				{
					$cust_info=$this->Customer->get_info($customer_id);
					$data['customer']=$cust_info->first_name.' '.$cust_info->last_name.($cust_info->account_number==''  ? '':' - '.$cust_info->account_number);
					$data['customer_company']= $cust_info->company_name;
					$data['customer_email'] = $cust_info->email;			
				}		
				
				
			}
		
			$data['fullscreen_customer_display'] = $this->session->userdata('fullscreen_customer_display');
			
			$this->load->view("sales/customer_display",$data);
		}
	}
	
	function customer_display_info($register_id)
	{
		
		//allow parallel searchs to improve performance.
		session_write_close();
		
		$return = array();
		$return['sale_id'] = false;
		
		if (!$register_id)
		{
			$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		}
		
		if ($this->Register->exists($register_id))
		{
			$data = $this->Register_cart->get_data($register_id);
			if (isset($data['sale_id']))
			{
				$return['sale_id'] = $data['sale_id'];
			}
		}		
		echo json_encode($return);
		
	}
	
	function open_drawer()
	{
		$this->load->view('sales/open_drawer');
	}
	
	function disable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','0');
		}
		
		redirect(site_url('sales'));	
	}
	
	function enable_test_mode()
	{
		$this->load->helper('demo');
		if (!is_on_demo_host())
		{
			$this->Appconfig->save('test_mode','1');
		}
		redirect(site_url('sales'));	
	}
	
	function create_po($sale_id)
	{
		$this->load->library('receiving_lib');
		$this->load->model('Sale');
		$this->load->model('Item_location');
		$this->load->model('Item');
				
		$this->receiving_lib->empty_cart();			
		$items = $this->Sale->get_sale_items($sale_id)->result_array(); 
		
		$item_ids = array();
		
		foreach($items as $item)
		{
			$item_id = $item['item_id'];
			$item_ids[$item_id] = TRUE;
		}
		
		foreach(array_keys($item_ids) as $item_id)
		{
			$quantity_to_add= 1;
			$cur_item_location_info = $this->Item_location->get_info($item_id);
			$cur_item_info = $this->Item->get_info($item_id);
			$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
						
			$quantity_to_add = $reorder_level - $cur_item_location_info->quantity;
			$this->receiving_lib->add_item($item_id,max(0,$quantity_to_add));
		}

		$this->receiving_lib->set_po(TRUE);
		$this->receiving_lib->set_mode('purchase_order');
		redirect('receivings');
	}
	
	function exchange_to()
	{
		$data = array();
		$rate = $this->input->post('rate');
		$this->sale_lib->set_exchange_details($rate);
		
  	$this->_reload($data);
	}

}
?>
