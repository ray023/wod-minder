<?php $this->load->helper('form'); ?>
<div data-role="page">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1><?php echo $title;?></h1>
	</div>
	<div data-role="content">		
		<div class="content-primary">	
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Search exercises..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
				<?php echo $paleo_history;?>
			</ul>
		</div><!--/content-primary -->		
	</div><!-- content -->
	<div>
	<?php	echo anchor('welcome/index/TRUE', 'Cancel', array(	'data-ajax'=>'false',
															'data-role'=>'button',
															'data-inline'=>'true'));
	?>
	</div>
</div>