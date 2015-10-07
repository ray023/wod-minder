<div data-role="page" id="Main">
	<div data-role="header">
		<a href= "<?php echo base_url()?>index.php/kiosk/pick_box" data-icon="back" data-iconpos="notext" data-ajax="false">Pick Box</a>
		<h1>Kiosk Menu</h1>
	</div><!-- /header -->
	<div data-role="content">		
		<div class="content-primary">	
			<p>
				<?php echo anchor('kiosk/view_daily_wod', 'Screen 1 - View Daily WOD', array(	'data-ajax'=>'false',
																								'data-role'=>'button'));?>	
			</p>		
			<p>
				<?php echo anchor('kiosk/save_daily_wod', 'Screen 2 - Save Daily WOD', array(	'data-ajax'=>'false',
																								'data-role'=>'button'));?>	
			</p>		
			<p>
				<?php echo anchor('kiosk/results', 'Screen 3 - Results', array(	'data-ajax'=>'false',
																				'data-role'=>'button'));?>	
			</p>		
		</div>
	</div>
</div><!--Page Main-->