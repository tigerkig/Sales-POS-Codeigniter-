<?php $this->load->view("partial/header"); ?>

<?php echo form_open('item_kits/save/'.(!isset($is_clone) ? $item_kit_info->item_kit_id : ''),array('id'=>'item_kit_form','class'=>'form-horizontal')); ?>
<div class="row" id="form">
	<div class="spinner" id="grid-loader" style="display:none">
	  <div class="rect1"></div>
	  <div class="rect2"></div>
	  <div class="rect3"></div>
	</div>
	<div class="col-md-12">
				
		<div class="panel panel-piluku">
				<div class="panel-heading">
	                <h3 class="panel-title">
	                    <i class="ion-edit"></i> 
	                    <?php echo lang("item_kits_info"); ?>
						<small>(<?php echo lang('common_fields_required_message'); ?>)</small>
	                </h3>
		        </div>

			<div class="panel-body">
				<div class="form-heading">
					
				</div>
				<span class="help-block" style="margin-left: 35px"><?php echo lang('item_kits_desc'); ?></span>
				<div class="form-group">
					<?php echo form_label(lang('item_kits_add_item').':', 'item',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'item',
							'id'=>'item'
						));?>
					</div>
				</div>
			</div>
		</div>

				<div class="panel panel-piluku">
				<div class="panel-heading">
	                <h3 class="panel-title">
	                    <i class="ion-ios-list-outline"></i> 
	                    <?php echo lang('item_kits_items_added');?>
	                </h3>
		        </div>

				<div class="panel-body">
				<table id="item_kit_items" class="table table-bordered table-striped text-success text-center">
					<tr>
						<th><?php echo lang('common_delete');?></th>
						<th><?php echo lang('item_kits_item');?></th>
						<th><?php echo lang('item_kits_quantity');?></th>
					</tr>

					<?php foreach ($this->Item_kit_items->get_info($item_kit_info->item_kit_id) as $item_kit_item) {?>
						<tr class="item_kit_item_row">
							<?php
							$item_info = $this->Item->get_info($item_kit_item->item_id);
							?>
							<td><a  href="#" onclick='return deleteItemKitRow(this);'><i class='ion-ios-trash-outline fa-2x text-danger'></i></a></td>
							<td><?php echo H($item_info->name); ?></td>

							<td>
								<div class="form-group table-form-group">
									<input class='form-control quantity' onchange="calculateSuggestedPrices();" id='item_kit_item_<?php echo $item_kit_item->item_id ?>' type='text' name=item_kit_item[<?php echo $item_kit_item->item_id ?>] value='<?php echo to_quantity($item_kit_item->quantity); ?>'/>	
								</div>
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<div class="panel">
			<div class="panel-body">
				<div class="form-group">
					<?php echo form_label(lang('common_item_number_expanded').':', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control form-inps',
						'name'=>'item_kit_number',
						'id'=>'item_kit_number',
						'value'=>$item_kit_info->item_kit_number)
					);?>
					</div>
				</div>
				<?php echo form_hidden('redirect', $redirect); ?>

				<div class="form-group">
					<?php echo form_label(lang('common_product_id').':', 'product_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'name'=>'product_id',
							'id'=>'product_id',
							'class'=>'form-control form-inps',
							'value'=>$item_kit_info->product_id)
						);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('item_kits_name').':', 'name',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control form-inps',
						'name'=>'name',
						'id'=>'name',
						'value'=>$item_kit_info->name)
					);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_category').':', 'category_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  required wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('category_id', $categories,$item_kit_info->category_id, 'class="form-control form-inps" id="category_id"');?>
						<?php if ($this->Employee->has_module_action_permission('items', 'manage_categories', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
								<div>
									<?php echo anchor("items/categories",lang('items_manage_categories'),array('target' => '_blank', 'title'=>lang('items_manage_categories')));?>
								</div>
						<?php } ?>
					</div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_manufacturer').':', 'manufacturer_id',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('manufacturer_id', $manufacturers, $selected_manufacturer,'class="form-control" id="manufacturer_id"');?>
						<?php if ($this->Employee->has_module_action_permission('items', 'manage_manufacturers', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
						<div>
							<?php echo anchor("items/manage_manufacturers",lang('common_manage_manufacturers'),array('target' => '_blank', 'title'=>lang('common_manage_manufacturers')));?>
						</div>
						<?php } ?>
						
					</div>
				</div>
				

				<div class="form-group">
					<?php echo form_label(lang('common_tags').':', 'tags',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'name'=>'tags',
						'id'=>'tags',
						'class'=>'form-control form-inps',
						'value' => $tags,
					));?>
					
					<?php if ($this->Employee->has_module_action_permission('items', 'manage_tags', $this->Employee->get_logged_in_employee_info()->person_id)) {?>
							<div>
								<?php echo anchor("items/manage_tags",lang('items_manage_tags'),array('target' => '_blank', 'title'=>lang('items_manage_tags')));?>
							</div>
					<?php } ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('item_kits_description').':', 'description',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control text-area',
						'value'=>$item_kit_info->description,
						'rows'=>'5',
						'cols'=>'17')
					);?>
					</div>
				</div>

				<div class="form-group">
					<?php echo form_label(lang('common_prices_include_tax').':', 'tax_included',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'tax_included',
							'id'=>'tax_included',
							'class'=>'tax-checkboxes',
							'value'=>1,
							'checked'=>($item_kit_info->tax_included || (!$item_kit_info->item_kit_id && $this->config->item('prices_include_tax'))) ? 1 : 0)
						);?>
						<label for="tax_included"><span></span></label>
					</div>
				</div>

				<?php if ($this->Employee->has_module_action_permission('item_kits','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_kit_info->name=="") { ?>
					<div class="form-group">
						<?php echo form_label(lang('common_cost_price').' ('.lang('common_without_tax').'):', 'cost_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps',
							'name'=>'cost_price',
							'id'=>'cost_price',
							'value'=>$item_kit_info->cost_price ? to_currency_no_money($item_kit_info->cost_price) : '')
						);?>
						</div>
					</div>
				<?php 
				}
				else
				{
					echo form_hidden('cost_price', $item_kit_info->cost_price);
				}
				?>
				<?php if ($this->Employee->has_module_action_permission('item_kits','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_kit_info->name=="") { ?>
		
				<div class="form-group">
					<?php echo form_label(lang('common_change_cost_price_during_sale').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'change_cost_price',
							'id'=>'change_cost_price',
							'class' => 'delete-checkbox',
							'value'=>1,
							'checked'=>(boolean)(($item_kit_info->change_cost_price))));
						?>
						<label for="change_cost_price"><span></span></label>
					</div>
				</div>
				<?php } elseif($item_kit_info->change_cost_price) { 
					echo form_hidden('change_cost_price', 1);
				?>
					
				<?php } ?> 
				
				
				<?php
				if ($this->config->item('enable_customer_loyalty_system') && $this->config->item('loyalty_option') == 'advanced')
				{
				?>
				
				<div class="form-group">
					<?php echo form_label(lang('common_disable_loyalty').':', 'disable_loyalty',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'disable_loyalty',
							'id'=>'disable_loyalty',
								'class'=>'delete-checkbox',
							'value'=>1,
							'checked'=>($item_kit_info->disable_loyalty)? 1 : 0)
						);?>
						<label for="disable_loyalty"><span></span></label>
					</div>
				</div>
				
				<?php
				}
				?>
				
					
				<?php if ($this->Employee->has_module_action_permission('item_kits','see_cost_price', $this->Employee->get_logged_in_employee_info()->person_id) or $item_kit_info->name=="") { ?>				
				
				<?php if ($this->config->item('enable_margin_calculator')) { ?>
				<div class="form-group">
					<?php echo form_label(lang('common_margin').':', 'margin',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">
					    <?php echo form_input(array(
					        'name'=>'margin',
					        'size'=>'8',
							'class'=>'form-control',
					        'id'=>'margin',
					        'value'=>'',
							  'placeholder' => lang('common_enter_margin_percent'),
							)
					    );?>
						 
				    </div>
				</div>
				<?php } ?>
				
				<?php } ?>
				<div class="form-group">
					<?php echo form_label(lang('common_unit_price').':', 'unit_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  ')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
					<?php echo form_input(array(
						'class'=>'form-control form-inps',
						'name'=>'unit_price',
						'id'=>'unit_price',
						'value'=>$item_kit_info->unit_price ? to_currency_no_money($item_kit_info->unit_price,10) : '')
					);?>
					</div>
				</div>
				
				<?php if ($this->config->item('limit_manual_price_adj')) { ?>
				
				<div class="form-group">
					<?php echo form_label(lang('common_min_edit_price').':', 'min_edit_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">	
						   	<?php echo form_input(array(
										'type'=> 'number',
										'min'=> '0',
						        'name'=>'min_edit_price',
										'class'=>'form-control',
						        'id'=>'min_edit_price',
						        'value'=> $item_kit_info->min_edit_price ? to_quantity($item_info->min_edit_price) : '')
						    );?>			
				    </div>
				</div>
				
				<div class="form-group">
					<?php echo form_label(lang('common_max_edit_price').':', 'max_edit_price',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">
						   	<?php echo form_input(array(
										'type'=> 'number',
										'min'=> '0',
						        'name'=>'max_edit_price',
										'class'=>'form-control',
						        'id'=>'max_edit_price',
						        'value'=> $item_kit_info->max_edit_price ? to_quantity($item_info->max_edit_price) : '')
						    );?>
				    </div>
				</div>
				<div class="form-group">
					<?php echo form_label(lang('common_max_discount_percent').':', 'max_discount_percent',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
				    <div class="col-sm-9 col-md-9 col-lg-10">
							<div class="input-group">
					    <?php echo form_input(array(
									'type'=> 'number',
									'min'=> '0',
									'max'=> '100',
					        'name'=>'max_discount_percent',
									'class'=>'form-control',
					        'id'=>'max_discount_percent',
					        'value'=> $item_kit_info->max_discount_percent ? to_quantity($item_kit_info->max_discount_percent) : '')
					    );?>
							<span class="input-group-addon bg"><span class="">%</span></span>
							
						</div>
				    </div>
				</div>
				
				<?php } ?>

				<?php
				if ($this->config->item('enable_ebt_payments')) { ?>
					<div class="form-group">
					
					<?php echo form_label(lang('common_is_ebt_item').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
						'name'=>'is_ebt_item',
						'id'=>'is_ebt_item',
						'class' => 'is_ebt_item delete-checkbox',
						'value'=>1,
						'checked'=>(boolean)(($item_kit_info->is_ebt_item))));
					?>
					<label for="is_ebt_item"><span></span></label>
				</div>
			</div>
			<?php } ?>

				<?php foreach($tiers as $tier) { ?>	
					
					<?php
					
					$selected_tier_type_option = '';
					$tier_price_value = '';
					
					if ($tier_prices[$tier->id] !== FALSE)
					{
						if ($tier_prices[$tier->id]->unit_price !== NULL)
						{
							$selected_tier_type_option = 'unit_price';
							$tier_price_value = to_currency_no_money($tier_prices[$tier->id]->unit_price);
							
						}
						elseif($tier_prices[$tier->id]->percent_off !== NULL)
						{
							$selected_tier_type_option = 'percent_off';									
							$tier_price_value = to_quantity($tier_prices[$tier->id]->percent_off);						
						}
						elseif($tier_prices[$tier->id]->cost_plus_fixed_amount !== NULL)
						{
							$selected_tier_type_option = 'cost_plus_fixed_amount';									
							$tier_price_value = to_currency_no_money($tier_prices[$tier->id]->cost_plus_fixed_amount);						
						}
						else
						{
							$selected_tier_type_option = 'cost_plus_percent';									
							$tier_price_value = to_quantity($tier_prices[$tier->id]->cost_plus_percent);						
						}						
					}
					
	
					?>
					<div class="form-group">
						<?php echo form_label($tier->name.':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
						<div class='col-sm-9 col-md-9 col-lg-10'>
						<?php echo form_input(array(
							'class'=>'form-control form-inps margin10',
							'name'=>'item_kit_tier['.$tier->id.']',
							'size'=>'8',
							'value' => $tier_price_value,
						));?>

							<?php 
					
							echo form_dropdown('tier_type['.$tier->id.']', $tier_type_options, $selected_tier_type_option,'class="form-control"');?>
						</div>
					</div>

				<?php } ?>

				<div class="form-group override-commission-container">
					<?php echo form_label(lang('common_override_default_commission').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'override_default_commission',
							'id'=>'override_default_commission',
							'class' => 'override_default_commission delete-checkbox',
							'value'=>1,
							'checked'=>(boolean)(($item_kit_info->commission_percent != '') || ($item_kit_info->commission_fixed != ''))));
						?>
						<label for="override_default_commission"><span></span></label>
					</div>
				</div>

				<div class="commission-container <?php if (!($item_kit_info->commission_percent != '') && !($item_kit_info->commission_fixed != '')){echo 'hidden';} ?>">
						<div class="form-group">
						<?php echo form_label(lang('reports_commission'), 'commission_value',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label wide')); ?>
						<div class='col-sm-9 col-md-9 col-lg-10'>
						<?php echo form_input(array(
							'name'=>'commission_value',
							'id'=>'commission_value',
							'size'=>'8',
							'class'=>'form-control margin10 form-inps', 
							'value'=> $item_kit_info->commission_fixed !='' ? to_quantity($item_kit_info->commission_fixed, FALSE) : to_quantity($item_kit_info->commission_percent, FALSE))
						);?>

						<?php echo form_dropdown('commission_type', array('percent' => lang('common_percentage'), 'fixed' => lang('common_fixed_amount')), $item_kit_info->commission_fixed > 0 ? 'fixed' : 'percent', 'id="commission_type"');?>
						</div>
					</div>
					
					<div class="form-group" id="commission-percent-calculation-container">	
						<?php echo form_label(lang('common_commission_percent_calculation').': ', 'commission_percent_type',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('commission_percent_type', array(
							'selling_price'  => lang('common_unit_price'),
							'profit'    => lang('common_profit'),
							),
							$item_kit_info->commission_percent_type,
							array('id' => 'commission_percent_type'))
							?>
						</div>
					</div>

				</div>
				
				<div class="form-group override-taxes-container">
					<?php echo form_label(lang('common_override_default_tax').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
					
					<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_checkbox(array(
							'name'=>'override_default_tax',
							'id'=>'override_default_tax',
							'class' => 'override_default_tax_checkbox tax-checkboxes',
							'value'=>1,
							'checked'=>(boolean)$item_kit_info->override_default_tax));
						?>
						<label for="override_default_tax"><span></span></label>
					</div>
				</div>

				<div class="tax-container main <?php if (!$item_kit_info->override_default_tax){echo 'hidden';} ?>">
					
					<div class="form-group">	
						<?php echo form_label(lang('common_tax_class').': ', 'tax_class',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_dropdown('tax_class', $tax_classes, $item_kit_info->tax_class_id, array('id' =>'tax_class', 'class' => 'form-control tax_class'));?>
						</div>
					</div>
					
					<div class="form-group">
						<h4 class="text-center"><?php echo lang('common_or') ?></h4>
					</div>
					
						
					<div class="form-group">
					<?php echo form_label(lang('common_tax_1').':', 'tax_name_1',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps margin10',
							'name'=>'tax_names[]',
							'placeholder' => lang('common_tax_name'),
							'id'=>'tax_name_1',
							'size'=>'8',
							'value'=> isset($item_kit_tax_info[0]['name']) ? $item_kit_tax_info[0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name')))
						);?>
						</div>
						<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps-tax',
							'name'=>'tax_percents[]',
							'placeholder' => lang('common_tax_percent'),
							'id'=>'tax_percent_name_1',
							'size'=>'3',
							'value'=> isset($item_kit_tax_info[0]['percent']) ? $item_kit_tax_info[0]['percent'] : '')
						);?>
						<div class="tax-percent-icon">%</div>
						<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
						</div>
					</div>

					<div class="form-group">
					<?php echo form_label(lang('common_tax_2').':', 'tax_name_2',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps margin10',
							'name'=>'tax_names[]',
							'placeholder' => lang('common_tax_name'),
							'id'=>'tax_name_2',
							'size'=>'8',
							'value'=> isset($item_kit_tax_info[1]['name']) ? $item_kit_tax_info[1]['name'] : ($this->Location->get_info_for_key('default_tax_2_name') ? $this->Location->get_info_for_key('default_tax_2_name') : $this->config->item('default_tax_2_name')))
						);?>
						</div>
						<label for="tax_percent_name_2" class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
						<div class="col-sm-9 col-md-9 col-lg-10">
						<?php echo form_input(array(
							'class'=>'form-control form-inps-tax',
							'name'=>'tax_percents[]',
							'placeholder' => lang('common_tax_percent'),
							'id'=>'tax_percent_name_2',
							'size'=>'3',
							'value'=> isset($item_kit_tax_info[1]['percent']) ? $item_kit_tax_info[1]['percent'] : '')
						);?>
						<div class="tax-percent-icon">%</div>
						<div class="clear"></div>
						<?php echo form_checkbox('tax_cumulatives[]', '1', isset($item_kit_tax_info[1]['cumulative']) && $item_kit_tax_info[1]['cumulative'] ? (boolean)$item_kit_tax_info[1]['cumulative'] : (boolean)$this->config->item('default_tax_2_cumulative'), 'class="cumulative_checkbox" id="tax_cumulatives"'); ?>
					    <label for="tax_cumulatives"><span></span></label>
					    <span class="cumulative_label">
						<?php echo lang('common_cumulative'); ?>
					    </span>
						</div>
					</div>
				
				
					<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3" style="visibility: <?php echo isset($item_kit_tax_info[2]['name']) ? 'hidden' : 'visible';?>">
						<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
					</div>
					
				
					<div class="more_taxes_container" style="display: <?php echo isset($item_kit_tax_info[2]['name']) ? 'block' : 'none';?>">
						<div class="form-group">
						<?php echo form_label(lang('common_tax_3').':', 'tax_name_3',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps margin10',
								'name'=>'tax_names[]',
								'placeholder' => lang('common_tax_name'),
								'id'=>'tax_name_3',
								'size'=>'8',
								'value'=> isset($item_kit_tax_info[2]['name']) ? $item_kit_tax_info[2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name')))
							);?>
							</div>
							<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps-tax',
								'name'=>'tax_percents[]',
								'placeholder' => lang('common_tax_percent'),
								'id'=>'tax_percent_name_3',
								'size'=>'3',
								'value'=> isset($item_kit_tax_info[2]['percent']) ? $item_kit_tax_info[2]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>
						
						<div class="form-group">
						<?php echo form_label(lang('common_tax_4').':', 'tax_name_4',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps margin10',
								'name'=>'tax_names[]',
								'placeholder' => lang('common_tax_name'),
								'id'=>'tax_name_4',
								'size'=>'8',
								'value'=> isset($item_kit_tax_info[3]['name']) ? $item_kit_tax_info[3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name')))
							);?>
							</div>
							<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps-tax',
								'name'=>'tax_percents[]',
								'placeholder' => lang('common_tax_percent'),
								'id'=>'tax_percent_name_4',
								'size'=>'3',
								'value'=> isset($item_kit_tax_info[3]['percent']) ? $item_kit_tax_info[3]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>

						<div class="form-group">
						<?php echo form_label(lang('common_tax_5').':', 'tax_name_5',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps margin10',
								'name'=>'tax_names[]',
								'placeholder' => lang('common_tax_name'),
								'id'=>'tax_name_5',
								'size'=>'8',
								'value'=> isset($item_kit_tax_info[4]['name']) ? $item_kit_tax_info[4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name')))
							);?>
							</div>
							<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
							<div class="col-sm-9 col-md-9 col-lg-10">
							<?php echo form_input(array(
								'class'=>'form-control form-inps-tax',
								'name'=>'tax_percents[]',
								'placeholder' => lang('common_tax_percent'),
								'id'=>'tax_percent_name_5',
								'size'=>'3',
								'value'=> isset($item_kit_tax_info[4]['percent']) ? $item_kit_tax_info[4]['percent'] : '')
							);?>
							<div class="tax-percent-icon">%</div>
							<div class="clear"></div>
							<?php echo form_hidden('tax_cumulatives[]', '0'); ?>
							</div>
						</div>				
					</div>		
					<div class="clear"></div>
				</div>	

				<?php if ($this->Location->count_all() > 1) {?>
		
		<div class="panel">
			<div class="panel-body">
		
					<?php foreach($locations as $location) { 					
					if($location->name == $authenticated_locations[$current_logged_in_location_id]) { ?>
						<div class="item-current-location">
							<div class="form-heading">
								<?php echo $location->name; ?> (<?php echo lang('common_current_location'); ?>)
							</div>
						</div>
					<?php } else { ?>
					<div>
						<div class="form-heading">
							<?php echo $location->name; ?>
						</div>
					<?php } ?>

					
							<div class="form-group override-prices-container">
								<?php echo form_label(lang('common_items_override_prices').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_checkbox(array(
										'name'=>'locations['.$location->location_id.'][override_prices]',
										'id'=>'locations['.$location->location_id.'][override_prices]',
										'class' => 'override_prices_checkbox tax-checkboxes',
										'value'=>1,
										'checked'=>(boolean)isset($location_item_kits[$location->location_id]) && is_object($location_item_kits[$location->location_id]) && $location_item_kits[$location->location_id]->is_overwritten));
									?>
									<label for="<?php echo 'locations['.$location->location_id.'][override_prices]' ?>"><span></span></label>
								</div>
							</div>

							<div class="item-kit-location-price-container <?php if ($location_item_kits[$location->location_id] === FALSE || !$location_item_kits[$location->location_id]->is_overwritten){echo 'hidden';} ?>">	
								<div class="form-group">
								<?php echo form_label(lang('common_cost_price').' ('.lang('common_without_tax').'):', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'class'=>'form-control form-inps',
											'name'=>'locations['.$location->location_id.'][cost_price]',
											'size'=>'8',
											'value'=> $location_item_kits[$location->location_id]->item_kit_id !== '' && $location_item_kits[$location->location_id]->cost_price ? to_currency_no_money($location_item_kits[$location->location_id]->cost_price, 10): ''
										)
										);?>
								</div>
							</div>

								<div class="form-group">
								<?php echo form_label(lang('common_unit_price').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps',
										'name'=>'locations['.$location->location_id.'][unit_price]',
										'size'=>'8',
										'value'=>$location_item_kits[$location->location_id]->item_kit_id !== '' &&  $location_item_kits[$location->location_id]->unit_price ? to_currency_no_money($location_item_kits[$location->location_id]->unit_price, 10): ''
										)
									);?>
									</div>
								</div>

								<?php foreach($tiers as $tier) { 
									
									
									$selected_location_tier_type_option = '';
									$tier_price_value = '';
						
									if ($location_tier_prices[$location->location_id][$tier->id] !== FALSE)
									{
										if ($location_tier_prices[$location->location_id][$tier->id]->unit_price !== NULL)
										{
											$selected_location_tier_type_option = 'unit_price';
											$tier_price_value = to_currency_no_money($location_tier_prices[$location->location_id][$tier->id]->unit_price);
										}
										elseif($location_tier_prices[$location->location_id][$tier->id]->percent_off !== NULL)
										{
											$selected_location_tier_type_option = 'percent_off';		
											$tier_price_value = to_quantity($location_tier_prices[$location->location_id][$tier->id]->percent_off);						
										}
										elseif($location_tier_prices[$location->location_id][$tier->id]->cost_plus_fixed_amount !== NULL)
										{
											$selected_location_tier_type_option = 'cost_plus_fixed_amount';				
											$tier_price_value = to_currency_no_money($location_tier_prices[$location->location_id][$tier->id]->cost_plus_fixed_amount);											
										}
										else
										{
											$selected_location_tier_type_option = 'cost_plus_percent';	
											$tier_price_value = to_quantity($location_tier_prices[$location->location_id][$tier->id]->cost_plus_percent);						
										}
									}
									
				

									?>
									<div class="form-group">
										<?php echo form_label($tier->name.':', null,array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label ')); ?>
										<div class="col-sm-9 col-md-9 col-lg-10">
										<?php echo form_input(array(
											'class'=>'form-control form-inps margin10',
											'name'=>'locations['.$location->location_id.'][item_tier]['.$tier->id.']',
											'size'=>'8',
											'value' => $tier_price_value,
										));?>

										<?php	echo form_dropdown('locations['.$location->location_id.'][tier_type]['.$tier->id.']', $tier_type_options, $selected_location_tier_type_option, array('class' => 'form-control'));?>
										</div>
									</div>

								<?php } ?>
							</div>
							<div class="form-group override-taxes-container">
								<?php echo form_label(lang('common_override_default_tax').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>

								<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_checkbox(array(
										'name'=>'locations['.$location->location_id.'][override_default_tax]',
										'id'=>'locations['.$location->location_id.'][override_default_tax]',
										'class' => 'override_default_tax_checkbox tax-checkboxes',
										'value'=>1,
										'checked'=> $location_item_kits[$location->location_id]->item_kit_id !== '' ? (boolean)$location_item_kits[$location->location_id]->override_default_tax: FALSE
										));
									?>
									<label for="<?php echo 'locations['.$location->location_id.'][override_default_tax]' ?>"><span></span></label>
								</div>
							</div>

							<div class="tax-container <?php if ($location_item_kits[$location->location_id] === FALSE || !$location_item_kits[$location->location_id]->override_default_tax){echo 'hidden';} ?>">	
								
								<div class="form-group">	
									<?php echo form_label(lang('common_tax_class').': ', 'tax_class',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_dropdown('locations['.$location->location_id.'][tax_class]', $tax_classes, $location_item_kits[$location->location_id]->tax_class_id, array('id' =>'tax_class', 'class' => 'form-control tax_class'));?>
									</div>
								</div>
					
								<div class="form-group">
									<h4 class="text-center"><?php echo lang('common_or') ?></h4>
								</div>
								
								
								<div class="form-group">
								<?php echo form_label(lang('common_tax_1').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps margin10',
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'placeholder' => lang('common_tax_name'),
										'size'=>'8',
										'value' => isset($location_taxes[$location->location_id][0]['name']) ? $location_taxes[$location->location_id][0]['name'] : ($this->Location->get_info_for_key('default_tax_1_name') ? $this->Location->get_info_for_key('default_tax_1_name') : $this->config->item('default_tax_1_name'))
									));?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps-tax margin10',
										'name'=>'locations['.$location->location_id.'][tax_percents][]',
										'placeholder' => lang('common_tax_percent'),
										'size'=>'3',
										'value' => isset($location_taxes[$location->location_id][0]['percent']) ? $location_taxes[$location->location_id][0]['percent'] : ''
									));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>
							<div class="form-group">
							<?php echo form_label(lang('common_tax_2').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps margin10',
									'name'=>'locations['.$location->location_id.'][tax_names][]',
									'placeholder' => lang('common_tax_name'),
									'size'=>'8',
									'value' => isset($location_taxes[$location->location_id][1]['name']) ? $location_taxes[$location->location_id][1]['name'] : ($this->Location->get_info_for_key('default_tax_2_name') ? $this->Location->get_info_for_key('default_tax_2_name') : $this->config->item('default_tax_2_name'))
									)
								);?>
								</div>
								<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
								<div class="col-sm-9 col-md-9 col-lg-10">
								<?php echo form_input(array(
									'class'=>'form-control form-inps-tax',
									'name'=>'locations['.$location->location_id.'][tax_percents][]',
									'placeholder' => lang('common_tax_percent'),
									'size'=>'3',
									'value' => isset($location_taxes[$location->location_id][1]['percent']) ? $location_taxes[$location->location_id][1]['percent'] : ''
									)
								);?>
								<div class="tax-percent-icon">%</div>
								<div class="clear"></div>
								<?php echo form_checkbox('locations['.$location->location_id.'][tax_cumulatives][]', '1', isset($location_taxes[$location->location_id][1]['cumulative']) ? (boolean)$location_taxes[$location->location_id][1]['cumulative'] :(boolean)$this->config->item('default_tax_2_cumulative'), 'class="cumulative_checkbox" id="locations['.$location->location_id.'][tax_cumulatives]"'); ?>
							    <label for="<?php echo 'locations['.$location->location_id.'][tax_cumulatives]' ?>"><span></span></label>
							    <span class="cumulative_label">
								<?php echo lang('common_cumulative'); ?>
							    </span>
								</div>
			
								<div class="col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3"  style="visibility: <?php echo isset($item_tax_info[2]['name']) ? 'hidden' : 'visible';?>">
									<a href="javascript:void(0);" class="show_more_taxes"><?php echo lang('common_show_more');?> &raquo;</a>
								</div>

							</div>
							
							
							<div class="more_taxes_container" style="display: <?php echo isset($location_taxes[$location->location_id][2]['name']) ? 'block' : 'none';?>">
								<div class="form-group">
								<?php echo form_label(lang('common_tax_3').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps margin10',
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'placeholder' => lang('common_tax_name'),
										'size'=>'8',
										'value' => isset($location_taxes[$location->location_id][2]['name']) ? $location_taxes[$location->location_id][2]['name'] : ($this->Location->get_info_for_key('default_tax_3_name') ? $this->Location->get_info_for_key('default_tax_3_name') : $this->config->item('default_tax_3_name'))
									));?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps-tax',
										'name'=>'locations['.$location->location_id.'][tax_percents][]',
										'placeholder' => lang('common_tax_percent'),
										'size'=>'3',
										'value' => isset($location_taxes[$location->location_id][2]['percent']) ? $location_taxes[$location->location_id][2]['percent'] : ''
									));?>
									<div class="tax-percent-icon">%</div>
								<div class="clear"></div>
									<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>

								<div class="form-group">
								<?php echo form_label(lang('common_tax_4').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps margin10',
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'placeholder' => lang('common_tax_name'),
										'size'=>'8',
										'value' => isset($location_taxes[$location->location_id][3]['name']) ? $location_taxes[$location->location_id][3]['name'] : ($this->Location->get_info_for_key('default_tax_4_name') ? $this->Location->get_info_for_key('default_tax_4_name') : $this->config->item('default_tax_4_name'))
									));?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps-tax',
										'name'=>'locations['.$location->location_id.'][tax_percents][]',
										'placeholder' => lang('common_tax_percent'),
										'size'=>'3',
										'value' => isset($location_taxes[$location->location_id][3]['percent']) ? $location_taxes[$location->location_id][3]['percent'] : ''
									));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>


								<div class="form-group">
								<?php echo form_label(lang('common_tax_5').':', '',array('class'=>'col-sm-3 col-md-3 col-lg-2 control-label  wide')); ?>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps margin10',
										'name'=>'locations['.$location->location_id.'][tax_names][]',
										'placeholder' => lang('common_tax_name'),
										'size'=>'8',
										'value' => isset($location_taxes[$location->location_id][4]['name']) ? $location_taxes[$location->location_id][4]['name'] : ($this->Location->get_info_for_key('default_tax_5_name') ? $this->Location->get_info_for_key('default_tax_5_name') : $this->config->item('default_tax_5_name'))
									));?>
									</div>
									<label class="col-sm-3 col-md-3 col-lg-2 control-label  wide">&nbsp;</label>
									<div class="col-sm-9 col-md-9 col-lg-10">
									<?php echo form_input(array(
										'class'=>'form-control form-inps-tax margin10',
										'name'=>'locations['.$location->location_id.'][tax_percents][]',
										'placeholder' => lang('common_tax_percent'),
										'size'=>'3',
										'value' => isset($location_taxes[$location->location_id][4]['percent']) ? $location_taxes[$location->location_id][4]['percent'] : ''
									));?>
									<div class="tax-percent-icon">%</div>
									<div class="clear"></div>
									<?php echo form_hidden('locations['.$location->location_id.'][tax_cumulatives][]', '0'); ?>
									</div>
								</div>
							</div>
							
							
							
							
						</div>	
						</div>	
						<?php } /*End foreach locations*/ ?>
				<?php } /*End if for multi locations*/?>
		
		
		
			

		
		
			<div class="form-actions pull-right">
				<?php
				echo form_submit(array(
					'name'=>'submit',
					'id'=>'submit',
					'value'=>lang('common_submit'),
					'class'=>'submit_button floating-button btn btn-primary')
				);
				?>
			</div>

	
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
</div>
<script type='text/javascript'>

