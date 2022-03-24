<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
	
	function reload_item_kits_table()
	{
		clearSelections();
			$("#table_holder").load(<?php echo json_encode(site_url("$controller_name/reload_table")); ?>);
	}
	
$(document).ready(function()
{
	
	$("#sortable").sortable({
		items : '.sort',
		containment: "#sortable",
		cursor: "move",
		handle: ".handle",
		revert: 100,
		update: function( event, ui ) {
			$input = ui.item.find("input[type=checkbox]");
			$input.trigger('change');
		}
	});
	
	$("#sortable").disableSelection();
	
	$("#config_columns a").on("click", function(e) {
		e.preventDefault();
		
		if($(this).attr("id") == "reset_to_default")
		{
			//Send a get request wihtout columns will clear column prefs
			$.get(<?php echo json_encode(site_url("$controller_name/save_column_prefs")); ?>, function()
			{
				reload_item_kits_table();
				var $checkboxs = $("#config_columns a").find("input[type=checkbox]");
				$checkboxs.prop("checked", false);
				
				<?php foreach($default_columns as $default_col) { ?>
						$("#config_columns a").find('#'+<?php echo json_encode($default_col);?>).prop("checked", true);
				<?php } ?>
			});
		}
		
		if(!$(e.target).hasClass("handle"))
		{
			var $checkboxs = $(this).find("input[type=checkbox]");
			$checkboxs.prop("checked", !$checkboxs.prop("checked")).trigger("change");
		}
		
		return false;
	});
	
	$("#config_columns input[type=checkbox]").change(
		function(e) {
			var columns = $("#config_columns input:checkbox:checked").map(function(){
    		return $(this).val();
  		}).get();
			
			$.post(<?php echo json_encode(site_url("$controller_name/save_column_prefs")); ?>, {columns:columns}, function(json)
			{
				reload_item_kits_table();
			});
			
	});
	
	enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>");
	enable_select_all();
	enable_checkboxes();
	enable_row_selection();
	enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
	enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
	enable_cleanup(<?php echo json_encode(lang("items_confirm_cleanup"));?>);
    
	$('#generate_barcodes').click(function()
	{
		var selected = get_selected_values();
		
		if (selected.length == 0)
		{
			bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
			return false;
		}

		$("#skip-labels").modal('show');
		return false;
	});
	
	$("#generate_barcodes_form").submit(function()
	{
		var selected = get_selected_values();
		var num_labels_skip = $("#num_labels_skip").val() ? $("#num_labels_skip").val() : 0;
		var url = '<?php echo site_url("item_kits/generate_barcodes");?>'+'/'+selected.join('~')+'/'+num_labels_skip;
		window.location = url;
		return false;
	});

    $('#generate_barcode_labels').click(function()
    {
    	var selected = get_selected_values();
    	if (selected.length == 0)
    	{
    		bootbox.alert(<?php echo json_encode(lang('common_must_select_item_for_barcode')); ?>);
    		return false;
    	}

    	$(this).attr('href','<?php echo site_url("item_kits/generate_barcode_labels");?>/'+selected.join('~'));
    });
	 
	 <?php if ($this->session->flashdata('manage_success_message')) { ?>
		show_feedback('success', <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>, <?php echo json_encode(lang('common_success')); ?>);
	 <?php } ?>
});

function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{
			sortList: [[1,0]],
			headers:
			{
				0: { sorter: false},
				5: { sorter: false}
			}

		});
	}
}
</script>

