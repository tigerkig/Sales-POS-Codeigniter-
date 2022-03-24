<?php
class receiving_lib
{
	var $CI;
	
	//This is used when we need to change the recv state and restore it before changing it (The case of showing a receipt in the middle of a recv)
	var $recv_state;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function get_cart()
	{
		if($this->CI->session->userdata('cartRecv') === NULL)
			$this->set_cart(array());

		return $this->CI->session->userdata('cartRecv');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cartRecv',$cart_data);
	}
	
	//Alain Multiple Payments
	function get_payments()
	{
		if($this->CI->session->userdata('recv_payments') === NULL)
			$this->set_payments(array());

		return $this->CI->session->userdata('recv_payments');
	}

	//Alain Multiple Payments
	function set_payments($payments_data)
	{
		$this->CI->session->set_userdata('recv_payments',$payments_data);
	}
	
	function empty_payments()
	{
		$this->CI->session->unset_userdata('recv_payments');
	}
	
	function get_selected_payment()
	{
		if($this->CI->session->userdata('recv_selected_payment') === NULL)
			$this->set_selected_payment('');

		return $this->CI->session->userdata('recv_selected_payment');
	}
	
	function set_selected_payment($payment)
	{
		$this->CI->session->set_userdata('recv_selected_payment',$payment);
	}
	
	function clear_selected_payment() 	
	{
		$this->CI->session->unset_userdata('recv_selected_payment');
	}
	
