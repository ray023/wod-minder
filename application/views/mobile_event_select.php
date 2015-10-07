<div data-role="page">
	<div data-role="header">
		<h1><?php echo $title; ?></h1>
	</div>
	<div data-role="content">		
		<div class="content-primary">	
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Pick an Event..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
				<?php echo $event_list;?>
			</ul>
		</div><!--/content-primary -->		
	</div><!-- content -->
</div>