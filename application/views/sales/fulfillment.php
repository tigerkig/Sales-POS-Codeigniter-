<?php $this->load->view("partial/header"); ?>
<?php
	$this->load->helper('sale');
	$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
	$website = ($website = $this->Location->get_info_for_key('website', isset($override_location_id) ? $override_location_id : FALSE)) ? $website : $this->config->item('website');
	$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');
?>

<div class="manage_buttons hidden-print">
	<div class="row">
		<?php if(rawurldecode($sale_id_raw) != lang('sales_test_mode_transaction')) { ?>
		<div class="col-md-6">
			<div class="hidden-print search no-left-border">
				<ul class="list-inline print-buttons">
					<li></li>
					<li>
						<button class="btn btn-primary btn-lg hidden-print" id="fufillment_sheet_button" onclick="window.open('<?php echo site_url("sales/receipt/$sale_id_raw"); ?>', 'blank');" > <?php echo lang('sales_receipt'); ?></button>
					</li>
				</ul>
			</div>
		</div>
		<?php } ?>
		<div class="col-md-<?php echo rawurldecode($sale_id_raw) != lang('sales_test_mode_transaction') ? 6 : 12;?>">	
			<div class="buttons-list">
				<div class="pull-right-btn">
					<ul class="list-inline">
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="print_button" onclick="print_fulfillment()" > <?php echo lang('common_print'); ?> </button>		
						</li>
						<li>
							<button class="btn btn-primary btn-lg hidden-print" id="new_sale_button_1" onclick="window.location='<?php echo site_url('sales'); ?>'" > <?php echo lang('sales_new_sale'); ?> </button>	
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
			        <!-- from address-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			            <ul class="list-unstyled invoice-address">
			                <?php if($company_logo) {?>
			                	<li class="invoice-logo">
									<?php echo img(array('src' => $this->Appfile->get_url_for_file($company_logo))); ?>
			                	</li>
			                <?php } ?>
			                <li class="company-title"><?php echo $company; ?></li>
			                <li><?php echo $this->Location->get_info_for_key('address', isset($override_location_id) ? $override_location_id : FALSE); ?></li>
			                <li><?php echo $this->Location->get_info_for_key('phone', isset($override_location_id) ? $override_location_id : FALSE); ?></li>
			                <?php if($website) { ?>
								<li><?php echo $website; ?></li>
							<?php } ?>
							<li class="title">
								<span class="pull-left"> <?php echo lang('sales_fulfillment_sheet'); ?></span>
								<span class="pull-right"><?php echo $transaction_time ?></span>
							</li>
			            </ul>
			        </div>
			        <!--  sales-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			            <ul class="list-unstyled invoice-detail">
							<li class="big-screen-title">
								 <?php echo lang('sales_fulfillment_sheet'); ?>
								 <br>
								 <strong><?php echo $transaction_time ?></strong>
							</li>
				      <li><span><?php echo lang('common_sale_id').":"; ?></span><?php echo rawurldecode($sale_id); ?></li>
							
							<?php if (isset($sale_type)) { ?>
								<li><?php echo $sale_type; ?></li>
							<?php } ?>
							
							<li><span><?php echo lang('common_employee').":"; ?></span><?php echo $employee; ?></li>
							<?php 
							if($this->Location->get_info_for_key('enable_credit_card_processing',isset($override_location_id) ? $override_location_id : FALSE))
							{
								echo '<li id="merchant_id"><span>'.lang('common_merchant_id').'</span>: '.$this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE).'</li>';
							}
							?>
			            </ul>
			        </div>
							
			        <!-- to address-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
			          <?php if(isset($customer)) { ?>
				        <ul class="list-unstyled invoice-address invoiceto">
									<li class="invoice-to"><?php echo lang('sales_invoice_to');?>:</li>
									<li><?php echo lang('common_customer').": ".$customer; ?></li>
									<?php if(!empty($customer_company)) { ?><li><?php echo lang('common_company').": ".$customer_company; ?></li><?php } ?>
									
									<?php if (!$this->config->item('remove_customer_contact_info_from_receipt')) { ?>
										<?php if(!empty($customer_address_1)){ ?><li><?php echo lang('common_address'); ?> : <?php echo $customer_address_1. ' '.$customer_address_2; ?></li><?php } ?>
										<?php if (!empty($customer_city)) { echo '<li>'.$customer_city.' '.$customer_state.', '.$customer_zip.'</li>';} ?>
										<?php if (!empty($customer_country)) { echo '<li>'.$customer_country.'</li>';} ?>			
										<?php if(!empty($customer_phone)){ ?><li><?php echo lang('common_phone_number'); ?> : <?php echo $customer_phone; ?></li><?php } ?>
										<?php if(!empty($customer_email)){ ?><li><?php echo lang('common_email'); ?> : <?php echo $customer_email; ?></li><?php } ?>
									<?php } ?>
				        </ul>
								
						<?php } ?>
			        </div>
							
			        <!-- delivery address-->
			        <div class="col-md-4 col-sm-4 col-xs-12">
						
			          <?php if(isset($delivery_person_info)) { ?>
				        <ul class="list-unstyled invoice-address" style="margin-bottom:2px;">
								
								
									<li class="invoice-to"><?php echo lang('deliveries_shipping_address');?>:</li>
									<li><?php echo lang('common_name').": ".$delivery_person_info['first_name'].' '.$delivery_person_info['last_name']; ?></li>
									
									<?php if(!empty($delivery_person_info['address_1']) || !empty($delivery_person_info['address_2'])){ ?><li><?php echo lang('common_address'); ?> : <?php echo $delivery_person_info['address_1']. ' '.$delivery_person_info['address_2']; ?></li><?php } ?>
									<?php if (!empty($delivery_person_info['city'])) { echo '<li>'.$delivery_person_info['city'].' '.$delivery_person_info['state'].', '.$delivery_person_info['zip'].'</li>';} ?>
									<?php if (!empty($delivery_person_info['country'])) { echo '<li>'.$delivery_person_info['country'].'</li>';} ?>			
									<?php if(!empty($delivery_person_info['phone'])){ ?><li><?php echo lang('common_phone_number'); ?> : <?php echo $delivery_person_info['phone']; ?></li><?php } ?>
									<?php if(!empty($delivery_person_info['email'])){ ?><li><?php echo lang('common_email'); ?> : <?php echo $delivery_person_info['email']; ?></li><?php } ?>
				        </ul>
								<?php } ?>
			        </div>
							
			    </div>
			    <!-- invoice heading-->
				<?php 
					$x_col = 6;
					$xs_col = 4;
					if($discount_exists)
					{
						$x_col = 4;
						$xs_col = 3;
					}
				?>
				<div class="invoice-table">
			        <div class="row">
			            <div class="col-md-<?php echo $x_col; ?> col-sm-<?php echo $x_col; ?> col-xs-12">
			                <div class="invoice-head invoice-heading"><?php echo lang('common_item_name'); ?></div>
			            </div>
			            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head"><?php echo lang('common_price'); ?></div>
			            </div>
			            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head"><?php echo lang('common_quantity'); ?></div>
			            </div>
						<?php if($discount_exists) { ?>
				            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
				                <div class="invoice-head"><?php echo lang('common_discount_percent'); ?></div>
				            </div>
			            <?php } ?>
			            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
			                <div class="invoice-head pull-right"><?php echo lang('common_total'); ?></div>
			            </div>
			        </div>
			    </div>				
			    <!-- Items -->
			    <?php if (count($sales_items) > 0) { ?>
					<div class="row">
			        	<div class="col-md-12 item-kits-heading">
			        		<?php echo lang('module_items'). ' ('.lang('common_without_tax').')'; ?>
			        	</div>

					</div>
				
			    <!-- Items table -->
			    <?php
				}
	    		$current_category = FALSE;

				foreach($sales_items as $item)
				{
					
				?>
			    <!-- invoice items-->
			    <div class="invoice-table-content">
			        <div class="row">
			        	<?php if ($current_category != $item['category_id']) { ?>
			        	<div class="col-md-12 category-heading">
			        		<?php echo $this->Category->get_full_path($item['category_id']);?>
			        	</div>
			        	<?php 
						$current_category = $item['category_id']; 
							} 
						?>
					
					<div class="col-md-<?php echo $x_col; ?> col-sm-<?php echo $x_col; ?> col-xs-12">
					    <div class="invoice-content invoice-con">
					        <div class="invoice-content-heading"><?php echo $item['name']; ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?></div>
									<?php if (!$this->config->item('hide_desc_on_receipt') && isset($item['description']) && !$item['description']=="" ) {?>
		                    	<div class="invoice-desc"><?php echo $item['description']; ?></div>
		                    <?php } ?>
							
		                    <?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
		                    	<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
		                    <?php } ?>
							
        
					    </div>
					</div>					
					
				    <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
						<div class="invoice-content"><?php echo to_currency($item['item_unit_price']); ?></div>
		            </div>
					
		            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
		                <div class="invoice-content"><?php echo to_quantity($item['quantity_purchased']); ?></div>
		            </div>
					
					
					<?php if($discount_exists) { ?>
					<div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
					    <div class="invoice-content"><?php echo to_quantity($item['discount_percent']); ?></div>
					</div>
					<?php } ?>
					
					
					<div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
		            
		                <div class="invoice-content pull-right"><?php echo to_currency($item['item_unit_price']*$item['quantity_purchased']-$item['item_unit_price']*$item['quantity_purchased']*$item['discount_percent']/100); ?></div>
			        </div>				
					</div>
			    </div>
								
			    <?php } ?>

			    <!-- Item Kits -->
			    <?php if (count($sales_item_kits) > 0) { ?>
					<div class="row">
			        	<div class="col-md-12 item-kits-heading">
			        		<?php echo lang('module_item_kits'). ' ('.lang('common_without_tax').')'; ?>
			        	</div>

					</div>
			    <?php
	    		$current_category = FALSE;

				foreach($sales_item_kits as $item)
				{
					
				?>
				
			    <!-- invoice items-->
			    <div class="invoice-table-content">
			        <div class="row">
			        	<?php if ($current_category != $item['category_id']) { ?>
			        	<div class="col-md-12 category-heading">
			        		<?php echo $this->Category->get_full_path($item['category_id']);?>
			        	</div>
			        	<?php 
						$current_category = $item['category_id']; 
							} 
						?>
					
					<div class="col-md-<?php echo $x_col; ?> col-sm-<?php echo $x_col; ?> col-xs-12">
					    <div class="invoice-content invoice-con">
					        <div class="invoice-content-heading"><?php echo $item['name']; ?></div>
		                    <?php if(isset($item['description']) && $item['description'] !=""){ ?>
		                    	<div class="invoice-desc"><?php echo $item['description']; ?></div>
		                    <?php } ?>
							
		                    <?php if(isset($item['serialnumber']) && $item['serialnumber'] !=""){ ?>
		                    	<div class="invoice-desc"><?php echo $item['serialnumber']; ?></div>
		                    <?php } ?>
							
        
					    </div>
					</div>					
					
				    <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
						<div class="invoice-content"><?php echo to_currency($item['item_kit_unit_price']); ?></div>
		            </div>
					
		            <div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
		                <div class="invoice-content"><?php echo to_quantity($item['quantity_purchased']); ?></div>
		            </div>
					
					
					<?php if($discount_exists) { ?>
					<div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
					    <div class="invoice-content"><?php echo to_quantity($item['discount_percent']); ?></div>
					</div>
					<?php } ?>
					
					
					<div class="col-md-2 col-sm-2 col-xs-<?php echo $xs_col; ?>">
		            
		                <div class="invoice-content pull-right"><?php echo to_currency($item['item_kit_unit_price']*$item['quantity_purchased']-$item['item_kit_unit_price']*$item['quantity_purchased']*$item['discount_percent']/100); ?></div>
			        </div>				
					</div>
			    </div>
			    <?php }
			}
			?>


			    <div class="invoice-footer">
			        

					<div class="row">
			            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-6 col-md-2 col-sm-2 col-xs-6">
			                <div class="invoice-footer-heading">
			                	<?php if($show_comment_on_receipt==1)
									{
										echo $comment ;
									}
								?>
			                </div>
			            </div>
			        </div>
			    </div>
			   
			    <!-- invoice footer-->
			    <div class="row">
			        <div class="col-md-12 col-sm-12">
			            <div class="invoice-policy">
			                <?php echo nl2br($this->config->item('return_policy')); ?>
			            </div>
			            <?php if (!$this->config->item('hide_barcode_on_sales_and_recv_receipt')) {?>
										<div id='barcode' class="invoice-policy">
										<?php echo "<img src='".site_url('barcode')."?barcode=$sale_id&text=$sale_id' />"; ?>
									</div>
									<?php } ?>
			        </div>
			    </div>
			</div>
			<!--container-->
		</div>		
	</div>
</div>

<?php $this->load->view("partial/footer"); ?>
<?php if ($this->config->item('print_after_sale') && $this->uri->segment(2) == 'fulfillment')
{
?>
<script type="text/javascript">
$(window).bind("load", function() {
	window.print();
});
</script>
<?php }  ?>

<script type="text/javascript">
function print_fulfillment()
 {
 	window.print();
 }
 </script>
