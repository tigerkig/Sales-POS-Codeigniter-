<?php $this->load->view("partial/header"); ?>
<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}

$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');

?>

<div class="manage_buttons hidden-print">
	<div class="row">
		<div class="col-md-6">
			<span class="hidden-print search no-left-border">
				<ul class="list-inline print-buttons">
					<li></li>
					
					<?php
					 if ($this->Employee->has_module_action_permission('receivings', 'edit_receiving', $this->Employee->get_logged_in_employee_info()->person_id)){
				   		$edit_recv_url = $suspended ? 'unsuspend' : 'change_recv';
						echo '<li>';
						echo form_open("receivings/$edit_recv_url/".$receiving_id_raw,array('id'=>'receivings_change_form')); ?>
						<button class="btn btn-primary btn-lg hidden-print" id="edit_recv" > <?php echo lang('receivings_edit'); ?> </button>
							</form>		
						</li>
				
					<?php }	?>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="barcode_labels_button" onClick="window.location='<?php echo site_url('items/generate_barcodes_labels_from_recv/'.$receiving_id_raw); ?>'"; > <?php echo lang('common_barcode_labels'); ?> </button>						
					</li>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="barcode_sheet_button" onClick="window.open('<?php echo site_url('items/generate_barcodes_from_recv/'.$receiving_id_raw); ?>','_blank');" > <?php echo lang('common_barcode_sheet'); ?> </button>						
					</li>
					
					<li>
						<?php if (!empty($supplier_email)) { ?>
							<?php echo anchor('receivings/email_receipt/'.$receiving_id_raw, $is_po ? lang('receivings_email_po') : lang('common_email_receipt'), array('id' => 'email_receipt','class' => 'btn btn-primary btn-lg hidden-print'));?>
						<?php }?>
					</li>
					
				</ul>
			</span>
		</div>
		<div class="col-md-6">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline print-buttons">
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onClick="do_print()" > <?php echo lang('common_print'); ?> </button>							
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="new_receiving_button_1" onclick="window.location='<?php echo site_url('receivings'); ?>'" > <?php echo lang('receivings_new_receiving'); ?> </button>
						</li>
					</ul>
				</div>
			</div>				
		</div>
	</div>
</div>

