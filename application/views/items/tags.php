<?php $this->load->view("partial/header"); ?>
<?php echo form_open('items/save_tag/',array('id'=>'tag_form','class'=>'form-horizontal')); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-piluku">
					<div class="panel-heading"><?php echo lang("items_manage_tags"); ?></div>
					<div class="panel-body">
						<a href="javascript:void(0);" class="add_tag" data-tag_id="0">[<?php echo lang('items_add_tag'); ?>]</a>
							<div id="tag_list" class="tag-tree">
								<?php echo $tag_list; ?>
							</div>
						<a href="javascript:void(0);" class="add_tag" data-tag_id="0">[<?php echo lang('items_add_tag'); ?>]</a>
					</div>
				</div>
			</div>
		</div><!-- /row -->
		<?php  echo form_close(); ?>
	</div>

			
<script type='text/javascript'>

$(document).on('click', ".edit_tag",function()
{
	var tag_id = $(this).data('tag_id');
	bootbox.prompt({
	  title: <?php echo json_encode(lang('items_please_enter_tag_name')); ?>,
	  value: $(this).data('name'),
	  callback: function(tag_name) {
		  
	  	if (tag_name)
	  	{
	  		$.post('<?php echo site_url("items/save_tag");?>'+'/'+tag_id, {tag_name : tag_name},function(response) {	
	  			show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
	  			if (response.success)
	  			{
	  				$('#tag_list').load("<?php echo site_url("items/tag_list"); ?>");
	  			}
	  		}, "json");

	  	}
	  }
	});
});

$(document).on('click', ".add_tag",function()
{
	bootbox.prompt(<?php echo json_encode(lang('items_please_enter_tag_name')); ?>, function(tag_name)
	{
		if (tag_name)
		{
			$.post('<?php echo site_url("items/save_tag");?>', {tag_name : tag_name},function(response) {
			
				show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

				//Refresh tree if success
				if (response.success)
				{
					$('#tag_list').load("<?php echo site_url("items/tag_list"); ?>");
				}
			}, "json");

		}
	});
});

$(document).on('click', ".delete_tag",function()
{
	var tag_id = $(this).data('tag_id');
	if (tag_id)
	{
		bootbox.confirm(<?php echo json_encode(lang('items_tag_delete_confirmation')); ?>, function(result)
		{
			if (result)
			{
				$.post('<?php echo site_url("items/delete_tag");?>', {tag_id : tag_id},function(response) {
				
					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

					//Refresh tree if success
					if (response.success)
					{
						$('#tag_list').load("<?php echo site_url("items/tag_list"); ?>");
					}
				}, "json");
			}
		});
	}
	
});

</script>
<?php $this->load->view('partial/footer'); ?>