function commission_change()
{
	if ($("#commission_type").val() == 'percent')
	{
		$("#commission-percent-calculation-container").show();
	}
	else
	{
		$("#commission-percent-calculation-container").hide();						
	}
}
$("#commission_type").change(commission_change);
$(document).ready(commission_change);

function get_taxes()
{
	var taxes = [];
	
	if (!$("#override_default_tax").prop('checked'))
	{
		var default_taxes = <?php echo json_encode($this->Item_kit_taxes_finder->get_info($item_kit_info->item_kit_id)) ?>;
	
		for(var k = 0;k<default_taxes.length;k++)
		{
			taxes.push({'percent': parseFloat(default_taxes[k]['percent']), 'cumulative':default_taxes[k]['cumulative'] == 1});
		}	
	}
	else
	{
		var k=0;
		
		$('.tax-container.main input[name="tax_percents[]"]').each(function()
		{
			if ($(this).val())
			{
				taxes.push({'percent': parseFloat($(this).val()), 'cumulative': k==1 && $("#tax_cumulatives").prop('checked')});
			}
			
			k++;
		});	
	}
	return taxes;
	
}

function get_total_tax_percent()
{
	var total_tax_percent = 0;
	var taxes = get_taxes();
	for(var k = 0;k<taxes.length;k++)
	{
		total_tax_percent += parseFloat(taxes[k]['percent']);
	}
	
	return total_tax_percent;
}

