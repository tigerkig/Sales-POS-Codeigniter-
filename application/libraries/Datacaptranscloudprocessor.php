<?php
require_once ("Creditcardprocessor.php");

//TODO: Better loading screen
abstract class Datacaptranscloudprocessor extends Creditcardprocessor
{
	private $iptran_device_id;
	private $merchant_id;
	private $emv_terminal_id;
	private $settings;
	
	function __construct($controller,$settings)
	{
		parent::__construct($controller);	
		$this->controller->load->helper('sale');	
		$current_register_id = $this->controller->Employee->get_logged_in_employee_current_register_id();
		$register_info = $this->controller->Register->get_info($current_register_id);
		$this->iptran_device_id = $register_info->iptran_device_id;
		$this->emv_terminal_id = $register_info->emv_terminal_id ? $register_info->emv_terminal_id : FALSE;
		$this->settings = $settings;
		
		$this->merchant_id = $this->controller->Location->get_info_for_key('emv_merchant_id');
		
		//If we don't have a merchant id set to ip tran device id
		if (!$this->merchant_id)
		{
			$this->merchant_id = $this->iptran_device_id;
		}
		
		if (!$this->controller->session->userdata('sequence_no'))
		{
			$this->controller->session->set_userdata('sequence_no', '0010010010');
		}
		
		$this->sequence_no = $this->controller->session->userdata('sequence_no');
		
	}
	
