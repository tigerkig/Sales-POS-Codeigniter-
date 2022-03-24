<?php
$this->load->view("partial/header");
?>
<div class="row">
	<?php foreach($summary_data as $name=>$value) { ?>
	    <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
	        <div class="info-seven primarybg-info">
	            <div class="logo-seven"><i class="ti-widget dark-info-primary"></i></div>
	            <?php echo to_currency($value); ?>
	            <p><?php echo lang('reports_'.$name); ?></p>
	        </div>
	    </div>
	<?php }?>
</div>

<div class="row">
	<div id="report_summary"  class="repors-summarys col-md-12 ">
		<!-- Css Loader  -->
		<div class="spinner" id="ajax-loader" style="width:70%;height:120px;top: 220px;">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		</div>
		
		<div class="panel panel-piluku">
			<div class="panel-heading">
				<?php echo lang('reports_reports'); ?> - <?php echo $title ?>
			</div>
			<div class="panel-body">
				<div id="chart_wrapper">
					<div id="chart-legend" class="chart-legend"></div>
					<canvas id="chart"></canvas>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>


<script type="text/javascript">
	$.getScript('<?php echo $graph_file; ?>', function()
	{
		$("#ajax-loader").hide();
	});
</script>

<?php
$this->load->view("partial/footer"); 
?>