<div class="modal fade skip-labels" id="skip-labels" role="dialog" aria-labelledby="skipLabels" aria-hidden="true">
    <div class="modal-dialog customer-recent-sales">
      	<div class="modal-content">
	        <div class="modal-header">
	          	<button type="button" class="close" data-dismiss="modal" aria-label=<?php echo json_encode(lang('common_close')); ?>><span aria-hidden="true">&times;</span></button>
	          	<h4 class="modal-title" id="skipLabels"><?php echo lang('common_skip_labels') ?></h4>
	        </div>
	        <div class="modal-body">
				
	          	<?php echo form_open("items/generate_barcodes", array('id'=>'generate_barcodes_form','autocomplete'=> 'off')); ?>				
				<input type="text" class="form-control text-center" name="num_labels_skip" id="num_labels_skip" placeholder="<?php echo lang('common_skip_labels') ?>">
					<?php echo form_submit('generate_barcodes_form',lang("common_submit"),'class="btn btn-block btn-primary"'); ?>
				<?php echo form_close(); ?>
				
	        </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="manage_buttons">
	<!-- Css Loader  -->
	<div class="spinner" id="ajax-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>

	<div class="manage-row-options hidden">
		<div class="email_buttons items text-center">
			<?php echo 
				anchor("$controller_name/generate_barcode_labels",
				'<span class="">'.lang("common_barcode_labels").'</span>',
				array('id'=>'generate_barcode_labels', 
					'class' => 'btn btn-primary btn-lg  disabled ',
					'title'=>lang('common_barcode_labels'))); 
			?>
			<?php echo 
				anchor("$controller_name/generate_barcodes",
				'<span class="">'.lang("common_barcode_sheet").'</span>',
				array('id'=>'generate_barcodes',
				 	'class' => 'btn btn-primary btn-lg  disabled ',
					'title'=>lang('common_barcode_sheet'),
					'target' => '_blank')); 
			?>
			
			<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
				
					<?php echo 
						anchor("$controller_name/delete",
						'<span class="">'.lang("common_delete").'</span>',
						array('id'=>'delete', 
							'class'=>'btn btn-red btn-lg disabled','title'=>lang("common_delete"))); 
					?>
			<?php } ?>
			<a href="#" class="btn btn-lg btn-warning btn-clear-selection"><?php echo lang('common_clear_selection'); ?></a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-9 col-sm-10 col-xs-10">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off', 'class'=>'')); ?>
				<div class="search search-items no-left-border">
					<ul class="list-inline">
						<li>
							&nbsp;
							<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
						</li>
						<li class="hidden-xs">
						<?php echo lang('common_fields'); ?>: 
						<?php 
						echo form_dropdown('fields', array(
							'all'=>lang('common_all'),
							$this->db->dbprefix('item_kits').'.item_kit_id' => lang('common_item_kit_id'),
							$this->db->dbprefix('item_kits').'.item_kit_number' => lang('common_item_number_expanded'),
							$this->db->dbprefix('item_kits').'.product_id' => lang('common_product_id'),
							$this->db->dbprefix('item_kits').'.name' => lang('item_kits_name'),
							$this->db->dbprefix('item_kits').'.description' => lang('common_description'),
							$this->db->dbprefix('item_kits').'.cost_price' => lang('common_cost_price'),
							$this->db->dbprefix('item_kits').'.unit_price' => lang('common_unit_price'),
							$this->db->dbprefix('manufacturers').'.name' => lang('common_manufacturer'),
							$this->db->dbprefix('tags').'.name' => lang('common_tag'),
							),$fields, 'class="form-control" id="fields"');
						?>
						</li>
						<li class="hidden-xs">
							<?php echo lang('common_category'); ?>: 	
							<?php echo form_dropdown('category_id', $categories,$category_id, 'class="form-control" id="category_id"'); ?>
						</li>
						<li>
							<button type="submit" class="btn btn-primary btn-lg"><span class="ion-ios-search-strong"></span><span class="hidden-xs hidden-sm"> <?php echo lang("common_search"); ?></span></button>
						<li>
							<div class="clear-block items-clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
								<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
									<i class="ion ion-close-circled"></i>
								</a>	
							</div>
						</li>
					</ul>
				</div>
			<?php echo form_close() ?>
		</div>
		<div class="col-md-3 col-sm-2 col-xs-2">
			<div class="buttons-list items-buttons">
				<div class="pull-right-btn">
					
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						<?php echo 
							anchor("$controller_name/view/-1/",
							'<span class="">'.lang($controller_name.'_new').'</span>',
							array('class'=>'btn btn-primary btn-lg hidden-sm hidden-xs', 
								'title'=>lang($controller_name.'_new')));
						?>
						
					<?php } ?>

					<div class="piluku-dropdown btn-group">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="ion-android-more-horizontal"></i>
					</button>
					<ul class="dropdown-menu" role="menu">
						
						<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>				
						<li class="visible-sm visible-xs">
							<?php echo 
								anchor("$controller_name/view/-1/",
								'<span class="ion-plus-round"> '.lang('common_add').' '.lang($controller_name.'_new').'</span>',
								array('class'=>'', 
									'title'=>lang($controller_name.'_new')));
							?>
						</li>
						<?php } ?>
						
						<li class="">
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<?php echo anchor("items/categories",
									'<span class="ion-ios-folder-outline"> '.lang("items_manage_categories").'</span>',
									array('class'=>'',
									'title'=>lang('items_manage_categories')));
								?>
							<?php } ?>		
						</li>
						<li class="">
							<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<?php echo anchor("items/manage_tags",
									'<span class="ion-ios-pricetag-outline"> '.lang("items_manage_tags").'</span>',
									array('class'=>'',
									'title'=>lang('items_manage_tags')));
								?>
							<?php } ?>				
						</li>
						<li>
							<?php echo anchor("$controller_name/excel_export",
							'<span class="ion-ios-upload-outline"> '.lang("common_excel_export").'</span>',
								array('class'=>'import ','title'=>lang('common_excel_export')));
							?>
						</li>
						
						<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
							<li>
								<?php echo 
									anchor("$controller_name/cleanup",
									'<span class="ion-loop"> '.lang("item_kits_cleanup_old_item_kits").'</span>',
									array('id'=>'cleanup', 
									'class'=>'','title'=>lang("item_kits_cleanup_old_item_kits"))); 
								?>
							</li>
						<?php }?>
						
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="container-fluid">
		<div class="row manage-table">
			<div class="panel panel-piluku">
				<div class="panel-heading">
				<h3 class="panel-title">
					<?php echo lang('common_list_of').' '.lang('module_'.$controller_name); ?>
					<span title="<?php echo $total_rows; ?> total <?php echo $controller_name?>" class="badge bg-primary tip-left" id="manage_total_items"><?php echo $total_rows; ?></span>
										
					<form id="config_columns">
					<div class="piluku-dropdown btn-group table_buttons pull-right m-left-20">
						<button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<i class="ion-gear-a"></i>
						</button>
						
						<ul id="sortable" class="dropdown-menu dropdown-menu-left col-config-dropdown" role="menu">
								<li class="dropdown-header"><a id="reset_to_default" class="pull-right"><span class="ion-refresh"></span> Reset</a><?php echo lang('common_column_configuration'); ?></li>
																
								<?php foreach($all_columns as $col_key => $col_value) { 
									$checked = '';
									
									if (isset($selected_columns[$col_key]))
									{
										$checked = 'checked ="checked" ';
									}
									?>
									<li class="sort"><a><input <?php echo $checked; ?> name="selected_columns[]" type="checkbox" class="columns" id="<?php echo $col_key; ?>" value="<?php echo $col_key; ?>"><label class="sortable_column_name" for="<?php echo $col_key; ?>"><span></span><?php echo $col_value['label']; ?></label><span class="handle ion-drag"></span></a></li>										
								<?php } ?>
							</ul>
					</div>
					</form>
					
					<span class="panel-options custom">
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>		
							</div>
					</span>
				</h3>
			</div>
			<div class="panel-body nopadding  table_holder table-responsive" id="table_holder" >
						<?php echo $manage_table; ?>			
			</div>
			
		</div>
	</div>
</div>
<div class="text-center">
	<div class="row pagination hidden-print alternate text-center" id="pagination_bottom" >
		<?php echo $pagination;?>
	</div>
</div>
</div>
<?php $this->load->view("partial/footer"); ?>