	function add_payment($payment_type,$payment_amount,$payment_date = false)
	{
		$payments=$this->get_payments();
		$payment = array(
			'payment_type'=>$payment_type,
			'payment_amount'=>$payment_amount,
			'payment_date' => $payment_date !== FALSE ? $payment_date : date('Y-m-d H:i:s'),
		);
		
		$payments[]=$payment;
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
	
	
	function get_supplier()
	{
		if(!$this->CI->session->userdata('supplier'))
			$this->set_supplier(-1);

		return $this->CI->session->userdata('supplier');
	}

	function set_supplier($supplier_id)
	{
		if (is_numeric($supplier_id))
		{
			$this->CI->session->set_userdata('supplier',$supplier_id);
		}
	}
	
	function get_po()
	{
		return $this->CI->session->userdata('is_po') ? $this->CI->session->userdata('is_po') : FALSE;
	}
	
	function set_po($value)
	{
		$this->CI->session->set_userdata('is_po', $value);
	}

	function get_location()
	{
		if(!$this->CI->session->userdata('location'))
			$this->set_location(-1);

		return $this->CI->session->userdata('location');
	}

	function set_location($location_id)
	{
		if (is_numeric($location_id))
		{
			$this->CI->session->set_userdata('location',$location_id);
		}
	}
	
	function get_email_receipt() 
	{
		return $this->CI->session->userdata('supplier_email_receipt');
	}

	function set_email_receipt($email_receipt) 
	{
		$this->CI->session->set_userdata('supplier_email_receipt', $email_receipt);
	}

	function clear_email_receipt() 	
	{
		$this->CI->session->unset_userdata('supplier_email_receipt');
	}	
		
	function get_mode()
	{
		if(!$this->CI->session->userdata('recv_mode'))
			$this->set_mode('receive');

		return $this->CI->session->userdata('recv_mode');
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
	
	function set_mode($mode)
	{
		$this->CI->session->set_userdata('recv_mode',$mode);
		
		if ($mode == 'purchase_order')
		{
			$this->set_po(TRUE);
		}
		else
		{
			$this->set_po(FALSE);
		}
	}	
	
	function set_comment($comment)
	{
		$this->CI->session->set_userdata('recv_comment',$comment);
	}
	
	function get_comment()
	{
		return $this->CI->session->userdata('recv_comment') ? $this->CI->session->userdata('recv_comment') : '';
	}
	
	function set_suspended_receiving_id($suspended_receiving_id)
	{
		$this->CI->session->set_userdata('suspended_recv_id',$suspended_receiving_id);
	}
	
	function get_suspended_receiving_id()
	{
		return $this->CI->session->userdata('suspended_recv_id');
	}
	
	
	function get_change_recv_id()
	{
		return $this->CI->session->userdata('change_recv_id');
	}
	
	function set_change_recv_id($change_recv_id)
	{
		$this->CI->session->set_userdata('change_recv_id',$change_recv_id);
	}
	
	function delete_change_recv_id()
	{
		$this->CI->session->unset_userdata('change_recv_id');
	}
	
	
	function get_deleted_taxes() 
	{
		$recv_deleted_taxes = $this->CI->session->userdata('recv_deleted_taxes') ? $this->CI->session->userdata('recv_deleted_taxes') : array();
		return $recv_deleted_taxes;
	}

	function add_deleted_tax($name) 
	{
		$recv_deleted_taxes = $this->CI->session->userdata('recv_deleted_taxes') ? $this->CI->session->userdata('recv_deleted_taxes') : array();
		
		if (!in_array($name, $recv_deleted_taxes))
		{
			$recv_deleted_taxes[] = $name;
		}
		$this->CI->session->set_userdata('recv_deleted_taxes', $recv_deleted_taxes);
	}
	
	function set_deleted_taxes($recv_deleted_taxes)
	{
		$this->CI->session->set_userdata('recv_deleted_taxes', $recv_deleted_taxes);		
	}
		
	function get_change_receiving_date() 
	{
		return $this->CI->session->userdata('change_receiving_date') ? $this->CI->session->userdata('change_receiving_date') : '';
	}
	function clear_change_receiving_date() 	
	{
		$this->CI->session->unset_userdata('change_receiving_date');
		
	}
	function clear_change_receiving_date_enable() 	
	{
		$this->CI->session->unset_userdata('change_receiving_date_enable');
	}
	
	function set_change_receiving_date_enable($change_receiving_date_enable)
	{
		$this->CI->session->set_userdata('change_receiving_date_enable',$change_receiving_date_enable);
	}
	
	function get_change_receiving_date_enable() 
	{
		return $this->CI->session->userdata('change_receiving_date_enable') ? $this->CI->session->userdata('change_receiving_date_enable') : '';
	}
	
	function set_change_receiving_date($change_receiving_date)
	{
		$this->CI->session->set_userdata('change_receiving_date',$change_receiving_date);
	}
	
	function clear_deleted_taxes() 	
	{
		$this->CI->session->unset_userdata('recv_deleted_taxes');
	}
	
	function add_paid_store_account_receiving($receiving_id)
	{
		$paid_store_account_receivings = $this->get_paid_store_account_receivings();
		$paid_store_account_receivings[$receiving_id] = TRUE;
		$this->CI->session->set_userdata('paid_store_account_receivings',$paid_store_account_receivings);
	}
	
	function get_paid_store_account_receivings()
	{
		if($this->CI->session->userdata('paid_store_account_receivings') === NULL)
		{
			return array();
		}
		
		return $this->CI->session->userdata('paid_store_account_receivings');
	}
	
	function remove_paid_store_account_receiving($receiving_id)
	{
		$paid_store_account_receivings = $this->get_paid_store_account_receivings();
		
		if (isset($paid_store_account_receivings[$receiving_id]))
		{
			unset($paid_store_account_receivings[$receiving_id]);
			$this->CI->session->set_userdata('paid_store_account_receivings',$paid_store_account_receivings);
			return true;
		}
		
		return false;
	}
	
	function clear_all_paid_store_account_receivings()
	{
		$this->CI->session->unset_userdata('paid_store_account_receivings');		
	}
	
	function add_scale_item($scan)
	{
		$data = parse_scale_data($scan);
		return $this->add_item($data['item_id'],to_quantity($data['cost_quantity']),0,0,$data['cost_price']);
	}
	
	
	function add_item($item_id,$quantity=1,$quantity_received=NULL,$discount=0,$price=null,$description=null,$serialnumber=null,$expire_date= null, $force_add = FALSE, $line = FALSE)
	{
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
		
		if(!$item_id)
		{
			return false;
		}

		//Get items in the receiving so far.
		$items = $this->get_cart();

    //We need to loop through all items in the cart.
    //If the item is already there, get it's key($updatekey).
    //We also need to get the next key that we are going to use in case we need to add the
    //item to the list. Since items can be deleted, we can't use a count. we use the highest key + 1.

    $maxkey=0;                       //Highest key so far
    $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$insertkey=0;                    //Key to use for new entry.
		$updatekey=0;                    //Key to use to update(quantity)

		foreach ($items as $item)
		{
      //We primed the loop so maxkey is 0 the first time.
      //Also, we have stored the key in the element itself so we can compare.
      //There is an array function to get the associated key for an element, but I like it better
      //like that!

			if($maxkey <= $item['line'])
			{
				$maxkey = $item['line'];
			}

			if($item['item_id']==$item_id)
			{
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
			}
		}

		$insertkey=$maxkey+1;

		$cur_item_info = $this->CI->Item->get_info($item_id);
		$cur_item_location_info = $this->CI->Item_location->get_info($item_id);
		
		$default_cost_price = ($cur_item_location_info && $cur_item_location_info->cost_price) ? $cur_item_location_info->cost_price : $cur_item_info->cost_price;
		
		if ($expire_date === NULL && $cur_item_info->expire_days !== NULL)
		{
			$expire_date = date(get_date_format(), strtotime('+ '.$cur_item_info->expire_days. ' days'));
		}
		elseif($expire_date !== NULL)
		{
			$expire_date = date(get_date_format(),strtotime($expire_date));
		}
		else
		{
			$expire_date = NULL;
		}
		
		$tax_info = $this->CI->Item_taxes_finder->get_info($item_id,'receiving');
		
		//array records are identified by $insertkey and item_id is just another field.
		$item = array(($line === FALSE ? $insertkey : $line)=>
		array(
			'item_id'=>$item_id,
			'line'=>$line === FALSE ? $insertkey : $line,
			'name'=>$this->CI->Item->get_info($item_id)->name,
			'size' => $this->CI->Item->get_info($item_id)->size,
			'item_number'=>$cur_item_info->item_number,
			'product_id' => $cur_item_info->product_id,
			'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
			'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
			'quantity'=>$quantity,
			'quantity_received' => $quantity_received,
         'discount'=>$discount,
			'price'=>$price!=null ? $price: $default_cost_price,
			'default_cost_price' => $default_cost_price,
			'expire_date' => $expire_date,
			'cost_price_preview' => $this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount),
			'selling_price' => $cur_item_info->unit_price,
			'taxable' => !empty($tax_info),
			)
		);
		
		//Item already exists
		if($itemalreadyinsale && !$this->CI->config->item('do_not_group_same_items') && isset($items[$line === FALSE ? $updatekey : $line]))
		{
			$items[$line === FALSE ? $updatekey : $line]['quantity']+=$quantity;
			$items[$updatekey]['cost_price_preview']=$this->calculate_average_cost_price_preview($item_id, $price!=null ? $price: $default_cost_price, $quantity,$discount);
		}
		else
		{
			//add to existing array
			$items+=$item;
		}

		$this->set_cart($items);
		return true;

	}

	function edit_item($line,$description = NULL,$serialnumber = NULL,$expire_date= null, $quantity = NULL,$quantity_received=NULL,$discount = NULL,$price = NULL, $selling_price = NULL)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			if ($description !== NULL ) {
				$items[$line]['description'] = $description;
			}
			if ($serialnumber !== NULL ) {
				$items[$line]['serialnumber'] = $serialnumber;
			}
			
			if ($expire_date !== NULL ) {
				
				if ($expire_date == '')
				{
					$items[$line]['expire_date'] = NULL;
				}
				else
				{
					$items[$line]['expire_date'] =  date(get_date_format(),strtotime($expire_date));
				}
			}
			
			if ($quantity_received !== NULL ) {
				$items[$line]['quantity_received'] = $quantity_received;
			}
			
			if ($quantity !== NULL ) {
				$items[$line]['quantity'] = $quantity;
			}
			if ($discount !== NULL ) {
				$items[$line]['discount'] = $discount;
			}
			if ($price !== NULL ) {
				$items[$line]['price'] = $price;
			}
			
			if ($selling_price !== NULL ) {
				$items[$line]['selling_price'] = $selling_price;
			}
			
			$items[$line]['cost_price_preview']=$this->calculate_average_cost_price_preview($items[$line]['item_id'], $items[$line]['price'], $items[$line]['quantity'],$items[$line]['discount']);
			
			 
			$this->set_cart($items);
			
			return true;
		}

