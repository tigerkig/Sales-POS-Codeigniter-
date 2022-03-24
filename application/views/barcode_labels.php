<?php $this->load->view("partial/header"); ?>
<style>
@media print
{
	.wrapper {
  	 overflow: visible;
	 font-family: serif !important;
	}
}

.barcode-label
{
	-webkit-box-sizing: content-box;
	-moz-box-sizing: content-box;
	box-sizing: content-box;
	width: 1.9in;
	height:.79in;
	letter-spacing: normal;
	word-wrap: break-word;
	overflow: hidden;
	margin:0 auto;
	text-align:center;
	padding: 10px;
	font-size: 10pt;
	line-height: 1em;
	 font-family: serif !important;
}

</style>
<h1 class="hidden-print" style="text-align: center;">Zebra Printers <?php echo lang('common_label_info');?></h1>

<div class="hidden-print" style="text-align: center;margin-top: 20px;">
	<button class="btn btn-primary text-white hidden-print" id="print_button" onclick="window.print();"><?php echo lang('common_print'); ?></button>	
</div>	
<?php 
$company = ($company = $this->Location->get_info_for_key('company')) ? $company : $this->config->item('company');

for($k=0;$k<count($items);$k++)
{
	$item = $items[$k];
	$expire_key = (isset($from_recv) ? $from_recv : 0).'|'.ltrim($item['id'],0);
	$barcode = $item['id'];
	$text = $item['name'];
	
	if(isset($items_expire[$expire_key]) && $items_expire[$expire_key])
	{
		$text.= " (".lang('common_expire_date').' '.$items_expire[$expire_key].')';		
	}
	elseif (isset($from_recv))
	{
		$text.= " (RECV $from_recv)";
	}
	

	$page_break_after = ($k == count($items) -1) ? 'auto' : 'always';
	echo "<div class='barcode-label' style='page-break-after: $page_break_after'>".$company."<br /><img style='vertical-align:baseline;'src='".site_url('barcode').'?barcode='.rawurlencode($barcode).'&text='.rawurlencode($barcode)."&scale=$scale' /><br />".$text."</div>";
}
?>
<?php $this->load->view("partial/footer"); ?>