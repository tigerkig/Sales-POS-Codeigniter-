<?php $this->load->view("partial/header"); 
$this->load->helper('demo');

if (is_on_phppos_host()) {
?>
	<?php if (isset($trial_on) && $trial_on === true) { ?>
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">					
				   <div class="alert alert-success">
				    <?php echo lang('login_trail_info'). ' '.date(get_date_format(), strtotime($cloud_customer_info['trial_end_date'])).'. '.lang('login_trial_info_2'); ?>
				    </div>
				    <a class="btn btn-block btn-success" href="https://phppointofsale.com/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>" target="_blank"><?php echo lang('common_update_billing_info');?></a>
					</div>
				</div>
			</div>
	<?php } ?>


	<?php if (isset($subscription_payment_failed) && $subscription_payment_failed === true) { ?>
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
				   <div class="alert alert-danger">
				        <?php echo lang('login_payment_failed_text'); ?>
				    </div>
				    <a class="btn btn-block btn-success" href="https://phppointofsale.com/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>" target="_blank"><?php echo lang('common_update_billing_info');?></a>
					</div>
				</div>
			</div>
	<?php } ?>

	<?php if (isset($subscription_cancelled_within_5_days) && $subscription_cancelled_within_5_days === true) { ?>
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
				    <div class="alert alert-danger">
				        <?php echo lang('login_resign_text'); ?>
				    </div>
					<a class="btn btn-block btn-sm btn-success" href="https://phppointofsale.com/update_billing.php?store_username=<?php echo $cloud_customer_info['username'];?>&username=<?php echo $this->Employee->get_logged_in_employee_info()->username; ?>&password=<?php echo $this->Employee->get_logged_in_employee_info()->password; ?>" target="_blank"><?php echo lang('login_resignup');?></a>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } ?>
<?php if ($can_show_mercury_activate) { ?>
	<!-- mercury activation message -->
	<div class="row " id="mercury_container">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					<a id="dismiss_mercury" href="<?php echo site_url('home/dismiss_mercury_message') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
					<div id="mercury_activate_container">
						<h3><a href="http://phppointofsale.com/credit_card_processing.php" target="_blank"><?php echo lang('common_credit_card_processing'); ?></a></h3>
						<a href="http://phppointofsale.com/credit_card_processing.php" class="mercury_description" target="_blank">
							<?php echo lang('home_mercury_activate_promo_text');?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php  } ?>

<?php 
$this->load->helper('demo');
if (!is_on_demo_host() && !$this->config->item('hide_test_mode_home') && !$this->config->item('disable_test_mode')) { ?>
	<?php if($this->config->item('test_mode')) { ?>
		<div class="alert alert-danger">
			<strong><?php echo lang('common_in_test_mode'); ?>. <a href="sales/disable_test_mode"></strong>
			<a href="<?php echo site_url('home/disable_test_mode'); ?>" id="disable_test_mode"><?php echo lang('common_disable_test_mode');?></a>
		</div>
	<?php } ?>

	<?php if(!$this->config->item('test_mode')  && !$this->config->item('disable_test_mode')) { ?>
		<div class="row " id="test_mode_container">
			<div class="col-md-12">
				<div class="panel">
					<div class="panel-body text-center">
						<a id="dismiss_test_mode" href="<?php echo site_url('home/dismiss_test_mode') ?>" class="pull-right text-danger"><?php echo lang('common_dismiss'); ?></a>
							<strong><?php echo anchor(site_url('home/enable_test_mode'), '<i class="ion-ios-settings-strong"></i> '.lang('common_enable_test_mode'),array('id'=>'enable_test_mode')); ?></strong>
							<p><?php echo lang('common_test_mode_desc')?></p>
						</div>
					</div>
				</div>
			</div>

	<?php } ?>
<?php } ?>


<div class="text-center">					

	<?php if ($this->Employee->has_module_action_permission('reports', 'view_dashboard_stats', $this->Employee->get_logged_in_employee_info()->person_id) && (!$this->agent->is_mobile() || $this->agent->is_tablet())) { ?>
	
	<?php
	if ($this->config->item('ecommerce_cron_running')) {
	?>
	<!-- ecommerce progress bar -->
	<div class="row" id="ecommerce_progress_container">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-heading">
					<h5><?php echo lang('home_ecommerce_platform_sync')?></h5>
				</div>
				<div class="panel-body">
					<div id="progress_bar">
						<div class="progress">
						  <div class="progress-bar progress-bar-striped active" id="progessbar" role="progressbar"
						  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
						    <span id="progress_percent">0</span>% <span id="progress_message"></span>
						  </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script>
	function check_ecommerce_status()
	{
		$.getJSON(SITE_URL+'/home/get_ecommerce_sync_progress', function(response)
		{
			set_progress(response.percent_complete,response.message);
		
			if (response.running)
			{
				setTimeout(check_ecommerce_status,5000);
			}
		});
	}
	
	function set_progress(percent, message)
	{
		$("#progress_container").show();
		$('#progessbar').attr('aria-valuenow', percent).css('width',percent+'%');
		$('#progress_percent').html(percent);
		if (message !='')
		{
			$("#progress_message").html('('+message+')');
		}
		else
		{
			$("#progress_message").html('');
		}
		
	}
	check_ecommerce_status();
	</script>
	
	<?php } ?>
	
	<div class="row">
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('sales'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatBluec"><?php echo $total_sales; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_sales'); ?></h4>
					</div>
					<div class="right flatBlue">
						<i class="ion ion-ios-cart-outline"></i>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('customers'); ?>">
				<div class="dashboard-stats" id="totalCustomers">
					<div class="left">
						<h3 class="flatGreenc"><?php echo $total_customers; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_customers'); ?></h4>
					</div>
					<div class="right flatGreen">
						<i class="ion ion-ios-people-outline"></i>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('items'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatRedc"><?php echo $total_items; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_items'); ?></h4>
					</div>
					<div class="right flatRed">
						<i class="icon ti-harddrive"></i>
					</div>
				</div>
			</a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<a href="<?php echo site_url('item_kits'); ?>">
				<div class="dashboard-stats">
					<div class="left">
						<h3 class="flatOrangec"><?php echo $total_item_kits; ?></h3>
						<h4><?php echo lang('common_total')." ".lang('module_item_kits'); ?></h4>
					</div>
					<div class="right flatOrange">
						<i class="ion ion-filing"></i>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>

<?php } ?>
<h5 class="text-center"><?php echo lang('home_welcome_message');?></h5>
<div class="row quick-actions">

	<?php if ($this->Employee->has_module_permission('sales', $this->Employee->get_logged_in_employee_info()->person_id)) {	?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('sales'); ?>"> <i class="icon ti-shopping-cart"></i> <?php echo lang('common_start_new_sale'); ?></a>
			</div>
		</div>
	<?php } ?>


	<?php if ($this->Employee->has_module_permission('receivings', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('receivings'); ?>"> <i class="icon ti-cloud-down"></i> <?php echo lang('home_receivings_start_new_receiving'); ?></a>
			</div>
		</div>
	<?php } ?>	
	
  	<?php if ($this->Employee->has_module_action_permission('reports', 'view_closeout', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/closeout/'.date('Y-m-d').'/'.date('Y-m-d'));?>"> <i class="ion-clock"></i> <?php echo lang('home_todays_closeout_report'); ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($this->Employee->has_module_action_permission('reports', 'view_sales', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/detailed_sales/'.date('Y-m-d').'%2000:00:00/'.date('Y-m-d').'%2023:59:59/all/0');?>"> <i class="ion-stats-bars"></i> <?php echo lang('home_todays_detailed_sales_report'); ?></a>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($this->Employee->has_module_action_permission('reports', 'view_items', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="list-group">
					<a class="list-group-item" href="<?php echo site_url('reports/summary_items/'.date('Y-m-d').'%2000:00:00/'.date('Y-m-d').'%2023:59:59/0/0/0/-1/-1/all/0');?>"> <i class="ion-clipboard"></i> <?php echo lang('home_todays_summary_items_report'); ?></a>
			</div>
		</div>
	<?php } ?>
		
	
</div>

<?php if ($this->Employee->has_module_action_permission('reports', 'view_dashboard_stats', $this->Employee->get_logged_in_employee_info()->person_id)) { ?>
<div class="row ">
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-body">
					
					<?php if (can_display_graphical_report()) { ?>
					<div class="panel-heading">
						<h4 class="text-center"><?php echo lang('common_sales_info') ?></h4>	
					</div>
					<!-- Nav tabs -->
                    <ul class="nav nav-tabs piluku-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#month" data-type="monthly" aria-controls="month" role="tab"><?php echo lang('common_month') ?></a></li>
                        <li role="presentation"><a href="#week" data-type="weekly" aria-controls="week" role="tab"><?php echo lang('common_week') ?></a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content piluku-tab-content">
                        <div role="tabpanel" class="tab-pane active" id="month">
                        	<div class="chart">
                        		<?php if(isset($month_sale) && !isset($month_sale['message'])){ ?>
									<canvas id="charts" width="400" height="100"></canvas>		
								<?php } else{ 
									echo $month_sale['message'];
									 } ?>
							</div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="week">
                        	
                       	</div>
                    </div>
						<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<?php if($choose_location && count($authenticated_locations) > 1){ ?>
	

<!-- Modal -->
<div class="modal fade" id="choose_location_modal" tabindex="-1" role="dialog" aria-labelledby="chooseLocation" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="chooseLocation"><?php echo lang('common_locations_choose_location'); ?></h4>
      </div>
      <div class="modal-body">
        <ul class="list-inline choose-location-home">
        	<?php foreach ($authenticated_locations as $key => $value) { ?>
				<li><a class="set_employee_current_location_after_login" data-location-id="<?php echo $key; ?>" href="<?php echo site_url('home/set_employee_current_location_id/'.$key) ?>"> <?php echo $value; ?> </a></li>
			<?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<!-- Location Message to employee -->
<script>
	$(document).ready(function(){

		$("#dismiss_mercury").click(function(e){
			e.preventDefault();
			$.get($(this).attr('href'));
			$("#mercury_container").fadeOut();
			
		});

		$("#dismiss_test_mode").click(function(e){
			e.preventDefault();
			$.get($(this).attr('href'));
			$("#test_mode_container").fadeOut();
		});
	
		<?php if($choose_location && count($authenticated_locations) > 1) { ?>
			
			$('#choose_location_modal').modal('show');

			$(".set_employee_current_location_after_login").on('click',function(e)
			{
				e.preventDefault();

				var location_id = $(this).data('location-id');
				$.ajax({
				    type: 'POST',
				    url: '<?php echo site_url('home/set_employee_current_location_id'); ?>',
				    data: { 
				        'employee_current_location_id': location_id, 
				    },
				    success: function(){

				    	window.location = <?php echo json_encode(site_url('home')); ?>;
				    }
				});
				
			});
			
		<?php } ?>


		<?php if(isset($month_sale) && !isset($month_sale['message'])){ ?>
			var data = {
				labels: <?php echo $month_sale['day'] ?>,
				datasets: [
				{
					fillColor : "#5d9bfb",
					strokeColor : "#5d9bfb",
					highlightFill : "#5d9bfb",
					highlightStroke : "#5d9bfb",
					data: <?php echo $month_sale['amount'] ?>
				}
				]
			};
			var ctx = document.getElementById("charts").getContext("2d");
			var myBarChart = new Chart(ctx).Bar(data, {
				responsive : true
			});
		<?php } ?>

	        

		$('.piluku-tabs a').on('click',function(e) {
			e.preventDefault();
			$('.piluku-tabs li').removeClass('active');
			$(this).parent('li').addClass('active');
			var type = $(this).attr('data-type');
			$.post('<?php echo site_url("home/sales_widget/'+type+'"); ?>', function(res)
			{
				var obj = jQuery.parseJSON(res);
				if(obj.message)
				{
					$(".chart").html(obj.message);
					return false;
				}
				
				renderChart(obj.day, obj.amount);
				
				myBarChart.update();
			});
		});

		function renderChart(label,data){

		    $(".chart").html("").html('<canvas id="charts" width="400" height="400"></canvas>');
		    var lineChartData = {
		        labels : label,
		        datasets : [
		            {
		                fillColor : "#5d9bfb",
						strokeColor : "#5d9bfb",
						highlightFill : "#5d9bfb",
						highlightStroke : "#5d9bfb",
		                data : data
		            }
		        ]

		    }
		    var canvas = document.getElementById("charts");
		    var ctx = canvas.getContext("2d");

		    myLine = new Chart(ctx).Bar(lineChartData, {
		        responsive: true,
		        maintainAspectRatio: false
		    });
		}
	});
</script>

<?php $this->load->view("partial/footer"); ?>