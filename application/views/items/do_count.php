<?php $this->load->view("partial/header"); ?>
	<?php if ($count_info->status == 'open') { ?>		
		<ul class="list-inline text-right">
			<li>
				<?php echo anchor('items/excel_import_count', lang('common_excel_import'),array('class'=>'btn btn-success btn-lg'));?>
			</li>
		</ul>
	<?php } ?>

<div id="content-header" class="hidden-print">
	<div class="col-lg-12 col-md-12 no-padding-left visible-lg visible-md">
</div>
</div>
<div id="count_container">
	<?php $this->load->view("items/do_count_data"); ?>
</div>

<?php $this->load->view('partial/footer'); ?>