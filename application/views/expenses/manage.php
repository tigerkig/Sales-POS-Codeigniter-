<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
	$(document).ready(function() 
	{ 
	    enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>");
	    enable_select_all();
	    enable_checkboxes();
	    enable_row_selection();
	    enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
	    enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);
       enable_cleanup(<?php echo json_encode(lang($controller_name."_confirm_cleanup"));?>);
		 
		 <?php if ($this->session->flashdata('manage_success_message')) { ?>
 			gritter(<?php echo json_encode(lang('common_success')); ?>, <?php echo json_encode($this->session->flashdata('manage_success_message')); ?>,'gritter-item-success',false,false);
		 <?php } ?>
			
	}); 
</script>


<div class="manage_buttons">
	<!-- Css Loader  -->
	<div class="spinner" id="ajax-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>

	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
				<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
					<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
						<i class="ion ion-close-circled"></i>
					</a>	
				</div>

			<?php echo form_close() ?>
			
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<div class="buttons-list">
				<div class="pull-right-btn">

					<?php if ($this->Employee->has_module_action_permission($controller_name, 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						<?php echo 
							anchor("$controller_name/view/-1/",
							'<span class="ion-plus-round visible-xs"></span> '.'<span class="hidden-xs">'.lang($controller_name.'_new').'</span>',
							array('class'=>'btn btn-primary btn-lg', 
								'title'=>lang($controller_name.'_new')));
						?>

					<?php } ?>
					
					<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>				
						<?php echo 
							anchor("$controller_name/delete",
							'<span class="ion-trash-a"></span> '.'<span class="hidden-xs">'.lang("common_delete").'</span>',
							array('id'=>'delete','class'=>'btn btn-red btn-lg disabled','title'=>lang("common_delete"))); 
						?>
					<?php } ?>
					
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row pagination-info">
	<div class="col-md-6">
		<div class="pagination hidden-print alternate text-center" id="pagination_top" >
			<?php echo $pagination;?>
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
						<span class="panel-options custom">
							<div class="pagination pagination-top hidden-print  text-center" id="pagination_top">
								<?php echo $pagination;?>		
							</div>
						</span>
					</h3>
				</div>
				<div class="panel-body nopadding table_holder table-responsive" >
					<?php echo $manage_table; ?>			
				</div>		
				
			</div>
		</div>
	</div>
<div class="row pagination hidden-print alternate text-center" id="pagination_bottom" >
	<?php echo $pagination;?>
</div>
</div>
<?php $this->load->view("partial/footer"); ?>