		return false;
	}

	function is_valid_receipt($receipt_receiving_id)
	{
		//RECV #
		$pieces = explode(' ',$receipt_receiving_id);

		if(count($pieces)==2 && $pieces[0] == 'RECV')
		{
			return $this->CI->Receiving->exists($pieces[1]);
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

	function return_entire_receiving($receipt_receiving_id)
	{
		//POS #
		$pieces = explode(' ',$receipt_receiving_id);
		$receiving_id = $pieces[1];

		$this->empty_cart();
		$this->delete_supplier();
		
		$receiving_taxes = $this->get_taxes($receiving_id);
		
		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$item_info = $this->CI->Item->get_info($row->item_id);
			$price_to_use = $row->item_unit_price;						
			
			//For return quantity_received needs to be NULL so the quantity gets updated correctly
			$this->add_item($row->item_id,-$row->quantity_purchased,NULL,$row->discount_percent,$price_to_use,$row->description,$row->serialnumber, $row->expire_date, TRUE, $row->line);
			
		}
		$recv_info = $this->CI->Receiving->get_info($receiving_id)->row_array();
		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);
		$this->set_location($recv_info['transfer_to_location_id']);
		
		if ($recv_info['transfer_to_location_id'])
		{
			$this->set_mode('transfer');
		}
		
		$this->set_deleted_taxes($this->CI->Receiving->get_deleted_taxes($receiving_id));
	}
	
	function add_item_kit($external_item_kit_id_or_item_number,$quantity=1)
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
		
		foreach ($this->CI->Item_kit_items->get_info($item_kit_id) as $item_kit_item)
		{
			$this->add_item($item_kit_item->item_id, $item_kit_item->quantity*$quantity);
		}
		
		return TRUE;
	}

	function copy_entire_receiving($receiving_id, $is_receipt = false)
	{
		$this->empty_cart();
		$this->delete_supplier();
		$receiving_taxes = $this->get_taxes($receiving_id);

		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$item_info = $this->CI->Item->get_info($row->item_id);
			$price_to_use = $row->item_unit_price;
			$this->add_item($row->item_id,$row->quantity_purchased,$row->quantity_received,$row->discount_percent,$price_to_use,$row->description,$row->serialnumber, $row->expire_date, TRUE, $row->line);
			
		}
		
		foreach($this->CI->Receiving->get_recv_payments($receiving_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount, $row->payment_date);
			
		}
		
		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);
		
		$recv_info = $this->CI->Receiving->get_info($receiving_id)->row_array();
		$this->set_comment($recv_info['comment']);
		$this->set_location($recv_info['transfer_to_location_id']);
		
		if ($recv_info['transfer_to_location_id'])
		{
			$this->set_mode('transfer');
		}
		$this->set_deleted_taxes($this->CI->Receiving->get_deleted_taxes($receiving_id));
		
	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cartRecv');
	}

	function delete_supplier()
	{
		$this->CI->session->unset_userdata('supplier');
	}

	function delete_location()
	{
		$this->CI->session->unset_userdata('location');
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('recv_mode');
	}
	
	function delete_comment()
	{
		$this->CI->session->unset_userdata('recv_comment');
	}
	
	function delete_suspended_receiving_id()
	{
		$this->CI->session->unset_userdata('suspended_recv_id');	
	}
	
	function clear_po()
	{
		$this->CI->session->unset_userdata('is_po');	
	}
	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->delete_supplier();
		$this->delete_location();
		$this->delete_comment();
		$this->delete_suspended_receiving_id();
		$this->clear_deleted_taxes();
		$this->clear_change_receiving_date_enable();
		$this->clear_change_receiving_date();
		$this->delete_change_recv_id();
		$this->clear_po();
		$this->clear_email_receipt();
		$this->empty_payments();
		$this->clear_selected_payment();
		$this->clear_all_paid_store_account_receivings();
	}
	
	function save_current_recv_state()
	{
		$this->recv_state = $this->CI->session->all_userdata();
	}
	
	function restore_current_recv_state()
	{
		if (isset($this->recv_state))
		{
			$this->CI->session->set_userdata($this->recv_state);
		}
	}
	
	function get_taxes($receiving_id = false)
	{
		$taxes = array();
		
		if (!$this->CI->config->item('charge_tax_on_recv'))
		{
			return $taxes;
		}
		
		if ($receiving_id)
		{
			$taxes_from_receiving = $this->CI->Receiving->get_receiving_items_taxes($receiving_id);
			foreach($taxes_from_receiving as $key=>$tax_item)
			{
				$name = $tax_item['percent'].'% ' . $tax_item['name'];
			
				if ($tax_item['cumulative'])
				{
					$prev_tax = ($tax_item['price']*$tax_item['quantity']-$tax_item['price']*$tax_item['quantity']*$tax_item['discount']/100)*(($taxes_from_receiving[$key-1]['percent'])/100);
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

			foreach($this->get_cart() as $line=>$item)
			{
				$price_to_use = $this->_get_price_for_item_in_cart($item);		
				
				$tax_info = $this->CI->Item_taxes_finder->get_info($item['item_id'],'receiving');
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
	
	function get_total_quantity()
	{
		$cart_count = 0;
	  foreach($this->get_cart() as $line=>$item) 
	  { 
	 	 	$cart_count = $cart_count + $item['quantity'];
  	}
	 
	 return $cart_count;
	}
	
	function get_item_subtotal($line)
	{
		$cart = $this->get_cart();
		$item = $cart[$line];
		$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
	  $subtotal=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
		
 		return to_currency_no_money($subtotal);
	}
	
	function get_subtotal($receiving_id = FALSE)
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
			$price_to_use = $this->_get_price_for_item_in_cart($item, $receiving_id);
		   $subtotal+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
		}
		
		return to_currency_no_money($subtotal);
	}
		
	function get_item_total($line)
	{
		$cart = $this->get_cart();
		$item = $cart[$line];
		
		$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
	  $total=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
		
		$tax_info = $this->CI->Item_taxes_finder->get_info($item['item_id'],'receiving');
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
	
	function get_item_profit($line,$item_cost_price)
	{
		$cart = $this->get_cart();
		$item = $cart[$line];
		$price_to_use = $this->_get_price_for_item_in_cart($item, FALSE);
	  $profit=to_currency_no_money(($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100) - ($item_cost_price*$item['quantity']));
		
 		return to_currency_no_money($profit);
	}
	
	
	function get_total($receiving_id = false)
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
			 $price_to_use = $this->_get_price_for_item_in_cart($item, $receiving_id);
		    $total+=to_currency_no_money($price_to_use*$item['quantity']-$price_to_use*$item['quantity']*$item['discount']/100);
		}

		foreach($this->get_taxes($receiving_id) as $tax)
		{
			$total+=$tax;
		}
		
		return to_currency_no_money($total);
	}
		
	function _get_price_for_item_in_cart($item, $receiving_id = FALSE)
	{
		$price_to_use = $item['price'];		
		return $price_to_use;
	}
	
	
	function calculate_average_cost_price_preview($item_id, $price, $additional_quantity,$discount_percent)
	{
		if ($this->CI->config->item('calculate_average_cost_price_from_receivings'))
		{
			$this->CI->load->model('Receiving');
			return $this->CI->Receiving->calculate_cost_price_preview($item_id, $price, $additional_quantity, $discount_percent);
		}
		return false;
	}
	
	function get_amount_due($recv_id = false)
	{
		$amount_due=0;
		$payment_total = $this->get_payments_totals();
		$sales_total=$this->get_total($recv_id);
		$amount_due=to_currency_no_money($sales_total - $payment_total);
		return $amount_due;
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
}
?>