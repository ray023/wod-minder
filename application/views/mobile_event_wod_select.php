<div data-role="page" id="Main">
	<div data-role="header">
		<a href="<?php echo base_url(); ?>index.php/welcome/index/TRUE" data-ajax ="false" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		<h1>Pick Event</h1>
	</div><!-- /header -->
	<div data-role="content">		
		<div class="content-primary">	
			<ul data-role="listview" data-filter="true" data-filter-placeholder="Search Events..." data-filter-theme="d" data-theme="d" data-divider-theme="d">
				<?php echo $event_list;?>
			</ul>
		</div>
	</div>
</div><!--Page Main-->
<?php echo $event_wod_pages;?>