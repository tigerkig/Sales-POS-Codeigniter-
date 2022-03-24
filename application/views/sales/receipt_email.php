<?php
$this->load->helper('sale');
$is_integrated_credit_sale = is_sale_integrated_cc_processing();
$is_ebt_sale = is_ebt_sale();
$company = ($company = $this->Location->get_info_for_key('company', isset($override_location_id) ? $override_location_id : FALSE)) ? $company : $this->config->item('company');
$company_logo = ($company_logo = $this->Location->get_info_for_key('company_logo', isset($override_location_id) ? $override_location_id : FALSE)) ? $company_logo : $this->config->item('company_logo');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--[if !mso]><!-->
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<!--<![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title></title>
	<!--[if (gte mso 9)|(IE)]>
	<style type="text/css">
		table {border-collapse: collapse !important;}
	</style>
	<![endif]-->
	<style type="text/css">
	
body {
	Margin: 0;
	padding: 0;
	min-width: 100%;
	background-color: #E8EBF1;
	line-height: 20px;
}
table {
	border-spacing: 0;
	font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
	color: #555555;
	font-size: 13px;
}
td {
	padding: 0;
}
img {
	border: 0;
}
.wrapper {
	width: 100%;
	table-layout: fixed;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
}
.webkit {
	max-width: 600px;
	background-color: #FFFFFF;
	margin-top: 30px;
	border-radius: 6px;
	border:1px solid #DCE0E6;
}
.outer {
	Margin: 0 auto;
	width: 100%;
	max-width: 600px;
}
.inner {
	padding: 10px;
}
.inner.no-padding {
	padding: 0px;
}
.contents {
	width: 100%;
}
p {
	Margin: 0;
}
a {
	color: #ee6a56;
	text-decoration: underline;
}
.h1 {
	font-size: 21px;
	font-weight: bold;
	Margin-bottom: 18px;
}
.h2 {
	font-size: 18px;
	font-weight: bold;
	Margin-bottom: 12px;
}
.full-width-image img {
	width: 100%;
	max-width: 600px;
	height: auto;
}
.border-right
{
	border-right: 1px solid #DCE0E6;
}
.border-left
{
	border-left: 1px solid #DCE0E6;
}
.primary-color
{
	color:#2196F3;
}
.text-right
{
	text-align: right !important;
}
.receipt-header
{
	text-align: center !important;
	height: 48px;
	background-color: #2196F3;
	color: #FFFFFF;
	border-top-left-radius: 6px;
	border-top-right-radius: 6px;
}

.one-column .contents {
	text-align: left;
}
.one-column p {
	font-size: 13px;
	Margin-bottom: 10px;
}


.two-column {
	text-align: center;
	font-size: 0;
	border-bottom: 1px solid #DCE0E6;
}
.two-column .column {
	width: 100%;
	max-width: 299px;
	display: inline-block;
	vertical-align: top;

}
.two-column .contents {
	font-size: 13px;
	text-align: left;
}
.two-column img {
	width: 100%;
	max-width: 280px;
	height: auto;
}
.two-column .text {
	padding-top: 0px;
}


.three-column {
	text-align: center;
	font-size: 0;
	padding-top: 10px;
	padding-bottom: 10px;
}
.three-column .column {
	width: 100%;
	max-width: 200px;
	display: inline-block;
	vertical-align: top;
}
.three-column img {
	width: 100%;
	max-width: 180px;
	height: auto;
}
.three-column .contents {
	font-size: 13px;
	text-align: center;
}
.three-column .text {
	padding-top: 10px;
}


.left-sidebar {
	text-align: center;
	font-size: 0;
}
.left-sidebar .column {
	width: 100%;
	display: inline-block;
	vertical-align: middle;
}
.left-sidebar .left {
	max-width: 100px;
}
.left-sidebar .right {
	max-width: 500px;
}
.left-sidebar .img {
	width: 100%;
	max-width: 80px;
	height: auto;
}
.left-sidebar .contents {
	font-size: 13px;
	text-align: center;
}
.left-sidebar a {
	color: #85ab70;
}


.right-sidebar {
	text-align: center;
	font-size: 0;
}
.right-sidebar .column {
	width: 100%;
	display: inline-block;
	vertical-align: middle;
}
.right-sidebar .left {
	max-width: 100px;
}
.right-sidebar .right {
	max-width: 500px;
}
.right-sidebar .img {
	width: 100%;
	max-width: 80px;
	height: auto;
}
.right-sidebar .contents {
	font-size: 13px;
	text-align: center;
}
.right-sidebar a {
	color: #70bbd9;
}

.items-table 
{
	padding-top: 10px !important;
}

.padding-right
{
	padding-right: 10px;
}

.item-row td
{
	padding-top: 10px !important;
	padding-left: 10px;
	border:1px solid #DCE0E6;
	border-bottom-width: 0px;
	border-right-width: 0px;
	padding-bottom: 10px;
}

.item-row:last-child td
{
	
	border:1px solid #DCE0E6;
	border-bottom-width: 1px;

}

.item-row:first-child
{
	
	border:1px solid #DCE0E6;

}

.items-table th
{
	background-color: #F5F5F5;
	height: 32px;
}


@media screen and (max-width: 400px) {
	.two-column .column,
	.three-column .column {
		max-width: 100% !important;
	}
	.two-column img {
		max-width: 100% !important;
	}
	.three-column img {
		max-width: 50% !important;
	}
}

@media screen and (min-width: 401px) and (max-width: 620px) {
	.three-column .column {
		max-width: 33% !important;
	}
	.two-column .column {
		max-width: 50% !important;
	}
}
	</style>
</head>
<body style="Margin:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;min-width:100%;background-color:#E8EBF1;line-height:20px;" >
	<center class="wrapper" style="width:100%;table-layout:fixed;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;" >
		<div class="webkit" style="max-width:600px;background-color:#FFFFFF;margin-top:30px;border-radius:6px;border-width:1px;border-style:solid;border-color:#DCE0E6;" >
			<!--[if (gte mso 9)|(IE)]>
			<table width="600" align="center" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
			<tr>
			<td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
			<![endif]-->
			<table class="outer" align="center" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;Margin:0 auto;width:100%;max-width:600px;" >
				<tr>
					<td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
							<tr>
								<td class="inner contents receipt-header" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;width:100%;height:48px;background-color:#2196F3;color:#FFFFFF;border-top-left-radius:6px;border-top-right-radius:6px;text-align:center !important;" >
									<?php echo $receipt_title; ?> #<?php echo $sale_id; ?>
									<br />
									<?php if (isset($deleted) && $deleted) {?>
					            	<span class="text-danger" style="color: #df6c6e;"><strong><?php echo lang('sales_deleted_voided'); ?></strong></span>
										<br />
									<?php } ?>
									
									<div id="sale_time"><?php echo $transaction_time ?></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#DCE0E6;" >
						<!--[if (gte mso 9)|(IE)]>
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
						<tr>
						<td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<![endif]-->
						<div class="column" style="width:100%; max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text" style="padding-bottom:0;padding-right:0;padding-left:0;padding-top:0px;" >
													<?php													
														if ($company_logo)
														{
															$this->load->helper('file');
															$file = $this->Appfile->get($company_logo);
															$base64_file_data = base64_encode($file->file_data);
															$mime = get_mime_by_extension($file->file_name);
														?>
														<img style="width:100px;" src="data:<?php echo $mime ?>;base64,<?php echo $base64_file_data ?>" />
														<br />
													<?php } ?>
													<b><?php echo $company; ?></b>
													<br />
													<?php echo nl2br($this->Location->get_info_for_key('address',isset($override_location_id) ? $override_location_id : FALSE)); ?>
													<br />
													<?php echo $this->Location->get_info_for_key('phone',isset($override_location_id) ? $override_location_id : FALSE); ?>
				  			          <?php if($this->config->item('website')) { ?>													
														<br />
														<a href="<?php echo prep_url($this->config->item('website')); ?>" class="primary-color" style="text-decoration:underline;color:#2196F3;"><?php echo $this->config->item('website'); ?></a>
													<?php } ?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<!--[if (gte mso 9)|(IE)]>
						</td><td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<![endif]-->
						<div class="column border-left" style="border-left-width:1px;border-left-style:solid;border-left-color:#DCE0E6;width:100%;max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text" style="padding-bottom:0;padding-right:0;padding-left:0;padding-top:0px;" >
												<?php if(isset($customer)) { ?>
													<b><?php echo lang('common_customer') ?> : </b> <?php echo $customer; ?> <br />
													<?php if (!empty($customer_city)) { echo "<b>".$customer_address_1. ' '.$customer_address_2." : </b>".$customer_city.' '.$customer_state.', '.$customer_zip;} ?>

													<?php if (!empty($customer_country)) { echo "<br />".$customer_country; } ?>

													<b><?php echo lang('common_phone_number') ?> : </b><?php echo $customer_phone; ?> <br />
													<b><?php echo lang('common_email') ?> : </b><?php echo $customer_email; ?> <br />
												<?php } ?>
												</td>

											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<!--[if (gte mso 9)|(IE)]>
						</td>
						</tr>
						</table>
						<![endif]-->
					</td>
				</tr>
					<tr>
					<td class="two-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;text-align:center;font-size:0;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#DCE0E6;" >
						<!--[if (gte mso 9)|(IE)]>
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
						<tr>
						<td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<![endif]-->
						<div class="column" style="width:100%;max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text" style="padding-bottom:0;padding-right:0;padding-left:0;padding-top:0px;" >
												
												<?php if ($register_name) { ?>
													<?php echo "<b>".lang('common_register_name').':</b> '.$register_name; ?>
												<?php } ?>
												
												<?php if ($tier) { ?>
													<?php echo "<b>".lang('common_tier_name').':</b> '.$tier; ?>
												<?php } ?>


												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<!--[if (gte mso 9)|(IE)]>
						</td><td width="50%" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<![endif]-->
						<div class="column" style="width:100%;max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text text-right" style="padding-bottom:0;padding-right:0;padding-left:0;text-align:right !important;padding-top:0px;" >
														<?php echo "<b>".lang('common_employee').":</b> ".$employee; ?>		

														<?php 
														if($this->Location->get_info_for_key('enable_credit_card_processing',isset($override_location_id) ? $override_location_id : FALSE))
														{
															echo '<br/><b>'.lang('common_merchant_id').':</b> '.$this->Location->get_merchant_id(isset($override_location_id) ? $override_location_id : FALSE);
														}
														?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<!--[if (gte mso 9)|(IE)]>
						</td>
						</tr>
						</table>
						<![endif]-->
					</td>
				</tr>
				<tr>
					<td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
							<tr>
								<td class="inner no-padding" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
									<table width="100%" class="items-table" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;padding-top:10px !important;" >
										<tr>
											<?php
												$column_width = "100px";
												$total_columns = 4;
										 	
											 	if($discount_exists) { $column_width = "75px"; $total_columns = 5; } 
											 ?>

											<th width="300px" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_item'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_price'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_quantity'); ?></th>

											<?php if($discount_exists) { ?>
												<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_discount_percent'); ?></th>
											<?php } ?>

											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_total'); ?></th>
										</tr>
										

										<?php
										if ($discount_item_line = $this->sale_lib->get_line_for_flat_discount_item())
										{
											$discount_item = $cart[$discount_item_line];
											unset($cart[$discount_item_line]);
											array_unshift($cart,$discount_item);
										}
										
											foreach(array_reverse($cart, true) as $line=>$item)
											{
												
												$item_number_for_receipt = false;
												
												if ($this->config->item('show_item_id_on_receipt'))
												{
													switch($this->config->item('id_to_show_on_sale_interface'))
													{
														case 'number':
														$item_number_for_receipt = $item['item_number'];
														break;
													
														case 'product_id':
														$item_number_for_receipt = $item['product_id'];
														break;
													
														case 'id':
														$item_number_for_receipt = $item['item_id'];
														break;
													
														default:
														$item_number_for_receipt = $item['item_number'];
														break;
													}
												}
											?>
										<tr class="text-center item-row">
											<td style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?><?php if (!$this->config->item('hide_desc_on_receipt') && $item['description']){ ?> - <?php echo $item['description']; ?><?php } ?><?php if ($item['size']){ ?> (<?php echo $item['size']; ?>)<?php } ?>
											</td>
											<td align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_currency($item['price']); ?>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_quantity($item['quantity']);?>
											</td>
											<?php if($discount_exists) { ?>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo to_quantity($item['discount']); ?>
												</td>
											<?php } ?>
										
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?>
											</td>
										</tr>

										<?php } ?>
										
										<?php if ($exchange_name) { ?>
										
										<tr class="text-center item-row">
											<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo lang('common_exchange_to').' '.$exchange_name; ?>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												x <?php echo to_currency_no_money($exchange_rate); ?>
											</td>
										</tr>
										
									<?php } ?>
									
									<tr class="text-center item-row">
										<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
											<?php echo lang('common_sub_total'); ?>
										</td>
										<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php if (isset($exchange_name) && $exchange_name) { 
													echo to_currency_as_exchange($subtotal);
												?>
												<?php } else {  ?>
												<?php echo to_currency($subtotal); ?>				
												<?php
												}
												?>
										</td>
									</tr>
									
									
										<?php foreach($taxes as $name=>$value) { ?>
											<tr class="text-center item-row">
												<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo $name; ?>:
												</td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php if (isset($exchange_name) && $exchange_name) { 
														echo to_currency_as_exchange($value*$exchange_rate);					
													?>
													<?php } else {  ?>
													<?php echo to_currency($value); ?>				
													<?php
													}
													?>
												</td>
											</tr>
										<?php }; ?>


										<tr class="text-center item-row">
											<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b><?php echo lang('common_total'); ?></b>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b> 
												
												<?php if (isset($exchange_name) && $exchange_name) { 
													?>
													<?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($total)) : to_currency_as_exchange($total); ?>				
												<?php } else {  ?>
												<?php echo $this->config->item('round_cash_on_sales') && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($total)) : to_currency($total); ?>				
												<?php
												}
												?>
												</b>
											</td>
										</tr>

									  	<tr><td colspan="<?php echo $total_columns; ?>">&nbsp;</td></tr>

									    <?php foreach($payments as $payment_id=>$payment) { ?>
											<tr class="text-center item-row">
												<td colspan="<?php echo $total_columns-2; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?>
												</td>

												<?php if ($is_integrated_credit_sale || $is_ebt_sale || sale_has_partial_credit_card_payment() || sale_has_partial_ebt_payment()) { ?>
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> </td>											 
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo $payment['card_issuer']. ' '.$payment['truncated_card']; ?></td>											 
												<?php } else { ?>
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> </td>											 
												<?php } ?>


												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													
													<?php 
													if (isset($exchange_name) && $exchange_name) { 
														?>
														<?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  to_currency_as_exchange(round_to_nearest_05($payment['payment_amount'])) : to_currency_as_exchange($payment['payment_amount']); ?>				
													<?php } else {  ?>
													<?php echo $this->config->item('round_cash_on_sales') && $payment['payment_type'] == lang('common_cash') ?  to_currency(round_to_nearest_05($payment['payment_amount'])) : to_currency($payment['payment_amount']); ?>				
													<?php
													}
									
									
													?>
												</td>
											</tr>
										<?php } ?>
										
										<?php foreach($payments as $payment) {?>
											<?php if (strpos($payment['payment_type'], lang('common_giftcard'))!== FALSE) {?>
												<?php $giftcard_payment_row = explode(':', $payment['payment_type']); ?>
												<td colspan="<?php echo $total_columns-2; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('sales_giftcard_balance'); ?></td>											 
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo $payment['payment_type'];?></td>											 
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo to_currency($this->Giftcard->get_giftcard_value(end($giftcard_payment_row))); ?></td>												
											<?php }?>
										<?php }?> 
										

									  	<tr><td colspan="<?php echo $total_columns; ?>">&nbsp;</td></tr>

										<?php if ($amount_change >= 0) { ?>
										<tr>
											<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('common_change_due'); ?></td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												
												<?php if (isset($exchange_name) && $exchange_name) { 
													?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($amount_change)) : to_currency_as_exchange($amount_change); ?>				
												<?php } else {  ?>
												<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change)) : to_currency($amount_change); ?>				
												<?php
												}
												?>
												
												
												
											</td>
										</tr>
										<?php } else { ?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('common_amount_due'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													
													<?php if (isset($exchange_name) && $exchange_name) { 
														?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency_as_exchange(round_to_nearest_05($amount_change * -1)) : to_currency_as_exchange($amount_change * -1); ?>
													<?php } else {  ?>
													<?php echo $this->config->item('round_cash_on_sales')  && $is_sale_cash_payment ?  to_currency(round_to_nearest_05($amount_change * -1)) : to_currency($amount_change * -1); ?>
													<?php
													}
													?>
													
												</td>
											</tr>	
										<?php } ?>
										
										<?php if (isset($customer_balance_for_sale) && $customer_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) { ?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('sales_customer_account_balance'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_currency($customer_balance_for_sale); ?> </td>
											</tr>
										<?php } ?>
										
										<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($sales_until_discount) && !$this->config->item('hide_sales_to_discount_on_receipt') && $this->config->item('loyalty_option') == 'simple') {?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('common_sales_until_discount'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo $sales_until_discount <= 0 ? lang('sales_redeem_discount_for_next_sale') : to_quantity($sales_until_discount); ?> </td>
											</tr>
										<?php
										}
										?>
										
										<?php if (!$disable_loyalty && $this->config->item('enable_customer_loyalty_system') && isset($customer_points) && !$this->config->item('hide_points_on_receipt') && $this->config->item('loyalty_option') == 'advanced') {?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('common_points'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_currency_no_money($customer_points); ?> </td>
											</tr>
										<?php
										}
										?>
										
										
										
										<?php if (isset($ref_no) && $ref_no) { ?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('sales_ref_no'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo $ref_no; ?></td>
											</tr>	
										<?php } ?>
										
										<?php if (isset($auth_code) && $auth_code) { ?>
											<tr>
												<td  colspan="<?php echo $total_columns-1; ?>"   align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo lang('sales_auth_code'); ?></td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php echo $auth_code; ?></td>
											</tr>	
										<?php } ?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
							<tr>
								<td class="inner contents" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;width:100%;text-align:left;" >
									<p style="Margin:0;font-size:13px;Margin-bottom:10px;" >
										<?php 
											if(isset($show_comment_on_receipt) && $show_comment_on_receipt == 1)
											{ 
												echo lang('common_comments').": ". $comment; 
											} 
										?>
									</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="one-column" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >
						<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
							<tr>
								<td class="inner contents" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;width:100%;text-align:left;" >
									<p style="Margin:0;font-size:13px;Margin-bottom:10px;" >
										<?php echo nl2br($this->config->item('return_policy')); ?>
									</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				
			</table>
			<!--[if (gte mso 9)|(IE)]>
			</td>
			</tr>
			</table>
			<![endif]-->
		</div>
	</center>
</body>
</html>