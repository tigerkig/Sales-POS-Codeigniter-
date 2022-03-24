<?php 
$locations_to_use = $authenticated_locations;

if (isset($can_view_inventory_at_all_locations) && $can_view_inventory_at_all_locations)
{
	$locations_to_use = $all_locations_in_system;
}

if (count($locations_to_use) > 1) {?>		
<div class="form-group">	
	<?php echo form_label(lang('common_locations').':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 col-sm-3 col-md-3 col-lg-2 control-label')); ?>
		<div class="col-sm-9 col-md-9 col-lg-10">
		<ul id="reports_locations_list" class="list-inline">
		<?php
			foreach($locations_to_use as $location_id => $location_name) 
			{
				$checkbox_options = array(
				'name' => 'reports_selected_location_ids[]',
				'id' => 'reports_selected_location_ids'.$location_id,
				'class' => 'reports_selected_location_ids_checkboxes',
				'value' => $location_id,
				'checked' => in_array($location_id, $reports_selected_location_ids),
				);
																
				echo '<li>'.form_checkbox($checkbox_options). '<label for="reports_selected_location_ids'.$location_id.'"><span></span>'.$location_name.'</label></li>';
			}
		?>
		</ul>
	</div>
</div>
<script type="text/javascript">
$('.reports_selected_location_ids_checkboxes').change(function()
{
	var selected_location_ids = [];
	$("input[name='reports_selected_location_ids[]']:checked").each(function()
	{
      selected_location_ids.push($(this).val());	
	});
	
	$.post('<?php echo site_url('reports/set_selected_location_ids'); ?>', {reports_selected_location_ids: selected_location_ids});
});
</script>
<?php } ?>