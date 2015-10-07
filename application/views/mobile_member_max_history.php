<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="fieldcontain" class="ui-hide-label">	
		<div class ="good-messsage">
			<p><?php echo isset($good_message) ? $good_message : '' ;?></p>
		</div>
		<div class ="error-messsage">
			<p><?php echo isset($error_message) ? $error_message : '' ;?></p>
		</div>
	</div>
	<div class="ui-grid-c">
		<div class="ui-block-a mobile-grid-header">&nbsp;</div>
		<div class="ui-block-b mobile-grid-header date-block">Date</div>
		<div class="ui-block-c mobile-grid-header number-block">Rep</div>
		<div class="ui-block-d mobile-grid-header number-block">Value</div>
		<?php echo $exercise_history;?>
	</div><!-- /grid-c -->
	<div>
	<?php	echo anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
															'data-role'=>'button',
															'data-inline'=>'true'));
	?>
	</div>
</div>
