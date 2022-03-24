<?php $this->load->view("partial/header"); ?>
<div class="modal fade category-input-data" id="category-input-data" tabindex="-1" role="dialog" aria-labelledby="categoryData" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
      	<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="categoryModalDialogTitle"></h4>
	        </div>
	        <div class="modal-body">
				<!-- Form -->
				<?php echo form_open_multipart('items/save_category/',array('id'=>'categories_form','class'=>'form-horizontal'));

				$parent_id_form_field = array(
					'type'  => 'hidden',
					'name'  => 'parent_id',
					'id'    => 'parent_id',
					'value' => '0',
				);

				echo form_input($parent_id_form_field);
				?>
			
				<div class="form-group">
					<?php echo form_label(lang('items_category_name').':', 'category_name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'type'  => 'text',
							'name'  => 'category_name',
							'id'    => 'category_name',
							'value' => '',
							'class'=> 'form-control form-inps',
						)); ?>
					</div>
				</div>
												
				<div class="form-group">
					<?php echo form_label(lang('items_category_color').':', 'category_color',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'category_color',
							'id'=>'category_color',
							'value'=>'')
						);?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('items_category_image').':', 'category_image',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<div class="image-upload">
							<?php echo form_input(array(
								'type'  => 'file',
								'name'  => 'category_image',
								'id'    => 'category_image',
								'value' => '',
								'class' => 'filestyle form-control form-inps',
								'data-icon' => 'false'
							)); ?>
						</div>
					</div>
				</div>
				
				<div id="preview-section" class="form-group" style="display:none;">
					<?php echo form_label(lang('image_preview').':', 'category_image_preview',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<img id="image-preview" src="#" alt="preview" style="max-width: 100%;">
					</div>
					
					<?php echo form_label(lang('common_del_image').':', 'category_image_delete',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-9">
						<?php echo form_checkbox(array(
							'name'=>'del_image',
							'id'=>'del_image',
							'class'=>'delete-checkbox',
							'value'=>1
						));?>
						<label for="del_image"><span></span></label>
					</div>
				</div>
				
				<div class="form-actions">
					<?php
						echo form_submit(array(
							'name'=>'submitf',
							'id'=>'submitf',
							'value'=>lang('common_submit'),
							'class'=>'submit_button pull-right btn btn-primary')
						);
					?>
					<div class="clearfix">&nbsp</div>
				</div>
			
				<?php echo form_close(); ?>
	        </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

		<div class="row">
			<div class="col-md-12">
				<div class="panel-piluku panel">
					<div class="panel-heading"><?php echo lang("items_manage_categories"); ?></div>
					<div class="panel-body">
						<a href="javascript:void(0);" class="add_child_category" data-category_id="0">[<?php echo lang('items_add_root_category'); ?>]</a>
							<div id="category_tree">
								<?php echo $category_tree; ?>
							</div>
						<a href="javascript:void(0);" class="add_child_category" data-category_id="0">[<?php echo lang('items_add_root_category'); ?>]</a>
					</div>
				</div>
			</div>
		</div><!-- /row -->
	</div>

			
<script type='text/javascript'>	
	$(function() {
		$('#category_color').colorpicker();
	});
	
	$('#category_image').change(function (e) {
		$("#categories_form").find('#image-preview').attr('src', URL.createObjectURL(e.target.files[0]));
	    $('#preview-section').show();
	});
	
	$(document).on('click', ".edit_category",function()
	{
		$("#categoryModalDialogTitle").html(<?php echo json_encode(lang('common_edit')); ?>);
		var parent_id = $(this).data('parent_id');
		$("#categories_form").find('#parent_id').val(parent_id);
		var category_id = $(this).data('category_id');
		$("#categories_form").attr('action',SITE_URL+'/items/save_category/'+category_id);
		
		//Populate form
		$(":file").filestyle('clear');
		$("#categories_form").find('#category_name').val($(this).data('name'));
		$("#categories_form").find('#category_color').val($(this).data('color'));
		$('#category_color').colorpicker('setValue', $(this).data('color'));
		
		
		$('#del_image').prop('checked',false);
		
		if ($(this).data('image_id'))
		{
			$("#categories_form").find('#image-preview').attr('src',SITE_URL+'/app_files/view/'+$(this).data('image_id')+"?timestamp="+$(this).data('image_timestamp'));
			$('#preview-section').show();
		}
		else 
		{
			$("#categories_form").find('#image-preview').attr('src','');
			$('#preview-section').hide();
		}
		
		//show
		$("#category-input-data").modal('show');
	});
	
	$(document).on('click', ".add_child_category",function()
	{
		$("#categoryModalDialogTitle").html(<?php echo json_encode(lang('items_add_child_category')); ?>);
		var parent_id = $(this).data('category_id');
		$("#categories_form").find('#parent_id').val(parent_id);
		$("#categories_form").attr('action',SITE_URL+'/items/save_category');
		
		//Clear form
		$(":file").filestyle('clear');
		$("#categories_form").find('#category_name').val("");
		$("#categories_form").find('#category_color').val("");
		$('#category_color').colorpicker('setValue', '');
		$("#categories_form").find('#category_image').val("");
		$("#categories_form").find('#image-preview').attr('src','');
		$('#del_image').prop('checked',false);
		$('#preview-section').hide();
		
		//show
		$("#category-input-data").modal('show');
	});

$("#categories_form").submit(function(event)
{
	event.preventDefault();
	
	$(this).ajaxSubmit({ 
		success: function(response, statusText, xhr, $form){
			show_feedback(response.success ? 'success' : 'error', response.message, response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
			if(response.success)
			{
				$("#category-input-data").modal('hide');
				$('#category_tree').load("<?php echo site_url("items/get_category_tree_list"); ?>");
			}		
	},
	dataType:'json',
});
	
	
});

$(document).on('click', ".delete_category",function()
{
	var category_id = $(this).data('category_id');
	if (category_id)
	{
		bootbox.confirm(<?php echo json_encode(lang('items_category_delete_confirmation')); ?>, function(result)
		{
			if(result)
			{

				$.post('<?php echo site_url("items/delete_category");?>', {category_id : category_id},function(response) {

					show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);

					//Refresh tree if success
					if (response.success)
					{
						$('#category_tree').load("<?php echo site_url("items/get_category_tree_list"); ?>");
					}
				}, "json");
			}
		});
	}

});

$(document).on('click', ".hide_from_grid",function()
{
	var category_id = $(this).data('category_id');
	if (category_id)
	{
		$.post('<?php echo site_url("items/save_category");?>'+'/'+category_id, {hide_from_grid: $(this).prop('checked') ? 1 : 0},function(response) {

			show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> : <?php echo json_encode(lang('common_error')); ?>);
			//Refresh tree if success
			if (response.success)
			{
				$('#category_tree').load("<?php echo site_url("items/get_category_tree_list"); ?>");
			}
		}, "json");

	}

});

</script>
<?php $this->load->view('partial/footer'); ?>
