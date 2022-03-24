<?php $this->load->view("partial/header"); ?>

<div class="manage_buttons">
<div class="manage-row-options hidden">
	<div class="email_buttons price_rules text-center">		

		<?php if ($this->Employee->has_module_action_permission($controller_name, 'delete', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
		<?php echo anchor("$controller_name/delete",
			'<span class="">'.lang('common_delete').'</span>'
			,array('id'=>'delete', 'class'=>'btn btn-red btn-lg disabled delete_inactive ','title'=>lang("common_delete"))); ?>
		<?php } ?>

		<a href="#" class="btn btn-lg btn-clear-selection btn-warning"><?php echo lang('common_clear_selection'); ?></a>
		
	</div>
</div>

	<div class="row">
		<div class="col-md-8 col-sm-8 col-xs-8">
			<?php echo form_open("$controller_name/search",array('id'=>'search_form', 'autocomplete'=> 'off')); ?>
				<div class="search no-left-border">
					<input type="text" class="form-control" name ='search' id='search' value="<?php echo H($search); ?>" placeholder="<?php echo lang('common_search'); ?> <?php echo lang('module_'.$controller_name); ?>"/>
				</div>
					<div class="clear-block <?php echo ($search=='') ? 'hidden' : ''  ?>">
						<a class="clear" href="<?php echo site_url($controller_name.'/clear_state'); ?>">
							<i class="ion ion-close-circled"></i>
						</a>	
					</div>
			</form>	
			
		</div>
		<div class="col-md-4 col-sm-4 col-xs-4">	
			<div class="buttons-list">
				<div class="pull-right-btn">
				  	
					<?php if ($this->Employee->has_module_action_permission('price_rules', 'add_update', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
					  	<a href="<?php echo site_url('price_rules/view/-1'); ?>" id="create_rule" class="btn btn-primary btn-lg"><span class=""><?php echo lang('price_rules_add_rule') ?></span></a>
					  	
				  	<?php } ?>

					</div>
				</div>
			</div>				
		</div>
	</div>
</div>

<div class="main-content">
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
			<div class="text-center">
			<div class="pagination hidden-print alternate text-center" id="pagination_bottom" >
				<?php echo $pagination;?>
			</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() 
		{
			enable_sorting("<?php echo site_url("$controller_name/sorting"); ?>");
			enable_select_all();
			enable_checkboxes();
			enable_row_selection();
			enable_search('<?php echo site_url("$controller_name");?>',<?php echo json_encode(lang("common_confirm_search"));?>);
			enable_delete(<?php echo json_encode(lang($controller_name."_confirm_delete"));?>,<?php echo json_encode(lang($controller_name."_none_selected"));?>);

			<?php if ($this->session->flashdata('success')) { ?>
			show_feedback('success', <?php echo json_encode($this->session->flashdata('success')); ?>, <?php echo json_encode(lang('common_success')); ?>);
			<?php } ?>

			<?php if ($this->session->flashdata('error')) { ?>
			show_feedback('error', <?php echo json_encode($this->session->flashdata('error')); ?>, <?php echo json_encode(lang('common_error')); ?>);
			<?php } ?>				
		});
	</script>
	<?php $this->load->view("partial/footer"); ?>
</div>