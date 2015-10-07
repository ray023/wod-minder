<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title.' History';?></h1>
	</div>
	<div data-role="content">		
		<div class="content-primary">	
			<div class="ui-grid-b">
				<div class="ui-block-a mobile-grid-header">&nbsp;</div>
				<div class="ui-block-b mobile-grid-header date-block">Date</div>
				<div class="ui-block-c mobile-grid-header number-block">Score</div>
				<?php echo $benchmark_wod_history;?>
			</div><!-- /grid-c -->
		</div><!--/content-primary -->		
	</div><!-- content -->
	<div>
		<b>NOTES</b>
		<ul>
			<li>An asterisk (*) indicates a scaled WOD.</li>
			<li>A dagger (&#8224;) indicates a Workout of the Day at your box</li>
		</ul>
	</div>
	<div>
	<?php	echo anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
															'data-role'=>'button',
															'data-inline'=>'true'));
	?>
	</div>
</div>