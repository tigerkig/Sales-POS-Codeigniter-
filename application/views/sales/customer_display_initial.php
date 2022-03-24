<?php $this->load->view("partial/header"); ?> 
<div id="sales_page_holder">
	<h2><?php echo lang('sales_signature');?></h2>
	
		<div id="digital_sig_holder" style="display: block;">
			<canvas id="sig_cnv" name="sig_cnv" class="signature" width="500" height="100"></canvas>
			<div id="sig_actions_container" class="pull-right">
					<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_done_button"> <?php echo lang('sales_done_capturing_sig'); ?> </button>
					<button class="btn btn-primary btn-radius btn-lg hidden-print" id="capture_digital_sig_clear_button"> <?php echo lang('sales_clear_signature'); ?> </button>
			</div>
			<div id="digital_sig_holder_signature">
			</div>
		</div>
		
		
	<div id="customer_display_container" class="sales clearfix">
	  <?php $this->load->view("sales/customer_display"); ?>
	</div>
</div>

<script>
var sale_id = false;
customer_display_update();

function customer_display_update()
{
	$("#customer_display_container").load('<?php echo site_url('sales/customer_display_update/'.$register_id); ?>', function()
	{
		$.get('<?php echo site_url('sales/customer_display_info/'.$register_id); ?>', function(json)
		{
			if (json.sale_id)
			{
				sale_id = json.sale_id;
			}
			
			setTimeout(customer_display_update, 1000);	
		
		},'json');
	});
}
$(document).on('click', "#email_receipt",function()
{
	$.get($(this).attr('href'), function()
	{
		show_feedback('success', <?php echo json_encode(lang('common_receipt_sent')); ?>, <?php echo json_encode(lang('common_success')); ?>);
		
	});
	
	return false;
});

$(document).ready(function(){
	$('.fullscreen').on('click',function (e) {
		e.preventDefault();
		salesRecvFullScreen();
		$.get('<?php echo site_url("home/set_fullscreen_customer_display/1");?>');
	});
	
	$(document).on('click', ".dismissfullscreen",function(e) {
		e.preventDefault();
		salesRecvDismissFullscren();
		$.get('<?php echo site_url("home/set_fullscreen_customer_display/0");?>');
	});

	$(window).load(function()
	{
		setTimeout(function()
		{
		<?php if ($fullscreen_customer_display) { ?>
			$('.fullscreen').click();
		<?php }
		else {
		?>
		$('.dismissfullscreen').click();	
		<?php
		} ?>
		
		}, 0);
	});
});


var sig_canvas = document.getElementById('sig_cnv');
var signaturePad = new SignaturePad(sig_canvas);

$("#capture_digital_sig_button").click(function()
{	
	signaturePad.clear();	
	$("#capture_digital_sig_button").hide();
});

$("#capture_digital_sig_clear_button").click(function()
{
		signaturePad.clear();
});

$("#capture_digital_sig_done_button").click(function()
{
		SigImageCallback(signaturePad.toDataURL().split(",")[1]);
		$("#capture_digital_sig_button").show();
});

function SigImageCallback( str )
{
	if (sale_id)
	{
		$.post('<?php echo site_url('sales/sig_save'); ?>', {sale_id: sale_id, image: str}, function(response)
		{
	 	 $("#digital_sig_holder_signature").html('<img src="'+SITE_URL+'/app_files/view/'+response.file_id+'?timestamp='+response.file_timestamp+'" width="250" />');
 		}, 'json');
	}
	else
	{
		bootbox.alert(<?php echo json_encode(lang('sales_cannot_sign')); ?>);
	}

}

</script>

<?php $this->load->view("partial/footer"); ?>
