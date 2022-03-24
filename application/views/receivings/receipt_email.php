<?php
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
									<?php echo $is_po ? lang('receivings_purchase_order') : $receipt_title; ?> #<?php echo $receiving_id; ?>
									<br />
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
						<div class="column" style="width:100%; height:100%;max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text" style="padding-bottom:0;padding-right:0;padding-left:0;padding-top:0px;" >
													<?php
														$this->load->helper('file');
														$file = $this->Appfile->get($company_logo);
														$base64_file_data = base64_encode($file->file_data);
														$mime = get_mime_by_extension($file->file_name);
													?>
													<img style="width:100px;" src="data:<?php echo $mime ?>;base64,<?php echo $base64_file_data ?>" />
													<br>
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
						<div class="column border-left" style="border-left-width:1px;border-left-style:solid;border-left-color:#DCE0E6;width:100%; height:100%;max-width:299px;display:inline-block;vertical-align:top;" >
							<table width="100%" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;font-size:13px;" >
								<tr>
									<td class="inner" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;" >
										<table class="contents" style="border-spacing:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#555555;width:100%;font-size:13px;text-align:left;" >
											<tr>
												<td class="text" style="padding-bottom:0;padding-right:0;padding-left:0;padding-top:0px;" >
												<?php if(isset($supplier)) { ?>
													<b><?php echo lang('common_supplier') ?> : </b> <?php echo $supplier; ?> <br />
													<?php if (!empty($supplier_city)) { echo "<b>".$supplier_address_1. ' '.$supplier_address_2." : </b>".$supplier_city.' '.$supplier_state.', '.$supplier_zip;} ?>

													<?php if (!empty($supplier_country)) { echo "<br />".$supplier_country; } ?>

													<b><?php echo lang('common_phone_number') ?> : </b><?php echo $supplier_phone; ?> <br />
													<b><?php echo lang('common_email') ?> : </b><?php echo $supplier_email; ?> <br />
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
			</table>
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
										<tr border="1">
											<?php
												$column_width = "75px";
												$total_columns = 5;
											 ?>

											<th width="300px" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_item'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_price'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_quantity'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_discount_percent'); ?></th>
											<th width="<?php echo $column_width ?>" style="background-color:#F5F5F5;height:32px;" ><?php echo lang('common_total'); ?></th>
										</tr>
										
										<?php
											foreach(array_reverse($cart, true) as $line=>$item)
											{
												?>
												
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
												

												<tr class="text-center item-row">
													<td style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
														<?php echo $item['name']; ?><?php if ($item_number_for_receipt){ ?> - <?php echo $item_number_for_receipt; ?><?php } ?>
													</td>
													<td align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
														<?php echo to_currency($item['price']); ?>
													</td>
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
														<?php echo to_quantity($item['quantity']);?>
													</td>
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
														<?php echo to_quantity($item['discount']); ?>
													</td>
										
													<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
														<?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?>
													</td>
												</tr>
										<?php } ?>

										<tr class="text-center item-row">
											<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo lang('common_sub_total'); ?>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<?php echo to_currency($subtotal); ?>
											</td>
										</tr>
									
										<?php foreach($taxes as $name=>$value) { ?>
											<tr class="text-center item-row">
												<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo $name; ?>:
												</td>
												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo to_currency($value); ?>
												</td>
											</tr>
										<?php }; ?>


										<tr class="text-center item-row">
											<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b><?php echo lang('common_total'); ?></b>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b> <?php echo to_currency($total); ?></b>
											</td>
										</tr>

									  	<tr><td colspan="<?php echo $total_columns; ?>">&nbsp;</td></tr>

									    <?php foreach($payments as $payment_id=>$payment) { ?>
											<tr class="text-center item-row">
												<td colspan="<?php echo $total_columns-2; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo (isset($show_payment_times) && $show_payment_times) ?  date(get_date_format().' '.get_time_format(), strtotime($payment['payment_date'])) : lang('common_payment'); ?>
												</td>

												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" ><?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?> </td>											 


												<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
													<?php echo to_currency($payment['payment_amount']); ?>
												</td>
											</tr>
										<?php } ?>
										
										<?php if (isset($supplier_balance_for_sale) && $supplier_balance_for_sale !== FALSE && !$this->config->item('hide_store_account_balance_on_receipt')) {?>
											
											
											<td colspan="<?php echo $total_columns-1; ?>" class=" padding-right" align="right" style="padding-right:10px;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b><?php echo lang('receivings_supplier_account_balance'); ?></b>
											</td>
											<td  align="center" style="padding-right:0;padding-top:10px !important;padding-left:10px;border-width:1px;border-style:solid;border-color:#DCE0E6;border-bottom-width:0px;border-right-width:0px;padding-bottom:10px;" >
												<b><?php echo to_currency($supplier_balance_for_sale); ?></b>
											</td>											
										<?php
										}
										?>
										
										
									</table>
								</td>
							</tr>
						</table>
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