<div class="row manage-table receipt_<?php echo $this->config->item('receipt_text_size') ? $this->config->item('receipt_text_size') : 'small';?>" id="receipt_wrapper">
	<div class="col-md-12" id="receipt_wrapper_inner">
		<div class="panel panel-piluku">
			<div class="panel-body panel-pad">
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<ul class="list-unstyled invoice-address">
							<?php if($company_logo) {?>
								<li id="company_logo" class="invoice-logo">
									<?php echo img(array('src' => $this->Appfile->get_url_for_file($company_logo))); ?>
								</li>
							<?php } ?>
							<li id="company_name"  class="company-title"><?php echo $company; ?></li>
							<li id="company_address"><?php echo nl2br($this->Location->get_info_for_key('address',isset($override_location_id) ? $override_location_id : FALSE)); ?></li>
							<li id="company_phone"><?php echo $this->Location->get_info_for_key('phone',isset($override_location_id) ? $override_location_id : FALSE); ?></li>
							<li id="sale_receipt"><?php echo $is_po ? lang('receivings_purchase_order') : $receipt_title; ?></li>
							<li id="sale_time"><?php echo $transaction_time ?></li>
						</ul>
					</div>
					<!--  sales-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			            <ul class="list-unstyled invoice-detail">
							<li id="sale_id"><span><?php echo $is_po ? lang('receivings_purchase_order') : lang('receivings_id').": "; ?></span><?php echo $receiving_id; ?></li>
							<li id="employee"><span><?php echo lang('common_employee').": "; ?></span><?php echo $employee; ?></li>
			            </ul>
			        </div>
			        <?php if(isset($supplier) || isset($transfer_to_location)) { ?>
			        <div class="col-md-4 col-sm-4 col-xs-12">
						<ul class="list-unstyled invoice-address invoiceto">
							<?php if(isset($supplier)) { ?>
								<li id="supplier"><?php echo lang('common_supplier').": ".$supplier; ?></li>
								<?php if(!empty($supplier_address_1)){ ?><li><?php echo lang('common_address'); ?> : <?php echo $supplier_address_1. ' '.$supplier_address_2; ?></li><?php } ?>
								<?php if (!empty($supplier_city)) { echo '<li>'.$supplier_city.' '.$supplier_state.', '.$supplier_zip.'</li>';} ?>
								<?php if (!empty($supplier_country)) { echo '<li>'.$supplier_country.'</li>';} ?>			
								<?php if(!empty($supplier_phone)){ ?><li><?php echo lang('common_phone_number'); ?> : <?php echo $supplier_phone; ?></li><?php } ?>
								<?php if(!empty($supplier_email)){ ?><li><?php echo lang('common_email'); ?> : <?php echo $supplier_email; ?></li><?php } ?>
								
							<?php } ?>
							<?php if(isset($transfer_to_location)) { ?>
								<li id="transfer_from"><span><?php echo lang('receivings_transfer_from').': ' ?></span><?php echo $transfer_from_location ?></li>
								<li id="transfer_to"><span><?php echo lang('receivings_transfer_to').': ' ?></span><?php echo $transfer_to_location ?></li>
							<?php } ?>
						</ul>
			        </div>
			        <?php } ?>
				</div>
				
					<?php
		    		$x_col = 6;
		    		$xs_col = 4;
		    		if($discount_exists)
		    		{
		    			$x_col = 4;
		    			$xs_col = 3;

							if($this->config->item('wide_printer_receipt_format'))
							{
				    		$x_col = 4;
								$xs_col = 2;
							}
		    		}
						else
						{
							if($this->config->item('wide_printer_receipt_format'))
							{
				    		$x_col = 6;
								$xs_col = 2;
							}
						}
					?>
			    <!-- invoice heading-->
			    <div class="invoice-table">
			        <div class="row">
			            <div class="<?php echo $this->config->item('wide_printer_receipt_format') ? 'col-md-'.$x_col . ' col-sm-' .$x_col . ' col-xs-'.$x_col : 'col-md-12 col-sm-12 col-xs-12' ?>">
			                <div class="invoice-head item-name"><?php echo lang('common_item_name'); ?></div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
			                <div class="invoice-head text-right item-price"><?php echo lang('common_price'); ?></div>
			            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head text-right item-qty"><?php echo lang('common_quantity'); ?></div>
			            </div>
									<?php if($discount_exists) { ?>
				            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
				                <div class="invoice-head text-right item-discount"><?php echo lang('common_discount_percent'); ?></div>
				            </div>
			            <?php } ?>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head pull-right item-total gift_receipt_element"><?php echo lang('common_total'); ?></div>
			            </div>
			        </div>
			    </div>
			    <?php foreach(array_reverse($cart, true) as $line=>$item) { ?>
					
					<?php
					$item_number_for_receipt = false;
					
					if ($this->config->item('show_item_id_on_receipt'))
					{
						switch($this->config->item('id_to_show_on_sale_interface'))
						{
							case 'number':
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : '';
							break;
						
							case 'product_id':
							$item_number_for_receipt = array_key_exists('product_id', $item) ? H($item['product_id']) : ''; 
							break;
						
							case 'id':
							$item_number_for_receipt = array_key_exists('item_id', $item) ? H($item['item_id']) : ''; 
							break;
						
							default:
							$item_number_for_receipt = array_key_exists('item_number', $item) ? H($item['item_number']) : '';
							break;
						}
					}
					?>
				    <!-- invoice items-->
				    <div class="invoice-table-content">
				        <div class="row">
			            	<div class="<?php echo $this->config->item('wide_printer_receipt_format') ? 'col-md-'.$x_col . ' col-sm-' .$x_col . ' col-xs-'.$x_col : 'col-md-12 col-sm-12 col-xs-12' ?>">
				                <div class="invoice-content invoice-con">
				                    <div class="invoice-content-heading"><?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></div>
									<?php if (!$this->config->item('hide_desc_on_receipt') && !$item['description']=="" ) {?>
				                    	<div class="invoice-desc"><?php echo H($item['description']); ?></div>
				                    <?php } ?>
									<?php if (isset($item['serialnumber']) && $item['serialnumber'] !="") { ?>
				                    	<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
									<?php } ?>
				                </div>
				            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
				                <div class="invoice-content item-price text-right"><?php echo to_currency($item['price']); ?></div>
				            </div>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
				                <div class="invoice-content item-qty text-right"><?php echo to_quantity($item['quantity']); ?></div>
				            </div>
							
							<?php if($discount_exists) { ?>
			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
					                <div class="invoice-content item-discount text-right"><?php echo to_quantity($item['discount']); ?></div>
					            </div>							
							<?php } ?>

			            <div class="col-md-<?php echo $xs_col; ?> col-sm-<?php echo $xs_col; ?> col-xs-<?php echo $xs_col; ?> gift_receipt_element">
			                	<div class="invoice-content item-total pull-right">
													
													<?php if ($this->config->item('indicate_taxable_on_receipt') && $item['taxable'] && !empty($taxes))
													{
														echo '<small>*'.lang('common_taxable').'</small>';
													}
													?>
													
													<?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?>
												
												</div>
			            	</div>
				        </div>					
				    </div>
			    <?php } ?>
				 
					<div class="row">
			            <div class="col-md-12 col-sm-12 col-xs-12">
			                <div class="text-center">
										<?php echo $comment; ?>
			                </div>
			            </div>
			        </div>
			    </div>
				 
			    <div class="invoice-footer panel-pad">
			    	<?php if ($this->config->item('charge_tax_on_recv')) {?>
				        <div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading sub-total-heading"><?php echo lang('common_sub_total'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value"><?php echo to_currency($subtotal); ?></div>
				            </div>
				        </div>
				        <?php if ($this->config->item('group_all_taxes_on_receipt')) { ?>
							<?php 
								$total_tax = 0;
								foreach($taxes as $name=>$value) 
								{
									$total_tax+=$value;
							 	}
							?>	
								<div class="row">
						            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
						                <div class="invoice-footer-heading tax-heading"><?php echo lang('common_tax'); ?></div>
						            </div>
						            <div class="col-md-2 col-sm-2 col-xs-4">
						                <div class="invoice-footer-value"><?php echo to_currency($total_tax); ?></div>
						            </div>
						        </div>						
						<?php }else {?>
							<?php foreach($taxes as $name=>$value) { ?>
								<div class="row">
						            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
						                <div class="invoice-footer-heading tax-heading"><?php echo $name; ?></div>
						            </div>
						            <div class="col-md-2 col-sm-2 col-xs-4">
						                <div class="invoice-footer-value"><?php echo to_currency($value); ?></div>
						            </div>
						        </div>
							<?php } ?>
						<?php } ?>
				    <?php } ?>
				    <div class="row">
			            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
			                <div class="invoice-footer-heading total-heading"><?php echo lang('common_total'); ?></div>
			            </div>
			            <div class="col-md-2 col-sm-2 col-xs-4">
			                <div class="invoice-footer-value"><?php echo to_currency($total); ?></div>
			            </div>
			        </div>
					
			        <?php
						foreach($payments as $payment_id=>$payment)
						{ 
					?>
						<div class="row">
				            <div class="col-md-offset-4 col-sm-offset-4 col-md-4 col-sm-4 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-4 col-xs-4">
								<div class="invoice-footer-value"><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?></div>																				
				            </div>
							
				            <div class="col-md-2 col-sm-2 col-xs-4">
								<div class="invoice-footer-value invoice-payment"><?php echo to_currency($payment['payment_amount']); ?></div>
				            </div>							
						</div>
					<?php
						}
					?>
										
			        <?php if(isset($amount_change)) { ?>
						<div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo lang('common_amount_tendered'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value"><?php echo to_currency($amount_tendered); ?></div>
				            </div>
				        </div>
				        <div class="row">
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-heading"><?php echo lang('common_change_due'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value"><?php echo $amount_change; ?></div>
				            </div>
				        </div>
					<?php } ?>
					
					<?php if (isset($supplier_balance_for_sale) && $supplier_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
					
						<div class="row">						
				            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-4 col-md-2 col-sm-2 col-xs-48">
				                <div class="invoice-footer-value"><?php echo lang('receivings_supplier_account_balance'); ?></div>
				            </div>
				            <div class="col-md-2 col-sm-2 col-xs-4">
				                <div class="invoice-footer-value invoice-payment"><?php echo to_currency($supplier_balance_for_sale); ?></div>
				            </div>
				        </div>
					<?php
					}
					?>
					
			    </div>
								
				
			    <!-- invoice footer -->
			    <div class="row">
			        <div class="col-md-12 col-sm-12 col-xs-12">
			            <?php if (!$this->config->item('hide_barcode_on_sales_and_recv_receipt')) {?>
				            <div class="invoice-policy" id="barcode">
				            	<?php echo "<img src='".site_url('barcode')."?barcode=$receiving_id&text=$receiving_id' />"; ?>
				            </div>
				        <?php } ?>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">

$("#edit_recv").click(function(e)
{
	e.preventDefault();
	bootbox.confirm(<?php echo json_encode(lang('receivings_edit_confirm')); ?>, function(result)
	{
		if (result)
		{
			$("#receivings_change_form").submit();
		}
	});
});

$("#email_receipt").click(function()
{
	$.get($(this).attr('href'), function()
	{
		show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
		
	});
	
	return false;
});

<?php if ($this->config->item('print_after_receiving') && $this->uri->segment(2) == 'complete')
{
?>
$(window).load(function()
{
	do_print();
});
<?php
}
?>
function do_print()
{
	window.print();
	<?php
	if ($this->config->item('redirect_to_sale_or_recv_screen_after_printing_receipt'))
	{
	?>
 	window.location = '<?php echo site_url('receivings'); ?>';
	<?php
	}
	?>
}
</script>