function are_taxes_cumulative()
{
	var taxes = get_taxes();
	
	return (taxes.length == 2 && taxes[1].cumulative);
}

function calculate_margin_percent()
{
	if ($("#tax_included").prop('checked') )
	{
		var cost_price = parseFloat($('#cost_price').val());
		var unit_price = parseFloat($('#unit_price').val());

		var cumulative = are_taxes_cumulative();
		
		if (!cumulative)
		{
			//Margin amount
			//(100*.1)
			//100 + (100*.1) = 118.80 * .08 
	
			//cost price 100.00
			//8% tax
			//margin 10%
			//110.00 before tax
			//selling price 118.80
			//100 * 1.1 = profit 10%	
	
	
			// X = COST PRICE
			// Y = MARGIN PERCENT
			// Z = SELLING PRICE
			// Q = TAX PERCENT
			//100 * (1+ (10/100)) = 118.80 - (100 * (1+ (10/100)) * 8/100);
	
			//X * (1+Y/100) = Z - (X * (1+(Y/100)) * Q/100)
			//Y = -(100 ((Q+100) X-100 Z))/((Q+100) X) and (Q+100) X!=0

			var tax_percent = parseFloat(get_total_tax_percent());
		
			var Z = unit_price;
			var X = cost_price;
			var Q = tax_percent;
			var margin_percent = -(100*((Q+100)*X-100*Z))/((Q+100)*X);
		}
		else
		{
			var taxes = get_taxes();
			var tax_1 = 1+(taxes[0]['percent']/100);
			var tax_2 = 1+(taxes[1]['percent']/100);
			margin_percent = (unit_price / (cost_price * tax_1 * tax_2) - 1) * 100;
		}

	}
	else
	{
		var cost_price = parseFloat($('#cost_price').val());
		var unit_price = parseFloat($('#unit_price').val());
		var margin_percent =  -100 + (100*(unit_price/cost_price));
	}

	margin_percent = parseFloat(Math.round(margin_percent * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
	
	$('#margin').val(margin_percent + '%');
}

function calculate_margin_price()
{		
	if ($("#tax_included").prop('checked') )
	{		
		var cost_price = parseFloat($('#cost_price').val());
		var margin_percent = parseFloat($("#margin").val());
		
		var cumulative = are_taxes_cumulative();
		
		if (!cumulative)
		{
			//Margin amount
			//(100*.1)
			//100 + (100*.1) = 118.80 * .08 
	
			//cost price 100.00
			//8% tax
			//margin 10%
			//110.00 before tax
			//selling price 118.80
			//100 * 1.1 = profit 10%	
	
	
			// X = COST PRICE
			// Y = MARGIN PERCENT
			// Z = SELLING PRICE
			// Q = TAX PERCENT
			//100 * (1+ (10/100)) = 118.80 - (100 * (1+ (10/100)) * 8/100);
	
			//X * (1+Y/100) = Z - (X * (1+(Y/100)) * Q/100)
			//Z = (Q X Y+100 Q X+100 X Y+10000 X)/10000
			
			var tax_percent = get_total_tax_percent();
				
			var X = cost_price;
			var Y = margin_percent;
			var Q = tax_percent;
		
			var margin_price = (Q*X*Y+100*Q*X+100*X*Y+10000*X)/10000;		
		}
		else
		{
			var marked_up_price_before_tax = cost_price * (1+(margin_percent/100));
			
			var taxes = get_taxes();
			var cumulative_tax_percent = taxes[1]['percent'];
			
			var first_tax = (marked_up_price_before_tax*(taxes[0]['percent']/100));
			var second_tax = (marked_up_price_before_tax + first_tax) *(taxes[1]['percent']/100);
			var margin_price = marked_up_price_before_tax + first_tax + second_tax;
		}
		
		margin_price = parseFloat(Math.round(margin_price * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
	}
	else
	{
		var cost_price = parseFloat($('#cost_price').val());
		var margin_percent = parseFloat($("#margin").val());

		var margin_price = cost_price + (cost_price / 100 * (margin_percent));
		margin_price = parseFloat(Math.round(margin_price * 100) / 100).toFixed(<?php echo json_encode($decimals); ?>);
		
	}

	$('#unit_price').val(margin_price);
}


//Add payment to the sale when hit enter on amount tendered input
$('#item').bind('keypress', function(e) {
	if(e.keyCode==13)
	{
		e.preventDefault();
		
		$.post('<?php echo site_url('item_kits/get_item_info'); ?>', {item_number: $("#item").val()}, function(response)
		{
			if(response)
			{
				addItemToKit(response.item_id,response.name);
			}
			else
			{
				show_feedback('error', <?php echo json_encode(lang('items_kit_unable_to_add_item'));?>, <?php echo json_encode(lang('common_error'));?>);
			}
			
		},'json');
		
	}
});
	
	function addItemToKit(value,label)
	{
		$( "#item" ).val("");
		if ($("#item_kit_item_"+value).length ==1)
		{
			$("#item_kit_item_"+value).val(parseFloat($("#item_kit_item_"+value).val()) + 1);
		}
		else
		{
			$("#item_kit_items").append("<tr class='item_kit_item_row'><td><a  href='#' onclick='return deleteItemKitRow(this);'><i class='ion-ios-trash-outline fa-2x text-danger'></i></a></td><td>"+label+"</td><td><div class='form-group table-form-group'><input class='quantity form-control' onchange='calculateSuggestedPrices();' id='item_kit_item_"+value+"' type='text' name=item_kit_item["+value+"] value='1'/></td></tr>");
		}
	
		calculateSuggestedPrices();
	}
	
	$( "#item" ).autocomplete({
 		source: '<?php echo site_url("item_kits/item_search");?>',
		delay: 150,
 		autoFocus: false,
 		minLength: 0,
 		select: function( event, ui ) 
 		{
			addItemToKit(ui.item.value,ui.item.label);
			return false;

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

//validation and submit handling
$(document).ready(function()
{
	<?php if ($this->config->item('enable_margin_calculator')) { ?>
	if ($('#unit_price').val() && $('#cost_price').val())
	{
		calculate_margin_percent();
	}
	
	$('#margin, #cost_price,.tax-container.main input[name="tax_percents[]"]').keyup(function()
	{
		if($("#margin").val() != '')
		{
			calculate_margin_price();
		}
	});
	<?php } ?>
	
	$('#category_id').selectize({
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_new_category')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		}
	});
    setTimeout(function(){$(":input:visible:first","#item_kit_form").focus();},100);
	
	$('#tags').selectize({
		delimiter: ',',
		loadThrottle : 215,
		persist: false,
		valueField: 'value',
		labelField: 'label',
		searchField: 'label',
		create: true,
		render: {
	      option_create: function(data, escape) {
				var add_new = <?php echo json_encode(lang('common_add_new_tag')) ?>;
	        return '<div class="create">'+escape(add_new)+' <strong>' + escape(data.input) + '</strong></div>';
	      }
		},
		load: function(query, callback) {
			if (!query.length) return callback();
			$.ajax({
				url:'<?php echo site_url("item_kits/tags");?>'+'?term='+encodeURIComponent(query),
				type: 'GET',
				error: function() {
					callback();
				},
				success: function(res) {
					res = $.parseJSON(res);
					callback(res);
				}
			});
		}
	});
	
	$(".override_default_tax_checkbox, .override_prices_checkbox, .override_default_commission").change(function()
	{
		$(this).parent().parent().next().toggleClass('hidden')
	});	

	$('#item_kit_form').validate({
		ignore: ':hidden:not([class~=selectized]),:hidden > .selectized, .selectize-control .selectize-input input',
		submitHandler:function(form)
		{
			$.post('<?php echo site_url("item_kits/check_duplicate");?>', {term: $('#name').val()},function(data) {
			<?php if(!$item_kit_info->item_kit_id) { ?>
			if(data.duplicate)
			{
				bootbox.confirm(<?php echo json_encode(lang('common_items_duplicate_exists'));?>, function(result)
				{
					if(result)
					{
						doItemKitSubmit(form);
					}
				});
			}
			else
			{
				doItemKitSubmit(form);
			}
			<?php } else { ?>
				doItemKitSubmit(form);
			<?php } ?>
			} , "json")
			.error(function() { 
			});
		

		},
	errorClass: "text-danger",
	errorElement: "span",
	highlight:function(element, errorClass, validClass) {
		$(element).parents('.form-group').removeClass('has-success').addClass('has-error');
	},
	unhighlight: function(element, errorClass, validClass) {
		$(element).parents('.form-group').removeClass('has-error').addClass('has-success');
	},
		rules:
			{
				<?php foreach($tiers as $tier) { ?>
					"<?php echo 'item_kit_tier['.$tier->id.']'; ?>":
					{
						number: true
					},
				<?php } ?>
		
				<?php foreach($locations as $location) { ?>
					"<?php echo 'locations['.$location->location_id.'][cost_price]'; ?>":
					{
						number: true
					},
					"<?php echo 'locations['.$location->location_id.'][unit_price]'; ?>":
					{
						number: true
					},			
					<?php foreach($tiers as $tier) { ?>
						"<?php echo 'locations['.$location->location_id.'][item_tier]['.$tier->id.']'; ?>":
						{
							number: true
						},
					<?php } ?>				
				<?php } ?>					
				name:"required",
				category_id:"required",
				unit_price: "number",
				cost_price: "number"
			},
			messages:
			{
				<?php foreach($tiers as $tier) { ?>
					"<?php echo 'item_kit_tier['.$tier->id.']'; ?>":
					{
						number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
					},
				<?php } ?>
		
				<?php foreach($locations as $location) { ?>
					"<?php echo 'locations['.$location->location_id.'][cost_price]'; ?>":
					{
						number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
					},
					"<?php echo 'locations['.$location->location_id.'][unit_price]'; ?>":
					{
						number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
					},			
					<?php foreach($tiers as $tier) { ?>
						"<?php echo 'locations['.$location->location_id.'][item_tier]['.$tier->id.']'; ?>":
						{
							number: <?php echo json_encode(lang('common_this_field_must_be_a_number')); ?>
						},
					<?php } ?>				
				<?php } ?>
				name:<?php echo json_encode(lang('common_item_name_required')); ?>,
				category_id:<?php echo json_encode(lang('common_category_required')); ?>,
				unit_price: <?php echo json_encode(lang('common_unit_price_number')); ?>,
				cost_price: <?php echo json_encode(lang('common_cost_price_number')); ?>
			}
	});
});

function deleteItemKitRow(link)
{
	$(link).parent().parent().remove();
	calculateSuggestedPrices();
	return false;
}

function calculateSuggestedPrices()
{
	var items = [];
	$("#item_kit_items").find('input').each(function(index, element)
	{
		var quantity = parseFloat($(element).val());
		var item_id = $(element).attr('id').substring($(element).attr('id').lastIndexOf('_') + 1);
	
		items.push({
			item_id: item_id,
			quantity: quantity
		});
	});
	calculateSuggestedPrices.totalCostOfItems = 0;
	calculateSuggestedPrices.totalPriceOfItems = 0;
	getPrices(items, 0);
}

function getPrices(items, index)
{
	if (index > items.length -1)
	{
		$("#unit_price").val(calculateSuggestedPrices.totalPriceOfItems);
		$("#cost_price").val(calculateSuggestedPrices.totalCostOfItems);
	}
	else
	{
		$.get('<?php echo site_url("items/get_info");?>'+'/'+items[index]['item_id'], {}, function(item_info)
		{
			calculateSuggestedPrices.totalPriceOfItems+=items[index]['quantity'] * parseFloat(item_info.unit_price);
			calculateSuggestedPrices.totalCostOfItems+=items[index]['quantity'] * parseFloat(item_info.cost_price);
			getPrices(items, index+1);
		}, 'json');
	}
}

var submitting = false;
function doItemKitSubmit(form)
{	
$('#grid-loader').show();
	if (submitting) return;
	submitting = true;
	$(form).ajaxSubmit({
	success:function(response)
    {
$('#grid-loader').hide();
		submitting = false;		
		show_feedback(response.success ? 'success' : 'error', response.message,response.success ? <?php echo json_encode(lang('common_success')); ?> +' #' + response.item_kit_id : <?php echo json_encode(lang('common_error')); ?>);
		<?php if(!$item_kit_info->item_kit_id) { ?>
		//If we have a new item, make sure we hide the tax containers to "reset"
		$(".tax-container").addClass('hidden');
		$(".item-kit-location-price-container").addClass('hidden');
		$('.commission-container').addClass('hidden');
		$('.item_kit_item_row').remove();
		
		var selectize = $("#tags")[0].selectize;
		selectize.clear();
		selectize.clearOptions();
		
		<?php } ?>

		if(response.redirect==2 && response.success)
		{
			window.location.href = '<?php echo site_url('item_kits'); ?>';
		}
		else
		{
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$(".form-group").removeClass('has-success has-error');
		}
	},
	<?php if(!$item_kit_info->item_kit_id) { ?>
	resetForm: true,
	<?php } ?>
	dataType:'json'
	});
}	


</script>
<?php $this->load->view("partial/footer"); ?>