	public function get_emv_ebt_balance($CardType='Foodstamp')
	{
		$InvoiceNo = $this->_get_session_invoice_no();
		
		$post_data = array(
			'TStream' => array(
				'Transaction' => array(
					'TranDeviceID' =>$this->iptran_device_id,
					'Memo' => 'PHP POS EMV Trans Cloud '.APPLICATION_VERSION,
					'TranType' => 'EBT',
					'TranCode' => 'EMVBalance',
					'CardType' => $CardType,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'MerchantID' => $this->merchant_id,
					'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
					'SecureDevice' => 'CloudEMV1',
					'InvoiceNo' => $InvoiceNo,
					'SequenceNo' => $this->sequence_no,
					'RefNo' => $InvoiceNo,
					'Amount' => array(
						'Purchase' => 0,							
					),						
				)
			),
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$balance = lang('common_error');
		$response = $this->_do_post($post_data);
   	$CmdStatus = $response->RStream->CmdStatus;
		
		if ($CmdStatus !="Error")
		{
			$balance = $response->RStream->Balance;
		}
		
		$this->controller->load->view('sales/datacap_ebt_balance_inquery', array('balance' => $balance));		
	}

	public function do_test_mode($device_id = false,$merchant_id = false)
	{
		if ($device_id)
		{
			$this->iptran_device_id = $device_id;
		}
		
		if ($merchant_id)
		{
			$this->merchant_id = $merchant_id;
		}
	
		$post_data = array(
			'TStream' => array(
				'Admin' => array(
					'MerchantID' => $this->merchant_id,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'TranCode' => 'TestMode',
					'SecureDevice' => 'CloudEMV1',
					'SequenceNo' => $this->sequence_no,
					'TranDeviceID' => $this->iptran_device_id,
				)
			)
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Admin']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$return = $this->_do_post($post_data);
		return $return;
	}

	
	public function do_update_parameters($device_id = false,$merchant_id = false)
	{
		if ($device_id)
		{
			$this->iptran_device_id = $device_id;
		}
		
		if ($merchant_id)
		{
			$this->merchant_id = $merchant_id;
		}
	
		$post_data = array(
			'TStream' => array(
				'Admin' => array(
					'MerchantID' => $this->merchant_id,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'TranCode' => 'LoadParams',
					'SecureDevice' => 'CloudEMV1',
					'SequenceNo' => $this->sequence_no,
					'TranDeviceID' => $this->iptran_device_id,
				)
			)
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Admin']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$return = $this->_do_post($post_data);
		return $return;
	}

	
	public function do_emv_param_download_params($device_id = false,$merchant_id = false)
	{
		if ($device_id)
		{
			$this->iptran_device_id = $device_id;
		}
		
		if ($merchant_id)
		{
			$this->merchant_id = $merchant_id;
		}
	
		$post_data = array(
			'TStream' => array(
				'Admin' => array(
					'MerchantID' => $this->merchant_id,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'TranCode' => 'EMVParamDownload',
					'SecureDevice' => 'CloudEMV1',
					'SequenceNo' => $this->sequence_no,
					'TranDeviceID' => $this->iptran_device_id,
				)
			)
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Admin']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$return = $this->_do_post($post_data);
		return $return;
	}
	
	public function pad_reset()
	{
		$post_data = array(
			'TStream' => array(
				'Transaction' => array(
					'MerchantID' => $this->merchant_id,
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'TranCode' => 'EMVPadReset',
					'SecureDevice' => 'CloudEMV1',
					'SequenceNo' => $this->sequence_no,
					'TranDeviceID' => $this->iptran_device_id,
				)
			)
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$return = $this->_do_post($post_data);
		return $return;
	}
	
	public function start_cc_processing()
	{
		$this->controller->load->view('sales/datacap_emv_trans_cloud_start_cc_processing');
		
	}
	
	public function do_start_cc_processing()
	{	
		$this->pad_reset();
		$cc_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));
		$ebt_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_ebt')));
		$ebt_cash_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_ebt_cash')));
		$tax_amount = to_currency_no_money(($this->controller->sale_lib->get_total() - $this->controller->sale_lib->get_subtotal()) * ($cc_amount / $this->controller->sale_lib->get_total()));
		$customer_id = $this->controller->sale_lib->get_customer();
		$customer_name = '';
		if ($customer_id != -1)
		{
			$customer_info=$this->controller->Customer->get_info($customer_id);
			$customer_name = $customer_info->first_name.' '.$customer_info->last_name;
		}
		
		if(!$this->controller->sale_lib->get_use_saved_cc_info())
		{
			$prompt = $this->controller->sale_lib->get_prompt_for_card();
			$voucher = $this->controller->sale_lib->get_ebt_voucher();
		
			$InvoiceNo = $this->_get_session_invoice_no();
			
			if (is_sale_integrated_ebt_sale())
			{
				if ($ebt_amount > 0)
				{
					$PurchaseAmount = to_currency_no_money($ebt_amount);					
					$TranCode = $voucher ? 'EMVVoucher' : 'EMVSale';
					$TranType = 'EBT';
					$CardType = 'Foodstamp';
				}
				elseif($ebt_amount < 0)
				{
					$PurchaseAmount = to_currency_no_money(abs($ebt_amount));
					$TranCode = $voucher ? 'EMVVoucherReturn' : 'EMVReturn';
					$TranType = 'EBT';
					$CardType = 'Foodstamp';
				}
				elseif($ebt_cash_amount > 0)
				{
					$PurchaseAmount = to_currency_no_money($ebt_cash_amount);					
					$TranCode = 'EMVSale';
					$TranType = 'EBT';
					$CardType = 'Cash';
				}
				elseif($ebt_cash_amount < 0)
				{
					$PurchaseAmount = to_currency_no_money(abs($ebt_cash_amount));
					$TranCode = 'EMVReturn';					
					$TranType = 'EBT';
					$CardType = 'Cash';
				}
			}
			else
			{
				$TranCode = $cc_amount > 0 ? 'EMVSale' : 'EMVReturn';
				$PurchaseAmount = to_currency_no_money(abs($cc_amount));
			}
			
			
			$post_data = array(
				'TStream' => array(
					'Transaction' => array(
						'TranDeviceID' =>$this->iptran_device_id,
						'Memo' => 'PHP POS EMV Trans Cloud '.APPLICATION_VERSION,
						'TranCode' => $TranCode,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'MerchantID' => $this->merchant_id,
						'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
						'SecureDevice' => 'CloudEMV1',
						'InvoiceNo' => $InvoiceNo,
						'SequenceNo' => $this->sequence_no,
						'RefNo' => $InvoiceNo,
						'Account' => array(
							'AcctNo' => $prompt ? 'Prompt' : 'SecureDevice',
						),
						'Amount' => array(
							'Purchase' => $PurchaseAmount,							
						),						
						'RecordNo' => 'RecordNumberRequested',
						'Frequency' => 'OneTime',
						'PartialAuth' => 'Allow',						
					)
				),
			);
			
			if (is_sale_integrated_ebt_sale())
			{
				$post_data['TStream']['Transaction']['TranType'] = $TranType;
				$post_data['TStream']['Transaction']['CardType'] = $CardType;
				
				if ($voucher)
				{
			
					$AuthCode = $this->controller->sale_lib->get_ebt_auth_code();
					$VoucherNo = $this->controller->sale_lib->get_ebt_voucher_no();
					
					$post_data['TStream']['Transaction']['TranInfo']['AuthCode'] = $AuthCode;
					$post_data['TStream']['Transaction']['TranInfo']['VoucherNo'] = $VoucherNo;
				}
			}
			
			if ($this->emv_terminal_id)
			{
				$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
			}
					
		}
		elseif($customer_info->cc_token)
		{
			if ($cc_amount <= 0)
			{
				$this->controller->_reload(array('error' => lang('sales_charging_card_failed_please_try_again')), false);
				return;
			}

			$InvoiceNo = $this->_get_session_invoice_no();
			
			$post_data = array(
				'TStream' => array(
					'Transaction' => array(
						'TranDeviceID' =>$this->iptran_device_id,
						'Memo' => 'PHP POS EMV Trans Cloud '.APPLICATION_VERSION,
						'TranCode' => 'SaleByRecordNo',
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'MerchantID' => $this->merchant_id,
						'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
						'TStream' => 'Transaction',
						'SecureDevice' => 'CloudEMV1',
						'InvoiceNo' => $InvoiceNo,
						'SequenceNo' => $this->sequence_no,
						'Account' => array(
							'AcctNo' => 'SecureDevice'
						),
						'Amount' => array(
							'Purchase' => to_currency_no_money($cc_amount),							
						),
						'RecordNo' => $customer_info->cc_token,
						'RefNo' => $customer_info->cc_ref_no,
						'Frequency' => 'OneTime',
						'PartialAuth' => 'Allow',						
						
					)
				),
			);
			
			if ($this->emv_terminal_id)
			{
				$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
			}
			
			

			if (isset($customer_info) && $customer_info->zip && $this->_is_valid_zip($customer_info->zip))
			{
				$customer_info->zip = str_replace('-','',$customer_info->zip);
				$post_data['TStream']['Transaction']['Zip'] = $customer_info->zip;
			}			
		}
		
	   $response = $this->_do_post($post_data);
	   $CmdStatus = $response->RStream->CmdStatus;
	   $TextResponse = $response->RStream->TextResponse;
		
		if ($CmdStatus == 'Approved')
		{
			//Make sure we remove invoice number in case of partial auth...We need a new invoice number
			$this->controller->sale_lib->clear_invoice_no();
			
		   $ResponseOrigin = $response->RStream->ResponseOrigin;
		   $DSIXReturnCode = $response->RStream->DSIXReturnCode;
		   $MerchantID =  $response->RStream->MerchantID;
		   $AcctNo = $response->RStream->AcctNo;
		   $CardType = $response->RStream->CardType;
		   $TranCode = $response->RStream->TranCode;
		   $AuthCode = @$response->RStream->AuthCode;
		   $CaptureStatus = $response->RStream->CaptureStatus;
		   $RefNo = $response->RStream->RefNo;
		   $InvoiceNo = $response->RStream->InvoiceNo;
		   $OperatorID = $response->RStream->OperatorID;
		   $Purchase = $response->RStream->Purchase;
		   $Authorize = $response->RStream->Authorize;
		   $RecordNo = @$response->RStream->RecordNo;
		   $AcqRefData = @$response->RStream->AcqRefData;
		   $ProcessData = @$response->RStream->ProcessData;
			
			$EntryMethod = @$response->RStream->EntryMethod;
			$ApplicationLabel = @$response->RStream->ApplicationLabel;
			$AID = @$response->RStream->AID;
			$TVR = @$response->RStream->TVR;
			$IAD = @$response->RStream->IAD;
			$TSI = @$response->RStream->TSI;
			$ARC = @$response->RStream->ARC;
			$CVM = @$response->RStream->CVM;
			$Balance = @$response->RStream->Balance;
			
			$this->controller->session->set_userdata('ref_no', $RefNo);
			$this->controller->session->set_userdata('auth_code', $AuthCode);
			$this->controller->session->set_userdata('cc_token', $RecordNo);
			$this->controller->session->set_userdata('acq_ref_data', $AcqRefData);
			$this->controller->session->set_userdata('process_data', $ProcessData);
			$this->controller->session->set_userdata('entry_method', $EntryMethod);
			$this->controller->session->set_userdata('aid', $AID);
			$this->controller->session->set_userdata('tvr', $TVR);
			$this->controller->session->set_userdata('iad', $IAD);
			$this->controller->session->set_userdata('arc', $ARC);
			$this->controller->session->set_userdata('cvm', $CVM);
			$this->controller->session->set_userdata('tsi', $TSI);
			$this->controller->session->set_userdata('application_label', $ApplicationLabel);
			$this->controller->session->set_userdata('tran_type', $TranCode);
			$this->controller->session->set_userdata('ebt_balance', $Balance);
			$this->controller->session->set_userdata('text_response', $TextResponse);
			
			
			//Payment covers purchase amount
			if ($Authorize == $Purchase)
			{
				$this->controller->session->set_userdata('masked_account', $AcctNo);
				$this->controller->session->set_userdata('card_issuer', $CardType);
						
				$info=$this->controller->Customer->get_info($this->controller->sale_lib->get_customer());
				
				//We want to save/update card when we have a customer AND they have chosen to save OR we have a customer and they are using a saved card
				if (($this->controller->sale_lib->get_save_credit_card_info()) && $this->controller->sale_lib->get_customer() != -1 
				|| ($this->controller->sale_lib->get_customer() != -1 && $this->controller->sale_lib->get_use_saved_cc_info()))
				{
					$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
					$customer_info = array('cc_token' => $RecordNo, 'cc_ref_no' => $RefNo, 'cc_preview' => $AcctNo, 'card_issuer' => $CardType);
					$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());
				}
				
				//If the sale payments cover the total, redirect to complete (receipt)
				if ($this->controller->_payments_cover_total())
				{
					$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
					redirect(site_url('sales/complete'));
				}
				else //Change payment type to Partial Credit Card and show sales interface
				{
					$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
				
					$credit_card_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_credit')));
				
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'InvoiceNo' => $InvoiceNo,
						'MerchantID' => $this->merchant_id ,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'Purchase' => $Purchase,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
						'AcqRefData' => $AcqRefData,
						'ProcessData' => $ProcessData,
					);
														
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));												
					$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $credit_card_amount, FALSE, $AcctNo, $CardType, $AuthCode, $RefNo,$RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->pad_reset();
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
				}
			}
			elseif($Authorize < $Purchase)
			{
				$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
			
				if ($CardType =='Foodstamp')
				{
					$foodstamp_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_ebt')));
					
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'InvoiceNo' => $InvoiceNo,
						'MerchantID' => $this->merchant_id ,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'Purchase' => $Authorize,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
						'AcqRefData' => $AcqRefData,
						'ProcessData' => $ProcessData,
					);
			
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_ebt')));
					$this->controller->sale_lib->add_payment(lang('common_partial_ebt'), $foodstamp_amount, FALSE, $AcctNo, $CardType,$AuthCode, $RefNo, $RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->pad_reset();
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
					
				}
				elseif ($CardType =='Cash')
				{
					$foodstamp_amount = to_currency_no_money($this->controller->sale_lib->get_payment_amount(lang('common_ebt_cash')));
					
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'InvoiceNo' => $InvoiceNo,
						'MerchantID' => $this->merchant_id ,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'Purchase' => $Authorize,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
						'AcqRefData' => $AcqRefData,
						'ProcessData' => $ProcessData,
					);
			
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_ebt')));
					$this->controller->sale_lib->add_payment(lang('common_partial_ebt_cash'), $foodstamp_amount, FALSE, $AcctNo, $CardType,$AuthCode, $RefNo, $RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->pad_reset();
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);			
				}
				else
				{
					$partial_transaction = array(
						'AuthCode' => $AuthCode,
						'InvoiceNo' => $InvoiceNo,
						'MerchantID' => $this->merchant_id ,
						'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
						'Purchase' => $Authorize,
						'RefNo' => $RefNo,
						'RecordNo' => $RecordNo,
						'AcqRefData' => $AcqRefData,
						'ProcessData' => $ProcessData,
					);
			
					$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
					$this->controller->sale_lib->add_payment(lang('sales_partial_credit'), $Authorize, FALSE, $AcctNo, $CardType, $AuthCode, $RefNo,$RecordNo,$AcqRefData,$ProcessData,$EntryMethod, $AID, $TVR, $IAD, $TSI, $ARC, $CVM, $TranCode, $ApplicationLabel);
					$this->controller->sale_lib->add_partial_transaction($partial_transaction);
					$this->pad_reset();
					$this->controller->_reload(array('warning' => lang('sales_credit_card_partially_charged_please_complete_sale_with_another_payment_method')), false);	
				}
				
			}
		}
		else
		{
			//If we are using saved token and have a failed response remove token from customer
			if ($this->controller->sale_lib->get_use_saved_cc_info() && $this->controller->sale_lib->get_customer() != -1)
			{
				//If we have failed, remove cc token and cc preview
				$person_info = array('person_id' => $this->controller->sale_lib->get_customer());
				$customer_info = array('cc_token' => NULL, 'cc_ref_no' => NULL, 'cc_preview' => NULL, 'card_issuer' => NULL);
				$this->controller->Customer->save_customer($person_info,$customer_info,$this->controller->sale_lib->get_customer());

				//Clear cc token for using saved cc info
				$this->controller->sale_lib->clear_use_saved_cc_info();
			}
			
			if ($CmdStatus == 'Declined')
			{
				redirect(site_url('sales/declined'));
			}
			else
			{
				$this->controller->_reload(array('error' => $CmdStatus.': '.urldecode($TextResponse)), false);
			}
		}
	}
	
	public function finish_cc_processing()
	{
		//No need for this method as it is handled by start method all at once
		return TRUE;
	}
	
	public function cancel_cc_processing()
	{
		$this->controller->sale_lib->delete_payment($this->controller->sale_lib->get_payment_ids(lang('common_credit')));
		$this->controller->_reload(array('error' => lang('sales_cc_processing_cancelled')), false);
	}
	
	private function void_sale_payment($payment_amount,$auth_code,$ref_no,$token,$acq_ref_data,$process_data)
	{
		$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
		
		$post_data = array(
			'TStream' => array(
				'Transaction' => array(
					'TranDeviceID' =>$this->iptran_device_id,
					'Memo' => 'PHP POS EMV Trans Cloud '.APPLICATION_VERSION,
					'TranCode' => 'VoidSaleByRecordNo',
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'MerchantID' => $this->merchant_id,
					'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
					'TStream' => 'Transaction',
					'SecureDevice' => 'CloudEMV1',
					'InvoiceNo' => $InvoiceNo,
					'RefNo' => $ref_no,
					'RecordNo' => $token,
					'AuthCode' => $auth_code,
					'SequenceNo' => $this->sequence_no,
					'AcqRefData' =>$acq_ref_data,
					'ProcessData' => $process_data,
					'Account' => array(
						'AcctNo' => 'SecureDevice'
					),
					'Amount' => array(
						'Purchase' => to_currency_no_money($payment_amount),							
					),
					'Frequency' => 'OneTime',
				)
			),
		);
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
		}
		
		
		$response = $this->_do_post($post_data);
    	$CmdStatus = $response->RStream->CmdStatus;
		return ($CmdStatus == 'Approved');
	}
	
	private function void_return_payment($payment_amount,$auth_code,$ref_no,$token,$acq_ref_data,$process_data)
	{
		$InvoiceNo = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
		
		//TODO: VoidReturnByRecordNo $payment_amount works with both positive and negative...which one should it be?
		$post_data = array(
			'TStream' => array(
				'Transaction' => array(
					'TranDeviceID' =>$this->iptran_device_id,
					'Memo' => 'PHP POS EMV Trans Cloud '.APPLICATION_VERSION,
					'TranCode' => 'VoidReturnByRecordNo',
					'OperatorID' => (!defined("ENVIRONMENT") or ENVIRONMENT == 'development') ? 'test' : $this->controller->Employee->get_logged_in_employee_info()->person_id,
					'MerchantID' => $this->merchant_id,
					'LaneID' => $this->controller->Employee->get_logged_in_employee_current_register_id()  && $this->controller->Employee->get_logged_in_employee_current_register_id() <=999 ? $this->controller->Employee->get_logged_in_employee_current_register_id()  : 0,
					'TStream' => 'Transaction',
					'SecureDevice' => 'CloudEMV1',
					'InvoiceNo' => $InvoiceNo,
					'RefNo' => $ref_no,
					'RecordNo' => $token,
					'AuthCode' => $auth_code,
					'SequenceNo' => $this->sequence_no,
					'AcqRefData' =>$acq_ref_data,
					'ProcessData' => $process_data,
					'Account' => array(
						'AcctNo' => 'SecureDevice'
					),
					'Amount' => array(
						'Purchase' => to_currency_no_money(abs($payment_amount)),							
					),
					'Frequency' => 'OneTime',
				)
			),
		);
		
		
		if ($this->emv_terminal_id)
		{
			$post_data['TStream']['Transaction']['TerminalID'] = $this->emv_terminal_id;
		}
		
		$response = $this->_do_post($post_data);
	   $CmdStatus = $response->RStream->CmdStatus;
		
		return ($CmdStatus == 'Approved');
	}
	
	public function void_partial_transactions()
	{
		$void_success = true;
		
		if ($partial_transactions = $this->controller->sale_lib->get_partial_transactions())
		{
			for ($k = 0;$k<count($partial_transactions);$k++)
			{
				$partial_transaction = $partial_transactions[$k];
				$void_success = $this->void_sale_payment(to_currency_no_money($partial_transaction['Purchase']),$partial_transaction['AuthCode'],$partial_transaction['RefNo'],$partial_transaction['RecordNo'],$partial_transaction['AcqRefData'],$partial_transaction['ProcessData']);
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
				$void_success = $this->void_sale_payment($payment['payment_amount'], $payment['auth_code'], $payment['ref_no'], $payment['cc_token'],$payment['acq_ref_data'], $payment['process_data']);
			}
			
			return $void_success;
		}
		
		return FALSE;
	}
	
	public function void_return($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_return($sale_id))
		{
			$void_success = true;
			
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			foreach($payments as $payment)
			{
				$void_success = $this->void_return_payment($payment['payment_amount'], $payment['auth_code'], $payment['ref_no'], $payment['cc_token'],$payment['acq_ref_data'], $payment['process_data']);
			}
			
			return $void_success;
		}
		
		return FALSE;	
	}
	
	
	private function _do_post($post_data)
	{
		
		$data_string = json_encode($post_data);                                                                                                                                                                                                        
		$ch = curl_init('https://trancloud.dsipscs.com/ProcessEMVTransaction');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
		
		//Don't verify ssl...just in case a server doesn't have the ability to verify
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERPWD, $this->settings['username'] . ":" . $this->settings['password']);                                                                   
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($data_string))                                                                       
		);
      $response = json_decode(curl_exec($ch)); 
      curl_close($ch);
		
		return $response;
	}
}