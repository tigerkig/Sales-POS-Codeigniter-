<?php
class Sale extends CI_Model
{
	public function __construct()
	{
      parent::__construct();
			$this->load->model('Inventory');	
	}
	
	public function get_info($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}
	
	function has_coupons_for_today()
	{
		$this->load->model('Price_rule');	
		return $this->Price_rule->has_coupons_for_today();
	}
	
	function get_cash_sales_total_for_shift($shift_start, $shift_end)
  {
		$sales_totals = $this->get_sales_totaled_by_id($shift_start, $shift_end);
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();
        
		$this->db->select('sales_payments.sale_id, sales_payments.payment_type, payment_amount, payment_id', false);
		$this->db->from('sales_payments');
		$this->db->join('sales','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		$this->db->order_by('payment_date');
		
		$payments_by_sale = array();
		$sales_payments = $this->db->get()->result_array();
		
		foreach($sales_payments as $row)
		{
        	$payments_by_sale[$row['sale_id']][] = $row;
		}
				
		$payment_data = $this->Sale->get_payment_data($payments_by_sale,$sales_totals);
		
		if (isset($payment_data[lang('common_cash')]))
		{
			return $payment_data[lang('common_cash')]['payment_amount'];
		}
		
		return 0.00;
  }
	
	function get_payment_data($payments_by_sale,$sales_totals)
	{
		static $foreign_language_to_cur_language = array();
		
		if (!$foreign_language_to_cur_language)
		{
			$this->load->helper('directory');
			$language_folder = directory_map(APPPATH.'language',1);
		
			$languages = array();
				
			foreach($language_folder as $language_folder)
			{
				$languages[] = substr($language_folder,0,strlen($language_folder)-1);
			}	
			
			$cur_lang = array();
			foreach($this->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
			{
				$cur_lang[$lang_key] = $cur_lang_value;
			}
		
		
			foreach($languages as $language)
			{
				$this->lang->load('common', $language);
				
				foreach($this->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
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
				
			//Switch back
			$this->lang->switch_to($this->config->item('language'));
		}
		$payment_data = array();
		
		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);
		
		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];		
				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}
					
					if (!isset($foreign_language_to_cur_language[$payment_row['payment_type']]) || !isset($payment_data[$foreign_language_to_cur_language[$payment_row['payment_type']]]))
					{
						$payment_key = NULL;
						
						//Gift card
						if (strpos($payment_row['payment_type'],':') !== FALSE && !isset($foreign_language_to_cur_language[$payment_row['payment_type']]))
						{
			   	     list($giftcard_translation, $giftcard_number) = explode(":",$payment_row['payment_type']);
							 $foreign_language_to_cur_language[$payment_row['payment_type']] = $foreign_language_to_cur_language[$giftcard_translation].':'.$giftcard_number;
							
							if (!isset($payment_data[$foreign_language_to_cur_language[$payment_row['payment_type']]]))
							{
								$payment_data[$foreign_language_to_cur_language[$payment_row['payment_type']]] = array('payment_type' => $foreign_language_to_cur_language[$payment_row['payment_type']], 'payment_amount' => 0 );							
							}
							$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
						}
						elseif(isset($foreign_language_to_cur_language[$payment_row['payment_type']]))
						{
							if (!isset($payment_data[$foreign_language_to_cur_language[$payment_row['payment_type']]]))
							{
								$payment_data[$foreign_language_to_cur_language[$payment_row['payment_type']]] = array('payment_type' => $foreign_language_to_cur_language[$payment_row['payment_type']], 'payment_amount' => 0 );
							}
							
							$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
						}
						else
						{
							if (!isset($payment_data[$payment_row['payment_type']]))
							{
								$payment_data[$payment_row['payment_type']] = array('payment_type' => $payment_row['payment_type'], 'payment_amount' => 0 );
							}
							
							$payment_key = $payment_row['payment_type']; 
						}
					}
					else
					{
						$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
					}
					
					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);
					
					
					if (($total_sale_balance != 0 || 
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$payment_key]['payment_amount'] += $payment_amount;
					}

					$total_sale_balance-=$payment_amount;					
				}
			}
		}
		
		return $payment_data;
	}
	
	function _does_payment_exist_in_array($payment_id, $payments)
	{
		foreach($payments as $payment)
		{
			if($payment['payment_id'] == $payment_id)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
		
	function _get_all_sale_payments($sale_ids)
	{
		$this->load->helper('text');
		$return = array();
		
		if (count($sale_ids) > 0)
		{
			$this->db->select('sales_payments.*, sales.sale_time');
      	$this->db->from('sales_payments');
      	$this->db->join('sales', 'sales.sale_id=sales_payments.sale_id');
			
			$this->db->group_start();
			$sale_ids_chunk = array_chunk($sale_ids,25);
			foreach($sale_ids_chunk as $sale_ids)
			{
				$this->db->or_where_in('sales_payments.sale_id', $sale_ids);
			}
			$this->db->group_end();
			
			
			
			$store_account_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_store_account')));
			$points_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_points')));
			$giftcard_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_giftcard')));
			$check_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_check')));
			$cash_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_cash')));
			$credit_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_credit')));
			$debit_payment_types = implode(',', array_map('add_quotes_and_escape', get_all_language_values_for_key('common_debit')));

			$this->db->order_by("(".$this->db->dbprefix("sales_payments").".payment_type IN ($store_account_payment_types)) DESC,(".$this->db->dbprefix("sales_payments").".payment_type IN ($points_payment_types)) DESC, (SUBSTRING_INDEX(".$this->db->dbprefix("sales_payments").".payment_type,':',1) IN ($giftcard_payment_types)) DESC,"."(".$this->db->dbprefix("sales_payments").".payment_type IN ($check_payment_types)) DESC,"."(".$this->db->dbprefix("sales_payments").".payment_type IN ($cash_payment_types)) DESC,"."(".$this->db->dbprefix("sales_payments").".payment_type IN ($credit_payment_types)) DESC,"."(".$this->db->dbprefix("sales_payments").".payment_type IN ($debit_payment_types)) DESC,payment_date");
			
			$result = $this->db->get()->result_array();
			foreach($result as $row)
			{
				$return[$row['sale_id']][] = $row;
			}
		}
		return $return;
	}
	
	
		
	function get_payment_data_grouped_by_sale($payments_by_sale,$sales_totals)
	{
		static $foreign_language_to_cur_language = array();
		
		if (!$foreign_language_to_cur_language)
		{
		$this->load->helper('directory');
			$language_folder = directory_map(APPPATH.'language',1);
		
			$languages = array();
				
			foreach($language_folder as $language_folder)
			{
				$languages[] = substr($language_folder,0,strlen($language_folder)-1);
			}
		
			$cur_lang = array();
			foreach($this->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
			{
				$cur_lang[$lang_key] = $cur_lang_value;
			}
		
		
			foreach($languages as $language)
			{
				$this->lang->load('common', $language);
			
				foreach($this->get_payment_options_with_language_keys() as $cur_lang_value => $lang_key)
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
				
			//Switch back
			$this->lang->switch_to($this->config->item('language'));
		}
		
		$payment_data = array();
		
		$sale_ids = array_keys($payments_by_sale);
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);
		
		foreach($all_payments_for_sales as $sale_id => $payment_rows)
		{
			if (isset($sales_totals[$sale_id]))
			{
				$total_sale_balance = $sales_totals[$sale_id];
			
				foreach($payment_rows as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}//Negtive sale total postive payment
					elseif($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}			
			
			
					if (!isset($foreign_language_to_cur_language[$payment_row['payment_type']]) || !isset($payment_data[$sale_id][$foreign_language_to_cur_language[$payment_row['payment_type']]]))
					{
						$payment_key = NULL;
						
						//Gift card
						if (strpos($payment_row['payment_type'],':') !== FALSE && !isset($foreign_language_to_cur_language[$payment_row['payment_type']]))
						{
			   	   	list($giftcard_translation, $giftcard_number) = explode(":",$payment_row['payment_type']);
							$foreign_language_to_cur_language[$payment_row['payment_type']] = $foreign_language_to_cur_language[$giftcard_translation].':'.$giftcard_number;							
							
							
							if (!isset($payment_data[$sale_id][$foreign_language_to_cur_language[$payment_row['payment_type']]]))
							{
								$payment_data[$sale_id][$foreign_language_to_cur_language[$payment_row['payment_type']]] = array('sale_id' => $sale_id,'payment_type' => $foreign_language_to_cur_language[$payment_row['payment_type']], 'payment_amount' => 0,'payment_date' => $payment_row['payment_date'], 'sale_time' => $payment_row['sale_time'] );
							}
							$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
							
						}
						elseif(isset($foreign_language_to_cur_language[$payment_row['payment_type']]))
						{
							if (!isset($payment_data[$sale_id][$foreign_language_to_cur_language[$payment_row['payment_type']]]))
							{
								$payment_data[$sale_id][$foreign_language_to_cur_language[$payment_row['payment_type']]] = array('sale_id' => $sale_id,'payment_type' => $foreign_language_to_cur_language[$payment_row['payment_type']], 'payment_amount' => 0,'payment_date' => $payment_row['payment_date'], 'sale_time' => $payment_row['sale_time'] );
							}
							$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
							
						}
						else
						{
							if (!isset($payment_data[$sale_id][$payment_row['payment_type']]))
							{
								$payment_data[$sale_id][$payment_row['payment_type']] = array('sale_id' => $sale_id,'payment_type' => $payment_row['payment_type'], 'payment_amount' => 0,'payment_date' => $payment_row['payment_date'], 'sale_time' => $payment_row['sale_time'] );
							}
							
							$payment_key = $payment_row['payment_type']; 
							
						}
					}
					else
					{
						$payment_key = $foreign_language_to_cur_language[$payment_row['payment_type']];
					}
					
					
					$exists = $this->_does_payment_exist_in_array($payment_row['payment_id'], $payments_by_sale[$sale_id]);
				
					if (($total_sale_balance != 0 || 
						($sales_totals[$sale_id] >= 0 && $payment_row['payment_amount']  < 0) ||
						($sales_totals[$sale_id] < 0 && $payment_row['payment_amount']  >= 0)) && $exists)
					{
						$payment_data[$sale_id][$payment_key]['payment_amount'] += $payment_amount;
					}
				
					$total_sale_balance-=$payment_amount;
				}
			}
		}
		
		return $payment_data;
	}
	
	
	function get_sales_totaled_by_id($shift_start, $shift_end)
	{
		$register_id = $this->Employee->get_logged_in_employee_current_register_id();
		
		$this->db->select('sales.sale_id', false);
      $this->db->from('sales');
      $this->db->join('sales_payments','sales_payments.sale_id=sales.sale_id');
		$this->db->where('sales_payments.payment_date >=', $shift_start);
		$this->db->where('sales_payments.payment_date <=', $shift_end);
		$this->db->where('register_id', $register_id);
		$this->db->where($this->db->dbprefix('sales').'.deleted', 0);
		
		$sale_ids = array();
		$result = $this->db->get()->result();
		foreach($result as $row)
		{
			$sale_ids[] = $row->sale_id;
		}
		
		$sales_totals = array();
		
		if (count($sale_ids) > 0)
		{
			$this->db->select('sale_id, total');
			$this->db->from('sales');
			$this->db->where($this->db->dbprefix('sales').'.sale_id IN('.implode(',',$sale_ids).')');
			
			foreach($this->db->get()->result_array() as $sale_total_row)
			{
				$sales_totals[$sale_total_row['sale_id']] = $sale_total_row['total'];
			}
		}
		
		return $sales_totals;
	}
	 
	function exists($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function update($sale_data, $sale_id)
	{
		$this->db->where('sale_id', $sale_id);
		$success = $this->db->update('sales',$sale_data);
		
		return $success;
	}
	
	function save($items,$customer_id,$employee_id, $sold_by_employee_id, $comment,$discount_reason,$show_comment_on_receipt, $coupons, $payments,$sale_id=false, $suspended = 0, $change_sale_date=false,$balance=0, $store_account_payment = 0)
	{
		
		$exchange_rate = $this->sale_lib->get_exchange_rate() ? $this->sale_lib->get_exchange_rate() : 1;
		$exchange_name = $this->sale_lib->get_exchange_name() ? $this->sale_lib->get_exchange_name() : '';
		$exchange_currency_symbol = $this->sale_lib->get_exchange_currency_symbol() ? $this->sale_lib->get_exchange_currency_symbol() : '';
		$exchange_currency_symbol_location = $this->sale_lib->get_exchange_currency_symbol_location() ? $this->sale_lib->get_exchange_currency_symbol_location() : '';
		$exchange_number_of_decimals = ($this->sale_lib->get_exchange_currency_number_of_decimals() !== '' && $this->sale_lib->get_exchange_currency_number_of_decimals() !== NULL ) ? $this->sale_lib->get_exchange_currency_number_of_decimals() : '';
		$exchange_thousands_separator = $this->sale_lib->get_exchange_currency_thousands_separator() ? $this->sale_lib->get_exchange_currency_thousands_separator() : '';
		$exchange_decimal_point = $this->sale_lib->get_exchange_currency_decimal_point() ? $this->sale_lib->get_exchange_currency_decimal_point() : '';
		
		//Do this before we clear_exchange_details so we have a string with the exchanged currencies
		$payment_types='';
		foreach($payments as $payment_id=>$payment)
		{
			$payment_types=$payment_types.$payment['payment_type'].': '.($exchange_rate == 1 ? to_currency($payment['payment_amount']) : to_currency_as_exchange($payment['payment_amount'])).'<br />';
		}
		
		
		
		//Clear currency exchange so it is saved right values for totals
		$this->sale_lib->clear_exchange_details();
		
		//Reset payments back to regular default currency
		
		for($k=0;$k<count($payments);$k++)
		{
			$payments[$k]['payment_amount'] = $payments[$k]['payment_amount']*pow($exchange_rate,-1);
		}
		
		if ($this->config->item('test_mode'))
		{
			$this->load->library('sale_lib');
			$this->sale_lib->clear_all();
			return lang('sales_test_mode_transaction');
		}
		
		$is_new_sale = $sale_id ? false : true;
		$this->load->model('Item_serial_number');
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
			
		$global_weighted_average_cost = FALSE;
		
		if ($this->config->item('always_use_average_cost_method'))
		{
			$global_weighted_average_cost=  $this->get_global_weighted_average_cost();
			$global_weighted_average_cost = to_currency_no_money($global_weighted_average_cost, 10);
		}
		
		if ($sale_id)
		{
			$before_save_sale_info = $this->get_info($sale_id)->row();
		}
		else
		{
			$before_save_sale_info = FALSE;
		}
		//we need to check the sale library for deleted taxes during sale
		$this->load->library('sale_lib');
		
		if(count($items)==0)
			return -1;
		
		$tier_id = $this->sale_lib->get_selected_tier_id();
		$deleted_taxes = $this->sale_lib->get_deleted_taxes();
		
		if (!$tier_id)
		{
			$tier_id = NULL;
		}
		
		$sale_total_qty = $this->sale_lib->get_total_quantity(); 
		$sale_subtotal = $this->sale_lib->get_subtotal();
		$sale_total = $this->sale_lib->get_total();
		$sale_tax = $sale_total - $sale_subtotal;
		
		$sales_data = array(
			'customer_id'=> $customer_id > 0 ? $customer_id : null,
			'employee_id'=>$employee_id,
			'sold_by_employee_id' => $sold_by_employee_id,
			'payment_type'=>$payment_types,
			'comment'=>$comment,
			'discount_reason'=>$discount_reason,
			'show_comment_on_receipt'=> $show_comment_on_receipt ?  $show_comment_on_receipt : 0,
			'suspended'=>$suspended,
			'deleted' => 0,
			'deleted_by' => NULL,
			'cc_ref_no' => $before_save_sale_info ? $before_save_sale_info->cc_ref_no : '',//Legacy for old payments; set new payments to empty
			'auth_code' => $before_save_sale_info ? $before_save_sale_info->auth_code : '',//Legacy for old payments; set new payments to empty
			'location_id' => $this->Employee->get_logged_in_employee_current_location_id(),
			'register_id' => $this->Employee->get_logged_in_employee_current_register_id(),
			'store_account_payment' => $store_account_payment,
			'tier_id' => $tier_id ? $tier_id : NULL,
			'deleted_taxes' =>  $deleted_taxes? serialize($deleted_taxes) : NULL,
			'total_quantity_purchased' => $sale_total_qty,
			'subtotal' => $sale_subtotal,
			'total' => $sale_total,
			'tax' => $sale_tax,
			'profit' =>0,//Will update when sale complete
			'rule_id' => $this->sale_lib->get_spending_price_rule_id(),
			'rule_discount' => $this->sale_lib->get_spending_price_rule_discount(),
			'exchange_rate' => $exchange_rate,
			'exchange_name' => $exchange_name,
			'exchange_currency_symbol' => $exchange_currency_symbol,
			'exchange_currency_symbol_location' => $exchange_currency_symbol_location,
			'exchange_number_of_decimals' => $exchange_number_of_decimals,
			'exchange_thousands_separator' => $exchange_thousands_separator,
			'exchange_decimal_point' => $exchange_decimal_point,
 		);
		
		$sale_profit = 0;
		
		if ($suspended == 1) //Layaway
		{
			$sales_data['was_layaway'] = 1;
		}
		elseif ($suspended == 2) //estimate
		{
			$sales_data['was_estimate'] = 1;				
		}
		
		if($sale_id)
		{
			$old_data=$this->get_info($sale_id)->row_array();
			$sales_data['sale_time']=$old_data['sale_time'];
		}
		else
		{
			$sales_data['sale_time'] = date('Y-m-d H:i:s');
		}
		
		if($change_sale_date) 
		{
			$sale_time = strtotime($change_sale_date);
			if($sale_time !== FALSE)
			{
				$sales_data['sale_time']=date('Y-m-d H:i:s', strtotime($change_sale_date));
			}
		}
		
		if ($sale_id)
		{
			//If we are NOT a suspended sale and wasn't a layaway
			if (!$this->sale_lib->get_suspended_sale_id() && !$old_data['was_layaway'])
			{
				$override_payment_time = $sales_data['sale_time'];
			}
		}
		elseif($change_sale_date)
		{
			if (!$this->sale_lib->get_suspended_sale_id())
			{
				$override_payment_time = $sales_data['sale_time'];
			}
			
		}
		
		$store_account_payment_amount = 0;
		
		if ($store_account_payment)
		{
			$store_account_payment_amount = $this->sale_lib->get_total();
		}
		
		//Only update balance + store account payments if we are NOT an estimate (suspended = 2)
		if ($suspended != 2)
		{
	   	  //Update customer store account balance
			  if($customer_id > 0 && $balance)
			  {
				  $this->db->set('balance','balance+'.$balance,false);
				  $this->db->where('person_id', $customer_id);
				  $this->db->update('customers');
			  }
			  
		     //Update customer store account if payment made
			if($customer_id > 0 && $store_account_payment_amount)
			{
				$this->db->set('balance','balance-'.$store_account_payment_amount,false);
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers');
			 }
		 }
		 		 
		 $previous_store_account_amount = 0;

		 if ($sale_id !== FALSE)
		 {
			 $previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
		 }
		 
		if ($sale_id)
		{
			//Delete previoulsy sale so we can overwrite data
			$this->delete($sale_id, true);
			
			$this->db->where('sale_id', $sale_id);
			$this->db->update('sales', $sales_data);
		}
		else
		{
			$this->db->insert('sales',$sales_data);
			$sale_id = $this->db->insert_id();
		}
		
		//store_accounts_paid_sales
		$paid_sales = $this->sale_lib->get_paid_store_account_sales();
		if (!empty($paid_sales))
		{
			foreach(array_keys($this->sale_lib->get_paid_store_account_sales()) as $sale_id_paid)
			{
				$this->db->insert('store_accounts_paid_sales',array('sale_id' => $sale_id_paid,'store_account_payment_sale_id' => $sale_id));
			}
		}
		
		//Loyalty systems
		 if ($suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system'))
		 {
		   $sales_data_loy = array();	 
		   $customer_info = $this->Customer->get_info($customer_id);
 		
		if(!$customer_info->disable_loyalty)
		{
			if ($this->config->item('loyalty_option') == 'simple')
			{
				if (!$store_account_payment)
				{
					if ($this->sale_lib->get_redeem())
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount -'.$this->config->item('number_of_sales_for_discount'),false);
						$this->db->update('customers');
						$sales_data_loy['did_redeem_discount'] = 1;				
					}
					else
					{
						$this->db->where('person_id', $customer_id);
						$this->db->set('current_sales_for_discount','current_sales_for_discount +1',false);
						$this->db->update('customers');
					}
				}
			}//End simple
			else
			{
				$current_points = $customer_info->points;
				$current_spend_for_points = $customer_info->current_spend_for_points;
			
				//This is duplicated below; but this is ok so we don't break anything else
				$giftcard_payments_amount = 0;
				foreach($payments as $payment_id=>$payment)
				{
					if ( substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
					{
						$giftcard_payments_amount+=$payment['payment_amount'];
					}
				}
			
				//Don't count points or gift cards or excluded items
				$excluded_items_total = $this->get_excluded_loyalty_items_total();
				
				$sale_total_with_or_without_tax = $this->config->item('loyalty_points_without_tax') ? $this->sale_lib->get_subtotal() : $this->sale_lib->get_total();
				$total_spend_for_sale = $sale_total_with_or_without_tax - $this->sale_lib->get_payment_amount(lang('common_points')) - $giftcard_payments_amount - $excluded_items_total;
	         	
				list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
		
				if (!$store_account_payment && $total_spend_for_sale != 0)
				{
					//If we earn any points
					if ($current_spend_for_points + abs($total_spend_for_sale) >= $spend_amount_for_points)
					{
						$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
						$new_points = (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);
						
						if ($total_spend_for_sale >= 0)
						{
							$new_point_value = $current_points + $new_points;					
						}
						else
						{
							$new_point_value = $current_points - $new_points;							
						}
						
						$new_current_spend_for_points = fmod(($current_spend_for_points + $total_spend_for_sale),$spend_amount_for_points);
					}
					else
					{
						$new_current_spend_for_points = $current_spend_for_points + $total_spend_for_sale;
						$new_point_value = $current_points;
					}
			
					$sales_data_loy['points_gained'] = (int)($new_point_value -  $current_points); 
				}
				else //Don't change any values for store account payment
				{
					$new_current_spend_for_points = $current_spend_for_points;
					$new_point_value = $current_points;
				}
		
				//Redeem points
				if ($payment_amount_points = $this->sale_lib->get_payment_amount(lang('common_points')))
				{
					$points_used = to_currency_no_money($payment_amount_points / $this->config->item('point_value'));
					$new_point_value -= $points_used;
					$sales_data_loy['points_used'] = (int)$points_used;
			
				}
				else
				{
					$sales_data_loy['points_used'] = 0;
				}
		
				$new_point_value = (int) round(to_currency_no_money($new_point_value));
				$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);
		
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));				
			 }
		 	
			if(!empty($sales_data_loy))
			{
				$this->db->where('sale_id', $sale_id);
				$this->db->update('sales', $sales_data_loy);
			}
		 }
	 }//End loyalty
 
		 				
		//Only update store account payments if we are NOT an estimate (suspended = 2)
		if ($suspended != 2)
		{
			// Our customer switched from before; add special logic
			if ($balance && $before_save_sale_info && $before_save_sale_info->customer_id && $before_save_sale_info->customer_id != $customer_id)
			{
				$store_account_transaction = array(
				   'customer_id'=>$customer_id,
				   'sale_id'=>$sale_id,
					'comment'=>$comment,
				   'transaction_amount'=>$balance,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
				
				
				$store_account_transaction = array(
				   'customer_id'=>$before_save_sale_info->customer_id,
				   'sale_id'=>$sale_id,
					'comment'=>$comment,
				   'transaction_amount'=>-$previous_store_account_amount,
					'balance'=>$this->Customer->get_info($before_save_sale_info->customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
				
			}
			elseif($customer_id > 0 && $balance)
			{
			 	$store_account_transaction = array(
			      'customer_id'=>$customer_id,
			      'sale_id'=>$sale_id,
					'comment'=>$comment,
			      'transaction_amount'=>$balance - $previous_store_account_amount,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);
				
				if ($balance - $previous_store_account_amount)
				{
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			 } 
			 elseif ($customer_id > 0 && $previous_store_account_amount) //We had a store account payment before has one...We need to log this
			 {
 			 	$store_account_transaction = array(
 			      'customer_id'=>$customer_id,
 			      'sale_id'=>$sale_id,
 					'comment'=>$comment,
 			      'transaction_amount'=> -$previous_store_account_amount,
 					'balance'=>$this->Customer->get_info($customer_id)->balance,
 					'date' => date('Y-m-d H:i:s')
 				);

 				$this->db->insert('store_accounts',$store_account_transaction);
				
			 } //We switched customers for a sale
			 //insert store account payment transaction 
			if($customer_id > 0 && $store_account_payment)
			{
			 	$store_account_transaction = array(
			        'customer_id'=>$customer_id,
			        'sale_id'=>$sale_id,
					'comment'=>$comment,
			       	'transaction_amount'=> -$store_account_payment_amount,
					'balance'=>$this->Customer->get_info($customer_id)->balance,
					'date' => date('Y-m-d H:i:s')
				);

				$this->db->insert('store_accounts',$store_account_transaction);
			 }
		 }
		 
		$total_giftcard_payments = 0;

		foreach($payments as $payment_id=>$payment)
		{
			//Only update giftcard payments if we are NOT an estimate (suspended = 2)
			if ($suspended != 2)
			{
				if ( substr( $payment['payment_type'], 0, strlen( lang('common_giftcard') ) ) == lang('common_giftcard') )
				{
					/* We have a gift card and we have to deduct the used value from the total value of the card. */
					$splitpayment = explode( ':', $payment['payment_type'] );
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $splitpayment[1] );
	
					$this->Giftcard->update_giftcard_value( $splitpayment[1], $cur_giftcard_value - $payment['payment_amount'] );
					$total_giftcard_payments+=$payment['payment_amount'];
					
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $splitpayment[1], "person" => lang('common_customer'), "old_value" => $cur_giftcard_value, "new_value" => $cur_giftcard_value - $payment['payment_amount'], "type" => 'sale'));
					
				}
			}

			$sales_payments_data = array
			(
				'sale_id'=>$sale_id,
				'payment_type'=>$payment['payment_type'],
				'payment_amount'=>$payment['payment_amount'],
				'payment_date' => isset($override_payment_time) ? $override_payment_time: $payment['payment_date'],
				'truncated_card' => $payment['truncated_card'],
				'card_issuer' => $payment['card_issuer'],
				'auth_code' => $payment['auth_code'],
				'ref_no' => $payment['ref_no'],
				'cc_token' => $payment['cc_token'],
				'acq_ref_data' => $payment['acq_ref_data'],
				'process_data' => $payment['process_data'],	
				'entry_method' => $payment['entry_method'],
				'aid' => $payment['aid'],
				'tvr' => $payment['tvr'],
				'iad' => $payment['iad'],
				'tsi' => $payment['tsi'],
				'arc' => $payment['arc'],
				'cvm' => $payment['cvm'],
				'tran_type' => $payment['tran_type'],
				'application_label' => $payment['application_label'],	
				'ebt_voucher_no' => $payment['ebt_voucher_no'],	
				'ebt_auth_code' => $payment['ebt_auth_code'],	
			);
			
			$this->db->insert('sales_payments',$sales_payments_data);
		}
	
		$has_added_giftcard_value_to_cost_price = $total_giftcard_payments > 0 ? false : true;
		$has_added_points_value_to_cost_price = $this->sale_lib->get_payment_amount(lang('common_points')) > 0 ? false : true;
		
		$store_account_item_id = $this->Item->get_store_account_item_id();
		
		foreach($items as $line=>$item)
		{			
			$sale_item_subtotal = $this->sale_lib->get_item_subtotal($line);
			
			$sale_item_total = $this->sale_lib->get_item_total($line);
			$sale_item_tax = $sale_item_total - $sale_item_subtotal;
			
			if (isset($item['item_id']))
			{
				$cur_item_info = $this->Item->get_info($item['item_id']);
				$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
				//Redeem profit when giftcard is used; so we set cost price to item price
				if ($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description']) && $this->config->item('calculate_profit_for_giftcard_when') == 'redeeming_giftcard')
				{
					$cost_price = $item['price'];					
				}
				elseif(($this->config->item('remove_points_from_profit') && !$has_added_points_value_to_cost_price) || ($this->config->item('remove_points_from_profit') && !$is_new_sale) || ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard' && !$has_added_giftcard_value_to_cost_price && !$is_new_sale))
				{					
					$cost_price = $cur_item_location_info->cost_price ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
				}
				elseif ($item['item_id'] != $store_account_item_id)
				{
					$cost_price = $item['cost_price'];
				}
				else // Set cost price = price so we have no profit
				{
					$cost_price = $item['price'];
				}
				
				
				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}
				
				if($this->config->item('remove_points_from_profit') && !$has_added_points_value_to_cost_price || ($this->config->item('remove_points_from_profit') && !$is_new_sale))
				{
					$cost_price += $this->sale_lib->get_payment_amount(lang('common_points')) / $item['quantity'];
					$has_added_points_value_to_cost_price = true;
				}
				
				$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
				
				if ($cur_item_info->tax_included)
				{
					$this->load->helper('items');
					$item['price'] = get_price_for_item_excluding_taxes($item['item_id'], $item['price']);
				}
				
				$sale_item_profit = $this->sale_lib->get_item_profit($line,$cost_price);
				$sale_profit+=$sale_item_profit;
				$this->load->helper('items');
								
				$sales_items_data = array
				(
					'sale_id'=>$sale_id,
					'item_id'=>$item['item_id'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'serialnumber'=>$item['serialnumber'],
					'quantity_purchased'=>$item['quantity'],
					'discount_percent'=>$item['discount'],
					'item_cost_price' =>  $global_weighted_average_cost === FALSE ? to_currency_no_money($cost_price,10) : $global_weighted_average_cost,
					'item_unit_price'=>$item['price'],
					'regular_item_unit_price_at_time_of_sale' =>$item['regular_price'],
					'commission' => get_commission_for_item($item['item_id'],$item['price'],to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
					'subtotal' => $sale_item_subtotal,
					'total' => $sale_item_total,
					'tax' => $sale_item_tax,
					'profit' =>$sale_item_profit,					
				);
				
				if ($item['serialnumber'])
				{
					$this->Item_serial_number->delete_serial($item['item_id'], $item['serialnumber']);
				}
				
				if (isset($item['rule']['rule_id']) && isset($item['rule']['rule_discount']))
				{
					$sales_items_data['rule_id'] = $item['rule']['rule_id'];
					$sales_items_data['rule_discount'] = $item['rule']['rule_discount'];
				}
				
				$this->db->insert('sales_items',$sales_items_data);
				
				//Only update giftcard payments if we are NOT an estimate (suspended = 2)
				if ($suspended != 2)
				{
					//create giftcard from sales 
					if($item['name']==lang('common_giftcard') && !$this->Giftcard->get_giftcard_id($item['description'])) 
					{ 
						$giftcard_data = array(
							'giftcard_number'=>$item['description'],
							'value'=>$item['price'],
							'description' => $comment,
							'customer_id'=>$customer_id > 0 ? $customer_id : null,
						);
												
						$this->Giftcard->save($giftcard_data);
						
						$employee_info = $this->Employee->get_logged_in_employee_info();
						$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $item['description'], "person"=>$employee_info->first_name . " " . $employee_info->last_name, "new_value" => $item['price'], 'old_value' => 0, "type" => 'create'));
					}
				}
				
				//Only do stock check + inventory update if we are NOT an estimate
				if ($suspended != 2)
				{
					$stock_recorder_check=false;
					$out_of_stock_check=false;
					$email=false;
					$message = '';

					//checks if the quantity is greater than reorder level
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
					{
						$stock_recorder_check=true;
					}
				
					//checks if the quantity is greater than 0
					if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
					{
						$out_of_stock_check=true;
					}
				
					//Update stock quantity IF not a service 
					if (!$cur_item_info->is_service)
					{
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
						$this->Item_location->save_quantity($cur_item_location_info->quantity - $item['quantity'], $item['item_id']);
					}
				
					//Re-init $cur_item_location_info after updating quantity
					$cur_item_location_info = $this->Item_location->get_info($item['item_id']);
				
					//checks if the quantity is out of stock
					if($out_of_stock_check && $cur_item_location_info->quantity <= 0)
					{
						$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
						if ($cur_item_info->item_id)
						{
							$message.="\n";
							$message.= lang('common_item_id').": ".$cur_item_info->item_id;
						}

						if ($cur_item_info->item_number)
						{
							$message.="\n";
							$message.= lang('common_item_number').": ".$cur_item_info->item_number;
						}

						if ($cur_item_info->product_id)
						{
							$message.="\n";
							$message.= lang('common_product_id').": ".$cur_item_info->product_id;
						}
						
						$email=true;
					
					}	
					//checks if the quantity hits reorder level 
					else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
					{
						$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
						if ($cur_item_info->item_id)
						{
							$message.="\n";
							$message.= lang('common_item_id').": ".$cur_item_info->item_id;
						}

						if ($cur_item_info->item_number)
						{
							$message.="\n";
							$message.= lang('common_item_number').": ".$cur_item_info->item_number;
						}

						if ($cur_item_info->product_id)
						{
							$message.="\n";
							$message.= lang('common_product_id').": ".$cur_item_info->product_id;
						}
						
						$email=true;
					}
				
					//send email 
					if($this->Location->get_info_for_key('receive_stock_alert') && $email)
					{			
						$this->load->library('email');
						$config = array();
						$config['mailtype'] = 'text';				
						$this->email->initialize($config);
						$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
						$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email')); 
						
						if ($this->Location->count_all() > 1)
						{
							$message.="\n\n".lang("common_location").': '.$this->Location->get_info_for_key('name');
						}
						
						$this->email->subject(lang('sales_stock_alert_item_name').$this->Item->get_info($item['item_id'])->name);
						$this->email->message($message);	
						$this->email->send();
					}
				
					if (!$cur_item_info->is_service)
					{
						$qty_buy = -$item['quantity'];
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item['item_id'],
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>$qty_buy,
							'location_id' => $this->Employee->get_logged_in_employee_current_location_id() 
						);
						$this->Inventory->insert($inv_data);
					}
				}
			}
			else
			{
				$cur_item_kit_info = $this->Item_kit->get_info($item['item_kit_id']);
				$cur_item_kit_location_info = $this->Item_kit_location->get_info($item['item_kit_id']);


				if(($this->config->item('remove_points_from_profit') && !$has_added_points_value_to_cost_price) || ($this->config->item('remove_points_from_profit') && !$is_new_sale) || ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard' && !$has_added_giftcard_value_to_cost_price && !$is_new_sale))
				{
					$cost_price = $cur_item_kit_location_info->cost_price ? $cur_item_kit_location_info->cost_price : $cur_item_kit_info->cost_price;
				}
				else
				{
					$cost_price = $item['cost_price'];					
				}
				
				if ($this->config->item('calculate_profit_for_giftcard_when') == 'selling_giftcard')
				{
					//Add to the cost price if we are using a giftcard as we have already recorded profit for sale of giftcard
					if (!$has_added_giftcard_value_to_cost_price)
					{
						$cost_price+= $total_giftcard_payments / $item['quantity'];
						$has_added_giftcard_value_to_cost_price = true;
					}
				}
				
				if ($this->config->item('remove_points_from_profit') && !$has_added_points_value_to_cost_price)
				{
					$cost_price += $this->sale_lib->get_payment_amount(lang('common_points')) / $item['quantity'];
					$has_added_points_value_to_cost_price = true;
				}
				
				
				if ($cur_item_kit_info->tax_included)
				{
					$this->load->helper('item_kits');
					$item['price'] = get_price_for_item_kit_excluding_taxes($item['item_kit_id'], $item['price']);
				}
				
				$sale_item_profit = $this->sale_lib->get_item_profit($line,$cost_price);
				$sale_profit+=$sale_item_profit;
				
				
				$this->load->helper('item_kits');
				$sales_item_kits_data = array
				(
					'sale_id'=>$sale_id,
					'item_kit_id'=>$item['item_kit_id'],
					'line'=>$item['line'],
					'description'=>$item['description'],
					'quantity_purchased'=>$item['quantity'],
					'discount_percent'=>$item['discount'],
					'item_kit_cost_price' => $global_weighted_average_cost === FALSE ? ($cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10)) : $global_weighted_average_cost,
					'item_kit_unit_price'=>$item['price'],
					'regular_item_kit_unit_price_at_time_of_sale' =>$item['regular_price'],
					'commission' => get_commission_for_item_kit($item['item_kit_id'],$item['price'],$cost_price === NULL ? 0.00 : to_currency_no_money($cost_price,10), $item['quantity'], $item['discount']),
					'subtotal' => $sale_item_subtotal,
					'total' => $sale_item_total,
					'tax' => $sale_item_tax,
					'profit' =>$sale_item_profit,	
				);


				if (isset($item['rule']['rule_id']))
				{
					$sales_item_kits_data['rule_id'] = $item['rule']['rule_id'];
					$sales_item_kits_data['rule_discount'] = $item['rule']['rule_discount'];
				}
				$this->db->insert('sales_item_kits',$sales_item_kits_data);
				
				foreach($this->Item_kit_items->get_info($item['item_kit_id']) as $item_kit_item)
				{
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
					
					$reorder_level = ($cur_item_location_info && $cur_item_location_info->reorder_level !== NULL) ? $cur_item_location_info->reorder_level : $cur_item_info->reorder_level;
					
					//Only do stock check + inventory update if we are NOT an estimate
					if ($suspended != 2)
					{
						$stock_recorder_check=false;
						$out_of_stock_check=false;
						$email=false;
						$message = '';


						//checks if the quantity is greater than reorder level
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > $reorder_level)
						{
							$stock_recorder_check=true;
						}

						//checks if the quantity is greater than 0
						if(!$cur_item_info->is_service && $cur_item_location_info->quantity > 0)
						{
							$out_of_stock_check=true;
						}

						//Update stock quantity IF not a service item and the quantity for item is NOT NULL
						if (!$cur_item_info->is_service)
						{
							$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
								
							$this->Item_location->save_quantity($cur_item_location_info->quantity - ($item['quantity'] * $item_kit_item->quantity),$item_kit_item->item_id);
						}
					
						//Re-init $cur_item_location_info after updating quantity
						$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id);
				
						//checks if the quantity is out of stock
						if($out_of_stock_check && !$cur_item_info->is_service && $cur_item_location_info->quantity <= 0)
						{
							$message= $cur_item_info->name.' '.lang('sales_is_out_stock').' '.to_quantity($cur_item_location_info->quantity);
							if ($cur_item_info->item_id)
							{
								$message.="\n";
								$message.= lang('common_item_id').": ".$cur_item_info->item_id;
							}

							if ($cur_item_info->item_number)
							{
								$message.="\n";
								$message.= lang('common_item_number').": ".$cur_item_info->item_number;
							}

							if ($cur_item_info->product_id)
							{
								$message.="\n";
								$message.= lang('common_product_id').": ".$cur_item_info->product_id;
							}
							$email=true;

						}	
						//checks if the quantity hits reorder level 
						else if($stock_recorder_check && ($cur_item_location_info->quantity <= $reorder_level))
						{
							$message= $cur_item_info->name.' '.lang('sales_hits_reorder_level').' '.to_quantity($cur_item_location_info->quantity);
							if ($cur_item_info->item_id)
							{
								$message.="\n";
								$message.= lang('common_item_id').": ".$cur_item_info->item_id;
							}

							if ($cur_item_info->item_number)
							{
								$message.="\n";
								$message.= lang('common_item_number').": ".$cur_item_info->item_number;
							}

							if ($cur_item_info->product_id)
							{
								$message.="\n";
								$message.= lang('common_product_id').": ".$cur_item_info->product_id;
							}
							
							$email=true;
						}

						//send email 
						if($this->Location->get_info_for_key('receive_stock_alert') && $email)
						{			
							$this->load->library('email');
							$config = array();
							$config['mailtype'] = 'text';				
							$this->email->initialize($config);
							$this->email->from($this->Location->get_info_for_key('email') ? $this->Location->get_info_for_key('email') : 'no-reply@mg.phppointofsale.com', $this->config->item('company'));
							$this->email->to($this->Location->get_info_for_key('stock_alert_email') ? $this->Location->get_info_for_key('stock_alert_email') : $this->Location->get_info_for_key('email')); 

							if ($this->Location->count_all() > 1)
							{
								$message.="\n\n".lang("common_location").': '.$this->Location->get_info_for_key('name');
							}
							$this->email->subject(lang('sales_stock_alert_item_name').$cur_item_info->name);
							$this->email->message($message);	
							$this->email->send();
						}

						if (!$cur_item_info->is_service)
						{
							$qty_buy = -$item['quantity'] * $item_kit_item->quantity;
							$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
							$inv_data = array
							(
								'trans_date'=>date('Y-m-d H:i:s'),
								'trans_items'=>$item_kit_item->item_id,
								'trans_user'=>$employee_id,
								'trans_comment'=>$sale_remarks,
								'trans_inventory'=>$qty_buy,
								'location_id' => $this->Employee->get_logged_in_employee_current_location_id()
							);
							$this->Inventory->insert($inv_data);
						}
					}
				}
			}
			
			$customer = $this->Customer->get_info($customer_id);
 			if ($customer_id == -1 or $customer->taxable)
 			{
				if (isset($item['item_id']))
				{
					foreach($this->Item_taxes_finder->get_info($item['item_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];
				
						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{	
							 $this->db->insert('sales_items_taxes', array(
								'sale_id' 	=>$sale_id,
								'item_id' 	=>$item['item_id'],
								'line'      =>$item['line'],
								'name'		=>$row['name'],
								'percent' 	=>$row['percent'],
								'cumulative'=>$row['cumulative']
							));
						}
					}
				}
				else
				{
					foreach($this->Item_kit_taxes_finder->get_info($item['item_kit_id']) as $row)
					{
						$tax_name = $row['percent'].'% ' . $row['name'];
				
						//Only save sale if the tax has NOT been deleted
						if (!in_array($tax_name, $this->sale_lib->get_deleted_taxes()))
						{
							$this->db->insert('sales_item_kits_taxes', array(
								'sale_id' 		=>$sale_id,
								'item_kit_id'	=>$item['item_kit_id'],
								'line'      	=>$item['line'],
								'name'			=>$row['name'],
								'percent' 		=>$row['percent'],
								'cumulative'	=>$row['cumulative']
							));
						}
					}					
				}
			}
		}
		
		$this->update(array('profit'=> $sale_profit),$sale_id);
				
		if ($coupons != NULL && !empty($coupons))
		{
			foreach($coupons as $coupon)
			{
				$coupon_data = array(
					'rule_id'=> $coupon['value'],
					'sale_id' => $sale_id,
				);
			
				$this->db->insert('sales_coupons', $coupon_data);
			}
		}
		
		if($this->sale_lib->get_delivery())
		{
			$this->load->model('Person');
			$this->load->model('Delivery');
			
			$delivery_person_info = $this->sale_lib->get_delivery_person_info();
			$delivery_info = $this->sale_lib->get_delivery_info();
			
			$person_id = FALSE;
			if (isset($delivery_person_info['person_id']))
			{
				$person_id = $delivery_person_info['person_id'];
				unset($delivery_person_info['person_id']);
			}
			if($this->Person->save($delivery_person_info,$person_id))
			{
				$delivery_info['sale_id'] = $sale_id;
				$delivery_info['shipping_address_person_id'] = $person_id ? $person_id : $delivery_person_info['person_id'];
				$delivery_info['tax_class_id'] = $this->sale_lib->get_delivery_tax_group_id() ? $this->sale_lib->get_delivery_tax_group_id() : NULL;
				
				if (!isset($delivery_info['shipping_method_id']) || !$delivery_info['shipping_method_id'])
				{
					$delivery_info['shipping_method_id'] = NULL;
				}

				if (!isset($delivery_info['shipping_zone_id']) || !$delivery_info['shipping_zone_id'])
				{
					$delivery_info['shipping_zone_id'] = NULL;
				}
				if((isset($delivery_info['estimated_shipping_date']) && $delivery_info['estimated_shipping_date']) || (isset($delivery_info['estimated_delivery_date']) && $delivery_info['estimated_delivery_date']))
				{
					$delivery_info['status'] = 'scheduled';
				}
				else
				{
					$delivery_info['status'] = 'not_scheduled';
				}
								
				$this->Delivery->save($delivery_info);
			}
		
		}
		
		
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
			
		return $sale_id;				
	}
	
	function update_store_account($sale_id,$undelete=0)
	{
		//update if Store account payment exists
		$this->db->from('sales_payments');
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		$this->db->where_in('payment_type', $store_account_in_all_languages);
		$this->db->where('sale_id',$sale_id);
		$to_be_paid_result = $this->db->get();
		
		$customer_id=$this->get_customer($sale_id)->person_id;
		
		
		if($to_be_paid_result->num_rows() >=1)
		{
			foreach($to_be_paid_result->result() as $to_be_paid)
			{
				if($to_be_paid->payment_amount) 
				{
					//update customer balance
					if($undelete==0)
					{
						$this->db->set('balance','balance-'.$to_be_paid->payment_amount,false);
					}
					else
					{
						$this->db->set('balance','balance+'.$to_be_paid->payment_amount,false);
					}
					$this->db->where('person_id', $customer_id);
					$this->db->update('customers'); 
				
				}
			}			
		}
	}
	
	function update_giftcard_balance($sale_id,$undelete=0)
	{
		//if gift card payment exists add the amount to giftcard balance
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();
			
			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$giftcard_number=str_ireplace(lang('common_giftcard').':','',$row->payment_type);
					$cur_giftcard_value = $this->Giftcard->get_giftcard_value($giftcard_number);
					$value=$row->payment_amount;
					
					$value_to_add_subtract = 0;
					if($undelete==0)
					{
						$this->db->set('value','value+'.$value,false);
						$value_to_add_subtract = $value;		
					}
					else
					{
						$this->db->set('value','value-'.$value,false);
						$value_to_add_subtract = -$value;		
					}
					$this->db->where('giftcard_number', $giftcard_number);
					$this->db->update('giftcards'); 
					$this->Giftcard->log_modification(array('sale_id' => $sale_id, "number" => $giftcard_number, "old_value" => $cur_giftcard_value, "new_value" => $cur_giftcard_value + $value_to_add_subtract, "type" => $undelete ? 'sale_undelete' : 'sale_delete'));
				}
			}
	}
	
	function update_loyalty_simple_count($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];
		
	  $customer_info = $this->Customer->get_info($customer_id);
		
		if($customer_info->disable_loyalty)
		{
			return false;
		}
		
		
	 	if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'simple')
		{
			if ($sale_info['did_redeem_discount'])
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' - ' : ' + ').$this->config->item('number_of_sales_for_discount'),false);
				$this->db->update('customers');				
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$this->db->set('current_sales_for_discount','current_sales_for_discount'.($undelete ? ' + ' : ' - ').'1',false);
				$this->db->update('customers');				
			}
		}
	}
	function update_points($sale_id, $undelete=0)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$store_account_payment = $sale_info['store_account_payment'];
		$customer_id = $sale_info['customer_id'];
		$suspended = $sale_info['suspended'];
	  $customer_info = $this->Customer->get_info($customer_id);
		
		if($customer_info->disable_loyalty)
		{
			return false;
		}
				
		 //Update points information if we have NOT a store account payment and not an estimate and we have a customer and we have loyalty enabled
		 if (!$store_account_payment && $suspended != 2 && $customer_id > 0 && $this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
		 {
		   $customer_info = $this->Customer->get_info($customer_id);
			$current_points = $customer_info->points;
			$current_spend_for_points = $customer_info->current_spend_for_points;
			$total_spend_for_sale = $this->get_sale_total($sale_id,$this->config->item('loyalty_points_without_tax'));
			
			
			//Remove giftcard from spend
			$this->db->from('sales_payments');
			$this->db->like('payment_type',lang('common_giftcard'));
			$this->db->where('sale_id',$sale_id);
			$sales_payment = $this->db->get();
			
			if($sales_payment->num_rows() >=1)
			{
				foreach($sales_payment->result() as $row)
				{
					$total_spend_for_sale-=$row->payment_amount;
				}
			}
			
			$excluded_items_total = $this->get_excluded_loyalty_items_total($sale_id);

			//remove excluded_items amount
			$total_spend_for_sale-=$excluded_items_total;
			
			//update if Store account payment exists
			$this->db->from('sales_payments');
			$this->db->where('payment_type',lang('common_points'));
			$this->db->where('sale_id',$sale_id);
			$points_payment = $this->db->get()->row_array();
			
			$points_payment =	isset($points_payment['payment_amount']) ? $points_payment['payment_amount'] : 0;
			
			//We should NOT count point payments for adding/removing points as we will do this later (at the end of this function)
			$total_spend_for_sale-=$points_payment;
			
		   list($spend_amount_for_points, $points_to_earn) = explode(":",$this->config->item('spend_to_point_ratio'),2);
			
			if($undelete) //Put points back
			{
				//If we earn any points
				if ($current_spend_for_points + abs($total_spend_for_sale) >= $spend_amount_for_points)
				{
					$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
					$new_points = (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);
					
					if ($total_spend_for_sale >= 0)
					{
						$new_point_value = $current_points + $new_points;					
					}
					else
					{
						$new_point_value = $current_points - $new_points;							
					}
					
					$new_current_spend_for_points = fmod(($current_spend_for_points + $total_spend_for_sale),$spend_amount_for_points);
				}
				else
				{
					$new_current_spend_for_points = $current_spend_for_points + $total_spend_for_sale;
					$new_point_value = $current_points;
				}
				
				$this->db->where('person_id', $customer_id);
				$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));
				
				//If we are undeleting a sale; any points used should be removed back
				if ($sale_info['points_used'])
				{
 				  $this->db->set('points','points-'.$sale_info['points_used'],false);
 				  $this->db->where('person_id', $customer_id);
 				  $this->db->update('customers');
				}
				
		 }
		 else //Take points away
		 {
			if ($current_spend_for_points - abs($total_spend_for_sale) >=0) //Just need to remove current spend
			{
				$new_point_value = $current_points;
				$new_current_spend_for_points = $current_spend_for_points - $total_spend_for_sale;
			}
			else
			{
				
				$total_amount_towards_points = $current_spend_for_points + abs($total_spend_for_sale);
				$new_points =  (((($total_amount_towards_points)-fmod(($total_amount_towards_points), $spend_amount_for_points))/$spend_amount_for_points) * $points_to_earn);
				
				if ($total_spend_for_sale >= 0)
				{
					$new_point_value = $current_points - $new_points;					
				}
				else
				{
					$new_point_value = $current_points + $new_points;							
				}
				
				$new_current_spend_for_points = fmod(($current_spend_for_points - $total_spend_for_sale),$spend_amount_for_points);
			}
			
			$new_point_value = (int) round(to_currency_no_money($new_point_value));
			$new_current_spend_for_points = to_currency_no_money($new_current_spend_for_points);
			
			$this->db->where('person_id', $customer_id);
			$this->db->update('customers', array('points' => $new_point_value, 'current_spend_for_points' => $new_current_spend_for_points));
		 	
			
			//If we are deleting a sale; any points used shouold be added back
			if ($sale_info['points_used'])
			{
			  $this->db->set('points','points+'.$sale_info['points_used'],false);
			  $this->db->where('person_id', $customer_id);
			  $this->db->update('customers');
			}
		 }
	  }
	}
	
	function get_sale_total($sale_id,$subtotal = false)
	{		
		$row = $this->get_info($sale_id)->row_array();
		if (isset($row['total']) && !$subtotal)
		{
			return $row['total'];
		}
		elseif(isset($row['subtotal']) && $subtotal)
		{
			return $row['subtotal'];
		}
		
		return 0;
	}
	
	function delete($sale_id, $all_data = false)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		
		//Only update stock quantity if we are NOT an estimate ($suspendd = 2)
		if ($suspended != 2)
		{
			$this->db->select('serialnumber, sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);	
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);
			
				$cur_item_quantity = $this->Item_location->get_location_quantity($sale_item_row['item_id'], $sale_location_id);
			
				if (!$cur_item_info->is_service)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_quantity + $sale_item_row['quantity_purchased'],$sale_item_row['item_id'], $sale_location_id);
					
					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>$sale_item_row['quantity_purchased']
					);
					$this->Inventory->insert($inv_data);
				}
				
				if ($sale_item_row['serialnumber'])
				{
					$this->load->model('Item_serial_number');
					$this->Item_serial_number->add_serial($sale_item_row['item_id'], $sale_item_row['serialnumber']);
				}
				
			}
		}

		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{		
			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);

					if (!$cur_item_info->is_service)
					{
						$cur_item_location_info->quantity = $cur_item_location_info->quantity !== NULL ? $cur_item_location_info->quantity : 0;
					
						$this->Item_location->save_quantity($cur_item_location_info->quantity + ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);

						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);
					}				
				}
			}

			$this->update_store_account($sale_id);
			$this->update_giftcard_balance($sale_id);
			$this->update_points($sale_id);
			$this->update_loyalty_simple_count($sale_id);
			
			//Only insert store account transaction if we aren't deleting the whole sale.
			//When deleting the whole sale save() takes care of this
			if (!$all_data)
			{
		 		$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
			
				if ($previous_store_account_amount)
				{	
					$store_account_transaction = array(
			   		'customer_id'=>$sale_info['customer_id'],
			      'sale_id'=>$sale_id,
						'comment'=>$sale_info['comment'],
			      'transaction_amount'=>-$previous_store_account_amount,
						'balance'=>$this->Customer->get_info($sale_info['customer_id'])->balance,
						'date' => date('Y-m-d H:i:s')
					);
					$this->db->insert('store_accounts',$store_account_transaction);
				}
			}
		}
		
		if ($all_data)
		{
			$this->db->delete('sales_payments', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_items_taxes', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_items', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_item_kits_taxes', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_item_kits', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_coupons', array('sale_id' => $sale_id)); 
			$this->db->delete('sales_deliveries', array('sale_id' => $sale_id)); 
		}

		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 1,'deleted_by'=>$employee_id));
	}
	
	function undelete($sale_id)
	{
		$sale_info = $this->get_info($sale_id)->row_array();
		$suspended = $sale_info['suspended'];
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
	
		//Only update stock quantity + store accounts + giftcard balance if we are NOT an estimate ($suspended = 2)
		if ($suspended != 2)
		{		
			$this->db->select('serialnumber,sales.location_id, item_id, quantity_purchased');
			$this->db->from('sales_items');
			$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
			$this->db->where('sales_items.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_row)
			{
				$sale_location_id = $sale_item_row['location_id'];
				$cur_item_info = $this->Item->get_info($sale_item_row['item_id']);	
				$cur_item_location_info = $this->Item_location->get_info($sale_item_row['item_id'], $sale_location_id);

				if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
				{
					//Update stock quantity
					$this->Item_location->save_quantity($cur_item_location_info->quantity - $sale_item_row['quantity_purchased'],$sale_item_row['item_id']);
		
					$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
					$inv_data = array
					(
						'location_id' => $sale_location_id,
						'trans_date'=>date('Y-m-d H:i:s'),
						'trans_items'=>$sale_item_row['item_id'],
						'trans_user'=>$employee_id,
						'trans_comment'=>$sale_remarks,
						'trans_inventory'=>-$sale_item_row['quantity_purchased']
						);
					$this->Inventory->insert($inv_data);
				}
				
				if ($sale_item_row['serialnumber'])
				{
					$this->load->model('Item_serial_number');
					$this->Item_serial_number->delete_serial($sale_item_row['item_id'], $sale_item_row['serialnumber']);
				}
			}
		
			$this->update_store_account($sale_id,1);
			$this->update_giftcard_balance($sale_id,1);
			$this->update_points($sale_id,1);
			$this->update_loyalty_simple_count($sale_id,1);
			
		 	$previous_store_account_amount = $this->get_store_account_payment_total($sale_id);
			
			if ($previous_store_account_amount)
			{	
			 	$store_account_transaction = array(
			      'customer_id'=>$sale_info['customer_id'],
			      'sale_id'=>$sale_id,
					'comment'=>$sale_info['comment'],
			      'transaction_amount'=>$previous_store_account_amount,
					'balance'=>$this->Customer->get_info($sale_info['customer_id'])->balance,
					'date' => date('Y-m-d H:i:s')
				);
				$this->db->insert('store_accounts',$store_account_transaction);
			}
			
			
			$this->db->select('sales.location_id, item_kit_id, quantity_purchased');
			$this->db->from('sales_item_kits');
			$this->db->join('sales', 'sales.sale_id = sales_item_kits.sale_id');
			$this->db->where('sales_item_kits.sale_id', $sale_id);
		
			foreach($this->db->get()->result_array() as $sale_item_kit_row)
			{
				foreach($this->Item_kit_items->get_info($sale_item_kit_row['item_kit_id']) as $item_kit_item)
				{
					$sale_location_id = $sale_item_kit_row['location_id'];
					$cur_item_info = $this->Item->get_info($item_kit_item->item_id);
					$cur_item_location_info = $this->Item_location->get_info($item_kit_item->item_id, $sale_location_id);
					if (!$cur_item_info->is_service && $cur_item_location_info->quantity !== NULL)
					{
						$this->Item_location->save_quantity($cur_item_location_info->quantity - ($sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity),$item_kit_item->item_id, $sale_location_id);
					
						$sale_remarks =$this->config->item('sale_prefix').' '.$sale_id;
						$inv_data = array
						(
							'location_id' => $sale_location_id,
							'trans_date'=>date('Y-m-d H:i:s'),
							'trans_items'=>$item_kit_item->item_id,
							'trans_user'=>$employee_id,
							'trans_comment'=>$sale_remarks,
							'trans_inventory'=>-$sale_item_kit_row['quantity_purchased'] * $item_kit_item->quantity
						);
						$this->Inventory->insert($inv_data);					
					}
				}
			}	
		}
		
		$this->db->where('sale_id', $sale_id);
		return $this->db->update('sales', array('deleted' => 0, 'deleted_by' => NULL));
	}

	function get_sale_items($sale_id)
	{
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}
	
	function get_sale_coupons($sale_id)
	{
		$this->db->from('sales_coupons');
		$this->db->join('price_rules','price_rules.id=sales_coupons.rule_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('sales_coupons.id');
		return $this->db->get();
	}
	
	
	function get_sale_items_ordered_by_category($sale_id)
	{
		$this->db->select('items.*, sales_items.*, categories.name as category, categories.id as category_id, sales_items.description as sales_items_description');
		$this->db->from('sales_items');
		$this->db->join('items', 'items.item_id = sales_items.item_id');
		$this->db->join('categories', 'categories.id = items.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, items.name');
		return $this->db->get();		
	}

	function get_sale_item_kits($sale_id)
	{
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('line');
		return $this->db->get();
	}
	
	function get_sale_item_kits_ordered_by_category($sale_id)
	{
		$this->db->select('item_kits.*, sales_item_kits.*, categories.name as category,categories.id as category_id');
		$this->db->from('sales_item_kits');
		$this->db->join('item_kits', 'item_kits.item_kit_id = sales_item_kits.item_kit_id');
		$this->db->join('categories', 'categories.id = item_kits.category_id');
		$this->db->where('sale_id',$sale_id);
		$this->db->order_by('categories.name, item_kits.name');
		return $this->db->get();		
	}
	
	function get_sale_items_taxes($sale_id, $line = FALSE)
	{
		$item_where = '';
		
		if ($line)
		{
			$item_where = 'and '.$this->db->dbprefix('sales_items').'.line = '.$line;
		}

		$query = $this->db->query('SELECT name, percent, cumulative, item_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_items_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_items'). ' USING (sale_id, item_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_items_taxes').".sale_id = $sale_id".' '.$item_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_items').'.line,'.$this->db->dbprefix('sales_items').'.item_id,cumulative,name,percent');
		return $query->result_array();
	}
	
	function get_sale_item_kits_taxes($sale_id, $line = FALSE)
	{
		$item_kit_where = '';
		
		if ($line)
		{
			$item_kit_where = 'and '.$this->db->dbprefix('sales_item_kits').'.line = '.$line;
		}
		
		$query = $this->db->query('SELECT name, percent, cumulative, item_kit_unit_price as price, quantity_purchased as quantity, discount_percent as discount '.
		'FROM '. $this->db->dbprefix('sales_item_kits_taxes'). ' JOIN '.
		$this->db->dbprefix('sales_item_kits'). ' USING (sale_id, item_kit_id, line) '.
		'WHERE '.$this->db->dbprefix('sales_item_kits_taxes').".sale_id = $sale_id".' '.$item_kit_where.' '.
		'ORDER BY '.$this->db->dbprefix('sales_item_kits').'.line,'.$this->db->dbprefix('sales_item_kits').'.item_kit_id,cumulative,name,percent');
		return $query->result_array();	
	}

	function get_sale_payments($sale_id)
	{
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get();
	}

	function get_customer($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}
	
	function get_comment($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->comment;
	}
		
	function get_tier_id($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->tier_id;		
	}
	
	function get_comment_on_receipt($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->show_comment_on_receipt;
	}
		
	function get_sold_by_employee_id($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return $this->db->get()->row()->sold_by_employee_id;
	}
	
	public function get_giftcard_value( $giftcardNumber )
	{
		if ( !$this->Giftcard->exists( $this->Giftcard->get_giftcard_id($giftcardNumber)))
			return 0;
		
		$this->db->from('giftcards');
		$this->db->where('giftcard_number',$giftcardNumber);
		return $this->db->get()->row()->value;
	}
	
	function get_all_suspended($suspended_types = array(1,2))
	{				
		$location_id = $this->Employee->get_logged_in_employee_current_location_id();		
		
		$this->db->from('sales');
		$this->db->join('customers', 'sales.customer_id = customers.person_id', 'left');
		$this->db->join('people', 'customers.person_id = people.person_id', 'left');
		$this->db->where('sales.deleted', 0);
		$this->db->where_in('suspended', $suspended_types);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();
				
		$sale_ids = array();
		
		foreach($sales as $sale)
		{
			$sale_ids[] = $sale['sale_id'];
		}
		
		$all_payments_for_sales = $this->_get_all_sale_payments($sale_ids);	
				
		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			$this->db->select('name');
			$this->db->from('item_kits');
			$this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			
			
			$sales[$k]['items'] = implode(', ', $item_names);
			
			
			
			$sales[$k]['last_payment_date'] = lang('common_none');			
			$sale_total = $this->get_sale_total($sales[$k]['sale_id']);		
			$amount_paid = 0;
			$sale_id = $sales[$k]['sale_id'];
						
			$payment_data = array();
			
			if (isset($all_payments_for_sales[$sale_id]))
			{
				$total_sale_balance = $sale_total;		
				
				foreach($all_payments_for_sales[$sale_id] as $payment_row)
				{
					//Postive sale total, positive payment
					if ($sale_total >= 0 && $payment_row['payment_amount'] >=0)
					{
						$payment_amount = $payment_row['payment_amount'] <= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Negative sale total negative payment
					elseif ($sale_total < 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $payment_row['payment_amount'] >= $total_sale_balance ? $payment_row['payment_amount'] : $total_sale_balance;
					}//Positive Sale total negative payment
					elseif($sale_total >= 0 && $payment_row['payment_amount']  < 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}//Negtive sale total postive payment
					elseif($sale_total < 0 && $payment_row['payment_amount']  >= 0)
					{
						$payment_amount = $total_sale_balance != 0 ? $payment_row['payment_amount'] : 0;
					}				
			
					$total_sale_balance-=$payment_amount;	
					$amount_paid+=	$payment_amount;	
					
					
					$sales[$k]['last_payment_date'] = date(get_date_format().' '.get_time_format(), strtotime($payment_row['payment_date']));		
				}
			}
			
			$sales[$k]['sale_total'] = $sale_total;
			$sales[$k]['amount_due'] = $sale_total - $amount_paid;
			$sales[$k]['amount_paid'] = $amount_paid;
		}
		
		return $sales;
		
	}
	
	function count_all()
	{
		$this->db->from('sales');
		$this->db->where('deleted',0);
		
		if ($this->config->item('hide_store_account_payments_in_reports'))
		{
			$this->db->where('store_account_payment',0);
		}
		
		return $this->db->count_all_results();
	}
	
	function get_recent_sales_for_customer($customer_id)
	{
		$return = array();
		
		$this->db->select('sales.*, SUM(quantity_purchased) as items_purchased');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('customer_id', $customer_id);
		$this->db->where('deleted', 0);
		$this->db->order_by('sale_time DESC');
		$this->db->group_by('sales.sale_id');
		$this->db->limit($this->config->item('number_of_recent_sales') ? $this->config->item('number_of_recent_sales') : 10);
		
		foreach($this->db->get()->result_array() as $row)
		{
			$return[] = $row;
		}

		return $return;
	}
	
	function get_store_account_payment_total($sale_id)
	{
		$this->db->select('SUM(payment_amount) as store_account_payment_total', false);
		$this->db->from('sales_payments');
		$this->db->where('sale_id', $sale_id);
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		$this->db->where_in('payment_type', $store_account_in_all_languages);
		
		$sales_payments = $this->db->get()->row_array();	
		
		return $sales_payments['store_account_payment_total'] ? $sales_payments['store_account_payment_total'] : 0;
	}
	
	function get_deleted_taxes($sale_id)
	{
		$this->db->from('sales');
		$this->db->where('sale_id',$sale_id);
		return unserialize($this->db->get()->row()->deleted_taxes);
	}
	
	function get_sales_amount_for_range($start_date, $end_date)
	{
		$this->load->model('Sale');
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$model->setParams(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => 'sales', 'offset' => 0, 'export_excel' => '1'));
		
		$report_order = $this->config->item('report_sort_order') ? $this->config->item('report_sort_order') : 'asc';
		$data = $model->getData();
		$report_data = array();
		
		foreach($data as $row)
		{
			$report_data[] = array('sale_date' => $row['sale_date'], 'sale_amount' => to_currency_no_money($row['total']));
		}
		
		if ($report_order == 'desc')
		{
			$report_data = array_reverse($report_data); 
		}	
		
		return $report_data;
		
	}
	
	function get_quantity_sold_for_item_in_sale($sale_id, $item_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_items');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_id',$item_id);
		$row = $this->db->get()->row_array();
		
		return empty($row) ? 0 : $row['quantity_purchased'];
	}
	
	function get_quantity_sold_for_item_kit_in_sale($sale_id, $item_kit_id)
	{
		$this->db->select('quantity_purchased');
		$this->db->from('sales_item_kits');
		$this->db->where('sale_id',$sale_id);
		$this->db->where('item_kit_id',$item_kit_id);
		$row = $this->db->get()->row_array();
		
		return empty($row) ? 0 : $row['quantity_purchased'];
		
	}
	
	function can_void_cc_sale($sale_id)
	{
		$processor = false;
		
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'heartland')
		{
			$processor = 'heartland';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'worldpay')
		{
			$processor = 'worldpay';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'firstdata')
		{
			$processor = 'firstdata';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'evo')
		{
			$processor = 'evo';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'other_usb')
		{
			$processor = 'other_usb';			
		}
		
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));
		
		$result = $this->db->get()->result_array();
		
		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury' || $processor == 'heartland' || $processor == 'evo' || $processor == 'worldpay' || $processor == 'firstdata' || $processor == 'other_usb')
			{
				if(!($row['auth_code'] && $row['ref_no'] && $row['cc_token'] && $row['acq_ref_data'] && $row['payment_amount'] > 0))
				{
					return FALSE;
				}
			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				if (!$row['ref_no'])
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
	
	function can_void_cc_return($sale_id)
	{
		$processor = false;
		
		
		if ($this->Location->get_info_for_key('credit_card_processor') == 'mercury' || !$this->Location->get_info_for_key('credit_card_processor'))
		{
			$processor = 'mercury';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'heartland')
		{
			$processor = 'heartland';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'worldpay')
		{
			$processor = 'worldpay';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'firstdata')
		{
			$processor = 'firstdata';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'evo')
		{
			$processor = 'evo';			
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'stripe')
		{
			$processor = 'stripe';
		}
		elseif($this->Location->get_info_for_key('credit_card_processor') == 'braintree')
		{
			$processor = 'braintree';
		}
		
		$this->db->from('sales_payments');
		$this->db->where('sale_id',$sale_id);
		$this->db->where_in('payment_type', array(lang('common_credit'),lang('sales_partial_credit')));
		
		$result = $this->db->get()->result_array();
		
		if (empty($result))
		{
			return FALSE;
		}

		foreach($result as $row)
		{
			if ($processor == 'mercury' || $processor == 'heartland' || $processor == 'evo' || $processor == 'worldpay' || $processor == 'firstdata' || $processor == 'other_usb')
			{				
				if(!($row['ref_no'] && $row['cc_token'] && $row['payment_amount'] < 0))
				{
					return FALSE;
				}
				
			}
			elseif($processor == 'stripe' || $processor == 'braintree')
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	function get_item_ids_sold_for_date_range($start_date, $end_date, $supplier_id, $location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->select('sales_items.item_id');
		$this->db->from('sales_items');
		$this->db->join('items', 'sales_items.item_id = items.item_id');
		$this->db->join('sales', 'sales.sale_id = sales_items.sale_id');
		$this->db->where('sale_time BETWEEN '.$this->db->escape($start_date).' and '.$this->db->escape($end_date).' and sales.deleted = 0');
		$this->db->where('supplier_id', $supplier_id);
		$this->db->where('location_id', $location_id);
		$this->db->where('items.deleted',0);
		$item_ids = array();
		
		foreach($this->db->get()->result_array() as $row)
		{
			$item_ids[$row['item_id']] = $row['item_id'];
		}
		
		return array_values($item_ids);
	}
	
	function get_last_sale_id($location_id = FALSE)
	{
		if ($location_id === FALSE)
		{
			$location_id = $this->Employee->get_logged_in_employee_current_location_id();
		}
		
		$this->db->select('sale_id');
		$this->db->from('sales');
		$this->db->where('deleted', 0);
		$this->db->where('location_id', $location_id);
		$this->db->order_by('sale_id DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if ($row = $query->row_array())
		{
			return $row['sale_id'];
		}
		
		return FALSE;
		
	}
	
	function get_global_weighted_average_cost()
	{
		$current_location=$this->Employee->get_logged_in_employee_current_location_id();
		
		$this->db->select('sum(IFNULL('.$this->db->dbprefix('location_items').'.cost_price, '.$this->db->dbprefix('items').'.cost_price) * quantity) / sum(quantity) as weighted_cost', FALSE);
		$this->db->from('items');
		$this->db->join('location_items', 'location_items.item_id = items.item_id and location_id = '.$current_location, 'left');
		$this->db->where('is_service !=', 1);
		$this->db->where('items.deleted', 0);
		
		$row = $this->db->get()->row_array();
		
		return $row['weighted_cost'];
		
	}
	
	function get_payment_options_with_language_keys()
	{		
		
		$payment_options=array(
		lang('common_cash') => 'common_cash',
		lang('common_check') => 'common_check',
		lang('common_giftcard') => 'common_giftcard',
		lang('common_debit') => 'common_debit',
		lang('common_credit') => 'common_credit',
		lang('common_store_account') => 'common_store_account',
		lang('common_points') => 'common_points',
		);
		
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$payment_options[$additional_payment_type] = $additional_payment_type;
		}
		
		return $payment_options;
	}
	
	function get_payment_options()
	{
		$payment_options = array();
		$this->load->library('sale_lib');
		
		$customer_id=$this->sale_lib->get_customer();
		
		if ($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
		}
		
		
		if ($this->Location->get_info_for_key('enable_credit_card_processing'))
		{
			$payment_options=array(
				lang('common_cash') => lang('common_cash'),
				lang('common_check') => lang('common_check'),
				lang('common_credit') => lang('common_credit'),
				lang('common_giftcard') => lang('common_giftcard'));
				
				if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment') 
				{
					$payment_options=array_merge($payment_options,	array(lang('common_store_account') => lang('common_store_account')		
					));
				}
				
				
				if (isset($cust_info) && !$cust_info->disable_loyalty)
				{
					if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
					{
						$payment_options=array_merge($payment_options,	array(lang('common_points') => lang('common_points')));		
					}
				}
				
				if($this->config->item('enable_ebt_payments')) 
				{
					$payment_options=array_merge($payment_options,	array(lang('common_ebt') => lang('common_ebt'),lang('common_ebt_cash') => lang('common_ebt_cash')));
				}
				
				if ($this->config->item('enable_wic'))
				{
					$payment_options=array_merge($payment_options,	array(lang('common_wic') => lang('common_wic')));					
				}
		}
		else
		{
			$payment_options=array(
				lang('common_cash') => lang('common_cash'),
				lang('common_check') => lang('common_check'),
				lang('common_giftcard') => lang('common_giftcard'),
				lang('common_debit') => lang('common_debit'),
				lang('common_credit') => lang('common_credit')
				);
				
				if($this->config->item('customers_store_accounts') && $this->sale_lib->get_mode() != 'store_account_payment') 
				{
					$payment_options=array_merge($payment_options,	array(lang('common_store_account') => lang('common_store_account')		
					));
				}
				if (isset($cust_info) && !$cust_info->disable_loyalty)
				{
					if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced' && count(explode(":",$this->config->item('spend_to_point_ratio'),2)) == 2 &&  isset($cust_info) && $cust_info->points >=1 && $this->sale_lib->get_payment_amount(lang('common_points')) <=0)
					{
						$payment_options=array_merge($payment_options,	array(lang('common_points') => lang('common_points')));		
					}
				}
				
				if($this->config->item('enable_ebt_payments')) 
				{
					$payment_options=array_merge($payment_options,	array(lang('common_ebt') => lang('common_ebt'),lang('common_ebt_cash') => lang('common_ebt_cash')));
				}
				
				if ($this->config->item('enable_wic'))
				{
					$payment_options=array_merge($payment_options,	array(lang('common_wic') => lang('common_wic')));					
				}
		}
		
		foreach($this->Appconfig->get_additional_payment_types() as $additional_payment_type)
		{
			$payment_options[$additional_payment_type] = $additional_payment_type;
		}
		
		$deleted_payment_types = $this->config->item('deleted_payment_types');
		$deleted_payment_types = explode(',',$deleted_payment_types);
		
		foreach($deleted_payment_types as $deleted_payment_type)
		{
			foreach($payment_options as $payment_option)
			{
				if ($payment_option == $deleted_payment_type)
				{
					unset($payment_options[$payment_option]);
				}
			}
		}
		return $payment_options;
	}
	
	function get_unpaid_store_account_sale_ids($customer_id,$limit = 1000)
	{
		$store_account_in_all_languages = get_all_language_values_for_key('common_store_account','common');
		
		$this->db->select('store_accounts.sale_id');
		$this->db->from('store_accounts');
		$this->db->join('sales_payments', 'store_accounts.sale_id = sales_payments.sale_id');
		$this->db->where('store_accounts.customer_id',$customer_id);
		$this->db->where('store_accounts.sale_id IS NOT NULL');
		$this->db->where('store_accounts.sale_id NOT IN (SELECT sale_id FROM '.$this->db->dbprefix('store_accounts_paid_sales').' WHERE sale_id IS NOT NULL)');
		$this->db->where_in('sales_payments.payment_type', $store_account_in_all_languages);
		$this->db->order_by('date');
		
		
		$sale_ids = array();
		
		foreach($this->db->get()->result_array() as $row)
		{
			$sale_ids[] = $row['sale_id'];
		}
			
		return $sale_ids;
	}
	
	function mark_all_unpaid_sales_paid($customer_id = -1)
	{
		$this->db->select('store_accounts.sale_id');
		$this->db->from('store_accounts');
		if ($customer_id != -1)
		{
			$this->db->where('customer_id',$customer_id);
		}
		
		$this->db->where('sale_id is NOT NULL and sale_id NOT IN (SELECT sale_id FROM '.$this->db->dbprefix('store_accounts_paid_sales').' WHERE sale_id is NOT NULL)');
		$this->db->order_by('date');
	
		foreach($this->db->get()->result_array() as $row)
		{
			$this->db->insert('store_accounts_paid_sales',array('sale_id' => $row['sale_id'],'store_account_payment_sale_id' => NULL));
	
		}
	}
	
	function get_discount_reason($sale_id)
	{
	       $this->db->from('sales');
	       $this->db->where('sale_id',$sale_id);
	       return $this->db->get()->row()->discount_reason;
	}
	
	function get_exchange_details($sale_id)
	{
    $this->db->from('sales');
    $this->db->where('sale_id',$sale_id);
    $row = $this->db->get()->row();
					
		return $row->exchange_rate.'|'.$row->exchange_name.'|'.$row->exchange_currency_symbol.'|'.$row->exchange_currency_symbol_location.'|'.$row->exchange_number_of_decimals.'|'.$row->exchange_thousands_separator.'|'.$row->exchange_decimal_point;
		
	}
	
	function get_excluded_loyalty_items_total($sale_id = false)
	{
		if (!$sale_id)
		{
			$this->load->library('sale_lib');
			$items = $this->sale_lib->get_cart();
		}
		else
		{
			$items_for_sale = $this->get_sale_items($sale_id)->result_array(); 
			$item_kits_for_sale = $this->get_sale_item_kits($sale_id)->result_array();
			
			$items = array();
			
			foreach($items_for_sale as $item)
			{
				$items[] = array(
					'item_id' => $item['item_id'],
					'price' => $item['item_unit_price'],
					'quantity' => $item['quantity_purchased'],
					'discount' => $item['discount_percent'],
				);
			}
			
			foreach($item_kits_for_sale as $item)
			{
				$items[] = array(
					'item_kit_id' => $item['item_kit_id'],
					'price' => $item['item_kit_unit_price'],
					'quantity' => $item['quantity_purchased'],
					'discount' => $item['discount_percent'],
				);
			}
			
		}
				
		$total = 0;
		
		foreach($items as $item)
		{
			if (isset($item['item_id']))
			{
				$this->load->helper('items');
				$info = $this->Item->get_info($item['item_id']);
				$item_id_or_line = isset($item['line']) ? $item['line'] : $item['item_id'];
				
				if ($this->config->item('loyalty_points_without_tax'))
				{
					if (!$info->tax_included)
					{
						$price = $item['price'];
					}
					else
					{
						$price = get_price_for_item_excluding_taxes($item_id_or_line,$item['price'], $sale_id);
					}					
				}
				else
				{
					if (!$info->tax_included)
					{
						$price = get_price_for_item_including_taxes($item_id_or_line,$item['price'], $sale_id);
					}
					else
					{
						$price = $item['price'];
					}
				}
			}
			else
			{				
				$this->load->helper('item_kits');
				$info = $this->Item_kit->get_info($item['item_kit_id']);
				$item_kit_id_or_line = isset($item['line']) ? $item['line'] : $item['item_kit_id'];
				
				if ($this->config->item('loyalty_points_without_tax'))
				{
					if (!$info->tax_included)
					{
						$price = $item['price'];
					}
					else
					{
						$price = get_price_for_item_kit_excluding_taxes($item_id_or_line,$item['price'], $sale_id);
					}
				}
				else
				{				
					if (!$info->tax_included)
					{
						$price = get_price_for_item_kit_including_taxes($item_kit_id_or_line,$item['price'], $sale_id);
					}
					else
					{
						$price = $item['price'];
					}
				}
			}
			
			if ($info->disable_loyalty)
			{
				$total+=to_currency_no_money($price*$item['quantity']-$price*$item['quantity']*$item['discount']/100,10);
			}
		}
		
		return to_currency_no_money($total);
	}
}
?>
