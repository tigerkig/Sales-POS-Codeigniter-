<div class="container-fluid">
<div class="row register">
	<div class="col-lg-12 col-md-12 no-padding-left no-padding-right">
		
		<?php if ($count_info->status == 'open') { ?>
			<div class="register-box register-items-form">
				<div class="item-form">
					<!-- Item adding form -->
					<?php echo form_open("items/add_item_to_inventory_count",array('id'=>'add_item_form','class'=>'form-inline', 'autocomplete'=> 'off')); ?>
						<div class="input-group input-group-mobile contacts">
							<span class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown">
								<?php echo anchor("#","<i class='icon ti-panel'></i> <span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','tabindex'=>'-1','title'=>$modes[$mode], 'id' => 'select-mode-1', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
						        <ul class="dropdown-menu sales-dropdown">
						        <?php foreach ($modes as $key => $value) {
						        	if($key!=$mode){
						        ?>
						        	<li><a tabindex="-1" href="#" data-mode="<?php echo H($key); ?>" class="change-mode"><?php echo $value;?></a></li>
						        <?php }  
							  	} ?>
	        					</ul>
							</span>						
						</div>
						
						<div class="input-group contacts register-input-group">						
							<input type="text" id="item" name="item"  class="add-item-input items-count pull-left" placeholder="<?php echo H(lang('common_start_typing_item_name')); ?>">
							<span class="input-group-addon register-mode <?php echo $mode; ?>-mode dropdown inventory-count">
								<?php echo anchor("#","<i class='icon ti-panel'></i> <span class='register-btn-text'>".$modes[$mode]."</span>", array('class'=>'none active','tabindex'=>'-1','title'=>$modes[$mode], 'id' => 'select-mode-1', 'data-target' => '#', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'role' => 'button', 'aria-expanded' => 'false')); ?>
						        <ul class="dropdown-menu sales-dropdown">
						        <?php foreach ($modes as $key => $value) {
						        	if($key!=$mode){
						        ?>
						        	<li><a tabindex="-1" href="#" data-mode="<?php echo H($key); ?>" class="change-mode"><?php echo $value;?></a></li>
						        <?php }  
							  	} ?>
	        					</ul>
							</span>
						
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<?php if($pagination) {  ?>
			<div class="pagination alternate hidden-print m-t-10 text-center" id="pagination_top">
				<?php echo $pagination;?>		
			</div>
		<?php }  ?>
			<?php  } ?>
	</div>
	<div class="row register">
		<div class="col-lg-12 col-md-12 no-padding-left no-padding-right">

		<div class="register-box register-items paper-cut">
			<div class="register-items-holder table-responsive">
				<table id="register" class="table table-hover">
					<thead>
						<tr class="register-items-header">
							<th><?php echo lang('common_item');?></th>
							<th><?php echo lang('items_count');?></th>
							<th><?php echo lang('items_actual_on_hand');?></th>
							<th><?php echo lang('common_comments');?></th>
							<?php if ($count_info->status == 'open') { ?>
								<th><?php echo lang('common_delete');?></th>
							<?php } ?>
						</tr>
					</thead>
				
					<tbody class="register-item-content">
						<?php foreach($items_counted as $counted_item) { ?>
							<tr class="register-item-details">
								<td><a href="<?php echo site_url('home/view_item_modal').'/'.$counted_item['item_id']; ?>" data-toggle="modal" class="register-item-name count-items" data-target="#myModal"><?php echo H($counted_item['name']).' ('.H($counted_item['category']).')'; ?></a></td>
								<td class="text-center"><a href="#" id="count" class="xeditable" data-type="text" data-pk="<?php echo $counted_item['item_id']; ?>" data-name="quantity" data-url="<?php echo site_url('items/edit_count_item'); ?>" data-title="<?php echo H(lang('items_edit_count')); ?>"><?php echo to_quantity($counted_item['count']); ?></a></td>
								<td class="text-center"><?php echo to_quantity($counted_item['actual_quantity']);?></td>
								<td class="text-center"><a href="#" id="comment" class="xeditable" data-type="text" data-pk="<?php echo $counted_item['item_id']; ?>" data-name="comment" data-url="<?php echo site_url('items/edit_count_item'); ?>" data-title="<?php echo H(lang('items_edit_comment')); ?>"><?php echo $counted_item['comment'] ? H($counted_item['comment']): 'None'; ?></a></td></a></td>
								<?php if ($count_info->status == 'open') { ?>
									<td class="text-center"><?php echo anchor('items/delete_inventory_count_item/'.$counted_item['item_id'], 'Delete Count Item',array('class' =>'text-danger'));?></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>


		<?php if($pagination) {  ?>
			<div class="pagination alternate hidden-print m-b-10 text-center" id="pagination_top">
				<?php echo $pagination;?>		
			</div>
		<?php }  ?>


<?php if ($count_info->status == 'open') { ?>

	<ul class="list-inline count-items-buttons">
		<?php if ($this->Employee->has_module_action_permission('items','edit_quantity', $this->Employee->get_logged_in_employee_info()->person_id))
		{
			echo "<li>".anchor('items/finish_count/1', lang('items_close_finish_count_update_inventory'),array('class'=>'btn btn-danger btn-lg finish-count'))."</li>";
		} ?>
		
		<li>
			<?php echo anchor('items/finish_count/0', lang('items_close_finish_count_do_not_update_inventory'),array('class'=>'btn btn-warning btn-lg finish-count'));?>
		</li>
		<li>
			<?php echo anchor('items/count', lang('items_continue_count_later'),array('class'=>'btn btn-primary btn-lg'));?>
		</li>
	</ul>
<?php } ?>

<br />


<script type='text/javascript'>

$(document).ready(function()
{
	<?php if(isset($error) && $error === true){ ?>
		show_feedback('error',<?php echo json_encode(lang('items_inventory_count_error')); ?>,<?php echo json_encode(lang('common_error')); ?>);	
	<?php } ?>
	// if #mode is changed
	$('.change-mode').click(function(e){
		e.preventDefault();
		
		$.post('<?php echo site_url("items/change_count_mode");?>', {mode: $(this).data('mode')}, function(response)
		{
			$("#count_container").html(response);
		});
	});
	
	
	<?php if ($count_info->status == 'open') { ?>
	  $('.xeditable').editable();
	 <?php }else { ?>
  	  $('.xeditable').click(function(e){e.preventDefault();});
	 	
	 <?php } ?>
   $('.xeditable').on('shown', function(e, editable) {
		
		$(this).closest('.table-responsive').css('overflow-x','hidden');

	   	editable.input.postrender = function() {
			//Set timeout needed when calling quantity_to_change.editable('show') (Not sure why)
			setTimeout(function() {
	        editable.input.$input.select();
			}, 200);
		};
	});

	$('.xeditable').on('hidden', function(e, editable) {
		$(this).closest('.table-responsive').css('overflow-x','auto');
	});
	
 	$('.xeditable').on('save', function(e, params) {
		$("#item").focus();
 	});

	$("#item").focus();
	$('#add_item_form').ajaxForm({target: "#count_container", success: itemAddSuccess});
	
	$( "#item" ).autocomplete({
 		source: '<?php echo site_url("items/item_search");?>',
		delay: 150,
 		autoFocus: false,
 		minLength: 0,
 		select: function( event, ui ) 
 		{
 			$.post('<?php echo site_url("items/add_item_to_inventory_count");?>', {item: ui.item.value }, function(response)
			{
				$("#count_container").html(response);
				$('#item').focus();
				itemAddSuccess();
			});	

 		},
	}).data("ui-autocomplete")._renderItem = function (ul, item) {
         return $("<li class='item-suggestions'></li>")
             .data("item.autocomplete", item)
	           .append('<a class="suggest-item"><div class="item-image">' +
							'<img src="' + item.image + '" alt="">' +
						'</div>' +
						'<div class="details">' +
							'<div class="name">' + 
								item.label +
							'</div>' +
							'<span class="attributes">' +
								'<?php echo lang("common_category"); ?>' + ' : <span class="value">' + (item.category ? item.category : <?php echo json_encode(lang('common_none')); ?>) + '</span>' +
							'</span>' +
						'</div>')
             .appendTo(ul);
     };	
	
	$(".finish-count").click(function(e)
	{
		e.preventDefault();
		var $that = $(this);
		
		bootbox.confirm(<?php echo json_encode(lang('items_confirm_finish_count')); ?>, function(result)
		{
			if(result)
			{
				window.location = $that.attr('href');
			}
		});
	});
});

function itemAddSuccess()
{
	<?php if ($mode == 'scan_and_set') { ?>
		var quantity_to_change = $('#register a[data-name="quantity"]').first();
		quantity_to_change.editable('show');
	<?php } ?>		
}
</script>